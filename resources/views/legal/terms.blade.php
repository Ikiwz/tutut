@extends('layouts.legal', ['title' => 'Terms of Service'])

@section('content')
<div class="space-y-8">
    {{-- Document Header --}}
    <div class="space-y-2 border-b border-zinc-200 dark:border-zinc-800 pb-6">
        <flux:badge color="blue" size="sm">Legal Documentation</flux:badge>
        <flux:heading size="xl" level="1">Terms of Service</flux:heading>
        <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">
            Last Updated: November 2026 &bull; Effective for all registered candidates and administrators.
        </flux:text>
    </div>

    {{-- Content Sections --}}
    <div class="prose dark:prose-invert max-w-none space-y-6 text-sm sm:text-base leading-relaxed text-zinc-700 dark:text-zinc-300">
        <section class="space-y-3">
            <flux:heading size="lg" level="2">1. Acceptance of Terms</flux:heading>
            <p>
                By creating an account, logging in, or accessing the <strong>TUTUT Online TOEFL Examination Portal</strong> ("Platform", "we", or "us"), you agree to be bound by these Terms of Service and all related academic regulations. If you do not agree to these terms, you must not access or use this portal.
            </p>
        </section>

        <section class="space-y-3">
            <flux:heading size="lg" level="2">2. Account Eligibility & Candidate Responsibilities</flux:heading>
            <p>
                Access to the candidate portal is restricted to authorized university students and registered exam examinees. When registering, you agree to:
            </p>
            <ul class="list-disc pl-5 space-y-1.5">
                <li>Provide accurate, current, and complete legal name and university contact details.</li>
                <li>Maintain the confidentiality of your login credentials and immediately report any unauthorized access.</li>
                <li>Accept sole responsibility for all actions performed under your account.</li>
                <li>Refrain from sharing account access or allowing any third party to take an examination on your behalf.</li>
            </ul>
        </section>

        <section class="space-y-3">
            <flux:heading size="lg" level="2">3. Examination Integrity & Academic Honesty</flux:heading>
            <p>
                TUTUT is an official university academic assessment tool. Examination scores contribute directly to degree qualifications, graduation requirements, or course credits. All candidates must uphold strict academic honesty.
            </p>
            <p>
                Any form of academic misconduct—including unauthorized collaboration, use of artificial intelligence tools, looking up answers online, screen capturing, or employing a proxy test-taker—will result in immediate termination of the examination attempt, forfeiture of fees, and reporting to the university academic disciplinary board.
            </p>
        </section>

        <section class="space-y-3">
            <flux:heading size="lg" level="2">4. Intellectual Property Rights</flux:heading>
            <p>
                All test materials, including listening audio recordings, reading passages, question items, answer keys, score conversion tables, and software source code, are proprietary intellectual property. You may not copy, record, transcribe, distribute, publish, or reproduce any examination content in any medium without express written authorization.
            </p>
        </section>

        <section class="space-y-3">
            <flux:heading size="lg" level="2">5. Service Availability & Technical Conditions</flux:heading>
            <p>
                While we strive for continuous service reliability during official examination schedules, platform access depends on internet connectivity, server load, and client device configurations. Candidates are responsible for ensuring:
            </p>
            <ul class="list-disc pl-5 space-y-1.5">
                <li>A modern, updated web browser (Google Chrome, Microsoft Edge, Mozilla Firefox, or Safari).</li>
                <li>A stable and reliable broadband internet connection.</li>
                <li>Functional audio playback hardware (headphones or speakers) for the Listening Comprehension section.</li>
            </ul>
        </section>

        <section class="space-y-3">
            <flux:heading size="lg" level="2">6. Limitation of Liability & Score Invalidation</flux:heading>
            <p>
                The university and platform administration reserve the right to review audit logs, response times, and anomaly detection reports. If an examination attempt exhibits evidence of technical tampering or irregular response patterns, the portal administration reserves the right to invalidate the attempt and require a supervised re-examination.
            </p>
        </section>

        <section class="space-y-3">
            <flux:heading size="lg" level="2">7. Changes to Terms</flux:heading>
            <p>
                We may periodically update these Terms to reflect changes in academic policy or examination procedures. Continued use of the portal after any modifications constitutes acceptance of the revised terms.
            </p>
        </section>
    </div>
</div>
@endsection

