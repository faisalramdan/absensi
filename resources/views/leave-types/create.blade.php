@extends('layouts.app')
@section('title', 'Tambah Cuti / Izin')
@section('content')

<div class="wrapper">

    <div class="page-content">

        <div class="container-xxl">

            <div class="row">

                <div class="col-xl-8">

                    <div class="card">

                        <div class="card-header">

                            <h4 class="card-title mb-0">
                                <iconify-icon icon="solar:calendar-add-bold-duotone" class="me-2"></iconify-icon>
                                Tambah Cuti / Izin
                            </h4>

                        </div>

                        <div class="card-body">

                            <form action="{{ route('leave-types.store') }}" method="POST">

                                @csrf

                                <div class="row">

                                    {{-- Kode --}}
                                    <div class="col-md-6 mb-3">

                                        <label class="form-label fw-semibold">
                                            Kode
                                            <span class="text-danger">*</span>
                                        </label>

                                        <input
                                            type="text"
                                            name="code"
                                            class="form-control @error('code') is-invalid @enderror"
                                            value="{{ old('code') }}"
                                            placeholder="Contoh : CT-THN"
                                            required>

                                        @error('code')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                        <small class="text-muted">
                                            Kode tidak boleh sama.
                                        </small>

                                    </div>

                                    {{-- Nama --}}
                                    <div class="col-md-6 mb-3">

                                        <label class="form-label fw-semibold">
                                            Nama Cuti / Izin
                                            <span class="text-danger">*</span>
                                        </label>

                                        <input
                                            type="text"
                                            name="name"
                                            class="form-control @error('name') is-invalid @enderror"
                                            value="{{ old('name') }}"
                                            placeholder="Masukkan nama cuti / izin"
                                            required>

                                        @error('name')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                    </div>

                                    {{-- Tag --}}
                                    <div class="col-md-6 mb-3">

                                        <label class="form-label fw-semibold">
                                            Tag
                                            <span class="text-danger">*</span>
                                        </label>

                                        <select
                                            name="tag"
                                            class="form-select @error('tag') is-invalid @enderror"
                                            required>

                                            <option value="">
                                                Pilih Tag
                                            </option>

                                            <option value="cuti"
                                                {{ old('tag') == 'cuti' ? 'selected' : '' }}>
                                                Cuti
                                            </option>

                                            <option value="izin"
                                                {{ old('tag') == 'izin' ? 'selected' : '' }}>
                                                Izin
                                            </option>

                                        </select>

                                        @error('tag')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                    </div>

                                    {{-- Jenis --}}
                                    <div class="col-md-6 mb-3">

                                        <label class="form-label fw-semibold">
                                            Jenis
                                            <span class="text-danger">*</span>
                                        </label>

                                        <select
                                            name="type"
                                            class="form-select @error('type') is-invalid @enderror"
                                            required>

                                            <option value="">
                                                Pilih Jenis
                                            </option>

                                            <option value="company"
                                                {{ old('type') == 'company' ? 'selected' : '' }}>
                                                Perusahaan
                                            </option>

                                            <option value="government"
                                                {{ old('type') == 'government' ? 'selected' : '' }}>
                                                Pemerintah
                                            </option>

                                        </select>

                                        @error('type')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                    </div>

                                    {{-- Kuota --}}
                                    <div class="col-md-6 mb-3">

                                        <label class="form-label fw-semibold">
                                            Kuota
                                        </label>

                                        <div class="input-group">

                                            <input
                                                type="number"
                                                name="quota"
                                                min="0"
                                                class="form-control @error('quota') is-invalid @enderror"
                                                value="{{ old('quota') }}"
                                                placeholder="Contoh : 12">

                                            <span class="input-group-text">
                                                Hari
                                            </span>

                                        </div>

                                        @error('quota')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                        <small class="text-muted">
                                            Kosongkan jika tidak memiliki batas kuota.
                                        </small>

                                    </div>

                                    {{-- Reset --}}
                                    <div class="col-md-6 mb-3">

                                        <label class="form-label fw-semibold">
                                            Reset Kuota
                                            <span class="text-danger">*</span>
                                        </label>

                                        <select
                                            name="reset_period"
                                            class="form-select @error('reset_period') is-invalid @enderror">

                                            <option value="month"
                                                {{ old('reset_period') == 'month' ? 'selected' : '' }}>
                                                Bulan
                                            </option>

                                            <option value="year"
                                                {{ old('reset_period', 'year') == 'year' ? 'selected' : '' }}>
                                                Tahun
                                            </option>

                                            <option value="never"
                                                {{ old('reset_period') == 'never' ? 'selected' : '' }}>
                                                Tidak Ditentukan
                                            </option>

                                        </select>

                                        @error('reset_period')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                    </div>

                                    {{-- Deskripsi --}}
                                    <div class="col-md-12 mb-3">

                                        <label class="form-label fw-semibold">
                                            Keterangan
                                        </label>

                                        <textarea
                                            name="description"
                                            rows="4"
                                            class="form-control @error('description') is-invalid @enderror"
                                            placeholder="Masukkan keterangan atau penjelasan...">{{ old('description') }}</textarea>

                                        @error('description')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                    </div>

                                    {{-- Status --}}
                                    <div class="col-md-12 mb-3">

                                        <label class="form-label fw-semibold">
                                            Status
                                        </label>

                                        <div class="form-check form-switch">

                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                name="is_active"
                                                value="1"
                                                checked>

                                            <label class="form-check-label">
                                                Aktif
                                            </label>

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
                                                <a href="{{ route('leave-types.index') }}"
                                                    class="btn btn-outline-secondary w-100">
                                                    Cancel </a>
                                            </div>

                                        </div>
                                    </div>

                            </form>

                        </div>

                    </div>

                </div>

                {{-- Sidebar --}}
                <div class="col-xl-4">

                    <div class="card">

                        <div class="card-header">
                            <h4 class="card-title">
                                Informasi
                            </h4>
                        </div>

                        <div class="card-body">

                            <div class="alert alert-info mb-0">

                                <h6 class="fw-bold">
                                    Petunjuk
                                </h6>

                                <ul class="mb-0 ps-3">

                                    <li>
                                        Gunakan kode yang unik.
                                    </li>

                                    <li>
                                        Pilih Tag Cuti atau Izin.
                                    </li>

                                    <li>
                                        Kuota dapat dikosongkan jika tidak dibatasi.
                                    </li>

                                    <li>
                                        Tentukan periode reset kuota.
                                    </li>

                                </ul>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection