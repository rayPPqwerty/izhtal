<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\PublishedDay;
use App\Models\ScheduleBooking;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $section = $request->get('section', 'ice');
        $facilities = Facility::where('section', $section)->orderBy('id')->get();

        if ($facilities->isEmpty()) {
            $facilities = Facility::orderBy('id')->get();
        }

        $currentFacility = $facilities->firstWhere('id', $request->get('facility_id'))
            ?? $facilities->first();

        $weekStart = $request->filled('week')
            ? Carbon::parse($request->get('week'))->startOfWeek(Carbon::MONDAY)
            : Carbon::now()->startOfWeek(Carbon::MONDAY);

        $days = [];
        for ($i = 0; $i < 7; $i++) {
            $date = $weekStart->copy()->addDays($i);
            $dateStr = $date->toDateString();

            $bookings = $currentFacility
                ? ScheduleBooking::where('facility_id', $currentFacility->id)
                    ->whereDate('date', $dateStr)
                    ->get()
                : collect();

            $isPublished = $currentFacility
                ? PublishedDay::where('facility_id', $currentFacility->id)
                    ->whereDate('date', $dateStr)
                    ->value('is_published') ?? false
                : false;

            $days[] = [
                'date'         => $date,
                'date_str'     => $dateStr,
                'is_published' => (bool) $isPublished,
                'slots'        => $this->buildDaySlots($bookings),
            ];
        }

        return view('admin.schedule.index', [
            'section'         => $section,
            'facilities'      => $facilities,
            'currentFacility' => $currentFacility,
            'weekStart'       => $weekStart,
            'days'            => $days,
            'categories'      => ScheduleBooking::CATEGORIES,
        ]);
    }

    private function buildDaySlots($bookings): array
    {
        $slots = [];
        $cursor = Carbon::createFromTime(6, 0);
        $endOfDay = Carbon::createFromTime(23, 45);
        $prevWasBusy = false;

        while ($cursor <= $endOfDay) {
            $timeStr = $cursor->format('H:i');
            $slotStartMin = $cursor->hour * 60 + $cursor->minute;

            $matchedBooking = $bookings->first(function ($b) use ($slotStartMin) {
                $bStart = Carbon::parse($b->start_time);
                $startMin = $bStart->hour * 60 + $bStart->minute;
                $endMin = $startMin + $b->duration_minutes;
                return $slotStartMin >= $startMin && $slotStartMin < $endMin;
            });

            if ($matchedBooking) {
                $state = $matchedBooking->is_ice_prep ? 'prep' : 'busy';
                $prevWasBusy = true;
            } else {
                $state = $prevWasBusy ? 'free-start' : 'free';
                $prevWasBusy = false;
            }

            $slots[] = [
                'time'    => $timeStr,
                'state'   => $state,
                'booking' => $matchedBooking,
            ];

            $cursor->addMinutes(15);
        }

        return $slots;
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'facility_id'      => 'required|exists:facilities,id',
            'date'             => 'required|date',
            'start_time'       => 'required|date_format:H:i',
            'duration_minutes' => 'required|integer|min:15|max:480',
            'is_ice_prep'      => 'nullable|boolean',
            'tenant_name'      => 'nullable|string|max:255',
            'category'         => 'required|string',
            'tenant_email'     => 'nullable|email|max:255',
            'tenant_phone'     => 'nullable|string|max:50',
        ]);

        $data['is_ice_prep'] = $request->boolean('is_ice_prep');

        if (!$data['is_ice_prep'] && empty($data['tenant_name'])) {
            return back()->withErrors(['tenant_name' => 'Укажите арендатора или выберите «Подготовка / заливка льда».']);
        }

        if ($this->hasOverlap($data['facility_id'], $data['date'], $data['start_time'], $data['duration_minutes'])) {
            return back()->withErrors(['overlap' => 'Выбранное время пересекается с существующей записью!']);
        }

        ScheduleBooking::create($data);

        return back()->with('success', 'Запись успешно добавлена в расписание.');
    }

    public function update(Request $request, ScheduleBooking $booking)
    {
        $data = $request->validate([
            'duration_minutes' => 'required|integer|min:15|max:480',
            'is_ice_prep'      => 'nullable|boolean',
            'tenant_name'      => 'nullable|string|max:255',
            'category'         => 'required|string',
            'tenant_email'     => 'nullable|email|max:255',
            'tenant_phone'     => 'nullable|string|max:50',
        ]);

        $data['is_ice_prep'] = $request->boolean('is_ice_prep');

        if ($this->hasOverlap(
            $booking->facility_id,
            $booking->date->toDateString(),
            Carbon::parse($booking->start_time)->format('H:i'),
            $data['duration_minutes'],
            $booking->id
        )) {
            return back()->withErrors(['overlap' => 'Новая длительность накладывается на следующую запись!']);
        }

        $booking->update($data);

        return back()->with('success', 'Запись обновлена.');
    }

    public function destroy(ScheduleBooking $booking)
    {
        $booking->delete();

        return back()->with('success', 'Запись удалена.');
    }

    public function togglePublish(Request $request)
    {
        $data = $request->validate([
            'facility_id' => 'required|exists:facilities,id',
            'date'        => 'required|date',
        ]);

        $day = PublishedDay::firstOrNew([
            'facility_id' => $data['facility_id'],
            'date'        => $data['date'],
        ]);

        $day->is_published = !$day->is_published;
        $day->save();

        return back()->with('success', $day->is_published
            ? 'День опубликован на сайте.'
            : 'День снят с публикации.');
    }

    private function hasOverlap(int $facilityId, string $date, string $startTime, int $duration, ?int $ignoreId = null): bool
    {
        $newStart = Carbon::parse($startTime)->hour * 60 + Carbon::parse($startTime)->minute;
        $newEnd = $newStart + $duration;

        $existing = ScheduleBooking::where('facility_id', $facilityId)
            ->whereDate('date', $date)
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->get();

        foreach ($existing as $item) {
            $s = Carbon::parse($item->start_time)->hour * 60 + Carbon::parse($item->start_time)->minute;
            $e = $s + $item->duration_minutes;
            if ($newStart < $e && $newEnd > $s) {
                return true;
            }
        }

        return false;
    }
}