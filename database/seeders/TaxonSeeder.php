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

class TaxonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


        //DB::table('taxa')->delete();

        $csv = Reader::createFromPath('/var/www/html/tmp/observations-582105.csv', 'r');
        $csv->setHeaderOffset(0); //set the CSV header offset
        $csv->setEscape(''); //required in PHP8.4+ to avoid deprecation notices

        $api = new RestClient([
            'base_url' => "https://api.inaturalist.org/v1/taxa",
        ]);


        $client = new Client(); // Http client

        foreach ($csv as $record) {

            if ($record['taxon_id']!="") {

                Taxon::where('id',$record['taxon_id'] )->firstOr(function () use ($record, $api, $client) {



                    if (! Storage::disk('public')->exists($record['taxon_id'].'.jpg')) {

                        echo $record['taxon_id']."\n";
                        echo "Pause 5 secondes \n";
                        sleep(5); // 60 requests per minute max for Inat.

                        $result = $api->get($record['taxon_id']);

                        if($result->info->http_code == 200) {
                            $default_photo_url=$result->decode_response()->results[0]->default_photo->medium_url;
                            $response = $client->get($default_photo_url);
                            $content=$response->getBody()->getContents();
                                Storage::disk('public')->put($record['taxon_id'].'.jpg',  $content);
                        }
                    }


                    return Taxon::create([
                        'id' => $record['taxon_id'],
                        'scientific_name' => $record['scientific_name'],
                        'common_name' => $record['common_name'],
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                });


            }

        }

    }

}
