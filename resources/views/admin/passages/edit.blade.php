@extends('layouts.admin', ['title' => 'Edit Passage'])

@section('content')
<div class="max-w-3xl space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl" level="1">Edit Reading Passage</flux:heading>
            <flux:text class="mt-1">Update reading text or attached narration.</flux:text>
        </div>
        <flux:button href="{{ route('admin.passages.index') }}" variant="ghost" icon="arrow-left">Back</flux:button>
    </div>

    <flux:card>
        <form method="POST" action="{{ route('admin.passages.update', $passage) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Section *</flux:label>
                    <flux:select name="section_id" required>
                        @foreach ($sections as $sec)
                            <option value="{{ $sec->id }}" {{ old('section_id', $passage->section_id) == $sec->id ? 'selected' : '' }}>
                                {{ $sec->name }}
                            </option>
                        @endforeach
                    </flux:select>
                    <flux:error name="section_id" />
                </flux:field>

                <flux:field>
                    <flux:label>Display Order *</flux:label>
                    <flux:input type="number" name="order" value="{{ old('order', $passage->order) }}" required min="0" />
                    <flux:error name="order" />
                </flux:field>
            </div>

            <flux:field>
                <flux:label>Passage Title *</flux:label>
                <flux:input name="title" value="{{ old('title', $passage->title) }}" required />
                <flux:error name="title" />
            </flux:field>

            <flux:field>
                <flux:label>Passage Content / Text *</flux:label>
                <flux:textarea name="content" rows="8">{{ old('content', $passage->content) }}</flux:textarea>
                <flux:error name="content" />
            </flux:field>

            <flux:field>
                <flux:label>Passage Audio (Optional Narration)</flux:label>
                @if ($passage->audio_path)
                    <div class="mb-2 flex items-center gap-2 text-xs text-emerald-600 dark:text-emerald-400">
                        <flux:icon icon="check-circle" class="size-4" />
                        <span>Current audio file: {{ basename($passage->audio_path) }}</span>
                    </div>
                @endif
                <input type="file" name="audio_path" accept="audio/*" class="block w-full text-sm text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-zinc-800 dark:file:text-zinc-200">
                <flux:error name="audio_path" />
            </flux:field>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                <flux:button href="{{ route('admin.passages.index') }}" variant="ghost">Cancel</flux:button>
                <flux:button type="submit" variant="primary">Save Changes</flux:button>
            </div>
        </form>
    </flux:card>
</div>
@endsection

