<!DOCTYPE html>
<html lang="en">

<head>
    {{-- Required meta tags --}}
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    {{-- Title --}}
    <title>Laporan Data Arsip Dokumen</title>
    
    {{-- custom style --}}
    <style type="text/css">
        body,
        html {
            font-family: sans-serif;
            font-size: 15px;
            color: #29343d;
        }

        table, th, td {
            border: 1px solid #dee2e6;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px 5px;
        }

        hr {
            color: #dee2e6;
        }
    </style>
</head>

<body>
    {{-- judul laporan --}}
    <div style="text-align: center">
        <h3>
            {{ request('jenis') == 'Semua' 
                ? 'Laporan Data Arsip Dokumen Tanggal ' . Carbon\Carbon::parse(request('tgl_awal'))->translatedFormat('j F Y') . ' - ' . Carbon\Carbon::parse(request('tgl_akhir'))->translatedFormat('j F Y')
                : 'Laporan Data Arsip ' . $fieldJenis->nama . ' Tanggal ' . Carbon\Carbon::parse(request('tgl_awal'))->translatedFormat('j F Y') . ' - ' . Carbon\Carbon::parse(request('tgl_akhir'))->translatedFormat('j F Y')
            }}
        </h3>
    </div>

    <hr style="margin-bottom:20px">

    {{-- tabel tampil data --}}
    <table style="width:100%">
        <thead style="background-color: #635bff; color: #ffffff">
            <th>No</th>
            <th>Nama Dokumen</th>
            <th>Nomor Dokumen</th>
            <th>Tanggal Dokumen</th>
            <th>Jenis Dokumen</th>
        </thead>
        <tbody>
        @php
            $no = 1;
        @endphp
        @forelse ($arsip as $data)
            {{-- jika data ada, tampilkan data --}}
            <tr>
                <td width="30" align="center">{{ $no++ }}</td>
                <td width="200">{{ $data->nama_surat }}</td>
                <td width="100">{{ $data->nomor_surat }}</td>
                <td width="100">{{ Carbon\Carbon::parse($data->tanggal_surat)->translatedFormat('j F Y') }}</td>
                <td width="100">{{ $data->jenis->nama }}</td>
            </tr>
        @empty
            {{-- jika data tidak ada, tampilkan pesan data tidak tersedia --}}
            <tr>
                <td align="center" colspan="5">Tidak ada data tersedia.</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div style="margin-top: 25px; text-align: right">Bandung, {{ Carbon\Carbon::now()->translatedFormat('j F Y') }}</div>
</body>

</html>
