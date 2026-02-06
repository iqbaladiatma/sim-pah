# Fitur Export Excel - SIM PAH

## 📊 Deskripsi Fitur

Fitur Export Excel memungkinkan admin untuk mengekspor semua data pengajuan (requests) ke dalam format Excel (.xlsx) yang rapih dan terstruktur.

## ✨ Fitur Utama

### 1. **Export Data Lengkap**

- Nomor urut otomatis
- Tanggal pengajuan (format: dd/mm/yyyy HH:mm)
- Kode dan nama lembaga
- Tipe pengajuan
- Judul dan deskripsi pengajuan
- Estimasi biaya (format Rupiah)
- Status pengajuan
- Catatan admin
- Data pengaju (nama dan email)

### 2. **Styling Premium**

- Header dengan background warna **Pail Gold (#C9A658)**
- Text header berwarna putih dan bold
- Border pada semua cell
- Column width yang optimal untuk readability
- Text alignment yang rapih

### 3. **Filter Data** (Opsional)

Export mendukung filter berdasarkan:

- **Status**: all, pending, approved, rejected
- **Tanggal Mulai**: Filter data dari tanggal tertentu
- **Tanggal Akhir**: Filter data sampai tanggal tertentu

## 🎯 Cara Menggunakan

### Export Semua Data

1. Buka halaman **Admin > Manajemen Pengajuan** (`/admin/requests`)
2. Klik tombol **"Export Excel"** (tombol hijau dengan icon download)
3. File Excel akan otomatis terdownload dengan nama: `Pengajuan_YYYY-MM-DD_HHMMSS.xlsx`

### Export dengan Filter (Advanced)

Gunakan URL dengan query parameters:

```
/admin/requests/export?status=pending
/admin/requests/export?status=approved&start_date=2026-01-01&end_date=2026-01-31
```

**Parameter yang tersedia:**

- `status`: all | pending | approved | rejected
- `start_date`: Format YYYY-MM-DD
- `end_date`: Format YYYY-MM-DD

## 📋 Struktur Excel

| Kolom               | Deskripsi                 | Lebar |
| ------------------- | ------------------------- | ----- |
| NO                  | Nomor urut                | 6     |
| TANGGAL PENGAJUAN   | Tanggal & waktu pengajuan | 18    |
| KODE LEMBAGA        | Kode institusi            | 15    |
| NAMA LEMBAGA        | Nama lengkap institusi    | 30    |
| TIPE PENGAJUAN      | Kategori pengajuan        | 18    |
| JUDUL PENGAJUAN     | Judul singkat             | 35    |
| DESKRIPSI           | Deskripsi lengkap         | 50    |
| ESTIMASI BIAYA (Rp) | Biaya dalam format Rupiah | 20    |
| STATUS              | Status approval           | 12    |
| CATATAN ADMIN       | Catatan dari admin        | 40    |
| PENGAJU             | Nama user yang mengajukan | 25    |
| EMAIL PENGAJU       | Email user                | 30    |

## 🔧 Technical Details

### Files Created

1. **Export Class**: `app/Exports/RequestsExport.php`
2. **Controller Method**: `GeneralRequestController@export`
3. **Route**: `GET /admin/requests/export`
4. **Config**: `config/excel.php`

### Dependencies

- Package: `maatwebsite/excel` v3.1
- PhpSpreadsheet (auto-installed)

## 💡 Tips Penggunaan

1. **Untuk Laporan Bulanan**:

    ```
    /admin/requests/export?start_date=2026-01-01&end_date=2026-01-31
    ```

2. **Untuk Review Pending**:

    ```
    /admin/requests/export?status=pending
    ```

3. **Untuk Audit Approved**:
    ```
    /admin/requests/export?status=approved
    ```

## 🎨 UI/UX

- **Tombol Export**: Warna hijau (#16A34A) dengan icon download
- **Posisi**: Di header halaman, sebelah tombol "Buat Baru"
- **Hover Effect**: Scale animation pada icon
- **Shadow**: Subtle shadow untuk depth

## 📝 Notes

- Export menggunakan data real-time dari database
- File Excel compatible dengan Microsoft Excel, Google Sheets, dan LibreOffice
- Sorting default: Terbaru ke terlama (latest)
- Semua data di-format untuk kemudahan pembacaan

---

**Dibuat oleh**: SIM PAH Development Team  
**Tanggal**: 2026-02-06  
**Versi**: 1.0.0
