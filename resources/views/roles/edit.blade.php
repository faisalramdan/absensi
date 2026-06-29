@extends('layouts.app')
@section('title', 'Buat Peran')
@section('content')
    <div class="wrapper">
        <div class="page-content">
            <div class="container-xxl">

                <form action="{{ route('roles.update', $role) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">

                        {{-- Informasi Role --}}
                        <div class="col-xl-4">
                            <div class="card">

                                <div class="card-header">
                                    <h4 class="card-title mb-0">
                                        <iconify-icon icon="solar:user-pen-bold-duotone" class="me-2"></iconify-icon>
                                        Perbarui Peran
                                    </h4>
                                </div>

                                <div class="card-body">

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">
                                            Nama Peran
                                            <span class="text-danger">*</span>
                                        </label>

                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            id="name" name="name" value="{{ old('name', $role->name) }}" required
                                            placeholder="Masukkan nama peran" maxlength="255">
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror

                                        @error('name')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="alert alert-info mb-0">
                                        Tentukan nama peran dan hak akses yang dimiliki oleh role ini.
                                    </div>

                                </div>
                            </div>
                        </div>

                        {{-- Permission --}}
                        <div class="col-xl-8">
                            <div class="card">

                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h4 class="card-title mb-0">
                                        <iconify-icon icon="solar:shield-user-bold-duotone" class="me-2"></iconify-icon>
                                        Hak Akses
                                    </h4>

                                    <div>
                                        <button type="button" class="btn btn-success btn-sm" id="check-all">
                                            Select All
                                        </button>

                                        <button type="button" class="btn btn-danger btn-sm" id="uncheck-all">
                                            Unselect All
                                        </button>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <div id="permissions-container">

                                        @foreach($groupedPermissions as $group => $permissions)
                                            <div class="permission-category mb-4" data-category="{{ ucfirst($group) }}">

                                                {{-- Header Category --}}
                                                <div
                                                    class="d-flex align-items-center justify-content-between p-3 bg-light rounded mb-3">

                                                    <div class="d-flex align-items-center">
                                                        <div
                                                            class="avatar-sm bg-primary-subtle rounded-circle me-3 d-flex align-items-center justify-content-center">
                                                            <iconify-icon icon="solar:folder-bold"
                                                                class="text-primary fs-16"></iconify-icon>
                                                        </div>

                                                        <div>
                                                            <h6 class="mb-0 fw-semibold">
                                                                {{ ucfirst(str_replace('-', ' ', $group)) }}
                                                            </h6>

                                                            <small class="text-muted">
                                                                {{ count($permissions) }} permissions available
                                                            </small>
                                                        </div>
                                                    </div>

                                                    <div class="d-flex align-items-center gap-2">
                                                        <span class="badge bg-info-subtle text-info"
                                                            id="category-count-{{ $group }}">
                                                            0 / {{ count($permissions) }} selected
                                                        </span>

                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-primary select-category"
                                                            data-category="{{ $group }}">
                                                            <iconify-icon icon="solar:check-square-bold"
                                                                class="me-1"></iconify-icon>
                                                            Select All
                                                        </button>

                                                        <button type="button"
                                                            class="btn btn-sm btn-link text-muted category-toggle"
                                                            data-category="{{ $group }}">
                                                            <iconify-icon icon="solar:alt-arrow-down-bold"
                                                                class="category-arrow"></iconify-icon>
                                                        </button>
                                                    </div>

                                                </div>

                                                {{-- Permission List --}}
                                                <div class="category-permissions" id="category-{{ $group }}">

                                                    <div class="row">

                                                        @foreach($permissions as $permission)
                                                            <div class="col-md-4 mb-2">

                                                                <div class="form-check border rounded p-2">
                                                                    <input
                                                                        class="form-check-input permission-checkbox category-{{ $group }}"
                                                                        type="checkbox" name="permissions[]"
                                                                        value="{{ $permission->name }}"
                                                                        id="perm{{ $permission->id }}" data-category="{{ $group }}"
                                                                        {{in_array($permission->name, old('permissions', $role->permissions->pluck('name')->toArray())) ? 'checked' : ''}}>

                                                                    <label class="form-check-label ms-1"
                                                                        for="perm{{ $permission->id }}">
                                                                        {{ $permission->name }}
                                                                    </label>
                                                                </div>

                                                            </div>
                                                        @endforeach

                                                    </div>

                                                </div>

                                            </div>
                                        @endforeach

                                    </div>

                                    @error('permissions')
                                        <div class="alert alert-danger mt-3">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                            </div>
                        </div>

                    </div>

                    <div class="p-3 bg-light mb-3 rounded">
                        <div class="row justify-content-end g-2">
                            <div class="col-lg-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    Simpan</button>
                            </div>
                            <div class="col-lg-2">
                                <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary w-100">
                                    Cancel </a>
                            </div>

                        </div>
                    </div>

                </form>

            </div>
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // Toggle Category
            document.querySelectorAll('.category-toggle').forEach(button => {
                button.addEventListener('click', function () {

                    const category = this.dataset.category;
                    const container = document.getElementById('category-' + category);

                    if (container.style.display === 'none') {
                        container.style.display = 'block';
                    } else {
                        container.style.display = 'none';
                    }
                });
            });

            // Select All Category
            document.querySelectorAll('.select-category').forEach(button => {
                button.addEventListener('click', function () {

                    const category = this.dataset.category;

                    const checkboxes = document.querySelectorAll(
                        '.category-' + category
                    );

                    const allChecked = [...checkboxes].every(cb => cb.checked);

                    checkboxes.forEach(cb => {
                        cb.checked = !allChecked;
                    });

                    updateCounter(category);
                });
            });

            // Counter
            document.querySelectorAll('.permission-checkbox').forEach(cb => {
                cb.addEventListener('change', function () {
                    updateCounter(this.dataset.category);
                });
            });

            function updateCounter(category) {

                const total = document.querySelectorAll(
                    '.category-' + category
                ).length;

                const checked = document.querySelectorAll(
                    '.category-' + category + ':checked'
                ).length;

                const counter = document.getElementById(
                    'category-count-' + category
                );

                if (counter) {
                    counter.innerText = checked + ' / ' + total + ' selected';
                }
            }

            // Select All Permissions
            document.getElementById('check-all')?.addEventListener('click', function () {

                document.querySelectorAll('.permission-checkbox').forEach(cb => {
                    cb.checked = true;
                });

                document.querySelectorAll('.permission-category').forEach(category => {
                    updateCounter(category.dataset.category);
                });

            });

            // Unselect All Permissions
            document.getElementById('uncheck-all')?.addEventListener('click', function () {

                document.querySelectorAll('.permission-checkbox').forEach(cb => {
                    cb.checked = false;
                });

                document.querySelectorAll('.permission-category').forEach(category => {
                    updateCounter(category.dataset.category);
                });

            });

            document.querySelectorAll('.permission-category').forEach(category => {
                updateCounter(category.dataset.category);
            });

        });
    </script>
@endsection