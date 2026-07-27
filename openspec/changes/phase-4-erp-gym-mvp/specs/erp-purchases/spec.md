## ADDED Requirements

### Requirement: Purchase Orders Table
A `purchase_orders` table SHALL exist with: id, po_number (UNIQUE), supplier_id (FK restrict), warehouse_id (FK restrict), order_date, expected_date (nullable), received_date (nullable), currency (default USD), subtotal_usd, shipping_usd, duty_usd, total_usd, exchange_rate (default 15800), total_idr, status (default draft), notes (nullable), created_by (FK users restrict), timestamps.

#### Scenario: PO is created with USD currency
- **WHEN** admin creates a purchase order
- **THEN** the currency MUST default to USD and exchange_rate MUST default to 15800

### Requirement: Receive Goods Action
The PurchaseOrderResource table SHALL have a "Receive" action (visible when status is sent or partial) that in a DB transaction: updates PO status to received, increments stock levels, creates stock movements (type in), calculates landed cost.

#### Scenario: Receiving goods increases stock
- **WHEN** admin receives goods for a PO with 10 units of a product
- **THEN** the product's stock level in the specified warehouse MUST increase by 10 and a stock movement (type in) MUST be created

### Requirement: Landed Cost Calculation
The `total_idr` (landed cost) SHALL be calculated as `(subtotal_usd + shipping_usd + duty_usd) * exchange_rate` during the Receive Goods action.

#### Scenario: Landed cost is calculated correctly
- **WHEN** a PO with subtotal_usd=1000, shipping_usd=100, duty_usd=50 is received with exchange_rate=15800
- **THEN** total_idr MUST be (1000+100+50) * 15800 = 18,170,000
