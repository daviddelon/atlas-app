<?php

/**
 * TaxonObservationSeeder
 *
 * Ce seeder importe des données d'observations et de taxons à partir d'un fichier CSV.
 * Il filtre les observations pour ne conserver que celles situées dans une commune spécifique (code 34343).
 *
 * Fonctionnement :
 * - Suppression des données existantes dans les tables observations et taxa
 * - Lecture du fichier CSV observations-658902.csv
 * - Vérification géospatiale : seules les observations dans la commune sont conservées
 * - Insertion par chunks de 10000 enregistrements pour optimisation
 * - Création des enregistrements taxons et observations
 *
 * Dépendances :
 * - Fichier CSV : /var/www/html/data/observations-658902.csv
 * - Commune avec code 34343 dans la base de données
 * - Extensions Mysql pour les fonctions spatiales (ST_Contains, etc.)
 *
 * @package Database\Seeders
 */

namespace Database\Seeders;

use App\Models\Taxon;
use GuzzleHttp\Client;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;
use League\Csv\Statement;
use RestClient;

class TaxonObservationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


        DB::disableQueryLog();



        DB::table('observations')->delete();
        DB::table('taxa')->delete();

        $communeGeom = DB::table('communes')
            ->where('code', '34343')
            ->value('geom');

        if (!$communeGeom) {
            throw new \Exception("Commune 34343 introuvable.");
        }

        $stream = fopen('/var/www/html/data/observations-658902.csv', 'r');
        $csv = Reader::createFromStream($stream);
        $csv->setHeaderOffset(0); //set the CSV header offset
        $csv->setEscape(''); //required in PHP8.4+ to avoid deprecation notices


        $stmt = new Statement()
        ->orderByAsc('taxon_id');


        $all = $stmt->process($csv);



        $previous_taxon_id=null;

        $i=0;
        foreach ($all->chunkBy(10000) as $chunk) {

            print "$i\n";
            $i=$i+10000;

             $observation_records=array();
             $observation_taxon_records=array();
             $taxon_records=array();

             foreach ($chunk as $record) {

                 if ($record['taxon_id']!="" && $record['taxon_species_name']!="")  {

                    $pointWKT = "POINT({$record['longitude']} {$record['latitude']})";

                    // Observation dans la zone de réference ?
                    $isInside = DB::selectOne("
                        SELECT ST_Contains(?, ST_SRID(ST_PointFromText(?), 4326)) AS inside", [$communeGeom, $pointWKT]);

                    if ($isInside->inside) {

                        $observation_records [] = [
                            'id' => $record['id'],
                            'taxon_id' => $record['taxon_id'],
                            'observed_on' => $record['observed_on'],
                            'observed_by' => $record['user_id'],
                            'license' => $record['license'],
                            'latitude' => $record['latitude'],
                            'longitude' => $record['longitude'],
                            'quality' =>($record['quality_grade']=='research')?'R':'N',
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                        $observation_taxon_records [] = [
                            'id' => $record['taxon_id'],
                            'scientific_name' => $record['scientific_name'],
                            'common_name' => $record['common_name'],
                            'kingdom' => $record['taxon_kingdom_name'],
                            'phylum' => $record['taxon_phylum_name'],
                            'subphylum' => $record['taxon_subphylum_name'],
                            'class' => $record['taxon_class_name'],
                            'subclass' => $record['taxon_subclass_name'],
                            'order' => $record['taxon_order_name'],
                            'suborder' => $record['taxon_suborder_name'],
                            'family' => $record['taxon_family_name'],
                            'subfamily' => $record['taxon_subfamily_name'],
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                    }
                }
             }

             if (!empty($observation_records)) {

                foreach ($observation_records as $key=>$observation) {
                    if ($previous_taxon_id!=$observation['taxon_id']) {
                        $previous_taxon_id=$observation['taxon_id'];


                        $taxon_records [] = [
                            'id' => $observation_taxon_records[$key]['id'],
                            'scientific_name' => $observation_taxon_records[$key]['scientific_name'],
                            'common_name' => $observation_taxon_records[$key]['common_name'],
                            'kingdom' => $observation_taxon_records[$key]['kingdom'],
                            'phylum' => $observation_taxon_records[$key]['phylum'],
                            'subphylum' => $observation_taxon_records[$key]['subphylum'],
                            'class' => $observation_taxon_records[$key]['class'],
                            'subclass' => $observation_taxon_records[$key]['subclass'],
                            'order' => $observation_taxon_records[$key]['order'],
                            'suborder' => $observation_taxon_records[$key]['suborder'],
                            'family' => $observation_taxon_records[$key]['family'],
                            'subfamily' => $observation_taxon_records[$key]['subfamily'],
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                    }

                }

             }



            if (!empty($taxon_records)) {
                DB::table('taxa')->insert($taxon_records);
            }

            if (!empty($observation_records)) {
                DB::table('observations')->insert($observation_records);
            }

        }




    }

}
