# 🔐 Panduan Autentikasi SIM URT PAH - MATARAM

## 📋 Daftar Akun Login

Sistem ini menggunakan **multi-tenancy** dengan role-based access control (RBAC). Setiap lembaga memiliki akun terpisah dengan akses terbatas pada data lembaga masing-masing.

---

## 👑 SUPER ADMIN (Akses Penuh Sistem)

**Email:** `admin@sim-pah.com`  
**Password:** `password`  
**Role:** Super Admin  
**Akses:**

- Seluruh sistem tanpa batasan
- Manajemen semua lembaga
- Konfigurasi sistem
- Audit log lengkap

---

## 🏢 ADMIN URT (Divisi URT - Pusat)

**Email:** `urt@sim-pah.com`  
**Password:** `password`  
**Role:** Admin  
**Lembaga:** URT (Unit Rumah Tangga)  
**Akses:**

- Validasi request dari semua lembaga
- Manajemen inventaris pusat
- Approval stok update
- Dashboard monitoring

---

## 🏫 AKUN LEMBAGA (28 Institusi Internal)

Setiap lembaga memiliki format login yang konsisten:

### Format Umum:

- **Email:** `[kode_lembaga]@simpah.test`
- **Password:** `password` (default untuk semua)
- **Role:** Lembaga
- **Akses:** Terbatas pada data lembaga sendiri

### Daftar Login Per Lembaga:

#### 📚 **Pendidikan**

1. **SD Putra Abu Hurairah**
    - Email: `sd_pa@simpah.test`
    - Kode Database: `SD IT PUTRA`
    - Pilih di Dropdown: "SD PUTRA (SD IT PUTRA)"

2. **SD Putri Abu Hurairah**
    - Email: `sd_pi@simpah.test`
    - Kode Database: `SD IT PUTRI`
    - Pilih di Dropdown: "SD PUTRI (SD IT PUTRI)"

3. **SMP Putra Abu Hurairah** ⭐
    - Email: `smp_pa@simpah.test`
    - Kode Database: `SMP PUTRA`
    - Pilih di Dropdown: "SMP PUTRA (SMP PUTRA)"

4. **SMP Putri Abu Hurairah**
    - Email: `smp_pi@simpah.test`
    - Kode Database: `SMP IT PUTRI`
    - Pilih di Dropdown: "SMP PUTRI (SMP IT PUTRI)"

5. **SMA Putri Abu Hurairah**
    - Email: `sma_pa@simpah.test`
    - Kode Database: `SMA IT PUTRI`
    - Pilih di Dropdown: "SMA PUTRI (SMA IT PUTRI)"

6. **MA Plus Abu Hurairah**
    - Email: `ma_pa@simpah.test`
    - Kode Database: `MA_PLUS`
    - Pilih di Dropdown: "MA PLUS (MA_PLUS)"

#### 📚 **Fullday Programs**

7. **SMP Fullday**
    - Email: `smp_fullday@simpah.test`
    - Kode Database: `SMP FULLDAY`

8. **SMA Fullday**
    - Email: `sma_fullday@simpah.test`
    - Kode Database: `SMA FULLDAY`

9. **SMP & SMA Fullday**
    - Email: `smp_sma_fullday@simpah.test`
    - Kode Database: `SMP & SMA FULLDAY`

#### 🕌 **Pondok & Akademik**

10. **Diniyah**
    - Email: `diniyah@simpah.test`
    - Kode Database: `DINIYAH`

11. **PGMI/I'DAD**
    - Email: `pgmi@simpah.test`
    - Kode Database: `PGMI`

12. **Departemen Tahfidz**
    - Email: `tahfidz@simpah.test`
    - Kode Database: `TAHFIDZ`

13. **Departemen Bahasa**
    - Email: `bahasa@simpah.test`
    - Kode Database: `BAHASA`

#### 🏠 **Asrama**

14. **Asrama SMP Putra**
    - Email: `asrama_smp_pa@simpah.test`
    - Kode Database: `ASPA_SMP`

15. **Asrama SMP Putri**
    - Email: `asrama_smp_pi@simpah.test`
    - Kode Database: `ASPI_SMP`

16. **Asrama MA Plus**
    - Email: `asrama_ma@simpah.test`
    - Kode Database: `AS_MA`

17. **Asrama SMA Putri**
    - Email: `asrama_sma_pi@simpah.test`
    - Kode Database: `ASPI_SMA`

#### 🏛️ **Administrasi & Operasional**

18. **Unit Rumah Tangga (URT)**
    - Email: `urt@simpah.test`
    - Kode Database: `URT`

19. **Sekretariat/Mudir**
    - Email: `sekretariat@simpah.test`
    - Kode Database: `SEKRETARIAT`

20. **Wadir Akademik dan Kepondokan**
    - Email: `wadir_akd@simpah.test`
    - Kode Database: `WADIR_AKD`

21. **Wadir Pelayanan Umum**
    - Email: `wadir_um@simpah.test`
    - Kode Database: `WADIR_UM`

