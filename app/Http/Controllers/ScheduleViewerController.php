<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScheduleViewerController extends Controller
{
    private const SOFT_PALETTE = [
        ['bg' => '#e8f4fc', 'border' => '#4ea1d3', 'text' => '#1e3a4c'],
        ['bg' => '#eef2ff', 'border' => '#6366f1', 'text' => '#272c5e'],
        ['bg' => '#e8f8f2', 'border' => '#2ca87a', 'text' => '#184635'],
        ['bg' => '#fef7e6', 'border' => '#e0a428', 'text' => '#4d390e'],
        ['bg' => '#f6ecfa', 'border' => '#a569bd', 'text' => '#3f224d'],
        ['bg' => '#fdecee', 'border' => '#d93b3b', 'text' => '#541b1b'],
        ['bg' => '#f1f5f9', 'border' => '#64748b', 'text' => '#203847'],
    ];

    private const SHORT_WEEKDAYS = [
        1 => 'Пн',
        2 => 'Вт',
        3 => 'Ср',
        4 => 'Чт',
        5 => 'Пт',
        6 => 'Сб',
        7 => 'Вс',
    ];

    private const RU_MONTHS_GENITIVE = [
        1  => 'января',
        2  => 'февраля',
        3  => 'марта',
        4  => 'апреля',
        5  => 'мая',
        6  => 'июня',
        7  => 'июля',
        8  => 'августа',
        9  => 'сентября',
        10 => 'октября',
        11 => 'ноября',
        12 => 'декабря',
    ];

    public function show(Request $request)
    {
        $spaceId = $request->get('space_id');
        $currentSpace = null;

        if ($spaceId) {
            $currentSpace = DB::table('spaces')
                ->join('facilities', 'spaces.facility_id', '=', 'facilities.id')
                ->where('spaces.id', (int) $spaceId)
                ->where('spaces.is_active', 1)
                ->select(
                    'spaces.*',
                    'facilities.name as facility_name',
                    'facilities.short_name as facility_short_name'
                )
                ->first();
        }

        if (!$currentSpace) {
            $currentSpace = DB::table('spaces')
                ->join('facilities', 'spaces.facility_id', '=', 'facilities.id')
                ->where('spaces.space_type', 'ice')
                ->where('spaces.is_active', 1)
                ->orderBy('spaces.sort_order')
                ->select(
                    'spaces.*',
                    'facilities.name as facility_name',
                    'facilities.short_name as facility_short_name'
                )
                ->first();
        }

        $weekStart = $request->filled('week')
            ? Carbon::parse($request->get('week'))->startOfWeek(Carbon::MONDAY)
            : Carbon::now()->startOfWeek(Carbon::MONDAY);

        $categories = DB::table('rental_categories')
            ->where('is_active', 1)
            ->orderBy('sort_order')
            ->get()
            ->map(function ($cat, $index) {
                $style = self::SOFT_PALETTE[$index % count(self::SOFT_PALETTE)];
                $cat->soft_bg = $style['bg'];
                $cat->soft_border = $style['border'];
                $cat->soft_text = $style['text'];
                return $cat;
            });

        $categoryStylesById = $categories->keyBy('id');

        $days = [];
        $dayStartHour = 6;
        $dayEndHour = 23;
        $totalMinutes = ($dayEndHour - $dayStartHour) * 60;

        for ($i = 0; $i < 7; $i++) {
            $date = $weekStart->copy()->addDays($i);
            $dateStr = $date->toDateString();

            $isPublished = false;
            $entries = [];

            if ($currentSpace) {
                $isPublished = (bool) DB::table('schedule_publications')
                    ->where('space_id', $currentSpace->id)
                    ->whereDate('schedule_date', $dateStr)
                    ->value('is_published');

                if ($isPublished) {
                    $dayStart = $date->copy()->startOfDay();
                    $dayEnd = $date->copy()->endOfDay();

                    $rawEntries = DB::table('schedule_entries')
                        ->leftJoin('rental_categories', 'schedule_entries.category_id', '=', 'rental_categories.id')
                        ->where('schedule_entries.space_id', $currentSpace->id)
                        ->whereNull('schedule_entries.deleted_at')
                        ->where('schedule_entries.start_at', '<', $dayEnd)
                        ->where('schedule_entries.end_at', '>', $dayStart)
                        ->select(
                            'schedule_entries.*',
                            'rental_categories.name as category_name'
                        )
                        ->orderBy('schedule_entries.start_at')
                        ->get();

                    foreach ($rawEntries as $entry) {
                        $start = Carbon::parse($entry->start_at);
                        $end = Carbon::parse($entry->end_at);

                        $startOffsetMin = ($start->hour * 60 + $start->minute) - ($dayStartHour * 60);
                        $durationMin = max(15, $start->diffInMinutes($end));

                        if ($startOffsetMin + $durationMin <= 0 || $startOffsetMin >= $totalMinutes) {
                            continue;
                        }

                        $leftPercent = max(0, ($startOffsetMin / $totalMinutes) * 100);
                        $widthPercent = min(100 - $leftPercent, ($durationMin / $totalMinutes) * 100);

                        if ($entry->entry_type === 'ice_resurfacing') {
                            $style = ['bg' => '#e9eef2', 'border' => '#475569', 'text' => '#1e293b'];
                            $categoryLabel = 'Подготовка льда';
                        } else {
                            $catStyle = $categoryStylesById->get($entry->category_id);
                            $style = $catStyle
                                ? ['bg' => $catStyle->soft_bg, 'border' => $catStyle->soft_border, 'text' => $catStyle->soft_text]
                                : ['bg' => '#f1f5f9', 'border' => '#64748b', 'text' => '#203847'];
                            $categoryLabel = $entry->category_name;
                        }

                        $entries[] = [
                            'id'            => $entry->id,
                            'title'         => $entry->title,
                            'notes'         => $entry->notes,
                            'time_label'    => $start->format('H:i') . '–' . $end->format('H:i'),
                            'type'          => $entry->entry_type,
                            'category_name' => $categoryLabel,
                            'bg'            => $style['bg'],
                            'border'        => $style['border'],
                            'text'          => $style['text'],
                            'left'          => $leftPercent,
                            'width'         => $widthPercent,
                        ];
                    }
                }
            }

            $weekdayShort = self::SHORT_WEEKDAYS[$date->dayOfWeekIso] ?? '';
            $monthGenitive = self::RU_MONTHS_GENITIVE[(int) $date->format('n')] ?? '';

            $days[] = [
                'date'         => $date,
                'date_str'     => $dateStr,
                'day_title'    => $weekdayShort . ', ' . $date->format('j') . ' ' . $monthGenitive,
                'is_today'     => $date->isToday(),
                'is_published' => $isPublished,
                'entries'      => $entries,
            ];
        }

        $sectionLabel = match ($currentSpace->space_type ?? 'ice') {
            'ice'     => 'АРЕНДА ЛЬДА / НЕДЕЛЬНЫЙ ПЛАН',
            'hall'    => 'АРЕНДА ЗАЛОВ / НЕДЕЛЬНЫЙ ПЛАН',
            'gym'     => 'ТРЕНАЖЁРНЫЙ ЗАЛ И ОФП / НЕДЕЛЬНЫЙ ПЛАН',
            'pool'    => 'БАССЕЙН / НЕДЕЛЬНЫЙ ПЛАН',
            default   => 'АРЕНДА ПОМЕЩЕНИЙ / НЕДЕЛЬНЫЙ ПЛАН',
        };

        $pageHeading = match ($currentSpace->space_type ?? 'ice') {
            'ice'     => 'План загрузки льда',
            'pool'    => 'План загрузки бассейна',
            default   => 'План загрузки помещения',
        };

        $monthHeader = mb_strtoupper(self::RU_MONTHS_GENITIVE[(int) $weekStart->format('n')] ?? '');

        return view('schedule.public-weekly', [
            'currentSpace' => $currentSpace,
            'weekStart'    => $weekStart,
            'categories'   => $categories,
            'days'         => $days,
            'dayStartHour' => $dayStartHour,
            'dayEndHour'   => $dayEndHour,
            'sectionLabel' => $sectionLabel,
            'pageHeading'  => $pageHeading,
            'monthHeader'  => $monthHeader,
        ]);
    }
}