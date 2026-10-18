# Frontend Pendaftaran OSIS (Project B)

Aplikasi web frontend berbasis **Laravel** yang mengonsumsi **REST API Pendaftaran OSIS (Project A)** secara penuh untuk operasi CRUD (Create, Read, Update, Delete) pendaftaran calon pengurus OSIS tanpa mengakses database secara langsung.

Dibuat untuk memenuhi tugas **LKPD Individual: Frontend Pendaftaran OSIS (Integrasi API)** — RPL Kelas XI.

---

## 1. Arsitektur Sistem

```text
Browser (Klien) ──HTTP──► Project B (Frontend, :8001) ──HTTP Client──► Project A (API OSIS, :8000) ──► Database MySQL
                           (Server bagi Browser,                          (Server bagi B)
                            Client bagi A)
```

| Komponen | Port | Peran & Deskripsi |
|---|---|---|
| **Project A (API OSIS)** | `8000` | Penyedia data & logika bisnis (`php artisan serve --port=8000`) |
| **Project B (Frontend)** | `8001` | Controller + Blade UI + HTTP Client (`php artisan serve --port=8001`) |

> **Prinsip Penting:** Project B sama sekali tidak melakukan query SQL langsung ke tabel OSIS. Seluruh data diambil dan dikirim melalui REST API Project A menggunakan Laravel HTTP Client (`Illuminate\Support\Facades\Http`).

---

## 2. Fitur Aplikasi

- [x] **FR-1: Daftar Pendaftaran (`GET /pendaftaran`)**
  - Mengambil data dari `GET /api/pendaftaran-osis`.
  - Menampilkan tabel berisi: Nama Siswa, Kelas, Jenis Kelamin, Pilihan Bidang (badge), Motivasi, serta tombol Aksi (Edit & Hapus).
  - Penanganan *empty state* saat data belum ada.
- [x] **FR-2: Tambah Pendaftaran (`GET /pendaftaran/create` & `POST /pendaftaran`)**
  - Dropdown siswa bersumber dinamis dari `GET /api/siswa` (menampilkan nama dan kelas, mengirim `siswa_id`).
  - Input pilihan bidang: `kepemimpinan`, `keagamaan`, `olahraga`, `seni`.
  - Textarea motivasi dengan indikator live counter karakter (min. 20, maks. 300).
  - Penanganan validasi 422: menampilkan pesan error per field dan mempertahankan input (`old()`).
- [x] **FR-3: Edit Pendaftaran (`GET /pendaftaran/{id}/edit` & `PUT /pendaftaran/{id}`)**
  - Mengambil detail via `GET /api/pendaftaran-osis/{id}` dan mengisi form secara otomatis.
  - Mengirim pembaruan via HTTP `PUT`.
  - Penanganan validasi 422 jika aturan dilanggar.
- [x] **FR-4: Hapus Pendaftaran (`DELETE /pendaftaran/{id}`)**
  - Konfirmasi pop-up dialog sebelum penghapusan.
  - Mengirim HTTP `DELETE` dan menangani respons **204 No Content** tanpa memanggil `->json()`.
  - Master data siswa tidak ikut terhapus.
- [x] **FR-5: Penanganan Error & Resiliensi Koneksi**
  - Pesan flash alert sukses dan error seragam di seluruh halaman.
  - Menangani error timeout / kegagalan koneksi jika API Project A sedang tidak aktif dengan pesan ramah pengguna.

---

## 3. Konfigurasi Lingkungan (`.env`)

URL API disimpan di file `.env` dan diakses melalui `config/services.php`:

```env
APP_NAME="Frontend OSIS"
APP_ENV=local
APP_URL=http://localhost:8001

# Endpoint API OSIS (Project A)
OSIS_API_URL=http://127.0.0.1:8000/api
```

Pada `config/services.php`:
```php
'osis' => [
    'api_url' => rtrim(env('OSIS_API_URL', env('API_URL', 'http://127.0.0.1:8000/api')), '/'),
],
```

---

## 4. Panduan Menjalankan Aplikasi

Buka dua jendela terminal untuk menjalankan kedua project secara berdampingan:

