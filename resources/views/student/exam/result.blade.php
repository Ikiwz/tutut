@extends('layouts.student', ['title' => 'TOEFL Exam Results'])

@section('content')
<div class="max-w-4xl mx-auto space-y-8 py-4">
    <!-- Header -->
    <div class="text-center space-y-2">
        <div class="inline-flex p-3 rounded-2xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 mb-2">
            <flux:icon icon="academic-cap" class="size-10" />
        </div>
        <flux:heading size="xl" level="1" class="text-3xl font-extrabold tracking-tight">
            Official TOEFL Examination Results
        </flux:heading>
        <flux:text class="text-sm">
            {{ $examAttempt->testSession->title ?? 'TOEFL Exam Session' }} &bull;
            Serial: <span class="font-mono font-bold text-zinc-800 dark:text-zinc-200">{{ $examAttempt->serial_number ?? ('#' . $examAttempt->id) }}</span>
        </flux:text>
    </div>

    <!-- Scaled Total Score Banner Card -->
    <flux:card class="bg-gradient-to-br from-blue-600 via-indigo-600 to-blue-700 text-white text-center p-8 sm:p-12 rounded-3xl shadow-xl border-0 space-y-3">
        <div class="text-6xl sm:text-7xl font-black font-mono tracking-tight drop-shadow-sm">
            {{ $examAttempt->total_score ?? 0 }}
        </div>
        <div class="text-base font-semibold text-blue-100 uppercase tracking-widest text-xs">
            Official Scaled TOEFL Score (Max 677)
        </div>
        <div class="flex items-center justify-center gap-6 pt-4 text-xs text-blue-100 border-t border-white/20">
            <span>📅 {{ $examAttempt->started_at?->format('d M Y') }}</span>
            <span>⏱️ {{ $examAttempt->started_at?->diffForHumans($examAttempt->completed_at, true) }}</span>
            <span>✅ Completed: {{ $examAttempt->completed_at?->format('H:i') }} WIB</span>
        </div>
    </flux:card>

    <!-- Sub-scores Section Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        @php
            $icons = ['listening' => 'speaker-wave', 'structure' => 'pencil-square', 'reading' => 'book-open'];
        @endphp
        @foreach($sectionResults as $slug => $result)
            <flux:card class="text-center space-y-3 border border-zinc-200 dark:border-zinc-800 shadow-sm p-6">
                <div class="inline-flex p-2.5 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 mx-auto">
                    <flux:icon icon="{{ $icons[$slug] ?? 'document-text' }}" class="size-6" />
                </div>
                <div>
                    <flux:heading size="md" class="font-bold uppercase tracking-wider text-xs text-zinc-500">
                        {{ $result['name'] }}
                    </flux:heading>
                    <div class="text-3xl font-extrabold font-mono text-zinc-900 dark:text-white mt-1">
                        {{ $examAttempt->{'score_' . $slug} ?? 0 }}
                    </div>
                </div>

                <div class="text-xs text-zinc-500">
                    {{ $result['correct'] }} of {{ $result['total'] }} questions correct
                </div>

                <!-- Official Flux Progress -->
                @php $pct = $result['total'] > 0 ? round(($result['correct'] / $result['total']) * 100) : 0; @endphp
                <flux:progress :value="$pct" color="blue" />
            </flux:card>
        @endforeach
    </div>

    <!-- Actions -->
    <div class="flex items-center justify-center gap-4 pt-4">
        <flux:button href="{{ route('student.dashboard') }}" variant="primary" icon="arrow-left" size="lg" class="font-bold">
            Back to Dashboard
        </flux:button>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        speak('Congratulations! Your official TOEFL scaled score is {{ $examAttempt->total_score }}. Press H for keyboard shortcuts.');
    });
</script>
@endsection
