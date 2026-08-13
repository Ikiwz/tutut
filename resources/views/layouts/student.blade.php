<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <title>@yield('title', 'TUTUT') — TUTUT (TOEFL for the Blind)</title>
    <meta name="description" content="TOEFL exam platform specifically designed for visually impaired users with full accessibility">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --primary-light: #3b82f6;
            --accent: #0ea5e9;
            --bg: #f8fafc;
            --bg-card: #ffffff;
            --bg-dark: #0f172a;
            --text: #1e293b;
            --text-muted: #64748b;
            --text-light: #94a3b8;
            --border: #e2e8f0;
            --success: #10b981;
            --success-bg: #d1fae5;
            --warning: #f59e0b;
            --warning-bg: #fef3c7;
            --danger: #ef4444;
            --danger-bg: #fee2e2;
            --info: #06b6d4;
            --radius: 12px;
            --radius-lg: 16px;
            --shadow: 0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.06);
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.08), 0 2px 4px -2px rgba(0,0,0,0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.08), 0 4px 6px -4px rgba(0,0,0,0.06);
            --font-size-base: 1rem;
        }

        /* High Contrast Mode */
        .high-contrast {
            --primary: #ffff00;
            --primary-dark: #e6e600;
            --primary-light: #ffff33;
            --accent: #00ffff;
            --bg: #000000;
            --bg-card: #1a1a1a;
            --bg-dark: #000000;
            --text: #ffffff;
            --text-muted: #cccccc;
            --text-light: #aaaaaa;
            --border: #555555;
            --success: #00ff00;
            --success-bg: #003300;
            --warning: #ffff00;
            --warning-bg: #333300;
            --danger: #ff0000;
            --danger-bg: #330000;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.6;
            font-size: var(--font-size-base);
            min-height: 100vh;
        }

        /* Focus styles for accessibility */
        *:focus-visible {
            outline: 3px solid var(--primary);
            outline-offset: 2px;
            border-radius: 4px;
        }

        .skip-link {
            position: absolute;
            top: -100%;
            left: 16px;
            background: var(--primary);
            color: #fff;
            padding: 12px 24px;
            border-radius: var(--radius);
            z-index: 9999;
            font-weight: 600;
            text-decoration: none;
            transition: top 0.2s;
        }
        .skip-link:focus {
            top: 16px;
        }

        /* Navigation */
        .navbar {
            background: var(--bg-card);
            border-bottom: 1px solid var(--border);
            padding: 0 24px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: var(--shadow);
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: var(--text);
        }

        .navbar-brand svg {
            width: 32px;
            height: 32px;
            color: var(--primary);
        }

        .navbar-brand span {
            font-size: 1.25rem;
            font-weight: 700;
            letter-spacing: -0.025em;
        }

        .navbar-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: var(--radius);
            font-weight: 600;
            font-size: 0.875rem;
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.2s ease;
            text-decoration: none;
            line-height: 1.4;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .btn:active {
            transform: translateY(0);
        }

        .btn-primary {
            background: var(--primary);
            color: #fff;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
        }

        .btn-secondary {
            background: var(--bg);
            color: var(--text);
            border-color: var(--border);
        }

        .btn-secondary:hover {
            background: var(--border);
        }

        .btn-success {
            background: var(--success);
            color: #fff;
        }

        .btn-danger {
            background: var(--danger);
            color: #fff;
        }

        .btn-sm {
            padding: 6px 14px;
            font-size: 0.8125rem;
        }

        .btn-lg {
            padding: 14px 28px;
            font-size: 1rem;
        }

        .btn-icon {
            padding: 8px;
            border-radius: 8px;
            background: var(--bg);
            border: 1px solid var(--border);
            color: var(--text-muted);
            cursor: pointer;
        }

        .btn-icon:hover {
            background: var(--border);
            color: var(--text);
        }

        /* Container */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 24px;
        }

        /* Cards */
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 24px;
            box-shadow: var(--shadow);
            transition: box-shadow 0.2s;
        }

        .card:hover {
            box-shadow: var(--shadow-md);
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--border);
        }

        .card-title {
            font-size: 1.125rem;
            font-weight: 700;
        }

        /* Form inputs */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
            color: var(--text);
            font-size: 0.875rem;
        }

        .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid var(--border);
            border-radius: var(--radius);
            font-size: 1rem;
            font-family: inherit;
            color: var(--text);
            background: var(--bg-card);
            transition: border-color 0.2s;
        }

        .form-input:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 600;
            line-height: 1.4;
        }

        .badge-primary { background: rgba(37, 99, 235, 0.1); color: var(--primary); }
        .badge-success { background: var(--success-bg); color: var(--success); }
        .badge-warning { background: var(--warning-bg); color: var(--warning); }
        .badge-danger { background: var(--danger-bg); color: var(--danger); }

        /* Grid */
        .grid { display: grid; gap: 20px; }
        .grid-2 { grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); }
        .grid-3 { grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); }
        .grid-4 { grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); }

        /* Accessibility Toolbar */
        .a11y-toolbar {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .a11y-toolbar button {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            border: 1px solid var(--border);
            background: var(--bg);
            color: var(--text-muted);
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.2s;
        }

        .a11y-toolbar button:hover,
        .a11y-toolbar button.active {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
        }

        /* Screen reader only */
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0,0,0,0);
            white-space: nowrap;
            border: 0;
        }

        /* Live region for screen readers */
        .aria-live-region {
            position: absolute;
            width: 1px;
            height: 1px;
            overflow: hidden;
            clip: rect(0,0,0,0);
        }

        /* Alert messages */
        .alert {
            padding: 14px 20px;
            border-radius: var(--radius);
            margin-bottom: 16px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-error {
            background: var(--danger-bg);
            color: var(--danger);
            border: 1px solid var(--danger);
        }

        .alert-success {
            background: var(--success-bg);
            color: var(--success);
            border: 1px solid var(--success);
        }

        /* Keyboard shortcut hints */
        kbd {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 24px;
            height: 24px;
            padding: 2px 6px;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 6px;
            font-size: 0.75rem;
            font-family: 'Inter', monospace;
            font-weight: 600;
            color: var(--text-muted);
            box-shadow: 0 1px 0 var(--border);
        }

        /* Shortcut help modal */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal {
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            padding: 32px;
            max-width: 560px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: var(--shadow-lg);
        }

        .modal h2 {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .shortcut-list {
            list-style: none;
        }

        .shortcut-list li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid var(--border);
        }

        .shortcut-list li:last-child {
            border-bottom: none;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container { padding: 16px; }
            .grid-2, .grid-3 { grid-template-columns: 1fr; }
            .navbar { padding: 0 16px; }
        }

        /* Font size scaling */
        .font-size-large {
            --font-size-base: 1.25rem;
        }

        .font-size-xl {
            --font-size-base: 1.5rem;
        }
    </style>
    @yield('styles')
</head>
<body>
    <a href="#main-content" class="skip-link" aria-label="Skip to main content">
        Skip to Main Content
    </a>

    <!-- Live region for announcements -->
    <div id="aria-live" class="aria-live-region" aria-live="assertive" aria-atomic="true" role="status"></div>

    <nav class="navbar" role="navigation" aria-label="Main navigation">
        <a href="{{ route('student.dashboard') }}" class="navbar-brand" aria-label="TUTUT - Home Page">
            <img src="{{ asset('images/logo.png') }}" alt="TUTUT Logo" class="logo-icon" style="height: 32px; width: auto;" aria-hidden="true">
            <span>TUTUT</span>
        </a>

        <div class="navbar-actions">
            <div class="a11y-toolbar" role="toolbar" aria-label="Accessibility settings">
                <button id="contrast-toggle" onclick="toggleHighContrast()" aria-label="Toggle high contrast" title="High Contrast (Ctrl+U)">◐</button>
                <button id="tts-toggle" onclick="toggleTTS()" aria-label="Toggle text-to-speech" title="Text-to-Speech (Ctrl+T)">🔊</button>
            </div>
            @guest
                <div style="display: flex; gap: 8px; margin-left: 12px;">
                    <a href="{{ route('student.login') }}" class="btn btn-primary btn-sm" aria-label="Login Peserta">Login Peserta</a>
                    <a href="{{ url('/admin') }}" class="btn btn-secondary btn-sm" aria-label="Login Admin">Login Admin</a>
                </div>
            @endguest
            @auth
                <form method="POST" action="{{ route('student.logout') }}" style="display:inline">
                    @csrf
                    <button type="submit" class="btn btn-secondary btn-sm" aria-label="Logout of account">
                        Logout
                    </button>
                </form>
            @endauth
        </div>
    </nav>

    <main id="main-content" role="main" tabindex="-1">
        @yield('content')
    </main>

    <!-- Keyboard Shortcuts Help Modal -->
    <div id="shortcuts-modal" class="modal-overlay" role="dialog" aria-modal="true" aria-label="Keyboard Shortcuts Help">
        <div class="modal">
            <h2>⌨️ Keyboard Shortcuts</h2>
            <ul class="shortcut-list" role="list">
                <li><span>Select answer A</span> <kbd>A</kbd></li>
                <li><span>Select answer B</span> <kbd>B</kbd></li>
                <li><span>Select answer C</span> <kbd>C</kbd></li>
                <li><span>Select answer D</span> <kbd>D</kbd></li>
                <li><span>Next question</span> <kbd>→</kbd> or <kbd>N</kbd></li>
                <li><span>Previous question</span> <kbd>←</kbd> or <kbd>P</kbd></li>
                <li><span>Play passage/story audio (Reading)</span> <kbd>R</kbd></li>
                <li><span>Play question audio / Reread (Reading: <kbd>M</kbd>)</span> <kbd>M</kbd></li>
                <li><span>Toggle Text to Speech (TTS)</span> <kbd>V</kbd></li>
                <li><span>Test Suara / Audio</span> <kbd>T</kbd></li>
                <li><span>Submit section</span> <kbd>Enter</kbd> (in confirmation)</li>
                <li><span>Jump to question number</span> <kbd>1</kbd>-<kbd>9</kbd></li>
                <li><span>Shortcut help</span> <kbd>H</kbd></li>
                <li><span>Zoom in</span> <kbd>Ctrl</kbd>+<kbd>+</kbd></li>
                <li><span>Zoom out</span> <kbd>Ctrl</kbd>+<kbd>-</kbd></li>
                <li><span>Reset zoom (100%)</span> <kbd>Ctrl</kbd>+<kbd>0</kbd></li>
                <li><span>High contrast</span> <kbd>Ctrl</kbd>+<kbd>U</kbd></li>
                <li><span>Close dialog</span> <kbd>Esc</kbd></li>
            </ul>
            <div style="margin-top: 20px; text-align: right;">
                <button class="btn btn-primary" onclick="closeShortcutsModal()" aria-label="Close shortcut help">
                    Close <kbd style="background: rgba(255,255,255,0.2); color: #fff; border-color: rgba(255,255,255,0.3);">Esc</kbd>
                </button>
            </div>
        </div>
    </div>

    <script>
        // ============================================================
        // ACCESSIBILITY ENGINE
        // ============================================================

        let ttsEnabled = localStorage.getItem('tts') === 'true';

        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            // Clean up leftover app zoom from old code
            document.body.style.zoom = '';
            document.documentElement.style.fontSize = '';
            localStorage.removeItem('appZoom');
            localStorage.removeItem('zoomVersion');

            // Restore high contrast
            if (localStorage.getItem('highContrast') === 'true') {
                document.documentElement.classList.add('high-contrast');
                document.getElementById('contrast-toggle')?.classList.add('active');
            }
            // Restore TTS
            if (ttsEnabled) {
                document.getElementById('tts-toggle')?.classList.add('active');
            }
        });

        let currentSpeechJobId = 0;
        
        // Text-to-Speech
        function speak(textOrChunks) {
            if (!ttsEnabled) return;
            window.speechSynthesis.cancel();
            
            const jobId = ++currentSpeechJobId;
            
            if (typeof textOrChunks === 'string') {
                const utterance = new SpeechSynthesisUtterance(textOrChunks);
                utterance.lang = 'en-US';
                utterance.rate = 0.9;
                utterance.pitch = 1;
                window.speechSynthesis.speak(utterance);
            } else if (Array.isArray(textOrChunks) && textOrChunks.length > 0) {
                let i = 0;
                function speakChunk() {
                    if (jobId !== currentSpeechJobId) return; // Abort if a new job has started
                    
                    if (i < textOrChunks.length) {
                        let chunk = textOrChunks[i];
                        const utterance = new SpeechSynthesisUtterance(chunk.text);
                        utterance.lang = 'en-US';
                        utterance.rate = chunk.rate || 0.9;
                        utterance.pitch = chunk.pitch || 1;
                        utterance.onend = () => {
                            if (jobId !== currentSpeechJobId) return;
                            i++;
                            speakChunk();
                        };
                        // In case of an error on a chunk, we just continue
                        utterance.onerror = (e) => {
                            if (jobId !== currentSpeechJobId) return;
                            console.warn('TTS chunk error:', e);
                            i++;
                            speakChunk();
                        };
                        window.speechSynthesis.speak(utterance);
                    }
                }
                speakChunk();
            }
        }

        function toggleTTS() {
            ttsEnabled = !ttsEnabled;
            localStorage.setItem('tts', ttsEnabled);
            document.getElementById('tts-toggle')?.classList.toggle('active', ttsEnabled);
            announce(ttsEnabled ? 'Text to speech enabled' : 'Text to speech disabled');
            if (ttsEnabled) {
                speak('Text to speech enabled');
            } else {
                window.speechSynthesis.cancel();
            }
        }

        // High Contrast
        function toggleHighContrast() {
            document.documentElement.classList.toggle('high-contrast');
            const isHC = document.documentElement.classList.contains('high-contrast');
            localStorage.setItem('highContrast', isHC);
            document.getElementById('contrast-toggle')?.classList.toggle('active', isHC);
            announce(isHC ? 'High contrast mode enabled' : 'High contrast mode disabled');
        }

        // Announce to screen readers
        function announce(message) {
            const el = document.getElementById('aria-live');
            if (el) {
                el.textContent = message;
                setTimeout(() => { el.textContent = ''; }, 3000);
            }
        }

        // Shortcuts Modal
        function openShortcutsModal() {
            const modal = document.getElementById('shortcuts-modal');
            modal.classList.add('active');
            modal.querySelector('button')?.focus();
            announce('Shortcut help opened');
        }

        function closeShortcutsModal() {
            document.getElementById('shortcuts-modal').classList.remove('active');
            announce('Shortcut help closed');
        }

        // Web Audio API for Sound Effects
        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        function playSaveBeep() {
            // Coba memutar file audio kustom terlebih dahulu
            const beepAudio = new Audio('{{ asset("audio/save-beep.mp3") }}');
            beepAudio.play().catch(e => {
                // Jika file tidak ditemukan atau gagal diputar, gunakan nada bawaan (synthesized)
                if (audioCtx.state === 'suspended') {
                    audioCtx.resume();
                }
                const oscillator = audioCtx.createOscillator();
                const gainNode = audioCtx.createGain();
                
                oscillator.type = 'sine';
                oscillator.frequency.setValueAtTime(880, audioCtx.currentTime); // A5 note
                oscillator.frequency.exponentialRampToValueAtTime(1760, audioCtx.currentTime + 0.1); // Slide up to A6
                
                gainNode.gain.setValueAtTime(0, audioCtx.currentTime);
                gainNode.gain.linearRampToValueAtTime(0.3, audioCtx.currentTime + 0.05); // Fade in
                gainNode.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.2); // Fade out
                
                oscillator.connect(gainNode);
                gainNode.connect(audioCtx.destination);
                
                oscillator.start(audioCtx.currentTime);
                oscillator.stop(audioCtx.currentTime + 0.2);
            });
        }

        // Global keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            // Don't trigger when typing in inputs
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;

            // Ctrl combinations (zoom handled by browser natively)
            if (e.ctrlKey) {
                if (e.key === 'u' || e.key === 'U') { e.preventDefault(); toggleHighContrast(); }
                return;
            }

            // V for Toggle TTS (Voice)
            if (!e.ctrlKey && !e.altKey && !e.metaKey && (e.key === 'v' || e.key === 'V')) {
                e.preventDefault();
                toggleTTS();
                return;
            }

            // H for help
            if (e.key === 'h' || e.key === 'H') {
                e.preventDefault();
                openShortcutsModal();
            }

            // Escape
            if (e.key === 'Escape') {
                closeShortcutsModal();
            }
        });
    </script>
    @yield('scripts')
</body>
</html>
