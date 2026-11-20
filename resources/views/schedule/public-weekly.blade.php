@extends('layouts.app')

@section('title', ($pageHeading ?? 'План загрузки') . ' — Ледовый дворец «Ижсталь»')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/public-schedule.css') }}">
@endpush

@section('content')
<section class="schedule-view-wrapper">
    <div class="container">
        <div class="schedule-header-meta">{{ $sectionLabel }}</div>
        <h1 class="schedule-main-title">{{ $pageHeading }}</h1>
        <div class="schedule-sub-title">
            {{ $currentSpace ? ($currentSpace->facility_name . ' — ' . $currentSpace->name) : 'Объект не выбран' }}
        </div>

        <div class="schedule-nav-row">
            <div class="schedule-nav-left">
                <span class="week-picker-date">
                    {{ $weekStart->format('d.m.Y') }} — {{ $weekStart->copy()->endOfWeek()->format('d.m.Y') }}
                </span>
                <div class="week-arrows">
                    <a href="{{ route('schedule.public', ['space_id' => $currentSpace?->id, 'week' => $weekStart->copy()->subWeek()->toDateString()]) }}"
                       class="btn-schedule-action btn-schedule-icon" title="Предыдущая неделя">‹</a>
                    <a href="{{ route('schedule.public', ['space_id' => $currentSpace?->id, 'week' => $weekStart->copy()->addWeek()->toDateString()]) }}"
                       class="btn-schedule-action btn-schedule-icon" title="Следующая неделя">›</a>
                    <a href="{{ route('schedule.public', ['space_id' => $currentSpace?->id]) }}"
                       class="btn-schedule-action">Сегодня</a>
                </div>
            </div>

            <div class="view-mode-switch">
                <button type="button" class="view-mode-btn is-active" data-mode="week">Неделя</button>
                <button type="button" class="view-mode-btn" data-mode="day">День</button>
            </div>
        </div>

        <div class="categories-legend">
            @foreach($categories as $cat)
                <span class="legend-pill">
                    <span class="legend-dot" style="background: {{ $cat->soft_border }};"></span>
                    {{ $cat->name }}
                </span>
            @endforeach
            <span class="legend-pill">
                <span class="legend-dot" style="background: #475569;"></span>
                Подготовка льда
            </span>
        </div>

        <div class="timeline-board">
            <div class="timeline-inner">
                <div class="timeline-header-row">
                    <div class="timeline-day-col-header">День / {{ $monthHeader }}</div>
                    <div class="timeline-hours-container">
                        @for($hour = $dayStartHour; $hour <= $dayEndHour; $hour++)
                            <div class="hour-marker">{{ sprintf('%02d:00', $hour) }}</div>
                        @endfor
                    </div>
                </div>

                @foreach($days as $day)
                    <div class="timeline-day-row" data-date="{{ $day['date_str'] }}" data-is-today="{{ $day['is_today'] ? '1' : '0' }}">
                        <div class="timeline-day-label-cell">
                            <div class="day-cell-name">{{ $day['day_title'] }}</div>
                            @if($day['is_today'])
                                <span class="day-today-badge">Сегодня</span>
                            @endif
                            <div class="day-cell-sub">
                                {{ $day['date']->isWeekend() ? 'Выходной' : 'Будний день' }}
                            </div>
                        </div>

                        <div class="timeline-events-cell">
                            @if($day['is_published'] && count($day['entries']) > 0)
                                @foreach($day['entries'] as $event)
                                    <div class="event-card"
                                         style="left: {{ $event['left'] }}%; width: {{ $event['width'] }}%; background: {{ $event['bg'] }}; border-color: {{ $event['border'] }}; color: {{ $event['text'] }};"
                                         data-time="{{ $event['time_label'] }}"
                                         data-title="{{ $event['title'] }}"
                                         data-category="{{ $event['category_name'] ?? '' }}"
                                         data-notes="{{ $event['notes'] ?? '' }}">
                                        <div class="event-time">{{ $event['time_label'] }}</div>
                                        <div class="event-title">{{ $event['title'] }}</div>
                                        @if($event['category_name'])
                                            <div class="event-category">{{ $event['category_name'] }}</div>
                                        @endif
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="schedule-footer-notes">
            <p>Свободные интервалы показаны белым. Время местное.</p>
            <a href="#" onclick="window.print(); return false;" class="btn-schedule-pdf">
                ↓ Скачать PDF
            </a>
        </div>
    </div>
</section>

<div class="schedule-tooltip" id="scheduleTooltip" role="tooltip">
    <div class="schedule-tooltip-time" id="tooltipTime"></div>
    <div class="schedule-tooltip-title" id="tooltipTitle"></div>
    <div class="schedule-tooltip-category" id="tooltipCategory"></div>
    <div class="schedule-tooltip-notes" id="tooltipNotes"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const modeButtons = document.querySelectorAll('.view-mode-btn');
    const dayRows = document.querySelectorAll('.timeline-day-row');

    modeButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            modeButtons.forEach(b => b.classList.remove('is-active'));
            btn.classList.add('is-active');

            const mode = btn.dataset.mode;
            if (mode === 'week') {
                dayRows.forEach(row => { row.style.display = ''; });
                return;
            }

            const todayRow = Array.from(dayRows).find(r => r.dataset.isToday === '1') || dayRows[0];
            dayRows.forEach(row => {
                row.style.display = (row === todayRow) ? '' : 'none';
            });
        });
    });

    const tooltip = document.getElementById('scheduleTooltip');
    const tooltipTime = document.getElementById('tooltipTime');
    const tooltipTitle = document.getElementById('tooltipTitle');
    const tooltipCategory = document.getElementById('tooltipCategory');
    const tooltipNotes = document.getElementById('tooltipNotes');

    function positionTooltip(card) {
        const rect = card.getBoundingClientRect();
        const tooltipRect = tooltip.getBoundingClientRect();

        let centerX = rect.left + rect.width / 2;
        const halfWidth = tooltipRect.width / 2 || 110;
        centerX = Math.max(halfWidth + 12, Math.min(window.innerWidth - halfWidth - 12, centerX));

        let top = rect.top - tooltipRect.height - 10;
        if (top < 12) {
            top = rect.bottom + 10;
        }

        tooltip.style.left = `${centerX}px`;
        tooltip.style.top = `${top}px`;
    }

    document.querySelectorAll('.event-card').forEach(card => {
        card.addEventListener('mouseenter', () => {
            tooltipTime.textContent = card.dataset.time || '';
            tooltipTitle.textContent = card.dataset.title || '';

            if (card.dataset.category) {
                tooltipCategory.textContent = card.dataset.category;
                tooltipCategory.style.display = '';
            } else {
                tooltipCategory.style.display = 'none';
            }

            if (card.dataset.notes) {
                tooltipNotes.textContent = card.dataset.notes;
                tooltipNotes.style.display = '';
            } else {
                tooltipNotes.style.display = 'none';
            }

            tooltip.classList.add('is-visible');
            positionTooltip(card);
        });

        card.addEventListener('mousemove', () => {
            if (tooltip.classList.contains('is-visible')) {
                positionTooltip(card);
            }
        });

        card.addEventListener('mouseleave', () => {
            tooltip.classList.remove('is-visible');
        });
    });

    window.addEventListener('scroll', () => tooltip.classList.remove('is-visible'), true);
});
</script>
@endsection