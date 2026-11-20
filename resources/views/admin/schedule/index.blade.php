<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Администратор — Ледовый дворец «Ижсталь»</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&family=Unbounded:wght@500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body class="admin-body">

<header class="admin-topbar">
    <div class="admin-topbar-inner">
        <div class="admin-brand">
            <span class="admin-brand-badge">Панель управления</span>
            <span class="admin-brand-title">ЛД «Ижсталь»</span>
        </div>

        <nav class="admin-section-tabs">
            <a href="{{ route('admin.schedule.index', ['section' => 'ice', 'week' => $weekStart->toDateString()]) }}"
               class="admin-section-tab {{ $section === 'ice' ? 'is-active' : '' }}">
                Аренда льда
            </a>
            <a href="{{ route('admin.schedule.index', ['section' => 'hall', 'week' => $weekStart->toDateString()]) }}"
               class="admin-section-tab {{ $section === 'hall' ? 'is-active' : '' }}">
                Аренда помещений
            </a>
            <a href="{{ route('admin.schedule.index', ['section' => 'foyer', 'week' => $weekStart->toDateString()]) }}"
               class="admin-section-tab {{ $section === 'foyer' ? 'is-active' : '' }}">
                Аренда фойе
            </a>
        </nav>

        <div style="display:flex; align-items:center; gap:14px;">
            <span style="font-size:13px; font-weight:700; color:var(--ink-soft);">
                Администратор: <strong>{{ auth()->user()->name ?? 'admin' }}</strong>
            </span>
            <a href="{{ url('/') }}" class="facility-pill" style="padding:7px 12px;">На сайт ↗</a>
            <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                @csrf
                <button type="submit" class="btn-admin btn-admin-primary" style="padding:7px 14px; font-size:12px;">Выйти</button>
            </form>
        </div>
    </div>
</header>

<main class="admin-container">

    @if(session('success'))
        <div class="alert-box alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert-box alert-error">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    {{-- Выбор площадки / зала из таблицы spaces --}}
    <div class="facility-pills">
        @foreach($facilities as $space)
            <a href="{{ route('admin.schedule.index', ['section' => $section, 'space_id' => $space->id, 'week' => $weekStart->toDateString()]) }}"
               class="facility-pill {{ $currentSpace && (int) $currentSpace->id === (int) $space->id ? 'is-active' : '' }}">
                {{ $space->facility_short_name ?? $space->facility_name }} — {{ $space->name }}
            </a>
        @endforeach
    </div>

    {{-- Навигация по неделям и легенда цветов --}}
    <div class="admin-controls-bar">
        <div class="week-nav">
            <a class="week-nav-btn"
               href="{{ route('admin.schedule.index', ['section' => $section, 'space_id' => $currentSpace?->id, 'week' => $weekStart->copy()->subWeek()->toDateString()]) }}">
                ←
            </a>
            <div class="week-nav-title">
                {{ $weekStart->format('d.m.Y') }} — {{ $weekStart->copy()->endOfWeek()->format('d.m.Y') }}
            </div>
            <a class="week-nav-btn"
               href="{{ route('admin.schedule.index', ['section' => $section, 'space_id' => $currentSpace?->id, 'week' => $weekStart->copy()->addWeek()->toDateString()]) }}">
                →
            </a>
        </div>

        <div class="slot-legend">
            <div class="legend-item"><span class="legend-dot" style="background:#2563eb;"></span> Занятое время</div>
            <div class="legend-item"><span class="legend-dot" style="background:#22c55e;"></span> Начало свободного</div>
            <div class="legend-item"><span class="legend-dot" style="background:#e2e8f0;"></span> Свободно</div>
            <div class="legend-item"><span class="legend-dot" style="background:#18181b;"></span> Заливка / подготовка</div>
        </div>
    </div>

    {{-- Дни недели (Пн - Вс) --}}
    @foreach($days as $day)
        <section class="day-card">
            <div class="day-card-header">
                <div class="day-card-title">
                    {{ $day['date']->format('d.m.Y') }}
                    <span>{{ $day['date']->translatedFormat('l') }}</span>
                </div>

                <form method="POST" action="{{ route('admin.days.toggle-publish') }}" style="margin:0;">
                    @csrf
                    <input type="hidden" name="space_id" value="{{ $currentSpace?->id }}">
                    <input type="hidden" name="date" value="{{ $day['date_str'] }}">
                    <button type="submit" class="btn-publish {{ $day['is_published'] ? 'is-published' : 'is-draft' }}">
                        {{ $day['is_published'] ? 'Убрать день' : 'Опубликовать день' }}
                    </button>
                </form>
            </div>

            <div class="slots-grid">
                @foreach($day['slots'] as $slot)
                    @php
                        $b = $slot['booking'];
                        $stateClass = match($slot['state']) {
                            'busy'       => 'slot-busy',
                            'free-start' => 'slot-free-start',
                            'prep'       => 'slot-prep',
                            default      => 'slot-free',
                        };
                    @endphp
                    <div class="slot-cell {{ $stateClass }}"
                         data-date="{{ $day['date_str'] }}"
                         data-date-human="{{ $day['date']->format('d.m.Y') }}"
                         data-time="{{ $slot['time'] }}"
                         data-booking='@json($b)'
                         title="{{ $b ? ($b->entry_type === 'ice_resurfacing' ? 'Заливка льда' : $b->title) : 'Свободно — двойной клик для записи' }}">
                        <span class="slot-time">{{ $slot['time'] }}</span>
                        @if($b)
                            <span class="slot-sub">{{ $b->entry_type === 'ice_resurfacing' ? 'ЗАЛИВКА' : $b->title }}</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </section>
    @endforeach
