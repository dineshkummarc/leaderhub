# LeaderHub — Database Schema

> **Version**: 1.0 | **Status**: Draft | **Last Updated**: 2026-07-26
> **DBMS**: MySQL 8.0 (kompatibel MariaDB 10.6+) | **Charset**: utf8mb4 | **Engine**: InnoDB

## 1. Konvensi

- **Naming**: snake_case, plural (e.g. `products`, `inquiry_items`)
- **Primary key**: `id` BIGINT UNSIGNED AUTO_INCREMENT
- **Foreign key**: `{table_singular}_id` (e.g. `product_id`)
- **Timestamps**: `created_at`, `updated_at` TIMESTAMP NULL
- **Soft delete**: `deleted_at` TIMESTAMP NULL (jika perlu)
- **Tenant**: tabel LeadGym punya `tenant_id` BIGINT (FK ke `tenants`)
- **Money**: DECIMAL(15,2) — IDR; DECIMAL(12,2) — USD
- **Status**: VARCHAR(50) dengan enum string (mudah dibaca, kompatibel)
- **UUID**: CHAR(36) untuk public-facing ID (inquiry, license key)

## 2. ERD Overview

```
┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│   users     │     │  settings   │     │  tenants    │
│ (auth shared)│     │ (key-value) │     │ (LeadGym)   │
└──────┬──────┘     └─────────────┘     └──────┬──────┘
       │                                       │
       │ polymorphic                       ┌───┴──────┐
       │                                   │          │
┌──────┴──────┐                      ┌─────┴────┐ ┌───┴────────┐
│  inquiries  │                      │ members  │ │ gym_users  │
└──────┬──────┘                      └────┬─────┘ └─────┬──────┘
       │                                  │             │
  ┌────┴─────┐                      ┌────┴─────┐  polymorphic
  │ inquiry  │                      │subscriptions│
  │  _items  │                      └────┬─────┘
  └────┬─────┘                           │
       │                            ┌────┴────┐
       │                            │ invoices│
┌──────┴──────┐                     └─────────┘
│  products   │
└──────┬──────┘
       │
  ┌────┴───────┬──────────┬──────────┐
  │            │          │          │
┌─┴─────┐ ┌───┴────┐ ┌───┴────┐ ┌───┴──────┐
│product│ │product │ │stock_  │ │purchase_ │
│_images│ │_specs  │ │levels  │ │orders    │
└───────┘ └────────┘ └────────┘ └────┬─────┘
                                     │
                               ┌─────┴──────┐
                               │ po_items   │
                               └────────────┘
```

## 3. Tabel — Shared (Core)

### 3.1 users
Autentikasi shared untuk website admin + LeaderP. LeadGym punya `gym_users` terpisah (per-tenant).

| Kolom | Tipe | Catatan |
|-------|------|--------|
| id | BIGINT PK | — |
| name | VARCHAR(255) | — |
| email | VARCHAR(255) UNIQUE | — |
| password | VARCHAR(255) | bcrypt |
| role | VARCHAR(50) | super_admin, content_admin, sales_admin, inventory_admin, accounting_admin, hr_admin, company_admin, license_admin |
| is_active | BOOLEAN | default true |
| last_login_at | TIMESTAMP NULL | — |
| created_at, updated_at | TIMESTAMP | — |

### 3.2 settings
Konfigurasi key-value, grouped.

| Kolom | Tipe | Catatan |
|-------|------|--------|
| id | BIGINT PK | — |
| group | VARCHAR(50) | general, contact, seo, social, currency |
| key | VARCHAR(100) | — |
| value | TEXT | — |
| type | VARCHAR(20) | text, textarea, boolean, number, json, image |
| is_translatable | BOOLEAN | — |
| created_at, updated_at | TIMESTAMP | — |

UNIQUE: `(group, key)`

### 3.3 translations
Override manual untuk multi-bahasa.

| Kolom | Tipe | Catatan |
|-------|------|--------|
| id | BIGINT PK | — |
| locale | VARCHAR(5) | id, en |
| namespace | VARCHAR(50) | website, product, blog |
| key | VARCHAR(255) | — |
| value | TEXT | — |
| created_at, updated_at | TIMESTAMP | — |

UNIQUE: `(locale, namespace, key)`

### 3.4 pages
Static pages (About, Contact, Terms, Privacy).

| Kolom | Tipe | Catatan |
|-------|------|--------|
| id | BIGINT PK | — |
| slug | VARCHAR(255) UNIQUE | — |
| title | VARCHAR(255) | — |
| content | LONGTEXT | rich text |
| seo_title | VARCHAR(255) NULL | — |
| seo_description | TEXT NULL | — |
| seo_image | VARCHAR(255) NULL | — |
| status | VARCHAR(20) | draft, published |
| sort_order | INT | — |
| created_at, updated_at | TIMESTAMP | — |

