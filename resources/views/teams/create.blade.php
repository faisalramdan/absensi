@extends('layouts.app')
@section('title', 'Tambah Team')
@section('content')
<!-- START Wrapper -->
    <div class="wrapper">
        <div class="page-content">
    <div class="container-xxl">

        <div class="card">

            <div class="card-header">

                <h4 class="card-title mb-0">
                    Tambah Team
                </h4>

            </div>

            <form action="{{ route('teams.store') }}" method="POST">

                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <label class="form-label">
                                Company <span class="text-danger">*</span>
                            </label>

                            <select name="company_id" class="form-select @error('company_id') is-invalid @enderror" required>

                                <option value="">Pilih Company</option>

                                @foreach($companies as $company)

                                    <option value="{{ $company->id }}"
                                        {{ old('company_id', $team->company_id ?? '') == $company->id ? 'selected' : '' }}>
                                        {{ $company->name }}
                                    </option>

                                @endforeach

                            </select>

                            @error('company_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                        </div>

                        <div class="col-lg-6 mb-3">

                            <label class="form-label">
                                Parent Team
                            </label>

                            <select name="parent_id" class="form-select">

                                <option value="">Tidak Ada</option>

                                @foreach($parents as $parent)

                                    <option value="{{ $parent->id }}"
                                        {{ old('parent_id', $team->parent_id ?? '') == $parent->id ? 'selected' : '' }}>
                                        {{ $parent->name }}
                                    </option>

                                @endforeach

                            </select>

                        </div>

                        <div class="col-lg-8 mb-3">

                            <label class="form-label">
                                Nama Team <span class="text-danger">*</span>
                            </label>

                            <input
                                type="text"
                                name="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $team->name ?? '') }}"
                                required>

                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                        </div>

                        <div class="col-lg-12 mb-3">

                            <label class="form-label">
                                Description
                            </label>

                            <textarea
                                name="description"
                                rows="3"
                                class="form-control">{{ old('description', $team->description ?? '') }}</textarea>

                        </div>

                        <div class="col-lg-4 mb-3">

                            <label class="form-label">
                                Sort Order
                            </label>

                            <input
                                type="number"
                                name="sort_order"
                                class="form-control"
                                value="{{ old('sort_order', $team->sort_order ?? 0) }}">

                        </div>

                        <div class="col-lg-8 mb-3">

                            <label class="form-label d-block">
                                Status
                            </label>

                            <div class="form-check form-check-inline">

                                <input
                                    class="form-check-input"
                                    type="radio"
                                    name="is_active"
                                    value="1"
                                    {{ old('is_active', $team->is_active ?? 1) == 1 ? 'checked' : '' }}>

                                <label class="form-check-label">
                                    Aktif
                                </label>

                            </div>

                            <div class="form-check form-check-inline">

                                <input
                                    class="form-check-input"
                                    type="radio"
                                    name="is_active"
                                    value="0"
                                    {{ old('is_active', $team->is_active ?? 1) == 0 ? 'checked' : '' }}>

                                <label class="form-check-label">
                                    Nonaktif
                                </label>

                            </div>

                        </div>

                    </div>
                </div>
                    <div class="p-3 bg-light mb-3 rounded">
                        <div class="card-footer text-end">
                            <button type="submit" class="btn btn-primary">
                                Simpan
                            </button>

                            <a href="{{ route('teams.index') }}" class="btn btn-light">
                                Kembali
                            </a>              
                        </div>
                    </div>

            </form>

        </div>

    </div>
    </div>
    </div>

@endsection