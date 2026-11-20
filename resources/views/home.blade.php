@extends('layouts.app')

@section('title', 'Ледовый дворец «Ижсталь» — спорт начинается здесь')

@section('content')
@php
    $scheduleWeeks = [
        ['label' => '2 — 8 ноября', 'start' => '02.11.2026', 'end' => '08.11.2026', 'date' => '2026-11-02'],
        ['label' => '9 — 15 ноября', 'start' => '09.11.2026', 'end' => '15.11.2026', 'date' => '2026-11-09'],
    ];
    $scheduleGroups = [
        ['name' => 'Крытый каток «Олимпиец» — ледовая арена', 'type' => 'Ледовая арена', 'icon' => '✳', 'icebox' => 2, 'space_id' => 1],
        ['name' => 'ЦАС «Удмуртия» — ледовая арена', 'type' => 'Ледовая арена', 'icon' => '✳', 'icebox' => 3, 'space_id' => 4],
        ['name' => 'Ледовая арена «Можга»', 'type' => 'Ледовая арена', 'icon' => '✳', 'icebox' => 8, 'space_id' => 8],
        ['name' => 'Хореографический зал — КК «Олимпиец»', 'type' => 'Зал', 'icon' => '⌁', 'icebox' => 4, 'space_id' => 2],
        ['name' => 'Зал ОФП — КК «Олимпиец»', 'type' => 'Зал', 'icon' => '↗', 'icebox' => 5, 'space_id' => 3],
        ['name' => 'Бассейн — ЦАС «Удмуртия»', 'type' => 'Бассейн', 'icon' => '≈', 'icebox' => 6, 'space_id' => 5],
        ['name' => 'Тренажёрный зал — ЦАС «Удмуртия»', 'type' => 'Тренажёрный зал', 'icon' => '＋', 'icebox' => 7, 'space_id' => 6],
        ['name' => 'Хореографический зал — ЦАС «Удмуртия»', 'type' => 'Зал', 'icon' => '⌁', 'icebox' => 9, 'space_id' => 7],
        ['name' => 'Зал хореографии — ЛА «Можга»', 'type' => 'Зал', 'icon' => '⌁', 'icebox' => 10, 'space_id' => 9],
        ['name' => 'Зал единоборств — ЛА «Можга»', 'type' => 'Зал', 'icon' => '✦', 'icebox' => 11, 'space_id' => 10],
        ['name' => 'Спортивный зал — ЛА «Можга»', 'type' => 'Зал', 'icon' => '↗', 'icebox' => 12, 'space_id' => 11],
    ];
@endphp

<!-- ВЕРХНИЙ БЛОК (HERO) -->
<section class="hero-section">
    <div class="hero-backdrop"></div>
    <div class="container hero-grid">
        <div class="hero-copy">
            <div class="eyebrow"><span class="eyebrow-line"></span> АУ УР «Ледовый дворец Ижсталь»</div>
            <h1>Территория<br>большого <em>спорта.</em></h1>

            <div class="hero-institution">
                <span class="hero-institution-kicker">Автономное учреждение Удмуртской республики</span>
                <p class="hero-institution-intro">
                    Ледовый дворец <strong>«Ижсталь»</strong> объединяет несколько спортивных объектов:
                </p>
                <ul class="hero-facility-list">
                    <li>
                        <div class="hero-list-text">
                            <span>Ледовый дворец «Ижсталь»</span>
                            <small>Находится на реконструкции</small>
                        </div>
                    </li>
                    <li>
                        <div class="hero-list-text"><span>Крытый каток «Олимпиец»</span></div>
                    </li>
                    <li>
                        <div class="hero-list-text"><span>Центр адаптивного спорта «Удмуртия»</span></div>
                    </li>
                    <li>
                        <div class="hero-list-text"><span>Ледовая арена «Можга»</span></div>
                    </li>
                </ul>
            </div>
        </div>
        <div class="hero-visual">
            <div class="hero-photo-frame"><img src="{{ asset('img/photo2.jpg') }}" alt="Хоккеист на ледовой арене с юным болельщиком" fetchpriority="high"></div>
            <div class="hero-photo-caption"><span></span><span>Спорт объединяет поколения</span></div>
            <div class="hero-stamp"><span>ICE</span><b>•</b><span>SPORT</span></div>
        </div>
    </div>
    <div class="hero-bottom container"><span></span><span class="scroll-cue">Листайте вниз <b>↓</b></span></div>
