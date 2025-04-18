VmShell-WHMCS-Stripe-CashApp支付网关插件介绍
监听事件：
payment_intent.succeeded

payment_intent.payment_failed

（可选）charge.refunded
<p><a href="https://linuxword.com/wp-content/uploads/2025/04/stripe-vmshell-cashapp.png"><img class="alignnone size-full wp-image-45118" src="https://linuxword.com/wp-content/uploads/2025/04/stripe-vmshell-cashapp.png" alt="" width="709" height="607" /></a></p>
概述：
随着全球支付方式的多样化，越来越多的用户倾向于使用本地化的支付渠道完成交易。对于美国及其他地区的客户来说，CashApp 是一种便捷的支付方式之一。为了满足这些市场的需求，我们开发了 WHMCS-Stripe-CashApp 支付网关插件。此插件通过 Stripe 提供的 CashApp 支付通道，将 WHMCS 与 CashApp 支付完美结合，让商家无需复杂的开发与申请，即可快速接入 CashApp 支付，提升美国及其他地区客户的支付体验。

此插件不仅具备强大的支付功能，还集成了财务管理工具，自动计算手续费并记录支付详情，极大简化了商家的支付流程。此外，插件还支持退款功能、实时支付状态同步和详细的支付记录，确保商家能够轻松管理所有支付事务。

本插件由 Vmshell INC 开发，作为一款高质量的开源解决方案，完全免费且开源，支持全球用户的使用。

Vmshell INC 公司介绍：
Vmshell INC 是一家注册于美国怀俄明州的正规企业，拥有自有的网络运营 ASN 号：147002。我们提供全球范围的高效网络服务，并特别注重为美国及全球用户提供高质量的云计算服务和互联网基础设施。公司目前运营香港 CMI 线路高速网络云计算中心和美国云计算中心，通过小巧灵动的 VPS，提供全面的全球网络服务。

我们致力于为全球用户提供最稳定、最快速的互联网连接服务，无论您是企业客户还是个人用户，都可以通过我们的高速网络和云计算服务享受顶尖的互联网体验。我们的 VPS 服务针对全球用户，适用于各种业务场景，如网站托管、应用部署、游戏服务器等，为用户提供无与伦比的灵活性和可靠性。

WHMCS-VMSHELL-Stripe-CashApp支付网关插件功能：
快速集成CashApp支付通道
该插件将 Stripe 的 CashApp 支付通道与 WHMCS 系统完美对接，用户在 WHMCS 系统中选择 CashApp 支付后，可以通过扫码完成支付，无需跳转至外部支付页面。系统会自动生成 CashApp 二维码，客户扫码后即可完成支付。该功能适用于虚拟商品、SaaS 服务、域名注册等各类互联网业务。

自动手续费计算与记录
Stripe 的 CashApp 支付通道会按一定比例收取手续费（通常为交易金额的0.6%-2.0%）。本插件支持自动计算和记录 CashApp 支付的手续费，并将其直接记录在 WHMCS 账单中。这为商家提供了便捷的财务核算功能，可以更准确地计算实际利润，避免因手续费计算错误而造成财务差异。

退款功能支持
为了提升客户体验并确保售后服务的完整性，插件还支持退款功能。管理员可以在 WHMCS 后台操作，发起退款请求，系统会自动调用 Stripe 提供的退款接口，将金额原路退回至客户的 CashApp 账户。退款过程透明、快速，商家可实时查看退款状态，保证资金的及时返还。

多币种支付与自动换算
插件支持美元（USD）及其他常见国际币种。在支付过程中，系统会自动根据汇率进行币种转换，确保用户可以以自己熟悉的货币支付。系统还会根据当前汇率生成相应的结算金额，商家可选择是否启用自动结算功能。

支付状态同步与自动回调
插件支持 Stripe Webhook 技术，能够实时获取支付状态并自动同步到 WHMCS 系统。当客户完成支付后，系统会自动更新订单状态为“已支付”，并触发相应的服务开通流程。无论是充值、续费、订单付款，系统都能够在支付成功后自动处理，并减少人工操作，提高工作效率。

