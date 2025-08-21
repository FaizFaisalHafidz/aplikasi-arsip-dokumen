import csv
import mysql.connector
from datetime import datetime

# Koneksi ke database MySQL
conn = mysql.connector.connect(
    host='localhost',
    user='root',
    password='',
    database='arsip_surat'
)
cursor = conn.cursor()

# Baca dataset dan ambil jenis dokumen unik
jenis_set = set()
with open('python_scripts/data/dataset_1000_dokumen_variasi.csv', 'r', encoding='utf-8') as file:
    reader = csv.DictReader(file)
    for row in reader:
        jenis_dokumen = row['Jenis Dokumen'].strip()
        if jenis_dokumen and jenis_dokumen != 'Jenis Dokumen':
            jenis_set.add(jenis_dokumen)

# Insert data jenis ke database
now = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
for jenis in sorted(jenis_set):
    cursor.execute(
        "INSERT INTO jenis (nama, created_at, updated_at) VALUES (%s, %s, %s)",
        (jenis, now, now)
    )

conn.commit()
cursor.close()
conn.close()

print(f"Berhasil menambahkan {len(jenis_set)} jenis dokumen ke database:")
for jenis in sorted(jenis_set):
    print(f"- {jenis}")
