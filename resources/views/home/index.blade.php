@extends('layouts.app')

@section('title', 'Home')

@section('additional_css')
    <!-- CSS for Volcano Animation -->
    <link rel="stylesheet" href="{{ asset('css/volcano.css') }}">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
        crossorigin="" />
    <!-- Map Styles -->
    <link rel="stylesheet" href="{{ asset('css/volcano-map.css') }}">
    <style>
        #filter-pill:hover {
            background: #ff8c00 !important;
            color: white !important;
            border-color: #ff8c00 !important;
        }
        #map-toggle-pill:hover {
            background: #3498db !important;
            color: white !important;
            border-color: #3498db !important;
        }
    </style>
@endsection

@section('head_js')
    <!-- Dependencies -->
    <script src="https://unpkg.com/react@17/umd/react.development.js"></script>
    <script src="https://unpkg.com/react-dom@17/umd/react-dom.development.js"></script>
    <!-- Leaflet JS for Interactive Map -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""></script>
    <script src="{{ asset('js/volcano.js') }}" defer></script>
    <script src="{{ asset('js/home.js') }}" defer></script>
    <!-- Volcano Map JavaScript -->
    <script src="{{ asset('js/volcano-map.js') }}" defer></script>
@endsection

@section('content')
    <!-- Hero Section -->
    <section class="hero-section" id="hero-carousel">
        <!-- Carousel Background Images -->
        <div class="carousel-backgrounds">
            <div class="carousel-bg active" data-bg="url('{{ asset('images/navbar/volcano-banner_1.jpg') }}')"></div>
            <div class="carousel-bg" data-bg="url('{{ asset('images/navbar/volcano-banner_2.jpg') }}')"></div>
            <div class="carousel-bg" data-bg="url('{{ asset('images/navbar/volcano-banner_3.jpg') }}')"></div>
            <div class="carousel-bg" data-bg="url('{{ asset('images/navbar/volcano-banner_4.jpg') }}')"></div>
        </div>

        <!-- Carousel Indicators -->
        <div class="carousel-indicators">
            <button class="indicator active" data-slide="0" aria-label="Slide 1"></button>
            <button class="indicator" data-slide="1" aria-label="Slide 2"></button>
            <button class="indicator" data-slide="2" aria-label="Slide 3"></button>
            <button class="indicator" data-slide="3" aria-label="Slide 4"></button>
        </div>

        <div class="container">
            <!-- Main Heading -->
            <h1 class="hero-title">
                The <i class="fa-solid fa-volcano"></i> volcano<br>
                <span class="exploration-line">
                    <button id="searchToggle" class="search-icon-inline"><i
                            class="fa-solid fa-magnifying-glass"></i></button>
                    <span class="word-part">exploration platform</span>
                </span>
            </h1>

            <!-- Description -->
            <p id="heroDescription" class="hero-description">
                Discover extraordinary volcanic destinations or revisit your favorite eruptions
            </p>

            <!-- Hidden Search Bar -->
            <div id="heroSearchBar" class="hero-search-bar hidden">
                <div class="hero-search-input">
                    <input type="text" placeholder="Enter volcano name or country...">
                </div>
            </div>

            <!-- Github Button -->
            <div class="hero-actions">
                <div class="hero-badge">
                    <span>MADE BY THE WONDERFUL GROUP 3 - </span>
                    <strong><a href="https://github.com/Manish-SDU/Volcanic.git" target="_blank"
                            class="github-link" title="Visit our GitHub repository">Volcanic <i class="fa-brands fa-github"></i></a></strong>
                </div>
            </div>
        </div>
    </section>

    <!-- Content Sections -->
    <section class="content-section">
        <div class="container">
            <div style="position: relative;">
                <h2>Discover Amazing Volcanoes</h2>
                <p id="search-status" class="section-description">Search by name or location</p>
                <div style="position: absolute; top: 0; right: 0; display: flex; gap: 12px;">
                    <span id="filter-pill" class="action-btn"
                          style="border-color: #ff8c00; color: #ff8c00; min-width: 85px; justify-content: center;">
                        <i class="fas fa-filter"></i>
                        <span>Filter</span>
                    </span>
                    <span id="map-toggle-pill" onclick="toggleMap()" class="action-btn"
                          style="border-color: #3498db; color: #3498db; min-width: 130px; justify-content: center; white-space: nowrap;">
                        <i class="fas fa-map"></i>
                        <span id="map-pill-text">View Map</span>
                    </span>
                </div>
            </div>
            
            <!-- Interactive Map Container -->
            <div id="volcano-map" style="width: 100%; height: 500px; border-radius: 16px; display: none; margin-bottom: 2rem;"></div>
            <div id="map-description" style="display: none; text-align: center; margin-bottom: 1rem; color: var(--text-secondary); font-style: italic;">
                Explore the locations of volcanoes around the world. Click on markers to discover each volcano's story.
            </div>
            <div id="no-results-message" class="hidden">
                <div class="empty-state">
                    <i class="fas fa-search fa-3x"></i>
                    <p>No "<span id="search-term"></span>" found in our database</p>
                </div>
            </div>
            <div class="volcano-grid">
                @foreach($volcanoes as $index => $volcano)
                <article class="volcano-card {{ $index >= 6 ? 'homepage-hidden' : '' }}" data-volcano-id="{{ $volcano->id }}" data-card-index="{{ $index }}">
                    <div class="volcano-image-container">
                        <!-- Lazy loaded image with delayed placeholder fallback -->
                        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='1' height='1'%3E%3C/svg%3E" 
                            data-src="{{ $volcano->safe_image_url }}" 
                            data-placeholder="{{ asset('images/volcanoes/placeholder.png') }}"
                            alt="{{ $volcano->name }} volcano"
                            class="lazy-image volcano-img">
                        
                        <!-- Volcano details overlay on image -->
                        <div class="volcano-details-overlay">
                            <div class="detail-simple">
                                <i class="fas fa-mountain"></i> {{ $volcano->type }}
                            </div>
                            <div class="detail-simple">
                                <i class="fas fa-globe-americas"></i> {{ $volcano->country }}
                            </div>
                            <div class="detail-simple">
                                <i class="fas fa-arrow-up"></i> {{ number_format($volcano->elevation) }}m
                            </div>
                        </div>
                    </div>
                    <div class="volcano-content">
                        <div class="volcano-header">
                            <h3 class="volcano-title">{{ $volcano->name }}</h3>
                            <div class="volcano-status">
                                <span class="status-badge {{ strtolower($volcano->activity) }}">
                                    @if(strtolower($volcano->activity) == 'active')
                                        <i class="fas fa-fire"></i>
                                    @elseif(strtolower($volcano->activity) == 'dormant')
                                        <i class="fas fa-clock"></i>
                                    @else
                                        <i class="fas fa-moon"></i>
                                    @endif
                                    {{ $volcano->activity }}
                                </span>
                            </div>
                        </div>
                        
                                                <div class="volcano-actions">
                            <button class="action-btn visited-btn" data-volcano="{{ $volcano->id }}">
                                <i class="fas fa-check"></i>
                                <span>Visited</span>
                            </button>
                            <button class="action-btn wishlist-btn" data-volcano="{{ $volcano->id }}">
                                <i class="fas fa-heart"></i>
                                <span>Wishlist</span>
                            </button>
                        </div>
                        
                        <!-- Interactive details container -->
                        <div class="details-container">
                            <!-- Fun hover message -->
                            <div class="hover-message">
                                @php
                                    $hoverMessages = [
                                        "🌋 Hover over me to see some hot details!",
                                        "👆 Psst... hover here to discover my secrets!",
                                        "🔍 Want to know more? Just hover over me!",
                                        "✨ I'm hiding some cool facts - hover to reveal!",
                                        "🏔️ Hover here to explore my volcanic features!",
                                        "🎯 Hover over me for the volcanic scoop!"
                                    ];
                                    $randomMessage = $hoverMessages[array_rand($hoverMessages)];
                                    
                                    $hoverActiveMessages = [
                                        "📸 Check above for my stats! Click below for the juicy volcanic gossip! 🌋",
                                        "⬆️ My details are up there! Hit that button below for more hot facts! 🔥",
                                        "✨ Stats revealed above! Click below to dive deeper into my volcanic drama! 📚",
                                        "🎯 Quick facts shown above! Button below unlocks my volcanic biography! 📖",
                                        "🏔️ Basic info up top! Click below for the full volcanic tea! ☕",
                                        "🌋 Mini-stats above! Button below = volcanic knowledge explosion! 💥"
                                    ];
                                    $randomActiveMessage = $hoverActiveMessages[array_rand($hoverActiveMessages)];
                                @endphp
                                <p class="default-message"><i class="fas fa-mouse-pointer"></i> {{ $randomMessage }}</p>
                                <p class="hover-active-message"><i class="fas fa-arrow-down"></i> {{ $randomActiveMessage }}</p>
                            </div>
                        </div>
                        
                        <div class="volcano-footer">
                            <button class="primary-btn learn-more-btn" data-volcano="{{ $volcano->id }}">
                                <i class="fas fa-info-circle"></i>
                                Learn More
                            </button>
                        </div>
                    </div>
                </article>
                @endforeach
            </div>
            
            <!-- Fun message for more volcanoes -->
            <div class="more-volcanoes-message">
                <div class="fun-message-container">
                    <h3 class="fun-title">🌋 Looking for more volcanoes?</h3>
                    <p class="fun-text">
                        We have hundreds more waiting to be discovered. <a href="#hero-carousel" class="search-link">Just search above</a> to explore our full collection!
                    </p>
                </div>
            </div>
        </div>
    </section>



    <!-- Volcano Animation -->
    <section id="volcano-container"></section>
@endsection