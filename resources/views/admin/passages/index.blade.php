@extends('layouts.admin', ['title' => 'Reading Passages'])

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <flux:heading size="xl" level="1">Reading Passages</flux:heading>
            <flux:text class="mt-1">Manage reading texts and corresponding audio narrations.</flux:text>
        </div>
        <flux:button href="{{ route('admin.passages.create') }}" variant="primary" icon="plus">
            Create Passage
        </flux:button>
    </div>

    <flux:card class="space-y-4">
        <form method="GET" action="{{ route('admin.passages.index') }}" class="flex flex-col sm:flex-row items-center gap-3">
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
                <flux:input name="search" value="{{ request('search') }}" placeholder="Search passage title..." icon="magnifying-glass" clearable />
            </div>
            <div class="flex items-center gap-2">
                <flux:button type="submit" variant="filled">Filter</flux:button>
                @if(request('search') || request('section_id'))
                    <flux:button href="{{ route('admin.passages.index') }}" variant="ghost">Reset</flux:button>
                @endif
            </div>
        </form>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>#</flux:table.column>
                <flux:table.column>Section</flux:table.column>
                <flux:table.column>Title</flux:table.column>
                <flux:table.column>Audio</flux:table.column>
                <flux:table.column>Questions</flux:table.column>
                <flux:table.column class="text-right">Actions</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($passages as $passage)
                    <flux:table.row>
                        <flux:table.cell>
                            <span class="font-mono text-zinc-400 font-bold">#{{ $passage->order }}</span>
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge size="sm" color="info">{{ $passage->section?->name ?? 'N/A' }}</flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $passage->title }}</div>
                            <div class="text-xs text-zinc-500 line-clamp-1 mt-0.5">{{ Str::limit(strip_tags($passage->content), 60) }}</div>
                        </flux:table.cell>
                        <flux:table.cell>
                            @if ($passage->audio_path)
                                <flux:badge size="sm" color="success" icon="speaker-wave">Audio</flux:badge>
                            @else
                                <span class="text-xs text-zinc-400">None</span>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge size="sm" color="zinc">{{ $passage->questions_count }} questions</flux:badge>
                        </flux:table.cell>
                        <flux:table.cell class="text-right">
                            <div class="flex items-center justify-end gap-2">
                                <flux:button href="{{ route('admin.passages.edit', $passage) }}" size="sm" variant="ghost" icon="pencil" />
                                <form method="POST" action="{{ route('admin.passages.destroy', $passage) }}" onsubmit="return confirm('Delete this passage?');" class="inline">
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
                            No reading passages found.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        <div class="pt-4">
            {{ $passages->links() }}
        </div>
    </flux:card>
</div>
@endsection

