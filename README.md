
Dockerisation du portail Drupal avec Geosource
================================================================================


Description
--------------------------------------------------------------------------------

Liste des conteneurs déployés :

	Apache HTTPd      (2.4)    ->  ex: <http://localhost:11481/>
	-	PHP           (5.6)
	-	NodeJS        (10)
	-	Drush         (8.*)    ->  ex: ./docker/dkdrush.sh
	-	Drupal        (7.x)
	PostgreSQL        (9.5)
	-	PostGIS       (2.5)
	Apache Tomcat     (9.0)    ->  ex: <http://localhost:11483/>
	-	Java JRE      (8)
	-	GeoSource     (3.0.1)
	-	HTTPd+mod_jk  (2.4)    ->  ex: <http://localhost:11482/>
	PostgreSQL        (9.5)
	-	PostGIS       (2.5)
	[pgAdmin]         (4)      ->  ex: <http://localhost:11481/pgadmin/>    [dev only]
	[MailDev]         (1.1.0)  ->  ex: <http://localhost:11481/maildev/>    [dev only]


Liste des fichiers de configuration de docker-compose :

*	`docker-compose.yml`            :  Configuration de base des conteneurs à déployer
*	`docker-compose.override.yml`   :  Surcharges par défaut pour la configuration en mode dev
*	`.env.dist`                     :  Variables d'environnement pour spécifier une instance (modèle)


Initialisation
--------------------------------------------------------------------------------

Aller dans le sous-dossier `docker`.

Configuration initiale d'une nouvelle instance du projet :
	[ -f '.env' ] || cp '.env.dist' '.env'

Puis éditer le fichier `.env` pour spécifier les paramètres propre à cette instance.


Des données existantes peuvent être fournies pour initialiser les instances :

*	Fichiers publics de Drupal dans `./files/public/`
*	Fichiers privés  de Drupal dans `./files/private/`
*	Import Postgres pour Drupal    dans `./dumps/init_drupaldb/1_import.sql.gz`
*	Import Postgres pour Geosource dans `./dumps/init_geodb/1_import.sql.gz`

Note : voir à la fin du chapitre suivant "Mise en place des données"


Utilisation
--------------------------------------------------------------------------------

Reconstruction des conteneurs (avec arrêt et redémarrage) :
	docker-compose down ; docker-compose build && docker-compose up -d

Lancement des conteneurs (par défaut en mode dev) :
	docker-compose up -d

Lancement des conteneurs en activant le mode prod :
	docker-compose -f docker-compose.yml up -d


Environnement de developpement CLI disponible sur le conteneur 'drupal' :
	./dkshell.sh
ou	./dkshell.sh <commande>
ou	./dkdrush.sh


Distribution
--------------------------------------------------------------------------------

Les configurations docker présentes dans ce dépôt propose aussi un `Dockerfile`
permettant de générer une image complète (distribuable) du container `drupal`
en y intégrant les dossiers des thèmes et modules `custom`.

