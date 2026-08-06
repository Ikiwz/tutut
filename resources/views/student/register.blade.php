@extends('layouts.student')
@section('title', 'Daftar Akun')

@section('styles')
<style>
    .login-page {
        min-height: calc(100vh - 64px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 24px;
        background: linear-gradient(135deg, #eff6ff 0%, #f8fafc 50%, #f0f9ff 100%);
    }

    .high-contrast .login-page {
        background: var(--bg);
    }

    .login-card {
        max-width: 440px;
        width: 100%;
    }

    .login-header {
        text-align: center;
        margin-bottom: 32px;
    }

    .login-header h1 {
        font-size: 1.75rem;
        font-weight: 800;
        color: var(--text);
        margin-bottom: 8px;
    }

    .login-header p {
        color: var(--text-muted);
        font-size: 0.9375rem;
    }

    .login-icon {
        width: 56px;
        height: 56px;
        background: var(--primary);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
    }

    .login-icon svg {
        width: 28px;
        height: 28px;
        color: #fff;
    }

    .login-footer {
        text-align: center;
        margin-top: 20px;
        font-size: 0.8125rem;
        color: var(--text-light);
    }

    .login-footer a {
        color: var(--primary);
        text-decoration: none;
        font-weight: bold;
    }
</style>
@endsection

@section('content')
<div class="login-page">
    <div class="login-card">
        <div class="card">
            <div class="login-header">
                <div class="login-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                </div>
                <h1>Daftar Akun Baru</h1>
                <p>Platform ujian TOEFL untuk tunanetra</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-error" role="alert">
                    <span aria-hidden="true">⚠️</span>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('student.register.submit') }}" aria-label="Form pendaftaran peserta">
                @csrf
                <div class="form-group">
                    <label for="name" class="form-label">Nama Lengkap</label>
                    <input type="text" id="name" name="name" class="form-input"
                           value="{{ old('name') }}" required autofocus
                           placeholder="Masukkan nama lengkap Anda">
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" class="form-input"
                           value="{{ old('email') }}" required
                           placeholder="Masukkan email Anda">
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-input"
                           required placeholder="Minimal 8 karakter">
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-input"
                           required placeholder="Ketik ulang password">
                </div>

                <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; justify-content: center;">
                    Daftar Sekarang
                </button>
            </form>
        </div>

        <div class="login-footer">
            Sudah punya akun? <a href="{{ route('student.login') }}">Masuk di sini</a>
        </div>
    </div>
</div>
@endsection
