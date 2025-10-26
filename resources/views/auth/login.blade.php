@extends('layouts.app')

@section('title', 'Log In')

@section('body_class', 'auth-page')

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

                <div class="login-data">
                    <label for="password">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        autocomplete="current-password"
                    >
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
