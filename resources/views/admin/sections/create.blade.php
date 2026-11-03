@extends('layouts.admin', ['title' => 'Create Section'])

@section('content')
<div class="max-w-2xl space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl" level="1">Create Exam Section</flux:heading>
            <flux:text class="mt-1">Define an official section of the TOEFL exam.</flux:text>
        </div>
        <flux:button href="{{ route('admin.sections.index') }}" variant="ghost" icon="arrow-left">Back</flux:button>
    </div>

    <flux:card>
        <form method="POST" action="{{ route('admin.sections.store') }}" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Section Name</flux:label>
                    <flux:input name="name" value="{{ old('name') }}" required placeholder="e.g. Listening Comprehension" />
                    <flux:error name="name" />
                </flux:field>

                <flux:field>
                    <flux:label>Slug Identifier</flux:label>
                    <flux:input name="slug" value="{{ old('slug') }}" required placeholder="e.g. listening" />
                    <flux:error name="slug" />
                </flux:field>
            </div>

            <flux:field>
                <flux:label>Description</flux:label>
                <flux:textarea name="description" rows="3">{{ old('description') }}</flux:textarea>
                <flux:error name="description" />
            </flux:field>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Duration (Minutes)</flux:label>
                    <flux:input type="number" name="duration_minutes" value="{{ old('duration_minutes', 35) }}" required min="1" />
                    <flux:error name="duration_minutes" />
                </flux:field>

                <flux:field>
                    <flux:label>Display Order</flux:label>
                    <flux:input type="number" name="order" value="{{ old('order', 1) }}" required min="0" />
                    <flux:error name="order" />
                </flux:field>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                <flux:button href="{{ route('admin.sections.index') }}" variant="ghost">Cancel</flux:button>
                <flux:button type="submit" variant="primary">Create Section</flux:button>
            </div>
        </form>
    </flux:card>
</div>
@endsection

