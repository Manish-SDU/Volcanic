@extends('layouts.app')

@section('title', 'Profile')

@section('head_js')
    @vite('resources/js/profile/profile.js')
@endsection

@section('content')
    <!-- Main Content -->
    <main class="container" style="padding: 2rem 0; max-width: 900px;">
    <!-- Profile Box (taken inspiration from volcano cards) -->
    <section class="panel" aria-labelledby="profileTitle">
      <h3 id="profileTitle">My Profile</h3>
      <br>
      <!-- Header: avatar + identity + action -->
      <header class="profile-header">

        <!-- Delete Button -->
        <form action="{{ route('profile.destroy') }}" method="POST" class="delete-form">
          @csrf
          @method('DELETE')
          <button type="submit" class="delete-btn" aria-label="Delete account">
            <i class="fa-solid fa-trash"></i> Delete
          </button>
        </form>

        <div class="profile-ident">
          {{-- Avatar: using a default image for now (since pfp is not stored) --}}
          <img id="userPhoto" class="profile-avatar" alt="User photo"
               src="{{ asset('images/users/default1.jpg') }}"/>

          <div id="userHandle" class="profile-handle">
            {{ $user->username }}
          </div>
        </div>

        <!-- Edit Button -->
        <a class="edit-btn" href="{{ route('profile.edit') }}" aria-label="Edit profile">
          <i class="fa-solid fa-pen"></i> Edit
        </a>

      </header>
      
      <!-- Bio section -->
      <div id="userBio" class="profile-bio">
        {{ $user->bio ?? 'No bio yet.' }}
      </div>
      <br>

      <!-- Basic info -->
      <dl class="profile-dl">
        <dt>Name</dt>
        <dd id="Name">
          {{ $user->name }}{{ $user->surname ? ' ' . $user->surname : '' }}
        </dd>

        <dt>Member since</dt>
        <dd id="memberSince">
          {{ optional($user->created_at)->format('d/m/Y') ?? '—' }}
        </dd>

        <dt>Where from</dt>
        <dd id="whereFrom">
          {{ $user->where_from ?? '—' }}
        </dd>

        @if($user->date_of_birth)
        <dt>Date of birth</dt>
        <dd id="dob">
          {{ \Carbon\Carbon::parse($user->date_of_birth)->format('d/m/Y') }}
        </dd>
        @endif
      </dl>
      <br><br>

      {{-- Progress bar --}}
      <div class="progress-container">
        <label class="progresslabel" for="milestone">🎯 Progress until next Visit Milestone:</label>
        <div class="progress-wrapper">
          <progress class="progressbar" id="milestone" value="{{ $visitCount }}" max="{{ $nextMilestone }}"></progress>
          <span class="progress-text">{{ $visitCount }}/{{ $nextMilestone }}</span>
        </div>
      </div>
    </section>

    <!-- Achievement Panel (still static, to be implemented) -->
    <section class="panel" aria-labelledby="achievementsTitle">
      <h2 id="achievementsTitle" class="achievementTitle">Achievements</h2>

      <!-- Obtained Achievements -->
      <ul class="achievement-grid badge-container">
        @forelse($unlockedAchievements as $achievement)
        <li class="badgedesc">
          <img src="{{ asset($achievement->image_path) }}" alt="Badge for {{ $achievement->description }}">
          <h4>{{ $achievement->name }}</h4>
          <p class="long-desc">{{ $achievement->description }}</p>
        </li>
        @empty
        <li class="badgedesc no-achievements">
          <p>No achievements unlocked yet. <br> Start exploring volcanoes to earn badges!</p>
        </li>
        @endforelse
      </ul>

      <button id="toggleAchievements" class="togglebutton" aria-expanded="false" aria-controls="achievementsList">
        Show All Achievements
      </button>

      <!-- Locked Achievements -->
      <ul id="lockedBadges" class="achievement-grid badge-container">
        @foreach($lockedAchievements as $achievement)
        <li class="badgedesc">
          <img src="{{ asset(str_replace('.png', ' Locked.png', $achievement->image_path)) }}" 
               alt="Badge for {{ $achievement->description }}">
          <h4>{{ $achievement->name }}</h4>
          <p class="long-desc">{{ $achievement->description }}</p>
        </li>
        @endforeach
      </ul>
    </section>
    
    {{-- Delete Confirmation Modal --}}
    <div id="deleteModal" class="modal-overlay">
      <div class="modal-content">
        <div class="modal-icon warning">
          <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <h3>Delete Account?</h3>
        <p>Are you sure you want to delete your account?</p>
        <p class="modal-warning">
          This action is <strong>irreversible</strong>. All your data will be permanently deleted:
        </p>
        <ul class="modal-list">
          <li><i class="fa-solid fa-circle-check"></i> Visited volcanoes</li>
          <li><i class="fa-solid fa-circle-check"></i> Wishlist items</li>
          <li><i class="fa-solid fa-circle-check"></i> Achievements</li>
          <li><i class="fa-solid fa-circle-check"></i> Profile information</li>
        </ul>
        <div class="modal-actions">
          <button type="button" id="cancelDelete" class="modal-btn cancel-btn">
            <i class="fa-solid fa-xmark"></i> Cancel
          </button>
          <button type="button" id="confirmDelete" class="modal-btn delete-btn-modal">
            <i class="fa-solid fa-trash"></i> Delete Forever
          </button>
        </div>
      </div>
    </div>
  </main>
@endsection