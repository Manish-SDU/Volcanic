@extends('layouts.app')

@section('title', 'Manage Users')

@section('body_class', 'admin-page')

@section('head_js')
    @vite(['resources/js/admin/manage-users.js'])
@endsection

@section('content')
    <div class="admin-container">
        <div class="admin-header">
            <h1>Manage Users</h1>
            <p>Delete existing users</p>
        </div>

        <div class="admin-grid">
            @can('view-users')
            <div class="volcanoes-list" style="grid-column: 1 / -1;">
                <h2>
                    <i class="fas fa-users"></i>
                    All Users
                </h2>

                @if ($users->count() > 0)
                    <div class="volcano-stats">
                        <div class="volcano-stat">
                            <div class="volcano-stat-number">{{ $users->total() }}</div>
                            <div class="volcano-stat-label">Total users</div>
                        </div>
                    </div>

                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="userSearch" placeholder="Search by name...">
                    </div>

                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Role</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    <tr>
                                        <td>
                                            <strong>{{ $user->name }}</strong>
                                        </td>
                                        <td>
                                            @if($user->is_admin)
                                                <span style="color:#BB0101 ; font-weight: bold;">Admin</span>
                                            @else
                                                <span>User</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="action-cell">
                                                @can('delete-users')
                                                <form action="{{ route('admin.manage-users.destroy', $user->id) }}" method="POST" style="margin: 0;" class="delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn-delete" data-delete-type="user" data-delete-name="{{ $user->name }}" data-form-index="{{ $loop->index }}">
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
                        {{ $users->links('vendor.pagination.custom') }}
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-users"></i>
                        <p>No users found in the database</p>
                    </div>
                @endif
            </div>
            @endcan
        </div>
    </div>
@endsection