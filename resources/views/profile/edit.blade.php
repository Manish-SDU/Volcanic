@extends('layouts.app')

@section('title', 'Edit Profile')

@section('body_class', 'auth-page')

@section('content')
    <main class="login-container">
        <section class="login-card register-card" aria-labelledby="editProfileTitle">
            <div class="text" id="editProfileTitle">
                🛠️ Update Your Profile
            </div>

            <p class="register-subtitle">Keep your info fresh so others can find and recognize you.</p>

            {{-- Flash / success --}}
            @if (session('status'))
                <div class="alert alert-success" role="status" aria-live="polite">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Validation errors (same structure as register) --}}
            @if ($errors->any())
                <div class="alert alert-danger" aria-live="polite">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('profile.update') }}" novalidate>
                @csrf
                @method('PUT')

                {{-- Personal Information --}}
                <div class="form-divider">
                    <span>Personal Information</span>
                </div>

                <div class="form-row">
                    <div class="login-data form-col">
                        <label for="name">
                            <i class="fas fa-user-circle"></i>
                            First Name <span class="required">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            value="{{ old('name', $user->name) }}" 
                            required 
                            autocomplete="given-name"
                            placeholder="Your first name"
                            class="form-input"
                        >
                    </div>

                    <div class="login-data form-col">
                        <label for="surname">
                            <i class="fas fa-user"></i>
                            Surname
                        </label>
                        <input 
                            type="text" 
                            id="surname" 
                            name="surname" 
                            value="{{ old('surname', $user->surname) }}" 
                            autocomplete="family-name"
                            placeholder="Your last name"
                            class="form-input"
                        >
                    </div>
                </div>

                {{-- Account Details --}}
                <div class="form-divider">
                    <span>Account Details</span>
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
                        value="{{ old('username', $user->username) }}" 
                        required 
                        autocomplete="username"
                        placeholder="your_username"
                        class="form-input"
                    >
                    <small class="hint">The username must be unique.</small>
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
                            value="{{ old('date_of_birth', optional(\Carbon\Carbon::parse($user->date_of_birth ?? ''))->format('Y-m-d')) }}"
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
                            value="{{ old('where_from', $user->where_from) }}"
                            placeholder="e.g. Milan, Italy"
                            autocomplete="country-name"
                            class="form-input"
                        >
                    </div>
                </div>

                <div class="login-data">
                    <label for="bio">
                        <i class="fas fa-pen-fancy"></i>
                        Bio
                    </label>
                    <textarea 
                        id="bio" 
                        name="bio" 
                        rows="3" 
                        maxlength="1000" 
                        placeholder="Write about your passion for volcanoes here!"
                        class="form-input"
                        aria-describedby="bioCount"
                    >{{ old('bio', $user->bio) }}</textarea>
                    <small class="hint" id="bioCount">Max 1000 characters.</small>
                </div>

                <div class="form-actions">
                    <a href="{{ route('profile') }}" class="btn btn-secondary" aria-label="Cancel editing and go back">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary" aria-label="Save profile changes">
                        Save Changes
                    </button>
                </div>
            </form>
        </section>
    </main>
@endsection

@push('scripts')
    <script>
        // Mirror register page UX where applicable

        // Bio live character count
        (function () {
            const bio = document.getElementById('bio');
            const counter = document.getElementById('bioCount');
            if (!bio || !counter) return;

            const max = bio.getAttribute('maxlength') ? parseInt(bio.getAttribute('maxlength'), 10) : 1000;
            const update = () => {
                const left = max - bio.value.length;
                counter.textContent = left + ' characters left';
            };
            bio.addEventListener('input', update);
            update();
        })();
    </script>
@endpush
