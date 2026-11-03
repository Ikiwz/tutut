@extends('layouts.student', ['title' => 'Candidate Dashboard'])

@section('content')
<div class="space-y-8">
    <!-- Welcome Banner Card -->
    <flux:card class="bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-700 dark:to-indigo-800 text-white rounded-2xl p-6 sm:p-8 shadow-lg border-0">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="space-y-2">
                <flux:heading size="xl" class="text-white font-extrabold text-2xl">
                    Welcome, {{ auth()->user()->name }}
                </flux:heading>
                <flux:text class="text-blue-100 text-sm max-w-xl">
                    Online TOEFL examination session.
                    Press <kbd class="px-2 py-0.5 rounded bg-white/20 text-white font-mono text-xs font-bold border border-white/30">H</kbd> anytime for keyboard shortcuts.
                </flux:text>
            </div>
            <div class="flex items-center gap-3">
                <flux:button variant="filled" icon="speaker-wave" onclick="testAudio()" class="bg-white text-blue-700 hover:bg-blue-50 font-bold border-0">
                    Test Audio (T)
                </flux:button>
            </div>
        </div>
    </flux:card>

    <!-- In Progress Warning / Resume Banner -->
    @if ($inProgressAttempt)
        <flux:callout variant="warning" icon="clock" class="shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 w-full">
                <div>
                    <div class="font-bold text-zinc-900 dark:text-white">Active Test Session In Progress</div>
                    <div class="text-xs text-zinc-600 dark:text-zinc-400 mt-0.5">
                        {{ $inProgressAttempt->testSession?->title ?? 'TOEFL Exam' }} — Section: {{ ucfirst($inProgressAttempt->current_section_slug ?? 'General') }}
                    </div>
                </div>
                <flux:button href="{{ route('exam.take', $inProgressAttempt) }}" variant="primary" icon:trailing="arrow-right" size="sm">
                    Continue Examination
                </flux:button>
            </div>
        </flux:callout>
    @endif

    <!-- Available Tests Section -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="lg" level="2">Available Test Sessions</flux:heading>
                <flux:text class="text-xs">Scheduled examination sessions eligible for your account.</flux:text>
            </div>
        </div>

        @if ($activeTests->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                @foreach ($activeTests as $test)
                    <flux:card class="flex flex-col justify-between space-y-4 border border-zinc-200 dark:border-zinc-800 shadow-sm hover:shadow-md transition-shadow">
                        <div class="space-y-3">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <flux:heading size="md" class="font-bold text-zinc-900 dark:text-white">
                                        {{ $test->title }}
                                    </flux:heading>
                                    <flux:text class="text-xs mt-1 text-zinc-500 line-clamp-2">
                                        {{ $test->description ?: 'Official TOEFL examination session.' }}
                                    </flux:text>
                                </div>
                                @if ($test->isLocked())
                                    <flux:badge size="sm" color="warning" icon="lock-closed">Locked</flux:badge>
                                @else
                                    <flux:badge size="sm" color="success" icon="check-circle">Open</flux:badge>
                                @endif
                            </div>

                            <!-- Schedule Details -->
                            @if ($test->starts_at || $test->ends_at)
                                <div class="p-3 rounded-xl bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200/80 dark:border-zinc-700/60 text-xs text-zinc-600 dark:text-zinc-300 space-y-1">
                                    @if ($test->starts_at)
                                        <div class="flex items-center gap-1.5">
                                            <flux:icon icon="calendar" class="size-3.5 text-zinc-400" />
                                            <span>Starts: <strong>{{ $test->starts_at->format('d M Y, H:i') }} WIB</strong></span>
                                        </div>
                                    @endif
                                    @if ($test->ends_at)
                                        <div class="flex items-center gap-1.5">
                                            <flux:icon icon="clock" class="size-3.5 text-zinc-400" />
                                            <span>Ends: <strong>{{ $test->ends_at->format('d M Y, H:i') }} WIB</strong></span>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <!-- Sections Badges -->
                            <div class="flex flex-wrap gap-1.5 pt-1">
                                @foreach ($test->sections as $sec)
                                    <flux:badge size="sm" color="info">{{ $sec->name }} ({{ $sec->duration_minutes }}m)</flux:badge>
                                @endforeach
                            </div>
                        </div>

                        <!-- Action Button -->
                        <div class="pt-2 border-t border-zinc-100 dark:border-zinc-800">
                            @if ($test->isLocked())
                                <flux:button disabled variant="filled" icon="lock-closed" class="w-full opacity-60 cursor-not-allowed">
                                    Locked (Starts {{ $test->starts_at->format('d M, H:i') }})
                                </flux:button>
                            @else
                                <form method="POST" action="{{ route('exam.start', $test) }}">
                                    @csrf
                                    <flux:button type="submit" variant="primary" icon="play" class="w-full font-bold">
                                        Start Exam
                                    </flux:button>
                                </form>
                            @endif
                        </div>
                    </flux:card>
                @endforeach
            </div>
        @else
            <flux:card class="text-center py-12 space-y-3">
                <flux:icon icon="clipboard-document-list" class="size-10 mx-auto text-zinc-400" />
                <flux:heading size="md">No tests currently available</flux:heading>
                <flux:text class="text-xs">There are no open TOEFL test schedules assigned to you right now.</flux:text>
            </flux:card>
        @endif
    </div>

    <!-- Exam History & Completed Attempts -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="lg" level="2">Exam History</flux:heading>
                <flux:text class="text-xs">Completed examination sessions and converted scores.</flux:text>
            </div>
        </div>

        <flux:card>
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Test Session &amp; Serial</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column>Listening</flux:table.column>
                    <flux:table.column>Structure</flux:table.column>
                    <flux:table.column>Reading</flux:table.column>
                    <flux:table.column>Total Score</flux:table.column>
                    <flux:table.column>Completed At</flux:table.column>
                    <flux:table.column class="text-right">Action</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($completedAttempts as $attempt)
                        <flux:table.row>
                            <flux:table.cell>
                                <div class="font-semibold text-zinc-900 dark:text-white">
                                    {{ $attempt->testSession?->title ?? 'TOEFL Exam' }}
                                </div>
                                <div class="text-xs text-zinc-500 font-mono">
                                    {{ $attempt->serial_number ?? ('#' . $attempt->id) }}
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge size="sm" color="success">Completed</flux:badge>
                            </flux:table.cell>
                            <flux:table.cell class="font-mono text-xs font-bold text-zinc-700 dark:text-zinc-300">
                                {{ $attempt->score_listening ?? 0 }}
                            </flux:table.cell>
                            <flux:table.cell class="font-mono text-xs font-bold text-zinc-700 dark:text-zinc-300">
                                {{ $attempt->score_structure ?? 0 }}
                            </flux:table.cell>
                            <flux:table.cell class="font-mono text-xs font-bold text-zinc-700 dark:text-zinc-300">
                                {{ $attempt->score_reading ?? 0 }}
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge size="md" color="emerald" class="font-extrabold font-mono text-sm">
                                    {{ $attempt->total_score ?? 0 }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell class="text-xs text-zinc-500">
                                {{ $attempt->completed_at?->format('d M Y, H:i') ?? '-' }}
                            </flux:table.cell>
                            <flux:table.cell class="text-right">
                                <flux:button href="{{ route('exam.result', $attempt) }}" size="sm" variant="ghost" icon="eye">
                                    View Result
                                </flux:button>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="8" class="text-center py-8 text-zinc-500 text-xs">
                                No completed examinations found.
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </flux:card>
    </div>
</div>
@endsection
