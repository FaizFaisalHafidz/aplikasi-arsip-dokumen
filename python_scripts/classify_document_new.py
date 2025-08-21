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

# Kata kunci untuk setiap kategori
keywords_dict = {
    'Administrasi': ['undangan', 'surat', 'rapat', 'izin', 'permohonan', 'administrasi', 'admin'],
    'Akademik': ['jadwal', 'pelajaran', 'pembelajaran', 'guru', 'mengajar', 'akademik', 'belajar'],
    'Evaluasi': ['penilaian', 'nilai', 'uji', 'kompetensi', 'evaluasi', 'test', 'ujian'],
    'Kepegawaian': ['guru', 'karyawan', 'pegawai', 'hadir', 'jabatan', 'cuti', 'kepegawaian', 'staff'],
    'Keuangan': ['laporan', 'keuangan', 'anggaran', 'bos', 'dana', 'gaji', 'finance', 'biaya'],
    'Kurikulum': ['kurikulum', 'silabus', 'materi', 'modul', 'curriculum'],
    'Sarana dan Prasarana': ['lab', 'inventaris', 'barang', 'fasilitas', 'alat', 'gedung', 'sarana', 'prasarana'],
    'Siswa': ['siswa', 'lulus', 'kelulusan', 'ppdb', 'absensi', 'pendaftaran', 'student', 'murid']
}

def classify_document(text, filename):
    """Klasifikasi dokumen berdasarkan konten atau nama file"""
    # Gabungkan teks PDF dan nama file untuk klasifikasi
    search_text = (text or '').lower() + ' ' + filename.lower()
    
    scores = {}
    for category, keywords in keywords_dict.items():
        score = 0
        for keyword in keywords:
            if keyword.lower() in search_text:
                score += 1
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
        predicted_category = classify_document(None, filename)
        result['predicted_category_id'] = predicted_category
        result['extraction_success'] = False
        result['message'] = "Gagal membaca konten PDF, menggunakan nama file untuk klasifikasi"
    
    print(json.dumps(result))

if __name__ == "__main__":
    main()
