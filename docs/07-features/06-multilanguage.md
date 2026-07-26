# LeaderHub — Feature Spec: Multi-Bahasa

> **Priority**: B | **App**: Website | **Status**: Draft

## 1. Tujuan
Menyediakan website dalam Bahasa Indonesia (utama) dan English (toggle),
dengan strategi hybrid: manual override + Google Translate fallback.

## 2. Strategi Hybrid

```
Admin input terjemahan EN manual (translations table)
  └→ Website cek: ada terjemahan EN untuk key ini?
     ├→ YA → pakai terjemahan manual (kualitas tinggi)
     └→ TIDAK → fallback Google Translate widget (otomatis)
```

## 3. Implementasi

### 3.1 Session-based Preference
- Default: `id` (Indonesia)
- Toggle di header → set session `locale = en`
- Tidak ada URL prefix (`/en/...`) — pakai session + cookie
- Alasan: simpler URL, SEO tetap pakai ID (canonical)

### 3.2 Translation Sources
| Source | Kualitas | Coverage |
|--------|---------|---------|
| Manual (`translations` table) | Tinggi (admin kontrol) | UI strings, label, key pages |
| Google Translate widget | Otomatis | Sisa konten yang belum diterjemahkan |
| Konten dinamis (produk, blog) | Manual per record | Admin input EN di resource |

### 3.3 UI Strings
- Pakai Laravel lang files: `lang/id/` dan `lang/en/`
- Atau database-backed: `translations` table (untuk edit via admin)
- Helper: `t('namespace.key')` → cek DB → fallback lang file

### 3.4 Konten Dinamis (Produk, Blog, Pages)
- Field tambahan: `name_en`, `description_en`, `content_en` (nullable)
- Jika locale = en dan field_en ada → pakai itu
- Jika field_en null → Google Translate widget translate otomatis
- Admin input EN via Filament resource (tab "English" di form)

### 3.5 Google Translate Widget
- Embed di website (header atau floating)
- Hanya muncul jika locale = en dan ada konten yang belum diterjemahkan manual
- Script:
```html
<div id="google_translate_element"></div>
<script>
function googleTranslateElementInit() {
  new google.translate.TranslateElement({pageLanguage: 'id', includedLanguages: 'en'}, 'google_translate_element');
}
</script>
<script src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
```
- Hide Google banner via CSS (auto-detect)

## 4. Admin (Filament)
- TranslationResource: CRUD `translations` (locale, namespace, key, value)
- Di ProductResource/BlogPostResource/PageResource: tab "English" dengan field `_en`

## 5. SEO
- Canonical: selalu ID version
- hreflang: `id` default, `en` alternate (jika ada EN content)
- Meta: dari translations (seo_title_en, seo_description_en)

## 6. Acceptance Criteria
1. Toggle ID/EN di header berfungsi (session)
2. UI strings terjemah (manual via translations table)
3. Konten dinamis: field_en dipakai jika ada
4. Google Translate widget muncul untuk fallback
5. Admin bisa input terjemahan EN via Filament
6. Preference persist via cookie (30 hari)
7. Default locale = id
8. Tidak ada URL prefix (session-based)