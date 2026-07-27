## ADDED Requirements

### Requirement: Inquiries Table
An `inquiries` table SHALL exist with: `id`, `inquiry_number` (UNIQUE), `uuid` (CHAR 36 UNIQUE), `customer_name`, `customer_phone`, `customer_email` (nullable), `message` (nullable), `status` (default new), `admin_notes` (nullable), `converted_sales_order_id` (nullable BIGINT, NO FK in this phase), `ip_address` (nullable), `user_agent` (nullable), timestamps. Index on `(status, created_at)`.

#### Scenario: converted_sales_order_id has no FK in Phase 3
- **WHEN** developer inspects the inquiries table in Phase 3
- **THEN** the `converted_sales_order_id` column MUST NOT have a foreign key constraint (FK is added in Phase 4)

### Requirement: InquiryService Create
`InquiryService::create(array $data, array $items)` SHALL wrap the operation in a DB transaction, create an Inquiry with auto-generated inquiry_number and uuid, create InquiryItem records for each item, and return the Inquiry. If any step fails, the transaction SHALL rollback.

#### Scenario: Inquiry is created with items
- **WHEN** `InquiryService::create()` is called with customer data and 2 items
- **THEN** an Inquiry record and 2 InquiryItem records MUST be created in the database

### Requirement: Inquiry Number Format
`InquiryService::generateNumber()` SHALL return a string in format `INQ-YYYY-XXXXX` where XXXXX is zero-padded 5-digit sequential (max id + 1 for the current year).

#### Scenario: First inquiry of 2026
- **WHEN** no inquiries exist for year 2026
- **THEN** `generateNumber()` MUST return `INQ-2026-00001`

### Requirement: Form Validation
The `InquiryController@store` SHALL validate: customer_name (required, min 3), customer_phone (required, min 8), customer_email (nullable, email), items (required, array, min 1), items.*.product_id (required, exists), items.*.qty (required, integer, min 1).

#### Scenario: Validation rejects short name
- **WHEN** user submits the inquiry form with customer_name = "Jo"
- **THEN** the server MUST return a validation error for customer_name

### Requirement: Honeypot Anti-Spam
The inquiry form SHALL include a hidden honeypot field named `website`. If the `website` field is filled, the server SHALL abort with HTTP 422.

#### Scenario: Bot filling honeypot is rejected
- **WHEN** a request is submitted with the `website` field filled
- **THEN** the server MUST abort with HTTP 422

### Requirement: Rate Limiting
The `InquiryController@store` SHALL rate limit to 5 inquiries per IP per hour (3600 seconds). If exceeded, redirect back with error "Terlalu banyak inquiry. Coba lagi nanti."

#### Scenario: 6th inquiry in an hour is rejected
- **WHEN** user submits 6 inquiries from the same IP within 1 hour
- **THEN** the 6th submission MUST be rejected with the rate limit error message

### Requirement: WhatsApp Channel Submit
When submitted with `channel = wa`: the system SHALL save the inquiry, email the admin, and redirect to `https://wa.me/{whatsapp}?text={urlencoded message}`.

#### Scenario: WA submit redirects to wa.me
- **WHEN** user submits the inquiry form with channel = wa
- **THEN** the browser MUST redirect to a wa.me URL containing the inquiry details in the text parameter

### Requirement: Email Channel Submit
When submitted with `channel = email`: the system SHALL save the inquiry, email the admin, and display a success page with inquiry number.

#### Scenario: Email submit shows success page
- **WHEN** user submits the inquiry form with channel = email
- **THEN** a success page MUST be displayed showing the inquiry number

### Requirement: Mailable
An `InquiryReceived` mailable SHALL exist, accepting an Inquiry model, with subject "Inquiry Baru: {inquiry_number}", using view `emails/inquiry-received.blade.php`.

#### Scenario: Email is sent to admin
- **WHEN** an inquiry is submitted
- **THEN** an email MUST be sent to the admin email (from settings, group contact, key email)

### Requirement: Empty State
When cart is empty, the `/inquiry` page SHALL display an empty state message with a link to browse the catalog. The form SHALL NOT be displayed.

#### Scenario: Empty cart shows empty state
- **WHEN** user navigates to `/inquiry` with an empty cart
- **THEN** the page MUST show "Belum ada produk di daftar minat" with a link to `/products`
