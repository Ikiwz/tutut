@extends('layouts.admin', ['title' => 'Exam Results & Proctoring Audit'])

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <flux:heading size="xl" level="1">Exam Results & Proctoring Audit</flux:heading>
            <flux:text class="mt-1">Candidate performance records, converted scores, and integrity violation logs.</flux:text>
        </div>
    </div>

    <flux:card class="space-y-4">
        <form method="GET" action="{{ route('admin.attempts.index') }}" class="flex flex-col sm:flex-row items-center gap-3">
            <div class="w-full sm:w-48">
                <flux:select name="status">
                    <option value="">All Statuses</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                </flux:select>
            </div>
            <div class="flex-1 w-full">
                <flux:input name="search" value="{{ request('search') }}" placeholder="Search by student name, ID, email, or serial number..." icon="magnifying-glass" clearable />
            </div>
            <div class="flex items-center gap-2">
                <flux:button type="submit" variant="filled">Filter</flux:button>
                @if(request('search') || request('status'))
                    <flux:button href="{{ route('admin.attempts.index') }}" variant="ghost">Reset</flux:button>
                @endif
            </div>
        </form>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>Serial & Candidate</flux:table.column>
                <flux:table.column>Test Session</flux:table.column>
                <flux:table.column>Scores (L / S / R)</flux:table.column>
                <flux:table.column>Total Score</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Proctor Violations</flux:table.column>
                <flux:table.column class="text-right">Actions</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($attempts as $attempt)
                    <flux:table.row>
                        <flux:table.cell>
                            <div class="font-bold text-zinc-900 dark:text-zinc-100 font-mono text-xs">
                                {{ $attempt->serial_number ?? ('#' . $attempt->id) }}
                            </div>
                            <div class="text-sm font-medium text-zinc-800 dark:text-zinc-200 mt-0.5">
                                {{ $attempt->user?->name ?? 'Deleted Candidate' }}
                            </div>
                            <div class="text-xs text-zinc-500 font-mono">
                                ID: {{ $attempt->user?->student_id ?? 'N/A' }}
                            </div>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="text-sm text-zinc-800 dark:text-zinc-200 font-medium">
                                {{ $attempt->testSession?->title ?? 'N/A' }}
                            </div>
                            <div class="text-xs text-zinc-500">
                                {{ $attempt->created_at->format('d M Y, H:i') }}
                            </div>
                        </flux:table.cell>
                        <flux:table.cell>
                            @if ($attempt->status === 'completed')
                                <div class="text-xs text-zinc-600 dark:text-zinc-400 font-mono">
                                    <span>L: <strong>{{ $attempt->score_listening }}</strong></span> /
                                    <span>S: <strong>{{ $attempt->score_structure }}</strong></span> /
                                    <span>R: <strong>{{ $attempt->score_reading }}</strong></span>
                                </div>
                            @else
                                <span class="text-xs text-zinc-400">In Progress</span>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            @if ($attempt->status === 'completed')
                                <flux:badge size="md" color="emerald" class="font-bold font-mono">
                                    {{ $attempt->total_score }}
                                </flux:badge>
                            @else
                                <span class="text-zinc-400 font-mono">-</span>
                            @endif
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
                            <div class="flex items-center gap-2">
                                @if ($attempt->focus_violations > 0)
                                    <flux:badge size="sm" color="danger" title="Focus blur / Tab switches">
                                        {{ $attempt->focus_violations }} Tab
                                    </flux:badge>
                                @endif
                                @if ($attempt->kiosk_violations > 0)
                                    <flux:badge size="sm" color="danger" title="Fullscreen exits">
                                        {{ $attempt->kiosk_violations }} Exit
                                    </flux:badge>
                                @endif
                                @if ($attempt->focus_violations === 0 && $attempt->kiosk_violations === 0)
                                    <flux:badge size="sm" color="success">Clean</flux:badge>
                                @endif
                            </div>
                        </flux:table.cell>
                        <flux:table.cell class="text-right">
                            <flux:button href="{{ route('admin.attempts.show', $attempt) }}" size="sm" variant="ghost" icon="eye">
                                Details
                            </flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="7" class="text-center py-8 text-zinc-500">
                            No exam attempts found.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        <div class="pt-4">
            {{ $attempts->links() }}
        </div>
    </flux:card>
</div>
@endsection

