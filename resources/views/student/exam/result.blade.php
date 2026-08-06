@extends('layouts.student')
@section('title', 'Hasil Ujian — TOEFL')

@section('styles')
<style>
    .result-page {
        padding: 40px 0;
    }

    .result-header {
        text-align: center;
        margin-bottom: 40px;
    }

    .result-header h1 {
        font-size: 2rem;
        font-weight: 800;
        margin-bottom: 8px;
    }

    .result-header p {
        color: var(--text-muted);
        font-size: 1rem;
    }

    .result-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: var(--success-bg);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        margin: 0 auto 20px;
    }

    .total-score-card {
        text-align: center;
        padding: 40px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
        color: #fff;
        border-radius: var(--radius-lg);
        margin-bottom: 32px;
    }

    .total-score-card .score-big {
        font-size: 4rem;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 8px;
    }

    .total-score-card .score-label {
        font-size: 1rem;
        opacity: 0.9;
    }

    .scores-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 32px;
    }

    .score-card {
        text-align: center;
        padding: 24px;
    }

    .score-card .section-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: rgba(37, 99, 235, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
        font-size: 1.5rem;
    }

    .score-card h3 {
        font-size: 0.875rem;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 8px;
    }

    .score-card .score-val {
        font-size: 2rem;
        font-weight: 800;
        color: var(--primary);
    }

    .score-card .score-detail {
        font-size: 0.8125rem;
        color: var(--text-muted);
        margin-top: 8px;
    }

    .progress-bar {
        height: 8px;
        background: var(--border);
        border-radius: 999px;
        overflow: hidden;
        margin-top: 12px;
    }

    .progress-fill {
        height: 100%;
        border-radius: 999px;
        background: var(--primary);
        transition: width 1s ease;
    }

    .result-actions {
        display: flex;
        justify-content: center;
        gap: 12px;
        margin-top: 32px;
    }

    .meta-info {
        display: flex;
        justify-content: center;
        gap: 32px;
        margin-bottom: 32px;
        color: var(--text-muted);
        font-size: 0.875rem;
    }

    .meta-info span {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    @media (max-width: 640px) {
        .scores-grid { grid-template-columns: 1fr; }
        .total-score-card .score-big { font-size: 3rem; }
    }
</style>
@endsection

@section('content')
<div class="container result-page">
    <div class="result-header">
        <div class="result-icon" aria-hidden="true">🎓</div>
        <h1>Hasil Ujian TOEFL</h1>
        <p>{{ $examAttempt->testSession->title ?? 'Test' }}</p>
    </div>

    <div class="meta-info" aria-label="Informasi ujian">
        <span>📅 {{ $examAttempt->started_at?->format('d M Y') }}</span>
        <span>⏱️ {{ $examAttempt->started_at?->diffForHumans($examAttempt->completed_at, true) }}</span>
        <span>✅ Selesai: {{ $examAttempt->completed_at?->format('H:i') }}</span>
    </div>

    <div class="total-score-card" role="region" aria-label="Total skor TOEFL">
        <div class="score-big" aria-label="Total skor: {{ $examAttempt->total_score }}">{{ $examAttempt->total_score }}</div>
        <div class="score-label">Total TOEFL Score (max 677)</div>
    </div>

    <div class="scores-grid" role="list" aria-label="Skor per section">
        @php
            $icons = ['listening' => '🎧', 'structure' => '📝', 'reading' => '📖'];
        @endphp
        @foreach($sectionResults as $slug => $result)
        <div class="card score-card" role="listitem" aria-label="{{ $result['name'] }}: skor {{ $examAttempt->{'score_' . $slug} ?? 0 }}">
            <div class="section-icon" aria-hidden="true">{{ $icons[$slug] ?? '📋' }}</div>
            <h3>{{ $result['name'] }}</h3>
            <div class="score-val">{{ $examAttempt->{'score_' . $slug} ?? 0 }}</div>
            <div class="score-detail">
                {{ $result['correct'] }} benar dari {{ $result['total'] }} soal
                ({{ $result['total'] > 0 ? round(($result['correct'] / $result['total']) * 100) : 0 }}%)
            </div>
            <div class="progress-bar" role="progressbar"
                 aria-valuenow="{{ $result['correct'] }}" aria-valuemin="0" aria-valuemax="{{ $result['total'] }}"
                 aria-label="Progress: {{ $result['correct'] }} dari {{ $result['total'] }}">
                <div class="progress-fill" style="width: {{ $result['total'] > 0 ? ($result['correct'] / $result['total']) * 100 : 0 }}%"></div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="result-actions">
        <a href="{{ route('student.dashboard') }}" class="btn btn-primary btn-lg" aria-label="Kembali ke dashboard">
            ← Kembali ke Dashboard
        </a>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Animate progress bars
        setTimeout(() => {
            document.querySelectorAll('.progress-fill').forEach(bar => {
                bar.style.width = bar.style.width;
            });
        }, 100);

        // Announce result
        speak('Your TOEFL score is {{ $examAttempt->total_score }}. Press H for keyboard shortcuts.');
    });
</script>
@endsection
