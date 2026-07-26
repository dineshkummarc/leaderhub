# LeaderHub — Design System

> **Version**: 1.1 | **Status**: Draft | **Last Updated**: 2026-07-26
> **Direction**: "Modern Premium" — netral-first, aksen merah secukupnya, tipografi rapat, radius lembut
> **Scope**: Unified design system untuk 3 aplikasi — Website (Vue), LeaderP/ERP (Filament), LeadGym (Filament)

---

## 1. Design Philosophy

Identitas visual LeaderHub menyampaikan **modern premium**: bersih, tenang,
dan terkurasi — bukan agresif. Netral menjadi panggung utama, aksen merah
dipakai secukupnya untuk menarik perhatian yang penting. Tipografi Inter yang
rapat (letter-spacing negatif) tanpa uppercase berlebihan memberi kesan
matang dan profesional, sesuai untuk distributor alat fitness kelas atas.

### 1.1 Principles
1. **Calm** — netral-first, whitespace generous, tidak ramai
2. **Refined** — tipografi rapat, bobot terukur (500–700), bukan 900
3. **Restrained accent** — merah dipakai hanya untuk aksi utama & status penting
4. **Soft** — radius lembut (10–14px), shadow halus berlapis, bukan dramatis
5. **Consistent** — tokens yang SAMA dipakai website (Vue/Tailwind) & admin (Filament theme)
6. **Premium** — detail kecil (focus ring, hover lembut, tabular numerals) terasa matang

### 1.2 Tujuan Konsistensi Antar-App
| App | Tech | Bagaimana pakai tokens |
|-----|------|------------------------|
| Website publik | Vue + Tailwind 4 | Tokens via `tailwind.config` + CSS variables |
| LeaderP (ERP) | Filament 5 (Livewire) | Filament Theme provider + custom CSS variables |
| LeadGym | Filament 5 (Livewire) | Filament Theme provider + custom CSS variables |

Tokens sumber kebenaran tunggal ada di `packages/design-tokens/` (monorepo),
diekspor sebagai: `tokens.css` (CSS variables), `tokens.js` (Tailwind config),
`tokens.json` (Filament theme config). Build script generate semua format dari
satu sumber → tidak ada drift antar app.

---

## 2. Design Tokens

### 2.1 Color Palette

#### Light Mode (DEFAULT)
| Token | Hex | Usage |
|-------|-----|-------|
| `--color-bg` | #FFFFFF | Page background |
| `--color-surface` | #F8FAFC | Secondary surface (table header, sidebar) |
| `--color-surface-2` | #F1F5F9 | Tertiary surface (hover, badge neutral) |
| `--color-elevated` | #FFFFFF | Cards, elevated surfaces |
| `--color-text` | #0F172A | Primary text (slate-900) |
| `--color-text-muted` | #64748B | Secondary text (slate-500) |
| `--color-text-subtle` | #94A3B8 | Tertiary text (slate-400) |
| `--color-border` | #E2E8F0 | Borders (slate-200) |
| `--color-border-strong` | #CBD5E1 | Strong borders (slate-300) |
| `--color-accent` | #DC2626 | Red accent — brand primary |
| `--color-accent-hover` | #B91C1C | Red on hover (darker) |
| `--color-accent-soft` | #FEF2F2 | Red tint background (subtle) |
| `--color-accent-ring` | rgba(220,38,38,0.12) | Focus ring |
| `--color-success` | #16A34A | Success green |
| `--color-warning` | #D97706 | Warning amber |
| `--color-info` | #2563EB | Info blue |

#### Dark Mode
| Token | Hex | Usage |
|-------|-----|-------|
| `--color-bg` | #0B0F14 | Page background (deep slate) |
| `--color-surface` | #111821 | Secondary surface |
| `--color-surface-2` | #1A2330 | Tertiary surface |
| `--color-elevated` | #131B26 | Cards, elevated surfaces |
| `--color-text` | #F1F5F9 | Primary text |
| `--color-text-muted` | #94A3B8 | Secondary text |
| `--color-text-subtle` | #64748B | Tertiary text |
| `--color-border` | #1F2937 | Borders |
| `--color-border-strong` | #334155 | Strong borders |
| `--color-accent` | #EF4444 | Red accent (brighter on dark) |
| `--color-accent-hover` | #F87171 | Red on hover |
| `--color-accent-soft` | rgba(239,68,68,0.10) | Red tint background |
| `--color-accent-ring` | rgba(239,68,68,0.18) | Focus ring |

