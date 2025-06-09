<?php

namespace Database\Seeders;

use App\Models\Observation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;

class ObservationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {



        DB::disableQueryLog();


        $stream = fopen('/var/www/html/tmp/observations-582105.csv', 'r');
        $csv = Reader::createFromStream($stream);
        $csv->setHeaderOffset(0); //set the CSV header offset
        $csv->setEscape(''); //required in PHP8.4+ to avoid deprecation notices


        //$csv->chunkBy(1000); // For performances reasons : a magnitude faster !


        foreach ($csv->chunkBy(1000) as $chunk) {

             $records=array();

             foreach ($chunk as $record) {

                if ($record['taxon_id']!="") {
                    $records [] = [
                    'id' => $record['id'],
                    'taxon_id' => $record['taxon_id'],
                    'observed_on' => $record['observed_on'],
                    'observed_by' => $record['user_id'],
                    'license' => $record['license'],
                    'latitude' => $record['latitude'],
                    'longitude' => $record['longitude'],
                    'created_at' => now(),
                    'updated_at' => now()
                    ];
                }

             }


             DB::table('observations')->insert($records);
        }


    }

}
