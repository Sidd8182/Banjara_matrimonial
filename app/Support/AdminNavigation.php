<?php

namespace App\Support;

class AdminNavigation
{
    public static function build(array $urls): array
    {
        $dashboardUrl = $urls['dashboardUrl'] ?? '#';
        $pricingManagementUrl = $urls['pricingManagementUrl'] ?? '#';
        $integrationSettingsUrl = $urls['integrationSettingsUrl'] ?? '#';
        $subscriptionsUrl = $urls['subscriptionsUrl'] ?? '#';

        return [
            [
                'label' => 'Main',
                'items' => [
                    ['label' => 'Dashboard', 'url' => $dashboardUrl],
                ],
            ],
            [
                'label' => 'User Management',
                'items' => [
                    [
                        'label' => 'All Members',
                        'children' => [
                            ['label' => 'Active Members', 'url' => '#'],
                            ['label' => 'Premium Members', 'url' => '#'],
                            ['label' => 'Free Members', 'url' => '#'],
                        ],
                    ],
                ],
            ],
            [
                'label' => 'Subscriptions',
                'items' => [
                    ['label' => 'Pricing Plans', 'url' => $pricingManagementUrl],
                    ['label' => 'User Subscriptions', 'url' => $subscriptionsUrl],
                ],
            ],
            [
                'label' => 'Platform Settings',
                'items' => [
                    ['label' => 'Email Setting', 'url' => $integrationSettingsUrl . '#email'],
                    ['label' => 'SMS Setting', 'url' => $integrationSettingsUrl . '#sms'],
                    ['label' => 'Whatsapp Setting', 'url' => $integrationSettingsUrl . '#whatsapp'],
                    ['label' => 'Payment Gateway Setting', 'url' => $integrationSettingsUrl . '#payment'],
                    ['label' => 'Push Notification Setting', 'url' => $integrationSettingsUrl . '#push'],
                    ['label' => 'Astrology Setting', 'url' => $integrationSettingsUrl . '#astrology'],
                ],
            ],
        ];
    }
}
