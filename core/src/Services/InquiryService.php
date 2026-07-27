<?php

namespace Leaderhub\Core\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Leaderhub\Core\Models\Inquiry;
use Leaderhub\Core\Models\InquiryItem;

class InquiryService
{
    /**
     * Create inquiry dengan items dalam DB transaction.
     */
    public static function create(array $data, array $items): Inquiry
    {
        return DB::transaction(function () use ($data, $items) {
            $inquiry = Inquiry::create([
                'inquiry_number' => static::generateNumber(),
                'uuid' => Str::uuid(),
                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'],
                'customer_email' => $data['customer_email'] ?? null,
                'message' => $data['message'] ?? null,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'status' => 'new',
            ]);

            foreach ($items as $item) {
                InquiryItem::create([
                    'inquiry_id' => $inquiry->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['qty'],
                ]);
            }

            return $inquiry;
        });
    }

    /**
     * Generate inquiry number: INQ-YYYY-XXXXX (sequential per year).
     */
    public static function generateNumber(): string
    {
        $year = now()->year;
        $last = Inquiry::whereYear('created_at', $year)->max('id') ?? 0;

        return sprintf('INQ-%d-%05d', $year, $last + 1);
    }

    /**
     * Build WhatsApp message dari inquiry.
     */
    public static function buildWaMessage(Inquiry $inquiry): string
    {
        $lines = [
            'Halo LeaderHub, saya tertarik dengan produk berikut:',
            '',
            "Inquiry: {$inquiry->inquiry_number}",
        ];

        foreach ($inquiry->items as $i => $item) {
            $productName = $item->product?->name ?? 'Product #' . $item->product_id;
            $productSku = $item->product?->sku ?? '';
            $lines[] = ($i + 1) . ". {$productName} ({$productSku}) - {$item->quantity} unit";
        }

        $lines[] = '';
        $lines[] = "Nama: {$inquiry->customer_name}";

        if ($inquiry->message) {
            $lines[] = "Pesan: {$inquiry->message}";
        }

        return implode("\n", $lines);
    }
}
