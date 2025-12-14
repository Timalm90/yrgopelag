<!--  Log in logics


 Plan:
 - Require in config.php
 - Hämta input via $_POST från de inputfält där admin försöker logga in. Action bör vara till kontrollpanelen.
 - Kör jämförande script, om något är fel hänvisa tillbaka till sidan där admin försöker logga in
 - Om inloggad, spara ner i session user, tweaka någon navbar med alterantivet logout.
 - Skapa en fil med logout-logics där sesssion['user'] = NULL i kombination med isset. Hänvisa tillbaka till startsidan där man då åter igen kan logga in :)-->

<?php

declare(strict_types=1);

// Autoload file is required if admin fills form on view/admin.php, form directs to this file, where .env password and input password is compared, if password_verify = true -> header(location: back to view/admin)
require __DIR__ . "/autoload.php";

// Option 2: This logic file i required into view/admin.php form action is empty, no redirection. Since view/admin already has require autoload.php, it is not necessary to have it in this file. It will probably just create more problems...
// This is a design/architecture problem...!!!