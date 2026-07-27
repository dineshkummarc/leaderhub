## ADDED Requirements

### Requirement: InquiryResource Exists
An `InquiryResource` SHALL exist in the website admin panel using `Leaderhub\Core\Models\Inquiry`, in the "Website" navigation group.

#### Scenario: Inquiry list is accessible
- **WHEN** admin navigates to `/admin/inquiries`
- **THEN** the inquiry list MUST be displayed

### Requirement: Table Columns
The InquiryResource table SHALL display: inquiry_number (searchable), customer_name (searchable), customer_phone, status badge (new=info, contacted=warning, converted=success, closed=gray, lost=danger), created_at.

#### Scenario: Inquiry status has colored badge
- **WHEN** admin views the inquiry list
- **THEN** each inquiry's status MUST be displayed as a colored badge

### Requirement: Form Fields
The edit form SHALL display: inquiry_number (disabled), customer_name (disabled), customer_phone (disabled), customer_email (disabled), message (disabled), status (select: editable), admin_notes (textarea: editable). Customer fields MUST be read-only.

#### Scenario: Admin can change status but not customer data
- **WHEN** admin edits an inquiry
- **THEN** customer fields MUST be disabled and status/admin_notes MUST be editable

### Requirement: WhatsApp Action
The table SHALL have a "WA" action button that opens `https://wa.me/{sanitized_phone}` in a new tab (phone digits only).

#### Scenario: WA action opens WhatsApp
- **WHEN** admin clicks the WA action on an inquiry with phone "0812-3456-7890"
- **THEN** a new tab MUST open to `https://wa.me/081234567890`

### Requirement: No Create Page
The InquiryResource SHALL NOT have a create page. Inquiries are only created via the public website form.

#### Scenario: No create inquiry page
- **WHEN** admin navigates to `/admin/inquiries/create`
- **THEN** a 404 or redirect MUST occur (create page does not exist)

### Requirement: Inquiry Appears After Submit
When a visitor submits an inquiry via the public form, the inquiry SHALL immediately appear in the admin panel with status `new`.

#### Scenario: Submitted inquiry appears in admin
- **WHEN** a visitor submits an inquiry on the website
- **THEN** the inquiry MUST appear in `/admin/inquiries` with status `new`
