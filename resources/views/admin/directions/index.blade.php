@extends('layouts.admin', ['title' => 'Exam Directions'])

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <flux:heading size="xl" level="1">Exam Directions & Instructions</flux:heading>
            <flux:text class="mt-1">Manage instructions and audio guidance for test parts (Part A, Part B, Part C).</flux:text>
        </div>
        <flux:button href="{{ route('admin.directions.create') }}" variant="primary" icon="plus">
            Create Direction
        </flux:button>
    </div>

    <flux:card class="space-y-4">
        <form method="GET" action="{{ route('admin.directions.index') }}" class="flex flex-col sm:flex-row items-center gap-3">
            <div class="w-full sm:w-64">
                <flux:select name="section_id" placeholder="All Sections">
                    <option value="">All Sections</option>
                    @foreach ($sections as $sec)
                        <option value="{{ $sec->id }}" {{ request('section_id') == $sec->id ? 'selected' : '' }}>
                            {{ $sec->name }}
                        </option>
                    @endforeach
                </flux:select>
            </div>
            <div class="flex-1 w-full">
                <flux:input name="search" value="{{ request('search') }}" placeholder="Search direction label or title..." icon="magnifying-glass" clearable />
            </div>
            <div class="flex items-center gap-2">
                <flux:button type="submit" variant="filled">Filter</flux:button>
                @if(request('search') || request('section_id'))
                    <flux:button href="{{ route('admin.directions.index') }}" variant="ghost">Reset</flux:button>
                @endif
            </div>
        </form>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>#</flux:table.column>
                <flux:table.column>Section</flux:table.column>
                <flux:table.column>Label</flux:table.column>
                <flux:table.column>Title</flux:table.column>
                <flux:table.column>Audio</flux:table.column>
                <flux:table.column>Questions</flux:table.column>
                <flux:table.column class="text-right">Actions</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($directions as $direction)
                    <flux:table.row>
                        <flux:table.cell>
                            <span class="font-mono text-zinc-400 font-bold">#{{ $direction->order }}</span>
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge size="sm" color="info">{{ $direction->section?->name ?? 'N/A' }}</flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            <span class="font-bold text-zinc-900 dark:text-zinc-100">{{ $direction->label }}</span>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="font-medium text-zinc-800 dark:text-zinc-200">{{ $direction->title }}</div>
                            <div class="text-xs text-zinc-500 line-clamp-1 mt-0.5">{{ Str::limit(strip_tags($direction->description), 50) }}</div>
                        </flux:table.cell>
                        <flux:table.cell>
                            @if ($direction->audio_path)
                                <flux:badge size="sm" color="success" icon="speaker-wave">Audio</flux:badge>
                            @else
                                <span class="text-xs text-zinc-400">None</span>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge size="sm" color="zinc">{{ $direction->questions_count }} questions</flux:badge>
                        </flux:table.cell>
                        <flux:table.cell class="text-right">
                            <div class="flex items-center justify-end gap-2">
                                <flux:button href="{{ route('admin.directions.edit', $direction) }}" size="sm" variant="ghost" icon="pencil" />
                                <form method="POST" action="{{ route('admin.directions.destroy', $direction) }}" onsubmit="return confirm('Delete this direction?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <flux:button type="submit" size="sm" variant="danger" icon="trash" />
                                </form>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="7" class="text-center py-8 text-zinc-500">
                            No directions found.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        <div class="pt-4">
            {{ $directions->links() }}
        </div>
    </flux:card>
</div>
@endsection

