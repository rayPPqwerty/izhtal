<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RentalRequestController extends Controller
{
    public const STATUSES = [
        'new'        => 'Новая',
        'processing' => 'В обработке',
        'approved'   => 'Одобрена',
        'rejected'   => 'Отклонена',
        'cancelled'  => 'Отменена',
    ];

    public const MANUAL_STATUSES = [
        'new'        => 'Новая',
        'processing' => 'В обработке',
        'rejected'   => 'Отклонена',
        'cancelled'  => 'Отменена',
    ];

    public const STATUS_CLASSES = [
        'new'        => 'rr-status-new',
        'processing' => 'rr-status-processing',
        'approved'   => 'rr-status-approved',
        'rejected'   => 'rr-status-rejected',
        'cancelled'  => 'rr-status-cancelled',
    ];

    public function index(Request $request)
    {
        $status = (string) $request->get('status', 'all');

        $query = DB::table('rental_requests')
            ->leftJoin('spaces', 'rental_requests.space_id', '=', 'spaces.id')
            ->leftJoin('facilities', 'spaces.facility_id', '=', 'facilities.id')
            ->leftJoin('rental_categories', 'rental_requests.category_id', '=', 'rental_categories.id')
            ->leftJoin('users', 'rental_requests.processed_by', '=', 'users.id')
            ->select(
                'rental_requests.*',
                'spaces.name as space_name',
                'spaces.space_type as space_type',
                'facilities.name as facility_name',
                'facilities.short_name as facility_short_name',
                'rental_categories.name as category_name',
                'rental_categories.color as category_color',
                'rental_categories.text_color as category_text_color',
                'users.name as processed_by_name'
            )
            ->orderByRaw("FIELD(rental_requests.status, 'new', 'processing', 'approved', 'rejected', 'cancelled')")
            ->orderByDesc('rental_requests.created_at');

        if ($status !== 'all' && array_key_exists($status, self::STATUSES)) {
            $query->where('rental_requests.status', $status);
        }

        $requests = $query->paginate(20)->withQueryString();

        $counts = ['all' => DB::table('rental_requests')->count()];
        foreach (array_keys(self::STATUSES) as $s) {
            $counts[$s] = DB::table('rental_requests')->where('status', $s)->count();
        }

        return view('admin.requests.index', [
            'requests' => $requests,
            'status'   => $status,
            'counts'   => $counts,
            'statuses' => self::STATUSES,
            'manualStatuses' => self::MANUAL_STATUSES,
            'classes'  => self::STATUS_CLASSES,
        ]);
    }

    public function updateStatus(Request $request, int $id)
    {
        $data = $request->validate([
            'status'        => 'required|in:' . implode(',', array_keys(self::MANUAL_STATUSES)),
            'admin_comment' => 'nullable|string|max:2000',
        ], [
            'status.in' => 'Статус «Одобрена» устанавливается только через кнопку «Одобрить в расписание».',
        ]);

        $row = DB::table('rental_requests')->where('id', $id)->first();
        if (!$row) {
            return back()->withErrors(['not_found' => 'Заявка не найдена.']);
        }

        DB::table('rental_requests')->where('id', $id)->update([
            'status'        => $data['status'],
            'admin_comment' => $data['admin_comment'] ?? $row->admin_comment,
            'processed_by'  => Auth::id(),
            'processed_at'  => now(),
            'updated_at'    => now(),
        ]);

        return back()->with('success', 'Статус заявки обновлён.');
    }

    public function approve(int $id)
    {
        $req = DB::table('rental_requests')->where('id', $id)->first();
        if (!$req) {
            return back()->withErrors(['not_found' => 'Заявка не найдена.']);
        }

        $startAt = Carbon::parse($req->preferred_date . ' ' . $req->preferred_start_time);
        $endAt   = $startAt->copy()->addMinutes((int) $req->duration_minutes);

        $existingEntry = DB::table('schedule_entries')
            ->where('space_id', (int) $req->space_id)
            ->whereNull('deleted_at')
            ->where('start_at', $startAt->toDateTimeString())
            ->where('end_at', $endAt->toDateTimeString())
            ->where('title', $req->title)
            ->first();

        if ($existingEntry) {
            DB::table('rental_requests')->where('id', $req->id)->update([
                'status'       => 'approved',
                'processed_by' => Auth::id(),
                'processed_at' => now(),
                'updated_at'   => now(),
            ]);
            return $this->redirectToSchedule($req, 'Заявка уже была в расписании — статус синхронизирован.');
        }

        $overlap = DB::table('schedule_entries')
            ->where('space_id', (int) $req->space_id)
            ->whereNull('deleted_at')
            ->where('start_at', '<', $endAt->toDateTimeString())
            ->where('end_at', '>', $startAt->toDateTimeString())
            ->first();

        if ($overlap) {
            $conflictTime = Carbon::parse($overlap->start_at)->format('H:i') . '–' . Carbon::parse($overlap->end_at)->format('H:i');
            return back()->withErrors([
                'overlap' => "На это время уже стоит запись «{$overlap->title}» ({$conflictTime}).",
            ]);
        }

        try {
            $renterId = $this->resolveRenterId($req->renter_name, $req->renter_phone, $req->renter_email);

            DB::transaction(function () use ($req, $renterId, $startAt, $endAt) {
                DB::table('schedule_entries')->insert([
                    'space_id'    => (int) $req->space_id,
                    'renter_id'   => $renterId,
                    'category_id' => $req->category_id ? (int) $req->category_id : null,
                    'created_by'  => Auth::id(),
                    'entry_type'  => 'rental',
                    'title'       => $req->title,
                    'start_at'    => $startAt->toDateTimeString(),
                    'end_at'      => $endAt->toDateTimeString(),
                    'notes'       => $req->notes,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);

                DB::table('rental_requests')->where('id', $req->id)->update([
                    'status'       => 'approved',
                    'processed_by' => Auth::id(),
                    'processed_at' => now(),
                    'updated_at'   => now(),
                ]);
            });
        } catch (\Throwable $e) {
            Log::error('Approve error: ' . $e->getMessage());
            return back()->withErrors(['db' => 'Ошибка БД: ' . $e->getMessage()]);
        }

        return $this->redirectToSchedule(
            $req,
            "Заявка «{$req->title}» одобрена и добавлена в расписание на " . $startAt->format('d.m.Y H:i') . '. Не забудьте нажать «Опубликовать день».'
        );
    }

    public function destroy(int $id)
    {
        DB::table('rental_requests')->where('id', $id)->delete();
        return back()->with('success', 'Заявка удалена.');
    }

    private function redirectToSchedule($req, string $message)
    {
        $spaceType = DB::table('spaces')->where('id', $req->space_id)->value('space_type');
        $section = match ($spaceType) {
            'ice' => 'ice',
            'hall', 'gym', 'pool', 'other' => 'hall',
            default => 'ice',
        };
        $weekStart = Carbon::parse($req->preferred_date)->startOfWeek(Carbon::MONDAY);

        return redirect()->route('admin.schedule.index', [
            'section'  => $section,
            'space_id' => (int) $req->space_id,
            'week'     => $weekStart->toDateString(),
        ])->with('success', $message);
    }

    private function resolveRenterId(string $name, ?string $phone, ?string $email): ?int
    {
        $name  = trim($name);
        $phone = trim((string) $phone);
        $email = trim((string) $email);

        if ($phone === '' && $email === '') return null;

        $existing = null;
        if ($email !== '') {
            $existing = DB::table('renters')->where('email', $email)->first();
        }
        if (!$existing && $phone !== '') {
            $existing = DB::table('renters')->where('phone', $phone)->first();
        }

        if ($existing) {
            DB::table('renters')->where('id', $existing->id)->update([
                'name' => $name !== '' ? $name : $existing->name,
                'phone' => $phone !== '' ? $phone : $existing->phone,
                'email' => $email !== '' ? $email : $existing->email,
                'updated_at' => now(),
            ]);
            return (int) $existing->id;
        }

        return (int) DB::table('renters')->insertGetId([
            'name' => $name !== '' ? $name : 'Арендатор',
            'phone' => $phone !== '' ? $phone : null,
            'email' => $email !== '' ? $email : null,
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}