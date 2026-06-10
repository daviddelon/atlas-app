
# Démarrage 

Expliquer .devcontainer vscode

# Ajout de données pour une nouvelle commune 

## 1 : Ajout contour d'une nouvelle commune

Une table "commune" contenant tous les contours des communes Française est déja chargée avec data/communes_seeder.sh
Cette table n'est pas effacée lors de l'import de nouvelles données.

Executer :


```
cd /var/www/html/data
bash commune_geojson.sh code_insee_commune
cp code_insee_commune.geojson ../storage/app/public

```


## 2 : Modification parametres application 


Modifier le fichier .env : 


DEFAULT_COMMUNE_CODE=34099   # commune affichée
AVAILABLE_COMMUNE_CODES=34343,34274,34172,34099   # communes disponibles dans le selecteur
COMMUNE_ZOOMS=34343=13,34274=12.5 # reglage zoom par defaut pour une commune


## 3 : Import des observations et des taxons

- Recuperer observations Inaturalist < 200 000 observations 


  - Explorer pour afficher la carte des observations
  - Zoomer sur la zone concernée
  - Refaire la recherche sur la carte (englober la commune). On peut faire une recherche sur tous l'Hérault si filtre plantes
  - Filtres / Sous-filtre eventuel plantes  / Telecharger 
  - Verifier le nombre d'observation, < 200 000  : OK 
  - Recuperer les champs suivants : 


Requête quality_grade=any&identifications=any&swlat=43.68556542781056&swlng=3.6176287054687606&nelat=43.880098383806974&nelng=3.905060247656267&verifiable=true
Colonnes id, observed_on, user_id, user_login, user_name, created_at, updated_at, quality_grade, license,  captive_cultivated, latitude, longitude, positional_accuracy, geoprivacy, scientific_name, common_name, taxon_id, taxon_kingdom_name, taxon_phylum_name, taxon_subphylum_name, taxon_superclass_name, taxon_class_name, taxon_subclass_name, taxon_superorder_name, taxon_order_name, taxon_suborder_name, taxon_superfamily_name, taxon_family_name, taxon_subfamily_name, taxon_supertribe_name, taxon_tribe_name, taxon_subtribe_name, taxon_genus_name, taxon_genushybrid_name, taxon_species_name, taxon_hybrid_name, taxon_subspecies_name, taxon_variety_name, taxon_form_name

Placer le fichier cvs dezippé dans data : exemple observations-658902.csv

Lancer

```
cd /var/www/html/

php artisan seed:taxon-observations code insee data/chemin_fichier_csv

```

Exemple : 

```
php artisan seed:taxon-observations 34099 data/observations-746447_plantes_herault_20160608.csv 
```

(Attention il peut être necessaire de vider les tables taxas ou observation manuellement si probleme. Utiliser dbveaver)

## 4 : Photos et description Costes

### Photos : 

Recherche des photos sous licence libre, CC-BY, CC-BY-SA ou CC0 pour les taxons n'ayant pas encore de photo.
Si aucune photo de trouvé, recherche de la photo la plus populaire en France sous licence  CC-BY, CC-BY-SA ou CC0 


```
php artisan seed:missing-photos
```