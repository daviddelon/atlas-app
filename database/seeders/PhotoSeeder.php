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

class PhotoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


        // No table seeded, used only to fetch photos


        $stream = fopen('/var/www/html/tmp/observations-582105.csv', 'r');
        $csv = Reader::createFromStream($stream);
        $csv->setHeaderOffset(0); //set the CSV header offset
        $csv->setEscape(''); //required in PHP8.4+ to avoid deprecation notices


        $stmt = new Statement()
        ->orderByAsc('taxon_id');


        $records = $stmt->process($csv);



        $api = new RestClient([
            'base_url' => "https://api.inaturalist.org/v1/taxa",
        ]);



        $client = new Client(); // Http client


        $previous_taxon_id=null;

        foreach ($records as $record) {

                if ($record['taxon_id']!="") {
                    if ($previous_taxon_id!=$record['taxon_id']) {
                        $previous_taxon_id=$record['taxon_id'];


                        if (! Storage::disk('public')->exists($record['taxon_id'].'.jpg')) {

                        echo $record['taxon_id']."\n";
                        echo "Pause 5 secondes \n";
                        sleep(5); // 60 requests per minute max for Inat.

                        $result = $api->get($record['taxon_id']);

                        if($result->info->http_code == 200) {
                            if (isset($result->decode_response()->results[0]->default_photo)) {
                                $default_photo_url=$result->decode_response()->results[0]->default_photo->medium_url;
                                $response = $client->get($default_photo_url);
                                $content=$response->getBody()->getContents();
                                Storage::disk('public')->put($record['taxon_id'].'.jpg',  $content);
                            }
                        }
                    }

                    }
                }

        }

    }

}