### 3.5 categories
Kategori produk (bukan blog).

| Kolom | Tipe | Catatan |
|-------|------|--------|
| id | BIGINT PK | — |
| parent_id | BIGINT NULL FK→categories | nested |
| slug | VARCHAR(255) UNIQUE | — |
| name | VARCHAR(255) | — |
| description | TEXT NULL | — |
| image | VARCHAR(255) NULL | — |
| sort_order | INT | — |
| is_featured | BOOLEAN | tampil di homepage |
| created_at, updated_at | TIMESTAMP | — |

Index: `(parent_id)`, `(slug)`

### 3.6 brands
| Kolom | Tipe | Catatan |
|-------|------|--------|
| id | BIGINT PK | — |
| slug | VARCHAR(255) UNIQUE | — |
| name | VARCHAR(255) | — |
| logo | VARCHAR(255) NULL | — |
| description | TEXT NULL | — |
| created_at, updated_at | TIMESTAMP | — |

### 3.7 products
| Kolom | Tipe | Catatan |
|-------|------|--------|
| id | BIGINT PK | — |
| category_id | BIGINT FK→categories | — |
| brand_id | BIGINT FK→brands NULL | — |
| slug | VARCHAR(255) UNIQUE | — |
| name | VARCHAR(255) | — |
| sku | VARCHAR(100) UNIQUE | — |
| barcode | VARCHAR(100) NULL | — |
| material | VARCHAR(255) NULL | — |
| description | LONGTEXT NULL | — |
| purchase_price_usd | DECIMAL(12,2) | harga beli USD |
| sale_price_idr | DECIMAL(15,2) | harga jual IDR |
| warranty_period_months | INT NULL | — |
| is_featured | BOOLEAN | homepage |
| is_new | BOOLEAN | badge "New" |
| is_best_seller | BOOLEAN | badge |
| status | VARCHAR(20) | draft, published, archived |
| sort_order | INT | — |
| created_at, updated_at | TIMESTAMP | — |

Index: `(category_id)`, `(brand_id)`, `(slug)`, `(sku)`, `(status)`, `(is_featured)`

### 3.8 product_images
| Kolom | Tipe | Catatan |
|-------|------|--------|
| id | BIGINT PK | — |
| product_id | BIGINT FK→products ON DELETE CASCADE | — |
| path | VARCHAR(255) | — |
| alt | VARCHAR(255) NULL | — |
| sort_order | INT | — |
| created_at, updated_at | TIMESTAMP | — |

Index: `(product_id)`

### 3.9 product_specs
Spec table per produk (key-value).

| Kolom | Tipe | Catatan |
|-------|------|--------|
| id | BIGINT PK | — |
| product_id | BIGINT FK→products ON DELETE CASCADE | — |
| label | VARCHAR(100) | — |
| value | VARCHAR(255) | — |
| sort_order | INT | — |

Index: `(product_id)`

### 3.10 warehouses
| Kolom | Tipe | Catatan |
|-------|------|--------|
| id | BIGINT PK | — |
| name | VARCHAR(255) | — |
| code | VARCHAR(50) UNIQUE | — |
| address | TEXT NULL | — |
| is_active | BOOLEAN | — |
| created_at, updated_at | TIMESTAMP | — |

### 3.11 stock_levels
| Kolom | Tipe | Catatan |
|-------|------|--------|
| id | BIGINT PK | — |
| product_id | BIGINT FK→products | — |
| warehouse_id | BIGINT FK→warehouses | — |
| quantity | INT | — |
| low_stock_threshold | INT DEFAULT 5 | — |
| updated_at | TIMESTAMP | — |

UNIQUE: `(product_id, warehouse_id)`

### 3.12 stock_movements
| Kolom | Tipe | Catatan |
|-------|------|--------|
| id | BIGINT PK | — |
| product_id | BIGINT FK→products | — |
| warehouse_id | BIGINT FK→warehouses | — |
| type | VARCHAR(20) | in, out, transfer |
| quantity | INT | — |
| reference_type | VARCHAR(50) | purchase_order, sales_order, adjustment, transfer |
| reference_id | BIGINT NULL | — |
| notes | TEXT NULL | — |
| created_at | TIMESTAMP | — |

Index: `(product_id)`, `(warehouse_id)`, `(created_at)`

