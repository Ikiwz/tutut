@extends('layouts.admin', ['title' => 'Edit Direction'])

@section('content')
<div class="max-w-3xl space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl" level="1">Edit Exam Direction</flux:heading>
            <flux:text class="mt-1">Update part guidance and audio instructions.</flux:text>
        </div>
        <flux:button href="{{ route('admin.directions.index') }}" variant="ghost" icon="arrow-left">Back</flux:button>
    </div>

    <flux:card>
        <form method="POST" action="{{ route('admin.directions.update', $direction) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Section *</flux:label>
                    <flux:select name="section_id" required>
                        @foreach ($sections as $sec)
                            <option value="{{ $sec->id }}" {{ old('section_id', $direction->section_id) == $sec->id ? 'selected' : '' }}>
                                {{ $sec->name }}
                            </option>
                        @endforeach
                    </flux:select>
                    <flux:error name="section_id" />
                </flux:field>

                <flux:field>
                    <flux:label>Display Order *</flux:label>
                    <flux:input type="number" name="order" value="{{ old('order', $direction->order) }}" required min="0" />
                    <flux:error name="order" />
                </flux:field>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Label *</flux:label>
                    <flux:input name="label" value="{{ old('label', $direction->label) }}" required />
                    <flux:error name="label" />
                </flux:field>

                <flux:field>
                    <flux:label>Title *</flux:label>
                    <flux:input name="title" value="{{ old('title', $direction->title) }}" required />
                    <flux:error name="title" />
                </flux:field>
            </div>

            <flux:field>
                <flux:label>Instructions Content / Description</flux:label>
                <flux:textarea name="description" rows="5">{{ old('description', $direction->description) }}</flux:textarea>
                <flux:error name="description" />
            </flux:field>

            <flux:field>
                <flux:label>Directions Audio File</flux:label>
                @if ($direction->audio_path)
                    <div class="mb-2 flex items-center gap-2 text-xs text-emerald-600 dark:text-emerald-400">
                        <flux:icon icon="check-circle" class="size-4" />
                        <span>Current audio file: {{ basename($direction->audio_path) }}</span>
                    </div>
                @endif
                <input type="file" name="audio_path" accept="audio/*" class="block w-full text-sm text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-zinc-800 dark:file:text-zinc-200">
                <flux:error name="audio_path" />
            </flux:field>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                <flux:button href="{{ route('admin.directions.index') }}" variant="ghost">Cancel</flux:button>
                <flux:button type="submit" variant="primary">Save Changes</flux:button>
            </div>
        </form>
    </flux:card>
</div>
@endsection

