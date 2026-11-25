<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Заявки — Ледовый дворец «Ижсталь»</title>
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
            <a href="{{ route('admin.requests.index') }}" class="admin-section-tab is-active">
                Заявки
                @if($counts['new'] > 0)<span class="rr-badge-count">{{ $counts['new'] }}</span>@endif
            </a>
            <a href="{{ route('admin.schedule.index', ['section' => 'ice']) }}" class="admin-section-tab">Расписание</a>
        </nav>
        <div style="display:flex; align-items:center; gap:14px;">
            <span style="font-size:13px; font-weight:700; color:var(--ink-soft);">Администратор: <strong>{{ auth()->user()->name ?? 'admin' }}</strong></span>
            <a href="{{ url('/') }}" class="facility-pill" style="padding:7px 12px;">На сайт ↗</a>
            <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                @csrf
                <button type="submit" class="btn-admin btn-admin-primary" style="padding:7px 14px; font-size:12px;">Выйти</button>
            </form>
        </div>
    </div>
</header>

<main class="admin-container">
    @if(session('success'))<div class="alert-box alert-success">{{ session('success') }}</div>@endif
    @if($errors->any())
        <div class="alert-box alert-error">
            @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
        </div>
    @endif

    <div class="rr-heading">
        <div class="eyebrow"><span class="eyebrow-line"></span>Управление</div>
        <h1 class="rr-title">Заявки на аренду</h1>
    </div>

    <div class="rr-filters">
        <a href="{{ route('admin.requests.index', ['status' => 'all']) }}" class="rr-filter {{ $status === 'all' ? 'is-active' : '' }}">Все <span class="rr-filter-count">{{ $counts['all'] }}</span></a>
        @foreach($statuses as $key => $label)
            <a href="{{ route('admin.requests.index', ['status' => $key]) }}" class="rr-filter {{ $status === $key ? 'is-active' : '' }}">{{ $label }} <span class="rr-filter-count">{{ $counts[$key] }}</span></a>
        @endforeach
    </div>

    @if($requests->isEmpty())
        <div class="rr-empty"><div class="rr-empty-icon">📋</div><div class="rr-empty-title">Заявок нет</div></div>
    @else
        <div class="rr-list">
            @foreach($requests as $r)
                @php
                    $startHm = substr($r->preferred_start_time, 0, 5);
                    $endHm   = \Carbon\Carbon::parse($r->preferred_date . ' ' . $r->preferred_start_time)->addMinutes((int)$r->duration_minutes)->format('H:i');
                @endphp
                <article class="rr-card">
                    <div class="rr-card-head">
                        <span class="rr-status {{ $classes[$r->status] ?? '' }}">{{ $statuses[$r->status] ?? $r->status }}</span>
                        <span class="rr-created">Создана {{ \Carbon\Carbon::parse($r->created_at)->format('d.m.Y H:i') }}</span>
                    </div>
                    <div class="rr-card-title">{{ $r->title }}</div>
                    <div class="rr-card-space">
                        <b>{{ $r->facility_short_name ?? $r->facility_name ?? '—' }}</b>
                        @if($r->space_name) — {{ $r->space_name }} @endif
                        @if($r->category_name)<span class="rr-cat" style="background:{{ $r->category_color }}; color:{{ $r->category_text_color }};">{{ $r->category_name }}</span>@endif
                    </div>

                    <div class="rr-grid">
                        <div class="rr-cell"><span class="rr-label">Дата</span><div class="rr-value">{{ \Carbon\Carbon::parse($r->preferred_date)->format('d.m.Y') }}</div></div>
                        <div class="rr-cell"><span class="rr-label">Время</span><div class="rr-value">{{ $startHm }} – {{ $endHm }} <small>({{ $r->duration_minutes }} мин)</small></div></div>
                        <div class="rr-cell"><span class="rr-label">Арендатор</span><div class="rr-value">{{ $r->renter_name }}</div></div>
                        <div class="rr-cell"><span class="rr-label">Телефон</span><div class="rr-value"><a href="tel:{{ preg_replace('/[^0-9+]/','',$r->renter_phone) }}">{{ $r->renter_phone }}</a></div></div>
                        <div class="rr-cell"><span class="rr-label">Email</span><div class="rr-value">{{ $r->renter_email ?: '—' }}</div></div>
                    </div>

                    @if($r->notes)<div class="rr-notes"><span class="rr-label">Пожелания</span><div class="rr-value">{{ $r->notes }}</div></div>@endif

                    <div class="rr-actions">
                        <form method="POST" action="{{ route('admin.requests.update-status', $r->id) }}" class="rr-status-form">
                            @csrf @method('PATCH')
                            @if($r->status === 'approved')
                                <span class="rr-status-fixed">Статус «Одобрена»</span>
                                <input type="hidden" name="status" value="approved">
                            @else
                                <select name="status" class="form-control" style="max-width:180px;">
                                    @foreach($manualStatuses as $key => $label)
                                        <option value="{{ $key }}" {{ $r->status === $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            @endif
                            <input type="text" name="admin_comment" value="{{ $r->admin_comment }}" class="form-control" placeholder="Комментарий" style="max-width:280px;">
                            @if($r->status !== 'approved')<button type="submit" class="btn-admin btn-admin-primary" style="padding:9px 14px; font-size:12px;">Сохранить</button>@endif
                        </form>

                        <div class="rr-actions-right">
                            @if($r->status !== 'approved')
                                <form method="POST" action="{{ route('admin.requests.approve', $r->id) }}" style="margin:0;">
                                    @csrf
                                    <button type="submit" class="btn-admin btn-admin-prep" style="padding:9px 14px; font-size:12px;" onclick="return confirm('Одобрить и добавить запись в расписание?');">Одобрить в расписание</button>
                                </form>
                            @else
                                <span class="rr-approved-hint">✓ Запись создана</span>
                            @endif
                            <form method="POST" action="{{ route('admin.requests.destroy', $r->id) }}" style="margin:0;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-admin btn-admin-danger" style="padding:9px 14px; font-size:12px;" onclick="return confirm('Удалить заявку?');">Удалить</button>
                            </form>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
        @if($requests->hasPages())<div class="rr-pagination">{{ $requests->links() }}</div>@endif
    @endif
</main>
</body>
</html>