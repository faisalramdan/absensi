@extends('layouts.app')
@section('title', 'Detail Cuti / Izin')
@section('content')

    <div class="wrapper">

        <div class="page-content"> 

            <div class="container-xxl">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h3 class="fw-bold">
                           
                        </h3>
                        <p class="text-muted mb-0">
                           
                        </p>
                    </div>

                    <a href="{{ route('leave-types.index') }}" class="btn btn-secondary">
                        <iconify-icon icon="solar:arrow-left-bold"></iconify-icon>
                        Kembali
                    </a>
                </div>

                <div class="row">
                    <div class="col-xl-8">
                        <div class="card">
                            
                            <div class="card-header">
                                <h4 class="card-title">
                                    Informasi Cuti / Izin
                                </h4>
                            </div>

                            <div class="card-body">

                                <table class="table table-bordered">

                                    <tr>
                                        <th width="30%">Kode</th>
                                        <td>{{ $leaveType->code }}</td>
                                    </tr>

                                    <tr>
                                        <th>Nama</th>
                                        <td>{{ $leaveType->name }}</td>
                                    </tr>

                                    <tr>
                                        <th>Tag</th>
                                        <td>{{ ucfirst($leaveType->tag) }}</td>
                                    </tr>

                                    <tr>
                                        <th>Jenis</th>
                                        <td>
                                            {{ $leaveType->type == 'company' ? 'Perusahaan' : 'Pemerintah' }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Kuota</th>
                                        <td>
                                            {{ $leaveType->quota ?? '-' }}
                                            Hari
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Reset Kuota</th>
                                        <td>
                                            @switch($leaveType->reset_period)

                                                @case('month')
                                                    Bulanan
                                                    @break

                                                @case('year')
                                                    Tahunan
                                                    @break

                                                @case('never')
                                                    Tidak Ditentukan
                                                    @break

                                            @endswitch
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Keterangan</th>
                                        <td>
                                            {{ $leaveType->description ?? '-' }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Status</th>
                                        <td>

                                            @if($leaveType->is_active)

                                                <span class="badge bg-success">
                                                    Aktif
                                                </span>

                                            @else

                                                <span class="badge bg-danger">
                                                    Non Aktif
                                                </span>

                                            @endif

                                        </td>
                                    </tr>

                                </table>

                            </div>

                        </div>

                    </div>

                    <div class="col-xl-4">

                        <div class="card">

                            <div class="card-header">

                                <h4 class="card-title">
                                    Audit Informasi
                                </h4>

                            </div>

                            <div class="card-body">

                                <ul class="list-group list-group-flush">

                                    <li class="list-group-item">

                                        <strong>Dibuat Oleh</strong>

                                        <br>

                                        {{ $leaveType->creator?->name ?? '-' }}

                                    </li>

                                    <li class="list-group-item">

                                        <strong>Dibuat Pada</strong>

                                        <br>

                                        {{ $leaveType->created_at->format('d M Y H:i') }}

                                    </li>

                                    <li class="list-group-item">

                                        <strong>Diubah Oleh</strong>

                                        <br>

                                        {{ $leaveType->updater?->name ?? '-' }}

                                    </li>

                                    <li class="list-group-item">

                                        <strong>Terakhir Diubah</strong>

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