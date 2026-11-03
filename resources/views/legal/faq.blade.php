@extends('layouts.legal', ['title' => 'FAQ & Candidate Help Center'])

@section('content')
<div class="space-y-8">
    {{-- Document Header --}}
    <div class="space-y-2 border-b border-zinc-200 dark:border-zinc-800 pb-6">
        <flux:badge color="zinc" size="sm">Help Center</flux:badge>
        <flux:heading size="xl" level="1">Frequently Asked Questions (FAQ)</flux:heading>
        <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">
            Quick answers to common questions about accounts, test sessions, audio setup, and TOEFL certification.
        </flux:text>
    </div>

    {{-- FAQ Content Accordion/Cards --}}
    <div class="space-y-6 text-sm sm:text-base leading-relaxed text-zinc-700 dark:text-zinc-300">
        {{-- Category 1: Registration & Account --}}
        <div class="space-y-4">
            <flux:heading size="lg" level="2" class="text-blue-600 dark:text-blue-400">Account & Registration</flux:heading>

            <div class="space-y-3">
                <flux:card variant="soft">
                    <flux:heading size="base" class="font-semibold">Why does registration only require basic account details?</flux:heading>
                    <flux:text size="sm" class="mt-1">
                        We keep registration simple so candidates can set up their portal account quickly. Your Student Identification Number (NIM), department/faculty, and student verification card can be completed inside your dashboard before you schedule or take your exam.
                    </flux:text>
                </flux:card>

                <flux:card variant="soft">
                    <flux:heading size="base" class="font-semibold">What should I do if I forget my account password?</flux:heading>
                    <flux:text size="sm" class="mt-1">
                        Click the <flux:link href="{{ route('password.request') }}">Forgot password?</flux:link> link on the login page and enter your registered email address. You will receive an email containing a secure link to create a new password.
                    </flux:text>
                </flux:card>

                <flux:card variant="soft">
                    <flux:heading size="base" class="font-semibold">Can I use both Google Sign-In and email login?</flux:heading>
                    <flux:text size="sm" class="mt-1">
                        Yes. If your Google account email matches your registered university email address, you can sign in seamlessly using either method.
                    </flux:text>
                </flux:card>
            </div>
        </div>

        {{-- Category 2: Exam Experience & Audio --}}
        <div class="space-y-4 pt-4 border-t border-zinc-200 dark:border-zinc-800">
            <flux:heading size="lg" level="2" class="text-blue-600 dark:text-blue-400">Taking the TOEFL Exam</flux:heading>

            <div class="space-y-3">
                <flux:card variant="soft">
                    <flux:heading size="base" class="font-semibold">How does Section 1 (Listening Comprehension) audio work?</flux:heading>
                    <flux:text size="sm" class="mt-1">
                        Audio clips for conversations and lectures play automatically when question sets load. According to standardized TOEFL regulations, each audio clip is played only once and cannot be paused or repeated. Please test your sound before beginning.
                    </flux:text>
                </flux:card>

                <flux:card variant="soft">
                    <flux:heading size="base" class="font-semibold">What happens if my internet connection drops during the test?</flux:heading>
                    <flux:text size="sm" class="mt-1">
                        TUTUT automatically synchronizes each answer to our secure servers the moment you select an option. If your connection drops, reconnect your internet and log in again immediately to continue from where you left off.
                    </flux:text>
                </flux:card>

                <flux:card variant="soft">
                    <flux:heading size="base" class="font-semibold">Can I go back to change answers in a previous section?</flux:heading>
                    <flux:text size="sm" class="mt-1">
                        No. Once you submit a section (e.g. Listening Comprehension) and move to the next section, standard TOEFL rules prohibit returning to previous sections. However, you can review and change answers within the active section before time expires.
                    </flux:text>
                </flux:card>
            </div>
        </div>

        {{-- Category 3: Scoring & Certification --}}
        <div class="space-y-4 pt-4 border-t border-zinc-200 dark:border-zinc-800">
            <flux:heading size="lg" level="2" class="text-blue-600 dark:text-blue-400">Scoring & Certificates</flux:heading>

            <div class="space-y-3">
                <flux:card variant="soft">
                    <flux:heading size="base" class="font-semibold">How is my final TOEFL score calculated?</flux:heading>
                    <flux:text size="sm" class="mt-1">
                        Your raw score from each section (number of correct answers) is converted into a standard scaled score ranging from 31 to 68. The three converted scores are added together, multiplied by 10, and divided by 3, resulting in a final score between 310 and 677.
                    </flux:text>
                </flux:card>

                <flux:card variant="soft">
                    <flux:heading size="base" class="font-semibold">When can I see my exam results and certificate?</flux:heading>
                    <flux:text size="sm" class="mt-1">
                        Your score breakdown is displayed immediately upon completing the final section. An official digital certificate with a verification code is generated directly in your candidate dashboard for university submission.
                    </flux:text>
                </flux:card>
            </div>
        </div>
    </div>
</div>
@endsection

