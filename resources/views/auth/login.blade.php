@extends('layouts.app')

@section('title', 'Вход в панель управления — Ледовый дворец «Ижсталь»')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endpush

@section('content')
<section class="auth-page-wrap">
    <div class="auth-card">
        <div class="auth-eyebrow">ЛД «Ижсталь»</div>
        <h1 class="auth-title">Вход в систему</h1>
        <p class="auth-subtitle">Введите данные учётной записи для управления расписанием объектов.</p>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="auth-field">
                <label for="email" class="auth-label">Email</label>
                <input id="email"
                       type="email"
                       class="auth-input @error('email') is-invalid @enderror"
                       name="email"
                       value="{{ old('email') }}"
                       placeholder="email"
                       required
                       autocomplete="email"
                       autofocus>

                @error('email')
                    <span class="auth-error" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="auth-field">
                <label for="password" class="auth-label">Пароль</label>
                <input id="password"
                       type="password"
                       class="auth-input @error('password') is-invalid @enderror"
                       name="password"
                       placeholder="••••••••"
                       required
                       autocomplete="current-password">

                @error('password')
                    <span class="auth-error" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="auth-row-between">
                <label class="auth-check" for="remember">
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <span>Запомнить меня</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="auth-link" href="{{ route('password.request') }}">
                        Забыли пароль?
                    </a>
                @endif
            </div>

            <button type="submit" class="auth-submit">
                Войти
            </button>
        </form>

        <div class="auth-footer-link">
            <a href="{{ url('/') }}">← Вернуться на главную</a>
        </div>
    </div>
</section>
@endsection