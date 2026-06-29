@extends('layouts.app')

@section('title', 'Ajukan Cuti / Izin')

@section('content')
    <!-- START Wrapper -->
    <div class="wrapper">
        <div class="page-content">
            <div class="container-xxl">

                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <form action="{{ route('leave-requests.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-xl-9 col-lg-8 ">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Form Pengajuan Cuti / Izin</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-semibold">
                                                Jenis Cuti
                                                <span class="text-danger">*</span>
                                            </label>

                                            <select name="leave_type_id" class="form-select" required>
                                                <option value="">Pilih</option>

                                                {{-- Mengubah looping dari master LeaveType menjadi dari LeaveAllocation karyawan --}}
                                                @foreach($leaveAllocations ?? [] as $allocation)
                                                    @php
                                                        // Mengambil model leaveType dari relasi allocation
                                                        $leaveType = $allocation->leaveType;
                                                        
                                                        // Menghilangkan desimal .00 menggunakan floatval
                                                        $remaining = floatval($allocation->remaining_days);
                                                        $allocated = floatval($allocation->allocated_days);
                                                    @endphp

                                                    @if($leaveType)
                                                        {{-- Hanya memunculkan jika jatah cuti masih ada atau bisa diajukan --}}
                                                        <option value="{{ $leaveType->id }}" {{ $remaining <= 0 ? 'disabled class=text-muted' : '' }}>
                                                            {{ $leaveType->name }} 
                                                            (Sisa {{ $remaining }} / {{ $allocated }} Hari)
                                                            
                                                            @if($remaining <= 0)
                                                                - [Kuota Habis]
                                                            @endif
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>


                                        <div class="col-md-3 mb-3">
                                            <label class="form-label fw-semibold">
                                                Tanggal Mulai
                                                <span class="text-danger">*</span>
                                            </label>

                                            <input type="date" name="start_date" min="{{ date('Y-m-d') }}" value="{{ old('start_date') }}"
                                                class="form-control" required>
                                        </div>

                                        <div class="col-md-3 mb-3">
                                            <label class="form-label fw-semibold">
                                                Tanggal Selesai
                                                <span class="text-danger">*</span>
                                            </label>

                                            <input type="date" name="end_date" min="{{ date('Y-m-d') }}" value="{{ old('end_date') }}"
                                                class="form-control" required>
                                        </div>

                                    </div>

                                    <div class="row">

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-semibold">
                                                Alasan
                                                <span class="text-danger">*</span>
                                            </label>

                                            <textarea name="reason" rows="4" class="form-control"
                                                required>{{ old('reason') }}</textarea>
                                        </div>


                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-semibold">
                                                Lampiran
                                            </label>

                                            <input type="file" name="attachment" class="form-control">
                                        </div>

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
                                        <a href="{{ route('leave-requests.index') }}"
                                            class="btn btn-outline-secondary w-100">
                                            Cancel </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">
                                        Informasi Pengajuan Cuti
                                    </h4>
                                </div>

                                <div class="card-body">
                                    <p>
                                        Silakan lengkapi form pengajuan cuti atau izin dengan benar sesuai kebutuhan.
                                    </p>
                                    <ul class="mb-0 ps-3">
                                        <li>Pastikan jenis cuti yang dipilih sesuai dengan ketentuan perusahaan.</li>
                                        <li>Isi tanggal mulai dan tanggal selesai dengan benar.</li>
                                        <li>Pengajuan cuti harus dilakukan minimal beberapa hari sebelumnya.</li>
                                        <li>Alasan pengajuan wajib diisi dengan jelas dan dapat dipertanggungjawabkan.</li>
                                        <li>Lampiran wajib disertakan jika cuti berkaitan dengan sakit atau keperluan
                                            tertentu.</li>
                                    </ul>

                                </div>
                                <div class="mt-3 p-3 bg-light rounded">
                                    <small class="text-muted">
                                        ⚠️ Pengajuan akan masuk ke proses approval atasan.
                                        Pastikan data sudah benar sebelum disimpan.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>


                </form>



            </div>
        </div>
    </div>

@endsection