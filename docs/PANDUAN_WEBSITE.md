# Buku Panduan Website Peduli Lingkungan

## 1. Pendahuluan

Website **Peduli Lingkungan** adalah platform komunitas untuk publikasi kegiatan lingkungan, edukasi, forum diskusi, katalog produk, pemesanan, dan pengelolaan konten oleh admin.

Dokumen ini adalah panduan operasional menyeluruh untuk:
- Pengunjung (guest)
- User terdaftar
- Admin
- Tim pengelola teknis

---

## 2. Tujuan Website

1. Menyebarkan informasi kegiatan lingkungan.
2. Menjadi pusat konten edukasi (artikel, galeri).
3. Memfasilitasi interaksi komunitas melalui forum.
4. Mendukung penjualan/pemesanan produk ramah lingkungan.
5. Menyediakan panel admin untuk manajemen data terpusat.

---

## 3. Peran Pengguna

### 3.1 Pengunjung (Belum Login)
- Melihat homepage, event, artikel, galeri, produk, dan forum.
- Bisa klik link WhatsApp komunitas.
- Belum bisa kirim pre-order, pesan produk, menulis testimoni, atau interaksi forum tertentu.

### 3.2 User Terdaftar (Sudah Login)
- Mengakses profil dan fitur personal.
- Membuat pesanan / pre-order.
- Melihat halaman **Pesanan Saya**.
- Mengirim testimoni (menunggu persetujuan admin).
- Berinteraksi di forum sesuai kebijakan.

### 3.3 Admin
- Mengelola semua data konten dari panel admin:
  - Banner, Event, Artikel, Galeri, Produk, Testimoni, User, Pengaturan, Pesanan.
- Memproses pesanan dan notifikasi.

---

## 4. Alur Penggunaan Publik

## 4.1 Homepage (`/`)
Homepage menampilkan:
- Hero banner (slider)
- Event unggulan/mendatang
- Produk terbaru
- Artikel terbaru
- Galeri
- Testimoni
- Popup event (jika aktif dan memenuhi syarat)

Catatan popup event:
- Popup hanya tampil jika event:
  - `has_popup = true`
  - `is_active = true`
  - tanggal event belum lewat (`event_date >= hari ini`)

## 4.2 Event
- Halaman daftar event: `/events`
- Detail event: `/events/{slug}`

## 4.3 Artikel
- Daftar artikel: `/artikel`
- Detail artikel: `/artikel/{slug}`

## 4.4 Galeri
- Daftar galeri publik: `/galeri`

## 4.5 Forum
- Daftar topik forum: `/forum`
- User login dapat membuat post/reply/like sesuai aturan.

---

## 5. Modul Produk & Pemesanan

## 5.1 Katalog Produk
- Daftar produk: `/products`
- Detail produk: `/products/{product}`
- Mendukung pencarian, filter stok, dan filter diskon.

## 5.2 Pre-order (Produk PO)
Jika produk bertipe pre-order:
- User login dapat mengirim form pre-order.
- Data disimpan ke tabel `orders` dengan status `pending`.
- Kuota pre-order diperbarui.
- User melihat notifikasi sukses + opsi chat admin via WhatsApp.

## 5.3 Pesanan Produk Biasa (Non-PO)
Di detail produk non-PO tersedia:
- Tombol **Pesan Sekarang** (menuju form order)
- Tombol **Tanya via WhatsApp** (langsung chat, tanpa simpan order)

## 5.4 Form Pesanan (`/produk/{product}/pesan`)
Field:
- Nama pembeli
- WhatsApp
- Qty
- Catatan (opsional)

Saat submit:
1. Simpan ke `orders` (`status = pending`, `is_read = false`)
2. Redirect ke WhatsApp admin dengan template pesan otomatis.

## 5.5 Pesanan Saya
- List: `/pesanan`
- Detail: `/pesanan/{order}`

Fitur:
- Riwayat semua pesanan milik user.
- Badge status:
  - Menunggu
  - Dikonfirmasi
  - Selesai
  - Dibatalkan
- Detail pesanan + tombol chat admin.

---

## 6. Modul Testimoni

## 6.1 Pengiriman Testimoni User
- Route form: `/testimoni/kirim` (login required)
- User isi nama, peran, dan testimoni.
- Testimoni disimpan sebagai **nonaktif** (`is_active = false`) sampai ditinjau admin.

## 6.2 Tampil di Homepage
Section testimoni hanya menampilkan data dengan:
- `is_active = true`

Sehingga semua testimoni user harus diaktifkan dulu di panel admin.

---

## 7. Panel Admin (`/admin`)

Akses admin memakai middleware:
- `auth`
- `is-admin`

Menu utama:
1. Dashboard
2. Banner
3. Event
4. Galeri
5. Artikel
6. Tentang Kami
7. Testimonial
8. Produk
9. Pesanan
10. Kelola User
11. Pengaturan

