@extends('layouts.app')

@section('title', 'Sign Up')

@section('body_class', 'auth-page')

@section('content')
    <main class="login-container">
        <section class="login-card" aria-labelledby="registerTitle">
            <div class="text">
                Sign Up
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

            <form action="{{ route('register.store') }}" method="POST" novalidate>
                @csrf

                <div class="login-data">
                    <label for="name">Name *</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required autocomplete="given-name">
                </div>

                <div class="login-data">
                    <label for="surname">Surname</label>
                    <input type="text" id="surname" name="surname" value="{{ old('surname') }}" autocomplete="family-name">
                </div>

                <div class="login-data">
                    <label for="username">Username *</label>
                    <input type="text" id="username" name="username" value="{{ old('username') }}" required autocomplete="username">
                </div>

                <div class="login-data">
                    <label for="date_of_birth">Date of birth</label>
                    <input type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}" autocomplete="bday">
                </div>

                <div class="login-data">
                    <label for="where_from">Country</label>
                    <input type="text" id="where_from" name="where_from" value="{{ old('where_from') }}" autocomplete="country-name">
                </div>

                <div class="login-data">
                    <label for="bio">Bio</label>
                    <textarea id="bio" name="bio" rows="4">{{ old('bio') }}</textarea>
                </div>

                <div class="login-data">
                    <label for="password">Password *</label>
                    <input type="password" id="password" name="password" required autocomplete="new-password">
                </div>

                <div class="login-data">
                    <label for="password_confirmation">Confirm password *</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
                </div>

                <div class="loginpage-btn">
                    <button type="submit">Create Account</button>
                </div>

                <div class="signup-link">
                    <label>Already have an account?</label>
                    <a href="{{ route('login') }}">Log In</a>
                </div>
            </form>
        </section>
    </main>
@endsection
