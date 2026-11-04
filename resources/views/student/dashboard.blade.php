@extends('layouts.student', ['title' => 'Candidate Dashboard'])

@section('content')
<div class="space-y-8" x-data="{ claimModal: false, paymentModal: false, selectedSessionId: '' }">
    <!-- Welcome Banner Card with Candidate Entitlement Status -->
    <flux:card class="bg-gradient-to-r from-blue-600 via-indigo-600 to-indigo-700 dark:from-blue-700 dark:via-indigo-800 dark:to-indigo-900 text-white rounded-2xl p-6 sm:p-8 shadow-lg border-0">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="space-y-3">
                <div class="flex flex-wrap items-center gap-2">
                    <flux:heading size="xl" class="text-white font-extrabold text-2xl">
                        Welcome, {{ auth()->user()->name }}
                    </flux:heading>

                    @if (auth()->user()->isInternal())
                        <flux:badge size="sm" color="lime" icon="academic-cap" class="bg-lime-400 text-lime-950 font-bold">
                            Internal (Civitas UMMI)
                        </flux:badge>
                    @else
                        <flux:badge size="sm" color="zinc" icon="user" class="bg-white/20 text-white font-semibold border border-white/30">
                            External Candidate
                        </flux:badge>
                    @endif
                </div>

                <!-- Entitlement explanation banner -->
                <div class="flex flex-wrap items-center gap-2 text-xs">
                    @if (auth()->user()->hasFreeAttemptAvailable())
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-100 border border-emerald-400/30 font-medium">
                            <flux:icon icon="sparkles" class="size-3.5 text-emerald-300" />
                            1 Free University Attempt Available (Degree Requirement)
                        </span>
                    @elseif (auth()->user()->isInternal())
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-amber-500/20 text-amber-100 border border-amber-400/30 font-medium">
                            <flux:icon icon="information-circle" class="size-3.5 text-amber-300" />
                            Complimentary attempt used ({{ auth()->user()->completedAttemptsCount() }} completed). Retakes require fee payment.
                        </span>
                    @elseif (auth()->user()->campus_claim_status === 'pending')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-amber-500/20 text-amber-100 border border-amber-400/30 font-medium">
                            <flux:icon icon="clock" class="size-3.5 text-amber-300" />
                            Campus Affiliation Claim under administrative review
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/10 text-blue-100 border border-white/20 font-medium">
                            <flux:icon icon="credit-card" class="size-3.5 text-blue-200" />
                            External Candidate Registration &amp; Examination Portal
                        </span>
                    @endif
                </div>

                <flux:text class="text-blue-100 text-xs sm:text-sm max-w-xl">
                    Institutional English Proficiency Test (EPT) session. Press <kbd class="px-2 py-0.5 rounded bg-white/20 text-white font-mono text-xs font-bold border border-white/30">H</kbd> anytime for keyboard shortcuts.
                </flux:text>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                @if (! auth()->user()->isInternal() && auth()->user()->campus_claim_status !== 'pending')
                    <flux:button variant="filled" icon="academic-cap" @click="claimModal = true" class="bg-white/10 text-white hover:bg-white/20 font-semibold border border-white/30 text-xs sm:text-sm">
                        Claim UMMI Status (KTM)
                    </flux:button>
                @endif

                @if (! auth()->user()->hasFreeAttemptAvailable())
                    <flux:button variant="filled" icon="credit-card" @click="paymentModal = true; selectedSessionId = ''" class="bg-amber-400 text-amber-950 hover:bg-amber-300 font-bold border-0 text-xs sm:text-sm">
                        Submit Payment Receipt
                    </flux:button>
                @endif

                <flux:button variant="filled" icon="speaker-wave" onclick="testAudio()" class="bg-white text-blue-700 hover:bg-blue-50 font-bold border-0 text-xs sm:text-sm">
                    Test Audio (T)
                </flux:button>
            </div>
        </div>
    </flux:card>

    <!-- In Progress Warning / Resume Banner -->
    @if ($inProgressAttempt)
        <flux:callout variant="warning" icon="clock" class="shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 w-full">
                <div>
                    <div class="font-bold text-zinc-900 dark:text-white">Active Test Session In Progress</div>
                    <div class="text-xs text-zinc-600 dark:text-zinc-400 mt-0.5">
                        {{ $inProgressAttempt->testSession?->title ?? 'EPT Exam' }} — Section: {{ ucfirst($inProgressAttempt->current_section_slug ?? 'General') }}
                    </div>
                </div>
                <flux:button href="{{ route('exam.take', $inProgressAttempt) }}" variant="primary" icon:trailing="arrow-right" size="sm">
                    Continue Examination
                </flux:button>
            </div>
        </flux:callout>
    @endif

    <!-- Available Tests Section -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="lg" level="2">Available Scheduled Sessions</flux:heading>
                <flux:text class="text-xs">Select your official examination schedule and seat allocation.</flux:text>
            </div>
        </div>

        @if ($activeTests->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                @foreach ($activeTests as $test)
                    @php
                        $eligibility = auth()->user()->isEligibleForTestSession($test);
                        $remaining = $test->remainingSeats();
                        $isFull = $test->isFull();
                    @endphp
                    <flux:card class="flex flex-col justify-between space-y-4 border border-zinc-200 dark:border-zinc-800 shadow-sm hover:shadow-md transition-shadow">
                        <div class="space-y-3">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <flux:heading size="md" class="font-bold text-zinc-900 dark:text-white">
                                        {{ $test->title }}
                                    </flux:heading>
                                    <flux:text class="text-xs mt-1 text-zinc-500 line-clamp-2">
                                        {{ $test->description ?: 'Institutional EPT examination session.' }}
                                    </flux:text>
                                </div>
                                <div class="flex flex-col items-end gap-1">
                                    @if ($test->isLocked())
                                        <flux:badge size="sm" color="warning" icon="lock-closed">Locked</flux:badge>
                                    @elseif ($test->isEnded())
                                        <flux:badge size="sm" color="zinc" icon="no-symbol">Ended</flux:badge>
                                    @else
                                        <flux:badge size="sm" color="success" icon="check-circle">Open</flux:badge>
                                    @endif

                                    <!-- Capacity Badge -->
                                    @if (! is_null($test->capacity))
                                        @if ($isFull)
                                            <flux:badge size="xs" color="danger" icon="x-circle">Full ({{ $test->capacity }}/{{ $test->capacity }})</flux:badge>
                                        @else
                                            <flux:badge size="xs" color="info" icon="users">{{ $remaining }} / {{ $test->capacity }} Seats</flux:badge>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            <!-- Schedule Details -->
                            @if ($test->starts_at || $test->ends_at)
                                <div class="p-3 rounded-xl bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200/80 dark:border-zinc-700/60 text-xs text-zinc-600 dark:text-zinc-300 space-y-1">
                                    @if ($test->starts_at)
                                        <div class="flex items-center gap-1.5">
                                            <flux:icon icon="calendar" class="size-3.5 text-zinc-400" />
                                            <span>Starts: <strong>{{ $test->starts_at->format('d M Y, H:i') }} WIB</strong></span>
                                        </div>
                                    @endif
                                    @if ($test->ends_at)
                                        <div class="flex items-center gap-1.5">
                                            <flux:icon icon="clock" class="size-3.5 text-zinc-400" />
                                            <span>Ends: <strong>{{ $test->ends_at->format('d M Y, H:i') }} WIB</strong></span>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <!-- Sections Badges -->
                            <div class="flex flex-wrap gap-1.5 pt-1">
                                @foreach ($test->sections as $sec)
                                    <flux:badge size="sm" color="zinc">{{ $sec->name }} ({{ $sec->duration_minutes }}m)</flux:badge>
                                @endforeach
                            </div>
                        </div>

                        <!-- Action Button & Eligibility Gate -->
                        <div class="pt-2 border-t border-zinc-100 dark:border-zinc-800 space-y-2">
                            @if ($test->isLocked())
                                <flux:button disabled variant="filled" icon="lock-closed" class="w-full opacity-60 cursor-not-allowed">
                                    Locked (Starts {{ $test->starts_at->format('d M, H:i') }})
                                </flux:button>
                            @elseif ($test->isEnded())
                                <flux:button disabled variant="filled" class="w-full opacity-50 cursor-not-allowed">
                                    Schedule Ended
                                </flux:button>
                            @elseif ($isFull)
                                <flux:button disabled variant="filled" icon="no-symbol" class="w-full opacity-50 cursor-not-allowed text-rose-600">
                                    Session Full — Select Another Schedule
                                </flux:button>
                            @elseif ($eligibility['eligible'])
                                <form method="POST" action="{{ route('exam.start', $test) }}">
                                    @csrf
                                    <flux:button type="submit" variant="primary" icon="play" class="w-full font-bold">
                                        {{ $eligibility['type'] === 'free' ? 'Start Exam (Complimentary 1x)' : 'Start Exam (Paid Registration)' }}
                                    </flux:button>
                                </form>
                            @else
                                <div class="space-y-1.5">
                                    <flux:button variant="filled" icon="credit-card" @click="paymentModal = true; selectedSessionId = '{{ $test->id }}'" class="w-full font-semibold bg-amber-500 hover:bg-amber-600 text-white border-0">
                                        Register &amp; Pay to Unlock
                                    </flux:button>
                                    <p class="text-[11px] text-zinc-500 text-center">{{ $eligibility['reason'] }}</p>
                                </div>
                            @endif
                        </div>
                    </flux:card>
                @endforeach
            </div>
        @else
            <flux:card class="text-center py-12 space-y-3">
                <flux:icon icon="clipboard-document-list" class="size-10 mx-auto text-zinc-400" />
                <flux:heading size="md">No tests currently available</flux:heading>
                <flux:text class="text-xs">There are no open EPT test schedules assigned to you right now.</flux:text>
            </flux:card>
        @endif
    </div>

    <!-- Exam History & Completed Attempts -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="lg" level="2">Exam History</flux:heading>
                <flux:text class="text-xs">Completed examination sessions and performance score reports.</flux:text>
            </div>
        </div>

        <flux:card>
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Test Session &amp; Serial</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column>Listening</flux:table.column>
                    <flux:table.column>Structure</flux:table.column>
                    <flux:table.column>Reading</flux:table.column>
                    <flux:table.column>Total Score</flux:table.column>
                    <flux:table.column>Completed At</flux:table.column>
                    <flux:table.column class="text-right">Action</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($completedAttempts as $attempt)
                        <flux:table.row>
                            <flux:table.cell>
                                <div class="font-semibold text-zinc-900 dark:text-white">
                                    {{ $attempt->testSession?->title ?? 'EPT Exam' }}
                                </div>
                                <div class="text-xs text-zinc-500 font-mono">
                                    {{ $attempt->serial_number ?? ('#' . $attempt->id) }}
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge size="sm" color="success">Completed</flux:badge>
                            </flux:table.cell>
                            <flux:table.cell class="font-mono text-xs font-bold text-zinc-700 dark:text-zinc-300">
                                {{ $attempt->score_listening ?? 0 }}
                            </flux:table.cell>
                            <flux:table.cell class="font-mono text-xs font-bold text-zinc-700 dark:text-zinc-300">
                                {{ $attempt->score_structure ?? 0 }}
                            </flux:table.cell>
                            <flux:table.cell class="font-mono text-xs font-bold text-zinc-700 dark:text-zinc-300">
                                {{ $attempt->score_reading ?? 0 }}
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge size="md" color="emerald" class="font-extrabold font-mono text-sm">
                                    {{ $attempt->total_score ?? 0 }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell class="text-xs text-zinc-500">
                                {{ $attempt->completed_at?->format('d M Y, H:i') ?? '-' }}
                            </flux:table.cell>
                            <flux:table.cell class="text-right">
                                <flux:button href="{{ route('exam.result', $attempt) }}" size="sm" variant="ghost" icon="eye">
                                    View Result
                                </flux:button>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="8" class="text-center py-8 text-zinc-500 text-xs">
                                No completed examinations found.
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </flux:card>
    </div>

    <!-- Modal 1: Claim Campus Status (KTM / Surat Sidang) -->
    <div x-show="claimModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" @keydown.escape.window="claimModal = false">
        <div class="w-full max-w-lg bg-white dark:bg-zinc-900 rounded-2xl p-6 shadow-2xl border border-zinc-200 dark:border-zinc-800 space-y-4" @click.away="claimModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-zinc-200 dark:border-zinc-800">
                <flux:heading size="lg" class="flex items-center gap-2">
                    <flux:icon icon="academic-cap" class="size-5 text-blue-600" />
                    <span>Claim UMMI Campus Status</span>
                </flux:heading>
                <flux:button variant="ghost" size="xs" icon="x-mark" @click="claimModal = false" />
            </div>

            <p class="text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed">
                Registered using an external email? If you are an active student of Universitas Muhammadiyah Sukabumi (UMMI), verify your credentials below to receive your <strong>1 Complimentary University Attempt</strong>.
            </p>

            <form method="POST" action="{{ route('student.claim-campus') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <flux:field>
                    <flux:label>Student ID Number (NIM)</flux:label>
                    <flux:input name="student_id" placeholder="e.g. 2230511102" required value="{{ old('student_id', auth()->user()->student_id) }}" />
                </flux:field>

                <flux:field>
                    <flux:label>Department / Study Program</flux:label>
                    <flux:input name="department" placeholder="e.g. Teknik Informatika" required value="{{ old('department', auth()->user()->department) }}" />
                </flux:field>

                <flux:field>
                    <flux:label>Student Card (KTM) or Defense Verification Letter</flux:label>
                    <input type="file" name="student_card" accept=".jpg,.jpeg,.png,.pdf" required class="block w-full text-xs text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-zinc-800 dark:file:text-zinc-200" />
                    <flux:description>Maximum file size: 2 MB. Accepted formats: JPG, PNG, PDF.</flux:description>
                </flux:field>

                <div class="pt-2 flex justify-end gap-2">
                    <flux:button variant="ghost" @click="claimModal = false">Cancel</flux:button>
                    <flux:button type="submit" variant="primary">Submit Credentials</flux:button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 2: Submit Payment Receipt -->
    <div x-show="paymentModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" @keydown.escape.window="paymentModal = false">
        <div class="w-full max-w-lg bg-white dark:bg-zinc-900 rounded-2xl p-6 shadow-2xl border border-zinc-200 dark:border-zinc-800 space-y-4" @click.away="paymentModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-zinc-200 dark:border-zinc-800">
                <flux:heading size="lg" class="flex items-center gap-2">
                    <flux:icon icon="credit-card" class="size-5 text-emerald-600" />
                    <span>Submit Examination Payment Proof</span>
                </flux:heading>
                <flux:button variant="ghost" size="xs" icon="x-mark" @click="paymentModal = false" />
            </div>

            <!-- Official University Bank Account Information Box -->
            <div class="p-4 rounded-xl bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-900 text-xs space-y-2">
                <div class="font-bold text-blue-900 dark:text-blue-200 flex items-center gap-1.5">
                    <flux:icon icon="building-library" class="size-4" />
                    <span>Official University Bank Account (UMMI)</span>
                </div>
                <div class="grid grid-cols-2 gap-2 text-zinc-700 dark:text-zinc-300">
                    <div>
                        <span class="text-zinc-500">Bank:</span>
                        <div class="font-semibold">Bank Syariah Indonesia (BSI)</div>
                    </div>
                    <div>
                        <span class="text-zinc-500">Account No:</span>
                        <div class="font-mono font-bold text-zinc-900 dark:text-white">777 000 1234</div>
                    </div>
                    <div>
                        <span class="text-zinc-500">Account Holder:</span>
                        <div class="font-semibold">Pusat Bahasa UMMI</div>
                    </div>
                    <div>
                        <span class="text-zinc-500">Registration Fee:</span>
                        <div class="font-bold text-emerald-600 dark:text-emerald-400">Rp 100.000,-</div>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('student.payment.submit') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <input type="hidden" name="test_session_id" :value="selectedSessionId" />

                <flux:field>
                    <flux:label>Payment Method / Sender Bank</flux:label>
                    <flux:input name="payment_method" placeholder="e.g. BSI Mobile / BCA Transfer / Mandiri" required />
                </flux:field>

                <flux:field>
                    <flux:label>Transferred Amount (IDR)</flux:label>
                    <flux:input type="number" name="amount" value="100000" required min="10000" />
                </flux:field>

                <flux:field>
                    <flux:label>Upload Transfer Receipt</flux:label>
                    <input type="file" name="proof" accept=".jpg,.jpeg,.png,.pdf" required class="block w-full text-xs text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-zinc-800 dark:file:text-zinc-200" />
                    <flux:description>Upload transfer screenshot or receipt (JPG, PNG, PDF, max 2MB).</flux:description>
                </flux:field>

                <div class="pt-2 flex justify-end gap-2">
                    <flux:button variant="ghost" @click="paymentModal = false">Cancel</flux:button>
                    <flux:button type="submit" variant="primary">Submit Payment Receipt</flux:button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
