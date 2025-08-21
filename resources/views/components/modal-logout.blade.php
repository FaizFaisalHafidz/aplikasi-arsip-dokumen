<div class="modal fade" id="modalLogout" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalLogoutLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title" id="exampleModalLabel">
                    <i class="ti ti-logout me-1"></i> Logout
                </h1>
            </div>
            <div class="modal-body">
                <p class="mb-2">
                    Apakah Anda yakin ingin logout?
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                {{-- button konfirmasi logout --}}
                <form action="{{ route('logout') }}" method="POST" class="ps-2">
                    @csrf
                    <button type="submit" class="btn btn-danger"> Ya, Logout! </button>
                </form>
            </div>
        </div>
    </div>
</div>