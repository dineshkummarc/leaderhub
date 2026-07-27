## ADDED Requirements

### Requirement: Sales Orders Table
A `sales_orders` table SHALL exist with: id, so_number (UNIQUE), inquiry_id (FK nullable, nullOnDelete), customer_name, customer_phone, customer_email (nullable), customer_address (nullable), order_date, delivery_date (nullable), subtotal_idr, discount_idr, tax_idr, total_idr, status (default draft), created_by (FK users), timestamps.

#### Scenario: SO is created from inquiry
- **WHEN** admin converts an inquiry to a sales order
- **THEN** the SO MUST have inquiry_id set and customer info copied from the inquiry

### Requirement: Convert from Inquiry Action
The SalesOrderResource SHALL have a "Convert Inquiry" action that creates an SO from inquiry items, sets inquiry status to converted, and redirects to SO edit page.

#### Scenario: Inquiry status changes to converted
- **WHEN** admin converts an inquiry to a sales order
- **THEN** the inquiry's status MUST be updated to `converted` and converted_sales_order_id MUST be set

### Requirement: Deliver Action
The SalesOrderResource SHALL have a "Deliver" action (visible when status=confirmed) that decrements stock, creates stock movements (type out), and updates SO status to delivered.

#### Scenario: Delivery decreases stock
- **WHEN** admin delivers a SO with 5 units of a product
- **THEN** the product's stock MUST decrease by 5 and a stock movement (type out) MUST be created

### Requirement: Inquiry FK Added
After sales_orders table is created, a migration SHALL add FK on `inquiries.converted_sales_order_id` to `sales_orders.id` with nullOnDelete.

#### Scenario: Inquiry FK is added in Phase 4
- **WHEN** developer runs migrations in Phase 4
- **THEN** the inquiries.converted_sales_order_id column MUST have a FK constraint to sales_orders.id
