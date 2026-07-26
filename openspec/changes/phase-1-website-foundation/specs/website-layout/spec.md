# Spec: website-layout

## Requirements

### REQ-001: Base Layout
A base layout `layouts/app.blade.php` MUST exist with: HTML lang attribute from session locale, dark class on `<html>` from session theme, CSRF meta tag, Google Fonts (Inter), Vite asset includes, JSON-LD Organization schema.

### REQ-002: Header
The header MUST contain: logo (LeaderHub wordmark with accent dot), navigation links (Produk, Gallery, Blog, Tentang, Kontak), dark/light toggle button, language toggle button (ID/EN), inquiry cart icon with count badge.

### REQ-003: Footer
The footer MUST contain 4 columns: brand + tagline, navigation links, company links, contact info (email, phone from settings).

### REQ-004: Dark Mode Toggle
Clicking the theme toggle MUST POST to `/api/theme` which toggles session `theme` between `light` and `dark`. The `<html>` element MUST get class `dark` when theme is dark. Preference MUST persist across page reloads via session.

### REQ-005: Language Toggle
Clicking the language toggle MUST POST to `/api/lang` which toggles session `locale` between `id` and `en`. The page MUST reload after toggle. Middleware `SetLocale` MUST set `app()->setLocale()` from session on every request.

### REQ-006: Modern Premium Design
The layout MUST use the Modern Premium design system: neutral-first colors (slate), accent red `#DC2626` used sparingly, Inter font, radius 10-14px, soft shadows, no uppercase headings, no grayscale image filters.

### REQ-007: Responsive
The layout MUST be responsive: mobile (320px), tablet (768px), desktop (1024px+). Navigation collapses on mobile.