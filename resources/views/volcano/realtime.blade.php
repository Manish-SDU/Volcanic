@extends('layouts.app')

@section('title', 'Real-time Volcano Activity')

{{-- Page-specific CSS via Vite --}}
@section('additional_css')
    @vite('resources/css/realTimeVolcano.css')
@endsection

@section('content')
    <main class="content-section volcano-activity-page"
          data-volcano-endpoint="{{ route('api.volcano.latest') }}">
        <div class="container">
            <div class="volcano-activity-header">
                <div class="volcano-activity-header-text">
                    <h1>
                        <i class="fa-solid fa-volcano"></i>
                        Real-time Volcano Activity
                    </h1>
                    <p>
                        See recent volcanic events around the world, powered by the Ambee Natural Disasters API.
                    </p>
                    <div class="volcano-powered-by">
                        Data source:
                        <a href="https://docs.ambeedata.com/apis/natural-disasters"
                           target="_blank" rel="noopener noreferrer">
                            Ambee Natural Disasters API (event type VO)
                        </a>
                    </div>
                </div>

                <div class="volcano-activity-controls">
                    <div>
                        <label for="volcano-continent-select">
                            Continent
                        </label>
                        <select id="volcano-continent-select">
                            <option value="EUR" selected>Europe (EUR)</option>
                            <option value="NAR">North America (NAR)</option>
                            <option value="SAR">South America (SAR)</option>
                            <option value="ASIA">Asia (ASIA)</option>
                            <option value="AUS">Australia & Oceania (AUS)</option>
                            <option value="AFR">Africa (AFR)</option>
                            <option value="ANT">Antarctica (ANT)</option>
                        </select>
                    </div>

                    <div>
                        <label for="volcano-limit-select">
                            Max events
                        </label>
                        <select id="volcano-limit-select">
                            <option value="5">5</option>
                            <option value="10" selected>10</option>
                            <option value="20">20</option>
                        </select>
                    </div>

                    <div>
                        <label for="volcano-fallback-checkbox" class="volcano-fallback-label">
                            <input type="checkbox" id="volcano-fallback-checkbox" checked>
                            Include fallback events
                        </label>
                    </div>

                    <button id="volcano-refresh-btn" class="primary-btn">
                        <i class="fas fa-rotate-right"></i>
                        Refresh
                    </button>
                </div>
            </div>

            <div id="volcano-activity-state">
                <div id="volcano-loading-state" class="volcano-loading">
                    <i class="fas fa-spinner"></i>
                    <div>Loading latest volcano activity…</div>
                </div>
                <div id="volcano-error-state" class="volcano-error" style="display:none;"></div>
            </div>

            <section id="volcano-activity-list" class="volcano-activity-grid"></section>

            <div id="volcano-empty-state" class="volcano-empty" style="display:none;">
                <p>
                    No recent volcano events found for this region.
                    Try a different continent or come back later.
                </p>
                <p>
                    (You can also check the "Include fallback events" option to see other natural disasters!)
                </p>
            </div>
        </div>
    </main>
@endsection

{{-- Page-specific JS via Vite --}}
@section('scripts')
    @vite('resources/js/realTimeVolcano/realTimeVolcano.js')
@endsection
