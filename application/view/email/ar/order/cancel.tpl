{'STORE_NAME'|config} ألغي الطلب
العزيز{$user.fullName},

طلبك <b class="orderID">#{$order.invoiceNumber}</b>, وضعت في {'STORE_NAME'|config}, تم إلغائه.

إذا كان لديك أي أسئلة بخصوص هذا الطلب، ويمكنك أن ترسل لنا رسالة عبر البريد الإلكتروني أو الاتصال من الصفحة التالية :
{link controller=user action=viewOrder id=$order.ID url=true}

تم الغاء هذه العناصر
{include file="email/blockOrderItems.tpl"}

{include file="email/en/signature.tpl"}