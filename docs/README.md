# LeaderHub — Documentation Index

> **Version**: 1.0 | **Last Updated**: 2026-07-26

Dokumentasi lengkap proyek LeaderHub — platform terintegrasi untuk distributor
alat fitness yang menyatukan website pemasaran, ERP perusahaan (LeaderP), dan
software gym multi-tenant (LeadGym) dalam satu monorepo.

## Daftar Dokumen

### Inti
| # | Dokumen | Deskripsi |
|---|---------|-----------|
| 00 | [Overview](00-overview.md) | Visi, ruang lingkup, glossary, tech stack summary |
| 01 | [Product Requirements](01-product-requirements.md) | PRD lengkap, user stories, acceptance criteria (website + ERP + gym) |
| 02 | [Architecture](02-architecture.md) | Monorepo 3-app, tech stack, folder structure, data flow, multi-tenant |
| 03 | [Database Schema](03-database-schema.md) | ERD, semua tabel (shared + ERP + gym + license), indexing strategy |
| 04 | [Design System](04-design-system.md) | Design tokens, komponen, Tailwind config, Filament theme |
| 05 | [Roadmap](05-roadmap.md) | Fase pengembangan, milestone, timeline (11 minggu) |
| 06 | [Deployment](06-deployment.md) | Panduan deploy cPanel VPS + dev env lokal (Docker) + aaPanel appendix |
| 08 | [Implementation Plan](08-implementation-plan.md) | Task breakdown detail per fase, dependency, parallel batches |
| 09 | [Feature Expansion](09-feature-expansion.md) | Fitur tambahan/differentiator: SEO+AI, content admin, job portal, gallery modern, ERP & Gym advanced |

### Feature Specs
| # | Dokumen | Deskripsi |
|---|---------|-----------|
| 07.1 | [Homepage](07-features/01-homepage.md) | Hero, featured products, kategori, blog, CTA |
| 07.2 | [Product Catalog](07-features/02-product-catalog.md) | Listing, detail, filter, search, sort |
| 07.3 | [Inquiry System](07-features/03-inquiry-system.md) | Cart, form, WA/email, numbering, admin view |
| 07.4 | [Gallery](07-features/04-gallery.md) | Album, masonry, lightbox |
| 07.5 | [Blog](07-features/05-blog.md) | Listing, detail, SEO, rich text editor |
| 07.6 | [Multi-Bahasa](07-features/06-multilanguage.md) | Hybrid translation (manual + Google Translate) |
| 07.7 | [Admin Panel](07-features/07-admin-panel.md) | LeaderP + LeadGym Filament panel, roles, dashboard |
| 07.8 | [ERP Integration & License](07-features/08-erp-integration.md) | Shared DB, inquiry→SO, license manager, validation API |
| 07.9 | [LeadGym](07-features/09-leadgym.md) | Multi-tenant gym software, modules, license validation |

### Design System Preview
| File | Deskripsi |
|------|-----------|
| [design-system/preview.html](design-system/preview.html) | Interactive HTML preview — colors, typography, buttons, cards, forms, table, modal, hero. Buka di browser, toggle dark/light. |

### Task Implementation (Step-by-Step)
| File | Deskripsi |
|------|-----------|
| [tasks/phase-0-monorepo-setup.md](tasks/phase-0-monorepo-setup.md) | Phase 0: monorepo, core, design tokens, 3 app skeleton |
| [tasks/phase-1-website-foundation.md](tasks/phase-1-website-foundation.md) | Phase 1: layout, design system, settings, auth, pages |
| [tasks/phase-2-website-content.md](tasks/phase-2-website-content.md) | Phase 2: homepage, catalog, gallery, SEO |
| [tasks/phase-3-blog-inquiry.md](tasks/phase-3-blog-inquiry.md) | Phase 3: blog, inquiry cart, form submit, admin view |
| [tasks/phase-4-erp-gym-mvp.md](tasks/phase-4-erp-gym-mvp.md) | Phase 4: LeaderP ERP + LeadGym multi-tenant + license manager |
| [tasks/phase-5-integration-polish.md](tasks/phase-5-integration-polish.md) | Phase 5: integration, SEO, performance, accessibility, docs |
| [tasks/phase-6-deployment.md](tasks/phase-6-deployment.md) | Phase 6: deploy ke cPanel VPS production |

## Cara Membaca

1. **Untuk memahami proyek**: mulai dari `00-overview.md`
2. **Untuk developer**: `02-architecture.md` → `03-database-schema.md` → `04-design-system.md` → `08-implementation-plan.md`
3. **Untuk PM/owner**: `01-product-requirements.md` → `05-roadmap.md`
4. **Untuk DevOps**: `02-architecture.md` → `06-deployment.md`
5. **Untuk designer**: `04-design-system.md` → buka `design-system/preview.html` di browser

## Tech Stack Summary

| Layer | Teknologi |
|-------|-----------|
| Framework | Laravel 12 + Filament 5 + Vue 3 |
| CSS | Tailwind CSS 4 (Modern Premium design system) |
| Database | MySQL 8.0 (kompatibel MariaDB 10.6+) |
| Multi-tenant | stancl/tenancy 3.x (LeadGym) |
| Package manager | pnpm (workspace monorepo) |
| Deployment | cPanel VPS (utama) / aaPanel (alternatif) |
| Dev env | Docker Compose / Laravel Sail / Herd (gratis) |

## Struktur Monorepo

```
leaderhub/
├── core/                    ← shared package (models, services, migrations)
├── packages/design-tokens/  ← design tokens (CSS/JS/PHP, sumber tunggal)
├── apps/
│   ├── website/             ← leaderhub.id (Vue + Filament content admin)
│   ├── hub/                 ← erp.leaderhub.id (LeaderP, Filament 5)
│   └── gym/                 ← gym.leaderhub.id (LeadGym, multi-tenant, Filament 5)
├── storage/                 ← shared storage (symlinked)
├── pnpm-workspace.yaml
└── composer.json
```

## Update Log

| Tanggal | Perubahan |
|---------|-----------|
| 2026-07-26 | Initial draft semua dokumen (v1.0) |