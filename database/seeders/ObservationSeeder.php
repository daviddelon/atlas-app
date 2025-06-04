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
        DB::table('observations')->delete();

        $csv = Reader::createFromPath('/var/www/html/tmp/test_reduit.csv', 'r');
        $csv->setHeaderOffset(0); //set the CSV header offset
        $csv->setEscape(''); //required in PHP8.4+ to avoid deprecation notices



        foreach ($csv as $record) {

            Observation::firstOrCreate(
                [
                'id' => $record['id']
                ],
                [
                    'taxon_id' => $record['taxon_id'],
                    'observed_on' => $record['observed_on'],
                    'observed_by' => $record['user_id'],
                    'license' => $record['license'],
                    'latitude' => $record['latitude'],
                    'longitude' => $record['longitude'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );

        }

    }

}