</section>

<!-- РАСПИСАНИЕ -->
<section class="section schedule-section" id="schedule">
    <div class="container">
        <div class="section-heading schedule-heading">
            <div><div class="eyebrow"><span class="eyebrow-line"></span> ПЛАНИРУЙТЕ СВОЁ ВРЕМЯ</div><h2>Расписание <em>аренды</em></h2></div>
            <p>Актуальные планы загрузки ледовых арен и спортивных помещений. Выберите объект и нужную неделю.</p>
        </div>
        
        <div class="schedule-panel-new">
            <div class="schedule-panel-top-new">
                <div>
                    <div class="panel-kicker-new">ПЛАН ЗАГРУЗКИ</div>
                    <h3 class="schedule-panel-title">Выберите расписание</h3>
                </div>
                <div class="week-switch-new" role="group" aria-label="Неделя расписания">
                    <button class="week-button-new is-active" type="button" data-week="0">2–8 ноября</button>
                    <button class="week-button-new" type="button" data-week="1">9–15 ноября</button>
                </div>
            </div>
            
            <div class="schedule-list-new">
                @foreach($scheduleGroups as $index => $group)
                    @php
                        $hrefFirst = route('schedule.public', ['space_id' => $group['space_id'], 'week' => '2026-11-02']);
                        $hrefSecond = route('schedule.public', ['space_id' => $group['space_id'], 'week' => '2026-11-09']);
                    @endphp
                    <a class="schedule-row-new"
                       href="{{ $hrefFirst }}"
                       data-href-first="{{ $hrefFirst }}"
                       data-href-second="{{ $hrefSecond }}">
                        
                        <div class="schedule-info-new">
                            <span class="schedule-category-new">{{ mb_strtoupper($group['type']) }} · ПЛАН ЗАГРУЗКИ</span>
                            <span class="schedule-name-new">{{ $group['name'] }}</span>
                            <span class="schedule-date-new schedule-date" data-date-first="02.11.2026 — 08.11.2026" data-date-second="09.11.2026 — 15.11.2026">02.11.2026 — 08.11.2026</span>
                        </div>
                        
                        <span class="schedule-arrow-new">↗</span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</section>

<!-- ЦИТАТА -->
<section class="quote-band">
    <div class="container quote-band-inner"><span class="quote-mark">“</span><div><p>Большие победы начинаются<br>с первого шага на лёд.</p><span>МЕСТО ДЛЯ СПОРТА. ПРОСТРАНСТВО ДЛЯ ЛЮДЕЙ.</span></div><span class="quote-spark">✳</span></div>
</section>

