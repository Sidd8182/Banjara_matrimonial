<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Support\AdminNavigation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Inertia\Inertia;

class IntegrationSettingsController extends Controller
{
    private const ENCRYPTED_KEYS = [
        'mail_password',
        'sms_api_key',
        'whatsapp_token',
        'whatsapp_webhook_secret',
        'paypal_client_secret',
        'stripe_secret_key',
        'stripe_webhook_secret',
        'razorpay_key_secret',
        'fcm_server_key',
        'onesignal_rest_api_key',
        'astrology_api_key',
    ];

    public function index()
    {
        $defaults = $this->defaults();
        $stored = SystemSetting::whereIn('key', array_keys($defaults))->get()->keyBy('key');

        $settings = [];
        foreach ($defaults as $key => $meta) {
            $record = $stored->get($key);
            $value = $meta['default'];

            if ($record && $record->value !== null) {
                $value = $this->decodeValue($record->value, (bool) $record->is_encrypted);
            }

            if ($meta['type'] === 'boolean') {
                $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
            }

            $settings[$key] = $value;
        }

        return Inertia::render('Admin/IntegrationSettings', [
            'settings' => $settings,
            'saveAction' => route('admin.integration-settings.update', [], false),
            'dashboardUrl' => route('admin.dashboard', [], false),
            'pricingManagementUrl' => route('admin.pricing-plans.index', [], false),
            'integrationSettingsUrl' => route('admin.integration-settings.index', [], false),
            'subscriptionsUrl' => route('admin.subscriptions.index', [], false),
            'logoutAction' => route('admin.logout', [], false),
            'sidebarMenus' => AdminNavigation::build([
                'dashboardUrl' => route('admin.dashboard', [], false),
                'pricingManagementUrl' => route('admin.pricing-plans.index', [], false),
                'integrationSettingsUrl' => route('admin.integration-settings.index', [], false),
                'subscriptionsUrl' => route('admin.subscriptions.index', [], false),
                'cmsPagesUrl' => route('admin.cms-pages.index', [], false),
                'cmsSectionsUrl' => route('admin.cms-sections.index', [], false),
            ]),
        ]);
    }

    public function update(Request $request)
    {
        $rules = [];
        foreach ($this->defaults() as $key => $meta) {
            $rules[$key] = $meta['rule'];
        }

        $data = $request->validate($rules);
        $defaults = $this->defaults();

        foreach ($defaults as $key => $meta) {
            $raw = $data[$key] ?? $meta['default'];
            $value = $meta['type'] === 'boolean' ? ((bool) $raw ? '1' : '0') : (string) $raw;
            $encrypted = in_array($key, self::ENCRYPTED_KEYS, true);

            SystemSetting::updateOrCreate(
                ['key' => $key],
                [
                    'group' => $meta['group'],
                    'value' => $this->encodeValue($value, $encrypted),
                    'is_encrypted' => $encrypted,
                ]
            );
        }

        Cache::forget('system_settings.all');

        return back()->with('status', 'Integration settings saved successfully.');
    }

