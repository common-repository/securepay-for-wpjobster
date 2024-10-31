<?php

$securepay_fields = [
    [
        'id' => 'securepay-general-options',
        'type' => 'section',
        'title' => esc_html__('General Options', 'securepaywpjobster'),
        'indent' => true,
    ],
    [
        'id' => 'wpjobster_securepay_enable',
        'type' => 'switch',
        'title' => esc_html__('Enable SecurePay', 'securepaywpjobster'),
        'subtitle' => esc_html__('Enable/Disable SecurePay payment gateway', 'securepaywpjobster'),
        'default' => false,
    ],
    [
        'id' => 'wpjobster_securepay_enabletestmode',
        'type' => 'switch',
        'title' => esc_html__('Enable Test mode', 'wpjobster'),
        'subtitle' => esc_html__('Enable/Disable SecurePay test mode', 'securepaywpjobster'),
        'default' => true,
    ],
];

foreach (wpj_get_payment_types(['subscription', 'badge']) as $key => $payment_type) {
    /*if (!\in_array($key, ['topup', 'featured', 'withdraw', 'custom_extra', 'tips'])) {
        continue;
    }*/
    $securepay_fields[] = [
        'id' => 'wpjobster_securepay_enable_'.$key,
        'type' => 'switch',
        'title' => $payment_type['enable_label'],
        'subtitle' => $payment_type['hint_label'],
        'default' => true,
    ];
}

$caption_button = wpj_get_option('wpjobster_securepay_button_caption');
if ('' === trim($caption_button)) {
    $caption_button = 'Pay with SecurePay';
}

$securepay_fields[] = [
    'id' => 'wpjobster_securepay_button_caption',
    'type' => 'text',
    'title' => esc_html__('SecurePay Button Caption', 'wpjobster'),
    'subtitle' => esc_html__('Put the SecurePay button caption you want user to see on purchase page', 'securepaywpjobster'),
    'default' => $caption_button,
];

$securepay_fields[] = [
    'id' => 'wpjobster_securepay_enableautoupdate',
    'type' => 'switch',
    'title' => esc_html__('Enable Auto-updates', 'wpjobster'),
    'subtitle' => esc_html__('Enable/Disable SecurePay plugin Auto-updates', 'securepaywpjobster'),
    'default' => true,
];

$securepay_fields[] = [
    'id' => 'securepay-live-options',
    'type' => 'section',
    'title' => esc_html__('Live Options', 'securepaywpjobster'),
    'indent' => true,
];

$securepay_fields[] = [
    'id' => 'wpjobster_securepay_live_token',
    'type' => 'text',
    'title' => esc_html__('SecurePay Live Token', 'wpjobster'),
    'subtitle' => esc_html__('Your SecurePay Live Token', 'securepaywpjobster'),
];

$securepay_fields[] = [
    'id' => 'wpjobster_securepay_live_checksum',
    'type' => 'text',
    'title' => esc_html__('SecurePay Live Checksum Token', 'wpjobster'),
    'subtitle' => esc_html__('Your SecurePay Live Checksum Token', 'securepaywpjobster'),
];

$securepay_fields[] = [
    'id' => 'wpjobster_securepay_live_uid',
    'type' => 'text',
    'title' => esc_html__('SecurePay Live UID', 'wpjobster'),
    'subtitle' => esc_html__('Your SecurePay Live UID', 'securepaywpjobster'),
];

$securepay_fields[] = [
    'id' => 'wpjobster_securepay_live_partner_uid',
    'type' => 'text',
    'title' => esc_html__('SecurePay Live Partner UID', 'wpjobster'),
    'subtitle' => esc_html__('Your SecurePay Live Partner UID', 'securepaywpjobster'),
];

$securepay_fields[] = [
    'id' => 'securepay-sandbox-options',
    'type' => 'section',
    'title' => esc_html__('Sandbox Options', 'securepaywpjobster'),
    'indent' => true,
];

$securepay_fields[] = [
    'id' => 'wpjobster_securepay_enablesandbox',
    'type' => 'switch',
    'title' => esc_html__('Enable Sandbox Mode', 'wpjobster'),
    'subtitle' => esc_html__('Enable/Disable SecurePay sandbox mode', 'securepaywpjobster'),
];

$securepay_fields[] = [
    'id' => 'wpjobster_securepay_sandbox_token',
    'type' => 'text',
    'title' => esc_html__('SecurePay Sandbox Token', 'wpjobster'),
    'subtitle' => esc_html__('Your SecurePay Sandbox Token', 'securepaywpjobster'),
];

$securepay_fields[] = [
    'id' => 'wpjobster_securepay_sandbox_checksum',
    'type' => 'text',
    'title' => esc_html__('SecurePay Sandbox Checksum Token', 'wpjobster'),
    'subtitle' => esc_html__('Your SecurePay Sandbox Checksum Token', 'securepaywpjobster'),
];

$securepay_fields[] = [
    'id' => 'wpjobster_securepay_sandbox_uid',
    'type' => 'text',
    'title' => esc_html__('SecurePay Sandbox UID', 'wpjobster'),
    'subtitle' => esc_html__('Your SecurePay Sandbox UID', 'securepaywpjobster'),
];

$securepay_fields[] = [
    'id' => 'wpjobster_securepay_sandbox_partner_uid',
    'type' => 'text',
    'title' => esc_html__('SecurePay Sandbox Partner UID', 'wpjobster'),
    'subtitle' => esc_html__('Your SecurePay Sandbox Partner UID', 'securepaywpjobster'),
];

$securepay_fields[] = [
    'id' => 'securepay-redirect-options',
    'type' => 'section',
    'title' => esc_html__('Redirect Options', 'securepaywpjobster'),
    'indent' => true,
];

$securepay_fields[] = [
    'id' => 'wpjobster_securepay_success_page',
    'type' => 'select',
    'data' => 'pages',
    'title' => esc_html__('Transaction Success Redirect', 'wpjobster'),
    'subtitle' => esc_html__('Please select a page to show when SecurePay payment successful. If empty, it redirects to the transaction page', 'securepaywpjobster'),
];

$securepay_fields[] = [
    'id' => 'wpjobster_securepay_failure_page',
    'type' => 'select',
    'data' => 'pages',
    'title' => esc_html__('Transaction Failure Redirect', 'wpjobster'),
    'subtitle' => esc_html__('Please select a page to show when SecurePay payment failed. If empty, it redirects to the transaction page', 'securepaywpjobster'),
];

Redux::setSection(
    $opt_name,
    [
        'title' => esc_html__('SecurePay', 'securepaywpjobster'),
        'desc' => esc_html__('SecurePay Settings', 'securepaywpjobster'),
        'id' => 'securepay-settings',
        'subsection' => true,
        'fields' => $securepay_fields,
    ]
);