</main>

{{-- МОДАЛЬНОЕ ОКНО  --}}
<div class="admin-modal-backdrop" id="bookingModal">
    <div class="admin-modal">
        <button type="button" class="admin-modal-close" id="closeModalBtn">×</button>
        <div class="eyebrow">
            <span class="eyebrow-line"></span>
            <span id="modalSubtitle">{{ $currentSpace ? (($currentSpace->facility_short_name ?? $currentSpace->facility_name) . ' — ' . $currentSpace->name) : '' }}</span>
        </div>
        <h3 id="modalTitle">Редакция расписания</h3>

        <form id="bookingForm" method="POST" action="{{ route('admin.bookings.store') }}">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <input type="hidden" name="space_id" value="{{ $currentSpace?->id }}">
            <input type="hidden" name="date" id="inputDate">
            <input type="hidden" name="start_time" id="inputStartTime">
            <input type="hidden" name="entry_type" id="inputEntryType" value="rental">

            <div class="form-group">
                <label class="form-label">Арендатор / название записи (команда, тренер, № раздевалок)</label>
                <input type="text" name="title" id="inputTitle" class="form-control" placeholder="Например: ХК Ижсталь разд. 08">
            </div>

            <div class="form-group" style="max-width: 220px;">
                <label class="form-label">Длительность аренды</label>
                <select name="duration_minutes" id="inputDuration" class="form-control">
                    @for($m = 15; $m <= 240; $m += 15)
                        @php
                            $hours = intdiv($m, 60);
                            $mins  = $m % 60;
                            $label = sprintf('%02d:%02d', $hours, $mins);
                        @endphp
                        <option value="{{ $m }}" {{ $m === 60 ? 'selected' : '' }}>{{ $label }}</option>
                    @endfor
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Принадлежность арендатора</label>
                <div class="category-grid">
                    @foreach($categories as $index => $cat)
                        <label class="category-tile" style="background: {{ $cat->color }}; color: {{ $cat->text_color }};">
                            <span>{{ $cat->name }}</span>
                            <input type="radio" name="category_id" value="{{ $cat->id }}" {{ $index === count($categories) - 1 ? 'checked' : '' }}>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Примечание</label>
                <input type="text" name="notes" id="inputNotes" class="form-control" placeholder="Дополнительные пометки (необязательно)">
            </div>

            <div class="form-group">
                <label class="form-label">Контактный телефон арендатора</label>
                <input type="tel" name="renter_phone" id="inputRenterPhone" class="form-control" placeholder="+7 (900) 000-00-00">
            </div>

            <div class="form-group">
                <label class="form-label">Email арендатора</label>
                <input type="email" name="renter_email" id="inputRenterEmail" class="form-control" placeholder="renter@example.ru">
            </div>

            <div class="modal-actions">
                <div>
                    <button type="button" id="deleteBtn" class="btn-admin btn-admin-danger" style="display:none;">Удалить</button>
                </div>
                <div style="display:flex; gap:10px; flex-wrap:wrap;">
                    <button type="button" id="prepSubmitBtn" class="btn-admin btn-admin-prep">Подготовка (заливка)</button>
                    <button type="submit" id="saveSubmitBtn" class="btn-admin btn-admin-primary">Добавить запись</button>
                </div>
            </div>
        </form>

        <form id="deleteForm" method="POST" style="display:none;">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('bookingModal');
    const closeBtn = document.getElementById('closeModalBtn');
    const form = document.getElementById('bookingForm');
    const deleteForm = document.getElementById('deleteForm');
    const deleteBtn = document.getElementById('deleteBtn');
    const prepSubmitBtn = document.getElementById('prepSubmitBtn');
    const saveSubmitBtn = document.getElementById('saveSubmitBtn');
    const defaultCategoryRadio = document.querySelector('input[name="category_id"]:last-of-type');

    function calcDurationMinutes(startAt, endAt) {
        const s = new Date(startAt.replace(' ', 'T'));
        const e = new Date(endAt.replace(' ', 'T'));
        const diff = Math.round((e - s) / 60000);
        return diff > 0 ? diff : 60;
    }

    function openCreateModal(cell) {
        form.action = "{{ route('admin.bookings.store') }}";
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('modalTitle').textContent = `Запись на ${cell.dataset.dateHuman} | ${cell.dataset.time}`;
        document.getElementById('inputDate').value = cell.dataset.date;
        document.getElementById('inputStartTime').value = cell.dataset.time;
        document.getElementById('inputEntryType').value = 'rental';
        document.getElementById('inputTitle').value = '';
        document.getElementById('inputDuration').value = '60';
        document.getElementById('inputNotes').value = '';
        document.getElementById('inputRenterPhone').value = '';
        document.getElementById('inputRenterEmail').value = '';
        if (defaultCategoryRadio) defaultCategoryRadio.checked = true;

        deleteBtn.style.display = 'none';
        saveSubmitBtn.textContent = 'Добавить запись';
        modal.classList.add('is-open');
    }

    function openEditModal(cell, booking) {
        form.action = `/admin/bookings/${booking.id}`;
        document.getElementById('formMethod').value = 'PUT';
        deleteForm.action = `/admin/bookings/${booking.id}`;

        const startTime = String(booking.start_at).substring(11, 16);
        document.getElementById('modalTitle').textContent = `Редакция записи на ${cell.dataset.dateHuman} | ${startTime}`;
        document.getElementById('inputDate').value = cell.dataset.date;
        document.getElementById('inputStartTime').value = startTime;
        document.getElementById('inputEntryType').value = booking.entry_type || 'rental';
        document.getElementById('inputTitle').value = booking.title || '';
        document.getElementById('inputDuration').value = String(calcDurationMinutes(booking.start_at, booking.end_at));
        document.getElementById('inputNotes').value = booking.notes || '';
        document.getElementById('inputRenterPhone').value = booking.renter_phone || '';
        document.getElementById('inputRenterEmail').value = booking.renter_email || '';

        if (booking.category_id) {
            const catRadio = document.querySelector(`input[name="category_id"][value="${booking.category_id}"]`);
            if (catRadio) catRadio.checked = true;
        } else if (defaultCategoryRadio) {
            defaultCategoryRadio.checked = true;
        }

        deleteBtn.style.display = 'inline-block';
        saveSubmitBtn.textContent = 'Изменить запись';
        modal.classList.add('is-open');
    }

    document.querySelectorAll('.slot-cell').forEach(cell => {
        const booking = JSON.parse(cell.dataset.booking || 'null');

        cell.addEventListener('click', () => {
            if (booking) openEditModal(cell, booking);
        });

        cell.addEventListener('dblclick', () => {
            if (!booking) openCreateModal(cell);
        });
    });

    prepSubmitBtn.addEventListener('click', () => {
        document.getElementById('inputEntryType').value = 'ice_resurfacing';
        form.submit();
    });

    saveSubmitBtn.addEventListener('click', () => {
        document.getElementById('inputEntryType').value = 'rental';
    });

    deleteBtn.addEventListener('click', () => {
        if (confirm('Удалить эту запись из расписания?')) {
            deleteForm.submit();
        }
    });

    closeBtn.addEventListener('click', () => modal.classList.remove('is-open'));
    modal.addEventListener('click', (e) => {
        if (e.target === modal) modal.classList.remove('is-open');
    });
});
</script>
</body>
</html>