<!-- ГАЛЕРЕЯ С 5 ФОТО -->
<section class="section gallery-section" id="gallery">
    <div class="container">
        <div class="section-heading gallery-heading">
            <div><div class="eyebrow"><span class="eyebrow-line"></span> МОМЕНТЫ, КОТОРЫЕ ВДОХНОВЛЯЮТ</div><h2>Жизнь <em>в движении</em></h2></div>
            <p>Эмоции соревнований, радость встреч и атмосфера спортивных побед.</p>
        </div>
        
        <div class="gallery-collage">
            <a class="collage-photo collage-large" href="{{ asset('img/photo1.jpg') }}">
                <img src="{{ asset('img/photo1.jpg') }}" alt="Хоккей объединяет" loading="lazy">
                <span>Хоккей объединяет <b>↗</b></span>
            </a>
            <a class="collage-photo" href="{{ asset('img/photo2.jpg') }}">
                <img src="{{ asset('img/photo2.jpg') }}" alt="Спорт для каждого" loading="lazy">
                <span>Спорт для каждого <b>↗</b></span>
            </a>
            <a class="collage-photo" href="{{ asset('img/photo3.jpg') }}">
                <img src="{{ asset('img/photo3.jpg') }}" alt="Новые дисциплины" loading="lazy">
                <span>Новые дисциплины <b>↗</b></span>
            </a>
            <a class="collage-photo" href="{{ asset('img/photo4.jpg') }}">
                <img src="{{ asset('img/photo4.jpg') }}" alt="Сила команды" loading="lazy">
                <span>Сила команды <b>↗</b></span>
            </a>
            <a class="collage-photo" href="{{ asset('img/photo5.jpg') }}">
                <img src="{{ asset('img/photo5.jpg') }}" alt="Лед для каждого" loading="lazy">
                <span>Лед для каждого <b>↗</b></span>
            </a>
        </div>

        <div class="gallery-more-link">
            <a href="{{ url('/gallery') }}" class="button button-quiet">Смотреть все фото <span>↗</span></a>
        </div>
    </div>
</section>

