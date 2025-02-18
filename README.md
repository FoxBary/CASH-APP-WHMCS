# CASH-APP-WHMCS
CASH APP-WHMCS PAYMENT
 STRIPE Webhook 配置
登录到 STRIPE Dashboard。

导航到 Developers -> Webhooks。

点击 Add endpoint 并设置你的 Webhook URL，例如：


https://yourdomain.com/modules/gateways/stripe_cashapp/stripe_cashapp_callback.php
在 Webhook 配置中，选择以下事件：

checkout.session.completed：当付款成功时触发。
获取 Webhook Secret 并将其填入到 stripe_cashapp_callback.php 文件中的 $webhookSecret 变量中。

说明
支付请求（stripe_cashapp.php）：

当用户选择用 STRIPE Cash App 支付时，stripe_cashapp_link 函数会创建一个支付会话并跳转到 STRIPE 的支付页面。
支付金额会扣除固定的手续费，用户支付后，STRIPE 会跳转到提供的成功或失败页面。
回调处理（stripe_cashapp_callback.php）：

该文件处理 STRIPE 的 Webhook 回调，验证支付是否成功，并更新 WHMCS 系统中的账单状态。
付款成功后，会将交易信息添加到 WHMCS 的财务记录中，并跳转回用户的账单页面。
