Selamat Datang di {'STORE_NAME'|config}!
Yth Bapak/Ibu {$user.fullName},

Berikut ini adalah informasi rekening Anda di {'STORE_NAME'|config}:

E-mail: <b>{$user.email}</b>
Password: <b>{$user.newPassword}</b>

Anda dapat melihat status order Anda, melihat order terdahulu, mendownload file (untuk pembelian berupa file), dan mengubah informasi alamat Anda, dengan cara login ke rekening Anda.

Anda dapat login ke rekening Anda di:
{link controller=user action=login url=true}

{include file="email/id/signature.tpl"}