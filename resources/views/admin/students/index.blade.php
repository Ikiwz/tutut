@extends('layouts.admin', ['title' => 'Student Academic Verification'])

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <flux:heading size="xl" level="1">Student Academic Verification</flux:heading>
            <flux:text class="mt-1">Manage registered candidates, university affiliation claims, student cards (KTM), and complimentary attempt eligibility.</flux:text>
        </div>
    </div>

    <!-- Stat Metric Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <flux:card class="p-4 space-y-1">
            <flux:text size="xs" class="font-medium text-zinc-500">Total Candidates</flux:text>
            <div class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $stats['total'] }}</div>
        </flux:card>
        <flux:card class="p-4 space-y-1">
            <flux:text size="xs" class="font-medium text-emerald-600 dark:text-emerald-400">Internal (UMMI)</flux:text>
            <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $stats['internal'] }}</div>
        </flux:card>
        <flux:card class="p-4 space-y-1">
            <flux:text size="xs" class="font-medium text-zinc-500">External</flux:text>
            <div class="text-2xl font-bold text-zinc-700 dark:text-zinc-300">{{ $stats['external'] }}</div>
        </flux:card>
        <flux:card class="p-4 space-y-1">
            <flux:text size="xs" class="font-medium text-amber-600 dark:text-amber-400">Pending Claims</flux:text>
            <div class="text-2xl font-bold text-amber-600 dark:text-amber-400">{{ $stats['pending_claims'] }}</div>
        </flux:card>
    </div>

    <flux:card class="space-y-4">
        <form method="GET" action="{{ route('admin.students.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div>
                <flux:select name="affiliation">
                    <option value="">All Affiliations</option>
                    <option value="internal" {{ request('affiliation') === 'internal' ? 'selected' : '' }}>Internal (UMMI)</option>
                    <option value="external" {{ request('affiliation') === 'external' ? 'selected' : '' }}>External</option>
                </flux:select>
            </div>
            <div>
                <flux:select name="status">
                    <option value="">All Verification</option>
                    <option value="verified" {{ request('status') === 'verified' ? 'selected' : '' }}>Verified Only</option>
                    <option value="unverified" {{ request('status') === 'unverified' ? 'selected' : '' }}>Unverified Only</option>
                </flux:select>
            </div>
            <div>
                <flux:select name="claim_status">
                    <option value="">All Claims</option>
                    <option value="pending" {{ request('claim_status') === 'pending' ? 'selected' : '' }}>Pending Review</option>
                    <option value="approved" {{ request('claim_status') === 'approved' ? 'selected' : '' }}>Approved Claims</option>
                    <option value="rejected" {{ request('claim_status') === 'rejected' ? 'selected' : '' }}>Rejected Claims</option>
                </flux:select>
            </div>
            <div class="flex items-center gap-2">
                <flux:input name="search" value="{{ request('search') }}" placeholder="Search candidate..." icon="magnifying-glass" clearable class="flex-1" />
                <flux:button type="submit" variant="filled">Filter</flux:button>
                @if(request('search') || request('status') || request('affiliation') || request('claim_status'))
                    <flux:button href="{{ route('admin.students.index') }}" variant="ghost">Reset</flux:button>
                @endif
            </div>
        </form>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>Candidate</flux:table.column>
                <flux:table.column>Affiliation</flux:table.column>
                <flux:table.column>Student ID (NIM)</flux:table.column>
                <flux:table.column>Department</flux:table.column>
                <flux:table.column>Verification Doc</flux:table.column>
                <flux:table.column>Claim Status</flux:table.column>
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
                            @if ($student->isInternal())
                                <flux:badge size="sm" color="lime" icon="academic-cap">Internal</flux:badge>
                            @else
                                <flux:badge size="sm" color="zinc" icon="user">External</flux:badge>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <span class="font-mono text-zinc-800 dark:text-zinc-200 font-bold text-xs">
                                {{ $student->student_id ?? '—' }}
                            </span>
                        </flux:table.cell>
                        <flux:table.cell>
                            <span class="text-zinc-700 dark:text-zinc-300 text-xs">
                                {{ $student->department ?? '—' }}
                            </span>
                        </flux:table.cell>
                        <flux:table.cell>
                            @if ($student->student_card)
                                <a href="{{ route('admin.students.document', $student) }}" target="_blank" class="text-blue-600 dark:text-blue-400 text-xs hover:underline flex items-center gap-1 font-medium">
                                    <flux:icon icon="document-text" class="size-4" />
                                    <span>View KTM/Doc</span>
                                </a>
                            @else
                                <span class="text-xs text-zinc-400">None</span>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            @if ($student->campus_claim_status === 'pending')
                                <flux:badge size="sm" color="warning" icon="clock">Pending Claim</flux:badge>
                            @elseif ($student->campus_claim_status === 'approved')
                                <flux:badge size="sm" color="success" icon="check-circle">Approved</flux:badge>
                            @elseif ($student->campus_claim_status === 'rejected')
                                <flux:badge size="sm" color="danger" icon="x-circle">Rejected</flux:badge>
                            @elseif ($student->verified_at)
                                <flux:badge size="sm" color="success">Auto-Verified</flux:badge>
                            @else
                                <flux:badge size="sm" color="zinc">Unverified</flux:badge>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <span class="font-semibold text-zinc-800 dark:text-zinc-200">{{ $student->exam_attempts_count }}</span>
                        </flux:table.cell>
                        <flux:table.cell class="text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                @if ($student->campus_claim_status === 'pending')
                                    <form method="POST" action="{{ route('admin.students.approve-claim', $student) }}" class="inline">
                                        @csrf
                                        <flux:button type="submit" size="xs" variant="primary" icon="check">
                                            Approve
                                        </flux:button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.students.reject-claim', $student) }}" class="inline">
                                        @csrf
                                        <flux:button type="submit" size="xs" variant="ghost" class="text-rose-600 hover:text-rose-700">
                                            Reject
                                        </flux:button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('admin.students.verify', $student) }}" class="inline">
                                        @csrf
                                        @if ($student->verified_at)
                                            <flux:button type="submit" size="xs" variant="ghost">Revoke</flux:button>
                                        @else
                                            <flux:button type="submit" size="xs" variant="filled">Verify</flux:button>
                                        @endif
                                    </form>
                                @endif

                                <form method="POST" action="{{ route('admin.students.destroy', $student) }}" onsubmit="return confirm('Delete this candidate account?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <flux:button type="submit" size="xs" variant="danger" icon="trash" />
                                </form>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="8" class="text-center py-8 text-zinc-500">
                            No candidates found matching the criteria.
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
