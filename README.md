# Travel Umrah - Manajemen Travel Haji & Umrah

Sistem manajemen travel umrah dan haji lengkap untuk agen perjalanan. Mencakup manajemen jamaah, paket perjalanan, booking, invoice, pembayaran, visa, kloter, hotel, bus, perlengkapan, CRM leads, API mobile untuk muthawif, customer portal, dan programmatic SEO.

## Tech Stack

- **Laravel** 13.x + **PHP** 8.3+
- **Filament** 5.x (admin panel)
- **MySQL** 8.0+
- **TailwindCSS** 4.x + **Vite**
- **Sanctum** (API authentication)
- **Chart.js** (laporan)
- **Playwright** (screenshot capture)

## Quick Start

```bash
# Clone & install
git clone <repo-url> travel-umrah
cd travel-umrah
composer install
cp .env.example .env
php artisan key:generate

# Setup database (MySQL)
# Edit .env: DB_DATABASE, DB_USERNAME, DB_PASSWORD
php artisan migrate:fresh --seed

# Build frontend
npm install
npm run build

# Laragon: akses langsung ke http://travelumrah.test
```

## Demo Accounts

| Role | Email | Password |
|---|---|---|
| Super Admin | admin@travelumrah.com | password |
| Admin | admin2@travelumrah.com | password |
| Finance | finance@travelumrah.com | password |
| Branch Manager | bm_bdg@travelumrah.com | password |
| Agent | agent_jkt@travelumrah.com | password |
| Muthawif | muthawif@travelumrah.com | password |

## Fitur

### Master Data
- Manajemen jamaah (biodata, KTP, paspor, medis, kontak darurat, mahram)
- Cabang dengan hierarki pusat-cabang, kuota haji/umrah, komisi referral
- Staff multi-role (6 role: Super Admin, Admin, Finance, Branch Manager, Agent, Muthawif)
- Dashboard per-role dengan widget berbeda (7 widget: stats, chart, pending approvals, follow-up, dsb)

### Penjualan
- Manajemen paket umrah & haji plus (maskapai, hotel Makkah/Madinah, itinerary, 4 tier harga)
- Booking jamaah dengan 12 status workflow (pending → dp_paid → confirmed → ... → departed → returned)
- Promo code (percentage/fixed, max discount, masa berlaku, batas pemakaian)
- Komisi referral otomatis per cabang

### CRM
- Lead capture otomatis (website, API, input manual — 7 source channel)
- Follow-up tracking 5 status (new → contacted → interested → follow_up → deal/lost)
- Activity log per lead (call, email, WA, meeting)
- Konversi lead → jamaah + booking dalam satu workflow
- Broadcast massal via WhatsApp/email ke jamaah/kloter
- Loyalty points (1 point per 1 juta, redeem untuk diskon)

### Keuangan
- Invoice otomatis per booking (INV-YYYYMM-XXXX, paid/partial/unpaid/overdue)
- Pembayaran dengan upload bukti transfer & verifikasi finance
- Approval workflow (threshold-based: pembayaran di atas nominal tertentu wajib approval)
- Multi-payment per invoice (DP + pelunasan)
- Expense management (per booking/cabang, kategori, approval workflow)
- Refund calculator (persentase refund berdasarkan hari sebelum keberangkatan)

### Operasional
- Kloter (departure groups) dengan assignment muthawif, seat, kamar, katering
- Manajemen visa (pengajuan, verifikasi dokumen, approval/rejection, 5 status workflow)
- Hotel & kamar (Makkah/Madinah, bintang, jarak ke masjid)
- Armada bus & assignment ke kloter (rute embarkasi, Madinah-Makkah, Arafah-Mina)
- Penerbangan (flight number, maskapai, gate, terminal, integrasi kloter)
- Perlengkapan jamaah (koper, ihram, name tag, distribusi & return tracking)
- Asuransi perjalanan & klaim (medical, lost baggage, cancellation, handler assignment)
- Check-in jamaah (verifikasi dokumen, timbang koper, boarding pass)
- Itinerary harian (jadwal ibadah, ziarah, makan per kloter per hari)

### Dokumen
- Upload & verifikasi dokumen jamaah (KTP, KK, paspor, pas foto, buku nikah, sertifikat vaksin, SK Sehat)

