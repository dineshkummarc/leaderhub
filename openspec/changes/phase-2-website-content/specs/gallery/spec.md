## ADDED Requirements

### Requirement: Gallery Page
`GET /gallery` SHALL display all gallery items in a masonry layout (CSS columns, 3 columns desktop, 1 mobile).

#### Scenario: Gallery displays masonry layout
- **WHEN** user navigates to `/gallery`
- **THEN** images MUST be displayed in a masonry layout using CSS columns

### Requirement: Album Filter
Album filter pills SHALL be displayed at top. "All" plus each album name. Clicking SHALL filter items client-side (no reload). Active album SHALL be highlighted.

#### Scenario: Album filter works without page reload
- **WHEN** user clicks an album filter pill
- **THEN** the gallery MUST filter to show only items from that album without a page reload

### Requirement: Lightbox
Clicking an image SHALL open GLightbox lightbox with: zoom, prev/next navigation, caption, keyboard navigation, touch swipe.

#### Scenario: Lightbox opens on image click
- **WHEN** user clicks a gallery image
- **THEN** a lightbox MUST open showing the full-size image with navigation controls

### Requirement: Lazy Load
All gallery images SHALL have `loading="lazy"` attribute.

#### Scenario: Images are lazy loaded
- **WHEN** developer inspects gallery image HTML
- **THEN** each `<img>` tag MUST have `loading="lazy"` attribute
