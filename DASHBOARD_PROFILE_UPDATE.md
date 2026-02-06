# ✅ UPDATE: Profil Lembaga di Dashboard

**Tanggal:** 6 Februari 2026  
**File:** `resources/js/Pages/Karyawan/Dashboard.vue`

---

## 🎯 Perubahan

Menambahkan **Institution Profile Card** di Dashboard Karyawan untuk menampilkan informasi lembaga yang sedang login.

---

## 📊 Fitur Baru

### Institution Profile Card

**Posisi:** Setelah Welcome Hub, sebelum Strategic Intelligence Grid

**Informasi yang Ditampilkan:**

1. ✅ **Nama Lembaga** - Nama lengkap institusi
2. ✅ **Kode Lembaga** - Kode unit (badge di kanan atas)
3. ✅ **Pengguna** - Nama & email user yang login
4. ✅ **Role** - Role pengguna (karyawan/lembaga)
5. ✅ **Status Akun** - Status aktif dengan pulse indicator

---

## 🎨 Design

**Card Style:**

- Gradient gold: `from-pail-gold via-[#D4B876] to-[#B89648]`
- Border radius: `rounded-[2.5rem]`
- Shadow: `shadow-2xl shadow-pail-gold/30`
- Blur orbs untuk depth effect

**Layout:**

- Header: Icon building + Nama lembaga + Kode badge
- Grid 3 kolom: Pengguna | Role | Status
- Glassmorphism cards: `bg-white/10 backdrop-blur-md`

**Elements:**

- Building icon SVG (white)
- Kode badge dengan `backdrop-blur-md`
- Status indicator dengan `animate-pulse`
- Typography: `font-black uppercase`

---

## 📱 Responsive

- **Desktop:** 3 kolom grid
- **Mobile:** 1 kolom stack

---

## 🔍 Data yang Digunakan

```javascript
user.institution.name; // Nama lembaga
user.institution.code; // Kode lembaga
user.name; // Nama pengguna
user.email; // Email pengguna
user.role; // Role pengguna
```

---

## 📸 Tampilan

**Desktop:**

```
┌─────────────────────────────────────────────────────┐
│  🏢  PROFIL LEMBAGA                    [SMP PUTRA]  │
│      SMP PUTRA                                      │
│                                                     │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐         │
│  │Pengguna  │  │Role      │  │Status    │         │
│  │John Doe  │  │KARYAWAN  │  │● AKTIF   │         │
│  │john@...  │  │          │  │          │         │
│  └──────────┘  └──────────┘  └──────────┘         │
└─────────────────────────────────────────────────────┘
```

---

## ✅ Testing

1. Login sebagai lembaga: `smp_pa@simpah.test`
2. Pilih institution: "SMP PUTRA (SMP PUTRA)"
3. Navigate ke: `http://sim-pah.test/dashboard`
4. Lihat card profil lembaga di bawah welcome hub

**Yang Harus Terlihat:**

- ✅ Nama lembaga: "SMP PUTRA"
- ✅ Kode badge: "SMP PUTRA"
- ✅ Nama user: "SMP PA User"
- ✅ Email: "smp_pa@simpah.test"
- ✅ Role: "KARYAWAN"
- ✅ Status: "AKTIF" dengan pulse indicator

---

## 🎉 Manfaat

1. **Clarity** - User langsung tahu lembaga mana yang sedang login
2. **Context** - Informasi profil lengkap di satu tempat
3. **Premium** - Design konsisten dengan theme gold
4. **Informative** - Menampilkan semua info penting

---

**Status:** ✅ SELESAI
