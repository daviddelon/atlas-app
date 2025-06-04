<?php

namespace Database\Seeders;

use App\Models\Taxon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;
use League\Csv\Statement;
use RestClient;

class TaxonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {



        $api = new RestClient([
            'base_url' => "https://api.inaturalist.org/v1/taxa",
        ]);

        $result = $api->get("209304");


        if($result->info->http_code == 200)
        print_r($result->decode_response()->results[0]->default_photo->medium_url);

        exit;

        DB::table('taxa')->delete();

        $csv = Reader::createFromPath('/var/www/html/tmp/test_reduit.csv', 'r');
        $csv->setHeaderOffset(0); //set the CSV header offset
        $csv->setEscape(''); //required in PHP8.4+ to avoid deprecation notices



        foreach ($csv as $record) {

            if ($record['taxon_id']!="") {// It can occurs, strange isn'it ?
                Taxon::firstOrCreate(
                    [
                    'id' => $record['taxon_id']
                    ],
                    [
                        'scientific_name' => $record['scientific_name'],
                        'common_name' => $record['common_name'],
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );
            }

        }

    }

}
