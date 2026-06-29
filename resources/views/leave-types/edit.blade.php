@extends('layouts.app')
@section('title', 'Perbarui Cuti / Izin')
@section('content')

<div class="wrapper">

    <div class="page-content">

        <div class="container-xxl">

            <div class="row">

                <div class="col-xl-8">

                    <div class="card">

                        <div class="card-header">

                            <h4 class="card-title mb-0">
                                <iconify-icon icon="solar:pen-bold-duotone" class="me-2"></iconify-icon>
                                Perbarui Cuti / Izin
                            </h4>

                        </div>

                        <div class="card-body">

                            <form action="{{ route('leave-types.update', $leaveType) }}"
                                method="POST">

                                @csrf
                                @method('PUT')

                                <div class="row">

                                    {{-- Kode --}}
                                    <div class="col-md-6 mb-3">

                                        <label class="form-label fw-semibold">
                                            Kode
                                            <span class="text-danger">*</span>
                                        </label>

                                        <input type="text"
                                            name="code"
                                            class="form-control @error('code') is-invalid @enderror"
                                            value="{{ old('code', $leaveType->code) }}"
                                            required>

                                        @error('code')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                    </div>

                                    {{-- Nama --}}
                                    <div class="col-md-6 mb-3">

                                        <label class="form-label fw-semibold">
                                            Nama Cuti / Izin
                                            <span class="text-danger">*</span>
                                        </label>

                                        <input type="text"
                                            name="name"
                                            class="form-control @error('name') is-invalid @enderror"
                                            value="{{ old('name', $leaveType->name) }}"
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
                                        </label>

                                        <select name="tag" class="form-select">

                                            <option value="cuti"
                                                {{ old('tag', $leaveType->tag) == 'cuti' ? 'selected' : '' }}>
                                                Cuti
                                            </option>

                                            <option value="izin"
                                                {{ old('tag', $leaveType->tag) == 'izin' ? 'selected' : '' }}>
                                                Izin
                                            </option>

                                        </select>

                                    </div>

                                    {{-- Jenis --}}
                                    <div class="col-md-6 mb-3">

                                        <label class="form-label fw-semibold">
                                            Jenis
                                        </label>

                                        <select name="type" class="form-select">

                                            <option value="company"
                                                {{ old('type', $leaveType->type) == 'company' ? 'selected' : '' }}>
                                                Perusahaan
                                            </option>

                                            <option value="government"
                                                {{ old('type', $leaveType->type) == 'government' ? 'selected' : '' }}>
                                                Pemerintah
                                            </option>

                                        </select>

                                    </div>

                                    {{-- Kuota --}}
                                    <div class="col-md-6 mb-3">

                                        <label class="form-label fw-semibold">
                                            Kuota
                                        </label>

                                        <input type="number"
                                            name="quota"
                                            class="form-control"
                                            value="{{ old('quota', $leaveType->quota) }}">

                                    </div>

                                    {{-- Reset --}}
                                    <div class="col-md-6 mb-3">

                                        <label class="form-label fw-semibold">
                                            Reset Kuota
                                        </label>

                                        <select
                                            name="reset_period"
                                            class="form-select">

                                            <option value="month"
                                                {{ old('reset_period', $leaveType->reset_period) == 'month' ? 'selected' : '' }}>
                                                Bulan
                                            </option>

                                            <option value="year"
                                                {{ old('reset_period', $leaveType->reset_period) == 'year' ? 'selected' : '' }}>
                                                Tahun
                                            </option>

                                            <option value="never"
                                                {{ old('reset_period', $leaveType->reset_period) == 'never' ? 'selected' : '' }}>
                                                Tidak Ditentukan
                                            </option>

                                        </select>

                                    </div>

                                    {{-- Deskripsi --}}
                                    <div class="col-md-12 mb-3">

                                        <label class="form-label fw-semibold">
                                            Keterangan
                                        </label>

                                        <textarea
                                            name="description"
                                            rows="4"
                                            class="form-control">{{ old('description', $leaveType->description) }}</textarea>

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
                                                {{ $leaveType->is_active ? 'checked' : '' }}>

                                            <label class="form-check-label">
                                                {{ $leaveType->is_active ? 'Aktif' : 'Non Aktif' }}
                                            </label>

                                        </div>

                                    </div>

                                </div>

                                <div class="p-3 bg-light mb-3 rounded">
                                        <div class="row justify-content-end g-2">
                                            <div class="col-lg-2">
                                                <button type="submit" class="btn btn-primary w-100">
                                                    Perbarui</button>
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

                            <ul class="list-group list-group-flush">

                                <li class="list-group-item">

                                    <strong>Dibuat Oleh :</strong>

                                    <br>

                                    {{ $leaveType->creator?->name ?? '-' }}

                                    <br>

                                    {{ $leaveType->created_at->format('d M Y H:i') }}

                                </li>

                                <li class="list-group-item">

                                    <strong>Terakhir Diubah :</strong>

                                    <br>

                                    {{ $leaveType->updater?->name ?? '-' }}

                                    <br>

                                    {{ $leaveType->updated_at->diffForHumans() }}

                                </li>

                            </ul>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection