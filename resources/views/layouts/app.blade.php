<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#f3faff">
    <meta name="description" content="Ледовый дворец «Ижсталь» — спортивные объекты, расписание аренды льда и помещений, контакты.">
    <title>@yield('title', 'Ледовый дворец «Ижсталь»')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Unbounded:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="icon" type="image/png" href="{{ asset('img/favicon.png') }}?v=1">
    @stack('styles')
</head>
<body>
    <div class="announcement-bar">
        <div class="container announcement-inner">
            <span class="announcement-mark"><span></span> Официальный сайт</span>
            <span class="announcement-note">Спорт объединяет. Движение вдохновляет.</span>
            <a href="https://vk.ru/public170007616" target="_blank" rel="noopener" class="announcement-social">Мы во ВКонтакте <span aria-hidden="true">↗</span></a>
        </div>
    </div>

    <header class="site-header" id="top">
        <div class="container header-inner">
            <a class="brand" href="{{ url('/') }}" aria-label="Ледовый дворец Ижсталь — главная">
                <img class="brand-logo" src="{{ asset('img/logo.png') }}" alt="Логотип Ледового дворца «Ижсталь»">
            </a>

            <button class="mobile-toggle" id="mobileToggle" type="button" aria-label="Открыть меню" aria-expanded="false" aria-controls="mainNav">
                <span></span><span></span>
            </button>

            <nav class="main-nav" id="mainNav" aria-label="Основная навигация">
                <a class="nav-link is-current" href="{{ url('/') }}">Главная</a>
                <a class="nav-link" href="{{ url('/documents') }}">Документы</a>
                <a class="nav-link" href="{{ url('/documents/figure-skating') }}">Документы школы фигурного катания</a>
                <a class="nav-link" href="{{ url('/contacts') }}">Контакты</a>
                <div class="nav-mobile-extra">
                    <a href="https://vk.ru/public170007616" target="_blank" rel="noopener">ВКонтакте ↗</a>
                </div>      
            </nav>

            <a class="header-contact" href="tel:+73412572051"><span class="contact-icon">↗</span><span><small>Приёмная</small><strong>+7 (3412) 57-20-51</strong></span></a>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="site-footer" id="contacts">
        <div class="container footer-main">
            <div class="footer-brand-block">
                <a class="brand brand-footer" href="{{ url('/') }}">
                    <img class="brand-logo brand-logo-footer" src="{{ asset('img/logo.png') }}" alt="Логотип Ледового дворца «Ижсталь»">
                </a>
                <p>Пространство спорта, движения<br>и новых достижений.</p>
                <a class="footer-vk" href="https://vk.ru/public170007616" target="_blank" rel="noopener">ВКонтакте <span>↗</span></a>
            </div>
            <div class="footer-column">
                <span class="footer-eyebrow">НАВИГАЦИЯ</span>
                <a href="{{ url('/') }}">Главная</a>
                <a href="#facilities">Спортивные объекты</a>
                <a href="#schedule">Расписание</a>
                <a href="#gallery">Фотогалерея</a>
                <a href="#documents">Документы</a>
            </div>
            <div class="footer-column footer-address">
                <span class="footer-eyebrow">МЫ НАХОДИМСЯ</span>
                <p>426034, Удмуртская Республика,<br>г. Ижевск, ул. Удмуртская, 222А<br>(КК «Олимпиец»)</p>
                <a href="mailto:mail@izhstal.udmr.ru">mail@izhstal.udmr.ru</a>
                <a href="tel:+73412572061">+7 (3412) 57-20-61 — администраторы</a>
            </div>
            <div class="footer-column footer-hours">
                <span class="footer-eyebrow">СВЯЗАТЬСЯ</span>
                <a class="footer-phone" href="tel:+73412572051">+7 (3412) 57-20-51</a>
                <span>Приёмная</span>
                <a class="footer-map-link" href="https://yandex.ru/maps/?text=Ижевск%20Удмуртская%20222А" target="_blank" rel="noopener">Построить маршрут <b>↗</b></a>
            </div>
        </div>
        <div class="container footer-bottom"><span>© {{ date('Y') }} АУ УР «Ледовый дворец „Ижсталь“»</span><span>Официальный сайт учреждения</span><a href="#hero-block">Наверх ↑</a></div>
    </footer>

    <script src="{{ asset('js/app.js') }}"></script>

    @stack('scripts')
</body>
</html>
