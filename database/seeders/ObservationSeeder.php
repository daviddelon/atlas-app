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


        DB::table('observations')->delete();

        $communeGeom = DB::table('communes')
            ->where('code', '34274')
            ->value('geom');

        if (!$communeGeom) {
            throw new \Exception("Commune 34274 introuvable.");
        }

        $stream = fopen('/var/www/html/tmp/observations-583469.csv', 'r');
        $csv = Reader::createFromStream($stream);
        $csv->setHeaderOffset(0); //set the CSV header offset
        $csv->setEscape(''); //required in PHP8.4+ to avoid deprecation notices



        $i=0;
        foreach ($csv->chunkBy(10000) as $chunk) {

             echo "$i \n";
             $i=$i+10000;

             $records=array();

             foreach ($chunk as $record) {

                if ($record['taxon_id']!="" && $record['taxon_species_name']!='')  {

                    $pointWKT = "POINT({$record['longitude']} {$record['latitude']})";

                    $isInside = DB::selectOne("
                        SELECT ST_Contains(?, ST_SRID(ST_PointFromText(?), 4326)) AS inside", [$communeGeom, $pointWKT]);

                    if ($isInside->inside) {

                        $records [] = [
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
                    }
                }

             }

             if (!empty($records)) {
                DB::table('observations')->insert($records);
             }
        }




// select * from `communes` where ST_Contains(geom, ST_SRID(Point(.3.7323530000, 3.7323530000), 4326)))

// select * from `communes` where ST_Contains(geom, ST_SRID(Point(.3.7323530000, 3.7323530000), 4326))
 //select * from `communes` where ST_Contains(geom, ST_SRID(Point(3.7323530000, 43.7624630000), 4326))
//SELECT * FROM communes
//WHERE ST_Contains(geom, ST_SRID(Point(2.35, 48.85), 4326));
    }

}
