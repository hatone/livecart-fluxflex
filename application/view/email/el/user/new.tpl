Καλωσορίσατε στο {'STORE_NAME'|config}!
Αγαπητέ/ή {$user.fullName},

Εδώ είναι οι πληροφορίες πρόσβασης στο λογαριασμό σας στο{'STORE_NAME'|config}:

E-mail: <strong><b>{$user.email}</b></strong>
Password: <strong><b>{$user.newPassword}</b></strong>

Από τον λογαριασμό του πελάτη μπορείτε να δείτε στιγμιαία την κατάσταση της παραγγελλία σας,παλαιότερες παραγγελλίες σας,να κατεβάσετε αρχεία(για αγορές ψηφιακών ειδών) και να αλλάξετε τα στοιχεία επικοινωνίας.

Μπορείτε να χρησιμοποιήσετε αυτή τη διεύθυνση για να υπογράψετε στο λογαριασμό σας:
{link controller=user action=login url=true}

{include file="email/en/signature.tpl"}