@extends('layouts.admin', ['title' => 'Sections'])

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <flux:heading size="xl" level="1">Exam Sections</flux:heading>
            <flux:text class="mt-1">Configure sections (Listening, Structure, Reading) and durations.</flux:text>
        </div>
        <flux:button href="{{ route('admin.sections.create') }}" variant="primary" icon="plus">
            Create Section
        </flux:button>
    </div>

    <flux:card>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Order</flux:table.column>
                <flux:table.column>Name</flux:table.column>
                <flux:table.column>Slug</flux:table.column>
                <flux:table.column>Duration</flux:table.column>
                <flux:table.column>Directions</flux:table.column>
                <flux:table.column>Passages</flux:table.column>
                <flux:table.column>Questions</flux:table.column>
                <flux:table.column class="text-right">Actions</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($sections as $section)
                    <flux:table.row>
                        <flux:table.cell>
                            <span class="font-mono font-bold text-zinc-500">#{{ $section->order }}</span>
                        </flux:table.cell>
                        <flux:table.cell>
                            <span class="font-semibold text-zinc-900 dark:text-white">{{ $section->name }}</span>
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge size="sm" color="zinc">{{ $section->slug }}</flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            <span class="text-zinc-700 dark:text-zinc-300 font-medium">{{ $section->duration_minutes }} mins</span>
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge size="sm" color="info">{{ $section->directions_count }} directions</flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge size="sm" color="zinc">{{ $section->passages_count }} passages</flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            <span class="font-bold text-blue-600 dark:text-blue-400">{{ $section->questions_count }}</span>
                        </flux:table.cell>
                        <flux:table.cell class="text-right">
                            <div class="flex items-center justify-end gap-2">
                                <flux:button href="{{ route('admin.sections.edit', $section) }}" size="sm" variant="ghost" icon="pencil" />
                                <form method="POST" action="{{ route('admin.sections.destroy', $section) }}" onsubmit="return confirm('Delete this section?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <flux:button type="submit" size="sm" variant="danger" icon="trash" />
                                </form>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="8" class="text-center py-8 text-zinc-500">
                            No sections configured yet.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </flux:card>
</div>
@endsection

