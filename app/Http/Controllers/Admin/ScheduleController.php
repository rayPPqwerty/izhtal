<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        Carbon::setLocale('ru');

        $section = $request->get('section', 'ice'); // ice | hall | foyer

        $spacesQuery = DB::table('spaces')
            ->join('facilities', 'spaces.facility_id', '=', 'facilities.id')
            ->where('spaces.is_active', 1)
            ->where('facilities.is_active', 1);

        if ($section === 'ice') {
            $spacesQuery->where('spaces.space_type', 'ice');
        } elseif ($section === 'hall') {
            $spacesQuery->whereIn('spaces.space_type', ['hall', 'gym', 'pool', 'other']);
        } elseif ($section === 'foyer') {
            $spacesQuery->where('spaces.space_type', 'other');
        }

        $spaces = $spacesQuery
            ->select(
                'spaces.*',
                'facilities.name as facility_name',
                'facilities.short_name as facility_short_name',
                'facilities.legacy_id'
            )
            ->orderBy('facilities.sort_order')
            ->orderBy('spaces.sort_order')
            ->orderBy('spaces.id')
            ->get();

        if ($spaces->isEmpty()) {
            $spaces = DB::table('spaces')
                ->join('facilities', 'spaces.facility_id', '=', 'facilities.id')
                ->where('spaces.is_active', 1)
                ->select(
                    'spaces.*',
                    'facilities.name as facility_name',
                    'facilities.short_name as facility_short_name',
                    'facilities.legacy_id'
                )
                ->orderBy('facilities.sort_order')
                ->orderBy('spaces.sort_order')
                ->orderBy('spaces.id')
                ->get();
        }

        $currentSpace = $spaces->firstWhere('id', (int) $request->get('space_id'))
            ?? $spaces->first();

        $weekStart = $request->filled('week')
            ? Carbon::parse($request->get('week'))->startOfWeek(Carbon::MONDAY)
            : Carbon::now()->startOfWeek(Carbon::MONDAY);

        $categories = DB::table('rental_categories')
            ->where('is_active', 1)
            ->orderBy('sort_order')
            ->get();

        $days = [];
        for ($i = 0; $i < 7; $i++) {
            $date = $weekStart->copy()->addDays($i);
            $dateStr = $date->toDateString();

            $entries = collect();
            $isPublished = false;

            if ($currentSpace) {
                $dayStart = $date->copy()->startOfDay();
                $dayEnd = $date->copy()->endOfDay();

                $entries = DB::table('schedule_entries')
                    ->leftJoin('rental_categories', 'schedule_entries.category_id', '=', 'rental_categories.id')
                    ->leftJoin('renters', 'schedule_entries.renter_id', '=', 'renters.id')
                    ->where('schedule_entries.space_id', $currentSpace->id)
                    ->whereNull('schedule_entries.deleted_at')
                    ->where('schedule_entries.start_at', '<', $dayEnd)
                    ->where('schedule_entries.end_at', '>', $dayStart)
                    ->select(
                        'schedule_entries.*',
                        'rental_categories.color as category_color',
                        'rental_categories.text_color as category_text_color',
                        'rental_categories.name as category_name',
                        'renters.phone as renter_phone',
                        'renters.email as renter_email'
                    )
                    ->orderBy('schedule_entries.start_at')
                    ->get();

                $isPublished = (bool) DB::table('schedule_publications')
                    ->where('space_id', $currentSpace->id)
                    ->whereDate('schedule_date', $dateStr)
                    ->value('is_published');
            }

            $days[] = [
                'date'         => $date,
                'date_str'     => $dateStr,
                'is_published' => $isPublished,
                'slots'        => $this->buildDaySlots($entries, $date),
            ];
        }

        return view('admin.schedule.index', [
            'section'      => $section,
            'facilities'   => $spaces,
            'currentSpace' => $currentSpace,
            'weekStart'    => $weekStart,
            'days'         => $days,
            'categories'   => $categories,
        ]);
    }

    private function buildDaySlots($entries, Carbon $date): array
    {
        $slots = [];
        $cursor = $date->copy()->setTime(6, 0);
        $endOfDay = $date->copy()->setTime(23, 45);
        $prevWasBusy = false;

        while ($cursor <= $endOfDay) {
            $timeStr = $cursor->format('H:i');
            $slotStart = $cursor->copy();
            $slotEnd = $cursor->copy()->addMinutes(15);

            $matched = $entries->first(function ($entry) use ($slotStart, $slotEnd) {
                $entryStart = Carbon::parse($entry->start_at);
                $entryEnd = Carbon::parse($entry->end_at);
                return $slotStart < $entryEnd && $slotEnd > $entryStart;
            });

            if ($matched) {
                $state = $matched->entry_type === 'ice_resurfacing' ? 'prep' : 'busy';
                $prevWasBusy = true;
            } else {
                $state = $prevWasBusy ? 'free-start' : 'free';
                $prevWasBusy = false;
            }

            $slots[] = [
                'time'    => $timeStr,
                'state'   => $state,
                'booking' => $matched,
            ];

            $cursor->addMinutes(15);
        }

        return $slots;
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'space_id'         => 'required|exists:spaces,id',
            'date'             => 'required|date',
            'start_time'       => 'required|date_format:H:i',
            'duration_minutes' => 'required|integer|min:15|max:480',
            'entry_type'       => 'nullable|in:rental,ice_resurfacing,blocked',
            'title'            => 'nullable|string|max:255',
            'category_id'      => 'nullable|exists:rental_categories,id',
            'notes'            => 'nullable|string',
            'renter_phone'     => 'nullable|string|max:40',
            'renter_email'     => 'nullable|email|max:255',
        ]);

        $entryType = $data['entry_type'] ?? 'rental';
        $title = trim((string) ($data['title'] ?? ''));

        if ($entryType === 'rental' && $title === '') {
            return back()
                ->withInput()
                ->withErrors(['title' => 'Укажите арендатора / название записи или выберите «Подготовка (заливка)».']);
        }

        if ($title === '') {
            $title = $entryType === 'ice_resurfacing' ? 'Заливка льда' : 'Занято';
        }

        $startAt = Carbon::parse($data['date'] . ' ' . $data['start_time'] . ':00');
        $endAt = $startAt->copy()->addMinutes((int) $data['duration_minutes']);

        if ($this->hasOverlap((int) $data['space_id'], $startAt, $endAt)) {
            return back()
                ->withInput()
                ->withErrors(['overlap' => 'Выбранное время пересекается с существующей записью!']);
        }

        $renterId = $entryType === 'rental'
            ? $this->resolveRenterId(
                $title,
                $data['renter_phone'] ?? null,
                $data['renter_email'] ?? null
            )
            : null;

        DB::table('schedule_entries')->insert([
            'space_id'    => (int) $data['space_id'],
            'renter_id'   => $renterId,
            'category_id' => $entryType === 'rental' ? ($data['category_id'] ?? null) : null,
            'created_by'  => Auth::id(),
            'entry_type'  => $entryType,
            'title'       => $title,
            'start_at'    => $startAt->toDateTimeString(),
            'end_at'      => $endAt->toDateTimeString(),
            'notes'       => $data['notes'] ?? null,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        return back()->with('success', 'Запись успешно добавлена в расписание.');
    }

    public function update(Request $request, int $booking)
    {
        $entry = DB::table('schedule_entries')
            ->where('id', $booking)
            ->whereNull('deleted_at')
            ->first();

        if (!$entry) {
            return back()->withErrors(['not_found' => 'Запись не найдена.']);
        }

        $data = $request->validate([
            'duration_minutes' => 'required|integer|min:15|max:480',
            'entry_type'       => 'nullable|in:rental,ice_resurfacing,blocked',
            'title'            => 'nullable|string|max:255',
            'category_id'      => 'nullable|exists:rental_categories,id',
            'notes'            => 'nullable|string',
            'renter_phone'     => 'nullable|string|max:40',
            'renter_email'     => 'nullable|email|max:255',
        ]);

        $entryType = $data['entry_type'] ?? $entry->entry_type;
        $title = trim((string) ($data['title'] ?? ''));

        if ($entryType === 'rental' && $title === '') {
            return back()
                ->withInput()
                ->withErrors(['title' => 'Укажите арендатора / название записи.']);
        }

        if ($title === '') {
            $title = $entryType === 'ice_resurfacing' ? 'Заливка льда' : $entry->title;
        }

        $startAt = Carbon::parse($entry->start_at);
        $endAt = $startAt->copy()->addMinutes((int) $data['duration_minutes']);

        if ($this->hasOverlap((int) $entry->space_id, $startAt, $endAt, $entry->id)) {
            return back()
                ->withInput()
                ->withErrors(['overlap' => 'Новая длительность накладывается на следующую запись!']);
        }

        $renterId = $entryType === 'rental'
            ? $this->resolveRenterId(
                $title,
                $data['renter_phone'] ?? null,
                $data['renter_email'] ?? null
            )
            : null;

        DB::table('schedule_entries')
            ->where('id', $entry->id)
            ->update([
                'renter_id'   => $renterId,
                'entry_type'  => $entryType,
                'title'       => $title,
                'category_id' => $entryType === 'rental' ? ($data['category_id'] ?? null) : null,
                'end_at'      => $endAt->toDateTimeString(),
                'notes'       => $data['notes'] ?? null,
                'updated_at'  => now(),
            ]);

        return back()->with('success', 'Запись обновлена.');
    }

    public function destroy(int $booking)
    {
        DB::table('schedule_entries')
            ->where('id', $booking)
            ->update(['deleted_at' => now()]);

        return back()->with('success', 'Запись удалена.');
    }

    public function togglePublish(Request $request)
    {
        $data = $request->validate([
            'space_id' => 'required|exists:spaces,id',
            'date'     => 'required|date',
        ]);

        $existing = DB::table('schedule_publications')
            ->where('space_id', (int) $data['space_id'])
            ->whereDate('schedule_date', $data['date'])
            ->first();

        $nextState = $existing ? !(bool) $existing->is_published : true;

        if ($existing) {
            DB::table('schedule_publications')
                ->where('id', $existing->id)
                ->update([
                    'is_published' => $nextState ? 1 : 0,
                    'published_at' => $nextState ? now() : null,
                    'published_by' => Auth::id(),
                    'updated_at'   => now(),
                ]);
        } else {
            DB::table('schedule_publications')->insert([
                'space_id'      => (int) $data['space_id'],
                'schedule_date' => $data['date'],
                'is_published'  => $nextState ? 1 : 0,
                'published_at'  => $nextState ? now() : null,
                'published_by'  => Auth::id(),
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }

        return back()->with('success', $nextState
            ? 'День опубликован на сайте.'
            : 'День снят с публикации.');
    }

    private function resolveRenterId(string $name, ?string $phone, ?string $email): ?int
    {
        $phone = trim((string) $phone);
        $email = trim((string) $email);
        $name = trim($name);

        if ($phone === '' && $email === '') {
            return null;
        }

        $query = DB::table('renters');

        if ($email !== '' && $phone !== '') {
            $existing = (clone $query)->where('email', $email)->where('phone', $phone)->first()
                ?? (clone $query)->where('email', $email)->first()
                ?? (clone $query)->where('phone', $phone)->first();
        } elseif ($email !== '') {
            $existing = (clone $query)->where('email', $email)->first();
        } else {
            $existing = (clone $query)->where('phone', $phone)->first();
        }

        if ($existing) {
            DB::table('renters')
                ->where('id', $existing->id)
                ->update([
                    'name'       => $name !== '' ? $name : $existing->name,
                    'phone'      => $phone !== '' ? $phone : $existing->phone,
                    'email'      => $email !== '' ? $email : $existing->email,
                    'updated_at' => now(),
                ]);

            return (int) $existing->id;
        }

        return (int) DB::table('renters')->insertGetId([
            'name'       => $name !== '' ? $name : 'Арендатор',
            'phone'      => $phone !== '' ? $phone : null,
            'email'      => $email !== '' ? $email : null,
            'is_active'  => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function hasOverlap(int $spaceId, Carbon $newStart, Carbon $newEnd, ?int $ignoreId = null): bool
    {
        return DB::table('schedule_entries')
            ->where('space_id', $spaceId)
            ->whereNull('deleted_at')
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->where('start_at', '<', $newEnd)
            ->where('end_at', '>', $newStart)
            ->exists();
    }
}