Pesan Baru Tentang Order Anda di {'STORE_NAME'|config}
Yth Bapak/Ibu {$user.fullName},

Ada pesan baru mengenai order Anda.

--------------------------------------------------
{$message.text}
--------------------------------------------------

Anda dapat memberikan respons dari halaman berikut:
{link controller=user action=viewOrder id=$order.ID url=true}

{include file="email/id/signature.tpl"}