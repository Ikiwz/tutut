@extends('layouts.student')
@section('title', $currentSection->name . ' — Ujian TOEFL')

@section('styles')
<style>
    .exam-layout {
        display: grid;
        grid-template-columns: 1fr 300px;
        gap: 0;
        min-height: calc(100vh - 64px);
    }

    /* Exam Header Bar */
    .exam-topbar {
        grid-column: 1 / -1;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 24px;
        background: var(--bg-card);
        border-bottom: 1px solid var(--border);
        gap: 16px;
        flex-wrap: wrap;
    }

    .exam-topbar-section {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .section-name {
        font-weight: 700;
        font-size: 1rem;
    }

    .timer-display {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        background: var(--bg);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        font-weight: 700;
        font-size: 1rem;
        font-variant-numeric: tabular-nums;
    }

    .timer-display.warning {
        background: var(--warning-bg);
        border-color: var(--warning);
        color: var(--warning);
    }

    .timer-display.danger {
        background: var(--danger-bg);
        border-color: var(--danger);
        color: var(--danger);
        animation: pulse 1s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }

    .question-counter {
        font-size: 0.875rem;
        color: var(--text-muted);
        font-weight: 600;
    }

    /* Main content area */
    .exam-main {
        padding: 32px;
        overflow-y: auto;
        max-height: calc(100vh - 64px - 56px);
    }

    /* Passage area (for reading) */
    .passage-area {
        background: var(--bg);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 24px;
        margin-bottom: 24px;
        max-height: 300px;
        overflow-y: auto;
        line-height: 1.8;
    }

    .passage-area h3 {
        font-size: 1.125rem;
        font-weight: 700;
        margin-bottom: 16px;
        color: var(--primary);
    }

    .passage-area p {
        margin-bottom: 12px;
    }

    /* Question */
    .question-container {
        display: none; /* Hide all by default */
        margin-bottom: 32px;
        padding: 24px;
        border-radius: var(--radius-lg);
        border: 2px solid transparent;
        transition: all 0.3s ease;
    }

    .question-container.current-question {
        display: block; /* Show only the active one */
        border-color: var(--primary);
        background: rgba(37, 99, 235, 0.03);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .question-number {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        background: var(--primary);
        color: #fff;
        border-radius: 50%;
        font-weight: 700;
        font-size: 0.875rem;
        margin-right: 12px;
    }

    .question-text {
        font-size: 1.125rem;
        font-weight: 600;
        line-height: 1.6;
        margin-bottom: 20px;
        display: flex;
        align-items: flex-start;
    }

    .question-text span {
        flex: 1;
    }

    /* Options */
    .options-list {
        list-style: none;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .option-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 16px 20px;
        border: 2px solid var(--border);
        border-radius: var(--radius);
        cursor: pointer;
        transition: all 0.15s ease;
        background: var(--bg-card);
    }

    .option-item:hover {
        border-color: var(--primary-light);
        background: rgba(37, 99, 235, 0.03);
    }

    .option-item.selected {
        border-color: var(--primary);
        background: rgba(37, 99, 235, 0.08);
    }

    .option-item:focus-visible {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
    }

    .option-key {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: var(--bg);
        border: 1px solid var(--border);
        font-weight: 700;
        font-size: 0.875rem;
        color: var(--text-muted);
        flex-shrink: 0;
        transition: all 0.15s;
    }

    .option-item.selected .option-key {
        background: var(--primary);
        color: #fff;
        border-color: var(--primary);
    }

    .option-text {
        flex: 1;
        font-size: 1rem;
        line-height: 1.5;
    }

    /* Navigation */
    .exam-nav {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 32px;
        gap: 12px;
    }

    .exam-nav .nav-hint {
        font-size: 0.8125rem;
        color: var(--text-light);
    }

    /* Sidebar */
    .exam-sidebar {
        border-left: 1px solid var(--border);
        background: var(--bg-card);
        padding: 20px;
        overflow-y: auto;
        max-height: calc(100vh - 64px - 56px);
    }

    .sidebar-title {
        font-size: 0.8125rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-muted);
        margin-bottom: 12px;
    }

    .question-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 6px;
        margin-bottom: 24px;
    }

    .q-btn {
        width: 100%;
        aspect-ratio: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        border: 1px solid var(--border);
        background: var(--bg);
        cursor: pointer;
        font-weight: 600;
        font-size: 0.8125rem;
        color: var(--text-muted);
        transition: all 0.15s;
    }

    .q-btn.active {
        background: var(--primary);
        color: #fff;
        border-color: var(--primary);
    }

    .q-btn.answered {
        background: var(--success-bg);
        color: var(--success);
        border-color: var(--success);
    }

    .q-btn:hover {
        transform: scale(1.1);
    }

    .section-tabs {
        display: flex;
        flex-direction: column;
        gap: 6px;
        margin-bottom: 24px;
    }

    .section-tab {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 14px;
        border-radius: var(--radius);
        font-size: 0.8125rem;
        font-weight: 600;
        border: 1px solid var(--border);
        background: var(--bg);
        color: var(--text-muted);
        cursor: default;
    }

    .section-tab.active {
        background: var(--primary);
        color: #fff;
        border-color: var(--primary);
    }

    .section-tab.completed {
        background: var(--success-bg);
        color: var(--success);
        border-color: var(--success);
    }

    .sidebar-actions {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    /* ========== Listening Speaker Icon ========== */
    .listening-speaker-wrap {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 32px 0;
        gap: 16px;
    }

    .listening-speaker-btn {
        width: 96px;
        height: 96px;
        border-radius: 50%;
        border: 3px solid;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        outline: none;
    }

    .listening-speaker-btn svg {
        width: 44px;
        height: 44px;
        transition: all 0.3s ease;
    }

    /* State: idle (green) */
    .listening-speaker-btn.state-idle {
        background: #d1fae5;
        border-color: #10b981;
        color: #10b981;
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4);
    }

    .listening-speaker-btn.state-idle:hover {
        background: #a7f3d0;
        transform: scale(1.08);
        box-shadow: 0 0 20px rgba(16, 185, 129, 0.3);
    }

    /* State: playing (blue) */
    .listening-speaker-btn.state-playing {
        background: #dbeafe;
        border-color: #2563eb;
        color: #2563eb;
        animation: speaker-pulse 1.5s ease-in-out infinite;
        cursor: default;
    }

    @keyframes speaker-pulse {
        0%   { box-shadow: 0 0 0 0 rgba(37, 99, 235, 0.5); transform: scale(1); }
        50%  { box-shadow: 0 0 24px 8px rgba(37, 99, 235, 0.25); transform: scale(1.05); }
        100% { box-shadow: 0 0 0 0 rgba(37, 99, 235, 0.5); transform: scale(1); }
    }

    /* State: played (red) */
    .listening-speaker-btn.state-played {
        background: #fee2e2;
        border-color: #ef4444;
        color: #ef4444;
        cursor: not-allowed;
        opacity: 0.8;
    }

    .listening-speaker-label {
        font-size: 0.875rem;
        font-weight: 600;
        text-align: center;
        transition: color 0.3s ease;
    }

    .listening-speaker-label.state-idle { color: #10b981; }
    .listening-speaker-label.state-playing { color: #2563eb; }
    .listening-speaker-label.state-played { color: #ef4444; }

    /* ========== Directions Interstitial Block ========== */
    .directions-block {
        display: none;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 48px 24px;
        min-height: 320px;
    }

    .directions-block.active {
        display: flex;
    }

    .directions-part-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 18px;
        border-radius: 999px;
        font-size: 0.8125rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        background: linear-gradient(135deg, var(--primary), var(--accent));
        color: #fff;
        margin-bottom: 16px;
    }

    .directions-title {
        font-size: 1.375rem;
        font-weight: 800;
        margin-bottom: 6px;
        color: var(--text);
    }

    .directions-subtitle {
        font-size: 0.9375rem;
        color: var(--text-muted);
        margin-bottom: 28px;
        max-width: 640px;
        line-height: 1.6;
        text-align: left;
        background: var(--bg);
        padding: 20px;
        border-radius: var(--radius);
        border: 1px solid var(--border);
    }

    .directions-subtitle p {
        margin-bottom: 12px;
    }
    
    .directions-subtitle p:last-child {
        margin-bottom: 0;
    }

    .directions-subtitle strong, .directions-subtitle b {
        color: var(--text);
        font-weight: 700;
    }

    .directions-subtitle ul {
        list-style-type: disc;
        margin-left: 20px;
        margin-bottom: 12px;
    }

    .directions-subtitle ol {
        list-style-type: decimal;
        margin-left: 20px;
        margin-bottom: 12px;
    }

    .directions-continue-hint {
        font-size: 0.8125rem;
        color: var(--text-light);
        margin-top: 20px;
    }

    /* Sidebar Part Labels */
    .part-label-sidebar {
        grid-column: 1 / -1;
        font-size: 0.6875rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--primary);
        padding: 4px 0 2px;
        border-bottom: 1px solid var(--border);
    }

    @media (max-width: 900px) {
        .exam-layout {
            grid-template-columns: 1fr;
        }
        .exam-sidebar {
            border-left: none;
            border-top: 1px solid var(--border);
            max-height: none;
        }
        .listening-speaker-btn {
            width: 80px;
            height: 80px;
        }
        .listening-speaker-btn svg {
            width: 36px;
            height: 36px;
        }
        .directions-block {
            padding: 32px 16px;
            min-height: 240px;
        }
    }
</style>
@endsection

@section('content')
<div class="exam-layout">
    <!-- Top Bar -->
    <div class="exam-topbar" role="banner">
        <div class="exam-topbar-section">
            <span class="section-name" aria-label="Section saat ini">{{ $currentSection->name }}</span>
            <span id="part-indicator" style="{{ $directions->isEmpty() ? 'display:none' : '' }}; font-size:0.8125rem; font-weight:700; color:var(--accent);"></span>
            <span class="question-counter" id="question-counter" aria-live="polite">
                Soal <span id="current-num">1</span> dari {{ $questions->count() }}
            </span>
        </div>
        <div class="exam-topbar-section">
            <div class="timer-display" id="timer" role="timer" aria-label="Sisa waktu" aria-live="off">
                <span aria-hidden="true">⏱️</span>
                <span id="timer-text">{{ $currentSection->duration_minutes }}:00</span>
            </div>
            <button class="btn btn-secondary btn-sm" onclick="openShortcutsModal()" aria-label="Bantuan keyboard shortcut">
                <span aria-hidden="true">⌨️</span> Shortcuts <kbd>H</kbd>
            </button>
        </div>
    </div>

    <!-- Main Content -->
    <div class="exam-main" id="exam-main" role="region" aria-label="Area soal ujian">
        @if($passage)
        <div class="passage-area" id="passage-area" tabindex="0" role="article"
             aria-label="Bacaan: {{ $passage->title }}">
            <h3>{{ $passage->title }}</h3>
            @foreach(explode("\n", $passage->content) as $paragraph)
                @if(trim($paragraph))
                    <p>{{ trim($paragraph) }}</p>
                @endif
            @endforeach
        </div>
        @endif

        <div id="question-area" role="region" aria-label="Pertanyaan dan pilihan jawaban">

            {{-- ===== DIRECTIONS BLOCKS (Dynamic — all sections) ===== --}}
            @foreach($directions as $dirIndex => $direction)
            <div class="directions-block" id="directions-{{ $direction->id }}"
                 role="group" aria-label="Directions {{ $direction->label }}">
                <span class="directions-part-badge">📋 {{ $direction->label }}</span>
                <h2 class="directions-title">{{ $direction->title }}</h2>

                <div class="listening-speaker-wrap" style="padding: 16px 0;">
                    <button type="button"
                            class="listening-speaker-btn state-idle"
                            id="dir-speaker-{{ $direction->id }}"
                            data-audio-src="{{ $direction->audio_path ? asset('storage/' . $direction->audio_path) : '' }}"
                            onclick="{{ $direction->audio_path ? "playDirectionsAudio('{$direction->id}')" : "readDirectionsTTS('{$direction->id}', '{$direction->label}')" }}"
                            aria-label="Putar Directions {{ $direction->label }}. Tekan M untuk memutar ulang.">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon>
                            <path d="M15.54 8.46a5 5 0 0 1 0 7.07"></path>
                            <path d="M19.07 4.93a10 10 0 0 1 0 14.14"></path>
                        </svg>
                    </button>
                    <span class="listening-speaker-label state-idle" id="dir-label-{{ $direction->id }}">
                        Tekan <kbd>M</kbd> untuk memutar suara
                    </span>
                </div>

                @if($direction->description)
                <div class="directions-subtitle prose prose-sm max-w-none text-left">
                    {!! $direction->description !!}
                </div>
                @endif

                <p class="directions-continue-hint">Tekan <strong>Next</strong> <kbd>N</kbd> untuk melanjutkan</p>
            </div>
            @endforeach

            {{-- ===== QUESTION CONTAINERS ===== --}}
            @foreach($questions as $index => $question)
            <div class="question-container" id="question-{{ $index }}"
                 data-question-id="{{ $question->id }}"
                 data-direction-id="{{ $question->direction_id ?? '' }}"
                 
                 role="group"
                 aria-label="Soal nomor {{ $index + 1 }}">

                <div class="question-text">
                    <span class="question-number" aria-hidden="true">{{ $index + 1 }}</span>
                    <span id="q-text-{{ $index }}">{{ $question->question_text ?? ($currentSection->slug === 'listening' ? 'Listening Question' : '') }}</span>
                </div>
                <div class="listening-speaker-wrap">
                    <button type="button"
                            class="listening-speaker-btn state-idle"
                            id="speaker-btn-{{ $index }}"
                            data-audio-src="{{ $question->audio_path ? asset('storage/' . $question->audio_path) : '' }}"
                            data-index="{{ $index }}"
                            onclick="{{ $currentSection->slug === 'listening' ? "playListeningAudio({$index})" : "readSpecificQuestion({$index})" }}"
                            aria-label="{{ $currentSection->slug === 'listening' ? "Putar audio soal " . ($index + 1) . ". Audio hanya bisa diputar 1 kali." : "Putar suara pembaca soal " . ($index + 1) . ". Tekan M untuk memutar ulang." }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon>
                            <path d="M15.54 8.46a5 5 0 0 1 0 7.07"></path>
                            <path d="M19.07 4.93a10 10 0 0 1 0 14.14"></path>
                        </svg>
                    </button>
                    <span class="listening-speaker-label state-idle" id="speaker-label-{{ $index }}">
                        Tekan <kbd>M</kbd> untuk memutar suara
                    </span>
                </div>

                <ul class="options-list" role="radiogroup" aria-label="Pilihan jawaban untuk soal {{ $index + 1 }}">
                    @foreach(['A' => $question->option_a, 'B' => $question->option_b, 'C' => $question->option_c, 'D' => $question->option_d] as $key => $option)
                    <li class="option-item {{ ($existingAnswers[$question->id] ?? '') === $key ? 'selected' : '' }}"
                        role="radio"
                        aria-checked="{{ ($existingAnswers[$question->id] ?? '') === $key ? 'true' : 'false' }}"
                        tabindex="0"
                        data-option="{{ $key }}"
                        data-question-id="{{ $question->id }}"
                        data-audio="{{ $question->{'option_'.strtolower($key).'_audio'} ? asset('storage/' . $question->{'option_'.strtolower($key).'_audio'}) : '' }}"
                        onclick="selectOption(this, '{{ $key }}', {{ $question->id }})"
                        aria-label="Pilihan {{ $key }}: {{ $option }}. Tekan {{ $key }} pada keyboard untuk memilih.">
                        <span class="option-key" aria-hidden="true">{{ $key }}</span>
                        <span class="option-text">{{ $option }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endforeach
        </div>

        <div class="exam-nav">
            @if($currentSection->slug !== 'listening')
            <button class="btn btn-secondary" id="btn-prev" onclick="prevQuestion()" aria-label="Soal sebelumnya. Shortcut: tombol panah kiri atau P">
                ← Prev <kbd>P</kbd>
            </button>
            @else
            <div></div> <!-- Spacer -->
            @endif
            <span class="nav-hint" aria-hidden="true">Tekan A/B/C/D untuk jawab • ←→ untuk navigasi</span>
            <button class="btn btn-primary" id="btn-next" onclick="nextQuestion()" aria-label="Soal berikutnya. Shortcut: tombol panah kanan atau N">
                Next → <kbd>N</kbd>
            </button>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="exam-sidebar" role="complementary" aria-label="Panel navigasi soal">
        <div class="sidebar-title">Sections</div>
        <div class="section-tabs">
            @foreach($sections as $section)
            <div class="section-tab {{ $section->slug === $currentSection->slug ? 'active' : '' }}"
                 aria-label="Section {{ $section->name }}{{ $section->slug === $currentSection->slug ? ' - aktif' : '' }}"
                 aria-current="{{ $section->slug === $currentSection->slug ? 'step' : 'false' }}">
                <span aria-hidden="true">{{ $section->slug === $currentSection->slug ? '▶' : '○' }}</span>
                {{ $section->name }}
            </div>
            @endforeach
        </div>

        <div class="sidebar-title">Navigasi Soal</div>
        <div class="question-grid" role="list" aria-label="Grid navigasi soal">
            @php $lastDirectionId = null; @endphp
            @foreach($questions as $index => $question)
                @if($question->direction_id && $question->direction_id !== $lastDirectionId)
                    @php $dirLabel = $directions->firstWhere('id', $question->direction_id); @endphp
                    @if($dirLabel)
                    <div class="part-label-sidebar">{{ $dirLabel->label }}</div>
                    @endif
                    @php $lastDirectionId = $question->direction_id; @endphp
                @endif
            <button class="q-btn {{ isset($existingAnswers[$question->id]) ? 'answered' : '' }}"
                    id="q-nav-{{ $index }}"
                    onclick="goToQuestion({{ $index }})"
                    role="listitem"
                    aria-label="Soal {{ $index + 1 }}{{ isset($existingAnswers[$question->id]) ? ', sudah dijawab' : ', belum dijawab' }}">
                {{ $index + 1 }}
            </button>
            @endforeach
        </div>

        <div class="sidebar-actions">
            @php
                $sectionIndex = $sections->pluck('slug')->search($currentSection->slug);
                $isLastSection = $sectionIndex === $sections->count() - 1;
            @endphp

            @if(!$isLastSection)
            <form method="POST" action="{{ route('exam.nextSection', $examAttempt) }}" id="next-section-form">
                @csrf
                <button type="button" class="btn btn-primary" style="width:100%;justify-content:center;"
                        onclick="confirmNextSection()"
                        aria-label="Lanjut ke section berikutnya. Shortcut: L">
                    Lanjut Section → <kbd>L</kbd>
                </button>
            </form>
            @else
            <a href="{{ route('exam.submit', $examAttempt) }}"
               class="btn btn-success" style="justify-content:center;"
               id="submit-btn"
               onclick="confirmSubmit(event)"
               aria-label="Submit ujian. Shortcut: S">
                ✅ Submit Ujian <kbd>S</kbd>
            </a>
            @endif

            <button class="btn btn-secondary btn-sm" onclick="readCurrentQuestion()" style="width:100%;justify-content:center;"
                    aria-label="Baca ulang soal saat ini. Shortcut: R">
                🔊 Baca Soal <kbd>R</kbd>
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // ============================================================
    // EXAM ENGINE — Step-based navigation with Part/Directions
    // ============================================================

    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;
    const SAVE_URL = "{{ route('exam.answer', $examAttempt) }}";
    const totalQuestions = {{ $questions->count() }};
    const durationSeconds = {{ $currentSection->duration_minutes }} * 60;
    const isListening = "{{ $currentSection->slug }}" === 'listening';
    const isStructure = "{{ $currentSection->slug }}" === 'structure';
    const isReading = "{{ $currentSection->slug }}" === 'reading';

    // ============================================================
    // STEP MAP — For listening, steps include directions + questions
    // ============================================================
    // stepMap: array of { type: 'directions'|'question', part: 'A'|'B'|'C', questionIndex: number|null }

    const stepMap = [];
    let currentStep = 0;
    let currentQuestionIndex = -1; // Track which question is currently visible
    let canProceed = true;

    // Build step map from directions and questions (works for ALL sections)
    (function() {
        @php
            $directionsJson = $directions->values()->map(function($d) {
                return [
                    'id' => $d->id,
                    'label' => $d->label,
                    'questionIds' => $d->questions->pluck('id')->toArray(),
                ];
            });
            $questionsJson = $questions->values()->map(function($q) {
                return [
                    'id' => $q->id,
                    'direction_id' => $q->direction_id,
                ];
            });
        @endphp
        const directions = @json($directionsJson);
        const questions = @json($questionsJson);

        if (directions.length > 0) {
            // Section has directions: insert direction step before each group of questions
            let lastDirectionId = null;
            questions.forEach((q, idx) => {
                const dirId = q.direction_id;
                if (dirId && dirId !== lastDirectionId) {
                    const dir = directions.find(d => d.id === dirId);
                    stepMap.push({ type: 'directions', directionId: dirId, label: dir ? dir.label : '', questionIndex: null });
                    lastDirectionId = dirId;
                }
                stepMap.push({ type: 'question', directionId: q.direction_id, questionIndex: idx });
            });
        } else {
            // No directions: simple 1:1 mapping
            for (let i = 0; i < totalQuestions; i++) {
                stepMap.push({ type: 'question', directionId: null, questionIndex: i });
            }
        }
    })();

    const totalSteps = stepMap.length;

    // ============================================================
    // LISTENING AUDIO STATE MANAGEMENT
    // ============================================================
    const audioStates = {};
    const directionsAudioStates = {};
    let currentListeningAudio = null;

    function stopCurrentAudio() {
        if (currentListeningAudio) {
            currentListeningAudio.pause();
            currentListeningAudio.currentTime = 0;
            currentListeningAudio = null;
        }
        if ('speechSynthesis' in window) {
            window.speechSynthesis.cancel();
        }
    }

    // Play question audio (1x only)
    function playListeningAudio(index) {
        if (audioStates[index] === 'played' || audioStates[index] === 'playing') {
            if (audioStates[index] === 'played') {
                announce('Audio sudah pernah diputar. Tidak bisa diputar ulang.');
            }
            return;
        }

        const btn = document.getElementById(`speaker-btn-${index}`);
        if (!btn) return;
        const audioSrc = btn.dataset.audioSrc;
        if (!audioSrc) return;

        stopCurrentAudio();

        const audio = new Audio(audioSrc);
        currentListeningAudio = audio;
        audioStates[index] = 'playing';
        updateSpeakerIcon(`speaker-btn-${index}`, `speaker-label-${index}`, 'playing', `audio soal ${index + 1}`);

        audio.play().catch(e => {
            console.error('Audio play failed:', e);
            audioStates[index] = 'idle';
            updateSpeakerIcon(`speaker-btn-${index}`, `speaker-label-${index}`, 'idle', `audio soal ${index + 1}`);
            announce('Gagal memutar audio. Silakan coba lagi.');
        });

        audio.addEventListener('ended', () => {
            audioStates[index] = 'played';
            updateSpeakerIcon(`speaker-btn-${index}`, `speaker-label-${index}`, 'played', `audio soal ${index + 1}`);
            currentListeningAudio = null;
            
            announce('Audio selesai diputar. Membacakan pilihan jawaban...');

            // Gather options text
            const qContainer = document.getElementById(`question-${index}`);
            let optionsText = 'Options. ';
            if (qContainer) {
                const options = qContainer.querySelectorAll('.option-item');
                options.forEach(opt => {
                    const optKey = opt.dataset.option;
                    const optText = opt.querySelector('.option-text').textContent.trim();
                    optionsText += `${optKey}: ${optText}. `;
                });
            }

            // Speak options, start timer when finished
            if ('speechSynthesis' in window) {
                const utterance = new SpeechSynthesisUtterance(optionsText);
                utterance.lang = 'en-US';
                utterance.onend = () => {
                    announce('Waktu menjawab dimulai.');
                    if (isListening) {
                        timerPaused = false;
                        timeLeft = 15;
                        document.getElementById('timer-text').textContent = '15';
                        document.getElementById('timer').classList.remove('warning', 'danger');
                    }
                };
                window.speechSynthesis.speak(utterance);
            } else {
                announce('Waktu menjawab dimulai.');
                if (typeof speak === 'function') speak('Audio finished. Timer started. Select your answer.');
                if (isListening) {
                    timerPaused = false;
                    timeLeft = 15;
                    document.getElementById('timer-text').textContent = '15';
                    document.getElementById('timer').classList.remove('warning', 'danger');
                }
            }
        });

        audio.addEventListener('error', () => {
            audioStates[index] = 'idle';
            updateSpeakerIcon(`speaker-btn-${index}`, `speaker-label-${index}`, 'idle', `audio soal ${index + 1}`);
            currentListeningAudio = null;
            announce('Error memutar audio.');
        });
    }

    // Play directions audio (Toggle Play/Pause/Restart)
    function playDirectionsAudio(part) {
        const btn = document.getElementById(`dir-speaker-${part}`);
        if (!btn) return;
        const audioSrc = btn.dataset.audioSrc;
        if (!audioSrc) return;

        // Toggling if it is the same audio
        if (currentListeningAudio && currentListeningAudio.src && currentListeningAudio.src.includes(audioSrc)) {
            if (!currentListeningAudio.paused && !currentListeningAudio.ended) {
                // Pause it
                currentListeningAudio.pause();
                directionsAudioStates[part] = 'paused';
                updateSpeakerIcon(`dir-speaker-${part}`, `dir-label-${part}`, 'idle', `Directions Part ${part}`);
                announce('Audio dihentikan sementara. Tekan M untuk melanjutkan.');
                return;
            } else if (currentListeningAudio.paused && !currentListeningAudio.ended) {
                // Resume it
                currentListeningAudio.play().catch(e => console.error(e));
                directionsAudioStates[part] = 'playing';
                updateSpeakerIcon(`dir-speaker-${part}`, `dir-label-${part}`, 'playing', `Directions Part ${part}`);
                announce('Melanjutkan pemutaran audio Directions.');
                return;
            } else if (currentListeningAudio.ended) {
                // Restart it
                currentListeningAudio.currentTime = 0;
                currentListeningAudio.play().catch(e => console.error(e));
                directionsAudioStates[part] = 'playing';
                updateSpeakerIcon(`dir-speaker-${part}`, `dir-label-${part}`, 'playing', `Directions Part ${part}`);
                announce('Mengulang pemutaran audio Directions.');
                return;
            }
        }

        stopCurrentAudio();

        const audio = new Audio(audioSrc);
        currentListeningAudio = audio;
        directionsAudioStates[part] = 'playing';
        updateSpeakerIcon(`dir-speaker-${part}`, `dir-label-${part}`, 'playing', `Directions Part ${part}`);

        audio.play().catch(e => {
            console.error('Directions audio play failed:', e);
            directionsAudioStates[part] = 'idle';
            updateSpeakerIcon(`dir-speaker-${part}`, `dir-label-${part}`, 'idle', `Directions Part ${part}`);
            announce('Gagal memutar audio. Silakan coba lagi.');
        });

        audio.addEventListener('ended', () => {
            directionsAudioStates[part] = 'played';
            updateSpeakerIcon(`dir-speaker-${part}`, `dir-label-${part}`, 'played', `Directions Part ${part}`);
            
            const currentStepData = stepMap[currentStep];
            if (currentStepData && currentStepData.type === 'directions' && currentStepData.directionId == part) {
                canProceed = true;
                const nextBtn = document.getElementById('btn-next');
                if (nextBtn) nextBtn.disabled = false;
            }

            announce('Audio Directions selesai diputar. Tekan M jika ingin mengulang, atau N untuk melanjutkan.');
            if (typeof speak === 'function') speak('Audio finished. Press M to replay, or N to continue.');
        });

        audio.addEventListener('error', () => {
            directionsAudioStates[part] = 'idle';
            updateSpeakerIcon(`dir-speaker-${part}`, `dir-label-${part}`, 'idle', `Directions Part ${part}`);
            if (currentListeningAudio === audio) currentListeningAudio = null;
            announce('Error memutar audio.');
        });
    }

    // Generic speaker icon state updater
    function updateSpeakerIcon(btnId, labelId, state, contextName) {
        const btn = document.getElementById(btnId);
        const label = document.getElementById(labelId);
        if (!btn || !label) return;

        btn.classList.remove('state-idle', 'state-playing', 'state-played');
        label.classList.remove('state-idle', 'state-playing', 'state-played');
        btn.classList.add(`state-${state}`);
        label.classList.add(`state-${state}`);

        const labels = {
            idle:    { text: 'Klik untuk memutar audio', aria: `Putar ${contextName}. Audio hanya bisa diputar 1 kali.` },
            playing: { text: 'Audio sedang diputar...', aria: `${contextName} sedang diputar.` },
            played:  { text: 'Audio telah diputar', aria: `${contextName} sudah selesai. Tidak bisa diputar ulang.` },
        };

        label.textContent = labels[state].text;
        btn.setAttribute('aria-label', labels[state].aria);
    }

    // ============================================================
    // TIMER
    // ============================================================
    let timeLeft = isListening ? 15 : durationSeconds;
    let answers = @json($existingAnswers);
    let timerPaused = isListening ? true : false; // Listening: paused until audio ends

    const timerInterval = setInterval(() => {
        if (timerPaused) return;

        timeLeft--;
        if (timeLeft <= 0) {
            if (isListening) {
                // Per-question timer expired: auto-advance to next question
                timerPaused = true;
                document.getElementById('timer-text').textContent = '0';
                
                // Play a simple beep instead of speaking words
                if (typeof audioCtx !== 'undefined') {
                    if (audioCtx.state === 'suspended') audioCtx.resume();
                    const osc = audioCtx.createOscillator();
                    const gain = audioCtx.createGain();
                    osc.type = 'square';
                    osc.frequency.setValueAtTime(440, audioCtx.currentTime);
                    osc.frequency.setValueAtTime(330, audioCtx.currentTime + 0.15);
                    gain.gain.setValueAtTime(0.1, audioCtx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.4);
                    osc.connect(gain);
                    gain.connect(audioCtx.destination);
                    osc.start();
                    osc.stop(audioCtx.currentTime + 0.4);
                }

                setTimeout(() => { nextQuestion(); }, 1000);
                return;
            }

            // Non-listening: section timer expired
            clearInterval(timerInterval);

            let sectionAudioName = '{{ $currentSection->name }}';
            if (isStructure) sectionAudioName = 'Section 2 Structure';
            if (isReading) sectionAudioName = 'Section 3 Reading';

            speak(`${sectionAudioName}, Complete.`);
            announce(`Waktu habis untuk ${sectionAudioName}.`);

            setTimeout(() => {
                @if(!$isLastSection)
                    document.getElementById('next-section-form')?.submit();
                @else
                    window.location.href = "{{ route('exam.submit', $examAttempt) }}";
                @endif
            }, 3000);

            return;
        }

        const mins = Math.floor(timeLeft / 60);
        const secs = timeLeft % 60;
        const display = isListening
            ? String(secs)
            : `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
        document.getElementById('timer-text').textContent = display;

        const timerEl = document.getElementById('timer');
        timerEl.classList.remove('warning', 'danger');
        if (timeLeft <= (isListening ? 5 : 60)) {
            timerEl.classList.add('danger');
        } else if (timeLeft <= (isListening ? 10 : 300)) {
            timerEl.classList.add('warning');
        }

        if (!isListening) {
            if (timeLeft === 300) { speak('Five Minutes Left'); }
        }
    }, 1000);

    // ============================================================
    // STEP-BASED NAVIGATION
    // ============================================================

    function hideAllSteps() {
        // Hide all directions blocks
        document.querySelectorAll('.directions-block').forEach(el => el.classList.remove('active'));
        // Remove active highlight from all question containers (but keep them visible)
        document.querySelectorAll('.question-container').forEach(el => el.classList.remove('current-question'));
    }

    function goToStep(stepIndex) {
        if (stepIndex < 0 || stepIndex >= totalSteps) return;

        // Listening: cannot go back
        if (isListening && stepIndex < currentStep) {
            announce('Tidak bisa kembali ke soal sebelumnya pada section Listening.');
            return;
        }

        // Stop any playing audio when navigating
        stopCurrentAudio();

        hideAllSteps();

        // Deactivate previous nav button
        if (currentQuestionIndex >= 0) {
            document.getElementById(`q-nav-${currentQuestionIndex}`)?.classList.remove('active');
        }

        currentStep = stepIndex;
        const step = stepMap[currentStep];

        if (step.type === 'directions') {
            // Show directions block
            const dirBlock = document.getElementById(`directions-${step.directionId}`);
            if (dirBlock) dirBlock.classList.add('active');

            // Update part indicator
            document.getElementById('part-indicator').textContent = step.label ? `— ${step.label}` : '';
            document.getElementById('part-indicator').style.display = '';

            // Update counter to show "Directions"
            document.getElementById('question-counter').style.visibility = 'hidden';

            // Pause the per-question timer during directions
            if (isListening) {
                timerPaused = true;
                document.getElementById('timer-text').textContent = '—';
            }

            // No longer lock the next button
            canProceed = true;
            const nextBtn = document.getElementById('btn-next');
            if (nextBtn) nextBtn.disabled = false;

            const speakerBtn = document.getElementById(`dir-speaker-${step.directionId}`);
            if (speakerBtn) {
                if (isListening) {
                    if (directionsAudioStates[step.directionId] !== 'played') {
                        announce(`Directions ${step.label}. Tekan tombol M untuk memutar petunjuk audio, atau tekan N untuk langsung melanjutkan.`);
                        if (typeof speak === 'function') speak(`Directions ${step.label}. Press M to play audio, or N to skip.`);
                    } else {
                        announce(`Directions ${step.label}. Dengarkan petunjuk, lalu klik Berikutnya.`);
                    }
                } else {
                    announce(`Directions ${step.label}. Tekan tombol M untuk memutar petunjuk, atau tekan N untuk langsung melanjutkan.`);
                    readDirectionsTTS(step.directionId, step.label);
                }
            }

        } else {
            // Show question
            currentQuestionIndex = step.questionIndex;
            const qContainer = document.getElementById(`question-${currentQuestionIndex}`);
            if (qContainer) {
                qContainer.classList.add('current-question');
                qContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }

            // Activate nav button
            document.getElementById(`q-nav-${currentQuestionIndex}`)?.classList.add('active');

            // Update counter
            document.getElementById('question-counter').style.visibility = 'visible';
            document.getElementById('current-num').textContent = currentQuestionIndex + 1;

            // Update part indicator
            if (step.directionId) {
                const dir = @json($directions->values()->map(fn($d) => ['id' => $d->id, 'label' => $d->label])).find(d => d.id === step.directionId);
                if (dir) {
                    document.getElementById('part-indicator').textContent = `— ${dir.label}`;
                    document.getElementById('part-indicator').style.display = '';
                }
            }

            // Reset timer for listening questions — keep paused until audio plays
            if (isListening) {
                timerPaused = true;
                timeLeft = 15;
                document.getElementById('timer-text').textContent = '—';
                document.getElementById('timer').classList.remove('warning', 'danger');
            }

            announce(`Question ${currentQuestionIndex + 1} of ${totalQuestions}`);

            // For listening: auto-play question audio
            if (isListening) {
                const speakerBtn = document.getElementById(`speaker-btn-${currentQuestionIndex}`);
                if (speakerBtn && audioStates[currentQuestionIndex] !== 'played') {
                    setTimeout(() => { playListeningAudio(currentQuestionIndex); }, 500);
                }
            }

            // Read question if TTS enabled (non-listening)
            if (!isListening) {
                readCurrentQuestion();
            }

            // Update nav buttons
            if (!isListening) {
                const prevBtn = document.getElementById('btn-prev');
                if (prevBtn) prevBtn.disabled = currentStep === 0;
            }
        }

        // Update next button text
        const nextBtn = document.getElementById('btn-next');
        if (currentStep === totalSteps - 1) {
            nextBtn.innerHTML = 'Terakhir';
        } else {
            nextBtn.innerHTML = 'Next → <kbd>N</kbd>';
        }
    }

    // Wrapper: go to a question index (used by sidebar grid)
    function goToQuestion(qIndex) {
        // Find the step index for this question
        const stepIndex = stepMap.findIndex(s => s.type === 'question' && s.questionIndex === qIndex);
        if (stepIndex >= 0) {
            goToStep(stepIndex);
        }
    }

    function nextQuestion() {
        if (!canProceed) {
            announce('Silakan dengarkan instruksi audio hingga selesai sebelum menekan Next.');
            if (typeof speak === 'function') speak('Please listen to the audio until finished.');
            return;
        }

        if (currentStep < totalSteps - 1) {
            goToStep(currentStep + 1);
        } else if (isListening) {
            // End of listening section
            speak('Section 1 Listening, Complete.');
            setTimeout(() => {
                @if(!$isLastSection)
                    document.getElementById('next-section-form')?.submit();
                @else
                    window.location.href = "{{ route('exam.submit', $examAttempt) }}";
                @endif
            }, 3000);
        }
    }

    function prevQuestion() {
        if (currentStep > 0) {
            goToStep(currentStep - 1);
        }
    }

    // Select option
    function selectOption(el, key, questionId) {
        const container = el.closest('.question-container');
        const options = container.querySelectorAll('.option-item');

        options.forEach(opt => {
            opt.classList.remove('selected');
            opt.setAttribute('aria-checked', 'false');
        });

        el.classList.add('selected');
        el.setAttribute('aria-checked', 'true');

        answers[questionId] = key;

        const optText = el.querySelector('.option-text') ? el.querySelector('.option-text').textContent.trim() : '';
        const textToSpeak = optText; // Only read the option text
        
        // Silently update screen reader live region without adding extra words if not needed
        // Or we can just let TTS handle the reading.
        // We will still keep the announce for aria-live, but with just the option text.
        announce(textToSpeak);

        if ('speechSynthesis' in window) {
            window.speechSynthesis.cancel(); // Stop anything currently speaking
            const utterance = new SpeechSynthesisUtterance(textToSpeak);
            utterance.lang = 'en-US';
            window.speechSynthesis.speak(utterance);
        } else if (typeof speak === 'function') {
            speak(textToSpeak);
        }

        // Update nav button
        if (currentQuestionIndex >= 0) {
            document.getElementById(`q-nav-${currentQuestionIndex}`)?.classList.add('answered');
        }

        // Save to server
        fetch(SAVE_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                question_id: questionId,
                selected_answer: key,
            }),
        }).then(res => res.json()).then(data => {
            if (data.success && typeof playSaveBeep === 'function') {
                playSaveBeep();
            }
            // No auto-advance: student can change answer until timer expires or presses Next
        }).catch(err => console.error('Save failed:', err));
    }

    let passageHasBeenRead = false;

    // Helper to read specific question (e.g. from click)
    function readSpecificQuestion(index) {
        if (index < 0) return;
        let qText = document.getElementById(`q-text-${index}`)?.textContent || '';
        
        let passageText = '';
        if (isReading && !passageHasBeenRead && index === 0) {
            const passageArea = document.getElementById('passage-area');
            if (passageArea) {
                const title = passageArea.querySelector('h3')?.textContent || '';
                let content = '';
                passageArea.querySelectorAll('p').forEach(p => content += p.textContent + '. ');
                passageText = `Passage: ${title}. ${content}`;
            }
            passageHasBeenRead = true;
        }
        
        const qContainer = document.getElementById(`question-${index}`);
        let optionsText = [];
        if (qContainer) {
            const options = qContainer.querySelectorAll('.option-item');
            options.forEach(opt => {
                const key = opt.querySelector('.option-key').textContent;
                const text = opt.querySelector('.option-text').textContent;
                optionsText.push(`${key}. ${text}`);
            });
        }

        if (isStructure || isReading) {
            // Replace underscores (____) or dots (...) with "bla... bla... bla..."
            qText = qText.replace(/(\([^)]*\)|_+|\.{3,})/g, 'bla... bla... bla...');
        }

        let fullSpeech = "";
        if (passageText) {
            fullSpeech += passageText + ". ";
        }
        if (qText) {
            fullSpeech += `Question ${index + 1}. ${qText}. `;
            if (isStructure || isReading) {
                fullSpeech += "The options are. " + optionsText.join(". ") + ".";
            }
        }
        
        if (fullSpeech) {
            speak(fullSpeech);
        }
    }

    // Read current question via TTS (auto-advance / 'M' key)
    function readCurrentQuestion() {
        readSpecificQuestion(currentQuestionIndex);
    }

    // Read current directions via TTS
    function readDirectionsTTS(directionId, label) {
        stopCurrentAudio();
        const dirDesc = document.querySelector(`#directions-${directionId} .directions-subtitle.prose`)?.textContent || '';
        if (dirDesc) {
            speak(`Directions ${label}. ${dirDesc.replace(/<[^>]*>?/gm, '')}`);
        }
    }

    // Custom Confirm Modal Logic
    function showConfirmModal(title, message, onConfirm) {
        let existingModal = document.getElementById('custom-confirm-modal');
        if (!existingModal) {
            existingModal = document.createElement('div');
            existingModal.id = 'custom-confirm-modal';
            existingModal.className = 'modal-overlay';
            existingModal.setAttribute('role', 'dialog');
            existingModal.setAttribute('aria-modal', 'true');
            existingModal.innerHTML = `
                <div class="modal">
                    <h2 id="confirm-title" style="margin-bottom: 12px;"></h2>
                    <p id="confirm-msg" style="margin-bottom: 24px; color: var(--text-muted);"></p>
                    <div style="display: flex; gap: 12px; justify-content: flex-end;">
                        <button class="btn btn-secondary" id="confirm-cancel" aria-label="Batal">Batal (P / Esc)</button>
                        <button class="btn btn-primary" id="confirm-ok" aria-label="Ya, Lanjutkan">Lanjutkan (Y / Enter)</button>
                    </div>
                </div>
            `;
            document.body.appendChild(existingModal);
        }

        document.getElementById('confirm-title').textContent = title;
        document.getElementById('confirm-msg').textContent = message;

        const cancelBtn = document.getElementById('confirm-cancel');
        const okBtn = document.getElementById('confirm-ok');

        existingModal.classList.add('active');
        okBtn.focus();
        announce(title + '. ' + message);

        return new Promise((resolve) => {
            const cleanup = () => {
                cancelBtn.onclick = null;
                okBtn.onclick = null;
                document.removeEventListener('keydown', keyHandler);
                existingModal.classList.remove('active');
            };

            const handleYes = () => { cleanup(); resolve(true); };
            const handleNo = () => { cleanup(); resolve(false); };

            cancelBtn.onclick = handleNo;
            okBtn.onclick = handleYes;

            const keyHandler = (e) => {
                const key = e.key.toUpperCase();
                if (key === 'ESCAPE' || key === 'P') { e.preventDefault(); handleNo(); }
                if (key === 'ENTER' || key === 'Y') { e.preventDefault(); handleYes(); }
            };
            document.addEventListener('keydown', keyHandler);
        });
    }

    // Confirm next section
    async function confirmNextSection() {
        const answered = document.querySelectorAll('.q-btn.answered').length;
        const msg = `You have answered ${answered} of ${totalQuestions} questions in this section. Move to the next section?`;

        const confirmed = await showConfirmModal('Pindah Section', msg);
        if (confirmed) {
            document.getElementById('next-section-form').submit();
        }
    }

    // Confirm submit
    async function confirmSubmit(e) {
        if (e) e.preventDefault();
        const answered = document.querySelectorAll('.q-btn.answered').length;
        const msg = `You have answered ${answered} of ${totalQuestions} questions. Submit your exam?`;

        const confirmed = await showConfirmModal('Submit Ujian', msg);
        if (confirmed) {
            speak('Exam, Complete.');
            setTimeout(() => {
                window.location.href = document.getElementById('submit-btn').href;
            }, 2000);
        }
    }

    // ============================================================
    // KEYBOARD SHORTCUTS (Exam-specific)
    // ============================================================
    document.addEventListener('keydown', (e) => {
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;
        if (e.ctrlKey || e.altKey || e.metaKey) return;

        const key = e.key.toUpperCase();

        // Spacebar - Pause / Resume TTS (Section 2 & 3)
        if (e.code === 'Space') {
            e.preventDefault();
            if (!isListening && window.speechSynthesis) {
                if (window.speechSynthesis.paused) {
                    window.speechSynthesis.resume();
                } else if (window.speechSynthesis.speaking) {
                    window.speechSynthesis.pause();
                }
            }
            return;
        }

        // S — Submit
        if (key === 'S') {
            e.preventDefault();
            const submitBtn = document.getElementById('submit-btn');
            if (submitBtn) {
                confirmSubmit().then(confirmed => {
                    if (confirmed) window.location.href = submitBtn.href;
                });
            }
            return;
        }

        // M — Memutar Audio (Play audio atau TTS)
        if (key === 'M') {
            e.preventDefault();
            const step = stepMap[currentStep];
            if (step.type === 'directions') {
                if (isListening && step.directionId) {
                    playDirectionsAudio(step.directionId);
                } else {
                    readDirectionsTTS(step.directionId, step.label);
                }
            } else if (step.type === 'question') {
                if (isListening) {
                    playListeningAudio(step.questionIndex);
                } else {
                    stopCurrentAudio();
                    passageHasBeenRead = false;
                    readCurrentQuestion();
                }
            }
            return;
        }

        // L — Lanjut Section
        if (key === 'L') {
            e.preventDefault();
            const nextForm = document.getElementById('next-section-form');
            if (nextForm) { confirmNextSection(); }
            return;
        }

        // A, B, C, D — select answer (only when on a question step)
        if (['A', 'B', 'C', 'D'].includes(key)) {
            if (currentQuestionIndex < 0) return; // Skip if on directions
            const step = stepMap[currentStep];
            if (step?.type !== 'question') return;

            e.preventDefault();
            const container = document.getElementById(`question-${currentQuestionIndex}`);
            const option = container?.querySelector(`[data-option="${key}"]`);
            if (option) {
                const qId = parseInt(option.dataset.questionId);
                selectOption(option, key, qId);
            }
            return;
        }

        // Arrow Right or N — next
        if (e.key === 'ArrowRight' || key === 'N') {
            e.preventDefault();
            nextQuestion();
            return;
        }

        // Arrow Left or P — previous
        if (e.key === 'ArrowLeft' || key === 'P') {
            e.preventDefault();
            if (isListening) {
                announce('Tidak bisa kembali ke soal sebelumnya.');
            } else {
                prevQuestion();
            }
            return;
        }



        // T — Test Suara
        if (key === 'T') {
            e.preventDefault();
            try {
                let ac = window.audioCtx || new (window.AudioContext || window.webkitAudioContext)();
                if (ac.state === 'suspended') ac.resume();
                const osc = ac.createOscillator();
                const gain = ac.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(440, ac.currentTime);
                gain.gain.setValueAtTime(0.1, ac.currentTime);
                osc.connect(gain);
                gain.connect(ac.destination);
                osc.start();
                osc.stop(ac.currentTime + 0.5);
                announce('Test suara berhasil diputar.');
            } catch(e) {
                if (typeof speak === 'function') speak('Sound test.');
                announce('Test suara diputar.');
            }
            return;
        }



        // R — Play Audio Reading (Baca Ulang Soal)
        if (key === 'R') {
            e.preventDefault();
            const step = stepMap[currentStep];
            if (step && step.type === 'question' && !isListening) {
                stopCurrentAudio();
                passageHasBeenRead = false; // allow reading passage again if needed
                readCurrentQuestion();
                announce('Membaca ulang soal.');
            }
            return;
        }

        // 1-9 — jump to question
        if (e.key >= '1' && e.key <= '9') {
            const num = parseInt(e.key) - 1;
            if (num < totalQuestions) {
                e.preventDefault();
                goToQuestion(num);
            }
            return;
        }
    });

    // ============================================================
    // INITIALIZATION
    // ============================================================
    document.addEventListener('DOMContentLoaded', () => {
        // Start at step 0 (Directions if available, or Q1)
        goToStep(0);

        const sectionLabel = '{{ $currentSection->name }}';
        const hasDirections = stepMap.length > 0 && stepMap[0].type === 'directions';

        if (hasDirections) {
            const firstDirLabel = stepMap[0].label || 'Directions';
            announce(`${sectionLabel} section. ${firstDirLabel}. Press H for keyboard shortcuts.`);
            if (typeof speak === 'function') {
                setTimeout(() => {
                    speak(`${sectionLabel} section. ${firstDirLabel}. Press H for keyboard shortcuts help.`);
                }, 500);
            }
        } else {
            announce(`${sectionLabel} section. Question 1 of {{ $questions->count() }}. Press H for keyboard shortcuts.`);
            if (typeof speak === 'function') {
                setTimeout(() => {
                    speak(`${sectionLabel} section. Question 1 of {{ $questions->count() }}. Press H for keyboard shortcuts help.`);
                }, 500);
            }
        }

        // Track current option audio for focus events
        let currentOptionAudio = null;

        document.querySelectorAll('.option-item').forEach(el => {
            el.addEventListener('focus', () => {
                if (currentOptionAudio) {
                    currentOptionAudio.pause();
                    currentOptionAudio = null;
                }

                const optKey = el.dataset.option;
                const optText = el.querySelector('.option-text').textContent;
                const audioUrl = el.dataset.audio;

                if (audioUrl) {
                    currentOptionAudio = new Audio(audioUrl);
                    currentOptionAudio.play().catch(e => console.log('Audio autoplay prevented'));
                } else {
                    speak(`Option ${optKey}. ${optText}`);
                }
            });
        });

        // Add focus listener for passage area (Reading section)
        const passageArea = document.getElementById('passage-area');
        if (passageArea) {
            passageArea.addEventListener('focus', () => {
                const title = passageArea.querySelector('h3').textContent;
                let content = '';
                passageArea.querySelectorAll('p').forEach(p => content += p.textContent + '. ');
                speak(`${title}. ${content}`);
            });
        }
    });
</script>
@endsection

