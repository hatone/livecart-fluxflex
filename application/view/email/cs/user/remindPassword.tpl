Vaše heslo na {'STORE_NAME'|config}!
Vážený(á) {$user.fullName},

zasíláme Vám přihlašovací údaje do našeho obchodu {$config.STORE_NAME}:

E-mail: <b>{$user.email}</b>
Heslo: <b>{$user.newPassword}</b>

Pro přihlášení k Vašemu účtu můžete použít tento odkaz:
{link controller=user action=login url=true}

{include file="email/en/signature.tpl"}