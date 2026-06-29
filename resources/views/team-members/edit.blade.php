@extends('layouts.app')

@section('title', 'Edit Anggota Team')

@section('content')

<div class="wrapper">

    <div class="page-content">

        <div class="container-xxl">

            <div class="card">

                <div class="card-header">

                    <h4 class="card-title mb-0">

                        Edit Anggota Team

                    </h4>

                </div>

                <form action="{{ route('teams.members.update', [$team, $member]) }}"
                    method="POST">

                    @csrf
                    @method('PUT')

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

                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    value="{{ $member->employee->full_name }}
                                    @if($member->employee->position)
                                        - {{ $member->employee->position->name }}
                                    @endif"
                                    readonly>

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
                                        @selected(old('member_role', $member->member_role) == 'Leader')>

                                        Leader

                                    </option>

                                    <option value="Co Leader"
                                        @selected(old('member_role', $member->member_role) == 'Co Leader')>

                                        Co Leader

                                    </option>

                                    <option value="Member"
                                        @selected(old('member_role', $member->member_role) == 'Member')>

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

                            <div class="col-lg-3 mb-3">

                                <label class="form-label">

                                    Tanggal Bergabung

                                </label>

                                <input
                                    type="date"
                                    name="joined_at"
                                    class="form-control @error('joined_at') is-invalid @enderror"
                                    value="{{ old('joined_at', optional($member->joined_at)->format('Y-m-d') ?? $member->joined_at) }}">

                                @error('joined_at')

                                    <div class="invalid-feedback">

                                        {{ $message }}

                                    </div>

                                @enderror

                            </div>

                            {{-- Left At --}}

                            <div class="col-lg-3 mb-3">

                                <label class="form-label">

                                    Tanggal Keluar

                                </label>

                                <input
                                    type="date"
                                    name="left_at"
                                    class="form-control @error('left_at') is-invalid @enderror"
                                    value="{{ old('left_at', optional($member->left_at)->format('Y-m-d') ?? $member->left_at) }}">

                                @error('left_at')

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

                                <div class="form-check form-check-inline">

                                    <input
                                        class="form-check-input"
                                        type="radio"
                                        name="is_active"
                                        value="1"
                                        {{ old('is_active', $member->is_active) == 1 ? 'checked' : '' }}>

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
                                        {{ old('is_active', $member->is_active) == 0 ? 'checked' : '' }}>

                                    <label class="form-check-label">

                                        Non Aktif

                                    </label>

                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="card-footer text-end">

                        <a href="{{ route('teams.members.index', $team) }}"
                            class="btn btn-light">

                            Kembali

                        </a>

                        <button
                            type="submit"
                            class="btn btn-primary">

                            Update

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>

@endsection