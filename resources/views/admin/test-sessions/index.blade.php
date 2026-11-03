@extends('layouts.admin', ['title' => 'Test Sessions'])

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <flux:heading size="xl" level="1">Test Sessions</flux:heading>
            <flux:text class="mt-1">Manage official exam schedules, proctoring access codes, and active statuses.</flux:text>
        </div>
        <flux:button href="{{ route('admin.test-sessions.create') }}" variant="primary" icon="plus">
            Create Session
        </flux:button>
    </div>

    <flux:card class="space-y-4">
        <form method="GET" action="{{ route('admin.test-sessions.index') }}" class="flex items-center gap-3">
            <div class="flex-1">
                <flux:input name="search" value="{{ request('search') }}" placeholder="Search by session title or access code..." icon="magnifying-glass" clearable />
            </div>
            <flux:button type="submit" variant="filled">Search</flux:button>
            @if(request('search'))
                <flux:button href="{{ route('admin.test-sessions.index') }}" variant="ghost">Reset</flux:button>
            @endif
        </form>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>Title & Access Code</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Sections</flux:table.column>
                <flux:table.column>Schedule</flux:table.column>
                <flux:table.column>Attempts</flux:table.column>
                <flux:table.column class="text-right">Actions</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($sessions as $session)
                    <flux:table.row>
                        <flux:table.cell>
                            <div class="font-medium text-zinc-900 dark:text-white">{{ $session->title }}</div>
                            <div class="text-xs text-zinc-500 font-mono mt-0.5">
                                Code: <span class="font-bold text-zinc-700 dark:text-zinc-300">{{ $session->access_code ?? 'None' }}</span>
                            </div>
                        </flux:table.cell>
                        <flux:table.cell>
                            @if ($session->is_active)
                                <flux:badge size="sm" color="success">Active</flux:badge>
                            @else
                                <flux:badge size="sm" color="zinc">Inactive</flux:badge>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex flex-wrap gap-1">
                                @forelse ($session->sections as $sec)
                                    <flux:badge size="sm" color="info">{{ $sec->name }}</flux:badge>
                                @empty
                                    <span class="text-xs text-zinc-400">No sections</span>
                                @endforelse
                            </div>
                        </flux:table.cell>
                        <flux:table.cell class="text-xs text-zinc-600 dark:text-zinc-400">
                            @if ($session->starts_at)
                                <div>Start: {{ $session->starts_at->format('d M Y, H:i') }}</div>
                            @endif
                            @if ($session->ends_at)
                                <div>End: {{ $session->ends_at->format('d M Y, H:i') }}</div>
                            @endif
                            @if (!$session->starts_at && !$session->ends_at)
                                <span class="text-zinc-400">Always open</span>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <span class="font-semibold text-zinc-700 dark:text-zinc-300">{{ $session->exam_attempts_count }}</span>
                        </flux:table.cell>
                        <flux:table.cell class="text-right">
                            <div class="flex items-center justify-end gap-2">
                                <flux:button href="{{ route('admin.test-sessions.edit', $session) }}" size="sm" variant="ghost" icon="pencil" />
                                <form method="POST" action="{{ route('admin.test-sessions.destroy', $session) }}" onsubmit="return confirm('Are you sure you want to delete this test session?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <flux:button type="submit" size="sm" variant="danger" icon="trash" />
                                </form>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6" class="text-center py-8 text-zinc-500">
                            No test sessions found.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        <div class="pt-4">
            {{ $sessions->links() }}
        </div>
    </flux:card>
</div>
@endsection

