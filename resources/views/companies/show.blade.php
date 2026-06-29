@extends('layouts.app')
@section('title', 'Detail Perusahaan')
@section('content')
    <div class="wrapper">
        <div class="page-content">
            <div class="container-xxl">

                <div class="row">

                    <div class="col-xl-8">

                        <div class="card">

                            <div class="card-header">
                                Detail Perusahaan
                            </div>

                            <div class="card-body">

                                <table class="table">

                                    <tr>
                                        <th width="200">Kode</th>
                                        <td>{{ $company->code }}</td>
                                    </tr>

                                    <tr>
                                        <th>Nama</th>
                                        <td>{{ $company->name }}</td>
                                    </tr>

                                    <tr>
                                        <th>Email</th>
                                        <td>{{ $company->email }}</td>
                                    </tr>

                                    <tr>
                                        <th>Telepon</th>
                                        <td>{{ $company->phone }}</td>
                                    </tr>

                                    <tr>
                                        <th>Alamat</th>
                                        <td>{{ $company->address }}</td>
                                    </tr>

                                    <tr>
                                        <th>Status</th>
                                        <td>

                                            @if($company->is_active)
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

                                    <tr>
                                        <th>Logo</th>
                                        <td>

                                            @if($company->logo)

                                                <img src="{{ asset('storage/' . $company->logo) }}" width="120">

                                            @else

                                                -

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
                                Informasi
                            </div>

                            <div class="card-body">

                                <ul class="list-group">

                                    <li class="list-group-item">
                                        <strong>Dibuat Oleh</strong><br>
                                        {{ $company->creator?->name ?? '-' }}
                                    </li>

                                    <li class="list-group-item">
                                        <strong>Diperbarui Oleh</strong><br>
                                        {{ $company->updater?->name ?? '-' }}
                                    </li>

                                    <li class="list-group-item">
                                        <strong>Dibuat Tanggal</strong><br>
                                        {{ $company->created_at?->format('d M Y H:i') }}
                                    </li>

                                    <li class="list-group-item">
                                        <strong>Diupdate Tanggal</strong><br>
                                        {{ $company->updated_at?->format('d M Y H:i') }}
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