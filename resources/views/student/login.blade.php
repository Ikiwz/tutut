@extends('layouts.student')
@section('title', 'Login')

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

    .login-hint {
        margin-top: 20px;
        padding: 14px;
        background: rgba(37, 99, 235, 0.05);
        border: 1px solid rgba(37, 99, 235, 0.15);
        border-radius: var(--radius);
        font-size: 0.8125rem;
        color: var(--text-muted);
        line-height: 1.6;
    }

    .login-hint strong {
        color: var(--text);
    }

    .login-footer {
        text-align: center;
        margin-top: 20px;
        font-size: 0.8125rem;
        color: var(--text-light);
    }

    .login-footer kbd {
        margin: 0 2px;
    }
</style>
@endsection

@section('content')
<div class="login-page">
    <div class="login-card">
        <div class="card">
            <div class="login-header">
                <img src="{{ asset('images/logo.png') }}" alt="TUTUT Logo" class="login-icon-img" style="height: 64px; width: auto; margin: 0 auto 20px; display: block;" aria-hidden="true">
                <h1>Log in to TUTUT</h1>
                <p>Accessible TOEFL exam platform for visually impaired students</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-error" role="alert">
                    <span aria-hidden="true">⚠️</span>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('student.login.submit') }}" aria-label="Student login form">
                @csrf
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" class="form-input"
                           value="{{ old('email') }}" required autofocus
                           aria-describedby="email-help"
                           placeholder="Enter your email">
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-input"
                           required placeholder="Enter your password">
                </div>

                <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; justify-content: center;">
                    Log in
                </button>
            </form>

            <div class="login-hint" aria-label="Demo account information">
                <strong>Demo Account:</strong><br>
                Email: <code>student@toefl.com</code><br>
                Password: <code>password</code>
            </div>
            
            <div style="text-align: center; margin-top: 16px;">
                Don't have an account? <a href="{{ route('student.register') }}" style="color: var(--primary); font-weight: bold; text-decoration: none;">Register here</a>
            </div>
        </div>

        <div class="login-footer">
            Press <kbd>H</kbd> for keyboard shortcuts help
        </div>
    </div>
</div>
@endsection