<!-- ВИДЖЕТ ГОСУСЛУГ -->
<section class="feedback-section feedback-section-widget" id="documents">
    <div class="container feedback-inner feedback-inner-widget">
        <div class="banner-image">
                    <script src="https://pos.gosuslugi.ru/bin/script.min.js"></script> 
                    <style>
                        #js-show-iframe-wrapper{position:relative;display:flex;align-items:center;justify-content:center;width:100%;min-width:293px;max-width:100%;background:linear-gradient(138.4deg,#38bafe 26.49%,#2d73bc 79.45%);color:#fff;cursor:pointer}#js-show-iframe-wrapper .pos-banner-fluid *{box-sizing:border-box}#js-show-iframe-wrapper .pos-banner-fluid .pos-banner-btn_2{display:block;width:240px;min-height:56px;font-size:18px;line-height:24px;cursor:pointer;background:#0d4cd3;color:#fff;border:none;border-radius:8px;outline:0}#js-show-iframe-wrapper .pos-banner-fluid .pos-banner-btn_2:hover{background:#1d5deb}#js-show-iframe-wrapper .pos-banner-fluid .pos-banner-btn_2:focus{background:#2a63ad}#js-show-iframe-wrapper .pos-banner-fluid .pos-banner-btn_2:active{background:#2a63ad}@-webkit-keyframes fadeInFromNone{0%{display:none;opacity:0}1%{display:block;opacity:0}100%{display:block;opacity:1}}@keyframes fadeInFromNone{0%{display:none;opacity:0}1%{display:block;opacity:0}100%{display:block;opacity:1}}@font-face{font-family:LatoWebLight;src:url(https://pos.gosuslugi.ru/bin/fonts/Lato/fonts/Lato-Light.woff2) format("woff2"),url(https://pos.gosuslugi.ru/bin/fonts/Lato/fonts/Lato-Light.woff) format("woff"),url(https://pos.gosuslugi.ru/bin/fonts/Lato/fonts/Lato-Light.ttf) format("truetype");font-style:normal;font-weight:400}@font-face{font-family:LatoWeb;src:url(https://pos.gosuslugi.ru/bin/fonts/Lato/fonts/Lato-Regular.woff2) format("woff2"),url(https://pos.gosuslugi.ru/bin/fonts/Lato/fonts/Lato-Regular.woff) format("woff"),url(https://pos.gosuslugi.ru/bin/fonts/Lato/fonts/Lato-Regular.ttf) format("truetype");font-style:normal;font-weight:400}@font-face{font-family:LatoWebBold;src:url(https://pos.gosuslugi.ru/bin/fonts/Lato/fonts/Lato-Bold.woff2) format("woff2"),url(https://pos.gosuslugi.ru/bin/fonts/Lato/fonts/Lato-Bold.woff) format("woff"),url(https://pos.gosuslugi.ru/bin/fonts/Lato/fonts/Lato-Bold.ttf) format("truetype");font-style:normal;font-weight:400}@font-face{font-family:RobotoWebLight;src:url(https://pos.gosuslugi.ru/bin/fonts/Roboto/Roboto-Light.woff2) format("woff2"),url(https://pos.gosuslugi.ru/bin/fonts/Roboto/Roboto-Light.woff) format("woff"),url(https://pos.gosuslugi.ru/bin/fonts/Roboto/Roboto-Light.ttf) format("truetype");font-style:normal;font-weight:400}@font-face{font-family:RobotoWebRegular;src:url(https://pos.gosuslugi.ru/bin/fonts/Roboto/Roboto-Regular.woff2) format("woff2"),url(https://pos.gosuslugi.ru/bin/fonts/Roboto/Roboto-Regular.woff) format("woff"),url(https://pos.gosuslugi.ru/bin/fonts/Roboto/Roboto-Regular.ttf) format("truetype");font-style:normal;font-weight:400}@font-face{font-family:RobotoWebBold;src:url(https://pos.gosuslugi.ru/bin/fonts/Roboto/Roboto-Bold.woff2) format("woff2"),url(https://pos.gosuslugi.ru/bin/fonts/Roboto/Roboto-Bold.woff) format("woff"),url(https://pos.gosuslugi.ru/bin/fonts/Roboto/Roboto-Bold.ttf) format("truetype");font-style:normal;font-weight:400}@font-face{font-family:ScadaWebRegular;src:url(https://pos.gosuslugi.ru/bin/fonts/Scada/Scada-Regular.woff2) format("woff2"),url(https://pos.gosuslugi.ru/bin/fonts/Scada/Scada-Regular.woff) format("woff"),url(https://pos.gosuslugi.ru/bin/fonts/Scada/Scada-Regular.ttf) format("truetype");font-style:normal;font-weight:400}@font-face{font-family:ScadaWebBold;src:url(https://pos.gosuslugi.ru/bin/fonts/Scada/Scada-Bold.woff2) format("woff2"),url(https://pos.gosuslugi.ru/bin/fonts/Scada/Scada-Bold.woff) format("woff"),url(https://pos.gosuslugi.ru/bin/fonts/Scada/Scada-Bold.ttf) format("truetype");font-style:normal;font-weight:400}@font-face{font-family:Geometria;src:url(https://pos.gosuslugi.ru/bin/fonts/Geometria/Geometria.eot);src:url(https://pos.gosuslugi.ru/bin/fonts/Geometria/Geometria.eot?#iefix) format("embedded-opentype"),url(https://pos.gosuslugi.ru/bin/fonts/Geometria/Geometria.woff) format("woff"),url(https://pos.gosuslugi.ru/bin/fonts/Geometria/Geometria.ttf) format("truetype");font-weight:400;font-style:normal}@font-face{font-family:Geometria-ExtraBold;src:url(https://pos.gosuslugi.ru/bin/fonts/Geometria/Geometria-ExtraBold.eot);src:url(https://pos.gosuslugi.ru/bin/fonts/Geometria/Geometria-ExtraBold.eot?#iefix) format("embedded-opentype"),url(https://pos.gosuslugi.ru/bin/fonts/Geometria/Geometria-ExtraBold.woff) format("woff"),url(https://pos.gosuslugi.ru/bin/fonts/Geometria/Geometria-ExtraBold.ttf) format("truetype");font-weight:900;font-style:normal}
                    </style>
                    <style>
                        #js-show-iframe-wrapper{background:var(--pos-banner-fluid-22__background)}#js-show-iframe-wrapper .pos-banner-fluid .pos-banner-btn_2{width:100%;min-height:52px;background:#fff;color:#0b1f33;font-size:16px;font-family:LatoWeb,sans-serif;font-weight:400;padding:0;line-height:1.2}#js-show-iframe-wrapper .pos-banner-fluid .pos-banner-btn_2:active,#js-show-iframe-wrapper .pos-banner-fluid .pos-banner-btn_2:focus,#js-show-iframe-wrapper .pos-banner-fluid .pos-banner-btn_2:hover{background:#e4ecfd}#js-show-iframe-wrapper .bf-22{position:relative;display:grid;grid-template-columns:var(--pos-banner-fluid-22__grid-template-columns);grid-template-rows:var(--pos-banner-fluid-22__grid-template-rows);width:100%;max-width:var(--pos-banner-fluid-22__max-width);box-sizing:border-box;grid-auto-flow:row dense}#js-show-iframe-wrapper .bf-22__decor{background:var(--pos-banner-fluid-22__bg-url) var(--pos-banner-fluid-22__bg-url-position) no-repeat;background-size:cover;background-color:#f8efec;position:relative}#js-show-iframe-wrapper .bf-22__content{display:flex;flex-direction:column;padding:var(--pos-banner-fluid-22__content-padding);grid-row:var(--pos-banner-fluid-22__content-grid-row);justify-content:center}#js-show-iframe-wrapper .bf-22__text{margin:var(--pos-banner-fluid-22__text-margin);font-size:var(--pos-banner-fluid-22__text-font-size);line-height:1.4;font-family:LatoWeb,sans-serif;font-weight:700;color:#fff}#js-show-iframe-wrapper .bf-22__bottom-wrap{display:flex;flex-direction:row;align-items:center}#js-show-iframe-wrapper .bf-22__logo-wrap{position:absolute;top:var(--pos-banner-fluid-22__logo-wrap-top);left:var(--pos-banner-fluid-22__logo-wrap-right);padding:var(--pos-banner-fluid-22__logo-wrap-padding);background:#fff;border-radius:0 0 8px 0}#js-show-iframe-wrapper .bf-22__logo{width:var(--pos-banner-fluid-22__logo-width);margin-left:1px}#js-show-iframe-wrapper .bf-22__slogan{font-family:LatoWeb,sans-serif;font-weight:700;font-size:var(--pos-banner-fluid-22__slogan-font-size);line-height:1.2;color:#005ca9}#js-show-iframe-wrapper .bf-22__btn-wrap{width:100%;max-width:var(--pos-banner-fluid-22__button-wrap-max-width)}
                    </style>
                    <div id="js-show-iframe-wrapper">
                      <div class="pos-banner-fluid bf-22">
                        <div class="bf-22__decor">
                          <div class="bf-22__logo-wrap">
                            <img class="bf-22__logo" src="https://pos.gosuslugi.ru/bin/banner-fluid/gosuslugi-logo-blue.svg" alt="Госуслуги">
                            <div class="bf-22__slogan">Решаем вместе</div>
                          </div>
                        </div>
                        <div class="bf-22__content">
                          <div class="bf-22__text">
                            Есть предложения по организации учебного процесса или знаете, как сделать школу лучше?
                          </div>
                          <div class="bf-22__bottom-wrap">
                            <div class="bf-22__btn-wrap">
                              <button class="pos-banner-btn_2" type="button">Написать о проблеме</button>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <script>
                    (function(){
                      "use strict";function ownKeys(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var o=Object.getOwnPropertySymbols(e);if(t)o=o.filter(function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable});n.push.apply(n,o)}return n}function _objectSpread(e){for(var t=1;t<arguments.length;t++){var n=null!=arguments[t]?arguments[t]:{};if(t%2)ownKeys(Object(n),true).forEach(function(t){_defineProperty(e,t,n[t])});else if(Object.getOwnPropertyDescriptors)Object.defineProperties(e,Object.getOwnPropertyDescriptors(n));else ownKeys(Object(n)).forEach(function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(n,t))})}return e}function _defineProperty(e,t,n){if(t in e)Object.defineProperty(e,t,{value:n,enumerable:true,configurable:true,writable:true});else e[t]=n;return e}var POS_PREFIX_22="--pos-banner-fluid-22__",posOptionsInitialBanner22={background:"linear-gradient(#2d73bc 26.49%,#38bafe 79.45%)","grid-template-columns":"100%","grid-template-rows":"262px auto","max-width":"100%","text-font-size":"20px","text-margin":"0 0 24px 0","button-wrap-max-width":"100%","bg-url":"url('https://pos.gosuslugi.ru/bin/banner-fluid/18/banner-fluid-bg-18-2.svg')","bg-url-position":"right bottom","content-padding":"26px 24px 24px","content-grid-row":"0","logo-wrap-padding":"16px 12px 12px","logo-width":"65px","logo-wrap-top":"0","logo-wrap-left":"0","slogan-font-size":"12px"},setStyles=function(e,t){var n=arguments.length>2&&void 0!==arguments[2]?arguments[2]:POS_PREFIX_22;Object.keys(e).forEach(function(o){t.style.setProperty(n+o,e[o])})},removeStyles=function(e,t){var n=arguments.length>2&&void 0!==arguments[2]?arguments[2]:POS_PREFIX_22;Object.keys(e).forEach(function(e){t.style.removeProperty(n+e)})};function changePosBannerOnResize(){var e=document.documentElement,t=_objectSpread({},posOptionsInitialBanner22),n=document.getElementById("js-show-iframe-wrapper"),o=n?n.offsetWidth:document.body.offsetWidth;if(o>340)t["button-wrap-max-width"]="209px";if(o>482)t["content-padding"]="24px",t["text-font-size"]="24px";if(o>568)t["grid-template-columns"]="1fr 292px",t["grid-template-rows"]="100%",t["content-grid-row"]="1",t["content-padding"]="32px 24px",t["bg-url-position"]="calc(100% + 35px) bottom";if(o>610)t["bg-url-position"]="calc(100% + 12px) bottom";if(o>726)t["bg-url-position"]="right bottom";if(o>783)t["grid-template-columns"]="1fr 390px";if(o>820)t["grid-template-columns"]="1fr 420px",t["bg-url-position"]="right bottom";if(o>1098)t["bg-url"]="url('https://pos.gosuslugi.ru/bin/banner-fluid/18/banner-fluid-bg-18-3.svg')",t["bg-url-position"]="calc(100% + 55px) bottom",t["grid-template-columns"]="1fr 557px",t["text-font-size"]="32px",t["content-padding"]="32px 32px 32px 50px",t["logo-width"]="78px",t["slogan-font-size"]="15px",t["logo-wrap-padding"]="20px 16px 16px";if(o>1422)t["max-width"]="1422px",t["grid-template-columns"]="1fr 720px",t["content-padding"]="32px 48px 32px 160px",t.background="linear-gradient(90deg, #2d73bc 5.49%,#38bafe 59.45%, #f8efec 60%)";setStyles(t,e)}changePosBannerOnResize(),window.addEventListener("resize",changePosBannerOnResize),window.onunload=function(){var e=document.documentElement,t=_objectSpread({},posOptionsInitialBanner22);window.removeEventListener("resize",changePosBannerOnResize),removeStyles(t,e)};
                    })()
                    </script>
                    <script>Widget("https://pos.gosuslugi.ru/form", 376293)</script>
                </div>
    </div>
</section>

<!-- БЛОК СВЯЗИ -->
<section class="contact-cta">
    <div class="container contact-cta-inner">
        <div>
            <div class="eyebrow"><span class="eyebrow-line"></span> ЕСТЬ ВОПРОСЫ?</div>
            <h2>Будем рады <em>помочь.</em></h2>
        </div>
        <a href="tel:+73412572051" class="contact-cta-link">
            <span>Позвонить в приёмную</span>
            <b>+7 (3412) 57-20-51</b>
            <i>↗</i>
        </a>
    </div>
</section>
@endsection