<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use App\Models\SystemSetting;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PricingController extends Controller
{
    public function index()
    {
        $plans = SubscriptionPlan::with('features')
            ->where('is_active', true)
            ->where('is_visible', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return Inertia::render('Pricing', [
            'plans' => $plans,
            'paymentConfig' => [
                'enabled' => filter_var($this->getSettingValue('payment_gateway_enabled', '1'), FILTER_VALIDATE_BOOLEAN),
                'gateway' => strtolower($this->getSettingValue('payment_primary_gateway', 'razorpay')),
                'currency' => strtoupper($this->getSettingValue('payment_currency', 'INR')),
                'razorpayMode' => strtolower($this->getSettingValue('razorpay_mode', 'test')),
                'razorpayKeyId' => $this->getSettingValue('razorpay_key_id', ''),
            ],
        ]);
    }

    public function purchase(Request $request)
    {
        $data = $request->validate([
            'plan_id' => ['required', 'integer', 'exists:subscription_plans,id'],
        ]);

        $plan = SubscriptionPlan::where('is_active', true)
            ->where('is_visible', true)
            ->findOrFail($data['plan_id']);

        UserSubscription::create([
            'user_id' => $request->user()->id,
            'subscription_plan_id' => $plan->id,
            'amount' => $plan->price,
            'currency' => 'INR',
            'status' => 'pending',
            'purchased_at' => now(),
            'gateway' => 'manual_pending',
            'notes' => 'Payment gateway integration pending. Admin can verify and activate manually.',
        ]);

        return back()->with('status', 'Purchase request submitted. Our team will contact you for payment confirmation.');
    }

    public function createOrder(Request $request)
    {
        $data = $request->validate([
            'plan_id' => ['required', 'integer', 'exists:subscription_plans,id'],
        ]);

        $plan = SubscriptionPlan::where('is_active', true)
            ->where('is_visible', true)
            ->findOrFail($data['plan_id']);

        $gatewayEnabled = filter_var($this->getSettingValue('payment_gateway_enabled', '1'), FILTER_VALIDATE_BOOLEAN);
        if (!$gatewayEnabled) {
            return response()->json([
                'message' => 'Payment gateway is currently disabled. Please contact support.',
            ], 422);
        }

        $primaryGateway = strtolower($this->getSettingValue('payment_primary_gateway', 'razorpay'));
        if ($primaryGateway !== 'razorpay') {
            return response()->json([
                'message' => 'Only Razorpay is enabled for checkout right now.',
            ], 422);
        }

        $keyId = $this->getSettingValue('razorpay_key_id', '');
        $keySecret = $this->getSettingValue('razorpay_key_secret', '');
        $currency = strtoupper($this->getSettingValue('payment_currency', 'INR'));
        $razorpayMode = strtolower($this->getSettingValue('razorpay_mode', 'test'));
        $razorpayMode = in_array($razorpayMode, ['test', 'live'], true) ? $razorpayMode : 'test';

        if ($keyId === '' || $keySecret === '') {
            return response()->json([
                'message' => 'Razorpay is not configured. Ask super admin to set key id and key secret.',
            ], 422);
        }

        if ($razorpayMode === 'test' && !str_starts_with($keyId, 'rzp_test_')) {
            return response()->json([
                'message' => 'Razorpay mode is set to TEST, but Key ID is not a test key (expected prefix: rzp_test_). Please update integration settings.',
            ], 422);
        }

        if ($razorpayMode === 'live' && !str_starts_with($keyId, 'rzp_live_')) {
            return response()->json([
                'message' => 'Razorpay mode is set to LIVE, but Key ID is not a live key (expected prefix: rzp_live_). Please update integration settings.',
            ], 422);
        }

        $amountInSubunit = (int) round(((float) $plan->price) * 100);
        if ($amountInSubunit <= 0) {
            return response()->json([
                'message' => 'Invalid plan amount. Please contact support.',
            ], 422);
        }

        $receipt = 'plan_' . $plan->id . '_u' . $request->user()->id . '_' . Str::lower(Str::random(8));

        $response = Http::withBasicAuth($keyId, $keySecret)
            ->acceptJson()
            ->post('https://api.razorpay.com/v1/orders', [
                'amount' => $amountInSubunit,
                'currency' => $currency,
                'receipt' => $receipt,
                'notes' => [
                    'user_id' => (string) $request->user()->id,
                    'plan_id' => (string) $plan->id,
                ],
            ]);

        if (!$response->successful()) {
            Log::warning('Razorpay order creation failed', [
                'status' => $response->status(),
                'response' => $response->json() ?: $response->body(),
                'user_id' => $request->user()->id,
                'plan_id' => $plan->id,
                'mode' => $razorpayMode,
                'key_prefix' => Str::substr($keyId, 0, 9),
            ]);

            return response()->json([
                'message' => 'Unable to create payment order. Please try again.',
                'detail' => $response->json('error.description') ?: $response->body(),
            ], 422);
        }

        $order = $response->json();

        UserSubscription::create([
            'user_id' => $request->user()->id,
            'subscription_plan_id' => $plan->id,
            'amount' => $plan->price,
            'currency' => $currency,
            'status' => 'pending',
            'purchased_at' => now(),
            'gateway' => 'razorpay',
            'gateway_txn_id' => $order['id'] ?? null,
            'notes' => json_encode([
                'state' => 'order_created',
                'razorpay_order_id' => $order['id'] ?? null,
                'receipt' => $receipt,
            ]),
        ]);

        return response()->json([
            'key' => $keyId,
            'order' => [
                'id' => $order['id'] ?? null,
                'amount' => $order['amount'] ?? $amountInSubunit,
                'currency' => $order['currency'] ?? $currency,
            ],
            'plan' => [
                'id' => $plan->id,
                'name' => $plan->name,
                'price' => (float) $plan->price,
            ],
            'user' => [
                'name' => (string) $request->user()->name,
                'email' => (string) $request->user()->email,
                'contact' => (string) ($request->user()->phone ?: ''),
            ],
        ]);
    }

    public function verifyPayment(Request $request)
    {
        $data = $request->validate([
            'plan_id' => ['required', 'integer', 'exists:subscription_plans,id'],
            'razorpay_order_id' => ['required', 'string', 'max:120'],
            'razorpay_payment_id' => ['required', 'string', 'max:120'],
            'razorpay_signature' => ['required', 'string', 'max:255'],
        ]);

        $keySecret = $this->getSettingValue('razorpay_key_secret', '');
        if ($keySecret === '') {
            return response()->json([
                'message' => 'Razorpay secret not configured.',
            ], 422);
        }

        $generatedSignature = hash_hmac('sha256', $data['razorpay_order_id'] . '|' . $data['razorpay_payment_id'], $keySecret);
        if (!hash_equals($generatedSignature, $data['razorpay_signature'])) {
            Log::warning('Razorpay signature verification failed', [
                'user_id' => $request->user()->id,
                'plan_id' => $data['plan_id'],
                'razorpay_order_id' => $data['razorpay_order_id'],
                'razorpay_payment_id' => $data['razorpay_payment_id'],
            ]);

            UserSubscription::where('user_id', $request->user()->id)
                ->where('subscription_plan_id', $data['plan_id'])
                ->where('gateway', 'razorpay')
                ->where('gateway_txn_id', $data['razorpay_order_id'])
                ->where('status', 'pending')
                ->latest('id')
                ->first()?->update([
                    'status' => 'failed',
                    'notes' => json_encode([
                        'state' => 'signature_mismatch',
                        'razorpay_order_id' => $data['razorpay_order_id'],
                    ]),
                ]);

            return response()->json([
                'message' => 'Payment verification failed. Signature mismatch.',
            ], 422);
        }

        $subscription = UserSubscription::where('user_id', $request->user()->id)
            ->where('subscription_plan_id', $data['plan_id'])
            ->where('gateway', 'razorpay')
            ->where('gateway_txn_id', $data['razorpay_order_id'])
            ->where('status', 'pending')
            ->latest('id')
            ->first();

        if (!$subscription) {
            return response()->json([
                'message' => 'Pending subscription order not found.',
            ], 404);
        }

        $plan = SubscriptionPlan::findOrFail($data['plan_id']);
        $startsAt = now();
        $endsAt = $this->calculateEndDate($plan->billing_cycle, $startsAt);

        UserSubscription::where('user_id', $request->user()->id)
            ->where('status', 'active')
            ->update([
                'status' => 'expired',
            ]);

        $subscription->update([
            'status' => 'active',
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'gateway_txn_id' => $data['razorpay_payment_id'],
            'notes' => json_encode([
                'state' => 'paid',
                'razorpay_order_id' => $data['razorpay_order_id'],
                'razorpay_payment_id' => $data['razorpay_payment_id'],
                'verified_at' => now()->toIso8601String(),
            ]),
        ]);

        return response()->json([
            'message' => 'Payment successful. Your membership is now active.',
        ]);
    }

    private function calculateEndDate(string $billingCycle, \Illuminate\Support\Carbon $startsAt): ?\Illuminate\Support\Carbon
    {
        if ($billingCycle === 'monthly') {
            return $startsAt->copy()->addMonth();
        }

        if ($billingCycle === 'quarterly') {
            return $startsAt->copy()->addMonths(3);
        }

        if ($billingCycle === 'yearly') {
            return $startsAt->copy()->addYear();
        }

        if ($billingCycle === 'lifetime') {
            return null;
        }

        return $startsAt->copy()->addMonth();
    }

    private function getSettingValue(string $key, string $default = ''): string
    {
        $setting = SystemSetting::where('key', $key)->first();
        if (!$setting || $setting->value === null || $setting->value === '') {
            return $default;
        }

        $value = (string) $setting->value;
        if (!$setting->is_encrypted) {
            return $value;
        }

        try {
            return Crypt::decryptString($value);
        } catch (\Throwable $exception) {
            return $default;
        }
    }
}
