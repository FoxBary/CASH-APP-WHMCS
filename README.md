实现说明
CashApp 支付支持：
使用 'payment_method_types' => ['cashapp'] 启用 CashApp 支付。

Stripe 会根据账户配置返回 display_cashapp_qr_code（二维码）或 redirect_to_url（跳转链接）。

代码动态处理这两种情况：显示二维码或提供跳转按钮。

二维码显示：
如果返回 display_cashapp_qr_code，从 image_url_png 获取二维码 URL 并嵌入账单页面。

JavaScript 每 2 秒检查支付状态，成功后跳转到 returnUrl。

货币限制：
CashApp 目前仅支持 USD，因此配置中只提供 USD 选项。

退款功能：
支持扣除手续费后的退款，逻辑与支付宝版本一致。

Webhook 回调：
处理 payment_intent.succeeded 事件，将支付记录添加到 WHMCS 账单。

配置和测试
文件放置：
将 vmshellstripecashapp.php 放入 /modules/gateways/。

将 callbackvmshellcashapp.php 放入 /modules/gateways/callback/。

下载最新 Stripe PHP SDK（从 GitHub）并放入 /stripe-php/。

Stripe 配置：
在 Stripe Dashboard 启用 CashApp Pay（需要申请访问权限，可能仅限美国账户）。

添加 Webhook Endpoint：
Webhook URL：https://yourdomain.com/callback/callbackvmshellcashapp.php

监听事件：payment_intent.succeeded 和 payment_intent.payment_failed。

WHMCS 配置：
在 WHMCS 后台配置支付网关，填写 Stripe 密钥和 Webhook 密钥。

创建测试账单，选择“VmShell-Stripe-CashApp”。
