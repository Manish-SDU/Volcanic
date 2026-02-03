@extends('layouts.app')

@section('title', 'Change Password')

@section('body_class', 'auth-page')

@section('content')
    <main class="login-container">
        <section class="login-card" aria-labelledby="changePasswordTitle">
            <div class="text" id="changePasswordTitle">
                🔐 Change Password
            </div>

            {{-- Flash / success --}}
            @if (session('status'))
                <div class="alert alert-success" role="status" aria-live="polite">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Validation errors --}}
            @if ($errors->any())
                <div class="alert alert-danger auth-error" role="alert">
                    <i class="fas fa-exclamation-circle"></i>
                    <div>
                        <strong>Please fix the following errors:</strong>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <form action="{{ route('password.change') }}" method="POST" novalidate class="login-form">
                @csrf

                <div class="login-data">
                    <label for="current_password">
                        <i class="fas fa-lock"></i>
                        Current Password
                    </label>
                    <div class="password-field">
                        <input
                            type="password"
                            id="current_password"
                            name="current_password"
                            required
                            autocomplete="current-password"
                            placeholder="Enter current password"
                            class="form-input"
                        >
                        <button type="button" class="password-toggle" data-target="current_password">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="login-data">
                    <label for="password">
                        <i class="fas fa-key"></i>
                        New Password
                    </label>
                    <div class="password-field">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            required
                            autocomplete="new-password"
                            placeholder="Create a strong password"
                            class="form-input"
                        >
                        <button type="button" class="password-toggle" data-target="password">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <small class="hint">
                        Minimum 12 characters, with upper & lowercase, a number, and a symbol. Avoid common passwords.
                    </small>
                </div>

                <div class="login-data">
                    <label for="password_confirmation">
                        <i class="fas fa-key"></i>
                        Confirm New Password
                    </label>
                    <div class="password-field">
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            required
                            autocomplete="new-password"
                            placeholder="Confirm your new password"
                            class="form-input"
                        >
                        <button type="button" class="password-toggle" data-target="password_confirmation">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="loginpage-btn">
                    <button type="submit" class="login-submit-btn">
                        <span class="btn-text">Update Password</span>
                        <span class="btn-loader">
                            <i class="fas fa-spinner fa-spin"></i>
                        </span>
                    </button>
                </div>

                <div class="signup-link">
                    <a href="{{ route('profile') }}" class="login-anchor">
                        <strong>Back to Profile</strong>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </form>
        </section>
    </main>

    <script>
        // form loading
        document.querySelector('.login-form').addEventListener('submit', function() {
            const btn = this.querySelector('.login-submit-btn');
            btn.classList.add('loading');
            btn.disabled = true;
        });

        // password visibility
        document.querySelectorAll('.password-toggle').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.dataset.target;
                const input = document.getElementById(targetId);
                const icon = this.querySelector('i');
                
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                    this.setAttribute('aria-label', 'Hide password');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                    this.setAttribute('aria-label', 'Show password');
                }
            });
        });
    </script>
@endsection
