@extends('layouts.app')

@section('title', 'My Volcanoes')

@section('head_js')
    @vite(['resources/js/my-volcanoes/panels.js', 'resources/js/my-volcanoes/number_increment.js', 'resources/js/my-volcanoes/render.js'])
@endsection

@section('content')
    <header class="myv-header">
        <h1>My Visited Volcanoes</h1>
        <p>Track your volcanic adventures and discoveries</p>
    </header>
    
    <!-- Stats -->
    <section class="statistics-section">
        <header>
            <h2 class ="statistics-title">
                <i class="fa-solid fa-chart-bar"></i>
                Your Volcano Statistics
            </h2>
        </header>
        <div class="statistics-grid">
            <dl class="stat-box">
                <dt class="stat-label">Volcanoes Visited</dt>
                <dd class="stat-value" id="visited-value">33</dd>
            </dl>
            <dl class="stat-box">
                <dt class="stat-label">Countries Explored</dt>
                <dd class="stat-value" id="countries-value">4</dd>
            </dl>
            <dl class="stat-box">
                <dt class="stat-label">Active Volcanoes</dt>
                <dd class="stat-value" id="active-value">1000</dd>
            </dl>
            <dl class="stat-box">
                <dt class="stat-label">Inactive Volcanoes</dt>
                <dd class="stat-value" id="inactive-value">1000</dd>
            </dl>
        </div>
    </section>

    <!-- Visited Volcanoes -->
     <!-- Template for volcano cards -->
    <template id="volcano-card-template">
        <div class="volcano-card">
            <div class="image-container">
                <img src="" alt="" class="volcano-thumb">
            </div>
            <div class="card-content">
                <h3></h3>
                <p class="country"></p>
                <button class="remove-btn" data-type="" data-id="">Remove</button>
            </div>
        </div>
    </template>

    <!-- panels -->
    <!-- Visited Volcanoes -->
    <section id="visited" class="myv-panel">
        <h2>Visited Volcanoes</h2>
        <div class="volcano-grid">
            <div class="empty-state" style="display: none">
                <p>No volcanoes visited yet</p>
                <a href="{{ route('home') }}">Explore Volcanoes</a>
            </div>
            <div class="volcanoes-container"></div>
        </div>

        <button class="next-btn" onclick="nextPanel()">→</button>
        <div class="next-tooltip tooltip">Wish list</div>
    </section>

    <section id="wish" class="myv-panel" style="display:none">
        <h2>Wish to Visit</h2>
        <div class="volcano-grid">
            <div class="empty-state" style="display: none">
                <p>Your wishlist is empty</p>
                <a href="{{ route('home') }}">Explore Volcanoes</a>
            </div>
            <div class="volcanoes-container"></div>
        </div>

        <button class="prev-btn" onclick="nextPanel()">←</button>
        <div class="prev-tooltip tooltip">Visited list</div>
    </section>
@endsection