### 2.2 Typography

| Token | Value | Usage |
|-------|-------|-------|
| `--font-sans` | 'Inter', system-ui, sans-serif | Semua — heading & body |
| `--font-mono` | ui-monospace, 'SF Mono', Menlo, monospace | Code, SKU, invoice number |

**Catatan**: Tidak ada font heading terpisah. Inter dipakai untuk semuanya
dengan variasi bobot dan letter-spacing. Ini memberi kesan modern, bersih,
dan konsisten (mengikuti estetika Linear/Vercel/Stripe).

#### Type Scale
| Token | Size | Line Height | Weight | Letter-spacing | Usage |
|-------|------|-------------|--------|----------------|-------|
| `--text-display` | 3rem (48px) | 1.05 | 700 | -0.035em | Hero headline |
| `--text-h1` | 2.25rem (36px) | 1.1 | 700 | -0.03em | Page title |
| `--text-h2` | 1.75rem (28px) | 1.15 | 700 | -0.025em | Section title |
| `--text-h3` | 1.375rem (22px) | 1.2 | 600 | -0.02em | Subsection |
| `--text-h4` | 1.125rem (18px) | 1.3 | 600 | -0.015em | Card title |
| `--text-body-lg` | 1.125rem (18px) | 1.6 | 400 | 0 | Lead paragraph |
| `--text-body` | 1rem (16px) | 1.6 | 400 | 0 | Body text |
| `--text-body-sm` | 0.875rem (14px) | 1.5 | 400 | 0 | Small text |
| `--text-label` | 0.8125rem (13px) | 1.4 | 500 | 0 | Form label, meta |
| `--text-caption` | 0.75rem (12px) | 1.4 | 500 | 0 | Caption, hint |

**Tidak ada uppercase** kecuali untuk overline/eyebrow opsional (font-size 12px,
weight 600, letter-spacing 0.04em, color accent).

### 2.3 Spacing

| Token | Value |
|-------|-------|
| `--space-0` | 0 |
| `--space-1` | 0.25rem (4px) |
| `--space-2` | 0.5rem (8px) |
| `--space-3` | 0.75rem (12px) |
| `--space-4` | 1rem (16px) |
| `--space-5` | 1.25rem (20px) |
| `--space-6` | 1.5rem (24px) |
| `--space-8` | 2rem (32px) |
| `--space-10` | 2.5rem (40px) |
| `--space-12` | 3rem (48px) |
| `--space-16` | 4rem (64px) |
| `--space-20` | 5rem (80px) |
| `--space-24` | 6rem (96px) |

### 2.4 Border Radius

| Token | Value | Usage |
|-------|-------|-------|
| `--radius-sm` | 6px | Small controls, tags |
| `--radius` | 10px | Buttons, inputs (default) |
| `--radius-lg` | 14px | Cards, modals, table wrap |
| `--radius-xl` | 18px | Large cards (opsional) |
| `--radius-full` | 9999px | Pills, badges, avatars |

### 2.5 Shadows (soft, berlapis)

| Token | Value | Usage |
|-------|-------|-------|
| `--shadow-sm` | 0 1px 2px rgba(15,23,42,0.04), 0 1px 3px rgba(15,23,42,0.06) | Subtle elevation (cards) |
| `--shadow-md` | 0 4px 12px rgba(15,23,42,0.06), 0 2px 4px rgba(15,23,42,0.04) | Hover, dropdown |
| `--shadow-lg` | 0 12px 32px rgba(15,23,42,0.10), 0 4px 8px rgba(15,23,42,0.06) | Modals, popovers |
| `--shadow-focus` | 0 0 0 3px var(--color-accent-ring) | Focus ring (bukan shadow, tapi ring) |

Dark mode shadows pakai rgba(0,0,0,0.3–0.45).

### 2.6 Transitions

| Token | Duration | Easing |
|-------|----------|--------|
| `--transition-fast` | 160ms | ease-out |
| `--transition` | 180ms | ease-out |
| `--transition-slow` | 220ms | cubic-bezier(0.4,0,0.2,1) |

