@extends('layouts.app')

@section('title', 'Фотогалерея — Ледовый дворец «Ижсталь»')

@section('content')
<section class="section gallery-page-section">
    <div class="container">
        <div class="section-heading gallery-heading">
            <div>
                <div class="eyebrow"><span class="eyebrow-line"></span> МОМЕНТЫ, КОТОРЫЕ ВДОХНОВЛЯЮТ</div>
                <h2>Жизнь <em>в движении</em></h2>
            </div>
            <p>Эмоции соревнований, радость встреч и атмосфера спортивных побед. Нажмите на фото, чтобы увеличить.</p>
        </div>
        
        <div class="gallery-page-grid">
            @for ($i = 1; $i <= 10 ; $i++)
                @php
                    $imgSrc = asset('img/photo' . $i . '.jpg');
                @endphp
                <a href="{{ $imgSrc }}" class="collage-photo gallery-page-item">
                    <img src="{{ $imgSrc }}" alt="Фотография {{ $i }}" loading="lazy">
                </a>
            @endfor
        </div>
    </div>
</section>
@endsection