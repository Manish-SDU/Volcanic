@extends('layouts.app')

@section('title', 'My Volcanoes')

@section('head_js')
    <script src="{{ asset('js/panels.js') }}" defer></script>
    <script src="{{ asset('js/number_increment.js') }}" defer></script>
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
    <!-- panels -->
    <section id="visited" class="myv-panel">
        <h2>Visited Volcanoes</h2>
        <p>No volcanoes visited yet. Start your volcanic adventure by exploring and marking volcanoes as visited on the home page.</p>
        <a href="{{ route('home') }}">Explore Volcanoes</a>

        <!-- arrow button -->
        <button class="next-btn" onclick="nextPanel()">→</button>
        <div class="next-tooltip tooltip">Wish list</div>
    </section>

    <section id="wish" class="myv-panel" style="display:none">
        <h2>Wish to Visit</h2>
        <p>Add volcanoes you'd like to visit in the future.</p>
        <a href="{{ route('home') }}">Explore Volcanoes</a>

        <!-- arrow button (loops back to visited) -->
        <button class="prev-btn" onclick="nextPanel()">←</button>
        <div class="prev-tooltip tooltip">Visited list</div>
    </section>
@endsection