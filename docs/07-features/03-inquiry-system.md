# LeaderHub — Feature Spec: Inquiry System

> **Priority**: A (MVP) | **App**: Website + LeaderP | **Status**: Draft

## 1. Tujuan
Mengumpulkan minat produk dari pengunjung (lead generation) dan kirim ke
admin via WhatsApp/email. Bukan e-commerce transaksional.

## 2. Flow

```
Pengunjung browse product
  └→ Klik "Tambah ke Daftar Minat" (product card/detail)
     └→ Product masuk inquiry cart (localStorage)
        └→ Floating inquiry bar muncul (count + "Lihat Daftar")
           └→ Klik → "Daftar Minat Saya" page
              └→ Review list, edit qty, hapus
                 └→ Klik "Kirim Inquiry"
                    └→ Form: nama, WA, email, pesan
                       └→ Submit
                          ├→ Generate INQ-YYYY-XXXXX
                          ├→ Save ke inquiries + inquiry_items
                          ├→ wa.me link (pre-filled message)
                          └→ Email ke admin
                             └→ Admin (LeaderP) lihat inquiry di dashboard
                                └→ Follow-up via WA/email
                                └→ Convert ke Sales Order (opsional)
```

## 3. Komponen

### 3.1 Inquiry Cart (localStorage)
- Key: `leaderhub_inquiry`
- Structure:
```json
{
  "items": [
    { "product_id": 1, "slug": "treadmill-pro-x1", "name": "Treadmill Pro X1", "qty": 1, "added_at": "2026-07-26T10:00:00Z" }
  ],
  "updated_at": "2026-07-26T10:00:00Z"
}
```
- Max 50 item per inquiry
- Persist di localStorage (tidak hilang saat refresh)
- Clear setelah submit sukses

### 3.2 Floating Inquiry Bar
- Fixed bottom, full width, z-index 300
- Border top 2px accent
- Shadow red glow
- Hidden jika cart kosong
- Shows: count (accent, weight 700) + "Lihat Daftar Minat" button
- Slide up animation saat ada item
- Mobile: full width, compact

### 3.3 "Daftar Minat Saya" Page (`/inquiry`)
```
┌─────────────────────────────────────────────┐
│ HEADER                                       │
├─────────────────────────────────────────────┤
│ H1: "DAFTAR MINAT SAYA"                      │
├─────────────────────────────────────────────┤
│ ITEM LIST                                    │
│ ┌──────────────────────────────────────┐    │
│ │ [img] Product Name      [qty-][+]    │    │
│ │        SKU              [Hapus]       │    │
│ └──────────────────────────────────────┘    │
│ ...                                          │
├─────────────────────────────────────────────┤
│ FORM                                         │
│ Nama Lengkap *  [____________________]       │
│ No. WhatsApp *  [____________________]       │
│ Email           [____________________]       │
│ Pesan           [____________________]       │
│                                              │
│ [Kirim via WhatsApp]  [Kirim via Email]     │
├─────────────────────────────────────────────┤
│ Empty state: "Belum ada produk di daftar..." │
└─────────────────────────────────────────────┘
```

### 3.4 Inquiry Form
- Nama (required)
- No. WhatsApp (required, validasi format Indonesia)
- Email (opsional, validasi format)
- Pesan (opsional)
- Honeypot anti-spam
- Rate limit: 5/IP/jam

### 3.5 Submit Actions
**Kirim via WhatsApp**:
- Generate wa.me link dengan pre-filled message:
  ```
  Halo LeaderHub, saya tertarik dengan produk berikut:
  
  Inquiry: INQ-2026-00001
  1. Treadmill Pro X1 (LDR-TREAD-X1) - 1 unit
  2. Dumbbell Set 20kg (LDR-DUMB-20) - 2 unit
  
  Nama: John Doe
  Pesan: (pesan user)
  ```
- Save inquiry ke DB
- Redirect ke wa.me link

**Kirim via Email**:
- Save inquiry ke DB
- Kirim Mailable ke admin (settings `contact.email`)
- Kirim copy ke customer email (jika diisi)
- Tampilkan success page dengan inquiry number

## 4. Inquiry Number Format
- Format: `INQ-YYYY-XXXXX` (e.g. `INQ-2026-00001`)
- Sequential per year
- Generated oleh `InquiryService::generateNumber()`
- Atomic increment (lock + check last + 1)

## 5. Admin View (LeaderP)

### 5.1 Inquiry Resource (Filament)
- List: inquiry_number, customer_name, phone, status, created_at, items count
- Filter: status, date range
- Search: inquiry_number, customer_name, phone
- Actions: View, Contact (WA), Email, Convert to SO, Close, Mark Lost

### 5.2 Inquiry Detail
- Customer info
- Items list (product, qty)
- Admin notes (private)
- Status timeline (new → contacted → converted/closed/lost)
- Activity log
- "Convert to Sales Order" button → create SO dari inquiry items

### 5.3 Dashboard Widget
- "New Inquiries" count (today/week/month)
- "Pending Follow-up" (status new > 24h)
- Recent inquiries list (5 terakhir)

## 6. Data Source
- Submit: insert ke `inquiries` + `inquiry_items`
- Admin: query `inquiries` + eager load `inquiry_items.product`

## 7. Rate Limiting
- 5 inquiry per IP per jam
- Implement via `RateLimiter` Laravel
- Response 429 dengan retry-after header

## 8. Security
- Honeypot field (hidden input, jika terisi → bot → reject)
- CSRF token
- Validate input (nama min 3, phone valid Indonesia, email format)
- Sanitize message (strip HTML)

## 9. Acceptance Criteria
1. "Tambah ke Daftar Minat" berfungsi di product card & detail
2. Inquiry cart persist di localStorage
3. Floating inquiry bar muncul saat ada item
4. "Daftar Minat Saya" page: list, edit qty, hapus
5. Form validasi (nama, WA required)
6. Submit via WA: generate wa.me link benar + save DB
7. Submit via Email: kirim email + save DB
8. Inquiry number auto-generate (INQ-YYYY-XXXXX)
9. Rate limit 5/IP/jam berfungsi
10. Admin (LeaderP) lihat inquiry di dashboard
11. Admin convert inquiry → Sales Order
12. Empty state tampil saat cart kosong
13. Honeypot anti-spam berfungsi