### 3.13 suppliers
| Kolom | Tipe | Catatan |
|-------|------|--------|
| id | BIGINT PK | — |
| name | VARCHAR(255) | — |
| contact_person | VARCHAR(255) NULL | — |
| email | VARCHAR(255) NULL | — |
| phone | VARCHAR(50) NULL | — |
| address | TEXT NULL | — |
| country | VARCHAR(100) | — |
| currency | VARCHAR(3) DEFAULT 'USD' | — |
| created_at, updated_at | TIMESTAMP | — |

### 3.14 purchase_orders
| Kolom | Tipe | Catatan |
|-------|------|--------|
| id | BIGINT PK | — |
| po_number | VARCHAR(50) UNIQUE | PO-YYYY-XXXXX |
| supplier_id | BIGINT FK→suppliers | — |
| warehouse_id | BIGINT FK→warehouses | — |
| order_date | DATE | — |
| expected_date | DATE NULL | — |
| received_date | DATE NULL | — |
| currency | VARCHAR(3) DEFAULT 'USD' | — |
| subtotal_usd | DECIMAL(12,2) | — |
| shipping_usd | DECIMAL(12,2) DEFAULT 0 | — |
| duty_usd | DECIMAL(12,2) DEFAULT 0 | — |
| total_usd | DECIMAL(12,2) | — |
| exchange_rate | DECIMAL(10,2) | USD→IDR saat receive |
| total_idr | DECIMAL(15,2) | landed cost |
| status | VARCHAR(20) | draft, sent, partial, received, cancelled |
| notes | TEXT NULL | — |
| created_by | BIGINT FK→users | — |
| created_at, updated_at | TIMESTAMP | — |

Index: `(supplier_id)`, `(status)`, `(order_date)`

### 3.15 purchase_order_items
| Kolom | Tipe | Catatan |
|-------|------|--------|
| id | BIGINT PK | — |
| purchase_order_id | BIGINT FK→purchase_orders ON DELETE CASCADE | — |
| product_id | BIGINT FK→products | — |
| quantity | INT | — |
| unit_price_usd | DECIMAL(12,2) | — |
| total_usd | DECIMAL(12,2) | — |
| received_quantity | INT DEFAULT 0 | — |

Index: `(purchase_order_id)`, `(product_id)`

### 3.16 inquiries
| Kolom | Tipe | Catatan |
|-------|------|--------|
| id | BIGINT PK | — |
| inquiry_number | VARCHAR(50) UNIQUE | INQ-YYYY-XXXXX |
| uuid | CHAR(36) UNIQUE | public ID |
| customer_name | VARCHAR(255) | — |
| customer_phone | VARCHAR(50) | WA |
| customer_email | VARCHAR(255) NULL | — |
| message | TEXT NULL | — |
| status | VARCHAR(20) | new, contacted, converted, closed, lost |
| admin_notes | TEXT NULL | — |
| converted_sales_order_id | BIGINT NULL FK→sales_orders | — |
| ip_address | VARCHAR(45) | — |
| user_agent | TEXT NULL | — |
| created_at, updated_at | TIMESTAMP | — |

Index: `(status)`, `(created_at)`, `(inquiry_number)`

### 3.17 inquiry_items
| Kolom | Tipe | Catatan |
|-------|------|--------|
| id | BIGINT PK | — |
| inquiry_id | BIGINT FK→inquiries ON DELETE CASCADE | — |
| product_id | BIGINT FK→products | — |
| quantity | INT DEFAULT 1 | — |
| notes | TEXT NULL | — |

Index: `(inquiry_id)`, `(product_id)`

### 3.18 sales_orders
| Kolom | Tipe | Catatan |
|-------|------|--------|
| id | BIGINT PK | — |
| so_number | VARCHAR(50) UNIQUE | SO-YYYY-XXXXX |
| inquiry_id | BIGINT NULL FK→inquiries | — |
| customer_name | VARCHAR(255) | — |
| customer_phone | VARCHAR(50) | — |
| customer_email | VARCHAR(255) NULL | — |
| customer_address | TEXT NULL | — |
| order_date | DATE | — |
| delivery_date | DATE NULL | — |
| subtotal_idr | DECIMAL(15,2) | — |
| discount_idr | DECIMAL(15,2) DEFAULT 0 | — |
| tax_idr | DECIMAL(15,2) DEFAULT 0 | — |
| total_idr | DECIMAL(15,2) | — |
| status | VARCHAR(20) | draft, confirmed, delivered, invoiced, closed, cancelled |
| created_by | BIGINT FK→users | — |
| created_at, updated_at | TIMESTAMP | — |

Index: `(status)`, `(order_date)`, `(inquiry_id)`

