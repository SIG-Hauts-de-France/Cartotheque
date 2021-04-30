### Fichier de configuration pour accéder à MailDev via un sous-dossier

ProxyRequests Off
ProxyPass        /maildev/ http://maildev:80/
ProxyPassReverse /maildev/ http://maildev:80/

