Novo sporočilo za naročilo na {'STORE_NAME'|config}
Stranka je dodala novo sporočilo glede naročila <b class="orderID">#{$order.invoiceNumber}</b>

--------------------------------------------------
{$message.text}
--------------------------------------------------

Odgovor lahko dodate tako, da se prijavite v administracijo:
{backendOrderUrl order=$order url=true}#tabOrderCommunication__

{include file="email/en/signature.tpl"}