Nová zpráva ohledně Vaší objednávky na {'STORE_NAME'|config}
Vážený(á) {$user.fullName},

Dostal jste novou zprávu ohledně Vaší objednávky.

--------------------------------------------------
{$message.text}
--------------------------------------------------

Odpovědět můžete pomocí tohoto odkazu:
{link controller=user action=viewOrder id=$order.ID url=true}

{include file="email/en/signature.tpl"}