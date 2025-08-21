<x-app-layout>
    <x-slot:title>Password</x-slot:title>
    <x-slot:breadcrumb>Ubah</x-slot:breadcrumb>

    <div class="card">
        <div class="card-body">
            {{-- judul form --}}
            <x-form-title>
                <i class="ti ti-edit fs-5 me-2"></i> Ubah Password
            </x-form-title>

            {{-- menampilkan pesan berhasil --}}
            <x-alert></x-alert>
            
            {{-- form ubah data --}}
            <form action="{{ route('password.update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label class="form-label">Password Lama <span class="text-danger">*</span></label>
                            <input type="password" name="password_lama" class="form-control @error('password_lama') is-invalid @enderror" autocomplete="off">
                            
                            {{-- pesan error untuk password lama --}}
                            @error('password_lama')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password Baru <span class="text-danger">*</span></label>
                            <input type="password" name="password_baru" class="form-control @error('password_baru') is-invalid @enderror" autocomplete="off">
                            
                            {{-- pesan error untuk password baru --}}
                            @error('password_baru')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div>
                            <label class="form-label">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                            <input type="password" name="konfirmasi_password" class="form-control @error('konfirmasi_password') is-invalid @enderror" autocomplete="off">
                            
                            {{-- pesan error untuk konfirmasi password --}}
                            @error('konfirmasi_password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>
        
                {{-- action buttons --}}
                <x-form-action-buttons>dashboard</x-form-action-buttons>
            </form>
        </div>
    </div>
</x-app-layout>