رسالة طلب جديد في {'STORE_NAME'|config}
أضاف عميل رسالة جديدة بشأن الطلب<b class="orderID">#{$order.invoiceNumber}</b>

--------------------------------------------------
{$message.text}
--------------------------------------------------

يمكنك إضافة أي رد من لوحةإدارة الطلب:
{backendOrderUrl order=$order url=true}#tabOrderCommunication__

{include file="email/en/signature.tpl"}