### 3.19 sales_order_items
| Kolom | Tipe | Catatan |
|-------|------|--------|
| id | BIGINT PK | — |
| sales_order_id | BIGINT FK→sales_orders ON DELETE CASCADE | — |
| product_id | BIGINT FK→products | — |
| quantity | INT | — |
| unit_price_idr | DECIMAL(15,2) | — |
| total_idr | DECIMAL(15,2) | — |
| serial_number | VARCHAR(100) NULL | — |

### 3.20 sales_activities
| Kolom | Tipe | Catatan |
|-------|------|--------|
| id | BIGINT PK | — |
| sales_order_id | BIGINT FK→sales_orders ON DELETE CASCADE | — |
| type | VARCHAR(50) | lead, contact, demo, quote, order, follow_up |
| description | TEXT | — |
| scheduled_at | TIMESTAMP NULL | — |
| completed_at | TIMESTAMP NULL | — |
| created_by | BIGINT FK→users | — |
| created_at, updated_at | TIMESTAMP | — |

### 3.21 installations
| Kolom | Tipe | Catatan |
|-------|------|--------|
| id | BIGINT PK | — |
| sales_order_id | BIGINT FK→sales_orders | — |
| scheduled_date | DATE | — |
| technician_id | BIGINT NULL FK→employees | — |
| location | TEXT | — |
| checklist | JSON | array of {item, done} |
| status | VARCHAR(20) | scheduled, in_progress, completed, cancelled |
| completed_at | TIMESTAMP NULL | — |
| sign_off_by | VARCHAR(255) NULL | customer name |
| notes | TEXT NULL | — |
| created_at, updated_at | TIMESTAMP | — |

### 3.22 warranties
| Kolom | Tipe | Catatan |
|-------|------|--------|
| id | BIGINT PK | — |
| warranty_number | VARCHAR(50) UNIQUE | WAR-YYYY-XXXXX |
| sales_order_id | BIGINT FK→sales_orders | — |
| product_id | BIGINT FK→products | — |
| serial_number | VARCHAR(100) | — |
| customer_name | VARCHAR(255) | — |
| start_date | DATE | — |
| end_date | DATE | — |
| status | VARCHAR(20) | active, expired, claimed |
| created_at, updated_at | TIMESTAMP | — |

Index: `(end_date)`, `(status)`

### 3.23 warranty_claims
| Kolom | Tipe | Catatan |
|-------|------|--------|
| id | BIGINT PK | — |
| warranty_id | BIGINT FK→warranties | — |
| claim_number | VARCHAR(50) UNIQUE | CLM-YYYY-XXXXX |
| issue | TEXT | — |
| type | VARCHAR(20) | repair, replacement, refund |
| status | VARCHAR(20) | open, in_progress, resolved, rejected |
| resolution | TEXT NULL | — |
| created_at, updated_at | TIMESTAMP | — |

### 3.24 support_tickets
| Kolom | Tipe | Catatan |
|-------|------|--------|
| id | BIGINT PK | — |
| ticket_number | VARCHAR(50) UNIQUE | TKT-YYYY-XXXXX |
| customer_name | VARCHAR(255) | — |
| customer_phone | VARCHAR(50) | — |
| subject | VARCHAR(255) | — |
| description | TEXT | — |
| priority | VARCHAR(20) | low, medium, high, urgent |
| status | VARCHAR(20) | open, in_progress, resolved, closed |
| related_sales_order_id | BIGINT NULL FK→sales_orders | — |
| assigned_to | BIGINT NULL FK→users | — |
| created_at, updated_at | TIMESTAMP | — |

### 3.25 gallery_albums
| Kolom | Tipe | Catatan |
|-------|------|--------|
| id | BIGINT PK | — |
| slug | VARCHAR(255) UNIQUE | — |
| name | VARCHAR(255) | — |
| description | TEXT NULL | — |
| cover_image | VARCHAR(255) NULL | — |
| sort_order | INT | — |
| created_at, updated_at | TIMESTAMP | — |

### 3.26 gallery_items
| Kolom | Tipe | Catatan |
|-------|------|--------|
| id | BIGINT PK | — |
| album_id | BIGINT FK→gallery_albums | — |
| path | VARCHAR(255) | — |
| alt | VARCHAR(255) NULL | — |
| caption | VARCHAR(255) NULL | — |
| sort_order | INT | — |
| created_at, updated_at | TIMESTAMP | — |

