@extends('layouts.app')

@section('title', 'Sign Up')

@section('body_class', 'auth-page')

@section('content')
    <main class="login-container">
        <section class="login-card register-card" aria-labelledby="registerTitle">
            <div class="text">
                🌋 Create Your Account
            </div>
            
            <p class="register-subtitle">Join us to explore the world's volcanoes</p>

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

            <form action="{{ route('register.store') }}" method="POST" novalidate class="register-form">
                @csrf

                <div class="form-row">
                    <div class="login-data form-col">
                        <label for="name">
                            <i class="fas fa-user-circle"></i>
                            Full Name <span class="required">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            value="{{ old('name') }}" 
                            required 
                            autocomplete="given-name"
                            class="form-input"
                        >
                    </div>

                    <div class="login-data form-col">
                        <label for="surname">
                            <i class="fas fa-user-tag"></i>
                            Surname
                        </label>
                        <input 
                            type="text" 
                            id="surname" 
                            name="surname" 
                            value="{{ old('surname') }}" 
                            autocomplete="family-name"
                            class="form-input"
                        >
                    </div>
                </div>

                <div class="login-data">
                    <label for="username">
                        <i class="fas fa-at"></i>
                        Username <span class="required">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        value="{{ old('username') }}" 
                        required 
                        autocomplete="username"
                        placeholder="your_username"
                        class="form-input"
                    >
                </div>

                <div class="form-row">
                    <div class="login-data form-col">
                        <label for="date_of_birth">
                            <i class="fas fa-birthday-cake"></i>
                            Date of Birth
                        </label>
                        <input 
                            type="date" 
                            id="date_of_birth" 
                            name="date_of_birth" 
                            value="{{ old('date_of_birth') }}" 
                            autocomplete="bday"
                            class="form-input"
                        >
                    </div>

                    <div class="login-data form-col">
                        <label for="where_from">
                            <i class="fas fa-globe"></i>
                            Country
                        </label>
                        <input 
                            type="text" 
                            id="where_from" 
                            name="where_from" 
                            value="{{ old('where_from') }}" 
                            autocomplete="country-name"
                            placeholder="e.g., Denmark"
                            class="form-input"
                        >
                    </div>
                </div>

                <div class="login-data">
                    <label for="bio">
                        <i class="fas fa-pen-fancy"></i>
                        Bio <span class="hint">(Optional)</span>
                    </label>
                    <textarea 
                        id="bio" 
                        name="bio" 
                        rows="3"
                        placeholder="Write about your passion for volcanoes here!"
                        class="form-input textarea-input"
                    >{{ old('bio') }}</textarea>
                </div>

                <div class="form-divider">
                    <span>Security Information</span>
                </div>

                <div class="form-row">
                    <div class="login-data form-col">
                        <label for="password">
                            <i class="fas fa-lock"></i>
                            Password <span class="required">*</span>
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
                    </div>

                    <div class="login-data form-col">
                        <label for="password_confirmation">
                            <i class="fas fa-key"></i>
                            Confirm Password <span class="required">*</span>
                        </label>
                        <div class="password-field">
                            <input 
                                type="password" 
                                id="password_confirmation" 
                                name="password_confirmation" 
                                required 
                                autocomplete="new-password"
                                placeholder="Confirm your password"
                                class="form-input"
                            >
                            <button type="button" class="password-toggle" data-target="password_confirmation">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="loginpage-btn">
                    <button type="submit" class="login-submit-btn">
                        <span class="btn-text">Create Account</span>
                        <span class="btn-loader">
                            <i class="fas fa-spinner fa-spin"></i>
                        </span>
                    </button>
                </div>

                <div class="signup-link">
                    <span>Already have an account?</span>
                    <a href="{{ route('login') }}" class="login-anchor">
                        <strong>Log In Here</strong>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </form>
        </section>
    </main>

    <script>
        // form same as log in
        document.querySelector('.register-form').addEventListener('submit', function() {
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
