@extends('layouts.app')
@section('title', 'Perbarui Perusahaan')
@section('content')
    <!-- START Wrapper -->
    <div class="wrapper">
        <div class="page-content">
            <div class="container-xxl">

                <div class="row">

                    <div class="col-xl-8">

                        <div class="card">

                            <div class="card-header">
                                <h4 class="card-title">
                                    Perbarui Perusahaan
                                </h4>
                            </div>

                            <div class="card-body">

                                <form action="{{ route('companies.update', $company) }}" method="POST"
                                    enctype="multipart/form-data">

                                    @csrf
                                    @method('PUT')

                                    <div class="row">

                                        <div class="col-md-6 mb-3">

                                            <label class="form-label">
                                                Kode
                                                <span class="text-danger">*</span>
                                            </label>

                                            <input type="text" name="code"
                                                class="form-control @error('code') is-invalid @enderror"
                                                value="{{ old('code', $company->code) }}">

                                            @error('code')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror

                                        </div>

                                        <div class="col-md-6 mb-3">

                                            <label class="form-label">
                                                Nama Perusahaan
                                                <span class="text-danger">*</span>
                                            </label>

                                            <input type="text" name="name"
                                                class="form-control @error('name') is-invalid @enderror"
                                                value="{{ old('name', $company->name) }}">

                                            @error('name')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror

                                        </div>

                                        <div class="col-md-6 mb-3">

                                            <label class="form-label">
                                                Email
                                            </label>

                                            <input type="email" name="email" class="form-control"
                                                value="{{ old('email', $company->email) }}">

                                        </div>

                                        <div class="col-md-6 mb-3">

                                            <label class="form-label">
                                                Telepon
                                            </label>

                                            <input type="text" name="phone" class="form-control"
                                                value="{{ old('phone', $company->phone) }}">

                                        </div>

                                        <div class="col-md-12 mb-3">

                                            <label class="form-label">
                                                Alamat
                                            </label>

                                            <textarea name="address" rows="3"
                                                class="form-control">{{ old('address', $company->address) }}</textarea>

                                        </div>

                                        <div class="col-md-6 mb-3">

                                            <label class="form-label">
                                                Logo
                                            </label>

                                            <input type="file" name="logo" class="form-control">

                                            @if($company->logo)

                                                <div class="mt-2">

                                                    @if($company->logo)

                                                        <img src="{{ asset('storage/' . $company->logo) }}" class="img-thumbnail"
                                                            width="120">

                                                    @else

                                                        <img src="{{ asset('assets/images/no-image.png') }}" class="img-thumbnail"
                                                            width="120">

                                                    @endif

                                                </div>

                                            @endif

                                        </div>

                                        <div class="col-md-6 mb-3">

                                            <label class="form-label">
                                                Status
                                            </label>

                                            <div class="form-check form-switch">

                                                <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                                    {{ $company->is_active ? 'checked' : '' }}>

                                                <label class="form-check-label">
                                                    {{ $company->is_active ? 'Aktif' : 'Non Aktif' }}
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
                                                <a href="{{ route('companies.index') }}"
                                                    class="btn btn-outline-secondary w-100">
                                                    Cancel </a>
                                            </div>

                                        </div>
                                    </div>

                                </form>

                            </div>

                        </div>

                    </div>

                    <div class="col-xl-4">

                        <div class="card">

                            <div class="card-header">
                                Informasi
                            </div>

                            <div class="card-body">

                                <ul class="list-group list-group-flush">

                                    <li class="list-group-item">

                                        <strong>Dibuat Oleh :</strong><br>

                                        {{ $company->creator?->name ?? '-' }}

                                        <br>

                                        {{ $company->created_at?->format('d M Y H:i') ?? '-' }}

                                    </li>

                                    <li class="list-group-item">

                                        <strong>Terakhir Diubah :</strong><br>

                                        {{ $company->updater?->name ?? '-' }}

                                        <br>

                                        {{ $company->updated_at?->diffForHumans() ?? '-' }}

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