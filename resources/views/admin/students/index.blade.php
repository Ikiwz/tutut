@extends('layouts.admin', ['title' => 'Student Verification'])

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <flux:heading size="xl" level="1">Student Academic Verification</flux:heading>
            <flux:text class="mt-1">Manage registered student identities, student cards, and official verification statuses.</flux:text>
        </div>
    </div>

    <flux:card class="space-y-4">
        <form method="GET" action="{{ route('admin.students.index') }}" class="flex flex-col sm:flex-row items-center gap-3">
            <div class="w-full sm:w-48">
                <flux:select name="status">
                    <option value="">All Verification</option>
                    <option value="verified" {{ request('status') === 'verified' ? 'selected' : '' }}>Verified Only</option>
                    <option value="unverified" {{ request('status') === 'unverified' ? 'selected' : '' }}>Unverified Only</option>
                </flux:select>
            </div>
            <div class="flex-1 w-full">
                <flux:input name="search" value="{{ request('search') }}" placeholder="Search by student name, ID (NIM), department, email..." icon="magnifying-glass" clearable />
            </div>
            <div class="flex items-center gap-2">
                <flux:button type="submit" variant="filled">Filter</flux:button>
                @if(request('search') || request('status'))
                    <flux:button href="{{ route('admin.students.index') }}" variant="ghost">Reset</flux:button>
                @endif
            </div>
        </form>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>Candidate</flux:table.column>
                <flux:table.column>Student ID (NIM)</flux:table.column>
                <flux:table.column>Department</flux:table.column>
                <flux:table.column>Student Card</flux:table.column>
                <flux:table.column>Verification</flux:table.column>
                <flux:table.column>Attempts</flux:table.column>
                <flux:table.column class="text-right">Actions</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($students as $student)
                    <flux:table.row>
                        <flux:table.cell>
                            <div class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $student->name }}</div>
                            <div class="text-xs text-zinc-500">{{ $student->email }}</div>
                        </flux:table.cell>
                        <flux:table.cell>
                            <span class="font-mono text-zinc-800 dark:text-zinc-200 font-bold text-xs">
                                {{ $student->student_id ?? 'None' }}
                            </span>
                        </flux:table.cell>
                        <flux:table.cell>
                            <span class="text-zinc-700 dark:text-zinc-300 text-xs">
                                {{ $student->department ?? 'General' }}
                            </span>
                        </flux:table.cell>
                        <flux:table.cell>
                            @if ($student->student_card)
                                <a href="{{ asset('storage/' . $student->student_card) }}" target="_blank" class="text-blue-600 dark:text-blue-400 text-xs hover:underline flex items-center gap-1">
                                    <flux:icon icon="document-text" class="size-4" />
                                    <span>View Card</span>
                                </a>
                            @else
                                <span class="text-xs text-zinc-400">Not uploaded</span>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            @if ($student->verified_at)
                                <flux:badge size="sm" color="success">Verified</flux:badge>
                                <div class="text-[10px] text-zinc-400 mt-0.5">{{ $student->verified_at->format('d/m/Y') }}</div>
                            @else
                                <flux:badge size="sm" color="zinc">Pending</flux:badge>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <span class="font-semibold text-zinc-800 dark:text-zinc-200">{{ $student->exam_attempts_count }}</span>
                        </flux:table.cell>
                        <flux:table.cell class="text-right">
                            <div class="flex items-center justify-end gap-2">
                                <form method="POST" action="{{ route('admin.students.verify', $student) }}" class="inline">
                                    @csrf
                                    @if ($student->verified_at)
                                        <flux:button type="submit" size="sm" variant="ghost">
                                            Revoke
                                        </flux:button>
                                    @else
                                        <flux:button type="submit" size="sm" variant="primary">
                                            Verify
                                        </flux:button>
                                    @endif
                                </form>

                                <form method="POST" action="{{ route('admin.students.destroy', $student) }}" onsubmit="return confirm('Delete this student account?');" class="inline">
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
                            No students registered yet.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        <div class="pt-4">
            {{ $students->links() }}
        </div>
    </flux:card>
</div>
@endsection