    private function defaults(): array
    {
        return [
            'mail_from_name' => ['group' => 'email', 'type' => 'string', 'default' => config('mail.from.name') ?: 'Banjara', 'rule' => ['nullable', 'string', 'max:120']],
            'mail_from_address' => ['group' => 'email', 'type' => 'string', 'default' => config('mail.from.address') ?: '', 'rule' => ['nullable', 'string', 'max:120']],
            'mail_host' => ['group' => 'email', 'type' => 'string', 'default' => config('mail.mailers.smtp.host') ?: '', 'rule' => ['nullable', 'string', 'max:120']],
            'mail_port' => ['group' => 'email', 'type' => 'string', 'default' => (string) (config('mail.mailers.smtp.port') ?: ''), 'rule' => ['nullable', 'string', 'max:10']],
            'mail_username' => ['group' => 'email', 'type' => 'string', 'default' => config('mail.mailers.smtp.username') ?: '', 'rule' => ['nullable', 'string', 'max:120']],
            'mail_password' => ['group' => 'email', 'type' => 'string', 'default' => config('mail.mailers.smtp.password') ?: '', 'rule' => ['nullable', 'string', 'max:255']],
            'mail_encryption' => ['group' => 'email', 'type' => 'string', 'default' => config('mail.mailers.smtp.encryption') ?: 'tls', 'rule' => ['nullable', 'string', 'max:20']],

            'sms_enabled' => ['group' => 'sms', 'type' => 'boolean', 'default' => false, 'rule' => ['nullable', 'boolean']],
            'sms_provider' => ['group' => 'sms', 'type' => 'string', 'default' => 'twilio', 'rule' => ['nullable', 'string', 'max:80']],
            'sms_api_key' => ['group' => 'sms', 'type' => 'string', 'default' => '', 'rule' => ['nullable', 'string', 'max:255']],
            'sms_sender_id' => ['group' => 'sms', 'type' => 'string', 'default' => '', 'rule' => ['nullable', 'string', 'max:80']],
            'sms_base_url' => ['group' => 'sms', 'type' => 'string', 'default' => '', 'rule' => ['nullable', 'string', 'max:255']],

            'whatsapp_enabled' => ['group' => 'whatsapp', 'type' => 'boolean', 'default' => false, 'rule' => ['nullable', 'boolean']],
            'whatsapp_provider' => ['group' => 'whatsapp', 'type' => 'string', 'default' => 'meta_cloud', 'rule' => ['nullable', 'string', 'max:80']],
            'whatsapp_token' => ['group' => 'whatsapp', 'type' => 'string', 'default' => '', 'rule' => ['nullable', 'string', 'max:255']],
            'whatsapp_phone_id' => ['group' => 'whatsapp', 'type' => 'string', 'default' => '', 'rule' => ['nullable', 'string', 'max:80']],
            'whatsapp_webhook_secret' => ['group' => 'whatsapp', 'type' => 'string', 'default' => '', 'rule' => ['nullable', 'string', 'max:255']],

            'payment_gateway_enabled' => ['group' => 'payment', 'type' => 'boolean', 'default' => true, 'rule' => ['nullable', 'boolean']],
            'payment_primary_gateway' => ['group' => 'payment', 'type' => 'string', 'default' => 'razorpay', 'rule' => ['nullable', 'string', 'max:40']],
            'payment_currency' => ['group' => 'payment', 'type' => 'string', 'default' => 'INR', 'rule' => ['nullable', 'string', 'max:10']],
            'razorpay_mode' => ['group' => 'payment', 'type' => 'string', 'default' => 'test', 'rule' => ['nullable', 'in:test,live']],
            'razorpay_key_id' => ['group' => 'payment', 'type' => 'string', 'default' => '', 'rule' => ['nullable', 'string', 'max:120']],
            'razorpay_key_secret' => ['group' => 'payment', 'type' => 'string', 'default' => '', 'rule' => ['nullable', 'string', 'max:255']],
            'paypal_client_id' => ['group' => 'payment', 'type' => 'string', 'default' => '', 'rule' => ['nullable', 'string', 'max:255']],
            'paypal_client_secret' => ['group' => 'payment', 'type' => 'string', 'default' => '', 'rule' => ['nullable', 'string', 'max:255']],
            'paypal_mode' => ['group' => 'payment', 'type' => 'string', 'default' => 'sandbox', 'rule' => ['nullable', 'string', 'max:20']],
            'stripe_public_key' => ['group' => 'payment', 'type' => 'string', 'default' => '', 'rule' => ['nullable', 'string', 'max:255']],
            'stripe_secret_key' => ['group' => 'payment', 'type' => 'string', 'default' => '', 'rule' => ['nullable', 'string', 'max:255']],
            'stripe_webhook_secret' => ['group' => 'payment', 'type' => 'string', 'default' => '', 'rule' => ['nullable', 'string', 'max:255']],

            'push_enabled' => ['group' => 'push', 'type' => 'boolean', 'default' => false, 'rule' => ['nullable', 'boolean']],
            'push_provider' => ['group' => 'push', 'type' => 'string', 'default' => 'fcm', 'rule' => ['nullable', 'string', 'max:40']],
            'fcm_server_key' => ['group' => 'push', 'type' => 'string', 'default' => '', 'rule' => ['nullable', 'string', 'max:255']],
            'fcm_sender_id' => ['group' => 'push', 'type' => 'string', 'default' => '', 'rule' => ['nullable', 'string', 'max:80']],
            'onesignal_app_id' => ['group' => 'push', 'type' => 'string', 'default' => '', 'rule' => ['nullable', 'string', 'max:255']],
            'onesignal_rest_api_key' => ['group' => 'push', 'type' => 'string', 'default' => '', 'rule' => ['nullable', 'string', 'max:255']],

            'astrology_enabled' => ['group' => 'astrology', 'type' => 'boolean', 'default' => false, 'rule' => ['nullable', 'boolean']],
            'astrology_provider' => ['group' => 'astrology', 'type' => 'string', 'default' => 'freeastrologyapi', 'rule' => ['nullable', 'string', 'max:80']],
            'astrology_api_base_url' => ['group' => 'astrology', 'type' => 'string', 'default' => 'https://json.freeastrologyapi.com', 'rule' => ['nullable', 'string', 'max:255']],
            'astrology_birth_details_path' => ['group' => 'astrology', 'type' => 'string', 'default' => '/planets/extended', 'rule' => ['nullable', 'string', 'max:120']],
            'astrology_matchmaking_path' => ['group' => 'astrology', 'type' => 'string', 'default' => '/matchmaking', 'rule' => ['nullable', 'string', 'max:120']],
            'astrology_api_key' => ['group' => 'astrology', 'type' => 'string', 'default' => '', 'rule' => ['nullable', 'string', 'max:255']],
            'astrology_matchmaking_enabled' => ['group' => 'astrology', 'type' => 'boolean', 'default' => false, 'rule' => ['nullable', 'boolean']],
            'astrology_fail_open' => ['group' => 'astrology', 'type' => 'boolean', 'default' => true, 'rule' => ['nullable', 'boolean']],
            'astrology_timeout_seconds' => ['group' => 'astrology', 'type' => 'string', 'default' => '8', 'rule' => ['nullable', 'numeric', 'min:2', 'max:30']],
            'astrology_cache_minutes' => ['group' => 'astrology', 'type' => 'string', 'default' => '1440', 'rule' => ['nullable', 'integer', 'min:5', 'max:10080']],
        ];
    }

    private function encodeValue(string $value, bool $encrypted): string
    {
        if (!$encrypted || $value === '') {
            return $value;
        }

        return Crypt::encryptString($value);
    }

    private function decodeValue(string $value, bool $encrypted): string
    {
        if (!$encrypted || $value === '') {
            return $value;
        }

        try {
            return Crypt::decryptString($value);
        } catch (\Throwable $exception) {
            return '';
        }
    }
}
