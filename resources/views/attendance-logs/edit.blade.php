@extends('layouts.app')
@section('title', 'Edit Attendance Log')
@section('content')

    <div class="wrapper">

        <div class="page-content">

            <div class="container-xxl">

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <iconify-icon icon="solar:danger-triangle-bold" class="me-1"></iconify-icon>
                        <strong>Terjadi Kesalahan!</strong> Mohon periksa kembali inputan Anda.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="card border-0 shadow-sm">

                    <div class="card-header d-flex align-items-center">
                        <iconify-icon icon="solar:pen-2-bold-duotone" class="text-primary me-2 fs-20">
                        </iconify-icon>
                        <h5 class="mb-0 fw-semibold">
                            Edit Attendance Log
                        </h5>
                    </div>

                    <div class="card-body">

                        <form action="{{ route('attendance-logs.update', $attendanceLog) }}" method="POST">

                            @csrf
                            @method('PUT')

                            {{-- Render komponen fields form dari file partial --}}
                            @include('attendance-logs._form')

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="{{ route('attendance-logs.index') }}" class="btn btn-secondary">
                                    Batal
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    Perbarui Data
                                </button>
                            </div>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection