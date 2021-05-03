

Fixtures requis à l'installation du module `tic_geosource`
--------------------------------------------------------------------------------

Le module impose le chargement de `fixtures` à partie des fichiers suivants :

*	sites/all/modules/custom/tic_geosource/fixtures/export_types.txt
*	sites/all/modules/custom/tic_geosource/fixtures/export_taxonomy.txt

Ces données peuvent être générées par un export via le module Bundle Copy :

*	Nodes (content types) : /admin/structure/types
*	Taxonomy              : /admin/structure/taxonomy


Import des termes des taxonomies
--------------------------------------------------------------------------------

Export des terms des taxonomies sur le serveur de PROD avec le module `taxonomy_csv` :
```bash
ssh root@91.230.1.143
cd /var/www/html/cartes

LST_TAXO="categorie collections tags mots_cles mots_cles_thesaurus_gemet thematique_gemet thematique_hdf"
for REF_TAXO in ${LST_TAXO}
  do drush taxocsv-export --order=tid "${REF_TAXO}" fields "/home/teicee/taxonomy-${REF_TAXO}.csv"
done
```

Récupération des exports générés sur le serveur de PROD :
```bash
scp root@91.230.1.143:/home/teicee/taxonomy-* .
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

Fichier récupéré sur le serveur de PROD ou de PREPROD :
```bash
scp root@91.230.1.143:/home/teicee/Cartes_production_200228.xlsx\\\ V2.xlsx .
scp root@cartotheque.teicee.fr:/var/dockers/npdcp/sites/all/themes/cartotheque/dump/Cartes_production_200228_V2.xlsx .
```

Import réalisable via une commande Drush du module `tic_theme_hdf_update` :
```bash
drush import-update-theme-hdf Cartes_production_200228_V2.xlsx
```

