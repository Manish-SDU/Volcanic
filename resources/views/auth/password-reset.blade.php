@extends('layouts.app')

@section('title', 'Reset Password')

@section('body_class', 'auth-page')

@section('content')
    <main class="login-container">
        <section class="login-card" aria-labelledby="resetPasswordTitle">
            <div class="text" id="resetPasswordTitle">
                ♻️ Reset Password
            </div>

            <p class="register-subtitle">Use your username and date of birth to verify your account.</p>

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

            <form action="{{ route('password.reset') }}" method="POST" novalidate class="login-form">
                @csrf

                <div class="login-data">
                    <label for="username">
                        <i class="fas fa-user"></i>
                        Username
                    </label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        value="{{ old('username') }}"
                        required
                        autocomplete="username"
                        placeholder="Enter your username"
                        class="form-input"
                    >
                </div>

                <div class="login-data">
                    <label for="date_of_birth">
                        <i class="fas fa-birthday-cake"></i>
                        Date of Birth
                    </label>
                    <input
                        type="date"
                        id="date_of_birth"
                        name="date_of_birth"
                        value="{{ old('date_of_birth') }}"
                        required
                        autocomplete="bday"
                        class="form-input"
                    >
                    <small class="hint">Must match the date of birth on your profile.</small>
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
                        <span class="btn-text">Reset Password</span>
                        <span class="btn-loader">
                            <i class="fas fa-spinner fa-spin"></i>
                        </span>
                    </button>
                </div>

                <div class="signup-link">
                    <span>Remembered your password?</span>
                    <a href="{{ route('login') }}" class="login-anchor">
                        <strong>Log In</strong>
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