### 2.7 Layout

| Token | Value |
|-------|-------|
| `--container-max` | 1120px |
| `--container-narrow` | 720px |
| `--container-wide` | 1280px |
| `--header-height` | 64px |
| `--grid-gap` | 20px |

### 2.8 Z-Index Scale

| Token | Value | Usage |
|-------|-------|-------|
| `--z-base` | 0 | Normal flow |
| `--z-dropdown` | 100 | Dropdowns |
| `--z-sticky` | 200 | Sticky header |
| `--z-inquiry-bar` | 300 | Floating inquiry bar |
| `--z-modal` | 1000 | Modals |
| `--z-toast` | 1100 | Toasts |

---

## 3. Components

### 3.1 Buttons
| Variant | Style |
|---------|-------|
| Primary | Accent bg, white text, weight 600, shadow-sm |
| Secondary | Elevated bg, border-strong, text utama |
| Outline | Transparent, accent border + accent text |
| Subtle | Accent-soft bg, accent text |
| Ghost | Transparent, hover surface |

Semua button: radius 10px, padding 10px 18px, font-size 14px, weight 600,
transition 180ms. Large: padding 13px 24px, font-size 15px. Tidak uppercase.

### 3.2 Product Card (Website)
- Aspect ratio 4:5 (portrait, modern)
- Border halus, shadow-sm, radius-lg
- Hover: lift -2px, shadow-lg, border-strong (tanpa grayscale filter dramatis)
- Tag: pill accent di top-left (radius-full)
- Category: label 13px muted
- Name: weight 600, size 1rem, letter-spacing -0.01em
- Price: weight 700, tabular-nums, currency muted kecil

### 3.3 Inquiry Bar (floating, Website)
- Fixed bottom, full width, z-300
- Background elevated, border-top, shadow-md
- Shows count (accent, weight 700) + "Lihat Daftar" button
- Slide up animation (220ms)

### 3.4 Hero Banner (Website)
- Background gradien gelap (slate) + radial glow aksen lembut
- Eyebrow: 13px weight 600 accent
- Title: display 48px weight 700 letter-spacing -0.035em, color putih
- Sub: 18px slate-300
- CTA: primary + secondary (translucent white)
- Stats: border-top subtle, angka tabular-nums

### 3.5 Editorial Blog Card (Website)
- Image 16:10, radius-lg
- Category: label 13px muted
- Title: weight 600, size 1.125rem, 2-line clamp
- Meta: caption 12px muted
- Hover: lift -2px, shadow-md

### 3.6 Admin Data Table (Filament — LeaderP & LeadGym)
- Header: surface bg, weight 600, 12px, color muted (tidak uppercase)
- Rows: hover surface bg, border-bottom border
- Action buttons: icon-only, ghost variant, hover accent-soft
- Wrap: radius-lg, shadow-sm, border

### 3.7 Admin Stat Card (Filament Dashboard)
- Elevated bg, border, radius-lg, shadow-sm, padding 20px 22px
- Label: 13px weight 500 muted
- Value: 1.875rem weight 700 letter-spacing -0.025em, tabular-nums
- Trend: 12px weight 600, success (up) / accent (down), dengan arrow

### 3.8 Form Inputs (Filament & Website)
- Background elevated, border-strong, radius 10px, padding 10px 14px
- Label: 13px weight 500
- Focus: border accent + ring 3px accent-ring
- Error: border accent, text accent, hint 12px
- Placeholder: text-subtle

### 3.9 Badge / Status Pill
- Radius full, padding 3px 11px, weight 600, size 12px
- Dot indicator (6px) sebelum teks (kecuali neutral)
- Variants: success, danger, warning, info, neutral

### 3.10 Modal / Dialog
- Backdrop: rgba(15,23,42,0.5) + blur 4px
- Container: elevated bg, radius-lg, shadow-lg, max-width 460px
- Header: padding 18px 22px, title weight 600
- Body: padding 8px 22px 22px, text muted
- Footer: surface bg, border-top, padding 14px 22px

