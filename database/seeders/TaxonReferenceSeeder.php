<?php

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

class TaxonReferenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

/* Abandonne : prevu pour fonctionner avec cette table :

    Schema::create('taxa', function (Blueprint $table) {
            $table->id();
            $table->string('scientific_name');
            $table->string('common_name')->nullable();
            $table->string('rank');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->timestamps();
        });

*/

        DB::disableQueryLog();




        DB::table('observations')->delete();
        DB::table('taxa')->delete();

        $stream = fopen('/var/www/html/tmp/dwca/taxa.csv', 'r'); // from https://www.inaturalist.org/taxa/inaturalist-taxonomy.dwca.zip
        // verna dans un fichier separe

        $csv = Reader::createFromStream($stream);
        $csv->setHeaderOffset(0); //set the CSV header offset
        $csv->setEscape(''); //required in PHP8.4+ to avoid deprecation notices





        $client = new Client(); // Http client

        $api = new RestClient([
            'base_url' => "https://api.inaturalist.org/v1/taxa",
        ]);

        $csv->chunkBy(1000); // For performances reasons : a magnitude faster !


        foreach ($csv->chunkBy(1000) as $chunk) {

             $records=array();

             foreach ($chunk as $record) {


                $records [] = [
                'id' => $record['id'],
                'scientific_name' => $record['scientificName'],
                'rank' => $record['taxonRank'],
                'created_at' => now(),
                'updated_at' => now()
                ];

             }


             DB::table('taxa')->insert($records);
        }



            // TODO : recuperer le rang et le nom associe au rang
            // $record['taxonRank']
            // parentNameUsageID
            // ver
            //kingdom,phylum,class,order,family,genus,specificEpithet,infraspecificEpithet



    }

}
