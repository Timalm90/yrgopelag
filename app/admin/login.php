<!--  Log in logics


 Plan:
 - Require in config.php
 - Hämta input via $_POST från de inputfält där admin försöker logga in. Action bör vara till kontrollpanelen.
 - Kör jämförande script, om något är fel hänvisa tillbaka till sidan där admin försöker logga in
 - Om inloggad, spara ner i session user, tweaka någon navbar med alterantivet logout.
 - Skapa en fil med logout-logics där sesssion['user'] = NULL i kombination med isset. Hänvisa tillbaka till startsidan där man då åter igen kan logga in :)-->