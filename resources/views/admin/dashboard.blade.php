@extends('layouts.admin', ['title' => 'Dashboard Overview'])

@section('content')
<div class="space-y-8">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <flux:heading size="xl" level="1">Dashboard Overview</flux:heading>
            <flux:text class="mt-1">Real-time campus examination statistics and proctoring summary.</flux:text>
        </div>
        <div class="flex items-center gap-3">
            <flux:button href="{{ route('admin.test-sessions.create') }}" variant="primary" icon="plus">
                New Test Session
            </flux:button>
            <flux:button href="{{ route('admin.questions.create') }}" variant="filled" icon="plus">
                Add Question
            </flux:button>
        </div>
    </div>

    <!-- Stats Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <flux:card class="flex items-center gap-4">
            <div class="p-3 rounded-xl bg-blue-50 dark:bg-blue-950 text-blue-600 dark:text-blue-400">
                <flux:icon icon="users" class="size-6" />
            </div>
            <div>
                <flux:text class="text-sm font-medium">Total Students</flux:text>
                <div class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white mt-1">
                    {{ number_format($stats['total_students']) }}
                </div>
            </div>
        </flux:card>

        <flux:card class="flex items-center gap-4">
            <div class="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400">
                <flux:icon icon="question-mark-circle" class="size-6" />
            </div>
            <div>
                <flux:text class="text-sm font-medium">Total Questions</flux:text>
                <div class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white mt-1">
                    {{ number_format($stats['total_questions']) }}
                </div>
            </div>
        </flux:card>

        <flux:card class="flex items-center gap-4">
            <div class="p-3 rounded-xl bg-amber-50 dark:bg-amber-950 text-amber-600 dark:text-amber-400">
                <flux:icon icon="clipboard-document-list" class="size-6" />
            </div>
            <div>
                <flux:text class="text-sm font-medium">Active Test Sessions</flux:text>
                <div class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white mt-1">
                    {{ number_format($stats['active_tests']) }}
                </div>
            </div>
        </flux:card>

        <flux:card class="flex items-center gap-4">
            <div class="p-3 rounded-xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400">
                <flux:icon icon="chart-bar" class="size-6" />
            </div>
            <div>
                <flux:text class="text-sm font-medium">Average EPT Score</flux:text>
                <div class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white mt-1">
                    {{ $stats['avg_score'] > 0 ? $stats['avg_score'] : 'N/A' }}
                </div>
            </div>
        </flux:card>
    </div>

    <!-- Active Sessions & Quick Highlights -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Active Sessions -->
        <flux:card class="lg:col-span-1 space-y-4">
            <div class="flex items-center justify-between">
                <flux:heading size="lg">Active Sessions</flux:heading>
                <flux:button href="{{ route('admin.test-sessions.index') }}" variant="ghost" size="sm">View All</flux:button>
            </div>
            <flux:separator />

            @forelse ($activeTestSessions as $session)
                <div class="p-3 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-zinc-50/50 dark:bg-zinc-800/50 space-y-2">
                    <div class="flex items-start justify-between gap-2">
                        <span class="font-semibold text-zinc-900 dark:text-white text-sm line-clamp-1">{{ $session->title }}</span>
                        <flux:badge size="sm" color="success">Active</flux:badge>
                    </div>
                    <div class="flex items-center justify-between text-xs text-zinc-500">
                        <span>Access Code: <code class="font-mono font-bold text-zinc-700 dark:text-zinc-300">{{ $session->access_code ?? 'None' }}</code></span>
                        <span>{{ $session->exam_attempts_count }} attempts</span>
                    </div>
                </div>
            @empty
                <div class="text-center py-6 text-zinc-500 text-sm">
                    No active test sessions found.
                </div>
            @endforelse
        </flux:card>

        <!-- Recent Exam Attempts Table -->
        <flux:card class="lg:col-span-2 space-y-4">
            <div class="flex items-center justify-between">
                <flux:heading size="lg">Recent Exam Attempts</flux:heading>
                <flux:button href="{{ route('admin.attempts.index') }}" variant="ghost" size="sm">View All</flux:button>
            </div>
            <flux:separator />

            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Student</flux:table.column>
                    <flux:table.column>Test Session</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column>Score</flux:table.column>
                    <flux:table.column>Date</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($recentAttempts as $attempt)
                        <flux:table.row>
                            <flux:table.cell>
                                <div class="font-medium text-zinc-900 dark:text-white">{{ $attempt->user?->name ?? 'Unknown' }}</div>
                                <div class="text-xs text-zinc-500 font-mono">{{ $attempt->serial_number ?? ('#' . $attempt->id) }}</div>
                            </flux:table.cell>
                            <flux:table.cell class="text-zinc-600 dark:text-zinc-400">
                                {{ $attempt->testSession?->title ?? 'N/A' }}
                            </flux:table.cell>
                            <flux:table.cell>
                                @if ($attempt->status === 'completed')
                                    <flux:badge size="sm" color="success">Completed</flux:badge>
                                @elseif ($attempt->status === 'in_progress')
                                    <flux:badge size="sm" color="warning">In Progress</flux:badge>
                                @else
                                    <flux:badge size="sm" color="zinc">{{ $attempt->status }}</flux:badge>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>
                                @if ($attempt->status === 'completed')
                                    <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ $attempt->total_score }}</span>
                                @else
                                    <span class="text-zinc-400">-</span>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell class="text-xs text-zinc-500">
                                {{ $attempt->created_at->format('d M Y, H:i') }}
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="5" class="text-center py-6 text-zinc-500">
                                No exam attempts recorded yet.
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </flux:card>
    </div>
</div>
@endsection

