#!/usr/bin/env python3
import sys
import json
import os
import re
from datetime import datetime

try:
    import PyPDF2
    PDF_AVAILABLE = True
except ImportError:
    PDF_AVAILABLE = False

def extract_text_from_pdf(pdf_path):
    """Ekstrak teks dari file PDF"""
    if not PDF_AVAILABLE:
        return None
        
    try:
        with open(pdf_path, 'rb') as file:
            pdf_reader = PyPDF2.PdfReader(file)
            text = ""
            for page in pdf_reader.pages:
                text += page.extract_text()
            return text
    except Exception as e:
        return None

def extract_document_info(text):
    """Ekstrak informasi dokumen dari teks PDF"""
    info = {
        'nama_dokumen': None,
        'nomor_dokumen': None,
        'tanggal_dokumen': None
    }
    
    if not text:
        return info
    
    # Pattern untuk mencari nomor dokumen
    # Contoh: No: 001/SK/2024, Nomor: 123/SPK/2024
    nomor_patterns = [
        r'(?:No\.?|Nomor\.?)\s*[:]\s*([A-Z0-9/\-\.]+)',
        r'(?:Number|Ref)\s*[:]\s*([A-Z0-9/\-\.]+)',
        r'\b(\d{2,4}/[A-Z]{2,5}/\d{4})\b'
    ]
    
    for pattern in nomor_patterns:
        match = re.search(pattern, text, re.IGNORECASE)
        if match:
            info['nomor_dokumen'] = match.group(1).strip()
            break
    
    # Pattern untuk mencari tanggal
    # Contoh: 21 Agustus 2024, 21-08-2024, 2024-08-21
    tanggal_patterns = [
        r'\b(\d{1,2})\s+(Januari|Februari|Maret|April|Mei|Juni|Juli|Agustus|September|Oktober|November|Desember)\s+(\d{4})\b',
        r'\b(\d{1,2})-(\d{1,2})-(\d{4})\b',
        r'\b(\d{4})-(\d{1,2})-(\d{1,2})\b',
        r'\b(\d{1,2})/(\d{1,2})/(\d{4})\b'
    ]
    
    for pattern in tanggal_patterns:
        match = re.search(pattern, text, re.IGNORECASE)
        if match:
            if 'Januari' in pattern or 'Februari' in pattern:  # Indonesian month names
                bulan_map = {
                    'Januari': '01', 'Februari': '02', 'Maret': '03', 'April': '04',
                    'Mei': '05', 'Juni': '06', 'Juli': '07', 'Agustus': '08',
                    'September': '09', 'Oktober': '10', 'November': '11', 'Desember': '12'
                }
                day, month_name, year = match.groups()
                month = bulan_map.get(month_name, '01')
                info['tanggal_dokumen'] = f"{year}-{month.zfill(2)}-{day.zfill(2)}"
            elif len(match.groups()) == 3:
                if match.group(1).isdigit() and len(match.group(1)) == 4:  # YYYY-MM-DD
                    year, month, day = match.groups()
                    info['tanggal_dokumen'] = f"{year}-{month.zfill(2)}-{day.zfill(2)}"
                else:  # DD-MM-YYYY or DD/MM/YYYY
                    day, month, year = match.groups()
                    info['tanggal_dokumen'] = f"{year}-{month.zfill(2)}-{day.zfill(2)}"
            break
    
    # Ekstrak nama dokumen (ambil dari beberapa baris pertama yang bukan header)
    lines = text.split('\n')
    for i, line in enumerate(lines[:10]):  # Cek 10 baris pertama
        line = line.strip()
        if len(line) > 10 and not re.match(r'^(No\.|Nomor|Tanggal|Date)', line, re.IGNORECASE):
            # Skip jika line hanya berisi nomor atau tanggal
            if not re.match(r'^\d+[/\-\.]\w+', line):
                info['nama_dokumen'] = line
                break
    
    return info

