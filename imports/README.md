

Fixtures requis à l'installation du module `tic_geosource`
--------------------------------------------------------------------------------

Le module impose le chargement de `fixtures` à partie des fichiers suivants :

*	`sites/all/modules/custom/tic_geosource/fixtures/export_types.txt`
*	`sites/all/modules/custom/tic_geosource/fixtures/export_taxonomy.txt`

Ces données peuvent être générées par un export via le module Bundle Copy :

*	Nodes (content types) : `/admin/structure/types`
*	Taxonomy              : `/admin/structure/taxonomy`


Import des termes des taxonomies
--------------------------------------------------------------------------------

Export des terms des taxonomies sur le serveur de PROD avec le module `taxonomy_csv` :
```bash
ssh root@91.230.1.143
cd /var/www/html/cartes

LST_TAXO="categorie collections tags mots_cles mots_cles_thesaurus_gemet thematique_gemet thematique_hdf"
for REF_TAXO in ${LST_TAXO}
  do drush taxocsv-export --order=tid "${REF_TAXO}" fields "/data/teicee/taxonomy-${REF_TAXO}.csv"
done
```

Récupération des exports générés sur le serveur de PROD :
```bash
scp root@91.230.1.143:/data/teicee/taxonomy-* .
```

Exemple de commande pour importer tous les fichiers CSV :
```bash
for SRC_TAXO in ./sites/default/fixtures/taxonomy-*.csv; do
  REF_TAXO="$(echo "${SRC_TAXO}" | sed 's|^.*/taxonomy-\(.*\)\.csv$|\1|')"
  drush taxocsv-import --keep_order --vocabulary_target=existing --vocabulary_id="${REF_TAXO}" "${SRC_TAXO}" fields
done
```


Import Excel pour la mise à jour des thèmes HdF
--------------------------------------------------------------------------------

Fichier récupéré sur le serveur de PROD :
```bash
scp root@91.230.1.143:/data/teicee/Cartes_production_200228.xlsx\\\ V2.xlsx .
```

Import réalisable via une commande Drush du module `tic_theme_hdf_update` :
```bash
drush import-update-theme-hdf Cartes_production_200228_V2.xlsx
```


Import des bases de données en production (drupal & geosource)
--------------------------------------------------------------------------------

Génération de dumps des bases Drupal et Geosource avec la commande `pg_dump` :
```bash
ssh root@91.230.1.143

pg_dump --host=172.16.102.23 --dbname=drupalcartes --username=drupalcartes \
 --no-owner --no-privileges --blobs --oids --compress=5 \
 --file="/data/teicee/dump-drupalcartes-$(date +"%Y%m%d").sql.gz"

pg_dump --host=172.16.102.23 --dbname=geosource    --username=postgres \
 --no-owner --no-privileges --blobs --oids --compress=5 \
 --file="/data/teicee/dump-geosource-$(date +"%Y%m%d").sql.gz"
```

Récupération des fichiers des dumps SQL du serveur de PROD :
```bash
cd ../docker/dumps

scp root@91.230.1.143:/data/teicee/dump-drupalcartes-$(date +"%Y%m%d").sql.gz ./init_drupaldb/1_import.sql.gz
scp root@91.230.1.143:/data/teicee/dump-geosource-$(date +"%Y%m%d").sql.gz    ./init_geodb/1_import.sql.gz
```

Suppression des volumes pour recharger les dumps des bases de données :
```bash
docker-compose down
docker volume rm hdf-carto_drupaldb_data
docker volume rm hdf-carto_geodb_data
docker-compose up
```


Récupération du dossier des fichiers de l'instance de production
--------------------------------------------------------------------------------

Génération d'une archive du dossier `files` de Drupal sur la PROD :
```bash
ssh root@91.230.1.143
tar cf /data/teicee/files-$(date +"%Y%m%d").tar /var/www/html/cartes/sites/default/files/
```

Récupération de l'archive et mise en place pour l'instance Docker :
```bash
scp root@91.230.1.143:/data/teicee/files-$(date +"%Y%m%d").tar .
tar xf ./files-$(date +"%Y%m%d").tar
mv ./var/www/html/cartes/sites/default/files/private/* ../docker/files/private/
rm -rf ./var/www/html/cartes/sites/default/files/private
mv ./var/www/html/cartes/sites/default/files/*         ../docker/files/public/
rm -rf ./var
```

