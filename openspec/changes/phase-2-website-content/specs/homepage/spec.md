# Spec: homepage

## Requirements

### REQ-001: Hero Banner
Homepage MUST display a Swiper carousel with active banners. Each slide shows: background image, dark gradient overlay, display headline (48px weight 700), subtitle, CTA button. Autoplay every 6 seconds with pagination dots.

### REQ-002: Featured Products
Homepage MUST display up to 8 featured products (`is_featured = true`, `status = published`) in a grid (4 columns desktop, 2 mobile).

### REQ-003: Shop by Category
Homepage MUST display featured categories (`is_featured = true`) in a grid with image + name. Click navigates to `/products?category={slug}`.

### REQ-004: CTA Section
Homepage MUST display a CTA section with WhatsApp link from settings (`whatsapp` key, `contact` group).

### REQ-005: Caching
Homepage data MUST be cached for 60 minutes. Cache MUST be invalidated when products or banners are updated.

### REQ-006: Load Time
Homepage MUST load in under 3 seconds on a 3G connection.