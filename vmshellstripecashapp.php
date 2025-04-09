<?php
// 文件编码声明，确保中文不乱码
header('Content-Type: text/html; charset=UTF-8');

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

// 加载 Stripe PHP 库
require_once __DIR__ . '/stripe-php/init.php';

use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Refund;

function vmshellstripecashapp_MetaData()
{
    return [
        'DisplayName' => 'VmShell-Stripe-CashApp',
        'APIVersion' => '1.1',
        'DisableLocalCreditCardInput' => true,
        'TokenisedStorage' => false,
    ];
}

function vmshellstripecashapp_config()
{
    return [
        'FriendlyName' => [
            'Type' => 'System',
            'Value' => 'VmShell-Stripe-CashApp',
        ],
        'stripeSecretKey' => [
            'FriendlyName' => 'Stripe 秘密密钥',
            'Type' => 'text',
            'Size' => '50',
            'Description' => '请输入您的 Stripe 秘密密钥 (SK_LIVE)。',
        ],
        'stripePublishableKey' => [
            'FriendlyName' => 'Stripe 发布密钥',
            'Type' => 'text',
            'Size' => '50',
            'Description' => '请输入您的 Stripe 发布密钥 (PK_LIVE)。',
        ],
        'webhookSecret' => [
            'FriendlyName' => 'Webhook 密钥',
            'Type' => 'text',
            'Size' => '50',
            'Description' => '请输入您的 Stripe Webhook 密钥，用于验证回调签名。',
        ],
        'currency' => [
            'FriendlyName' => '收款货币',
            'Type' => 'dropdown',
            'Options' => [
                'USD' => '美元 (USD)', // CashApp 仅支持 USD
            ],
            'Default' => 'USD',
            'Description' => 'CashApp 支付仅支持美元 (USD)。',
        ],
        'feePercentage' => [
            'FriendlyName' => '手续费百分比',
            'Type' => 'text',
            'Size' => '10',
            'Default' => '2.9',
            'Description' => '每笔交易收取的手续费百分比（默认 2.9%）。',
        ],
        'feeFixed' => [
            'FriendlyName' => '固定手续费',
            'Type' => 'text',
            'Size' => '10',
            'Default' => '0.3',
            'Description' => '每笔交易收取的固定手续费（默认 0.3）。',
        ],
    ];
}

function vmshellstripecashapp_link($params)
{
    if (empty($params['stripeSecretKey']) || empty($params['stripePublishableKey'])) {
        logTransaction('vmshellstripecashapp', ['error' => 'Stripe密钥未配置'], '配置错误');
        return '<div style="color: red; text-align: center;">错误：支付网关未正确配置，请联系管理员。</div>';
    }

    $publishableKey = htmlspecialchars($params['stripePublishableKey'], ENT_QUOTES, 'UTF-8');
    $secretKey = $params['stripeSecretKey'];
    $invoiceId = (int)$params['invoiceid'];
    $description = htmlspecialchars($params['description'], ENT_QUOTES, 'UTF-8');
    $amount = floatval($params['amount']);
    $gatewayCurrency = strtolower($params['currency']);
    $totalAmountCents = (int)($amount * 100);
    $returnUrl = htmlspecialchars($params['systemurl'] . '/viewinvoice.php?id=' . $invoiceId . '&payment=success', ENT_QUOTES, 'UTF-8');

    // 设置 Stripe API 密钥
    Stripe::setApiKey($secretKey);
    Stripe::setApiVersion('2024-06-20');

    try {
        // 创建 PaymentIntent
        $paymentIntent = PaymentIntent::create([
            'amount' => $totalAmountCents,
            'currency' => $gatewayCurrency,
            'payment_method_types' => ['cashapp'],
            'description' => $description,
            'metadata' => [
                'invoice_id' => $invoiceId,
                'original_amount' => $amount,
                'fee_percentage' => $params['feePercentage'],
                'fee_fixed' => $params['feeFixed'],
            ],
        ]);

        logTransaction('vmshellstripecashapp', [
            'payment_intent_id' => $paymentIntent->id,
            'status' => $paymentIntent->status,
        ], 'PaymentIntent 创建');

        $clientSecret = $paymentIntent->client_secret;

        // 构造 HTML 和 JavaScript
        $htmlOutput = '<script src="https://js.stripe.com/v3/"></script>';
        $htmlOutput .= '<style>
            #cashapp-button { 
                background-color: #00C805; 
                color: white; 
                padding: 10px 20px; 
                border: none; 
                border-radius: 5px; 
                cursor: pointer; 
                font-size: 16px; 
                margin: 20px auto; 
                display: block; 
            }
            #payment-status { 
                text-align: center; 
                margin-top: 10px; 
                color: #666; 
            }
            #error-message { 
                text-align: center; 
                margin-top: 10px; 
                color: red; 
            }
            #cashapp-element {
                margin: 20px auto;
                width: 300px;
                text-align: center;
            }
        </style>';
        $htmlOutput .= '<div style="text-align: center; margin: 10px;">请使用 CashApp 扫描下方二维码支付</div>';
        $htmlOutput .= '<div id="cashapp-element"></div>';
        $htmlOutput .= '<button id="cashapp-button">使用 CashApp 支付 ' . number_format($amount, 2) . ' ' . strtoupper($gatewayCurrency) . '</button>';
        $htmlOutput .= '<div id="payment-status"></div>';
        $htmlOutput .= '<div id="error-message"></div>';

        $htmlOutput .= "
        <script>
            (function() {
                const stripe = Stripe('$publishableKey', { apiVersion: '2024-06-20' });
                const clientSecret = '$clientSecret';
                const payButton = document.getElementById('cashapp-button');
                const statusDiv = document.getElementById('payment-status');
                const errorDiv = document.getElementById('error-message');

                // 初始化 Stripe Elements
                const elements = stripe.elements({ clientSecret: clientSecret });
                const paymentElement = elements.create('payment', {
                    paymentMethodTypes: ['cashapp']
                });
                paymentElement.mount('#cashapp-element');

                console.log('Stripe Elements 初始化完成，clientSecret:', clientSecret);

                payButton.addEventListener('click', async function() {
                    payButton.disabled = true;
                    statusDiv.textContent = '正在处理 CashApp 支付...';
                    errorDiv.textContent = '';

                    try {
                        const result = await stripe.confirmPayment({
                            elements: elements,
                            confirmParams: {
                                return_url: '$returnUrl'
                            }
                        });

                        console.log('confirmPayment 返回结果:', result);

                        if (result.error) {
                            errorDiv.textContent = '支付失败：' + result.error.message + ' (Code: ' + result.error.code + ')';
                            statusDiv.textContent = '';
                            payButton.disabled = false;
                        } else if (result.paymentIntent) {
                            if (result.paymentIntent.status === 'succeeded') {
                                statusDiv.textContent = '支付成功！正在跳转...';
                                setTimeout(() => { window.location.href = '$returnUrl'; }, 2000);
                            } else if (result.paymentIntent.status === 'requires_action') {
                                statusDiv.textContent = '请在 CashApp 中完成支付...';
                                checkPaymentStatus();
                            } else {
                                errorDiv.textContent = '支付状态异常：' + result.paymentIntent.status;
                                payButton.disabled = false;
                            }
                        }
                    } catch (error) {
                        console.error('支付处理错误:', error);
                        errorDiv.textContent = '支付处理错误：' + (error.message || '未知错误') + ' (请检查控制台)';
                        statusDiv.textContent = '';
                        payButton.disabled = false;
                    }
                });

                // 检查支付状态
                function checkPaymentStatus() {
                    stripe.retrievePaymentIntent(clientSecret).then(function(response) {
                        console.log('retrievePaymentIntent 状态:', response);
                        if (response.error) {
                            errorDiv.textContent = '检查状态失败：' + response.error.message;
                        } else if (response.paymentIntent.status === 'succeeded') {
                            statusDiv.textContent = '支付成功！正在跳转...';
                            setTimeout(() => { window.location.href = '$returnUrl'; }, 2000);
                        } else if (response.paymentIntent.status === 'requires_payment_method' || response.paymentIntent.status === 'requires_action') {
                            setTimeout(checkPaymentStatus, 2000); // 继续轮询
                        } else {
                            errorDiv.textContent = '支付状态异常：' + response.paymentIntent.status;
                            payButton.disabled = false;
                        }
                    }).catch(function(error) {
                        console.error('检查状态错误:', error);
                        errorDiv.textContent = '检查状态错误：' + (error.message || '未知错误');
                    });
                }
            })();
        </script>";

        return $htmlOutput;
    } catch (\Exception $e) {
        logTransaction('vmshellstripecashapp', ['error' => $e->getMessage()], '支付处理失败');
        return '<div style="color: red; text-align: center;">错误：支付处理失败 - ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</div>';
    }
}