### Laporan
- Laporan Penjualan (revenue summary, chart per bulan, top packages, top agents, conversion rate, export PDF)
- Laporan Keuangan (P&L, AR/AP aging, expense by category, revenue vs expense, export PDF)
- Laporan Operasional (kloter utilization, visa approval rate, equipment status, export PDF)
- Date filter + group by harian/mingguan/bulanan + Chart.js interactive

### Marketing & SEO
- Landing page marketing (home, paket, about, kontak)
- Blog & artikel (kategori, SEO meta, featured image, scheduled publish)
- Halaman CMS custom (tentang kami, syarat ketentuan, kebijakan privasi)
- FAQ dengan kategori & sort order
- Testimonial jamaah (rating bintang, social proof)
- Slider banner homepage (gambar, CTA, sort order)
- **Programmatic SEO**: 9 pattern halaman auto-generated dengan JSON-LD schema (ItemList, FAQPage, Product), meta tags lengkap, konten 300+ kata unik
- Dynamic sitemap.xml & robots.txt

### Customer Portal (`/portal`)
- Login mandiri untuk jamaah
- Dashboard: stat cards (active bookings, total spent, loyalty balance)
- My Bookings: list + detail (item, invoice, payment history)
- My Invoices: list + detail + download PDF
- Upload bukti pembayaran
- Data isolation (jamaah hanya lihat data sendiri)

### API Mobile (Sanctum)
- **Public:** daftar paket, detail paket, featured packages, submit lead
- **Jamaah:** profil, update profil, bookings, invoices, pembayaran, dokumen, itinerary
- **Muthawif:** kloter, daftar jamaah, absensi harian, laporan harian, emergency report

### Integrasi
- **Payment Gateway:** 13 preset Indonesia siap pilih (Midtrans, Xendit, Tripay, Doku, OY!, iPaymu, Faspay, Nicepay, Doku Wallet, Artajasa, Finnet, QRIS, LinkAja) — auto-fill nama/code/format/URL
- 7 format API: simulator, redirect_snap, redirect, redirect_tripay, direct_api, ewallet, qr
- API key terenkripsi AES-256-CBC
- User input sendiri provider, tanpa hardcode

### Pengaturan
- System settings (brand name, bank info, approval threshold, komisi default, currency rate)
- Audit log (tracking semua aktivitas user: create, update, delete, login)

### Automasi (Scheduler)
- `bookings:escalate-overdue` — per jam: cek booking overdue → escalate
- `notifications:send-reminders` — daily 08:00: reminder H-1 return + invoice due
- `db:backup` — daily 02:00: backup database
- `broadcasts:send` — per 5 menit: kirim broadcast dari queue

## Programmatic SEO

Halaman SEO di-generate otomatis di route `/s/`:

| Route | Deskripsi |
|---|---|
| `/s/paket-umrah-{bulan}-{tahun}` | Paket per bulan |
| `/s/best-umrah-packages-{tahun}` | Top 10 terpopuler |
| `/s/umrah-{kategori}` | Filter kategori (ekonomi/standard/vip/vvip) |
| `/s/umrah-dari-{kota}` | Filter kota keberangkatan |
| `/s/compare/{a}-vs-{b}` | Head-to-head comparison |
| `/s/alternatives-to-{slug}` | Paket alternatif |
| `/s/travel-umrah-murah` | Budget umrah |
| `/s/haji-plus-{tahun}` | Haji plus tahunan |
| `/s/{maskapai}` | Filter maskapai |

Setiap halaman dilengkapi JSON-LD schema, meta tags, dan konten 300+ kata.

### Submit Sitemap ke Google Search Console
1. Buka [Google Search Console](https://search.google.com/search-console)
2. Tambahkan property (domain atau URL prefix)
3. Verifikasi kepemilikan
4. Submit sitemap: `https://[domain-anda]/sitemap.xml`

## License

Sistem lisensi menggunakan whitelabel.co.id v3 pairing:
- RSA-signed payload + AES-256-GCM encrypted lock file
- Browser wizard di `/__pair`
- Heartbeat validasi dengan 7 hari grace period
- Route marketing & API tetap accessible tanpa lisensi

Untuk development lokal, lisensi di-bypass otomatis (`LICENSE_DEV_BYPASS=true`).

---

Domain apa yang dipakai untuk deploy aplikasi ini?