详细支付记录与对账报表
插件会生成详细的支付日志，记录包括交易 ID、支付金额、支付状态、手续费、支付时间等信息。管理员可以随时查看和导出支付记录，以便进行财务对账和审计。系统内置的报表功能帮助商户快速了解支付情况，便于管理和优化财务流程。

兼容 WHMCS 多版本
本插件已通过 WHMCS 8.x 和之前版本的兼容测试，支持 PHP 7.4 至 8.2 环境，确保与 WHMCS 系统的稳定运行兼容。安装过程简便，按照配置指南一步步进行即可。

Vmshell 提供的其他服务：
作为一家全球领先的网络服务提供商，Vmshell INC 还提供多种高效且稳定的互联网服务：

香港 CMI 线路高速网络云计算中心：提供超低延迟、高带宽的网络连接，适用于需要高性能计算资源的企业用户。

美国云计算中心：我们在美国的云计算中心具备强大的计算能力，能够支持各种复杂的计算需求，满足全球客户对高效云服务的需求。

小巧灵动的 VPS 服务：我们的 VPS 服务采用先进的硬件和技术，为全球用户提供高性能、低延迟的网络连接，适用于不同的业务需求，包括网站托管、虚拟服务器和数据存储等。

适用场景：
WHMCS-Stripe-CashApp支付网关插件适用于各种面向美国及其他地区市场的在线服务平台，特别适合以下行业：

虚拟主机服务提供商：支持虚拟主机、VPS、域名注册等支付场景。

SaaS 软件服务商：通过 CashApp 支付接收订阅费、月度费用等。

跨境电商平台：为美国消费者提供便捷的支付方式，提升购买转化率。

在线教育平台：支持学费支付、课程购买等。

游戏充值平台：CashApp 支付为游戏玩家提供快速充值途径。

总结：
WHMCS-Stripe-CashApp支付网关插件是一款为全球用户打造的支付解决方案，致力于通过 Stripe 实现 CashApp 支付的完美集成，帮助商家拓展美国及其他市场。凭借其简单易用的设置过程、强大的自动化功能以及高安全性的支付体验，插件能够为您的业务提供稳定、可靠、便捷的支付支持，助力您的跨境扩展与本地市场渗透。立即通过 GitHub 获取源码并开始使用，让您的 WHMCS 系统更加智能高效。

Vmshell INC 作为全球领先的网络服务提供商，不仅提供高效、稳定的云计算与网络基础设施，还为全球用户提供灵活的支付解决方案。无论是企业用户还是个人开发者，您都可以通过我们的官网，轻松获取所需的服务与支持。
官网订购地址：https://vmshell.com/

企业高速网络：https://tototel.com/

TeleGram讨论群：https://t.me/vmshellhk

TeleGram频道：https://t.me/vmshell

实现说明：
CashApp 支付支持： 使用 'payment_method_types' => ['cashapp'] 启用 CashApp 支付。

二维码显示： 如果返回 display_cashapp_qr_code，从 image_url_png 获取二维码 URL 并嵌入账单页面。

货币限制： CashApp 目前仅支持 USD，因此配置中只提供 USD 选项。

退款功能： 支持扣除手续费后的退款，逻辑与支付宝版本一致。

Webhook 回调： 处理 payment_intent.succeeded 事件，将支付记录添加到 WHMCS 账单。

配置和测试：
文件放置：将 vmshellstripecashapp.php 放入 /modules/gateways/，将 callbackvmshellcashapp.php 放入 /modules/gateways/callback/。

下载最新 Stripe PHP SDK（从 GitHub）并放入 /stripe-php/。

在 Stripe Dashboard 启用 CashApp Pay（需要申请访问权限，可能仅限美国账户）。

添加 Webhook Endpoint：Webhook URL：https://yourdomain.com/callback/callbackvmshellcashapp.php。

在 WHMCS 后台配置支付网关，填写 Stripe 密钥和 Webhook 密钥。
