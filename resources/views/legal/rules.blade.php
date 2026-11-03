@extends('layouts.legal', ['title' => 'Exam Rules & Honor Code'])

@section('content')
<div class="space-y-8">
    {{-- Document Header --}}
    <div class="space-y-2 border-b border-zinc-200 dark:border-zinc-800 pb-6">
        <flux:badge color="amber" size="sm">Academic Integrity Pledge</flux:badge>
        <flux:heading size="xl" level="1">Official TOEFL Exam Rules & Honor Code</flux:heading>
        <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">
            Mandatory guidelines for all candidates taking the institutional online TOEFL assessment.
        </flux:text>
    </div>

    {{-- Content Sections --}}
    <div class="space-y-8 text-sm sm:text-base leading-relaxed text-zinc-700 dark:text-zinc-300">
        {{-- Section 1: Overview and Structure --}}
        <section class="space-y-3">
            <flux:heading size="lg" level="2">1. Test Format & Section Breakdown</flux:heading>
            <p>
                The TUTUT Institutional TOEFL examination consists of three sequentially timed sections totaling 140 multiple-choice questions over approximately 115 minutes:
            </p>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 my-4">
                <div class="p-4 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900/50 space-y-1">
                    <flux:badge color="blue" size="sm">Section 1</flux:badge>
                    <flux:heading size="base">Listening Comprehension</flux:heading>
                    <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">50 Questions &bull; ~35 Minutes</flux:text>
                    <flux:text size="xs" class="block text-zinc-600 dark:text-zinc-400 pt-1">
                        Tests ability to understand spoken North American English conversations and academic lectures.
                    </flux:text>
                </div>

                <div class="p-4 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900/50 space-y-1">
                    <flux:badge color="emerald" size="sm">Section 2</flux:badge>
                    <flux:heading size="base">Structure & Written Expression</flux:heading>
                    <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">40 Questions &bull; 25 Minutes</flux:text>
                    <flux:text size="xs" class="block text-zinc-600 dark:text-zinc-400 pt-1">
                        Measures mastery of standard formal written English grammar, sentence structure, and style.
                    </flux:text>
                </div>

                <div class="p-4 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900/50 space-y-1">
                    <flux:badge color="purple" size="sm">Section 3</flux:badge>
                    <flux:heading size="base">Reading Comprehension</flux:heading>
                    <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">50 Questions &bull; 55 Minutes</flux:text>
                    <flux:text size="xs" class="block text-zinc-600 dark:text-zinc-400 pt-1">
                        Assesses comprehension of academic passages, vocabulary in context, and main idea inference.
                    </flux:text>
                </div>
            </div>
        </section>

        {{-- Section 2: Equipment & Audio Rules --}}
        <section class="space-y-3">
            <flux:heading size="lg" level="2">2. Hardware, Audio & Workspace Requirements</flux:heading>
            <ul class="list-disc pl-5 space-y-1.5">
                <li><strong>Audio Equipment:</strong> Candidates must use functional headphones, earphones, or speakers. Audio plays automatically during Section 1 and cannot be paused or replayed.</li>
                <li><strong>Single Device Only:</strong> Only one laptop or desktop computer may be logged in during the examination. Mobile devices may not be used to access the exam take interface.</li>
                <li><strong>Browser Compatibility:</strong> Use modern versions of Chrome, Edge, Firefox, or Safari. Ensure hardware acceleration and JavaScript are enabled.</li>
                <li><strong>Private Room:</strong> Candidates must take the test in a quiet, well-lit environment without unauthorized individuals present.</li>
            </ul>
        </section>

        {{-- Section 3: Strictly Prohibited Acts --}}
        <section class="space-y-3">
            <flux:heading size="lg" level="2">3. Strictly Prohibited Conduct</flux:heading>
            <p>
                Engaging in any of the following actions constitutes an immediate violation of university examination policy:
            </p>
            <div class="p-4 rounded-xl border border-red-200 dark:border-red-900/50 bg-red-50/50 dark:bg-red-950/20 space-y-2">
                <ul class="list-disc pl-5 space-y-1.5 text-red-900 dark:text-red-300">
                    <li><strong>No Proxy / Substitute Test-Takers (Joki):</strong> Another individual taking the test on your behalf will result in criminal fraud and academic dismissal.</li>
                    <li><strong>No External Tools or AI Assistance:</strong> The use of ChatGPT, Google Translate, dictionaries, browser extensions, or automated answering bots is prohibited.</li>
                    <li><strong>No Tab Switching or Multi-Window Browsing:</strong> Leaving the exam window, opening new tabs, or switching applications triggers security audit warnings.</li>
                    <li><strong>No Developer Tools or Source Inspecting:</strong> Attempting to inspect DOM elements, copy question text, or intercept network requests is strictly banned.</li>
                    <li><strong>No Screen Recording or Capturing:</strong> Recording, screenshotting, photographing, or publishing question materials violates intellectual property and confidentiality agreements.</li>
                </ul>
            </div>
        </section>

        {{-- Section 4: Academic Sanctions --}}
        <section class="space-y-3">
            <flux:heading size="lg" level="2">4. Penalties for Misconduct</flux:heading>
            <p>
                Failure to comply with this Honor Code will result in:
            </p>
            <ol class="list-decimal pl-5 space-y-1.5">
                <li>Immediate disqualification and invalidation of the current test attempt (score awarded: 0).</li>
                <li>Permanent suspension of portal test-taking privileges.</li>
                <li>Formal submission of violation incident reports to the university Dean of Academic Affairs.</li>
            </ol>
        </section>

        {{-- Section 5: Technical Interruption Protocol --}}
        <section class="space-y-3">
            <flux:heading size="lg" level="2">5. Technical Interruption & Connection Recovery</flux:heading>
            <p>
                In the event of an unexpected internet disconnection or power failure:
            </p>
            <ul class="list-disc pl-5 space-y-1.5">
                <li>All answered questions are saved automatically in real-time on our servers.</li>
                <li>Log in again immediately using your registered credentials to resume your session.</li>
                <li>The section timer will continue running; please contact your exam proctor if your disruption exceeds 5 minutes.</li>
            </ul>
        </section>
    </div>
</div>
@endsection

