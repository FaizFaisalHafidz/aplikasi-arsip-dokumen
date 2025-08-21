import sys
import json
import os

# Mapping dari label prediksi Python ke nama kategori database
category_id_map = {
    0: 'Administrasi',
    1: 'Akademik',
    2: 'Evaluasi',
    3: 'Kepegawaian',
    4: 'Keuangan',
    5: 'Kurikulum',
    6: 'Sarana dan Prasarana',
    7: 'Siswa',
}

# Kata kunci untuk setiap kategori (sederhana berdasarkan nama file)
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

def classify_by_filename(file_path):
    """Klasifikasi berdasarkan nama file sebagai fallback sederhana"""
    filename = os.path.basename(file_path).lower()
    
    scores = {}
    for category, keywords in keywords_dict.items():
        score = 0
        for keyword in keywords:
            if keyword.lower() in filename:
                score += 1
        scores[category] = score
    
    # Ambil kategori dengan skor tertinggi
    if max(scores.values()) > 0:
        predicted_category = max(scores, key=scores.get)
        return predicted_category
    
    return None

def main():
    if len(sys.argv) < 2:
        print(json.dumps({'error': 'No file path provided.'}))
        return

    file_path = sys.argv[1]

    try:
        if not os.path.exists(file_path):
            raise FileNotFoundError(f"File tidak ditemukan: {file_path}")
        
        # Klasifikasi berdasarkan nama file (sebagai demo sederhana)
        predicted_category = classify_by_filename(file_path)
        
        if predicted_category is None:
            # Jika tidak bisa diklasifikasi, kembalikan default atau error
            raise ValueError("Tidak dapat mengklasifikasi dokumen berdasarkan nama file")

        # Kembalikan hasil klasifikasi
        print(json.dumps({'predicted_category_id': predicted_category}))

    except Exception as e:
        print(json.dumps({'error': str(e)}))

if __name__ == "__main__":
    main()