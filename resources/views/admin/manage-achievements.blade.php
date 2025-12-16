@extends('layouts.app')

@section('title', 'Manage Achievements')

@section('body_class', 'admin-page')

@section('head_js')
    @vite(['resources/js/admin/manage-achievements.js'])
@endsection

@section('content')
    <div class="admin-container">
        <div class="admin-header">
            <h1>Manage Achievements</h1>
            <p>Add new achievements or delete existing ones</p>
        </div>

        <div class="admin-grid">
            @can('create-achievements')
            <div class="add-volcano-form">
                <h2>
                    <i class="fas fa-plus-circle"></i>
                    Add a new achievement
                </h2>
                <form action="{{ route('admin.manage-achievements.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- Hidden dimensions field that JavaScript will populate -->
                    <input type="hidden" id="dimensions" name="dimensions" value="">

                    @if ($errors->any())
                        <div class="form-errors">
                            <strong>Validation Error:</strong>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Achievement Name *</label>
                            <input type="text" id="name" name="name" placeholder="e.g., Volcano Explorer" value="{{ old('name') }}" required @if($errors->has('name')) class="form-input-error" @endif>
                            @if($errors->has('name'))
                                <span class="form-input-error-message">{{ $errors->first('name') }}</span>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="metric">Metric *</label>
                            <select id="metric" name="metric" required>
                                <option value="" disabled selected>Select a metric...</option>
                                <option value="total_visits" {{ old('metric') == 'total_visits' ? 'selected' : '' }}>Total Visits</option>
                                <option value="visits_by_continent" {{ old('metric') == 'visits_by_continent' ? 'selected' : '' }}>Visits by Continent</option>
                                <option value="visits_by_activity" {{ old('metric') == 'visits_by_activity' ? 'selected' : '' }}>Visits by Activity</option>
                                <option value="visits_by_type" {{ old('metric') == 'visits_by_type' ? 'selected' : '' }}>Visits by Type</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="aggregator">Aggregator *</label>
                            <select id="aggregator" name="aggregator" required>
                                <option value="" disabled selected>Select an aggregator...</option>
                                <option value="count" {{ old('aggregator') == 'count' ? 'selected' : '' }}>Count</option>
                                <option value="count_distinct" {{ old('aggregator') == 'count_distinct' ? 'selected' : '' }}>Count Distinct</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="threshold">Threshold *</label>
                            <input type="number" id="threshold" name="threshold" placeholder="e.g., 10" value="{{ old('threshold') }}" required min="1">
                        </div>
                    </div>

                    <div id="dimensions-container" style="display: none;">
                        <div class="form-group form-row full">
                            <label for="dimensions">Dimensions *</span></label>
                            <div id="dimensions-inputs">
                                <!-- Will be populated by JavaScript based on metric selection -->
                            </div>
                            <small style="color: #666; margin-top: 5px; display: block;">Select the specific values to match for this achievement.</small>
                        </div>
                    </div>

                    <div class="form-group form-row full">
                        <label for="description">Description *</label>
                        <textarea id="description" name="description" placeholder="Enter a detailed description of the achievement..." required>{{ old('description') }}</textarea>
                    </div>

                    <div class="form-group form-row full">
                        <label for="image_path">Unlocked Badge Image (Optional)</label>
                        <input type="file" id="image_path" name="image_path" accept="image/*" data-max-size="5242880">
                        <small style="color: #666; margin-top: 5px; display: block;">Maximum file size: 5 MB</small>
                    </div>

                    <div class="form-group form-row full">
                        <label for="locked_image_path">Locked Badge Image (Optional)</label>
                        <input type="file" id="locked_image_path" name="locked_image_path" accept="image/*" data-max-size="5242880">
                        <small style="color: #666; margin-top: 5px; display: block;">Maximum file size: 5 MB</small>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-check-circle"></i>
                            Add Achievement
                        </button>
                    </div>
                </form>
            </div>
            @endcan

            @can('view-achievements')
            <div class="volcanoes-list">
                <h2>
                    <i class="fas fa-list"></i>
                    All Achievements
                </h2>

                @if ($achievements->count() > 0)
                    <div class="volcano-stats">
                        <div class="volcano-stat">
                            <div class="volcano-stat-number">{{ $achievements->total() }}</div>
                            <div class="volcano-stat-label">Total achievements</div>
                        </div>
                    </div>

                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="achievementSearch" placeholder="Search by name...">
                    </div>

                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($achievements as $achievement)
                                    <tr>
                                        <td>
                                            <strong>{{ $achievement->name }}</strong>
                                        </td>
                                        <td>{{ $achievement->description }}</td>
                                        <td>
                                            <div class="action-cell">
                                                @can('delete-achievements')
                                                <form action="{{ route('admin.manage-achievements.destroy', $achievement->id) }}" method="POST" style="margin: 0;" class="delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn-delete" data-delete-type="achievement" data-delete-name="{{ $achievement->name }}" data-form-index="{{ $loop->index }}">
                                                        <i class="fas fa-trash"></i>
                                                        Delete
                                                    </button>
                                                </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination Links -->
                    <div class="pagination-wrapper">
                        {{ $achievements->links('vendor.pagination.custom') }}
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-trophy"></i>
                        <p>No achievements found in the database</p>
                        <p style="font-size: 0.95rem; margin-top: 0.5rem;">Create one using the form to get started!</p>
                    </div>
                @endif
            </div>
            @endcan
        </div>
    </div>
@endsection