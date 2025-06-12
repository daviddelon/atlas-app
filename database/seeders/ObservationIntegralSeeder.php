<?php

namespace Database\Seeders;

use App\Models\Observation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;
use League\Csv\Writer;

class ObservationIntegralSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {



        DB::disableQueryLog();


       // DB::table('observations')->delete();


        $stream = fopen('/var/www/html/tmp/observations.csv', 'r');
        $csv = Reader::createFromStream($stream);
        $csv->setHeaderOffset(0); //set the CSV header offset
        $csv->setEscape(''); //required in PHP8.4+ to avoid deprecation notices


        $writer = Writer::createFromPath('/var/www/html/tmp/france.csv', 'w+');



        $i=0;
        foreach ($csv->chunkBy(10000) as $chunk) {

             echo "$i \n";
             $i=$i+10000;
            $records=array();

            foreach ($chunk as $record) {
                if ($record['countryCode']=='FR') {


                    $records [] = $record;

                }

            }
            $writer->insertAll($records);


        }



// select * from `communes` where ST_Contains(geom, ST_SRID(Point(.3.7323530000, 3.7323530000), 4326)))

// select * from `communes` where ST_Contains(geom, ST_SRID(Point(.3.7323530000, 3.7323530000), 4326))
 //select * from `communes` where ST_Contains(geom, ST_SRID(Point(3.7323530000, 43.7624630000), 4326))
//SELECT * FROM communes
//WHERE ST_Contains(geom, ST_SRID(Point(2.35, 48.85), 4326));
    }

}