### 3.11 Segmented Nav (tabs)
- Container: surface bg, border, radius 10px, padding 4px
- Item: padding 7px 14px, 13px weight 500, muted
- Active: elevated bg, text utama, shadow-sm
- Digunakan di: filter, settings tabs, admin sub-nav

---

## 4. Tailwind Config (Website — Vue)

```js
// apps/website/tailwind.config.js
import tokens from '@leaderhub/design-tokens/tokens.js';

export default {
  content: ['./resources/views/**/*.{vue,php}'],
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        bg: 'var(--color-bg)',
        surface: 'var(--color-surface)',
        'surface-2': 'var(--color-surface-2)',
        elevated: 'var(--color-elevated)',
        text: 'var(--color-text)',
        'text-muted': 'var(--color-text-muted)',
        'text-subtle': 'var(--color-text-subtle)',
        border: 'var(--color-border)',
        'border-strong': 'var(--color-border-strong)',
        accent: {
          DEFAULT: 'var(--color-accent)',
          hover: 'var(--color-accent-hover)',
          soft: 'var(--color-accent-soft)',
        },
        success: 'var(--color-success)',
        warning: 'var(--color-warning)',
        info: 'var(--color-info)',
      },
      fontFamily: {
        sans: ['Inter', 'system-ui', 'sans-serif'],
        mono: ['ui-monospace', 'SF Mono', 'Menlo', 'monospace'],
      },
      fontSize: {
        display: ['3rem', { lineHeight: '1.05', fontWeight: '700', letterSpacing: '-0.035em' }],
        h1: ['2.25rem', { lineHeight: '1.1', fontWeight: '700', letterSpacing: '-0.03em' }],
        h2: ['1.75rem', { lineHeight: '1.15', fontWeight: '700', letterSpacing: '-0.025em' }],
        h3: ['1.375rem', { lineHeight: '1.2', fontWeight: '600', letterSpacing: '-0.02em' }],
        h4: ['1.125rem', { lineHeight: '1.3', fontWeight: '600', letterSpacing: '-0.015em' }],
      },
      maxWidth: {
        container: '1120px',
        narrow: '720px',
        wide: '1280px',
      },
      borderRadius: {
        sm: '6px',
        DEFAULT: '10px',
        lg: '14px',
        xl: '18px',
      },
      boxShadow: {
        sm: '0 1px 2px rgba(15,23,42,0.04), 0 1px 3px rgba(15,23,42,0.06)',
        md: '0 4px 12px rgba(15,23,42,0.06), 0 2px 4px rgba(15,23,42,0.04)',
        lg: '0 12px 32px rgba(15,23,42,0.10), 0 4px 8px rgba(15,23,42,0.06)',
        focus: '0 0 0 3px var(--color-accent-ring)',
      },
      transitionDuration: { DEFAULT: '180ms' },
    },
  },
};
```

---

## 5. Filament Theme (LeaderP & LeadGym)

Filament 5 pakai Tailwind 4. Theme di-override via `PanelProvider` + custom CSS.

### 5.1 Panel Provider Config
```php
// apps/hub/app/Providers/Filament/HubPanelProvider.php
public function panel(Panel $panel): Panel
{
    return $panel
        ->id('hub')
        ->path('hub')
        ->brandName('LeaderP')
        ->font('Inter')
        ->colors([
            'primary' => '#DC2626',  // accent
            'danger'  => '#DC2626',
            'success' => '#16A34A',
            'warning' => '#D97706',
            'info'    => '#2563EB',
            'gray'    => '#64748B',
        ])
        ->renderHook('panels.head', fn () => view('filament.theme-styles'))
        ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
        // ...
}
```

### 5.2 Custom Theme CSS (`resources/views/filament/theme-styles.blade.php`)
```html
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
  :root {
    --color-accent: #DC2626;
    --font-sans: 'Inter', system-ui, sans-serif;
  }
  html.dark { --color-accent: #EF4444; }
  body { font-family: var(--font-sans); -webkit-font-smoothing: antialiased; }
  .fi-topbar, .fi-sidebar { font-family: var(--font-sans); }
  .fi-sidebar-brand h1 { font-weight: 700; letter-spacing: -0.02em; }
  .fi-btn-primary { border-radius: 10px; font-weight: 600; }
  .fi-card { border-radius: 14px; box-shadow: 0 1px 2px rgba(15,23,42,0.04), 0 1px 3px rgba(15,23,42,0.06); }
  .fi-table th { font-weight: 600; font-size: 12px; color: #64748B; text-transform: none; letter-spacing: 0; }
  .fi-input, .fi-select { border-radius: 10px; }
  .fi-input:focus, .fi-select:focus { box-shadow: 0 0 0 3px rgba(220,38,38,0.12); }
</style>
```