22. **Badan Kehormatan**
    - Email: `bk@simpah.test`
    - Kode Database: `BK`

23. **Keuangan/IT**
    - Email: `keuangan@simpah.test`
    - Kode Database: `KEU_IT`

#### 🏗️ **Infrastruktur**

24. **Sarpras**
    - Email: `sarpras@simpah.test`
    - Kode Database: `Sarpras`

25. **Kebersihan & Pertamanan**
    - Email: `kbt@simpah.test`
    - Kode Database: `KBT`

26. **Transportasi**
    - Email: `transport@simpah.test`
    - Kode Database: `TRANSPORT`

27. **Dapur/UGL**
    - Email: `dapur@simpah.test`
    - Kode Database: `DAPUR`

28. **Keamanan**
    - Email: `keamanan@simpah.test`
    - Kode Database: `SECURITY`

29. **UKS**
    - Email: `uks@simpah.test`
    - Kode Database: `UKS`

30. **UUP**
    - Email: `uup@simpah.test`
    - Kode Database: `UUP`

31. **Perpustakaan**
    - Email: `perpustakaan@simpah.test`
    - Kode Database: `PERPUS`

---

## 🔑 Catatan Keamanan

### Password Default

- **Semua akun menggunakan password:** `password`
- ⚠️ **PENTING:** Segera ubah password setelah login pertama kali di production!

### Kebijakan Akses

1. **Super Admin**: Akses penuh tanpa batasan
2. **Admin URT**: Validasi dan approval request dari lembaga
3. **Lembaga**: Hanya dapat mengakses dan mengelola data lembaga sendiri

### Multi-Tenancy

- Setiap lembaga memiliki `institution_id` unik
- Data inventaris, request, dan aktivitas difilter otomatis berdasarkan lembaga
- Lembaga tidak dapat melihat data lembaga lain

---

## 🚀 Cara Login

### ⚠️ PENTING - WAJIB BACA!

**Untuk Akun Lembaga (role: 'lembaga'):**
- **WAJIB** memilih lembaga dari dropdown sebelum login
- Jika tidak memilih lembaga → Error: "User Lembaga wajib memilih unit yang sesuai"
- Jika memilih lembaga yang salah → Error: "Lembaga yang dipilih tidak sesuai dengan akun Anda"

**Untuk Admin/Super Admin:**
- **TIDAK PERLU** memilih lembaga
- Field lembaga bisa dikosongkan atau diabaikan

---

### Langkah Login untuk Lembaga:

1. Buka aplikasi: `http://sim-pah.test`
2. **PILIH LEMBAGA TERLEBIH DAHULU** dari dropdown "Identitas Lembaga"
   - Contoh untuk SMP PA: Pilih **"SMP PUTRA (SMP PUTRA)"**
3. Masukkan **Email**: sesuai daftar di atas
   - Contoh: `smp_pa@simpah.test`
4. Masukkan **Password**: `password`
5. Klik **"Inisialisasi Pusat Akses"**

### Langkah Login untuk Admin:

1. Buka aplikasi: `http://sim-pah.test`
2. Masukkan **Email**: `admin@sim-pah.com` atau `urt@sim-pah.com`
3. Masukkan **Password**: `password`
4. **Kosongkan** field lembaga (tidak perlu dipilih)
5. Klik **"Inisialisasi Pusat Akses"**


---

## 🔧 Troubleshooting

### Tidak Bisa Login?

1. Pastikan database sudah di-seed: `php artisan db:seed`
2. Pastikan email dan password benar (case-sensitive)
3. Untuk lembaga, pastikan dropdown lembaga sudah dipilih
4. Cek console browser untuk error

### Lupa Password?

- Development: Reset via seeder `php artisan db:seed --class=DatabaseSeeder`
- Production: Hubungi Super Admin untuk reset password

### Akun Lembaga Tidak Ada?

Jalankan seeder khusus lembaga:

```bash
php artisan db:seed --class=LembagaUsersSeeder
```

---

## 📊 Hierarki Akses

```
┌─────────────────────────────────────┐
│         SUPER ADMIN                 │
│    (Akses Penuh Sistem)            │
└─────────────────────────────────────┘
              │
              ▼
┌─────────────────────────────────────┐
│          ADMIN URT                  │
│   (Validasi & Approval)            │
└─────────────────────────────────────┘
              │
              ▼
┌─────────────────────────────────────┐
│      28 LEMBAGA INTERNAL            │
│  (Akses Terbatas Per Lembaga)     │
└─────────────────────────────────────┘
```

---

## 📝 Catatan Pengembangan

- Sistem menggunakan Laravel Breeze untuk autentikasi
- Session-based authentication dengan cookie
- Password di-hash menggunakan bcrypt
- Middleware `auth` dan `role` untuk proteksi route
- Activity log otomatis menggunakan Spatie Laravel Activity Log

---

**Terakhir diupdate:** 6 Februari 2026  
**Versi Sistem:** SIM URT PAH v1.0  
**Developer:** Iqbal Muhammad Adiatma
