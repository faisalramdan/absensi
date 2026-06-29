@extends('layouts.app')
@section('title', 'Import Absensi')
@section('content')
    <div class="wrapper">

        <div class="page-content">

            <div class="container-xxl">

                {{-- INFORMASI / PANDUAN IMPORT --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light d-flex align-items-center">
                        <iconify-icon icon="solar:info-circle-bold-duotone" class="text-warning me-2 fs-22"></iconify-icon>
                        <h5 class="mb-0 fw-semibold text-dark">Panduan Penting Sebelum Import Data</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-4 fs-14">
                            Untuk menghindari kegagalan sistem, mohon pastikan file Excel Anda telah disiapkan mengikuti
                            langkah-langkah standarisasi format di bawah ini:
                        </p>

                        {{-- Timeline Langkah-Langkah --}}
                        <div class="position-relative ps-3">
                            <div class="position-absolute start-0 top-0 bottom-0 border-start border-2 border-primary-subtle"
                                style="left: 7px !important;"></div>

                            <div class="position-relative mb-4">
                                <div class="position-absolute start-0 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 24px; height: 24px; left: -24px !important; top: 0px; z-index: 2;">
                                    <small class="fw-bold">1</small>
                                </div>
                                <div class="ms-3">
                                    <h6 class="fw-semibold mb-1">Unduh File dari Mesin Fingerprint</h6>
                                    <p class="text-muted small mb-0">Pastikan Anda telah mengekspor data absensi dari mesin
                                        fingerprint (biasanya berformat bawaan <span
                                            class="badge bg-light text-dark border">.xls</span>).</p>
                                </div>
                            </div>

                            <div class="position-relative mb-4">
                                <div class="position-absolute start-0 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 24px; height: 24px; left: -24px !important; top: 0px; z-index: 2;">
                                    <small class="fw-bold">2</small>
                                </div>
                                <div class="ms-3">
                                    <h6 class="fw-semibold mb-1">Ubah Format File (Save As)</h6>
                                    <p class="text-muted small mb-0">Buka file tersebut di Microsoft Excel, lalu lakukan
                                        <strong>Save As</strong> dan ubah formatnya menjadi <span
                                            class="badge bg-success-subtle text-success border border-success-subtle">.xlsx</span>
                                        (Excel Workbook).
                                    </p>
                                </div>
                            </div>

                            <div class="position-relative mb-4">
                                <div class="position-absolute start-0 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 24px; height: 24px; left: -24px !important; top: 0px; z-index: 2;">
                                    <small class="fw-bold">3</small>
                                </div>
                                <div class="ms-3">
                                    <h6 class="fw-semibold mb-1">Pindahkan Sheet "Catatan" ke Urutan Pertama</h6>
                                    <p class="text-muted small mb-1">Sistem ini dikonfigurasi secara ketat untuk
                                        <strong>hanya membaca sheet pertama</strong>.
                                    </p>
                                    <div class="alert alert-warning py-2 px-3 mb-0 d-inline-block rounded-3 fs-13">
                                        <iconify-icon icon="solar:danger-triangle-bold"
                                            class="me-1 text-warning"></iconify-icon>
                                        Buka file Anda, cari sheet bernama <strong>"Catatan"</strong>, lalu geser/pindahkan
                                        sheet tersebut ke <strong>posisi paling kiri (urutan pertama)</strong>, kemudian
                                        simpan kembali (Save).
                                    </div>
                                </div>
                            </div>

                            <div class="position-relative">
                                <div class="position-absolute start-0 bg-success text-white rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 24px; height: 24px; left: -24px !important; top: 0px; z-index: 2;">
                                    <iconify-icon icon="solar:check-circle-bold" class="fs-14"></iconify-icon>
                                </div>
                                <div class="ms-3">
                                    <h6 class="fw-semibold mb-1 text-success">Siap untuk Di-import</h6>
                                    <p class="text-muted small mb-0">File Anda sekarang sudah memenuhi standar sistem dan
                                        siap diunggah pada form di bawah ini.</p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- FORM IMPORT ATTENDANCE --}}
                <div class="card border-0 shadow-sm">

                    <div class="card-header d-flex align-items-center">
                        <iconify-icon icon="solar:document-upload-bold-duotone"
                            class="text-primary me-2 fs-20"></iconify-icon>
                        <h5 class="mb-0 fw-semibold">Upload File Attendance</h5>
                    </div>

                    <div class="card-body">

                        <form action="{{ route('attendance-logs.import') }}" method="POST" enctype="multipart/form-data">

                            @csrf

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Pilih File Excel</label>
                                <input type="file" name="file" class="form-control" accept=".xlsx" required>
                                <div class="form-text">Hanya menerima format berkas .xlsx yang sudah disesuaikan.</div>
                            </div>

                            {{-- INFORMASI OVERWRITE / UPDATE DATA (YANG BARU DITAMBAHKAN) --}}
                            <div class="alert alert-info border-0 shadow-sm d-flex align-items-start gap-2 mb-4 rounded-3">
                                <iconify-icon icon="solar:info-circle-bold text-info" class="fs-20 mt-1"></iconify-icon>
                                <div>
                                    <h6 class="alert-heading fw-semibold mb-1">Catatan Mengenai Pembaruan Data:</h6>
                                    <p class="text-muted small mb-0">
                                        Jika sebelumnya Anda sudah pernah meng-import data untuk periode yang sama, proses
                                        import yang baru ini akan <strong>otomatis menimpa (meng-update)</strong> data lama
                                        tersebut dengan data terbaru yang ada di dalam file Excel Anda. Data tidak akan
                                        menjadi ganda (double).
                                    </p>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary px-4">
                                <iconify-icon icon="solar:import-bold" class="me-1 align-middle"></iconify-icon> Import Data
                            </button>

                            <a href="{{ route('attendance-logs.index') }}" class="btn btn-secondary">
                                Kembali
                            </a>

                        </form>

                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection