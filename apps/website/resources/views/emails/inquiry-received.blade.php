<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Inter', Arial, sans-serif; line-height: 1.6; color: #1a1a2e; }
        .container { max-width: 600px; margin: 0 auto; padding: 24px; }
        .header { border-bottom: 2px solid #dc2626; padding-bottom: 16px; margin-bottom: 24px; }
        .header h1 { font-size: 20px; font-weight: 700; color: #1a1a2e; margin: 0; }
        .header p { font-size: 14px; color: #64748b; margin: 4px 0 0 0; }
        .section { margin-bottom: 24px; }
        .section h2 { font-size: 14px; font-weight: 600; color: #dc2626; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 8px 0; }
        .info-row { display: flex; padding: 6px 0; border-bottom: 1px solid #e2e8f0; font-size: 14px; }
        .info-label { width: 120px; color: #64748b; font-weight: 500; }
        .info-value { color: #1a1a2e; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        th { text-align: left; padding: 8px 12px; background: #f8fafc; border-bottom: 2px solid #e2e8f0; color: #64748b; font-weight: 600; font-size: 12px; text-transform: uppercase; }
        td { padding: 8px 12px; border-bottom: 1px solid #e2e8f0; }
        .footer { margin-top: 32px; padding-top: 16px; border-top: 1px solid #e2e8f0; font-size: 12px; color: #94a3b8; text-align: center; }
        .badge { display: inline-block; padding: 2px 10px; font-size: 12px; font-weight: 600; border-radius: 999px; background: #dbeafe; color: #2563eb; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Inquiry Baru</h1>
            <p>Seorang pelanggan telah mengirimkan inquiry melalui website.</p>
        </div>

        <div class="section">
            <h2>Informasi Inquiry</h2>
            <div class="info-row"><span class="info-label">Nomor</span><span class="info-value">{{ $inquiry->inquiry_number }}</span></div>
            <div class="info-row"><span class="info-label">Status</span><span class="info-value"><span class="badge">New</span></span></div>
            <div class="info-row"><span class="info-label">Tanggal</span><span class="info-value">{{ $inquiry->created_at->format('d F Y H:i') }}</span></div>
            <div class="info-row"><span class="info-label">IP Address</span><span class="info-value">{{ $inquiry->ip_address ?? '-' }}</span></div>
        </div>

        <div class="section">
            <h2>Data Pelanggan</h2>
            <div class="info-row"><span class="info-label">Nama</span><span class="info-value">{{ $inquiry->customer_name }}</span></div>
            <div class="info-row"><span class="info-label">WhatsApp</span><span class="info-value">{{ $inquiry->customer_phone }}</span></div>
            @if($inquiry->customer_email)
                <div class="info-row"><span class="info-label">Email</span><span class="info-value">{{ $inquiry->customer_email }}</span></div>
            @endif
            @if($inquiry->message)
                <div class="info-row"><span class="info-label">Pesan</span><span class="info-value">{{ $inquiry->message }}</span></div>
            @endif
        </div>

        <div class="section">
            <h2>Produk yang Diminati</h2>
            <table>
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>SKU</th>
                        <th>Qty</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inquiry->items as $item)
                        <tr>
                            <td>{{ $item->product?->name ?? 'Product #' . $item->product_id }}</td>
                            <td>{{ $item->product?->sku ?? '-' }}</td>
                            <td>{{ $item->quantity }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" style="color: #94a3b8;">Tidak ada item</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="footer">
            <p>LeaderHub &mdash; Premium Fitness Equipment Distributor</p>
            <p>Email ini dikirim otomatis oleh sistem. Silakan login ke panel admin untuk merespon inquiry.</p>
        </div>
    </div>
</body>
</html>