### 3.27 banners
| Kolom | Tipe | Catatan |
|-------|------|--------|
| id | BIGINT PK | — |
| title | VARCHAR(255) | — |
| subtitle | TEXT NULL | — |
| image | VARCHAR(255) | — |
| link | VARCHAR(255) NULL | — |
| cta_text | VARCHAR(100) NULL | — |
| sort_order | INT | — |
| is_active | BOOLEAN | — |
| created_at, updated_at | TIMESTAMP | — |

### 3.28 blog_categories
| Kolom | Tipe | Catatan |
|-------|------|--------|
| id | BIGINT PK | — |
| slug | VARCHAR(255) UNIQUE | — |
| name | VARCHAR(255) | — |
| description | TEXT NULL | — |
| created_at, updated_at | TIMESTAMP | — |

### 3.29 blog_tags
| Kolom | Tipe | Catatan |
|-------|------|--------|
| id | BIGINT PK | — |
| slug | VARCHAR(255) UNIQUE | — |
| name | VARCHAR(255) | — |
| created_at, updated_at | TIMESTAMP | — |

### 3.30 blog_posts
| Kolom | Tipe | Catatan |
|-------|------|--------|
| id | BIGINT PK | — |
| blog_category_id | BIGINT FK→blog_categories | — |
| slug | VARCHAR(255) UNIQUE | — |
| title | VARCHAR(255) | — |
| excerpt | TEXT NULL | — |
| content | LONGTEXT | — |
| featured_image | VARCHAR(255) NULL | — |
| author_id | BIGINT FK→users | — |
| status | VARCHAR(20) | draft, published, archived |
| published_at | TIMESTAMP NULL | — |
| reading_time | INT NULL | minutes |
| seo_title | VARCHAR(255) NULL | — |
| seo_description | TEXT NULL | — |
| created_at, updated_at | TIMESTAMP | — |

Index: `(status)`, `(published_at)`, `(blog_category_id)`

### 3.31 blog_post_tags
| Kolom | Tipe | Catatan |
|-------|------|--------|
| blog_post_id | BIGINT FK→blog_posts ON DELETE CASCADE | — |
| blog_tag_id | BIGINT FK→blog_tags ON DELETE CASCADE | — |

PK composite: `(blog_post_id, blog_tag_id)`

### 3.32 employees
| Kolom | Tipe | Catatan |
|-------|------|--------|
| id | BIGINT PK | — |
| name | VARCHAR(255) | — |
| department_id | BIGINT NULL FK→departments | — |
| position | VARCHAR(255) NULL | — |
| email | VARCHAR(255) NULL | — |
| phone | VARCHAR(50) NULL | — |
| hire_date | DATE NULL | — |
| salary | DECIMAL(15,2) NULL | — |
| status | VARCHAR(20) | active, inactive, terminated |
| created_at, updated_at | TIMESTAMP | — |

### 3.33 departments
| Kolom | Tipe | Catatan |
|-------|------|--------|
| id | BIGINT PK | — |
| name | VARCHAR(255) | — |
| head_id | BIGINT NULL FK→employees | — |
| created_at, updated_at | TIMESTAMP | — |

### 3.34 company_contacts
| Kolom | Tipe | Catatan |
|-------|------|--------|
| id | BIGINT PK | — |
| type | VARCHAR(20) | supplier, vendor, service, government |
| name | VARCHAR(255) | — |
| contact_person | VARCHAR(255) NULL | — |
| email | VARCHAR(255) NULL | — |
| phone | VARCHAR(50) NULL | — |
| address | TEXT NULL | — |
| notes | TEXT NULL | — |
| created_at, updated_at | TIMESTAMP | — |

### 3.35 agreements
| Kolom | Tipe | Catatan |
|-------|------|--------|
| id | BIGINT PK | — |
| company_contact_id | BIGINT FK→company_contacts | — |
| title | VARCHAR(255) | — |
| type | VARCHAR(20) | contract, mou, partnership |
| start_date | DATE | — |
| end_date | DATE NULL | — |
| file_path | VARCHAR(255) NULL | — |
| notes | TEXT NULL | — |
| created_at, updated_at | TIMESTAMP | — |

### 3.36 — Accounting (Priority D)
Tabel: `chart_of_accounts`, `journal_entries`, `journal_items`, `bank_accounts`, `bank_reconciliations`. Detail di Phase 6.

### 3.37 — Documents (Priority C)
Tabel: `documents`, `document_templates`. Detail di Phase 5.

## 4. Tabel — LeadGym (Multi-tenant)

Semua tabel LeadGym punya `tenant_id` + global scope.

