## ADDED Requirements

### Requirement: Suppliers Table
A `suppliers` table SHALL exist with: id, name, contact_person (nullable), email (nullable), phone (nullable), address (nullable), country (default China), currency (default USD), timestamps.

#### Scenario: Supplier is created
- **WHEN** admin creates a supplier with name and currency USD
- **THEN** the supplier MUST be saved with country defaulting to China and currency defaulting to USD

### Requirement: ProductResource with Stock Levels
A `ProductResource` SHALL exist in the hub panel with a `StockLevelsRelationManager` showing stock per warehouse.

#### Scenario: Admin can set stock per warehouse
- **WHEN** admin edits a product and adds stock levels for 2 warehouses
- **THEN** the stock levels MUST be saved and visible in the relation manager

### Requirement: Warehouse and StockLevel Resources
`WarehouseResource` and `StockLevelResource` SHALL exist with CRUD. Unique constraint on `(product_id, warehouse_id)` for stock levels.

#### Scenario: Duplicate stock level is rejected
- **WHEN** admin attempts to create a stock level for a product-warehouse combination that already exists
- **THEN** the database MUST reject the duplicate

### Requirement: Low Stock Visibility
The ProductResource table SHALL show an indicator when total stock is below low_stock_threshold. Dashboard widget SHALL count "Low Stock Products".

#### Scenario: Low stock product is flagged
- **WHEN** a product's total stock is 3 and low_stock_threshold is 5
- **THEN** the product MUST be flagged as low stock in the table and counted in the dashboard widget
