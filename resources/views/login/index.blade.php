@extends('layouts.app')

@section('title', 'Log In')

@section('content')
    <!-- Log In -->
    <main class="login-container">
        <section class="login-card" aria-labelledby="loginTitle">
            <div class="text">
                Log In
            </div>

            <form action="{{ route('login.process') }}" method="POST">
                @csrf
                <div class="login-data">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="login-data">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>

                <div class="forgot-password">
                    <a href="{{ route('password.request') }}">Forgot Password?</a>
                </div>

                <div class="loginpage-btn">
                    <button type="submit">Log In</button>
                </div>

                <div class="signup-link">
                    <label for="">Not a member?</label>
                    <a href="{{ route('register') }}">Sign-Up now</a>
                </div>
            </form>
        </section>
    </main>
@endsection