### 4.1 tenants
| Kolom | Tipe | Catatan |
|-------|------|--------|
| id | BIGINT PK | — |
| uuid | CHAR(36) UNIQUE | — |
| name | VARCHAR(255) | nama gym |
| slug | VARCHAR(255) UNIQUE | subdomain identifier |
| domain | VARCHAR(255) NULL | custom domain (future) |
| logo | VARCHAR(255) NULL | — |
| address | TEXT NULL | — |
| phone | VARCHAR(50) NULL | — |
| email | VARCHAR(255) NULL | — |
| primary_color | VARCHAR(20) NULL | override (within range) |
| capacity | INT DEFAULT 50 | max concurrent |
| operating_hours | JSON | {mon: {open, close}, ...} |
| license_id | BIGINT NULL FK→licenses | — |
| license_key | VARCHAR(100) NULL | — |
| license_status | VARCHAR(20) | active, expired, suspended, inactive |
| license_expires_at | DATE NULL | — |
| status | VARCHAR(20) | active, suspended, terminated |
| created_at, updated_at | TIMESTAMP | — |

Index: `(slug)`, `(license_key)`

### 4.2 gym_users
Auth terpisah per-tenant (gym owner, staff, trainer).

| Kolom | Tipe | Catatan |
|-------|------|--------|
| id | BIGINT PK | — |
| tenant_id | BIGINT FK→tenants ON DELETE CASCADE | — |
| name | VARCHAR(255) | — |
| email | VARCHAR(255) | — |
| password | VARCHAR(255) | — |
| role | VARCHAR(20) | gym_owner, gym_staff, gym_trainer |
| is_active | BOOLEAN | — |
| created_at, updated_at | TIMESTAMP | — |

UNIQUE: `(tenant_id, email)`

### 4.3 members
| Kolom | Tipe | Catatan |
|-------|------|--------|
| id | BIGINT PK | — |
| tenant_id | BIGINT FK→tenants | — |
| member_code | VARCHAR(50) | per-tenant unique |
| name | VARCHAR(255) | — |
| photo | VARCHAR(255) NULL | — |
| gender | VARCHAR(10) NULL | male, female |
| dob | DATE NULL | — |
| phone | VARCHAR(50) NULL | — |
| email | VARCHAR(255) NULL | — |
| address | TEXT NULL | — |
| emergency_contact | VARCHAR(255) NULL | — |
| emergency_phone | VARCHAR(50) NULL | — |
| qr_code | VARCHAR(255) NULL | generated |
| status | VARCHAR(20) | active, inactive, blacklisted |
| joined_at | DATE | — |
| created_at, updated_at | TIMESTAMP | — |

UNIQUE: `(tenant_id, member_code)`

Index: `(tenant_id)`, `(tenant_id, status)`

### 4.4 plans
| Kolom | Tipe | Catatan |
|-------|------|--------|
| id | BIGINT PK | — |
| tenant_id | BIGINT FK→tenants | — |
| name | VARCHAR(255) | — |
| type | VARCHAR(20) | membership, pt_sessions, add_on |
| duration_days | INT | — |
| price | DECIMAL(15,2) | — |
| features | JSON | array of strings |
| is_active | BOOLEAN | — |
| created_at, updated_at | TIMESTAMP | — |

Index: `(tenant_id)`

### 4.5 subscriptions
| Kolom | Tipe | Catatan |
|-------|------|--------|
| id | BIGINT PK | — |
| tenant_id | BIGINT FK→tenants | — |
| member_id | BIGINT FK→members | — |
| plan_id | BIGINT FK→plans | — |
| start_date | DATE | — |
| end_date | DATE | — |
| status | VARCHAR(20) | active, expired, frozen, transferred, cancelled |
| frozen_at | DATE NULL | — |
| frozen_days | INT DEFAULT 0 | — |
| transferred_to | BIGINT NULL FK→members | — |
| amount | DECIMAL(15,2) | — |
| created_at, updated_at | TIMESTAMP | — |

Index: `(tenant_id)`, `(member_id)`, `(status)`, `(end_date)`

### 4.6 services
| Kolom | Tipe | Catatan |
|-------|------|--------|
| id | BIGINT PK | — |
| tenant_id | BIGINT FK→tenants | — |
| name | VARCHAR(255) | — |
| type | VARCHAR(20) | pt_session, class, locker, towel, other |
| price | DECIMAL(15,2) | — |
| duration_minutes | INT NULL | — |
| is_active | BOOLEAN | — |
| created_at, updated_at | TIMESTAMP | — |

### 4.7 member_services
Service yang dibeli/digunakan member (e.g. PT sessions).

