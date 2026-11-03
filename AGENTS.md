# AI AGENT RULES & PRODUCTION DIRECTIVES

> [!CAUTION]
> **CRITICAL ENVIRONMENT NOTICE — LIVE CAMPUS PRODUCTION**
> Aplikasi ini (**TUTUT - TOEFL Online**) **SUDAH DIGUNAKAN DI PRODUKSI (LIVE KAMPUS)** untuk ujian mahasiswa.
> Seluruh AI Coding Agent (Antigravity, Cursor, Claude Code, Copilot, dsb.) **WAJIB MEMATUHI** aturan berikut tanpa pengecualian.

---

## 1. Golden Rules untuk AI Agent

1. **JANGAN SEMBARANGAN MENGUBAH KODE**:
   - Dilarang refactoring besar, merombak arsitektur, atau mengubah alur yang sudah stabil.
   - Dilarang mengubah formula konversi skor TOEFL di [`app/Models/ExamAttempt.php`](file:///D:/tutut/app/Models/ExamAttempt.php).
   - Dilarang membuat migrasi database *destructive* (`dropColumn`, `dropTable`, ubah tipe kolom yang memicu kehilangan data ujian).

2. **PROTEKSI INTEGRITAS DATA UJIAN**:
   - Data tabel `users`, `exam_attempts`, `exam_answers`, `questions`, `passages`, dan `directions` adalah data aktif kampus.
   - Pastikan query atau mutasi data tidak memutus sesi ujian yang sedang berjalan.

3. **VERIFIKASI SEBELUM EKSEKUSI**:
   - Wajib konfirmasi ke pengguna jika perubahan berpotensi memengaruhi ujian aktif.

---

## 2. Standar Resmi Branching (Tri-Branch Enterprise Flow)

```
[feature/* atau fix/*]
        │ (PR)
        ▼
     [ dev ]      ──────► Deploy otomatis: Server Development (Uji Internal)
        │ (PR)
        ▼
   [ staging ]    ──────► Deploy otomatis: Server Staging (Simulasi / UAT Kampus)
        │ (PR + Tagging)
        ▼
    [ main ]      ──────► Deploy resmi: Server Production (Live Ujian Kampus)
        ▲
        │ (Emergency Only)
   [hotfix/*]
```

---

## 3. Matriks Keamanan & Akses Branch Deployment

| Branch | Target Environment | Level Proteksi | Kebijakan Keamanan (Branch Protection) |
| :--- | :--- | :--- | :--- |
| **`main`** | **Production (Live)** | **MAXIMUM** | <ul><li>Direct push: **BANNED**</li><li>Force push: **BANNED**</li><li>Delete branch: **BANNED**</li><li>Wajib Pull Request dari `staging` (atau `hotfix/*`)</li><li>Wajib Lolos CI Test & Build</li><li>Wajib Tag SemVer (`vX.Y.Z`)</li></ul> |
| **`staging`** | **Staging (UAT / Gladi Bersih)** | **HIGH** | <ul><li>Direct push: **BANNED**</li><li>Force push: **BANNED**</li><li>Delete branch: **BANNED**</li><li>Wajib Pull Request dari `dev`</li><li>Wajib Lolos CI Test & Build</li></ul> |
| **`dev`** | **Development (Integrasi)** | **CONTROLLED** | <ul><li>Direct push: **BANNED**</li><li>Force push: **BANNED**</li><li>Delete branch: **BANNED**</li><li>Wajib Pull Request dari `feature/*` atau `fix/*`</li><li>Wajib Lolos Automated Linting & Unit Test</li></ul> |

---

## 4. Standar Working Branch (Non-Deployment)

* **`feature/<nama>`**: Fitur baru, dicabangkan dari `dev`, merge ke `dev`.
* **`fix/<nama>`**: Perbaikan bug dev, dicabangkan dari `dev`, merge ke `dev`.
* **`hotfix/<nama>`**: Perbaikan darurat live, dicabangkan dari `main`, merge ke `main` & `staging` & `dev`.
