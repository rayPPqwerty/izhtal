@extends('layouts.app')

@section('title', 'Контакты — Ледовый дворец «Ижсталь»')

@section('content')
<section class="section contact-page-section">
    <div class="container">
        <div class="section-heading contact-heading">
            <div>
                <div class="eyebrow"><span class="eyebrow-line"></span> СВЯЗАТЬСЯ С НАМИ</div>
                <h2>Наши контакты</h2>
            </div>
            <p>Мы всегда рады ответить на ваши вопросы, помочь с выбором секции или забронировать лёд.</p>
        </div>

        <div class="contact-page-grid">
            <div class="contact-card">
                <div class="contact-card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                        <circle cx="12" cy="10" r="3"></circle>
                    </svg>
                </div>
                <h3>Как нас найти</h3>
                <p>426034, Удмуртская Республика,<br>г. Ижевск, ул. Удмуртская, 222А<br>(КК «Олимпиец»)</p>
                <a href="https://yandex.ru/maps/?text=Ижевск%20Удмуртская%20222А" target="_blank" rel="noopener" class="contact-action-link">Построить маршрут <span>↗</span></a>
            </div>

            <div class="contact-card">
                <div class="contact-card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                    </svg>
                </div>
                <h3>Телефоны</h3>
                <div class="contact-phones-list">
                    <div>
                        <span class="contact-label">Приёмная</span>
                        <a href="tel:+73412572051" class="contact-phone-link">+7 (3412) 57-20-51</a>
                    </div>
                    <div style="margin-top: 15px;">
                        <span class="contact-label">Администраторы</span>
                        <a href="tel:+73412572061" class="contact-phone-link">+7 (3412) 57-20-61</a>
                    </div>
                </div>
            </div>

            <div class="contact-card">
                <div class="contact-card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                        <polyline points="22,6 12,13 2,6"></polyline>
                    </svg>
                </div>
                <h3>Почта и соцсети</h3>
                <p>По всем вопросам пишите нам на официальную почту:</p>
                <a href="mailto:mail@izhstal.udmr.ru" class="contact-action-link" style="margin-bottom: 15px;">mail@izhstal.udmr.ru</a>
                <p>Следите за новостями в нашей группе:</p>
                <a href="https://vk.ru/public170007616" target="_blank" rel="noopener" class="contact-action-link">ВКонтакте <span>↗</span></a>
            </div>
        </div>

        <div class="contact-map-wrapper">
            <iframe src="https://yandex.ru/map-widget/v1/?text=Ижевск%20Удмуртская%20222%D0%90&z=16" width="100%" height="420" frameborder="0" allowfullscreen="true" style="border-radius: 24px; border: 1px solid #e5f0f5; box-shadow: 0 10px 40px rgba(52,108,135,.05);"></iframe>
        </div>
    </div>
</section>
@endsection