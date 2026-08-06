@extends('layouts.student')
@section('title', 'Dashboard Peserta')

@section('styles')
<style>
    .dashboard-header {
        margin-bottom: 32px;
    }
    .dashboard-header h1 {
        font-size: 1.75rem;
        font-weight: 800;
        margin-bottom: 4px;
    }
    .dashboard-header p {
        color: var(--text-muted);
    }
    .welcome-card {
        background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
        color: #fff;
        padding: 32px;
        border-radius: var(--radius-lg);
        margin-bottom: 32px;
    }
    .welcome-card h2 {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 8px;
    }
    .welcome-card p {
        opacity: 0.9;
        font-size: 0.9375rem;
    }
    .section-title {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .test-card {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    .test-card .test-info {
        flex: 1;
    }
    .test-card h3 {
        font-size: 1.0625rem;
        font-weight: 700;
        margin-bottom: 6px;
    }
    .test-card p {
        color: var(--text-muted);
        font-size: 0.875rem;
        margin-bottom: 12px;
    }
    .test-sections {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-bottom: 16px;
    }
    .score-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
        margin-bottom: 12px;
    }
    .score-item {
        text-align: center;
        padding: 12px 8px;
        background: var(--bg);
        border-radius: var(--radius);
    }
    .score-item .score-value {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--primary);
    }
    .score-item .score-label {
        font-size: 0.6875rem;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .empty-state {
        text-align: center;
        padding: 48px 24px;
        color: var(--text-muted);
    }
    .empty-state svg {
        width: 48px;
        height: 48px;
        margin-bottom: 16px;
        opacity: 0.4;
    }
    .resume-bar {
        background: var(--warning-bg);
        border: 1px solid var(--warning);
        border-radius: var(--radius);
        padding: 16px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 24px;
        gap: 16px;
        flex-wrap: wrap;
    }
    @media (max-width: 640px) {
        .score-grid { grid-template-columns: repeat(2, 1fr); }
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="welcome-card" role="banner">
        <h2>👋 Selamat datang, {{ auth()->user()->name }}!</h2>
        <p>Platform ujian TOEFL yang dirancang khusus untuk aksesibilitas. Tekan <kbd style="background:rgba(255,255,255,0.2);color:#fff;border-color:rgba(255,255,255,0.3)">H</kbd> untuk melihat semua keyboard shortcuts.</p>
        
        <div style="margin-top: 16px;">
            <button class="btn" style="background: #fff; color: var(--primary);" onclick="testAudio()" aria-label="Tes Perangkat Audio. Shortcut: T">
                🔊 Tes Perangkat Audio <kbd style="color:var(--primary);border-color:var(--primary)">T</kbd>
            </button>
        </div>
    </div>

    @if($inProgressAttempt)
    <div class="resume-bar" role="alert">
        <div>
            <strong>⏳ Ujian sedang berlangsung:</strong>
            {{ $inProgressAttempt->testSession->title ?? 'Test' }}
        </div>
        <a href="{{ route('exam.take', $inProgressAttempt) }}" class="btn btn-primary btn-sm">
            Lanjutkan Ujian →
        </a>
    </div>
    @endif

    <!-- Available Tests -->
    <div class="section-title">
        <span aria-hidden="true">📝</span>
        <span>Ujian Tersedia</span>
    </div>

    @if($activeTests->count() > 0)
    <div class="grid grid-2" style="margin-bottom: 40px;">
        @foreach($activeTests as $test)
        <div class="card test-card" role="article" aria-label="Ujian: {{ $test->title }}">
            <div class="test-info">
                <h3>{{ $test->title }}</h3>
                <p>{{ $test->description }}</p>
                <div class="test-sections" aria-label="Sections dalam ujian ini">
                    @foreach($test->sections as $section)
                        <span class="badge badge-primary">{{ $section->name }} ({{ $section->duration_minutes }} min)</span>
                    @endforeach
                </div>
            </div>
            <form method="POST" action="{{ route('exam.start', $test) }}">
                @csrf
                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;"
                        aria-label="Mulai ujian {{ $test->title }}. Shortcut: M">
                    🚀 Mulai Ujian <kbd>M</kbd>
                </button>
            </form>
        </div>
        @endforeach
    </div>
    @else
    <div class="card empty-state" style="margin-bottom: 40px;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
            <path d="M9 12h6m-3-3v6m-7 4h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
        </svg>
        <p>Belum ada ujian yang tersedia saat ini.</p>
    </div>
    @endif

    <!-- Completed Tests -->
    <div class="section-title">
        <span aria-hidden="true">📊</span>
        <span>Riwayat Ujian</span>
    </div>

    @if($completedAttempts->count() > 0)
    <div class="grid grid-2">
        @foreach($completedAttempts as $attempt)
        <div class="card" role="article" aria-label="Hasil ujian {{ $attempt->testSession->title }}">
            <div class="card-header">
                <span class="card-title">{{ $attempt->testSession->title ?? 'Test' }}</span>
                <span class="badge badge-success">Selesai</span>
            </div>
            <div class="score-grid" aria-label="Skor per section">
                <div class="score-item">
                    <div class="score-value">{{ $attempt->score_listening ?? '-' }}</div>
                    <div class="score-label">Listening</div>
                </div>
                <div class="score-item">
                    <div class="score-value">{{ $attempt->score_structure ?? '-' }}</div>
                    <div class="score-label">Structure</div>
                </div>
                <div class="score-item">
                    <div class="score-value">{{ $attempt->score_reading ?? '-' }}</div>
                    <div class="score-label">Reading</div>
                </div>
                <div class="score-item">
                    <div class="score-value" style="color: var(--success);">{{ $attempt->total_score ?? '-' }}</div>
                    <div class="score-label">Total</div>
                </div>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:0.8125rem;color:var(--text-muted);">
                <span>{{ $attempt->completed_at?->format('d M Y, H:i') }}</span>
                <a href="{{ route('exam.result', $attempt) }}" class="btn btn-secondary btn-sm">Detail →</a>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="card empty-state">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
            <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
        </svg>
        <p>Belum ada ujian yang diselesaikan.</p>
    </div>
    @endif

    @if(session('login_success'))
    <!-- WELCOME SPLASH SCREEN MODAL -->
    <div id="welcomeSplashScreen" class="modal-overlay active" role="dialog" aria-modal="true" aria-label="Selamat datang di TUTUT" style="z-index: 99999;">
        <div class="modal" style="text-align: center; border: 2px solid var(--primary);">
            <img src="{{ asset('images/welcome.gif') }}" alt="Welcome Animation" style="max-width: 100%; width: 320px; border-radius: var(--radius); margin-bottom: 24px;">
            <h2 style="margin-bottom: 0;">Selamat Datang di TUTUT!</h2>
        </div>
    </div>
    @endif
</div>
@endsection

@section('styles')
@parent
<style>
    /* Custom styles removed - using Tailwind CSS now */
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        @if(session('login_success'))
            const splashScreen = document.getElementById('welcomeSplashScreen');
            const welcomeAudio = new Audio("{{ asset('audio/welcome.mp3') }}");

            function closeSplash() {
                if (splashScreen) {
                    splashScreen.classList.remove('active');
                    setTimeout(() => { splashScreen.remove(); }, 300);
                }
            }

            welcomeAudio.play().then(() => {
                console.log("Audio welcome berhasil diputar.");
            }).catch(err => {
                console.warn("Autoplay diblokir oleh browser, fallback ke TTS:", err);
                if (typeof speak === 'function') speak('Welcome to the TUTUT Website');
                if (typeof announce === 'function') announce('Welcome to the TUTUT Website');
            });

            welcomeAudio.addEventListener('ended', closeSplash);
            setTimeout(closeSplash, 5000);

            if (splashScreen) {
                splashScreen.addEventListener('click', closeSplash);
            }
            
            const handleSkip = (e) => {
                closeSplash();
                document.removeEventListener('keydown', handleSkip);
            };
            document.addEventListener('keydown', handleSkip);
        @endif
    });

    function testAudio() {
        const text = "Hallo, my name is TUTUT. TUTUT is designed to provide an accessible, independent, and comfortable testing experience. Please make sure your headset is connected and working properly before you begin. Good luck, and do your best.";
        speak(text);
        announce(text);
    }

    // Keyboard Shortcuts for Dashboard
    document.addEventListener('keydown', (e) => {
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;
        
        // Ignore modifier keys to prevent conflicting with browser shortcuts
        if (e.ctrlKey || e.altKey || e.metaKey) return;

        const key = e.key.toUpperCase();

        if (key === 'T') {
            e.preventDefault();
            testAudio();
        } else if (key === 'M') {
            e.preventDefault();
            const startBtn = document.querySelector('form[action*="exam/start"] button');
            if (startBtn) {
                announce('Memulai ujian');
                startBtn.click();
            }
        }
    });
</script>
@endsection
