@extends('layouts.app')

@section('title', 'Profile')

@section('head_js')
    <script src="{{ asset('js/profile.js') }}" defer></script>
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
        
        <div class="profile-ident">
          <img id="userPhoto" class="profile-avatar" alt="User photo" src="{{ asset('images/users/default1.jpg') }}"/>
          <div id="userName" class="profile-name">User Name</div>
          <div id="userHandle" class="profile-handle">@Marcy</div>
        </div>

        <!-- Edit Button -->
        <button class="edit-btn">✏️ Edit</button>
      </header>
      
      <!-- Bio section -->
      <div id="userBio" class="profile-bio">Explorer of volcanoes and collector of unforgettable sunsets 🌋🌅</div>
      <br>

      <!-- Basic info -->
      <dl class="profile-dl">
        <dt>Name</dt>
        <dd id="Name">Marcus Hermansen</dd>

        <dt>Member since</dt>
        <dd id="memberSince">03/04/2023</dd>

        <dt>Where from</dt>
        <dd id="memberSince">Aalborg (DK)</dd>
        
      </dl>
      <br>
      <br>
      <div class="progress-container">
        <label class="progresslabel" for="milestone">🎯 Progress until next Visit Milestone:</label>
          <div class="progress-wrapper">
            <progress class="progressbar" id="milestone" value="1" max="5"></progress>
            <span class="progress-text">1/5</span>
          </div>
      </div>
    </section>

    <!-- Achievement Panel -->
    <section class="panel" aria-labelledby="achievementsTitle">
      <h2 id="achievementsTitle" class="achievementTitle">Achievements</h2>

      <!-- Obtained Achievements -->
      <ul class="achievement-grid badge-container">
        <li class="badgedesc">
          <img src="{{ asset('images/badges/First Eruption.png') }}" alt="Badge for visiting your first volcano.">
          <h4>First Eruption</h4>
          <p class="long-desc">Visit your first volcano.</p>
        </li>
        <li class="badgedesc">
          <img src="{{ asset('images/badges/Dormant Dreamer.png') }}" alt="Badge for visiting an extinct volcano.">
          <h4>Dormant Dreamer</h4>
          <p class="long-desc">Visit an extinct volcano.</p>
        </li>
      </ul>
      <button id="toggleAchievements" class="togglebutton" aria-expanded="false" aria-controls="achievementsList">
        Show All Achievements
      </button>

      <!-- Locked Achievements -->
      <ul id="lockedBadges" class="achievement-grid badge-container">
        <li class="badgedesc">
          <img src="{{ asset('images/badges/Lava Rookie Locked.png') }}" alt="Badge for exploring 5 volcanoes.">
          <h4>Lava Rookie</h4>
          <p class="long-desc">Visit 5 volcanoes.</p>
        </li>
        <li class="badgedesc">
          <img src="{{ asset('images/badges/Ash Walker Locked.png') }}" alt="Badge for exploring 10 volcanoes.">
          <h4>Ash Walker</h4>
          <p class="long-desc">Visit 10 volcanoes.</p>
        </li>
        <li class="badgedesc">
          <img src="{{ asset('images/badges/Volcano Veteran Locked.png') }}" alt="Badge for visiting 25 volcanoes.">
          <h4>Volcano Veteran</h4>
          <p class="long-desc">Visit 25 volcanoes.</p>
        </li>
        <li class="badgedesc">
          <img src="{{ asset('images/badges/Magma Master locked.png') }}" alt="Badge for exploring 50+ volcanoes.">
          <h4>Magma Master</h4>
          <p class="long-desc">Visit 50+ volcanoes.</p>          
        </li>
        <li class="badgedesc">
          <img src="{{ asset('images/badges/Explorer Locked.png') }}" alt="Badge for exploring a volcano on each continent.">
          <h4>Explorer</h4>
          <p class="long-desc">Visit a volcano on each continent.</p>
        </li>
        <li class="badgedesc">
          <img src="{{ asset('images/badges/Lava Lover Locked.png') }}" alt="Badge for visiting an active volcano with lava.">
          <h4>Lava Lover</h4>
          <p class="long-desc">Visit an active volcano.</p>
        </li>
        <li class="badgedesc">
          <img src="{{ asset('images/badges/Polar Pioneer Locked.png') }}" alt="Badge for exploring a volcano in/near the Artic/Antartica.">
          <h4>Polar Pioneer</h4>
          <p class="long-desc">Visit a volcano in/near the Arctic/Antarctic.</p>
        </li>
        <li class="badge">
          <img src="{{ asset('images/badges/Ring of Fire Explorer Locked.png') }}" alt="Badge for visiting a volcano along the Pacific Ring of Fire.">
          <h4>Ring of Fire Explorer</h4>
          <p class="long-desc">Visit a volcano along the Pacific Ring of Fire.</p>
        </li>
      </ul>

    </section>
  </main>
@endsection