import pandas as pd
from kmodes.kprototypes import KPrototypes
import joblib

# 1. Muat Data
# Asumsi: data kamu ada dalam file CSV atau Excel
# data_dokumen.csv harus berisi data mentah yang akan kamu klasifikasi
# Contoh kolom: ['id', 'judul_surat', 'isi_singkat', 'jenis_surat', 'jumlah_kata']
df = pd.read_csv('dataset_1000_dokumen_variasi.csv')

print("Data berhasil dimuat.")
print(f"Jumlah baris data: {len(df)}")

# 2. Pra-pemrosesan Data
# Di sini kamu akan mengubah data mentah menjadi fitur yang bisa diproses algoritma.
# Misalnya:
# - Menghapus tanda baca, mengubah ke huruf kecil (untuk data teks)
# - Mengubah data kategorikal menjadi numerik
# - Ekstraksi fitur dari teks (misalnya, bag-of-words, TF-IDF)

# Contoh data (misalnya setelah pra-pemrosesan)
# Kolom yang akan kamu gunakan untuk klasifikasi
data_to_cluster = df[['Nama_dokumen', 'Nomor_Dokumen', 'Tanggal_Dokumen' ]]

# Tentukan kolom mana yang numerik dan mana yang kategorikal
# 'jumlah_kata' adalah numerik
# 'judul_surat' dan 'isi_singkat' adalah kategorikal (setelah diolah menjadi label)
categorical_features_idx = [0, 1, 2] # Indeks kolom kategorikal

# 3. Inisialisasi dan Latih Model
# n_clusters adalah jumlah klaster (kategori) yang kamu inginkan
# Kamu bisa mencoba beberapa nilai k untuk mendapatkan hasil terbaik
kproto = KPrototypes(n_clusters=8, init='Cao', verbose=2, random_state=42)
clusters = kproto.fit_predict(data_to_cluster, categorical=categorical_features_idx)

print("\nModel K-Prototype berhasil dilatih.")

# 4. Simpan Hasil Model
# Simpan model yang sudah dilatih ke file .pkl
# File ini yang akan kamu gunakan di Laravel
joblib.dump(kproto, 'kprototype_model.pkl')

print("Model K-Prototype berhasil dilatih dan disimpan sebagai kprototype_model.pkl")

# Optional: Kamu bisa simpan juga mapping antara hasil klaster ke nama kategori
# Misalnya, kamu tentukan bahwa klaster 0 adalah 'Administrasi', klaster 1 adalah 'Akademik', dst
category_map = {
    0: 'Administrasi',
    1: 'Akademik',
    2: 'Evaluasi',
    3: 'Kepegawaian',
    4: 'Keuangan',
    5: 'Kurikulum',
    6: 'Sarana dan Prasarana',
    7: 'Siswa',
    # ...
}

# Simpan pemetaan kategori ke dalam file JSON
with open('category_map.json', 'w') as f:
    json.dump(category_map, f, indent=4)

print("Pemetaan kategori berhasil disimpan sebagai category_map.json.")
print("\nIsi category_map.json:")
print(json.dumps(category_map, indent=4))