### 5.3 LeadGym Theme
Identik dengan LeaderP, hanya `brandName('LeadGym')` dan logo berbeda. Tokens
warna sama → konsistensi terjaga.

---

## 6. Assets

### 6.1 Fonts (Google Fonts)
```html
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
```

### 6.2 Icons
- **Lucide Icons** (SVG, tree-shakeable) — untuk website Vue & Filament
- Usage website: Vue component atau inline SVG

### 6.3 Logo
- Wordmark: "LeaderHub" weight 700, letter-spacing -0.02em
- Mark: kotak rounded (radius 6px) berwarna aksen merah, di kiri wordmark
- Variants: full-color (light bg), white (dark bg), mono (single color)

---

## 7. Image Treatment

- Product/gallery images: tanpa grayscale filter default. Kualitas foto menjadi fokus.
- Hero background: gradien gelap + radial glow aksen
- Format: WebP (dengan fallback JPG/PNG), responsive srcset
- Lazy load untuk gambar di bawah fold

---

## 8. Responsive Breakpoints

| Token | Width | Usage |
|-------|-------|-------|
| `--bp-sm` | 640px | Mobile landscape |
| `--bp-md` | 768px | Tablet portrait |
| `--bp-lg` | 1024px | Tablet landscape / small laptop |
| `--bp-xl` | 1280px | Desktop |

Mobile-first: base styles untuk mobile, `md:` ke atas untuk tablet/desktop.

---

## 9. Accessibility (WCAG 2.1 AA)

- Kontras teks: minimum 4.5:1 (normal), 3:1 (large)
  - Slate-900 (#0F172A) on white: 18.4:1 ✅
  - White on accent (#DC2626): 5.9:1 ✅
  - Slate-500 (#64748B) on white: 4.6:1 ✅
  - Slate-400 (#94A3B8) on white: 2.8:1 ⚠️ (hanya untuk non-essential text)
- Focus visible: ring 3px accent-ring
- Keyboard navigation: semua interaktif reachable via Tab
- ARIA labels pada icon-only buttons
- Reduced motion: respect `prefers-reduced-motion`

---

## 10. Design Tokens Package (Monorepo)

Struktur `packages/design-tokens/`:

```
packages/design-tokens/
├── tokens.json          ← SUMBER KEBENARAN (Style Dictionary format)
├── build.js             ← Generate tokens.css, tokens.js, tokens.scss
├── dist/
│   ├── tokens.css       ← CSS variables (dipakai semua app)
│   ├── tokens.js        ← Tailwind config object (website)
│   ├── tokens.scss      ← (opsional, untuk Filament custom)
│   └── filament-theme.php  ← Filament colors array (LeaderP & LeadGym)
└── package.json
```

### Build
```bash
cd packages/design-tokens
pnpm install
pnpm build   # regenerate dist/ from tokens.json
```

### Konsumsi
- **Website**: `import tokens from '@leaderhub/design-tokens/dist/tokens.js'` di tailwind.config
- **LeaderP/LeadGym**: `require('@leaderhub/design-tokens/dist/tokens.css')` + pakai `filament-theme.php` colors
- **Update tokens**: edit `tokens.json` → `pnpm build` → commit `dist/` → semua app dapat update

---

## 11. Interactive Preview

Lihat preview interaktif (tokens, komponen, light/dark toggle):
[`../design-system/preview.html`](../design-system/preview.html)

Buka file di browser untuk melihat:
- Color palette (light & dark)
- Typography scale (Inter, rapat, tanpa uppercase)
- Button variants (primary, secondary, outline, subtle, ghost)
- Product card, stat card, blog card
- Form inputs, badges, table, modal
- Hero dengan gradien + glow lembut
- Segmented nav
- Dark/light mode toggle live