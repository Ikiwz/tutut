@extends('layouts.admin', ['title' => 'Exam Attempt Details'])

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl" level="1">Exam Attempt Details</flux:heading>
            <flux:text class="mt-1 font-mono text-xs">Serial: {{ $examAttempt->serial_number ?? ('#' . $examAttempt->id) }}</flux:text>
        </div>
        <flux:button href="{{ route('admin.attempts.index') }}" variant="ghost" icon="arrow-left">Back to Results</flux:button>
    </div>

    <!-- Candidate & Score Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Candidate Information -->
        <flux:card class="space-y-4">
            <flux:heading size="lg">Candidate Identity</flux:heading>
            <flux:separator />

            <div class="space-y-3 text-sm">
                <div>
                    <span class="text-zinc-500 text-xs block">Full Name</span>
                    <span class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $examAttempt->user?->name ?? 'N/A' }}</span>
                </div>
                <div>
                    <span class="text-zinc-500 text-xs block">Student ID (NIM)</span>
                    <span class="font-mono font-medium text-zinc-900 dark:text-zinc-100">{{ $examAttempt->user?->student_id ?? 'Not provided' }}</span>
                </div>
                <div>
                    <span class="text-zinc-500 text-xs block">Department (Prodi)</span>
                    <span class="text-zinc-800 dark:text-zinc-200">{{ $examAttempt->user?->department ?? 'General' }}</span>
                </div>
                <div>
                    <span class="text-zinc-500 text-xs block">Email</span>
                    <span class="text-zinc-800 dark:text-zinc-200">{{ $examAttempt->user?->email ?? 'N/A' }}</span>
                </div>
                <div>
                    <span class="text-zinc-500 text-xs block">Academic Verification</span>
                    @if ($examAttempt->user?->verified_at)
                        <flux:badge size="sm" color="success">Verified ({{ $examAttempt->user->verified_at->format('d M Y') }})</flux:badge>
                    @else
                        <flux:badge size="sm" color="zinc">Unverified</flux:badge>
                    @endif
                </div>
            </div>
        </flux:card>

        <!-- TOEFL Scores Summary -->
        <flux:card class="space-y-4">
            <flux:heading size="lg">TOEFL Score Conversion</flux:heading>
            <flux:separator />

            <div class="text-center py-2">
                <div class="text-4xl font-extrabold text-blue-600 dark:text-blue-400 font-mono">
                    {{ $examAttempt->total_score ?? 0 }}
                </div>
                <div class="text-xs text-zinc-500 uppercase tracking-wider font-semibold mt-1">Official Scaled Score</div>
            </div>

            <div class="grid grid-cols-3 gap-2 text-center pt-2 border-t border-zinc-200 dark:border-zinc-700">
                <div class="p-2 rounded bg-zinc-50 dark:bg-zinc-800">
                    <div class="text-xs text-zinc-500">Listening</div>
                    <div class="text-lg font-bold text-zinc-800 dark:text-zinc-200 font-mono mt-1">{{ $examAttempt->score_listening ?? 0 }}</div>
                </div>
                <div class="p-2 rounded bg-zinc-50 dark:bg-zinc-800">
                    <div class="text-xs text-zinc-500">Structure</div>
                    <div class="text-lg font-bold text-zinc-800 dark:text-zinc-200 font-mono mt-1">{{ $examAttempt->score_structure ?? 0 }}</div>
                </div>
                <div class="p-2 rounded bg-zinc-50 dark:bg-zinc-800">
                    <div class="text-xs text-zinc-500">Reading</div>
                    <div class="text-lg font-bold text-zinc-800 dark:text-zinc-200 font-mono mt-1">{{ $examAttempt->score_reading ?? 0 }}</div>
                </div>
            </div>

            <div class="text-xs text-zinc-500 space-y-1 pt-2">
                <div class="flex justify-between">
                    <span>Started:</span>
                    <span>{{ $examAttempt->started_at?->format('d M Y, H:i:s') ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Completed:</span>
                    <span>{{ $examAttempt->completed_at?->format('d M Y, H:i:s') ?? '-' }}</span>
                </div>
            </div>
        </flux:card>

        <!-- Proctoring & Integrity Telemetry -->
        <flux:card class="space-y-4">
            <flux:heading size="lg">Proctoring Telemetry</flux:heading>
            <flux:separator />

            <div class="space-y-3 text-xs">
                <div>
                    <span class="text-zinc-500 block">Focus Violations (Tab Switches)</span>
                    <span class="font-bold text-sm {{ $examAttempt->focus_violations > 0 ? 'text-red-600 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                        {{ $examAttempt->focus_violations }} event(s)
                    </span>
                </div>

                <div>
                    <span class="text-zinc-500 block">Kiosk Violations (Fullscreen Exits)</span>
                    <span class="font-bold text-sm {{ $examAttempt->kiosk_violations > 0 ? 'text-red-600 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                        {{ $examAttempt->kiosk_violations }} event(s)
                    </span>
                </div>

                <div>
                    <span class="text-zinc-500 block">Device Token (Lockdown Key)</span>
                    <span class="font-mono text-zinc-700 dark:text-zinc-300 break-all">{{ $examAttempt->device_token ?? 'None' }}</span>
                </div>

                <div>
                    <span class="text-zinc-500 block">IP Address</span>
                    <span class="font-mono text-zinc-700 dark:text-zinc-300">{{ $examAttempt->ip_address ?? 'Localhost / Direct' }}</span>
                </div>

                <div>
                    <span class="text-zinc-500 block">Tamper Signature (HMAC)</span>
                    <span class="font-mono text-zinc-600 dark:text-zinc-400 break-all text-[11px]">{{ $examAttempt->signature ?? 'Unsigned legacy record' }}</span>
                </div>
            </div>
        </flux:card>
    </div>

    <!-- Answers Audit Table -->
    <flux:card class="space-y-4">
        <flux:heading size="lg">Answer Sheet Audit Log</flux:heading>
        <flux:separator />

        <flux:table>
            <flux:table.columns>
                <flux:table.column>#</flux:table.column>
                <flux:table.column>Question Text</flux:table.column>
                <flux:table.column>Selected Answer</flux:table.column>
                <flux:table.column>Result</flux:table.column>
                <flux:table.column>Answered At</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($examAttempt->answers as $index => $answer)
                    <flux:table.row>
                        <flux:table.cell>
                            <span class="font-mono text-zinc-400">#{{ $index + 1 }}</span>
                        </flux:table.cell>
                        <flux:table.cell class="max-w-md">
                            <div class="text-xs text-zinc-800 dark:text-zinc-200 line-clamp-2">
                                {{ $answer->question?->question_text ?: '(Listening Audio Question)' }}
                            </div>
                        </flux:table.cell>
                        <flux:table.cell>
                            <span class="font-mono font-bold text-zinc-900 dark:text-zinc-100">
                                {{ $answer->selected_answer }}
                            </span>
                        </flux:table.cell>
                        <flux:table.cell>
                            @if ($answer->is_correct)
                                <flux:badge size="sm" color="success">Correct</flux:badge>
                            @else
                                <flux:badge size="sm" color="danger">Incorrect</flux:badge>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell class="text-xs text-zinc-500">
                            {{ $answer->updated_at->format('H:i:s') }}
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="5" class="text-center py-6 text-zinc-500">
                            No answers recorded for this attempt.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </flux:card>
</div>
@endsection