| Kolom | Tipe | Catatan |
|-------|------|--------|
| id | BIGINT PK | — |
| tenant_id | BIGINT FK→tenants | — |
| member_id | BIGINT FK→members | — |
| service_id | BIGINT FK→services | — |
| total_sessions | INT | — |
| used_sessions | INT DEFAULT 0 | — |
| status | VARCHAR(20) | active, completed, expired |
| created_at, updated_at | TIMESTAMP | — |

### 4.8 invoices
| Kolom | Tipe | Catatan |
|-------|------|--------|
| id | BIGINT PK | — |
| tenant_id | BIGINT FK→tenants | — |
| invoice_number | VARCHAR(50) | per-tenant unique |
| member_id | BIGINT NULL FK→members | — |
| subscription_id | BIGINT NULL FK→subscriptions | — |
| issue_date | DATE | — |
| due_date | DATE NULL | — |
| subtotal | DECIMAL(15,2) | — |
| discount | DECIMAL(15,2) DEFAULT 0 | — |
| tax | DECIMAL(15,2) DEFAULT 0 | — |
| total | DECIMAL(15,2) | — |
| paid_amount | DECIMAL(15,2) DEFAULT 0 | — |
| status | VARCHAR(20) | unpaid, partial, paid, cancelled |
| notes | TEXT NULL | — |
| created_by | BIGINT FK→gym_users | — |
| created_at, updated_at | TIMESTAMP | — |

UNIQUE: `(tenant_id, invoice_number)`

Index: `(tenant_id)`, `(member_id)`, `(status)`

### 4.9 invoice_items
| Kolom | Tipe | Catatan |
|-------|------|--------|
| id | BIGINT PK | — |
| invoice_id | BIGINT FK→invoices ON DELETE CASCADE | — |
| description | VARCHAR(255) | — |
| type | VARCHAR(20) | subscription, service, add_on, penalty |
| reference_id | BIGINT NULL | subscription_id / service_id |
| quantity | INT DEFAULT 1 | — |
| unit_price | DECIMAL(15,2) | — |
| total | DECIMAL(15,2) | — |

### 4.10 invoice_transactions
Pembayaran yang masuk untuk invoice.

| Kolom | Tipe | Catatan |
|-------|------|--------|
| id | BIGINT PK | — |
| invoice_id | BIGINT FK→invoices | — |
| amount | DECIMAL(15,2) | — |
| payment_method | VARCHAR(20) | cash, transfer, card, qris, other |
| payment_date | DATE | — |
| reference | VARCHAR(255) NULL | — |
| created_by | BIGINT FK→gym_users | — |
| created_at, updated_at | TIMESTAMP | — |

### 4.11 enquiries
Lead gym (walk-in, online).

| Kolom | Tipe | Catatan |
|-------|------|--------|
| id | BIGINT PK | — |
| tenant_id | BIGINT FK→tenants | — |
| name | VARCHAR(255) | — |
| phone | VARCHAR(50) | — |
| email | VARCHAR(255) NULL | — |
| source | VARCHAR(50) | walk_in, online, referral, social |
| interest | VARCHAR(255) NULL | plan yang diminati |
| status | VARCHAR(20) | new, contacted, converted, lost |
| converted_member_id | BIGINT NULL FK→members | — |
| notes | TEXT NULL | — |
| created_at, updated_at | TIMESTAMP | — |

### 4.12 follow_ups
| Kolom | Tipe | Catatan |
|-------|------|--------|
| id | BIGINT PK | — |
| tenant_id | BIGINT FK→tenants | — |
| enquiry_id | BIGINT FK→enquiries ON DELETE CASCADE | — |
| follow_up_date | DATE | — |
| note | TEXT | — |
| next_action | VARCHAR(255) NULL | — |
| status | VARCHAR(20) | pending, done |
| created_by | BIGINT FK→gym_users | — |
| created_at, updated_at | TIMESTAMP | — |

### 4.13 expenses
| Kolom | Tipe | Catatan |
|-------|------|--------|
| id | BIGINT PK | — |
| tenant_id | BIGINT FK→tenants | — |
| expense_date | DATE | — |
| category | VARCHAR(100) | — |
| amount | DECIMAL(15,2) | — |
| description | TEXT NULL | — |
| created_by | BIGINT FK→gym_users | — |
| created_at, updated_at | TIMESTAMP | — |

Index: `(tenant_id)`, `(expense_date)`

### 4.14 check_ins
| Kolom | Tipe | Catatan |
|-------|------|--------|
| id | BIGINT PK | — |
| tenant_id | BIGINT FK→tenants | — |
| member_id | BIGINT FK→members | — |
| subscription_id | BIGINT NULL FK→subscriptions | — |
| check_in_at | TIMESTAMP | — |
| check_out_at | TIMESTAMP NULL | — |
| created_by | BIGINT FK→gym_users | — |

