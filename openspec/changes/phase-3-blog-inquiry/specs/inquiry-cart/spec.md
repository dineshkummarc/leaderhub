## ADDED Requirements

### Requirement: localStorage Key
The inquiry cart SHALL be stored in browser localStorage under key `leaderhub_inquiry` with a JSON structure containing `items` array (each with product_id, name, slug, qty, added_at) and `updated_at`.

#### Scenario: Cart persists across page reloads
- **WHEN** user adds a product to the cart and refreshes the page
- **THEN** the cart MUST still contain the added product (localStorage persists)

### Requirement: Add Item Function
A global function `window.addToInquiry(productId)` SHALL be available. It SHALL read product name and slug from DOM `data-product-id` element, call `addItem()`, and show user feedback.

#### Scenario: Add to inquiry from product detail
- **WHEN** user clicks "Tambah ke Daftar Minat" on a product detail page
- **THEN** the product MUST be added to the localStorage cart and user MUST see a confirmation feedback

### Requirement: Add Item Logic
`addItem` SHALL: if item with same product_id exists, increment qty; otherwise push new item. Then save cart and call `updateBar()`.

#### Scenario: Adding same product increments quantity
- **WHEN** user adds a product that is already in the cart
- **THEN** the item's quantity MUST be incremented by 1 (not duplicated)

### Requirement: Remove and Update Functions
`removeItem(productId)` SHALL filter out the item. `updateQty(productId, qty)` SHALL set qty to `Math.max(1, qty)`. Both SHALL save cart and call `updateBar()`.

#### Scenario: Remove item removes from cart
- **WHEN** user removes a product from the cart
- **THEN** the product MUST no longer be in the localStorage cart and the inquiry bar MUST update

### Requirement: Floating Inquiry Bar
A floating bar with `id="inquiry-bar"` SHALL exist in the layout. It SHALL be fixed at bottom, z-index 300, hidden by default, show item count with `id="inquiry-bar-count"`, and a "Lihat Daftar" button linking to `/inquiry`.

#### Scenario: Bar appears when cart has items
- **WHEN** the cart has at least 1 item
- **THEN** the inquiry bar MUST be visible (hidden class removed)

#### Scenario: Bar hides when cart is empty
- **WHEN** all items are removed from the cart
- **THEN** the inquiry bar MUST be hidden

### Requirement: Max Items Limit
The cart SHALL enforce a maximum of 50 items. Adding beyond 50 SHALL be rejected with a user-facing message.

#### Scenario: 51st item is rejected
- **WHEN** user attempts to add a 51st distinct product to the cart
- **THEN** the add MUST be rejected with message "Maksimal 50 produk per inquiry"

### Requirement: Product Card Data Attributes
Product cards SHALL include `data-product-id`, `data-name`, and `data-slug` attributes on the root element for `addToInquiry()` to read.

#### Scenario: Product card has data attributes
- **WHEN** developer inspects a product card HTML
- **THEN** the root element MUST have `data-product-id`, `data-name`, and `data-slug` attributes

### Requirement: Global Function Scope
`window.addToInquiry` SHALL be a global function (attached to `window`), not module-scoped, because it is called from `onclick` in Blade templates.

#### Scenario: addToInquiry is callable from HTML onclick
- **WHEN** a product detail page has `onclick="addToInquiry(1)"`
- **THEN** the function MUST execute without "addToInquiry is not defined" error
