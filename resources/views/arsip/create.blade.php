<x-app-layout>
    <x-slot:title>Arsip Dokumen</x-slot:title>
    <x-slot:breadcrumb>Tambah</x-slot:breadcrumb>

    <div class="card">
        <div class="card-body">
            {{-- judul form --}}
            <x-form-title>
                <i class="ti ti-pencil-plus fs-5 me-2"></i> Tambah Data Arsip Dokumen
            </x-form-title>
            
            {{-- form tambah data --}}
            <form action="{{ route('arsip.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label class="form-label">Nama Dokumen <span class="text-danger">*</span></label>
                            <textarea name="nama_dokumen" rows="2" class="form-control @error('nama_dokumen') is-invalid @enderror" autocomplete="off">{{ old('nama_dokumen') }}</textarea>
                            
                            {{-- pesan error untuk nama dokumen--}}
                            @error('nama_dokumen')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nomor Dokumen <span class="text-danger">*</span></label>
                            <input type="text" name="nomor_dokumen" class="form-control @error('nomor_dokumen') is-invalid @enderror" value="{{ old('nomor_dokumen') }}" autocomplete="off">
                            
                            {{-- pesan error untuk nomor dokumen --}}
                            @error('nomor_dokumen')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal Dokumen <span class="text-danger">*</span></label>
                            <input type="text" name="tanggal_dokumen" class="form-control datepicker @error('tanggal_dokumen') is-invalid @enderror" value="{{ old('tanggal_dokumen') }}" autocomplete="off">
                            
                            {{-- pesan error untuk tanggal dokumen --}}
                            @error('tanggal_dokumen')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div>
                            <label class="form-label">Dokumen Elektronik <span class="text-danger">*</span></label>
                            <input type="file" accept=".pdf" name="dokumen_elektronik" class="form-control @error('dokumen_elektronik') is-invalid @enderror" autocomplete="off">
                
                            {{-- pesan error untuk dokumen elektronik --}}
                            @error('dokumen_elektronik')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                            <div class="form-text text-primary mb-0">
                                <div class="badge fw-medium bg-primary-subtle text-primary">Keterangan :</div>
                                <div>
                                    - Jenis file yang bisa diunggah adalah: pdf. <br>
                                    - Ukuran file yang bisa diunggah maksimal 5 MB.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- action buttons --}}
                <x-form-action-buttons>arsip</x-form-action-buttons>
            </form>
        </div>
    </div>
</x-app-layout>