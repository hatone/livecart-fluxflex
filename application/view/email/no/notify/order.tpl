Ny bestilling hos {'STORE_NAME'|config}
Bestillingsnr: {$order.invoiceNumber}

Order administration:
{backendOrderUrl order=$order url=true}

Følgende produkter er bestilt:
{include file="email/blockOrder.tpl"}

{include file="email/blockOrderAddresses.tpl"}

{include file="email/no/signature.tpl"}