# Sistem Informasi Rental PlayStation

Sistem informasi berbasis web yang dibangun dengan Next.js (App Router) untuk mengelola bisnis rental PlayStation. Aplikasi ini mencakup billing, manajemen data master (Ruangan, Pelanggan, User), dan sistem autentikasi kasir/admin.

---

## 🚀 Fitur yang Sudah Ada

* **Autentikasi & Keamanan:**
    * Login dan Logout untuk user (Kasir/Admin) menggunakan **Next-Auth (v4)**.
    * Password user di-hash menggunakan **bcryptjs** saat disimpan.
* **Perlindungan Halaman (Middleware):**
    * Halaman Dashboard (`/`) dan Admin (`/admin/*`) dilindungi dan hanya bisa diakses setelah login.
    * Halaman Admin (`/admin/*`) hanya bisa diakses oleh user dengan role **`admin`**.
* **Dashboard Billing (Real-time):**
    * Menampilkan status semua Ruangan (Kosong/Terisi) secara real-time (auto-refresh via **SWR**).
    * Memulai sesi billing (timer berjalan) dan mencatat ID kasir yang bertugas dari session.
    * Menghentikan sesi dan menghitung total tagihan secara otomatis berdasarkan `tarifPerJam`.
* **Point of Sale (POS):**
    * Modal (pop-up) konfirmasi pembayaran muncul setelah sesi dihentikan.
    * Memiliki API untuk mengubah status transaksi di database menjadi **"LUNAS"**.
* **Manajemen Data Master (CRUD):**
    * Halaman admin untuk CRUD (Create, Read, Update, Delete) data **Ruangan**.
    * Halaman admin untuk CRUD (Create, Read, Update, Delete) data **Pelanggan (Member)**.
    * Halaman admin untuk CRUD (Create, Read, Update, Delete) data **User (Kasir/Admin)**.

---

## 💻 Tumpukan Teknologi (Tech Stack)

* **Framework:** Next.js 14 (App Router)
* **Bahasa:** TypeScript
* **Database ORM:** Prisma
* **Database:** MySQL
* **Autentikasi:** Next-Auth (v4)
* **Styling:** Tailwind CSS
* **Data Fetching (Client):** SWR
* **Password Hashing:** bcryptjs

---

## 🛠️ Instalasi dan Setup

Berikut adalah langkah-langkah untuk menjalankan proyek ini di lokal:

### 1. Clone Repository
```bash
git clone https://github.com/[USERNAME-KAMU]/[NAMA-REPO-KAMU].git
cd [NAMA-REPO-KAMU]
```

### 2. Install Dependensi
Pastikan kamu memiliki Node.js (v18 atau lebih baru).

```bash
npm install
```

### 3. Setup Database
Proyek ini menggunakan MySQL.

Buat sebuah database baru di MySQL (misalnya dengan nama `rental_ps`).

### 4. Konfigurasi Environment (.env)
Buat file baru bernama `.env` di folder utama proyek. Salin dan tempel konten di bawah ini, lalu sesuaikan dengan konfigurasimu:

```env
# 1. Sesuaikan dengan info koneksi database MySQL kamu
# Format: mysql://[USER]:[PASSWORD]@[HOST]:[PORT]/[DATABASE_NAME]
DATABASE_URL="mysql://root:@localhost:3306/rental_ps"

# 2. Buat secret key untuk Next-Auth
# Jalankan perintah ini di terminal untuk membuat key baru:
# node -e "console.log(require('crypto').randomBytes(32).toString('hex'))"
NEXTAUTH_SECRET="GANTI_DENGAN_SECRET_KEY_YANG_KAMU_BUAT"

# 3. URL aplikasi lokal kamu
NEXTAUTH_URL="http://localhost:3000"
```

### 5. Jalankan Migrasi Database
Perintah ini akan membaca `prisma/schema.prisma` dan membuat semua tabel di database MySQL-mu.

```bash
npx prisma migrate dev
```

### 6. Jalankan Aplikasi
```bash
npm run dev
```

### 7. (PENTING) Membuat User Admin Pertama
Saat pertama kali dijalankan, kamu tidak bisa login karena belum ada user.

1. Buka file `src/middleware.ts`.

2. Ubah sementara bagian `config.matcher` agar tidak mengunci `/admin/user`.

```typescript
// Ubah dari:
export const config = {
  matcher: ["/", "/admin/:path*"],
};

// Menjadi:
export const config = {
  matcher: ["/", "/admin/ruangan", "/admin/pelanggan"], // Hapus /admin/user sementara
};
```

3. Simpan file (server akan hot-reload).

4. Buka `http://localhost:3000/admin/user` di browser.

5. Buat user baru dengan Role: **admin**.

6. **KEMBALIKAN** file `src/middleware.ts` seperti semula agar semua halaman admin aman.

---

## 🔮 Fitur yang Belum Ada (To-Do)

## Ubah Frontend

---
