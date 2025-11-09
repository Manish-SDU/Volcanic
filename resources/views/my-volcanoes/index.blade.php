@extends('layouts.app')

@section('title', 'My Volcanoes')

@section('head_js')
    @vite(['resources/js/my-volcanoes/panels.js', 'resources/js/my-volcanoes/number_increment.js', 'resources/js/my-volcanoes/volcano-actions.js', 'resources/js/my-volcanoes/edit-date-popup.js'])
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
                <dd class="stat-value" id="visited-value">{{ $stats['volcanoes_visited'] }}</dd>
            </dl>
            <dl class="stat-box">
                <dt class="stat-label">Countries Explored</dt>
                <dd class="stat-value" id="countries-value">{{ $stats['countries_explored'] }}</dd>
            </dl>
            <dl class="stat-box">
                <dt class="stat-label">Active Volcanoes</dt>
                <dd class="stat-value" id="active-value">{{ $stats['active_volcanoes'] }}</dd>
            </dl>
            <dl class="stat-box">
                <dt class="stat-label">Inactive Volcanoes</dt>
                <dd class="stat-value" id="inactive-value">{{ $stats['inactive_volcanoes'] }}</dd>
            </dl>
        </div>
    </section>

    <!-- Visited Volcanoes -->
    <section id="visited" class="myv-panel">
        <h2><i class="fas fa-check-circle"></i> Visited Volcanoes</h2>
        
        @if($visited->isEmpty())
            <div class="volcano-grid">
                <div class="empty-state">
                    <p>No volcanoes visited yet</p>
                    <a href="{{ route('home') }}">Explore Volcanoes</a>
                </div>
            </div>
        @else
            <div class="volcano-grid">
                @foreach($visited as $userVolcano)
                    <div class="volcano-card">
                        <div class="image-container">
                            <img src="{{ $userVolcano->volcano->safe_image_url }}" 
                                 alt="{{ $userVolcano->volcano->name }}" 
                                 class="volcano-thumb">
                        </div>
                        <div class="card-content">
                            <h3>{{ $userVolcano->volcano->name }}</h3>
                            <p class="country">
                                <i class="fas fa-map-marker-alt"></i> 
                                {{ $userVolcano->volcano->country }}
                            </p>
                            @if($userVolcano->visited_at)
                                <p class="visited-date">
                                    <i class="fas fa-calendar-check"></i>
                                    Visited on {{ $userVolcano->visited_at->format('M d, Y') }}
                                </p>
                            @endif

                            <button type="button" class="date-edit-btn" onclick="openDateModal({{ $userVolcano->volcano->id }}, '{{ $userVolcano->visited_at ? $userVolcano->visited_at->format('Y-m-d') : date('Y-m-d') }}', '{{ $userVolcano->volcano->name }}')">
                                <i class="fas fa-edit"></i> Edit Visited Date
                            </button>            
                            
                            <form action="{{ route('user.volcanoes.toggle', ['id' => $userVolcano->volcano->id, 'status' => 'visited']) }}" 
                                  method="POST">
                                @csrf
                                <button type="submit" class="remove-btn">
                                    <i class="fas fa-times"></i> Remove
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Navigation Button -->
        <button class="next-btn" onclick="nextPanel()">→</button>
        <div class="next-tooltip tooltip">Wish list</div>
    </section>

    <!-- Wishlist Volcanoes -->
    <section id="wish" class="myv-panel" style="display: none;">
        <h2><i class="fas fa-heart"></i> Wish to Visit</h2>
        
        @if($wishlist->isEmpty())
            <div class="volcano-grid">
                <div class="empty-state">
                    <p>Your wishlist is empty</p>
                    <a href="{{ route('home') }}">Explore Volcanoes</a>
                </div>
            </div>
        @else
            <div class="volcano-grid">
                @foreach($wishlist as $userVolcano)
                    <div class="volcano-card" data-volcano-id="{{ $userVolcano->volcano->id }}">
                        <div class="image-container">
                            <img src="{{ $userVolcano->volcano->safe_image_url }}" 
                                 alt="{{ $userVolcano->volcano->name }}" 
                                 class="volcano-thumb">
                        </div>
                        <div class="card-content">
                            <h3>{{ $userVolcano->volcano->name }}</h3>
                            <p class="country">
                                <i class="fas fa-map-marker-alt"></i> 
                                {{ $userVolcano->volcano->country }}
                            </p>
                            <div class="wishlist-actions">
                                <form action="{{ route('user.volcanoes.toggle', ['id' => $userVolcano->volcano->id, 'status' => 'visited']) }}"
                                    method="POST" class="mark-visited-form">
                                    @csrf
                                    <button type="submit" class="mark-visited-btn">
                                        <i class="fas fa-check"></i> Mark as Visited
                                    </button>
                                </form>
                                <form action="{{ route('user.volcanoes.toggle', ['id' => $userVolcano->volcano->id, 'status' => 'wishlist']) }}" 
                                    method="POST">
                                    @csrf
                                    <button type="submit" class="remove-btn">
                                        <i class="fas fa-times"></i> Remove
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Navigation Button -->
        <button class="prev-btn" onclick="prevPanel()">←</button>
        <div class="prev-tooltip tooltip">Visited list</div>
    </section>

    <!-- Notification Container -->
    <div id="notifications-container"></div>

    <!-- Success Notification Template -->
    <template id="success-notification-template">
        <div class="notification notification-success">
            <div class="notification-content">
                <span><i class="fas fa-check-circle"></i> <span class="notification-message"></span></span>
                <button class="notification-close" onclick="this.closest('.notification').remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </template>

    <!-- Error Notification Template -->
    <template id="error-notification-template">
        <div class="notification notification-info">
            <div class="notification-content">
                <span><i class="fas fa-exclamation-circle"></i> <span class="notification-message"></span></span>
                <button class="notification-close" onclick="this.closest('.notification').remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </template>

    <!-- Empty State Templates -->
    <template id="visited-empty-template">
        <div class="empty-state">
            <p>No volcanoes visited yet</p>
            <a href="{{ route('home') }}">Explore Volcanoes</a>
        </div>
    </template>

    <template id="wishlist-empty-template">
        <div class="empty-state">
            <p>Your wishlist is empty</p>
            <a href="{{ route('home') }}">Explore Volcanoes</a>
        </div>
    </template>

    <!-- Volcano Card Template for dynamically added cards -->
    <template id="volcano-card-template">
        <div class="volcano-card" data-volcano-id="">
            <div class="image-container">
                <img src="" alt="" class="volcano-thumb">
            </div>
            <div class="card-content">
                <h3></h3>
                <p class="country">
                    <i class="fas fa-map-marker-alt"></i>
                    <span class="country-name"></span>
                </p>
                <p class="visited-date">
                    <i class="fas fa-calendar-alt"></i>
                    <span class="date-text"></span>
                </p>
                
                <button type="button" class="date-edit-btn" onclick="">
                    <i class="fas fa-edit"></i> Edit Visited Date
                </button>
                
                <form method="POST" action="" class="remove-form">
                    <input type="hidden" name="_token" value="">
                    <button type="submit" class="remove-btn">
                        <i class="fas fa-times"></i> Remove
                    </button>
                </form>
            </div>
        </div>
    </template>

    <!-- Date Edit Modal -->
    <div id="dateModal" class="date-edit-modal" style="display: none;">
        <div class="date-modal-content">
            <div class="date-modal-header">
                <h3 class="date-modal-title"><i class="fas fa-calendar-edit"></i> Edit Visited Date</h3>
                <button type="button" class="date-modal-close" onclick="closeDateModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="date-modal-body">
                <p>Update the visited date for <strong id="modal-volcano-name"></strong>:</p>
                <form id="dateUpdateForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="date-modal-form-group">
                        <label for="modal-visited-date" class="date-modal-label">
                            <i class="fas fa-calendar-check"></i> Visited Date:
                        </label>
                        <input type="date" 
                            id="modal-visited-date" 
                            name="visited_date" 
                            class="date-modal-input"
                            max="{{ date('Y-m-d') }}"
                            required>
                    </div>
                    <div class="date-modal-actions">
                        <button type="button" class="date-cancel-btn" onclick="closeDateModal()">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" class="date-save-btn">
                            <i class="fas fa-save"></i> Save Date
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal backdrop --}}
    <div id="modalBackdrop" class="date-modal-backdrop" style="display: none;" onclick="closeDateModal()"></div>
@endsection