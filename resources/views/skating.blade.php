@extends('layouts.app')

@section('title', 'Документы школы фигурного катания — Ледовый дворец «Ижсталь»')

@section('content')
<section class="documents-section">
    <div class="container">
        <div class="section-heading doc-heading">
            <div>
                <div class="eyebrow"><span class="eyebrow-line"></span> ШКОЛА ФИГУРНОГО КАТАНИЯ</div>
                <h2>Документы школы фигурного катания</h2>
            </div>
        </div>

        <div class="documents-grid">
            @php
                $docs = [
                    'Положение о школе фигурного катания',
                    'Правила приема и зачисления',
                    'Расписание занятий на 2026-2027 учебный год',
                    'Список тренерского состава',
                    'Договор об оказании образовательных услуг',
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