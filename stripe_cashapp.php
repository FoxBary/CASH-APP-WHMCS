<?php

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

// 引入 STRIPE PHP SDK
require_once __DIR__ . '/stripe-php/init.php';  // 请根据实际路径调整

// 配置参数函数
function stripe_cashapp_config() {
    $configarray = array(
        "name" => "STRIPE Cash App",
        "description" => "Pay with STRIPE Cash App via Checkout.",
        "version" => "1.0",
        "author" => "Your Company Name",
        "fields" => array(
            "SK_LIVE" => array(
                "FriendlyName" => "STRIPE Live Secret Key",
                "Type" => "text",
                "Size" => "40",
                "Description" => "Enter your STRIPE live secret key here.",
            ),
            "WEBHOOK_SECRET" => array(
                "FriendlyName" => "STRIPE Webhook Secret",
                "Type" => "text",
                "Size" => "40",
                "Description" => "Enter your STRIPE webhook secret key here.",
            ),
            "CURRENCY" => array(
                "FriendlyName" => "Currency",
                "Type" => "text",
                "Size" => "3",
                "Description" => "Enter the currency code (e.g., USD).",
            ),
            "FIXED_FEE" => array(
                "FriendlyName" => "Fixed Transaction Fee",
                "Type" => "text",
                "Size" => "10",
                "Description" => "Enter the fixed transaction fee (e.g., 2.00).",
            ),
            "PERCENTAGE_FEE" => array(
                "FriendlyName" => "Percentage Transaction Fee",
                "Type" => "text",
                "Size" => "5",
                "Description" => "Enter the percentage fee for STRIPE (e.g., 2.9 for 2.9%).",
            ),
        ),
    );
    return $configarray;
}

// 处理支付请求
function stripe_cashapp_link($params) {
    // 获取配置项
    $skLive = $params['SK_LIVE'];
    $webhookSecret = $params['WEBHOOK_SECRET'];
    $currency = $params['CURRENCY'];
    $fixedFee = $params['FIXED_FEE'];
    $percentageFee = $params['PERCENTAGE_FEE'];

    // 设置 STRIPE API 密钥
    \Stripe\Stripe::setApiKey($skLive);

    // 创建一个新的支付会话
    try {
        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card', 'cashapp'],
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => $currency,
                        'product_data' => [
                            'name' => 'Invoice Payment',
                        ],
                        'unit_amount' => ($params['amount'] - $fixedFee) * 100,  // 处理手续费
                    ],
                    'quantity' => 1,
                ],
            ],
            'mode' => 'payment',
            'success_url' => $params['systemurl'] . '/modules/gateways/stripe_cashapp/stripe_cashapp_callback.php?status=success&invoice_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $params['systemurl'] . '/modules/gateways/stripe_cashapp/stripe_cashapp_callback.php?status=failed&invoice_id={CHECKOUT_SESSION_ID}',
            'metadata' => [
                'invoice_id' => $params['invoiceid'],
                'fixed_fee' => $fixedFee,
                'percentage_fee' => $percentageFee,
            ],
        ]);
        
        return [
            'status' => 'success',
            'redirecturl' => $session->url,  // 跳转到 STRIPE 支付页面
        ];
    } catch (\Stripe\Exception\ApiErrorException $e) {
        logActivity("Stripe Cash App Error: " . $e->getMessage());
        return [
            'status' => 'error',
            'error' => "Payment creation failed. Please try again.",
        ];
    }
}
