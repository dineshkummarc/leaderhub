<?php

namespace App\Http\Controllers;

use App\Mail\InquiryReceived;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Leaderhub\Core\Models\Setting;
use Leaderhub\Core\Services\InquiryService;

class InquiryController extends Controller
{
    public function index()
    {
        return view('inquiry.index');
    }

    public function store(Request $request)
    {
        // 1. Rate limit: 5 inquiry per IP per hour
        $key = 'inquiry:'.$request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return back()->with('error', 'Terlalu banyak inquiry. Coba lagi nanti.');
        }
        RateLimiter::hit($key, 3600);

        // 2. Honeypot: if "website" field is filled → bot → abort
        if ($request->filled('website')) {
            abort(422);
        }

        // 3. Validate
        $validated = $request->validate([
            'customer_name' => 'required|min:3',
            'customer_phone' => 'required|min:8',
            'customer_email' => 'nullable|email',
            'message' => 'nullable',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        // 4. Create inquiry via service
        $inquiry = InquiryService::create($validated, $validated['items']);

        // 5. Email ke admin
        try {
            $adminEmail = Setting::get('email', 'contact');
            if ($adminEmail) {
                Mail::to($adminEmail)->send(new InquiryReceived($inquiry));
            }
        } catch (\Exception $e) {
            // Log email error but don't block the user
            logger()->error('Failed to send inquiry email: '.$e->getMessage());
        }

        // 6. Channel: WA or email
        if ($request->input('channel') === 'wa') {
            $wa = Setting::get('whatsapp', 'contact');
            $phone = preg_replace('/[^0-9]/', '', $wa);
            $msg = urlencode(InquiryService::buildWaMessage($inquiry));

            return redirect("https://wa.me/{$phone}?text={$msg}");
        }

        // 7. Email channel → success page
        return view('inquiry.success', ['inquiry' => $inquiry]);
    }
}
