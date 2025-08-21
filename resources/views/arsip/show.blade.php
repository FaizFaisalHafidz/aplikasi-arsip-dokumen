<x-app-layout>
    <x-slot:title>Arsip Dokumen</x-slot:title>
    <x-slot:breadcrumb>Detail</x-slot:breadcrumb>

    <div class="card">
        <div class="card-body">
            {{-- judul form --}}
            <x-form-title>
                <i class="ti ti-list fs-5 me-2"></i> Detail Data Arsip Dokumen
            </x-form-title>

            {{-- tampilkan detail data --}}
            <div class="table-responsive border rounded mb-4">
                <table class="table align-middle text-nowrap mb-0">
                    <tr>
                        <td width="150">Nama Dokumen</td>
                        <td width="10">:</td>
                        <td>{{ $arsip->nama_surat }}</td>
                    </tr>
                    <tr>
                        <td width="150">Nomor Dokumen</td>
                        <td width="10">:</td>
                        <td>{{ $arsip->nomor_surat }}</td>
                    </tr>
                    <tr>
                        <td width="150">Tanggal Dokumen</td>
                        <td width="10">:</td>
                        <td>{{ Carbon\Carbon::parse($arsip->tanggal_surat)->translatedFormat('j F Y') }}</td>
                    </tr>
                    <tr>
                        <td width="150">Jenis Dokumen</td>
                        <td width="10">:</td>
                        <td>{{ $arsip->jenis->nama }}</td>
                    </tr>
                </table>
            </div>
            {{-- tampilkan dokumen elektronik --}}
            <div class="pt-2">
             <embed type="application/pdf" width="100%" height="700px" src="{{ asset('storage/dokumen/' . urlencode($arsip->dokumen_elektronik)) }}" class="border rounded">
            </div>
            
            {{-- action buttons --}}
            <x-form-action-buttons>arsip</x-form-action-buttons>
        </div>
    </div>
</x-app-layout>