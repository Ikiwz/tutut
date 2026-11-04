@extends('layouts.student', ['title' => 'EPT Examination Results'])

@section('content')
<div class="max-w-4xl mx-auto space-y-8 py-4">
    <!-- Header -->
    <div class="text-center space-y-2">
        <div class="inline-flex p-3 rounded-2xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 mb-2">
            <flux:icon icon="academic-cap" class="size-10" />
        </div>
        <flux:heading size="xl" level="1" class="text-3xl font-extrabold tracking-tight">
            Institutional EPT Examination Results
        </flux:heading>
        <flux:text class="text-sm">
            {{ $examAttempt->testSession->title ?? 'EPT Session' }} &bull;
            Serial: <span class="font-mono font-bold text-zinc-800 dark:text-zinc-200">{{ $examAttempt->serial_number ?? ('#' . $examAttempt->id) }}</span>
        </flux:text>
    </div>

    <!-- Scaled Total Score Banner Card -->
    <flux:card class="bg-gradient-to-br from-blue-600 via-indigo-600 to-blue-700 text-white text-center p-8 sm:p-12 rounded-3xl shadow-xl border-0 space-y-3">
        <div class="text-6xl sm:text-7xl font-black font-mono tracking-tight drop-shadow-sm">
            {{ $examAttempt->total_score ?? 0 }}
        </div>
        <div class="text-base font-semibold text-blue-100 uppercase tracking-widest text-xs">
            Converted Scaled Score &bull; Standardized TOEFL® Format (Max 677)
        </div>
        <div class="flex items-center justify-center gap-6 pt-4 text-xs text-blue-100 border-t border-white/20">
            <span>Date: {{ $examAttempt->started_at?->format('d M Y') }}</span>
            <span>Duration: {{ $examAttempt->started_at?->diffForHumans($examAttempt->completed_at, true) }}</span>
            <span>Completed: {{ $examAttempt->completed_at?->format('H:i') }} WIB</span>
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

    <!-- Session Integrity & Proctoring Activity Audit Report -->
    @php
        $integrityLogs = $examAttempt->integrityLogs;
        $totalLogs = $integrityLogs->count();
        $severeCount = $integrityLogs->where('severity', 'severe')->count();
        $mediumCount = $integrityLogs->where('severity', 'medium')->count();
        $minorCount = $integrityLogs->where('severity', 'minor')->count();
    @endphp

    <flux:card class="space-y-4 border border-zinc-200 dark:border-zinc-800 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-zinc-100 dark:border-zinc-800 pb-4">
            <div class="flex items-center gap-3">
                <div class="p-2.5 rounded-xl {{ $totalLogs > 0 ? 'bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400' : 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400' }}">
                    <flux:icon icon="{{ $totalLogs > 0 ? 'shield-exclamation' : 'shield-check' }}" class="size-6" />
                </div>
                <div>
                    <flux:heading size="lg" level="2" class="font-bold">
                        Session Integrity &amp; Activity Audit
                    </flux:heading>
                    <flux:text class="text-xs">
                        Real-time proctoring record tracking browser focus, shortcut keys, and examination security events.
                    </flux:text>
                </div>
            </div>

            <!-- Severity Counters Badges -->
            <div class="flex flex-wrap items-center gap-2">
                @if ($severeCount > 0)
                    <flux:badge size="sm" color="danger" icon="exclamation-triangle">
                        {{ $severeCount }} Severe (Berat)
                    </flux:badge>
                @endif
                @if ($mediumCount > 0)
                    <flux:badge size="sm" color="warning" icon="exclamation-circle">
                        {{ $mediumCount }} Medium (Sedang)
                    </flux:badge>
                @endif
                @if ($minorCount > 0)
                    <flux:badge size="sm" color="zinc" icon="information-circle">
                        {{ $minorCount }} Minor (Sepele)
                    </flux:badge>
                @endif
                @if ($totalLogs === 0)
                    <flux:badge size="sm" color="success" icon="check-badge">
                        Flawless &bull; 0 Infractions
                    </flux:badge>
                @endif
            </div>
        </div>

        @if ($totalLogs > 0)
            <div class="overflow-x-auto">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>Time</flux:table.column>
                        <flux:table.column>Severity</flux:table.column>
                        <flux:table.column>Infraction / Activity</flux:table.column>
                        <flux:table.column>Section / Context</flux:table.column>
                        <flux:table.column>Audit Note</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach ($integrityLogs as $log)
                            <flux:table.row>
                                <flux:table.cell class="font-mono text-xs text-zinc-500 whitespace-nowrap">
                                    {{ $log->recorded_at?->format('H:i:s') ?? $log->created_at?->format('H:i:s') }}
                                </flux:table.cell>
                                <flux:table.cell>
                                    <flux:badge size="xs" color="{{ $log->severityColor() }}">
                                        {{ $log->severityLabel() }}
                                    </flux:badge>
                                </flux:table.cell>
                                <flux:table.cell class="font-semibold text-xs text-zinc-900 dark:text-zinc-100 whitespace-nowrap">
                                    {{ $log->title }}
                                </flux:table.cell>
                                <flux:table.cell class="text-xs text-zinc-600 dark:text-zinc-400 whitespace-nowrap">
                                    {{ $log->section_slug ? ucfirst($log->section_slug) : 'General' }}
                                    @if ($log->question_number)
                                        &bull; Q#{{ $log->question_number }}
                                    @endif
                                </flux:table.cell>
                                <flux:table.cell class="text-xs text-zinc-500">
                                    {{ $log->description ?: '-' }}
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </div>
        @else
            <div class="p-4 rounded-xl bg-emerald-50/50 dark:bg-emerald-950/20 border border-emerald-200/50 dark:border-emerald-800/50 flex items-center gap-3">
                <flux:icon icon="check-circle" class="size-5 text-emerald-600 dark:text-emerald-400 shrink-0" />
                <p class="text-xs text-emerald-800 dark:text-emerald-300">
                    <strong>Exemplary Academic Honesty:</strong> No tab switches, window un-focusing, unauthorized clipboard copy, or prohibited shortcut keys were registered during this examination session.
                </p>
            </div>
        @endif
    </flux:card>

    <!-- Actions -->
    <div class="flex items-center justify-center gap-4 pt-4">
        <flux:button href="{{ route('student.dashboard') }}" variant="primary" icon="arrow-left" class="font-bold">
            Back to Dashboard
        </flux:button>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        speak('Congratulations! Your converted scaled score is {{ $examAttempt->total_score }}. Press H for keyboard shortcuts.');
    });
</script>
@endsection
