@extends('layouts.app')

@section('title', 'Документы — Ледовый дворец «Ижсталь»')

@section('content')
<section class="documents-section">
    <div class="container">
        <div class="section-heading doc-heading">
            <div>
                <div class="eyebrow"><span class="eyebrow-line"></span> ОФИЦИАЛЬНАЯ ИНФОРМАЦИЯ</div>
                <h2>Документы</h2>
            </div>
        </div>

        <div class="documents-grid">
            @php
                $docs = [
                    'Положение о порядке предоставления платных услуг с 07.09.2026 г.',
                    'Перечень платных услуг с 07.09.2026 г.',
                    'Политика в отношении обработки персональных данных',
                    'Устав автономного учреждения Удмуртской Республики "Ледовый дворец "Ижсталь"',
                    'Изменения в Устав',
                    'Положение о Комиссии по против коррупции',
                    'Антикоррупционная политика',
                    'Кодекс этики и служебного поведения работников',
                    'Правила нахождения на спортивных объектах АУ УР ЛД Ижсталь',
                    'Правила поведения зрителей при проведении массовых мероприятий',
                    'Порядок бесплатного посещения участниками СВО и членами семьи'
                ];
            @endphp

            @foreach($docs as $doc)
                <a href="#" class="doc-card" target="_blank">
                    <div class="doc-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10 9 9 9 8 9"></polyline>
                        </svg>
                    </div>
                    <span>{{ $doc }}</span>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endsection