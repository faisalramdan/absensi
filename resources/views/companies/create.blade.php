@extends('layouts.app')
@section('title', 'Tambah Perusahaan')
@section('content')
    <!-- START Wrapper -->
    <div class="wrapper">
        <!-- ==================================================== -->
        <!-- Start right Content here -->
        <!-- ==================================================== -->
        <div class="page-content">

            <!-- Start Container Fluid -->
            <div class="container-xxl">


                <div class="card">

                    <div class="card-header">
                        <h4 class="card-title">
                            Tambah Perusahaan
                        </h4>
                    </div>

                    <div class="card-body">

                        <form action="{{ route('companies.store') }}" method="POST" enctype="multipart/form-data">

                            @csrf

                            <div class="row">

                                <div class="col-md-6 mb-3">
                                    <label>Kode *</label>
                                    <input type="text" name="code" class="form-control" value="{{ old('code') }}">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>Nama *</label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name') }}">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>Telepon</label>
                                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label>Alamat</label>
                                    <textarea name="address" class="form-control" rows="3">{{ old('address') }}</textarea>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>Logo</label>
                                    <input type="file" name="logo" class="form-control">
                                </div>

                                <div class="col-md-6 mb-3">

                                    <label>Status</label>

                                    <div class="form-check form-switch">

                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>

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
                                        <a href="{{ route('companies.index') }}" class="btn btn-outline-secondary w-100">
                                            Cancel </a>
                                    </div>

                                </div>
                            </div>

                        </form>

                    </div>

                </div>

            </div>
        </div>
    </div>

@endsection