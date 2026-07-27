## ADDED Requirements

### Requirement: Invoice Numbering Per-Tenant
The invoice_number SHALL be auto-generated in format `INV-YYYY-XXXXX` where XXXXX is sequential PER TENANT. Each tenant has an independent sequence.

#### Scenario: Invoice numbering is per-tenant
- **WHEN** Tenant A has 3 invoices and Tenant B has 1 invoice
- **THEN** Tenant A's next invoice MUST be INV-2026-00004 and Tenant B's next MUST be INV-2026-00002

### Requirement: Payment Recording
Recording a payment SHALL create an InvoiceTransaction, increment invoice.paid_amount, and auto-update status (paid if paid_amount >= total, partial if 0 < paid_amount < total).

#### Scenario: Full payment sets status to paid
- **WHEN** a payment is recorded for the full total amount
- **THEN** the invoice status MUST be updated to paid

### Requirement: Invoice PDF
The InvoiceResource SHALL have an action to generate a PDF with tenant branding, invoice number, items, totals, and status.

#### Scenario: PDF is generated with tenant name
- **WHEN** admin generates a PDF for an invoice
- **THEN** the PDF MUST include the tenant's name and logo
