<?php

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

// 引入 STRIPE PHP SDK
require_once __DIR__ . '/stripe-php/init.php';  // 请根据实际路径调整

// 获取 WHMCS 配置
$skLive = 'YOUR_STRIPE_API_KEY';  // 从 WHMCS 配置中获取
$webhookSecret = 'YOUR_STRIPE_WEBHOOK_SECRET';  // 从 WHMCS 配置中获取

// 设置 STRIPE API 密钥
\Stripe\Stripe::setApiKey($skLive);

// 获取回调请求数据
$input = @file_get_contents("php://input");
$event = null;

// 验证回调签名以确保请求的合法性
$signature = $_SERVER['HTTP_STRIPE_SIGNATURE'];

try {
    // 根据签名验证回调请求，构造事件对象
    $event = \Stripe\Webhook::constructEvent($input, $signature, $webhookSecret);
} catch (\Exception $e) {
    logActivity("Stripe Webhook Error: " . $e->getMessage());
    http_response_code(400);  // 请求无效
    echo "Invalid signature";
    exit();
}

// 处理支付成功的回调事件
if ($event->type === 'checkout.session.completed') {
    $session = $event->data->object;
    $invoiceId = $session->metadata->invoice_id;
    $paymentStatus = $session->payment_status;  // 'paid' or 'unpaid'

    // 更新发票状态
    $command = 'UpdateInvoice';
    $postData = [
        'invoiceid' => $invoiceId,
        'status' => $paymentStatus == 'paid' ? 'Paid' : 'Unpaid',
    ];
    localAPI($command, $postData);

    // 添加交易记录
    $command = 'AddTransaction';
    $postData = [
        'userid' => $session->customer_email,
        'amountin' => $session->amount_total / 100,
        'description' => 'Payment for Invoice #' . $invoiceId . ' via STRIPE Cash App',
        'paymentmethod' => 'STRIPE',
        'transid' => $session->payment_intent,
    ];
    localAPI($command, $postData);

    // 跳转到账单页面
    $invoiceUrl = $params['systemurl'] . '/viewinvoice.php?id=' . $invoiceId;
    header('Location: ' . $invoiceUrl);
    exit();
} else {
    logActivity("Unhandled Stripe Webhook Event: " . $event->type);
    http_response_code(400);  // 请求无效
    echo "Unhandled event type";
    exit();
}

http_response_code(200);  // 返回 200 表示成功处理
