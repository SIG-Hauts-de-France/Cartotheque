

Fixtures requis à l'installation du module `tic_geosource`
--------------------------------------------------------------------------------

Le module impose le chargement de `fixtures` à partie des fichiers suivants :

*	sites/all/modules/custom/tic_geosource/fixtures/export_types.txt
*	sites/all/modules/custom/tic_geosource/fixtures/export_taxonomy.txt

Ces données peuvent être générées par un export via le module Bundle Copy :

*	Nodes (content types) : /admin/structure/types
*	Taxonomy              : /admin/structure/taxonomy


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

