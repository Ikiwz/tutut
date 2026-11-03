@extends('layouts.admin', ['title' => 'Create Passage'])

@section('content')
<div class="max-w-3xl space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl" level="1">Create Reading Passage</flux:heading>
            <flux:text class="mt-1">Add a passage text for Reading Comprehension.</flux:text>
        </div>
        <flux:button href="{{ route('admin.passages.index') }}" variant="ghost" icon="arrow-left">Back</flux:button>
    </div>

    <flux:card>
        <form method="POST" action="{{ route('admin.passages.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Section *</flux:label>
                    <flux:select name="section_id" required>
                        <option value="">Select Section</option>
                        @foreach ($sections as $sec)
                            <option value="{{ $sec->id }}" {{ old('section_id') == $sec->id ? 'selected' : '' }}>
                                {{ $sec->name }}
                            </option>
                        @endforeach
                    </flux:select>
                    <flux:error name="section_id" />
                </flux:field>

                <flux:field>
                    <flux:label>Display Order *</flux:label>
                    <flux:input type="number" name="order" value="{{ old('order', 1) }}" required min="0" />
                    <flux:error name="order" />
                </flux:field>
            </div>

            <flux:field>
                <flux:label>Passage Title *</flux:label>
                <flux:input name="title" value="{{ old('title') }}" required placeholder="e.g. Passage 1: The Solar System" />
                <flux:error name="title" />
            </flux:field>

            <flux:field>
                <flux:label>Passage Content / Text *</flux:label>
                <flux:textarea name="content" rows="8" placeholder="Type or paste the complete reading passage text here...">{{ old('content') }}</flux:textarea>
                <flux:error name="content" />
            </flux:field>

            <flux:field>
                <flux:label>Passage Audio (Optional Narration)</flux:label>
                <input type="file" name="audio_path" accept="audio/*" class="block w-full text-sm text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-zinc-800 dark:file:text-zinc-200">
                <flux:error name="audio_path" />
            </flux:field>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                <flux:button href="{{ route('admin.passages.index') }}" variant="ghost">Cancel</flux:button>
                <flux:button type="submit" variant="primary">Create Passage</flux:button>
            </div>
        </form>
    </flux:card>
</div>
@endsection

