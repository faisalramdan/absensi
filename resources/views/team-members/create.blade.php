@extends('layouts.app')

@section('title', 'Tambah Anggota Team')

@section('content')

<div class="wrapper">

    <div class="page-content">

        <div class="container-xxl">

            <div class="card">

                <div class="card-header">

                    <h4 class="card-title mb-0">

                        Tambah Anggota Team

                    </h4>

                </div>

                <form action="{{ route('teams.members.store', $team) }}"
                    method="POST">

                    @csrf

                    <div class="card-body">

                        <div class="row">

                            {{-- Team --}}

                            <div class="col-lg-6 mb-3">

                                <label class="form-label">

                                    Team

                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    value="{{ $team->name }}"
                                    readonly>

                            </div>

                            {{-- Company --}}

                            <div class="col-lg-6 mb-3">

                                <label class="form-label">

                                    Company

                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    value="{{ $team->company?->name }}"
                                    readonly>

                            </div>

                            {{-- Employee --}}

                            <div class="col-lg-12 mb-3">

                                <label class="form-label">

                                    Karyawan

                                    <span class="text-danger">*</span>

                                </label>

                                <select
                                    name="employee_id"
                                    class="form-select @error('employee_id') is-invalid @enderror"
                                    required>

                                    <option value="">

                                        Pilih Karyawan

                                    </option>

                                    @foreach($employees as $employee)

                                        <option
                                            value="{{ $employee->id }}"
                                            @selected(old('employee_id')==$employee->id)>

                                            {{ $employee->full_name }}

                                            @if($employee->position)

                                                - {{ $employee->position->name }}

                                            @endif

                                        </option>

                                    @endforeach

                                </select>

                                @error('employee_id')

                                    <div class="invalid-feedback">

                                        {{ $message }}

                                    </div>

                                @enderror

                            </div>

                            {{-- Role --}}

                            <div class="col-lg-6 mb-3">

                                <label class="form-label">

                                    Role

                                    <span class="text-danger">*</span>

                                </label>

                                <select
                                    name="member_role"
                                    class="form-select @error('member_role') is-invalid @enderror">

                                    <option value="Leader"
                                        @selected(old('member_role')=='Leader')>

                                        Leader

                                    </option>

                                    <option value="Co Leader"
                                        @selected(old('member_role')=='Co Leader')>

                                        Co Leader

                                    </option>

                                    <option value="Member"
                                        @selected(old('member_role','Member')=='Member')>

                                        Member

                                    </option>

                                </select>

                                @error('member_role')

                                    <div class="invalid-feedback">

                                        {{ $message }}

                                    </div>

                                @enderror

                            </div>

                            {{-- Joined At --}}

                            <div class="col-lg-6 mb-3">

                                <label class="form-label">

                                    Tanggal Bergabung

                                </label>

                                <input
                                    type="date"
                                    name="joined_at"
                                    class="form-control @error('joined_at') is-invalid @enderror"
                                    value="{{ old('joined_at', now()->format('Y-m-d')) }}">

                                @error('joined_at')

                                    <div class="invalid-feedback">

                                        {{ $message }}

                                    </div>

                                @enderror

                            </div>

                            {{-- Status --}}

                            <div class="col-lg-12">

                                <label class="form-label d-block">

                                    Status

                                </label>

                                {{-- Pilihan: Aktif (Default) --}}
                                <div class="form-check form-check-inline">
                                    <input
                                        class="form-check-input"
                                        type="radio"
                                        name="is_active"
                                        value="1"
                                        {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label">
                                        Aktif
                                    </label>
                                </div>

                                {{-- Pilihan: Non Aktif --}}
                                <div class="form-check form-check-inline">
                                    <input
                                        class="form-check-input"
                                        type="radio"
                                        name="is_active"
                                        value="0"
                                        {{ old('is_active') === '0' ? 'checked' : '' }}>
                                    <label class="form-check-label">
                                        Non Aktif
                                    </label>
                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="card-footer text-end">

                        <a href="{{ route('teams.members.index',$team) }}"
                            class="btn btn-light">

                            Kembali

                        </a>

                        <button
                            type="submit"
                            class="btn btn-primary">

                            Simpan

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>

@endsection