<div class="pt-4 pb-1 mt-5 border-top">
    <div class="d-grid gap-3 d-sm-flex justify-content-md-start pt-1">
        @if (request()->routeIs($slot . '.show'))
            {{-- button kembali ke halaman index --}}
            <a href="{{ route($slot . '.index') }}" class="btn btn-primary px-4">
                <i class="ti ti-arrow-left me-2"></i> Kembali
            </a>
        @elseif (request()->routeIs($slot . '.index') || request()->routeIs($slot . '.filter'))
            {{-- button tampil data laporan --}}
            <button type="submit" class="btn btn-primary px-4">
                Tampilkan <i class="ti ti-arrow-right ms-2"></i>
            </button>
        @else
            {{-- button simpan data --}}
            <button type="submit" class="btn btn-primary px-4">Simpan</button>
            {{-- button kembali ke halaman index --}}
            <a href="{{ route($slot . '.index') }}" class="btn btn-secondary px-4">Batal</a>
        @endif
    </div>
</div>