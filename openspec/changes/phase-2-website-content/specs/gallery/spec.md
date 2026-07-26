# Spec: gallery

## Requirements

### REQ-001: Gallery Page
`GET /gallery` MUST display all gallery items in a masonry layout (CSS columns, 3 columns desktop, 1 mobile).

### REQ-002: Album Filter
Album filter pills MUST be displayed at top. "All" + each album name. Clicking filters items client-side (no reload). Active album highlighted.

### REQ-003: Lightbox
Clicking an image MUST open GLightbox lightbox with: zoom, prev/next navigation, caption (from item caption), keyboard navigation, touch swipe.

### REQ-004: Lazy Load
All gallery images MUST have `loading="lazy"` attribute.

### REQ-005: Image Format
Images MUST be served in WebP format with JPG fallback. Responsive srcset for multiple widths.