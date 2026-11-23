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

        @php
            $ruShortDays = [
                1 => 'Пн',
                2 => 'Вт',
                3 => 'Ср',
                4 => 'Чт',
                5 => 'Пт',
                6 => 'Сб',
                7 => 'Вс',
            ];
            $hasToday = collect($days)->contains('is_today', true);
        @endphp

        <div class="mobile-day-picker" id="mobileDayPicker" aria-label="Выбор дня недели">
            @foreach($days as $index => $day)
                @php
                    $isActiveMobile = $day['is_today'] || ($index === 0 && !$hasToday);
                    $shortDay = $ruShortDays[$day['date']->dayOfWeekIso] ?? 'День';
                @endphp
                <button type="button"
                        class="mobile-day-btn {{ $isActiveMobile ? 'is-active' : '' }}"
                        data-date="{{ $day['date_str'] }}">
                    <span class="mobile-day-short">{{ $shortDay }}</span>
                    <span class="mobile-day-num">{{ $day['date']->format('d.m') }}</span>
                </button>
            @endforeach
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

                @foreach($days as $index => $day)
                    @php
                        $isDefaultMobileDay = $day['is_today'] || ($index === 0 && !$hasToday);
                    @endphp
                    <div class="timeline-day-row {{ $isDefaultMobileDay ? 'is-mobile-active' : '' }}"
                         data-date="{{ $day['date_str'] }}"
                         data-is-today="{{ $day['is_today'] ? '1' : '0' }}">
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
                                         data-category="{{ $event['category_name'] ?? '' }}">
                                        <div class="event-card-content">
                                            <div class="event-time">{{ $event['time_label'] }}</div>
                                            <div class="event-title">{{ $event['title'] }}</div>
                                            @if($event['category_name'])
                                                <div class="event-category">{{ $event['category_name'] }}</div>
                                            @endif
                                        </div>
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
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const modeButtons = document.querySelectorAll('.view-mode-btn');
    const dayRows = document.querySelectorAll('.timeline-day-row');
    const mobileDayButtons = document.querySelectorAll('.mobile-day-btn');
    const eventCards = document.querySelectorAll('.event-card');
    const mobileMedia = window.matchMedia('(max-width: 991.98px)');

    function updateNarrowCards() {
        if (mobileMedia.matches) {
            eventCards.forEach(card => card.classList.remove('is-narrow'));
            return;
        }
        eventCards.forEach(card => {
            card.classList.toggle('is-narrow', card.getBoundingClientRect().width < 58);
        });
    }

    function selectMobileDay(dateStr) {
        mobileDayButtons.forEach(btn => {
            btn.classList.toggle('is-active', btn.dataset.date === dateStr);
        });
        dayRows.forEach(row => {
            row.classList.toggle('is-mobile-active', row.dataset.date === dateStr);
        });
    }

    mobileDayButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            selectMobileDay(btn.dataset.date);
        });
    });

    function syncLayoutByViewport() {
        if (mobileMedia.matches) {
            dayRows.forEach(row => { row.style.display = ''; });
            const activeBtn = Array.from(mobileDayButtons).find(b => b.classList.contains('is-active')) || mobileDayButtons[0];
            if (activeBtn) {
                selectMobileDay(activeBtn.dataset.date);
            }
        } else {
            const activeModeBtn = Array.from(modeButtons).find(b => b.classList.contains('is-active'));
            const mode = activeModeBtn ? activeModeBtn.dataset.mode : 'week';
            if (mode === 'week') {
                dayRows.forEach(row => { row.style.display = ''; });
            } else {
                const todayRow = Array.from(dayRows).find(r => r.dataset.isToday === '1') || dayRows[0];
                dayRows.forEach(row => {
                    row.style.display = (row === todayRow) ? '' : 'none';
                });
            }
        }
        updateNarrowCards();
    }

    modeButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            modeButtons.forEach(b => b.classList.remove('is-active'));
            btn.classList.add('is-active');
            syncLayoutByViewport();
        });
    });

    if (mobileMedia.addEventListener) {
        mobileMedia.addEventListener('change', syncLayoutByViewport);
    } else {
        mobileMedia.addListener(syncLayoutByViewport);
    }

    window.addEventListener('resize', updateNarrowCards);
    requestAnimationFrame(updateNarrowCards);

    const tooltip = document.getElementById('scheduleTooltip');
    const tooltipTime = document.getElementById('tooltipTime');
    const tooltipTitle = document.getElementById('tooltipTitle');
    const tooltipCategory = document.getElementById('tooltipCategory');

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

    eventCards.forEach(card => {
        card.addEventListener('mouseenter', () => {
            if (mobileMedia.matches) return;
            tooltipTime.textContent = card.dataset.time || '';
            tooltipTitle.textContent = card.dataset.title || '';

            if (card.dataset.category) {
                tooltipCategory.textContent = card.dataset.category;
                tooltipCategory.style.display = '';
            } else {
                tooltipCategory.style.display = 'none';
            }

            tooltip.classList.add('is-visible');
            positionTooltip(card);
        });

        card.addEventListener('mousemove', () => {
            if (!mobileMedia.matches && tooltip.classList.contains('is-visible')) {
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