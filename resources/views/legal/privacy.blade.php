@extends('layouts.legal', ['title' => 'Privacy Policy'])

@section('content')
<div class="space-y-8">
    {{-- Document Header --}}
    <div class="space-y-2 border-b border-zinc-200 dark:border-zinc-800 pb-6">
        <flux:badge color="green" size="sm">Data Protection</flux:badge>
        <flux:heading size="xl" level="1">Privacy Policy</flux:heading>
        <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">
            Last Updated: November 2026 &bull; Compliant with academic data privacy and personal data protection regulations.
        </flux:text>
    </div>

    {{-- Content Sections --}}
    <div class="prose dark:prose-invert max-w-none space-y-6 text-sm sm:text-base leading-relaxed text-zinc-700 dark:text-zinc-300">
        <section class="space-y-3">
            <flux:heading size="lg" level="2">1. Overview</flux:heading>
            <p>
                This Privacy Policy explains how <strong>TUTUT</strong> collects, uses, stores, and protects personal information belonging to candidates and administrators. We are committed to safeguarding your academic records and maintaining the highest standard of data privacy.
            </p>
        </section>

        <section class="space-y-3">
            <flux:heading size="lg" level="2">2. Information We Collect</flux:heading>
            <p>
                To provide examination services, generate certified score reports, and prevent fraud, we collect:
            </p>
            <ul class="list-disc pl-5 space-y-1.5">
                <li><strong>Identity Information:</strong> Full legal name, email address, student identification number (NIM), department/faculty, and profile photo/avatar.</li>
                <li><strong>OAuth Account Data:</strong> When signing in via Google, we receive your verified Google ID, primary email address, and avatar image. We do not access your Google password or private drive files.</li>
                <li><strong>Examination & Performance Data:</strong> Selected answers, response timestamps, section completion times, converted scaled TOEFL scores, raw scores, and certification records.</li>
                <li><strong>Technical & Audit Logs:</strong> IP address, device user-agent string, session identifiers, and security event logs to verify that examination attempts originate from authorized clients.</li>
            </ul>
        </section>

        <section class="space-y-3">
            <flux:heading size="lg" level="2">3. How Your Information Is Used</flux:heading>
            <p>
                We process your data strictly for legitimate academic and administrative purposes:
            </p>
            <ul class="list-disc pl-5 space-y-1.5">
                <li>Authenticating candidate identity and securing test-taking sessions.</li>
                <li>Computing TOEFL section scores (Listening, Structure, Reading) according to standard conversion tables.</li>
                <li>Generating verifiable score transcripts and completion certificates for university graduation audits.</li>
                <li>Detecting unauthorized concurrent logins or anomalous response patterns.</li>
            </ul>
        </section>

        <section class="space-y-3">
            <flux:heading size="lg" level="2">4. Data Storage, Encryption & Security</flux:heading>
            <p>
                All account passwords are encrypted using one-way adaptive hashing (Bcrypt). Communication between your browser and our servers is secured with Transport Layer Security (TLS/HTTPS). Examination session tokens and database records are housed within access-controlled server environments.
            </p>
        </section>

        <section class="space-y-3">
            <flux:heading size="lg" level="2">5. Disclosure of Data to Third Parties</flux:heading>
            <p>
                We do not sell, rent, or monetize your personal information. Examination results are shared exclusively with authorized university bodies, including:
            </p>
            <ul class="list-disc pl-5 space-y-1.5">
                <li>University language center coordinators and verified exam proctors.</li>
                <li>Academic affairs and registrar departments for degree clearance verification.</li>
            </ul>
        </section>

        <section class="space-y-3">
            <flux:heading size="lg" level="2">6. Data Retention</flux:heading>
            <p>
                Examination attempts, score conversions, and answers are retained in accordance with university academic archive retention guidelines to allow historical verification of certificates requested by employers or academic institutions.
            </p>
        </section>

        <section class="space-y-3">
            <flux:heading size="lg" level="2">7. Inquiries & Contact</flux:heading>
            <p>
                For questions regarding your academic records or personal data privacy, please contact the University Language Center through the official campus administrative portal.
            </p>
        </section>
    </div>
</div>
@endsection

