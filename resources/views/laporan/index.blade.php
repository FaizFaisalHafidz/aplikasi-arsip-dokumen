<x-app-layout>
    <x-slot:title>Clustering</x-slot:title>
    {{-- <x-slot:breadcrumb>Clustering</x-slot:breadcrumb> --}}

    <div class="card">
        <div class="card-body">
            {{-- judul form --}}
            <x-form-title>
                <i class="ti ti-filter fs-5 me-2"></i> Filter Data Arsip Dokumen
            </x-form-title>

            {{-- form filter data --}}
            <form action="{{ route('laporan.filter') }}" method="GET">
                <div class="row">
                    <div class="col-lg-6 mb-4 mb-lg-0">
                        <label class="form-label">Jenis Dokumen <span class="text-danger">*</span></label>
                        <select name="jenis" class="form-select select2-single @error('jenis') is-invalid @enderror" autocomplete="off">
                                <option {{ old('jenis', request('jenis')) == 'Semua' ? 'selected' : '' }} value="Semua">- Semua -</option>
                                @foreach ($jenis as $data)
                                    <option {{ old('jenis', request('jenis')) == $data->id ? 'selected' : '' }} value="{{ $data->id }}">{{ $data->nama }}</option>
                                @endforeach
                        </select>

                        {{-- pesan error untuk jenis --}}
                        @error('jenis')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-lg-3">
                        <label class="form-label">Tanggal Dokumen <span class="text-danger">*</span></label>
                        <input type="text" name="tgl_awal" class="form-control datepicker @error('tgl_awal') is-invalid @enderror" value="{{ old('tgl_awal', request('tgl_awal')) }}" placeholder="Tanggal awal" autocomplete="off">
                        
                        {{-- pesan error untuk tgl_awal --}}
                        @error('tgl_awal')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-lg-3">
                        <label style="color: transparent !important;" class="form-label">Tanggal Dokumen</label>
                        <input type="text" name="tgl_akhir" class="form-control datepicker @error('tgl_akhir') is-invalid @enderror" value="{{ old('tgl_akhir', request('tgl_akhir')) }}" placeholder="Tanggal akhir" autocomplete="off">
                        
                        {{-- pesan error untuk tgl_akhir --}}
                        @error('tgl_akhir')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
        
                {{-- action buttons --}}
                <x-form-action-buttons>laporan</x-form-action-buttons>
            </form>
        </div>
    </div>

    @if (request(['jenis', 'tgl_awal', 'tgl_akhir']))
        <div class="card">
            <div class="card-body">
                <div class="d-sm-flex justify-content-between align-items-center mb-4">
                    <div class="d-flex align-items-center">
                        {{-- judul laporan --}}
                        <h6 class="report-title mb-0">
                            <i class="ti ti-file-text fs-5 align-text-top me-1"></i> 
                            {{ request('jenis') == 'Semua' 
                                ? 'Laporan Arsip Dokumen Tanggal ' . Carbon\Carbon::parse(request('tgl_awal'))->translatedFormat('j F Y') . ' s.d. ' . Carbon\Carbon::parse(request('tgl_akhir'))->translatedFormat('j F Y') . '.'
                                : 'Laporan Arsip Dokumen ' . $fieldJenis->nama . ' Tanggal ' . Carbon\Carbon::parse(request('tgl_awal'))->translatedFormat('j F Y') . ' s.d. ' . Carbon\Carbon::parse(request('tgl_akhir'))->translatedFormat('j F Y') . '.' 
                            }}
                        </h6>
                    </div>

                    <div class="d-grid gap-2 mb-3 mb-sm-0">
                        {{-- button cetak laporan (export PDF) --}}
                        <a href="{{ route('laporan.print', [request('jenis'), request('tgl_awal'), request('tgl_akhir')]) }}" target="_blank" class="btn btn-warning px-4">
                            <i class="ti ti-printer me-2"></i> Cetak
                        </a>
                    </div>
                </div>

                {{-- tabel tampil data --}}
                <div class="table-responsive border rounded mb-2">
                    <table class="table align-middle text-nowrap mb-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Dokumen</th>
                                <th>Nomor Dokumen</th>
                                <th>Tanggal Dokumen</th>
                                <th>Jenis Dokumen</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        @php
                            $no = 1;
                        @endphp
                        @forelse ($arsip as $data)
                            {{-- jika data ada, tampilkan data --}}
                            <tr>
                                <td width="30">{{ $no++ }}</td>
                                <td width="250">{{ $data->nama_surat }}</td>
                                <td width="120">{{ $data->nomor_surat }}</td>
                                <td width="120">{{ Carbon\Carbon::parse($data->tanggal_surat)->translatedFormat('j F Y') }}</td>
                                <td width="120">{{ $data->jenis->nama }}</td>
                                <td width="100">
                                    {{-- button form detail data --}}
                                    <a href="{{ route('arsip.show', ['id' => $data->id, 'from' => 'clustering']) }}" class="btn btn-warning btn-sm m-1" data-bs-tooltip="tooltip" data-bs-title="Detail">
                                        <i class="ti ti-list"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            {{-- jika data tidak ada, tampilkan pesan data tidak tersedia --}}
                            <tr>
                                <td colspan="6">
                                    <div class="d-flex justify-content-center align-items-center">
                                        <i class="ti ti-info-circle fs-5 me-2"></i>
                                        <div>Tidak ada data tersedia.</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</x-app-layout>
