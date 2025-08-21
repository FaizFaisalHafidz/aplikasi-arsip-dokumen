<x-app-layout>
    <x-slot:title>User</x-slot:title>
    <x-slot:breadcrumb>Tambah</x-slot:breadcrumb>

    <div class="card">
        <div class="card-body">
            {{-- judul form --}}
            <x-form-title>
                <i class="ti ti-pencil-plus fs-5 me-2"></i> Tambah Data User
            </x-form-title>
            
            {{-- form tambah data --}}
            <form action="{{ route('user.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label class="form-label">Nama User <span class="text-danger">*</span></label>
                            <input type="text" name="nama_user" class="form-control @error('nama_user') is-invalid @enderror" value="{{ old('nama_user') }}" autocomplete="off">
                            
                            {{-- pesan error untuk nama user --}}
                            @error('nama_user')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username') }}" autocomplete="off">
                            
                            {{-- pesan error untuk username --}}
                            @error('username')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" autocomplete="off">
                            
                            {{-- pesan error untuk password --}}
                            @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div>
                            <label class="form-label">Role <span class="text-danger">*</span></label>
                            <select name="role" class="form-select select2-single @error('role') is-invalid @enderror" autocomplete="off">
                                <option selected disabled value="">- Pilih -</option>
                                <option {{ old('role') == 'Admin' ? 'selected' : '' }} value="Admin">Admin</option>
                                <option {{ old('role') == 'User' ? 'selected' : '' }} value="User">User</option>
                            </select>
    
                            {{-- pesan error untuk role --}}
                            @error('role')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>
        
                {{-- action buttons --}}
                <x-form-action-buttons>user</x-form-action-buttons>
            </form>
        </div>
    </div>
</x-app-layout>