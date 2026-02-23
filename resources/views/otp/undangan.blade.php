@extends('layouts.app')

@section('title', 'Undangan Eksklusif')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Pinyon+Script&family=Playfair+Display:ital,wght@0,700;1,400&family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
<style>
    .font-pinyon { font-family: 'Pinyon Script', cursive; }
    .font-playfair { font-family: 'Playfair Display', serif; }
    .sakura {
        position: fixed; top: -10%; background: #ffd1dc;
        border-radius: 100% 0 100% 0; animation: fall linear infinite;
        z-index: 1; pointer-events: none;
    }
    @keyframes fall {
        0% { top: -10%; transform: translateX(0) rotate(0deg); opacity: 1; }
        100% { top: 100%; transform: translateX(100px) rotate(360deg); opacity: 0; }
    }
    .invitation-card {
        background: white;
        border-radius: 40px;
        border: 1px solid #fce4ec;
        position: relative;
        overflow: hidden;
    }
</style>

<div id="sakura-container"></div>

<div class="row justify-content-center">
    <div class="col-md-8 grid-margin stretch-card">
        <div class="card invitation-card shadow-lg">
            <div class="card-body p-5 text-center">
                <div class="d-flex justify-content-end mb-4">
                    <div class="rounded-circle bg-light border d-flex align-items-center justify-content-center shadow-sm" style="width: 70px; height: 70px; border: 4px solid white !important;">
                        <span class="font-pinyon text-primary h3 mb-0 font-weight-bold">NZ</span>
                    </div>
                </div>

                <h4 class="text-uppercase tracking-widest text-primary small font-weight-bold mb-4" style="letter-spacing: 0.5em;">Special Invitation</h4>
                
                <p class="font-playfair font-italic text-muted mb-2">Diberikan Kepada,</p>
                <h1 class="font-pinyon display-3 text-dark mb-4">{{ $namaTamu }}</h1>

                <div class="mx-auto bg-primary mb-4" style="width: 60px; height: 1px; opacity: 0.3;"></div>

                <h2 class="font-playfair h3 text-dark mb-3">Gala Dinner & Mentoring</h2>
                <p class="text-muted mb-5">
                    Sebagai bentuk apresiasi atas partisipasi Anda dalam <br>
                    <span class="font-weight-bold text-danger text-uppercase">"Seminar Bisnis Hijab 2026"</span>
                </p>

                <div class="row mb-5">
                    <div class="col-md-6 mb-3">
                        <div class="bg-light p-4 rounded-lg border text-left">
                            <p class="small text-primary font-weight-bold text-uppercase mb-2">Waktu & Tempat</p>
                            <div class="small text-dark">
                                <p class="mb-1">📅 Sabtu, 28 Feb 2026</p>
                                <p class="mb-1">⏰ 19.00 WIB - Selesai</p>
                                <p class="mb-0">📍 Grand Rose Ballroom</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="bg-light p-4 rounded-lg border">
                            <p class="small text-primary font-weight-bold text-uppercase mb-2">Countdown</p>
                            <div class="d-flex justify-content-around">
                                <div><span class="d-block h4 font-weight-bold mb-0">07</span><span class="small text-muted">Hari</span></div>
                                <div><span class="d-block h4 font-weight-bold mb-0">12</span><span class="small text-muted">Jam</span></div>
                                <div><span class="d-block h4 font-weight-bold mb-0">45</span><span class="small text-muted">Mnt</span></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-column flex-md-row align-items-center justify-content-between border-top pt-4">
                    <div class="d-flex align-items-center mb-3 mb-md-0">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=GrandRoseBallroom" class="img-thumbnail" style="width: 70px;">
                        <div class="text-left ml-3">
                            <p class="small font-weight-bold text-muted text-uppercase mb-0">Petunjuk Lokasi</p>
                            <p class="small text-muted mb-0">Scan Maps</p>
                        </div>
                    </div>
                    <div>
                        <a href="#" class="btn btn-gradient-danger btn-lg font-weight-bold mr-2">KONFIRMASI RSVP</a>
                    </div>
                </div>
            </div>
            <div class="p-1 bg-gradient-primary"></div>
        </div>
    </div>
</div>

<script>
    const container = document.getElementById('sakura-container');
    for (let i = 0; i < 15; i++) {
        const petal = document.createElement('div');
        petal.className = 'sakura';
        petal.style.left = Math.random() * 100 + 'vw';
        petal.style.width = Math.random() * 10 + 5 + 'px';
        petal.style.height = petal.style.width;
        petal.style.animationDuration = Math.random() * 5 + 5 + 's';
        petal.style.opacity = Math.random();
        container.appendChild(petal);
    }
</script>
@endsection