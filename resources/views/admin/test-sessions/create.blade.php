@extends('layouts.admin', ['title' => 'Create Test Session'])

@section('content')
<div class="max-w-3xl space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl" level="1">Create Test Session</flux:heading>
            <flux:text class="mt-1">Define an official exam event with schedule and proctor access code.</flux:text>
        </div>
        <flux:button href="{{ route('admin.test-sessions.index') }}" variant="ghost" icon="arrow-left">Back</flux:button>
    </div>

    <flux:card>
        <form method="POST" action="{{ route('admin.test-sessions.store') }}" class="space-y-6">
            @csrf

            <flux:field>
                <flux:label>Session Title</flux:label>
                <flux:input name="title" value="{{ old('title') }}" required placeholder="e.g. Official Campus TOEFL Batch 1" />
                <flux:error name="title" />
            </flux:field>

            <flux:field>
                <flux:label>Description</flux:label>
                <flux:textarea name="description" rows="3" placeholder="Additional instructions or guidelines for candidates...">{{ old('description') }}</flux:textarea>
                <flux:error name="description" />
            </flux:field>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Access Code (Proctor Passcode)</flux:label>
                    <flux:input name="access_code" value="{{ old('access_code') }}" placeholder="e.g. TOEFL-2026" />
                    <flux:description>Required by candidates to unlock this exam.</flux:description>
                    <flux:error name="access_code" />
                </flux:field>

                <div class="flex flex-col justify-center pt-2">
                    <flux:switch name="is_active" value="1" :checked="old('is_active', true)" label="Session Active" description="Toggle whether this session is open in the portal." />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Start Date & Time</flux:label>
                    <flux:input type="datetime-local" name="starts_at" value="{{ old('starts_at') }}" />
                    <flux:error name="starts_at" />
                </flux:field>

                <flux:field>
                    <flux:label>End Date & Time</flux:label>
                    <flux:input type="datetime-local" name="ends_at" value="{{ old('ends_at') }}" />
                    <flux:error name="ends_at" />
                </flux:field>
            </div>

            <flux:field>
                <flux:label>Included Test Sections</flux:label>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-2">
                    @foreach ($sections as $section)
                        <label class="flex items-center gap-2 p-3 rounded-lg border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-800 cursor-pointer">
                            <input type="checkbox" name="sections[]" value="{{ $section->id }}" class="rounded text-blue-600 focus:ring-blue-500" {{ in_array($section->id, old('sections', [])) ? 'checked' : '' }}>
                            <div>
                                <div class="text-sm font-medium text-zinc-900 dark:text-white">{{ $section->name }}</div>
                                <div class="text-xs text-zinc-500">{{ $section->duration_minutes }} mins</div>
                            </div>
                        </label>
                    @endforeach
                </div>
                <flux:error name="sections" />
            </flux:field>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                <flux:button href="{{ route('admin.test-sessions.index') }}" variant="ghost">Cancel</flux:button>
                <flux:button type="submit" variant="primary">Create Test Session</flux:button>
            </div>
        </form>
    </flux:card>
</div>
@endsection

