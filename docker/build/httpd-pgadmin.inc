### Fichier de configuration pour accéder à pgAdmin via un sous-dossier

<IfModule proxy_http_module>
	ProxyRequests Off
	ProxyPass        /pgadmin/ http://pgadmin:5050/
	ProxyPassReverse /pgadmin/ http://pgadmin:5050/
</IfModule>

<Location "/pgadmin/">
	RequestHeader set "X-Script-Name" "/pgadmin"
	Header        set "X-Script-Name" "/pgadmin"
</Location>

