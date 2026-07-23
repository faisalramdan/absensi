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
                        <div class="alert alert-primary py-2 px-3 mb-4 rounded-3 d-flex align-items-start">
                            <iconify-icon icon="solar:bell-bing-bold-duotone"
                                class="fs-20 me-2 mt-1 text-primary"></iconify-icon>
                            <span class="fs-14"><strong>Gunakan Format Template Mandiri:</strong> Pastikan Anda menggunakan
                                file template Excel absensi mandiri yang sudah disediakan. Harap perhatikan hal-hal krusial
                                di bawah ini sebelum melakukan proses import.</span>
                        </div>

                        {{-- Timeline Langkah-Langkah --}}
                        <div class="position-relative ps-3">
                            <div class="position-absolute start-0 top-0 bottom-0 border-start border-2 border-primary-subtle"
                                style="left: 7px !important;"></div>

                            {{-- LANGKAH 1: NIK KARYAWAN --}}
                            <div class="position-relative mb-4">
                                <div class="position-absolute start-0 bg-danger text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                                    style="width: 24px; height: 24px; left: -24px !important; top: 0px; z-index: 2;">
                                    <small class="fw-bold">1</small>
                                </div>
                                <div class="ms-3">
                                    <h6 class="fw-semibold mb-1 text-danger">Kesesuaian NIK Karyawan (Sangat Penting!)</h6>
                                    <p class="text-muted small mb-1">Data absensi dicocokkan berdasarkan Nomor Induk
                                        Karyawan.</p>
                                    <div
                                        class="alert alert-danger py-2 px-3 mb-0 d-inline-block rounded-3 fs-13 border-danger-subtle">
                                        <iconify-icon icon="solar:danger-triangle-bold"
                                            class="me-1 text-danger"></iconify-icon>
                                        Pastikan <strong>NIK</strong> dari setiap karyawan di file Excel <strong>sama
                                            persis</strong> dengan NIK yang terdaftar di dalam sistem HRIS. Jika NIK
                                        salah/berbeda, data absen karyawan tersebut tidak akan masuk!
                                    </div>
                                </div>
                            </div>

                            {{-- LANGKAH 2: FORMAT PERIODE --}}
                            <div class="position-relative mb-4">
                                <div class="position-absolute start-0 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 24px; height: 24px; left: -24px !important; top: 0px; z-index: 2;">
                                    <small class="fw-bold">2</small>
                                </div>
                                <div class="ms-3">
                                    <h6 class="fw-semibold mb-1">Perhatikan Format Penulisan Periode</h6>
                                    <p class="text-muted small mb-0">
                                        Periksa kembali format tanggal pada baris periode (contoh:
                                        <code>Periode: 26 Juni 2026 s/d 25 Juli 2026</code>). Pastikan ejaan dan penulisan
                                        tahunnya benar agar sistem dapat mendeteksi bulan absensi dengan tepat.
                                    </p>
                                </div>
                            </div>

                            {{-- LANGKAH 3: STRUKTUR HEADER --}}
                            <div class="position-relative mb-4">
                                <div class="position-absolute start-0 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 24px; height: 24px; left: -24px !important; top: 0px; z-index: 2;">
                                    <small class="fw-bold">3</small>
                                </div>
                                <div class="ms-3">
                                    <h6 class="fw-semibold mb-1">Jangan Ubah Baris Judul (Header)</h6>
                                    <p class="text-muted small mb-0">
                                        Biarkan <strong>Baris 1 hingga 4</strong> (Data Absensi, Nama PT, dan baris Periode)
                                        berada di posisinya. Sistem diprogram untuk mulai membaca data absen persis di bawah
                                        baris tersebut.
                                    </p>
                                </div>
                            </div>

                            {{-- SIAP IMPORT --}}
                            <div class="position-relative">
                                <div class="position-absolute start-0 bg-success text-white rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 24px; height: 24px; left: -24px !important; top: 0px; z-index: 2;">
                                    <iconify-icon icon="solar:check-circle-bold" class="fs-14"></iconify-icon>
                                </div>
                                <div class="ms-3">
                                    <h6 class="fw-semibold mb-1 text-success">Siap untuk Di-import</h6>
                                    <p class="text-muted small mb-0">Jika NIK, Periode, dan struktur file sudah benar, file
                                        Anda siap diunggah pada form di bawah ini dalam format <span
                                            class="badge bg-success-subtle text-success border border-success-subtle">.xlsx</span>
                                        atau <span
                                            class="badge bg-success-subtle text-success border border-success-subtle">.csv</span>.
                                    </p>
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
                                <label class="form-label fw-semibold">Pilih File Absensi (Format Mandiri)</label>
                                <input type="file" name="file" class="form-control" accept=".xlsx,.csv" required>
                                <div class="form-text">Menerima berkas berekstensi .xlsx atau .csv.</div>
                            </div>

                            {{-- INFORMASI OVERWRITE / UPDATE DATA --}}
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