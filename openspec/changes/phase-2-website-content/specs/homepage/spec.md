## ADDED Requirements

### Requirement: Hero Banner
Homepage SHALL display a Swiper carousel with active banners. Each slide SHALL show: background image, dark gradient overlay, display headline (48px weight 700), subtitle, and CTA button. Autoplay SHALL run every 6 seconds with pagination dots.

#### Scenario: Hero carousel autoplays
- **WHEN** user loads the homepage
- **THEN** the hero carousel MUST auto-advance slides every 6 seconds

### Requirement: Featured Products
Homepage SHALL display up to 8 featured products (`is_featured = true`, `status = published`) in a grid (4 columns desktop, 2 mobile).

#### Scenario: Featured products are displayed
- **WHEN** user loads the homepage and there are 8 featured products
- **THEN** the homepage MUST display 8 product cards in a 4-column grid

### Requirement: Shop by Category
Homepage SHALL display featured categories (`is_featured = true`) in a grid with image and name. Click SHALL navigate to `/products?category={slug}`.

#### Scenario: Category click navigates to filtered products
- **WHEN** user clicks a category card on the homepage
- **THEN** the browser MUST navigate to `/products?category={category-slug}`

### Requirement: CTA Section
Homepage SHALL display a CTA section with a WhatsApp link from settings (`whatsapp` key, `contact` group).

#### Scenario: CTA links to WhatsApp
- **WHEN** user clicks the CTA button
- **THEN** the browser MUST open `https://wa.me/{whatsapp_number}` where the number comes from settings

### Requirement: Caching
Homepage data SHALL be cached for 60 minutes. Cache SHALL be invalidated when products or banners are updated.

#### Scenario: Cache is used on second load
- **WHEN** user loads the homepage twice within 60 minutes
- **THEN** the second load MUST use cached data (no database queries for banners/products)

### Requirement: Load Time
Homepage SHALL load in under 3 seconds on a 3G connection.

#### Scenario: Homepage loads quickly
- **WHEN** user loads the homepage on a 3G connection
- **THEN** the page MUST fully load in under 3 seconds