# Kata kunci untuk setiap kategori berdasarkan dataset
keywords_dict = {
    'Administrasi': ['agenda kegiatan', 'proposal kegiatan', 'laporan hasil rapat', 'surat undangan rapat', 'daftar hadir rapat', 'undangan', 'rapat', 'permohonan'],
    'Akademik': ['daftar mata pelajaran', 'jadwal ujian', 'laporan praktik', 'daftar guru pengajar', 'guru pengajar', 'jadwal', 'ujian', 'pembelajaran', 'mengajar', 'akademik', 'belajar', 'kelas', 'mata pelajaran', 'pengajar'],
    'Evaluasi': ['rekap penilaian', 'rekap nilai', 'laporan hasil wawancara', 'laporan monitoring', 'penilaian', 'nilai', 'uji', 'kompetensi', 'evaluasi', 'test', 'monitoring'],
    'Kepegawaian': ['data sertifikasi guru', 'rekap data guru', 'sk pengangkatan guru', 'sk pemberhentian', 'jadwal piket guru', 'notulen rapat guru', 'formulir cuti', 'laporan absensi pegawai', 'daftar hadir guru', 'absensi guru', 'hadir guru', 'piket guru', 'rapat guru', 'sertifikasi guru', 'pengangkatan guru', 'guru', 'karyawan', 'pegawai', 'jabatan', 'cuti', 'kepegawaian', 'staff', 'piket', 'sertifikasi', 'pengangkatan', 'absensi pegawai'],
    'Keuangan': ['bukti penerimaan', 'laporan kas', 'laporan audit', 'rencana pengadaan barang', 'laporan', 'keuangan', 'anggaran', 'bos', 'dana', 'gaji', 'finance', 'biaya', 'kas', 'audit'],
    'Kurikulum': ['bahan ajar', 'modul pembelajaran', 'rencana pengembangan kurikulum', 'kurikulum', 'silabus', 'materi', 'modul', 'curriculum', 'pengembangan'],
    'Sarana dan Prasarana': ['rencana perbaikan gedung', 'daftar inventaris', 'daftar fasilitas', 'jadwal pemakaian lab', 'rekap barang masuk', 'laporan pengadaan alat', 'lab', 'inventaris', 'barang', 'fasilitas', 'alat', 'gedung', 'sarana', 'prasarana', 'perbaikan'],
    'Siswa': ['laporan kegiatan ekstrakurikuler', 'laporan hasil konseling', 'kartu ujian siswa', 'siswa', 'lulus', 'kelulusan', 'ppdb', 'pendaftaran', 'student', 'murid', 'konseling', 'ekstrakurikuler']
}

def classify_document(text, filename):
    """Klasifikasi dokumen berdasarkan konten atau nama file"""
    # Gabungkan teks PDF dan nama file untuk klasifikasi
    search_text = (text or '').lower() + ' ' + filename.lower()
    
    # Periksa kombinasi kata yang menunjukkan kepegawaian dengan prioritas tinggi
    kepegawaian_priority_patterns = [
        'daftar hadir.*guru',
        'absensi.*guru', 
        'hadir.*guru',
        'guru.*absen',
        'guru.*hadir',
        'rapat guru',
        'piket guru',
        'data.*guru',
        'sk.*guru',
        'sertifikasi guru',
        'pegawai.*absen',
        'pegawai.*hadir'
    ]
    
    import re
    for pattern in kepegawaian_priority_patterns:
        if re.search(pattern, search_text):
            return 'Kepegawaian'
    
    scores = {}
    for category, keywords in keywords_dict.items():
        score = 0
        for keyword in keywords:
            if keyword.lower() in search_text:
                # Berikan skor berdasarkan panjang kata kunci (frasa yang lebih spesifik mendapat skor lebih tinggi)
                word_count = len(keyword.split())
                score += word_count * word_count  # Kuadratkan untuk memberikan prioritas lebih tinggi pada frasa panjang
        scores[category] = score
    
    # Ambil kategori dengan skor tertinggi
    if max(scores.values()) > 0:
        predicted_category = max(scores, key=scores.get)
        return predicted_category
    
    return 'Administrasi'  # Default category

def main():
    if len(sys.argv) < 2:
        print(json.dumps({"error": "Usage: python classify_document.py <pdf_path> [original_filename]"}))
        sys.exit(1)
    
    pdf_path = sys.argv[1]
    original_filename = sys.argv[2] if len(sys.argv) > 2 else os.path.basename(pdf_path)
    
    if not os.path.exists(pdf_path):
        print(json.dumps({"error": "File tidak ditemukan"}))
        sys.exit(1)
    
    # Ekstrak teks dari PDF
    text = extract_text_from_pdf(pdf_path)
    filename = os.path.basename(pdf_path)
    
    result = {}
    
    # Nama dokumen selalu dari nama file asli (tanpa ekstensi)
    result['nama_dokumen'] = os.path.splitext(original_filename)[0]
    
    if text and text.strip():
        # Jika berhasil membaca PDF, ekstrak nomor dan tanggal dari konten
        doc_info = extract_document_info(text)
        result['nomor_dokumen'] = doc_info['nomor_dokumen']
        result['tanggal_dokumen'] = doc_info['tanggal_dokumen']
        
        # Klasifikasi berdasarkan konten
        predicted_category = classify_document(text, filename)
        result['predicted_category_id'] = predicted_category
        result['extraction_success'] = True
        result['message'] = "Berhasil mengekstrak informasi dari PDF"
    else:
        # Jika gagal membaca PDF, set nomor dan tanggal ke null
        result['nomor_dokumen'] = None
        result['tanggal_dokumen'] = None
        
        # Klasifikasi berdasarkan nama file
        predicted_category = classify_document(None, original_filename)
        result['predicted_category_id'] = predicted_category
        result['extraction_success'] = False
        result['message'] = "Gagal membaca konten PDF, menggunakan nama file untuk klasifikasi"
    
    print(json.dumps(result))

if __name__ == "__main__":
    main()
