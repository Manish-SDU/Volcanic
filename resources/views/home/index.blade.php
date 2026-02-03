@extends('layouts.app')

@section('title', 'Home')

@section('additional_css')
    @vite(['resources/css/home/volcano-animation.css', 'resources/css/home/ai-bot.css', 'resources/css/home/filter-modal.css', 'resources/css/home/sort-dropdown.css'])
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />

    <!-- Map Styles -->
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
    @vite(['resources/js/home/lazy-load.js', 'resources/js/home/load-more.js', 'resources/js/home/volcano-animation.js', 'resources/js/home/home.js', 'resources/js/my-volcanoes/volcano-actions.js', 'resources/js/home/volcano-modal.js', 'resources/js/home/ai-bot.js', 'resources/js/home/interactive-map/map-core.js', 'resources/js/home/interactive-map/map-display.js', 'resources/js/home/interactive-map/map-sync.js', 'resources/js/home/filter-modal.js', 'resources/js/home/sort-dropdown.js'])

    <!-- Dependencies -->
    <script src="https://unpkg.com/react@17/umd/react.development.js"></script>
    <script src="https://unpkg.com/react-dom@17/umd/react-dom.development.js"></script>


    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>

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
                    <span class="search-toggle-wrapper">
                        <button id="searchToggle" class="search-icon-inline" aria-describedby="searchToggleTooltip">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                        <span class="search-tooltip" id="searchToggleTooltip" role="tooltip">
                            Search volcanoes!
                        </span>
                    </span>
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
                <div class="search-suggestions suggestion-empty" aria-live="polite">
                    <span class="search-suggestion-hint">Try "Fuji", "Japan", or "JP"</span>
                </div>
            </div>

        </div>
    </section>

    <!-- Content Sections -->
    <section class="content-section">
        <div class="container">
            <div class="home-controls" style="position: relative;">
                <div id="sort-dropdown-container" class="sort-dropdown-container" style="position: absolute; top: 0; left: 0;">
                    <button id="sort-dropdown-btn" class="action-btn" 
                        style="border-color: #9b59b6; color: #9b59b6; min-width: 185px; justify-content: center; white-space: nowrap;"
                        title="Sort volcanoes">
                        <i class="fas fa-sort"></i>
                        <span id="sort-label">Alphabetical</span>
                        <i class="fas fa-chevron-down" style="margin-left: 8px; font-size: 12px;"></i>
                    </button>
                    <div id="sort-dropdown-menu" class="sort-dropdown-menu" style="display: none;">
                        <button class="sort-option active" data-sort="alphabetical">
                            <i class="fas fa-sort-alpha-down"></i>
                            <span>Alphabetical</span>
                        </button>
                        <button class="sort-option" data-sort="random">
                            <i class="fas fa-random"></i>
                            <span>Random Discovery</span>
                        </button>
                    </div>
                </div>
                
                <h2>Ignite Your Volcano Journey</h2>
                <p id="search-status" class="section-description">Discover Earth's Fiery Secrets</p>
                
                <!-- Filter and Map buttons on the right -->
                <div style="position: absolute; top: 0; right: 0; display: flex; gap: 12px; align-items: center;">
                    <span id="filter-pill" class="action-btn"
                        style="border-color: #ff8c00; color: #ff8c00; min-width: 130px; justify-content: center; white-space: nowrap;">
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

            <!-- Map Legend (only for logged-in users) -->
            <div id="map-legend"
                style="display: none; margin-bottom: 1rem; padding: 12px 20px; background: #f8f9fa; border-radius: 8px; text-align: center;">
                <p style="margin: 0; color: #2c3e50; font-size: 16px; font-weight: 500;">
                    <span id="legend-text">Loading...</span>
                </p>
            </div>

            <!-- Interactive Map Container -->
            <div id="interactive-map"
                style="display: none; width: 100%; height: 110vh; margin-bottom: 2rem; border-radius: 12px; text-align: center">
                <!-- The map will be rendered here by Leaflet (see resources > js > home > interactive map) -->
            </div>

            <!-- No Results Message (for search) -->
            <div id="no-results-message" class="hidden">
                <div class="empty-state">
                    <i class="fas fa-search fa-3x"></i>
                    <p>No "<span id="search-term"></span>" found in our database</p>
                </div>
            </div>

            <div class="volcano-grid">
                @foreach($volcanoes as $index => $volcano)
                    <article class="volcano-card {{ $index >= 9 ? 'homepage-hidden' : '' }}"
                        data-volcano-id="{{ $volcano->id }}" data-card-index="{{ $index }}">
                        <div class="volcano-image-container">
                            <!-- Lazy loaded image with delayed placeholder fallback -->
                            <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='1' height='1'%3E%3C/svg%3E"
                                data-src="{{ $volcano->safe_image_url }}"
                                data-placeholder="{{ asset('images/volcanoes/placeholder.png') }}"
                                alt="{{ $volcano->name }} volcano" class="lazy-image volcano-img">

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

                            <!-- Action Buttons -->
                            @auth
                                <div class="volcano-actions">
                                    <form
                                        action="{{ route('user.volcanoes.toggle', ['id' => $volcano->id, 'status' => 'visited']) }}"
                                        method="POST" class="inline">
                                        @csrf
                                        <button type="submit"
                                            class="action-btn visited-btn {{ $volcano->isVisitedBy(auth()->user()) ? 'active' : '' }}">
                                            <i class="fas fa-check"></i>
                                            <span>Visited</span>
                                        </button>
                                    </form>

                                    <form
                                        action="{{ route('user.volcanoes.toggle', ['id' => $volcano->id, 'status' => 'wishlist']) }}"
                                        method="POST" class="inline">
                                        @csrf
                                        <button type="submit"
                                            class="action-btn wishlist-btn {{ $volcano->isWishlistedBy(auth()->user()) ? 'active' : '' }}"
                                            {{ $volcano->isVisitedBy(auth()->user()) ? 'disabled' : '' }}>
                                            <i class="fas fa-heart"></i>
                                            <span>Wishlist</span>
                                        </button>
                                    </form>
                                </div>
                            @endauth

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
                                    <p class="hover-active-message"><i class="fas fa-arrow-down"></i> {{ $randomActiveMessage }}
                                    </p>
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

            <!-- Load More Button -->
            <div class="load-more-container" id="loadMoreContainer" style="text-align: center; margin: 40px 0;">
                <button id="load-more-btn" class="btn-load-more" data-load-count="9">
                    Load More Volcanoes
                </button>
                <p id="all-loaded-message" style="display: none; color: #666; margin-top: 20px;">
                    All volcanoes loaded! Use search above to explore more.
                </p>
            </div>

            <!-- Fun message for more volcanoes -->
            <div class="more-volcanoes-message">
                <div class="fun-message-container">
                    <h3 class="fun-title">🌋 Looking for more volcanoes?</h3>
                    <p class="fun-text">
                        We have hundreds more waiting to be discovered. <a href="#hero-carousel" class="search-link">Just
                            search above</a> to explore our full collection!
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Volcano Details Modal -->
    <div id="volcano-modal" class="volcano-modal hidden">
        <div class="volcano-modal-overlay">
            <div class="volcano-modal-content">
                <button class="volcano-modal-close" aria-label="Close modal">
                    <i class="fas fa-times"></i>
                </button>

                <div class="volcano-modal-header">
                    <div class="volcano-image-container">
                        <img id="modal-volcano-image" src="" alt="" class="modal-volcano-image">
                    </div>
                    <div class="volcano-info">
                        <h2 id="modal-volcano-name"></h2>
                        <div class="volcano-meta">
                            <span class="volcano-location">
                                <i class="fas fa-globe-americas"></i>
                                <span id="modal-volcano-continent"></span>
                            </span>
                            <span class="volcano-coordinates">
                                <i class="fas fa-map-pin"></i>
                                <span id="modal-volcano-latitude"></span>°, <span id="modal-volcano-longitude"></span>°
                            </span>
                        </div>
                    </div>
                </div>

                <div class="volcano-modal-body">
                    <div class="volcano-description">
                        <h3>About this volcano</h3>
                        <p id="modal-volcano-description">Loading...</p>
                    </div>
                    <div style="text-align: center; margin-top: 20px;">
                        <button id="ask-ai-about-volcano" class="primary-btn" style="background: #BB0101;">
                            <i class="fas fa-robot"></i>
                            Ask AI for More Info
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- AI Bot Floating Button & Chat Window -->
    <button id="ai-bot-toggle" class="ai-bot-toggle">
        <i class="fas fa-robot"></i>
    </button>
    <section class="chat-window ai-bot-hidden">
        <button class="close" id="ai-bot-close">x close</button>
        <div class="chat">
            <div class="model">
                <p>Hi, how can I help you?</p>
            </div>
        </div>
        <div class="input-area">
            <input placeholder="Ask me anything..." type="text">
            <button>
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </section>

    <!-- Filter Modal -->
    <div id="filterModal" class="filter-modal">
        <div class="filter-modal-content">
            <div class="filter-modal-header">
                <h2><i class="fas fa-filter"></i> Filter Volcanoes</h2>
                <span class="close">&times;</span>
            </div>
            <form action="{{ route('home') }}" method="GET">
                <div class="filter-modal-body">
                    <div class="filter-group">
                        <label for="country"><i class="fas fa-flag"></i> Country</label>
                        <input type="text" name="country" id="country" value="{{ request('country') }}" placeholder="Enter country name (e.g., Japan, Italy)">
                    </div>

                    <div class="filter-group">
                        <label for="continent"><i class="fas fa-globe-americas"></i> Continent</label>
                        <select name="continent" id="continent">
                            <option value="">All Continents</option>
                            <option value="Africa" {{ request('continent') == 'Africa' ? 'selected' : '' }}>Africa</option>
                            <option value="Asia" {{ request('continent') == 'Asia' ? 'selected' : '' }}>Asia</option>
                            <option value="Europe" {{ request('continent') == 'Europe' ? 'selected' : '' }}>Europe</option>
                            <option value="North America" {{ request('continent') == 'North America' ? 'selected' : '' }}>North America</option>
                            <option value="South America" {{ request('continent') == 'South America' ? 'selected' : '' }}>South America</option>
                            <option value="Oceania" {{ request('continent') == 'Oceania' ? 'selected' : '' }}>Oceania</option>
                            <option value="Antarctica" {{ request('continent') == 'Antarctica' ? 'selected' : '' }}>Antarctica</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="activity"><i class="fas fa-fire"></i> Activity Status</label>
                        <select name="activity" id="activity">
                            <option value="">All Activity Levels</option>
                            <option value="Active" {{ request('activity') == 'Active' ? 'selected' : '' }}>Active</option>
                            <option value="Inactive" {{ request('activity') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="Extinct" {{ request('activity') == 'Extinct' ? 'selected' : '' }}>Extinct</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="type"><i class="fas fa-mountain"></i> Type</label>
                        <select name="type" id="type">
                            <option value="">All Types</option>
                            <option value="Stratovolcano" {{ request('type') == 'Stratovolcano' ? 'selected' : '' }}>Stratovolcano</option>
                            <option value="Shield" {{ request('type') == 'Shield' ? 'selected' : '' }}>Shield</option>
                            <option value="Caldera" {{ request('type') == 'Caldera' ? 'selected' : '' }}>Caldera</option>
                            <option value="Complex" {{ request('type') == 'Complex' ? 'selected' : '' }}>Complex</option>
                            <option value="Cinder cone" {{ request('type') == 'Cinder cone' ? 'selected' : '' }}>Cinder cone</option>
                            <option value="Lava dome" {{ request('type') == 'Lava dome' ? 'selected' : '' }}>Lava dome</option>
                            <option value="Submarine" {{ request('type') == 'Submarine' ? 'selected' : '' }}>Submarine</option>
                            <option value="Fissure vent" {{ request('type') == 'Fissure vent' ? 'selected' : '' }}>Fissure vent</option>
                            <option value="Volcanic field" {{ request('type') == 'Volcanic field' ? 'selected' : '' }}>Volcanic field</option>
                            <option value="Flood basalt" {{ request('type') == 'Flood basalt' ? 'selected' : '' }}>Flood basalt</option>
                            <option value="Somma" {{ request('type') == 'Somma' ? 'selected' : '' }}>Somma</option>
                            <option value="Tuff ring" {{ request('type') == 'Tuff ring' ? 'selected' : '' }}>Tuff ring</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="elevation_min"><i class="fas fa-arrow-up"></i> Elevation (meters)</label>
                        <div class="elevation-range">
                            <input type="number" name="elevation_min" id="elevation_min" value="{{ request('elevation_min') }}" placeholder="Min (e.g., 1000)">
                            <span>to</span>
                            <input type="number" name="elevation_max" id="elevation_max" value="{{ request('elevation_max') }}" placeholder="Max (e.g., 5000)">
                        </div>
                    </div>
                </div>

                <div class="filter-modal-actions">
                    <button type="submit" class="btn-apply">
                        <i class="fas fa-check"></i> Apply Filters
                    </button>
                    <a href="{{ route('home') }}" class="btn-clear">
                        <i class="fas fa-times"></i> Clear All
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Volcano Animation -->
    <section id="volcano-container"></section>
@endsection