## 7.1 Dashboard
- Ringkasan metrik konten.
- Event terdekat.
- Artikel terbaru.

## 7.2 Event
Fitur:
- CRUD event
- Toggle active / featured
- Popup event
- Tampilkan event di navbar

Field penting event:
- `event_date`
- `is_active`
- `has_popup`
- `popup_redirect_url`
- `show_in_navbar`

## 7.3 Produk
Fitur:
- CRUD produk
- Harga normal/diskon
- Stok
- Flag pre-order + kuota + estimasi

## 7.4 Testimoni
Fitur:
- CRUD testimoni
- Toggle aktif/nonaktif

## 7.5 Pesanan (Admin)
Route: `/admin/orders`

Fitur:
- Tabel semua order
- Filter status
- Ubah status
- Hapus order

Kolom utama:
- Buyer
- Produk
- Qty
- WhatsApp
- Catatan
- Status
- Tanggal

## 7.6 Notifikasi Pesanan
- Badge di sidebar menu Pesanan.
- Badge bell icon di header admin.
- Menghitung order `pending` yang `is_read = false`.
- Saat halaman orders dibuka, pending unread ditandai read.

---

## 8. Struktur Data Penting

## 8.1 Tabel `orders`
Kolom:
- `id`
- `user_id` (nullable)
- `product_id`
- `buyer_name`
- `whatsapp`
- `qty`
- `catatan`
- `status` (`pending`, `confirmed`, `selesai`, `dibatalkan`)
- `is_read` (boolean)
- timestamps

## 8.2 Tabel `events` (terkait popup)
Kolom penting popup:
- `has_popup`
- `popup_redirect_url`
- `event_date`
- `is_active`

---

## 9. Alur Operasional Harian (Admin)

Checklist harian:
1. Buka `/admin/orders` untuk cek order baru.
2. Ubah status order sesuai progres.
3. Verifikasi testimoni masuk, aktifkan yang layak tampil.
4. Pastikan event mendatang tetap aktif.
5. Cek popup event (aktif + tanggal valid + URL benar).
6. Update konten artikel/galeri bila perlu.

---

## 10. Panduan Troubleshooting

## 10.1 Popup Event Tidak Muncul
Periksa:
1. `has_popup` aktif?
2. `is_active` aktif?
3. `event_date` belum lewat?
4. Event punya poster/gambar yang valid?

Jika semua benar, refresh browser dan cek cache.

## 10.2 Badge Pesanan 0 Terus
Badge menghitung:
- `status = pending`
- `is_read = false`

Jika sudah pernah dibuka `/admin/orders`, data otomatis jadi read.

## 10.3 User Tidak Bisa Lihat Pesanan
Pastikan:
- User login.
- `order.user_id` sesuai user yang login.

## 10.4 Testimoni User Tidak Tampil
Kemungkinan besar:
- `is_active` masih `false`.
Aktifkan dari admin testimonial.

## 10.5 Error Deploy Railway: Vite manifest not found
Pastikan proses deploy menjalankan:
- `npm ci`
- `npm run build`

Dan file ini terbentuk:
- `public/build/manifest.json`

---

## 11. Panduan Deploy Singkat (Railway + Nixpacks)

Konsep penting:
1. Build backend (`composer install`)
2. Build frontend (`npm ci && npm run build`)
3. Start app (`php artisan ... && php artisan serve --host=0.0.0.0 --port=$PORT`)

Best practice:
- Jangan `config:cache` saat build tanpa env runtime yang benar.
- Gunakan logging ke `stderr` agar error muncul di `railway logs`.

---

## 12. Keamanan & Praktik Baik

1. Nonaktifkan `APP_DEBUG` di production setelah issue selesai.
2. Rotasi password admin berkala.
3. Backup database rutin.
4. Batasi akses admin hanya untuk role tertentu.
5. Hindari menyimpan kredensial di repo.

---

## 13. Rekomendasi Pengembangan Berikutnya

1. Tambah status order “diproses”.
2. Notifikasi real-time admin (WebSocket/Pusher).
3. Riwayat aktivitas admin (audit trail).
4. Moderasi testimoni berbasis workflow (pending/approved/rejected).
5. Export pesanan ke Excel/PDF.

---

## 14. Ringkasan Singkat

Website ini sudah mencakup:
- CMS komunitas (event/artikel/galeri/banner)
- Forum diskusi
- E-commerce ringan (order + pre-order + tracking user)
- Notifikasi operasional admin
- Integrasi WhatsApp untuk komunikasi cepat
- Alur testimoni user dengan approval admin

Dokumen ini dapat dipakai sebagai buku panduan onboarding tim konten, admin operasional, dan developer baru.

