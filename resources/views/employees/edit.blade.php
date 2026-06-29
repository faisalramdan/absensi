@extends('layouts.app')
@section('title', 'Edit Karyawan')
@section('content')
    <!-- START Wrapper -->
    <div class="wrapper">
        <!-- ==================================================== -->
        <!-- Start right Content here -->
        <!-- ==================================================== -->
        <div class="page-content">
            
            <!-- Start Container Fluid -->
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
                <form action="{{ route('employees.update', $employee) }}"
                  method="POST"
                  enctype="multipart/form-data">

                @csrf
                @method('PUT')
                    <div class="row">
                        <div class="col-xl-9 col-lg-8 ">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Informasi Dasar</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-6">
                                         
                                                <div class="mb-3">
                                                    <label for="nik" class="form-label fw-semibold">
                                                        NIK (Nomor Induk Karyawan) & ID Fingerprint
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" name="nik" class="form-control" value="{{ old('nik', $employee->nik) }}"
                                                        placeholder="Masukkan NIK" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="email" class="form-label fw-semibold">
                                                        Email
                                                        <span class="text-danger">*</span>
                                                    </label>

                                                    <input type="email" name="email" class="form-control"
                                                        value="{{ old('email', $employee->email) }}" placeholder="Masukkan Email" required>
                                                </div>
                                        </div>
                                        <div class="col-lg-6">
                                           
                                                <div class="mb-3">
                                                    <label for="full_name" class="form-label fw-semibold">
                                                        Nama Lengkap
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" name="full_name" class="form-control"
                                                        value="{{ old('full_name', $employee->full_name) }}" placeholder="Masukkan Nama Lengkap"
                                                        required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="phone" class="form-label fw-semibold">
                                                        No HP
                                                        <span class="text-danger">*</span>
                                                    </label>

                                                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $employee->phone) }}" placeholder="Masukkan No HP" required>
                                                </div>
                                        </div>
                                        <div class="col-lg-6">
                                    
                                                <div class="mb-3">
                                                    <label for="gender" class="form-label fw-semibold">
                                                        Jenis Kelamin
                                                        <span class="text-danger">*</span>
                                                    </label>

                                                    <select name="gender" class="form-select" required>
                                                        <option value="">Pilih</option>
                                                        <option value="Laki-Laki"
                                                            {{ old('gender', $employee->gender) == 'Laki-Laki' ? 'selected' : '' }}>
                                                            Laki-Laki
                                                        </option>
                                                        <option value="Perempuan"
                                                            {{ old('gender', $employee->gender) == 'Perempuan' ? 'selected' : '' }}>
                                                            Perempuan
                                                        </option>
                                                    </select>
                                                </div>
                                        
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Data Kelahiran</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-6">

                                                <div class="mb-3">
                                                    <label for="birth_place" class="form-label fw-semibold">
                                                        Tempat Lahir
                                                        <span class="text-danger">*</span>
                                                    </label>

                                                    <input type="text" name="birth_place" class="form-control" value="{{ old('birth_place', $employee->birth_place) }}" placeholder="Masukkan Tempat Lahir" required>
                                                </div>

                                        </div>
                                        <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="birth_date" class="form-label fw-semibold">
                                                    Tanggal Lahir
                                                    <span class="text-danger">*</span>
                                                </label>

                                                <input
                                                    type="date"
                                                    name="birth_date"
                                                    class="form-control"
                                                    value="{{ old('birth_date', $employee->birth_date) }}"
                                                    required>
                                                </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Data Pendidikan</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="education" class="form-label fw-semibold">
                                                        Pendidikan Terakhir
                                                        <span class="text-danger">*</span>
                                                    </label>

                                                    <select
                                                        name="education"
                                                        class="form-select"
                                                        required>

                                                        <option value="">Pilih</option>

                                                        <option value="SD"
                                                            {{ old('education', $employee->education) == 'SD' ? 'selected' : '' }}>
                                                            SD
                                                        </option>

                                                        <option value="SMP"
                                                            {{ old('education', $employee->education) == 'SMP' ? 'selected' : '' }}>
                                                            SMP
                                                        </option>

                                                        <option value="SMA/SMK"
                                                            {{ old('education', $employee->education) == 'SMA/SMK' ? 'selected' : '' }}>
                                                            SMA / SMK
                                                        </option>

                                                        <option value="D3"
                                                            {{ old('education', $employee->education) == 'D3' ? 'selected' : '' }}>
                                                            D3
                                                        </option>
                                                        <option value="D4"
                                                            {{ old('education', $employee->education) == 'D4' ? 'selected' : '' }}>
                                                            D4
                                                        </option>

                                                        <option value="S1"
                                                            {{ old('education', $employee->education) == 'S1' ? 'selected' : '' }}>
                                                            S1
                                                        </option>

                                                        <option value="S2"
                                                            {{ old('education', $employee->education) == 'S2' ? 'selected' : '' }}>
                                                            S2
                                                        </option>

                                                        <option value="S3"
                                                            {{ old('education', $employee->education) == 'S3' ? 'selected' : '' }}>
                                                            S3
                                                        </option>

                                                    </select>
                                                </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Alamat</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="ktp_number" class="form-label fw-semibold">
                                                        No KTP (Nomor Induk Kependudukan)
                                                        <span class="text-danger">*</span>
                                                    </label>

                                                    <input type="text" name="ktp_number" class="form-control"value="{{ old('ktp_number', $employee->ktp_number) }}" placeholder="Masukkan No KTP" required>
                                                </div>
                                        </div>
                                        <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="address" class="form-label fw-semibold">
                                                        Alamat <span class="text-danger">*</span>
                                                    </label>

                                                    <textarea name="address" rows="3" class="form-control" placeholder="Masukkan Alamat" required>{{ old('address', $employee->address) }}</textarea>
                                                </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Informasi Pekerjaan</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="company_id" class="form-label fw-semibold">
                                                        Perusahaan
                                                        <span class="text-danger">*</span>
                                                    </label>

                                                    <select
                                                        name="company_id"
                                                        class="form-select"
                                                        required>

                                                        <option value="" selected disabled>
                                                            Silakan Pilih
                                                        </option>

                                                        @foreach($companies as $company)

                                                            <option
                                                                value="{{ $company->id }}"
                                                                {{ old('company_id', $employee->company_id) == $company->id ? 'selected' : '' }}>
                                                                {{ $company->name }}
                                                            </option>

                                                        @endforeach

                                                    </select>
                                                </div>

                                                

                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">
                                                        Tanggal Bergabung <span class="text-danger">*</span>
                                                    </label>

                                                    <input type="date" name="join_date" class="form-control"
                                                        value="{{ old('join_date', $employee->join_date) }}">
                                                </div>
                                        </div>
                                        <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="position_id" class="form-label fw-semibold">
                                                        Jabatan <span class="text-danger">*</span>
                                                    </label>

                                                    <select
                                                        name="position_id"
                                                        class="form-select"
                                                        required>

                                                        <option value="" disabled {{ old('position_id') ? '' : 'selected' }}>
                                                            Silakan Pilih
                                                        </option>

                                                        @foreach($positions as $position)

                                                            <option
                                                                value="{{ $position->id }}"
                                                                {{ old('position_id', $employee->position_id) == $position->id ? 'selected' : '' }}>
                                                                {{ $position->name }}
                                                            </option>

                                                        @endforeach

                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                        <label for="role_id" class="form-label fw-semibold">
                                                            Peran Pengguna Terhadap Sistem
                                                            <span class="text-danger">*</span>
                                                        </label>

                                                        <select name="role_id" class="form-select"
                                                            required>

                                                            <option value="" disabled {{ old('role_id') ? '' : 'selected' }}>
                                                                Silakan Pilih
                                                            </option>

                                                            @foreach($roles as $role)

                                                            <option value="{{ $role->id }}" {{ old('role_id', $employee->role_id)==$role->id ? 'selected' :
                                                                '' }}>
                                                                {{ $role->name }}
                                                            </option>

                                                            @endforeach

                                                        </select>
                                                </div>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Photo</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-6">
                                                @if($employee->photo)
                                                    <div class="mb-3 text-center">
                                                        <img
                                                            src="{{ asset('storage/' . $employee->photo) }}"
                                                            alt="Foto Karyawan"
                                                            class="img-thumbnail"
                                                            style="max-height: 250px;">
                                                    </div>
                                                @endif

                                                <div class="mb-3">
                                                    <input
                                                        type="file"
                                                        name="photo"
                                                        class="form-control">
                                                </div>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Kontak Darurat</h4>
                                </div>
                                <div class="card-body">
                                        <div id="emergency-container">

                                        @forelse($employee->emergencyContacts as $contact)

                                            <div class="row emergency-item mb-2">

                                                <div class="col-md-4">

                                                    <label>Nama Kontak</label>

                                                    <input
                                                        type="text"
                                                        name="emergency_name[]"
                                                        value="{{ $contact->name }}"
                                                        class="form-control">

                                                </div>

                                                <div class="col-md-3">

                                                    <label>Hubungan</label>

                                                    <select
                                                        name="emergency_relationship[]"
                                                        class="form-select">

                                                        <option value="">Pilih</option>

                                                        @foreach([
                                                            'Suami',
                                                            'Istri',
                                                            'Ayah',
                                                            'Ibu',
                                                            'Anak',
                                                            'Kakak',
                                                            'Adik',
                                                            'Paman',
                                                            'Bibi',
                                                            'Saudara'
                                                        ] as $relationship)

                                                            <option
                                                                value="{{ $relationship }}"
                                                                {{ $contact->relationship == $relationship ? 'selected' : '' }}>

                                                                {{ $relationship }}

                                                            </option>

                                                        @endforeach

                                                    </select>

                                                </div>

                                                <div class="col-md-4">

                                                    <label>No HP</label>

                                                    <input
                                                        type="text"
                                                        name="emergency_phone[]"
                                                        value="{{ $contact->phone }}"
                                                        class="form-control">

                                                </div>

                                                <div class="col-md-1 d-flex align-items-end">

                                                    <button
                                                        type="button"
                                                        class="btn btn-danger remove-contact">
                                                        -
                                                    </button>

                                                </div>

                                            </div>

                                        @empty

                                            <div class="row emergency-item">

                                                <div class="col-md-4">

                                                    <label>Nama Kontak</label>

                                                    <input
                                                        type="text"
                                                        name="emergency_name[]"
                                                        class="form-control">

                                                </div>

                                                <div class="col-md-3">

                                                    <label>Hubungan</label>

                                                    <select
                                                        name="emergency_relationship[]"
                                                        class="form-select">

                                                        <option value="">Pilih</option>

                                                    </select>

                                                </div>

                                                <div class="col-md-4">

                                                    <label>No HP</label>

                                                    <input
                                                        type="text"
                                                        name="emergency_phone[]"
                                                        class="form-control">

                                                </div>

                                            </div>

                                        @endforelse

                                    </div>
                                    <div class="mt-3">

                                        <button
                                            type="button"
                                            id="add-contact"
                                            class="btn btn-primary">

                                            + Tambah Kontak Darurat

                                        </button>

                                    </div>

                                    </div>
                            </div>

                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Status</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        
                                        <div class="col-lg-6">
                                                <input class="form-check-input" type="checkbox" name="is_active" value="1" {{
                                                    old('is_active', true) ? 'checked' : '' }}>
                                                <label class="form-check-label">
                                                    Aktif
                                                </label>
                                        </div>
                                        
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
                                        <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary w-100">
                                            Cancel </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">
                                        Informasi
                                    </h4>
                                </div>

                                <div class="card-body">
                                    <p>
                                        Lengkapi data karyawan sesuai identitas yang berlaku.
                                    </p>
                                    <p>
                                        Pastikan NIK dan Nomor Karyawan tidak duplikat.
                                    </p>

                                </div>
                            </div>
                        </div>
                    </div>
                </form>


            </div>
            <!-- End Container Fluid -->


        </div>
        <!-- ==================================================== -->
        <!-- End Page Content -->
        <!-- ==================================================== -->

    </div>
    <!-- END Wrapper -->


    </html>



    <script>
        document
    .getElementById('add-contact')
    .addEventListener('click', function () {

        let html = `
        <div class="row emergency-item mt-2">

            <div class="col-md-4">

                <input
                    type="text"
                    name="emergency_name[]"
                    class="form-control"
                    placeholder="Nama Kontak">

            </div>

            <div class="col-md-3">

                <select
                    name="emergency_relationship[]"
                    class="form-select">

                    <option value="">Pilih</option>
                    <option value="Suami">Suami</option>
                    <option value="Istri">Istri</option>
                    <option value="Ayah">Ayah</option>
                    <option value="Ibu">Ibu</option>
                    <option value="Anak">Anak</option>
                    <option value="Kakak">Kakak</option>
                    <option value="Adik">Adik</option>
                    <option value="Paman">Paman</option>
                    <option value="Bibi">Bibi</option>
                    <option value="Saudara">Saudara</option>

                </select>

            </div>

            <div class="col-md-4">

                <input
                    type="text"
                    name="emergency_phone[]"
                    class="form-control"
                    placeholder="No HP">

            </div>

            <div class="col-md-1">

                <button
                    type="button"
                    class="btn btn-danger remove-contact">

                    -

                </button>

            </div>

        </div>
        `;

        document
            .getElementById('emergency-container')
            .insertAdjacentHTML('beforeend', html);

    });

        document.addEventListener('click', function (e) {

            if (e.target.classList.contains('remove-contact')) {

                e.target.closest('.emergency-item').remove();

            }

        });

    </script>
@endsection