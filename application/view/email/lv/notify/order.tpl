Saņemts jauns pasūtījums {'STORE_NAME'|config}
Pasūtījuma ID: {$order.invoiceNumber}

Pasūtījuma administrācija:
{backendOrderUrl order=$order url=true}

Pasūtītas sekojošas preces:
{include file="email/blockOrder.tpl"}

{include file="email/blockOrderAddresses.tpl"}

{include file="email/lv/signature.tpl"}