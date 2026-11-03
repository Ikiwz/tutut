@extends('layouts.admin', ['title' => 'Payment Verification'])

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <flux:heading size="xl" level="1">Exam Payment Verification</flux:heading>
            <flux:text class="mt-1">Review candidate bank transfer receipts, approve examination registrations, and audit retake fees.</flux:text>
        </div>
    </div>

    <!-- Payment Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <flux:card class="p-4 space-y-1">
            <flux:text size="xs" class="font-medium text-amber-600 dark:text-amber-400">Pending Review</flux:text>
            <div class="text-2xl font-bold text-amber-600 dark:text-amber-400">{{ $stats['pending'] }}</div>
        </flux:card>
        <flux:card class="p-4 space-y-1">
            <flux:text size="xs" class="font-medium text-emerald-600 dark:text-emerald-400">Approved Payments</flux:text>
            <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $stats['approved'] }}</div>
        </flux:card>
        <flux:card class="p-4 space-y-1">
            <flux:text size="xs" class="font-medium text-zinc-500">Rejected</flux:text>
            <div class="text-2xl font-bold text-zinc-700 dark:text-zinc-300">{{ $stats['rejected'] }}</div>
        </flux:card>
    </div>

    <flux:card class="space-y-4">
        <form method="GET" action="{{ route('admin.payments.index') }}" class="flex flex-col sm:flex-row items-center gap-3">
            <div class="w-full sm:w-48">
                <flux:select name="status">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending Review</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </flux:select>
            </div>
            <div class="flex-1 w-full">
                <flux:input name="search" value="{{ request('search') }}" placeholder="Search by candidate name, email, NIM..." icon="magnifying-glass" clearable />
            </div>
            <div class="flex items-center gap-2">
                <flux:button type="submit" variant="filled">Filter</flux:button>
                @if(request('search') || request('status'))
                    <flux:button href="{{ route('admin.payments.index') }}" variant="ghost">Reset</flux:button>
                @endif
            </div>
        </form>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>Candidate</flux:table.column>
                <flux:table.column>Session</flux:table.column>
                <flux:table.column>Method & Amount</flux:table.column>
                <flux:table.column>Receipt Proof</flux:table.column>
                <flux:table.column>Submitted At</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column class="text-right">Actions</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($payments as $payment)
                    <flux:table.row>
                        <flux:table.cell>
                            <div class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $payment->user?->name ?? 'Unknown' }}</div>
                            <div class="text-xs text-zinc-500">{{ $payment->user?->email }}</div>
                            @if ($payment->user?->student_id)
                                <div class="text-[11px] font-mono text-zinc-400">NIM: {{ $payment->user->student_id }}</div>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <span class="text-xs font-medium text-zinc-800 dark:text-zinc-200">
                                {{ $payment->testSession?->title ?? 'General Exam Access' }}
                            </span>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="text-xs font-semibold text-zinc-900 dark:text-white">Rp {{ number_format($payment->amount, 0, ',', '.') }}</div>
                            <div class="text-[11px] text-zinc-500">{{ $payment->payment_method }}</div>
                        </flux:table.cell>
                        <flux:table.cell>
                            <a href="{{ route('admin.payments.proof', $payment) }}" target="_blank" class="text-blue-600 dark:text-blue-400 text-xs font-semibold hover:underline flex items-center gap-1">
                                <flux:icon icon="document-arrow-down" class="size-4" />
                                <span>View Receipt</span>
                            </a>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="text-xs text-zinc-600 dark:text-zinc-400">
                                {{ $payment->created_at->format('d M Y, H:i') }}
                            </div>
                        </flux:table.cell>
                        <flux:table.cell>
                            @if ($payment->isPending())
                                <flux:badge size="sm" color="warning" icon="clock">Pending</flux:badge>
                            @elseif ($payment->isApproved())
                                <flux:badge size="sm" color="success" icon="check-circle">Approved</flux:badge>
                                @if ($payment->verifiedBy)
                                    <div class="text-[10px] text-zinc-400 mt-0.5">by {{ $payment->verifiedBy->name }}</div>
                                @endif
                            @elseif ($payment->isRejected())
                                <flux:badge size="sm" color="danger" icon="x-circle">Rejected</flux:badge>
                                @if ($payment->rejection_reason)
                                    <div class="text-[10px] text-rose-500 max-w-xs truncate mt-0.5" title="{{ $payment->rejection_reason }}">{{ $payment->rejection_reason }}</div>
                                @endif
                            @endif
                        </flux:table.cell>
                        <flux:table.cell class="text-right">
                            @if ($payment->isPending())
                                <div class="flex items-center justify-end gap-1.5">
                                    <form method="POST" action="{{ route('admin.payments.approve', $payment) }}" class="inline">
                                        @csrf
                                        <flux:button type="submit" size="xs" variant="primary" icon="check">
                                            Approve
                                        </flux:button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.payments.reject', $payment) }}" class="inline" onsubmit="return prompt('Rejection reason (optional):') !== null;">
                                        @csrf
                                        <flux:button type="submit" size="xs" variant="ghost" class="text-rose-600 hover:text-rose-700">
                                            Reject
                                        </flux:button>
                                    </form>
                                </div>
                            @else
                                <span class="text-xs text-zinc-400 font-mono">Processed</span>
                            @endif
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="7" class="text-center py-8 text-zinc-500">
                            No payment receipts found matching the filter.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        <div class="pt-4">
            {{ $payments->links() }}
        </div>
    </flux:card>
</div>
@endsection

