@extends('layouts.app')

@section('title', 'Manage Volcanoes')

@section('body_class', 'admin-page')

@section('head_js')
    @vite(['resources/js/admin/manage-volcanoes.js'])
@endsection

@section('content')
    <div class="admin-container">
        <div class="admin-header">
            <h1>Manage Volcanoes</h1>
            <p>Add new volcanoes or delete existing ones</p>
        </div>

        <div class="admin-grid">
            @can('create-volcanoes')
            <div class="add-volcano-form">
                <h2>
                    <i class="fas fa-plus-circle"></i>
                    Add a new volcano
                </h2>
                <form action="{{ route('admin.manage-volcanoes.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

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
                            <label for="name">Volcano Name *</label>
                            <input type="text" id="name" name="name" placeholder="e.g., Mount Fuji" value="{{ old('name') }}" required @if($errors->has('name')) class="form-input-error" @endif>
                            @if($errors->has('name'))
                                <span class="form-input-error-message">{{ $errors->first('name') }}</span>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="country">Country *</label>
                            <input type="text" id="country" name="country" placeholder="e.g., Japan" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="continent">Continent *</label>
                            <input type="text" id="continent" name="continent" placeholder="e.g., Asia" required>
                        </div>
                        <div class="form-group">
                            <label for="activity">Activity Level *</label>
                            <input type="text" id="activity" name="activity" placeholder="e.g., Active, Inactive, Dormant" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="latitude">Latitude *</label>
                            <input type="number" id="latitude" name="latitude" placeholder="e.g., 35.3606" step="0.0001" required>
                        </div>
                        <div class="form-group">
                            <label for="longitude">Longitude *</label>
                            <input type="number" id="longitude" name="longitude" placeholder="e.g., 138.7274" step="0.0001" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="elevation">Elevation (meters) *</label>
                            <input type="number" id="elevation" name="elevation" placeholder="e.g., 3776" required>
                        </div>
                        <div class="form-group">
                            <label for="type">Volcano Type *</label>
                            <input type="text" id="type" name="type" placeholder="e.g., Stratovolcano, Shield" required>
                        </div>
                    </div>

                    <div class="form-group form-row full">
                        <label for="image_url">Image (Optional)</label>
                        <input type="file" id="image_url" name="image_url" accept="image/*" data-max-size="5242880">
                        <small style="color: #666; margin-top: 5px; display: block;">Maximum file size: 5 MB</small>
                    </div>

                    <div class="form-group form-row full">
                        <label for="description">Description *</label>
                        <textarea id="description" name="description" placeholder="Enter a detailed description of the volcano..." required></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-check-circle"></i>
                            Add Volcano
                        </button>
                    </div>
                </form>
            </div>
            @endcan

            @can('view-volcanoes')
            <div class="volcanoes-list">
                <h2>
                    <i class="fas fa-list"></i>
                    All Volcanoes
                </h2>

                @if ($volcanoes->count() > 0)
                    <div class="volcano-stats">
                        <div class="volcano-stat">
                            <div class="volcano-stat-number">{{ $volcanoes->total() }}</div>
                            <div class="volcano-stat-label">Total volcanoes</div>
                        </div>
                    </div>

                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="volcanoSearch" placeholder="Search by name or country...">
                    </div>

                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Country</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($volcanoes as $volcano)
                                    <tr>
                                        <td>
                                            <strong>{{ $volcano->name }}</strong>
                                        </td>
                                        <td>{{ $volcano->country }}</td>
                                        <td>
                                            <div class="action-cell">
                                                @can('delete-volcanoes')
                                                <form action="{{ route('admin.manage-volcanoes.destroy', $volcano->id) }}" method="POST" style="margin: 0;" class="delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn-delete" data-delete-type="volcano" data-delete-name="{{ $volcano->name }}" data-form-index="{{ $loop->index }}">
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
                        {{ $volcanoes->links('vendor.pagination.custom') }}
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-volcano"></i>
                        <p>No volcanoes found in the database</p>
                        <p style="font-size: 0.95rem; margin-top: 0.5rem;">Create one using the form to get started!</p>
                    </div>
                @endif
            </div>
            @endcan
        </div>
    </div>
@endsection