Bienvenue sur {'STORE_NAME'|config}!
Cher {$user.fullName},

Voici vos informations de connexion client sur {'STORE_NAME'|config}:

E-mail: <b>{$user.email}</b>
Mot de passe: <b>{$user.newPassword}</b>

A partir de votre compte client vous pouvez instantanément voir le statut de votre commande, voir vos anciennes commandes,télécharger des fichiers,et modifier vos informations de contact.

Vous pouvez utiliser cette adresse pour vous connecter a votre compte:
{link controller=user action=login url=true}

{include file="email/fr/signature.tpl"}