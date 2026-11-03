@extends('layouts.admin', ['title' => 'Edit Question'])

@section('content')
<div class="max-w-4xl space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl" level="1">Edit Question</flux:heading>
            <flux:text class="mt-1">Update question text, media, and vaulted key.</flux:text>
        </div>
        <flux:button href="{{ route('admin.questions.index') }}" variant="ghost" icon="arrow-left">Back</flux:button>
    </div>

    <form method="POST" action="{{ route('admin.questions.update', $question) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Question Classification & Context -->
        <flux:card class="space-y-4">
            <flux:heading size="lg">Context & Hierarchy</flux:heading>
            <flux:separator />

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <flux:field>
                    <flux:label>Section *</flux:label>
                    <flux:select name="section_id" required>
                        @foreach ($sections as $sec)
                            <option value="{{ $sec->id }}" {{ old('section_id', $question->section_id) == $sec->id ? 'selected' : '' }}>
                                {{ $sec->name }}
                            </option>
                        @endforeach
                    </flux:select>
                    <flux:error name="section_id" />
                </flux:field>

                <flux:field>
                    <flux:label>Direction / Part</flux:label>
                    <flux:select name="direction_id">
                        <option value="">None / Optional</option>
                        @foreach ($directions as $dir)
                            <option value="{{ $dir->id }}" {{ old('direction_id', $question->direction_id) == $dir->id ? 'selected' : '' }}>
                                [{{ $dir->section?->name }}] {{ $dir->label }} - {{ $dir->title }}
                            </option>
                        @endforeach
                    </flux:select>
                    <flux:error name="direction_id" />
                </flux:field>

                <flux:field>
                    <flux:label>Reading Passage</flux:label>
                    <flux:select name="passage_id">
                        <option value="">None / Optional</option>
                        @foreach ($passages as $pas)
                            <option value="{{ $pas->id }}" {{ old('passage_id', $question->passage_id) == $pas->id ? 'selected' : '' }}>
                                {{ $pas->title }}
                            </option>
                        @endforeach
                    </flux:select>
                    <flux:error name="passage_id" />
                </flux:field>
            </div>

            <flux:field>
                <flux:label>Question Text</flux:label>
                <flux:textarea name="question_text" rows="3">{{ old('question_text', $question->question_text) }}</flux:textarea>
                <flux:error name="question_text" />
            </flux:field>

            <flux:field>
                <flux:label>Question Audio File</flux:label>
                @if ($question->audio_path)
                    <div class="mb-2 flex items-center gap-2 text-xs text-emerald-600 dark:text-emerald-400">
                        <flux:icon icon="check-circle" class="size-4" />
                        <span>Current audio file: {{ basename($question->audio_path) }}</span>
                    </div>
                @endif
                <input type="file" name="audio_path" accept="audio/*" class="block w-full text-sm text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-zinc-800 dark:file:text-zinc-200">
                <flux:error name="audio_path" />
            </flux:field>
        </flux:card>

        <!-- Answer Options & Key Vault -->
        <flux:card class="space-y-4">
            <flux:heading size="lg">Options & Key Vault</flux:heading>
            <flux:separator />

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Option A *</flux:label>
                    <flux:textarea name="option_a" rows="2" required>{{ old('option_a', $question->option_a) }}</flux:textarea>
                    <input type="file" name="option_a_audio" accept="audio/*" class="mt-2 block w-full text-xs text-zinc-500 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:bg-zinc-100 dark:file:bg-zinc-800">
                    <flux:error name="option_a" />
                </flux:field>

                <flux:field>
                    <flux:label>Option B *</flux:label>
                    <flux:textarea name="option_b" rows="2" required>{{ old('option_b', $question->option_b) }}</flux:textarea>
                    <input type="file" name="option_b_audio" accept="audio/*" class="mt-2 block w-full text-xs text-zinc-500 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:bg-zinc-100 dark:file:bg-zinc-800">
                    <flux:error name="option_b" />
                </flux:field>

                <flux:field>
                    <flux:label>Option C *</flux:label>
                    <flux:textarea name="option_c" rows="2" required>{{ old('option_c', $question->option_c) }}</flux:textarea>
                    <input type="file" name="option_c_audio" accept="audio/*" class="mt-2 block w-full text-xs text-zinc-500 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:bg-zinc-100 dark:file:bg-zinc-800">
                    <flux:error name="option_c" />
                </flux:field>

                <flux:field>
                    <flux:label>Option D *</flux:label>
                    <flux:textarea name="option_d" rows="2" required>{{ old('option_d', $question->option_d) }}</flux:textarea>
                    <input type="file" name="option_d_audio" accept="audio/*" class="mt-2 block w-full text-xs text-zinc-500 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:bg-zinc-100 dark:file:bg-zinc-800">
                    <flux:error name="option_d" />
                </flux:field>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                @php $currentKey = $question->examKey?->correct_key ?? $question->correct_answer; @endphp
                <flux:field>
                    <flux:label>Correct Answer Key * (Vaulted)</flux:label>
                    <flux:select name="correct_answer" required>
                        <option value="A" {{ old('correct_answer', $currentKey) == 'A' ? 'selected' : '' }}>Option A</option>
                        <option value="B" {{ old('correct_answer', $currentKey) == 'B' ? 'selected' : '' }}>Option B</option>
                        <option value="C" {{ old('correct_answer', $currentKey) == 'C' ? 'selected' : '' }}>Option C</option>
                        <option value="D" {{ old('correct_answer', $currentKey) == 'D' ? 'selected' : '' }}>Option D</option>
                    </flux:select>
                    <flux:error name="correct_answer" />
                </flux:field>

                <flux:field>
                    <flux:label>Order Index *</flux:label>
                    <flux:input type="number" name="order" value="{{ old('order', $question->order) }}" required min="0" />
                    <flux:error name="order" />
                </flux:field>
            </div>
        </flux:card>

        <div class="flex items-center justify-end gap-3">
            <flux:button href="{{ route('admin.questions.index') }}" variant="ghost">Cancel</flux:button>
            <flux:button type="submit" variant="primary">Update Question</flux:button>
        </div>
    </form>
</div>
@endsection