Index: `(tenant_id)`, `(member_id)`, `(check_in_at)`

## 5. Tabel — License Manager (LeaderP)

### 5.1 license_customers
| Kolom | Tipe | Catatan |
|-------|------|--------|
| id | BIGINT PK | — |
| company_name | VARCHAR(255) | — |
| contact_person | VARCHAR(255) | — |
| email | VARCHAR(255) | — |
| phone | VARCHAR(50) | — |
| address | TEXT NULL | — |
| tenant_id | BIGINT NULL FK→tenants | — |
| created_at, updated_at | TIMESTAMP | — |

### 5.2 license_plans
| Kolom | Tipe | Catatan |
|-------|------|--------|
| id | BIGINT PK | — |
| name | VARCHAR(100) | basic, pro, enterprise |
| tier | VARCHAR(20) | — |
| price_monthly | DECIMAL(15,2) | — |
| price_yearly | DECIMAL(15,2) | — |
| features | JSON | feature flags |
| max_members | INT NULL | NULL = unlimited |
| max_staff | INT NULL | — |
| is_active | BOOLEAN | — |

### 5.3 licenses
| Kolom | Tipe | Catatan |
|-------|------|--------|
| id | BIGINT PK | — |
| license_key | VARCHAR(100) UNIQUE | generated |
| customer_id | BIGINT FK→license_customers | — |
| plan_id | BIGINT FK→license_plans | — |
| tenant_id | BIGINT NULL FK→tenants | — |
| billing_cycle | VARCHAR(20) | monthly, yearly |
| started_at | DATE | — |
| expires_at | DATE | — |
| status | VARCHAR(20) | active, suspended, expired, cancelled |
| suspended_reason | TEXT NULL | — |
| created_by | BIGINT FK→users | — |
| created_at, updated_at | TIMESTAMP | — |

Index: `(license_key)`, `(status)`, `(expires_at)`

### 5.4 license_activations
| Kolom | Tipe | Catatan |
|-------|------|--------|
| id | BIGINT PK | — |
| license_id | BIGINT FK→licenses | — |
| tenant_id | BIGINT FK→tenants | — |
| activated_at | TIMESTAMP | — |
| deactivated_at | TIMESTAMP NULL | — |
| ip_address | VARCHAR(45) | — |
| created_at | TIMESTAMP | — |

### 5.5 license_validations
Log setiap LeadGym validate license (untuk audit).

| Kolom | Tipe | Catatan |
|-------|------|--------|
| id | BIGINT PK | — |
| license_id | BIGINT FK→licenses | — |
| tenant_id | BIGINT FK→tenants | — |
| valid | BOOLEAN | — |
| status | VARCHAR(20) | active, expired, suspended |
| validated_at | TIMESTAMP | — |
| ip_address | VARCHAR(45) | — |

Index: `(license_id)`, `(validated_at)`

## 6. Indexing Strategy

### 6.1 Hot Queries & Index
| Query | Index |
|-------|-------|
| Product listing by category+status | composite `(category_id, status)` |
| Product search by name | FULLTEXT `name` (atau LIKE + index) |
| Inquiry by status+date | composite `(status, created_at)` |
| SO by status+date | composite `(status, order_date)` |
| Member by tenant+status | composite `(tenant_id, status)` |
| Subscription by tenant+end_date | composite `(tenant_id, end_date)` |
| License by key | UNIQUE `license_key` |
| License expiring soon | `(status, expires_at)` |

### 6.2 Foreign Keys
Semua FK dengan `ON DELETE CASCADE` untuk child (items, images) atau
`ON DELETE RESTRICT` untuk referensi kuat (product → category).

### 6.3 Cache Tables
Laravel cache (file/database/redis). Jika database, pakai tabel `cache`:
| Kolom | Tipe |
|-------|------|
| key | VARCHAR(255) PK |
| value | LONGTEXT |
| expiration | INT |

## 7. Seed Data

- 1 super_admin, 1 per role lain
- 10 categories, 5 brands, 30 products (dengan images & specs)
- 3 warehouses, 5 suppliers
- 2 PO (1 received, 1 draft)
- 5 inquiries (various status)
- 3 SO (various status)
- 2 gallery albums, 10 items
- 5 banners
- 5 blog categories, 10 tags, 10 posts
- 2 tenants (LeadGym demo), 20 members, 5 plans, 10 subscriptions, 5 invoices
- 2 license customers, 2 licenses (1 active, 1 expired)