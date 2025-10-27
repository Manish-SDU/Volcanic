@extends('layouts.app')

@section('title', 'Log In')

@section('body_class', 'auth-page')

@section('head_js')
    @vite('resources/js/login/login.js')
@endsection

@section('content')
    <!-- Log In -->
    <main class="login-container">
        <section class="login-card" aria-labelledby="loginTitle">
            <div class="text">
                Log In
            </div>

            {{-- Validation errors --}}
            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login.perform') }}" method="POST" novalidate>
                @csrf

                <div class="login-data">
                    <label for="username">Username</label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        value="{{ old('username') }}"
                        required
                        autocomplete="username"
                    >
                </div>

                <div class="login-data password-wrapper">
                    <label for="password">Password</label>
                    <div class="password-field">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            required
                            autocomplete="current-password"
                        >

                        <button
                            type="button"
                            id="togglePassword"
                            class="password-toggle"
                            aria-label="Mostra password"
                            aria-pressed="false"
                            title="Mostra password"
                        >
                            <!-- eye -->
                            <svg class="icon-eye" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12Z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>

                            <!-- eye-off -->
                            <svg class="icon-eye-off" style="display:none" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M17.94 17.94A10.94 10.94 0 0 1 12 19c-7 0-11-7-11-7a21.77 21.77 0 0 1 5.06-5.94M9.9 4.24A10.93 10.93 0 0 1 12 5c7 0 11 7 11 7a21.8 21.8 0 0 1-3.06 3.88"/>
                                <path d="M1 1l22 22" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="forgot-password">
                    <a href="{{ route('password.request') }}">Forgot Password?</a>
                </div>

                <div class="loginpage-btn">
                    <button type="submit">Log In</button>
                </div>

                <div class="signup-link">
                    <label>Not a member?</label>
                    <a href="{{ route('register.show') }}">Sign-Up now</a>
                </div>
            </form>
        </section>
    </main>
@endsection
