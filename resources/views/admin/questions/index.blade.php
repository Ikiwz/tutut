@extends('layouts.admin', ['title' => 'Questions Vault'])

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <flux:heading size="xl" level="1">Questions Vault</flux:heading>
            <flux:text class="mt-1">Manage all TOEFL exam questions, audio narrations, and secure answer keys.</flux:text>
        </div>
        <flux:button href="{{ route('admin.questions.create') }}" variant="primary" icon="plus">
            Create Question
        </flux:button>
    </div>

    <flux:card class="space-y-4">
        <form method="GET" action="{{ route('admin.questions.index') }}" class="flex flex-col sm:flex-row items-center gap-3">
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
                <flux:input name="search" value="{{ request('search') }}" placeholder="Search question text..." icon="magnifying-glass" clearable />
            </div>
            <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                <flux:button type="submit" variant="filled">Filter</flux:button>
                @if(request('search') || request('section_id'))
                    <flux:button href="{{ route('admin.questions.index') }}" variant="ghost">Reset</flux:button>
                @endif
            </div>
        </form>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>#</flux:table.column>
                <flux:table.column>Section / Direction</flux:table.column>
                <flux:table.column>Question Preview</flux:table.column>
                <flux:table.column>Audio</flux:table.column>
                <flux:table.column>Key Vault</flux:table.column>
                <flux:table.column class="text-right">Actions</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($questions as $question)
                    <flux:table.row>
                        <flux:table.cell>
                            <span class="font-mono text-zinc-400 font-bold">#{{ $question->order }}</span>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div>
                                <flux:badge size="sm" color="info">{{ $question->section?->name ?? 'N/A' }}</flux:badge>
                                @if($question->direction)
                                    <div class="text-xs text-zinc-500 mt-1 font-medium">{{ $question->direction->label }}</div>
                                @endif
                            </div>
                        </flux:table.cell>
                        <flux:table.cell class="max-w-md">
                            <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100 line-clamp-2">
                                {{ $question->question_text ?: '(Audio-only Listening Question)' }}
                            </div>
                            <div class="text-xs text-zinc-500 mt-1 flex gap-2">
                                <span>A: {{ Str::limit($question->option_a, 20) }}</span>
                                <span>B: {{ Str::limit($question->option_b, 20) }}</span>
                            </div>
                        </flux:table.cell>
                        <flux:table.cell>
                            @if ($question->audio_path)
                                <flux:badge size="sm" color="success" icon="speaker-wave">Has Audio</flux:badge>
                            @else
                                <span class="text-xs text-zinc-400">None</span>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            @php $key = $question->examKey?->correct_key ?? $question->correct_answer; @endphp
                            <flux:badge size="sm" color="emerald" class="font-bold font-mono">
                                {{ $key }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell class="text-right">
                            <div class="flex items-center justify-end gap-2">
                                <flux:button href="{{ route('admin.questions.edit', $question) }}" size="sm" variant="ghost" icon="pencil" />
                                <form method="POST" action="{{ route('admin.questions.destroy', $question) }}" onsubmit="return confirm('Delete this question?');" class="inline">
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
                            No questions found.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        <div class="pt-4">
            {{ $questions->links() }}
        </div>
    </flux:card>
</div>
@endsection