Celle-ci est construite à partir de l'image par défaut du container `drupal`
(qui n'intègre pas les dossiers qui sont seulement montés en volumes pour le dev)
puis en y copiant les dossiers du dépôt.

Pour générer cette il suffit d'exécuter le script : `./docker/mkdist.sh`
L'image obtenue est ensuite identifiable par le tag `hdf-cartotheque-dist_drupal`


Consultation
--------------------------------------------------------------------------------

Accès à l'instance du container `drupal` :

*	http://localhost:11481/

Accès aux outils de dev `pgadmin` et `maildev` (via proxy sur `drupal`) :

*	http://localhost:11481/pgadmin/
*	http://localhost:11481/maildev/

Accès à l'application `geosource` par frontal web (`httpd`/`mod_jk`/`ajp`) :

*	http://localhost:11482/
	-	http://localhost:11482/geosource/
	-	http://localhost:11482/geosource/srv/fre/catalog.search#/home

Accès aux consoles d'administration de `tomcat` et `mod_jk` :

*	http://localhost:11482/manager/
	-	http://localhost:11482/manager/html
	-	http://localhost:11482/manager/status
*	http://localhost:11482/jk-manager

Accès direct au serveur `tomcat` du container `geosource` (dev only) :

*	http://localhost:11483/
*	http://localhost:11483/manager/
	-	access denied (localhost only)
*	http://localhost:11483/geosource/
	-	http://localhost:11483/geosource/srv/fre/catalog.search#/home



Mise en place des données
================================================================================


Fixtures requis à l'installation du module `tic_geosource`
--------------------------------------------------------------------------------

Le module impose le chargement de `fixtures` à partie des fichiers suivants :

*	`sites/all/modules/custom/tic_geosource/fixtures/export_types.txt`
*	`sites/all/modules/custom/tic_geosource/fixtures/export_taxonomy.txt`

Ces données peuvent être générées par un export via le module Bundle Copy :

*	Nodes (content types) : `https://cartes.hautsdefrance.fr/admin/structure/types`
*	Taxonomy              : `https://cartes.hautsdefrance.fr/admin/structure/taxonomy`

Note : des données sont déjà inclues dans le dossier `/imports` de ce dépôt Git
(permettant une initialisation lors du lancement d'une nouvelle instance Drupal)


Import des termes des taxonomies
--------------------------------------------------------------------------------

Export des terms des taxonomies sur le serveur de PROD avec le module `taxonomy_csv` :
```bash
ssh root@[L'IP de mon serveur]
cd /var/www/html/cartes

LST_TAXO="categorie collections tags mots_cles mots_cles_thesaurus_gemet thematique_gemet thematique_hdf"
for REF_TAXO in ${LST_TAXO}
  do drush taxocsv-export --order=tid "${REF_TAXO}" fields "/data/teicee/taxonomy-${REF_TAXO}.csv"
done
```

Récupération des exports générés sur le serveur de PROD :
```bash
scp root@[L'IP de mon serveur]:/data/teicee/taxonomy-* .
```

Exemple de commande pour importer tous les fichiers CSV :
```bash
for SRC_TAXO in ./sites/default/fixtures/taxonomy-*.csv; do
  REF_TAXO="$(echo "${SRC_TAXO}" | sed 's|^.*/taxonomy-\(.*\)\.csv$|\1|')"
  drush taxocsv-import --keep_order --vocabulary_target=existing \
                       --vocabulary_id="${REF_TAXO}" "${SRC_TAXO}" fields
done
```

Note : des données sont déjà inclues dans le dossier `/imports` de ce dépôt Git
(permettant un import automatique au lancement d'une nouvelle instance Drupal)


Import Excel pour la mise à jour des thèmes HdF
--------------------------------------------------------------------------------

Fichier récupéré sur le serveur de PROD :
```bash
scp root@[L'IP de mon serveur]:/data/teicee/Cartes_production_200228_V2.xlsx ./imports/
```

Import réalisable via une commande Drush du module `tic_theme_hdf_update` :
```bash
drush import-update-theme-hdf ./sites/default/fixtures/Cartes_production_200228_V2.xlsx
```

Note : des données sont déjà inclues dans le dossier `/imports` de ce dépôt Git
(mais AUCUNE exécution n'est réalisée au lancement de l'instance Drupal,
pour ne pas risquer un échec fatal pour le démarrage du container)


Import des bases de données en production (drupal & geosource)
--------------------------------------------------------------------------------

Génération de dumps des bases Drupal et Geosource avec la commande `pg_dump` :
```bash
ssh root@[L'IP de mon serveur]

pg_dump --host=[Mon host] --dbname=drupalcartes --username=drupalcartes \
 --no-owner --no-privileges --blobs --oids --compress=5 \
 --file="/data/teicee/dump-drupalcartes-$(date +"%Y%m%d").sql.gz"

pg_dump --host=[Mon host] --dbname=geosource    --username=postgres \
 --no-owner --no-privileges --blobs --oids --compress=5 \
 --file="/data/teicee/dump-geosource-$(date +"%Y%m%d").sql.gz"
```

Récupération des fichiers des dumps SQL du serveur de PROD :
```bash
cd ./docker/dumps

scp root@[L'IP de mon serveur]:/data/teicee/dump-drupalcartes-$(date +"%Y%m%d").sql.gz ./init_drupaldb/1_import.sql.gz
scp root@[L'IP de mon serveur]:/data/teicee/dump-geosource-$(date +"%Y%m%d").sql.gz    ./init_geodb/1_import.sql.gz
```

Suppression des volumes pour recharger les dumps des bases de données :
```bash
docker-compose down
docker volume rm hdf-cartotheque-dev_drupaldb_data
docker volume rm hdf-cartotheque-dev_geodb_data
docker-compose up
```

Note : des dumps réalisés le 04/05/2021 sont disponibles sur le serveur de PROD
(`dump-drupalcartes-20210504.sql.gz` et `dump-geosource-20210504.sql.gz`)


Récupération du dossier des fichiers de l'instance de production
--------------------------------------------------------------------------------

Génération d'une archive du dossier `files` de Drupal sur la PROD :
```bash
ssh root@[L'IP de mon serveur]
tar cf /data/teicee/files-$(date +"%Y%m%d").tar /var/www/html/cartes/sites/default/files/
```

Récupération de l'archive et mise en place pour l'instance Docker :
```bash
scp root@[L'IP de mon serveur]:/data/teicee/files-$(date +"%Y%m%d").tar .
tar xf ./files-$(date +"%Y%m%d").tar
mv ./var/www/html/cartes/sites/default/files/private/* ./docker/files/private/
rm -rf ./var/www/html/cartes/sites/default/files/private
mv ./var/www/html/cartes/sites/default/files/*         ./docker/files/public/
rm -rf ./var
```

Note : une archive réalisée le 04/05/2021 est disponible sur le serveur de PROD
(`/data/teicee/files-20210504.tar` attention elle pèse 3.9 Go)