function vmshellstripecashapp_refund($params)
{
    $secretKey = $params['stripeSecretKey'];
    $transactionId = htmlspecialchars($params['transid'], ENT_QUOTES, 'UTF-8');
    $amount = floatval($params['amount']);
    $feePercentage = floatval($params['feePercentage']) / 100; // 转换为小数
    $feeFixed = floatval($params['feeFixed']);

    if (empty($transactionId) || $amount <= 0) {
        logTransaction('vmshellstripecashapp', ['error' => '无效的退款参数'], '退款失败');
        return ['status' => 'error', 'message' => '退款失败：无效参数'];
    }

    // 计算手续费和实际退款金额
    $feeAmount = ($amount * $feePercentage) + $feeFixed;
    $refundableAmount = max(0, $amount - $feeAmount);
    $refundAmountCents = (int)($refundableAmount * 100);

    // 设置 Stripe API 密钥
    Stripe::setApiKey($secretKey);
    Stripe::setApiVersion('2024-06-20');

    try {
        $refund = Refund::create([
            'payment_intent' => $transactionId,
            'amount' => $refundAmountCents,
            'metadata' => [
                'invoice_id' => $params['invoiceid'],
                'original_amount' => $amount,
                'fee_percentage' => $params['feePercentage'],
                'fee_fixed' => $params['feeFixed'],
                'fee_amount' => $feeAmount,
                'refundable_amount' => $refundableAmount,
            ],
        ]);

        logTransaction('vmshellstripecashapp', [
            'refund_id' => $refund->id,
            'original_amount' => $amount,
            'fee_amount' => $feeAmount,
            'refunded_amount' => $refundableAmount,
        ], '退款成功');

        return [
            'status' => ($refund->status === 'succeeded' || $refund->status === 'pending') ? 'success' : 'error',
            'transid' => $refund->id,
            'amount' => $refundableAmount,
            'rawdata' => (array)$refund,
            'message' => "退款已处理，原金额: $amount，手续费: $feeAmount，实际退款: $refundableAmount，预计24小时内到账。\nThe refund has been processed, original amount: $amount, fee: $feeAmount, refunded: $refundableAmount, expected within 24 hours.",
        ];
    } catch (\Exception $e) {
        logTransaction('vmshellstripecashapp', ['error' => $e->getMessage()], '退款失败');
        return [
            'status' => 'error',
            'rawdata' => $e->getMessage(),
            'transid' => $transactionId,
            'message' => '退款失败：' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'),
        ];
    }
}