### Terminal 1 — Jalankan Backend API (Project A)
```bash
cd /Applications/XAMPP/xamppfiles/htdocs/latihan-rest-api-2

# Pastikan MySQL aktif, migration & seed sudah berjalan
php artisan migrate --seed

# Jalankan server API di port 8000
php artisan serve --port=8000
```
Server API aktif di: `http://127.0.0.1:8000`

### Terminal 2 — Jalankan Frontend (Project B)
```bash
cd /Applications/XAMPP/xamppfiles/htdocs/pendaftaran-osis-API

# Install dependensi jika belum
composer install
npm install

# Kompilasi aset frontend
npm run build

# Jalankan frontend di port 8001
php artisan serve --port=8001
```
Akses aplikasi web di browser: **`http://localhost:8001`** atau **`http://127.0.0.1:8001`**.

---

## 5. Menjalankan Automated Tests

Project ini dilengkapi dengan unit & feature testing menyeluruh menggunakan **Pest**:

```bash
php artisan test
```

---

## 6. Pembahasan Pertanyaan Analisis (Bahan Laporan PDF)

Berikut panduan jawaban untuk **Bagian 12 Pertanyaan Analisis** pada LKPD:

### 1. Client & Server pada Browser → B dan B → A
- **Pada hubungan Browser → B:**
  - **Client:** Browser web pengguna yang mengirim HTTP Request (misal `GET /pendaftaran` atau submit form).
  - **Server:** Project B (Laravel Frontend di port 8001) yang menerima request, memproses Controller, dan merender HTML (Blade) kembali ke browser.
- **Pada hubungan B → A:**
  - **Client:** Project B (via Laravel `Http::` Client / Guzzle) yang mengirim HTTP Request (`GET`, `POST`, `PUT`, `DELETE`) ke API.
  - **Server:** Project A (REST API di port 8000) yang memproses rute API, validasi data, query database, dan mengembalikan respons format JSON.

### 2. Telusur Satu Alur Kiriman Form (Submit Form → Database Berubah)
1. **Blade Form:** Pengguna menekan tombol "Simpan Pendaftaran" di `resources/views/pendaftaran/create.blade.php`, mengirim form `POST /pendaftaran`.
2. **Route B:** `routes/web.php` mengarahkan rute `POST /pendaftaran` ke `PendaftaranController@store`.
3. **Controller B:** `PendaftaranController` menangkap data (`siswa_id`, `pilihan_bidang`, `motivasi`) dan memanggil `Http::post($this->apiUrl() . '/pendaftaran-osis', $payload)`.
4. **HTTP Client (Guzzle):** Mengirim HTTP POST Request melalui jaringan lokal ke `http://127.0.0.1:8000/api/pendaftaran-osis`.
5. **Route A:** `routes/api.php` pada Project A menangkap request dan mengarahkannya ke `Api\PendaftaranOsisController@store`.
6. **Validasi A:** Request divalidasi aturan L2, L3, L4, dan L5 (keberadaan siswa, keunikan pendaftaran, bidang valid, panjang motivasi).
7. **Model & Database A:** Model `PendaftaranOsis::create(...)` mengeksekusi query `INSERT INTO pendaftaran_osis ...` ke database MySQL.
8. **Response A:** API A mengembalikan JSON respons HTTP 201 Created.
9. **Redirect B:** Controller B menerima status 201 dan melakukan redirect ke `route('pendaftaran.index')` dengan session flash message `'Pendaftaran OSIS berhasil disimpan'`.
10. **Browser:** Merender ulang halaman daftar pendaftar dengan data terbaru dan banner sukses warna hijau.

### 3. Bukti Bahwa Data Berasal dari API (Bukan Statis)
1. **Bukti Refresh Database:** Tambah atau ubah data pendaftaran langsung di MySQL / phpMyAdmin Project A, lalu muat ulang (`F5`) halaman di Project B: data pada tabel frontend otomatis berubah mengikuti database A.
2. **Bukti Matikan Server API:** Matikan terminal Project A (`Ctrl + C`), lalu refresh Project B: Project B langsung menampilkan banner peringatan ramah bahwa server API tidak dapat dihubungi, membuktikan B selalu bergantung pada respons jaringan A.
3. **Bukti Kesesuaian Postman:** Respons JSON pada Postman `GET /api/pendaftaran-osis` memiliki jumlah dan isi atribut yang identik dengan baris data pada tabel HTML Project B.
