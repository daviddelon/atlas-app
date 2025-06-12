<?php

namespace Database\Seeders;

use App\Models\Taxon;
use GuzzleHttp\Client;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;
use League\Csv\Statement;
use RestClient;

class DescriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {




        $api = new RestClient([
            'base_url' => "https://api.inaturalist.org/v1/taxa",
        ]);

        $client = new Client(); // Http client

        $i=0;

        DB::table('taxa')->orderBy('id')->chunk(25, function (Collection $taxa) use ($api,$client,&$i){

            print $i."\n";

            $i=$i+25;



            $records=array();
            $ids=array();
            foreach ($taxa as $taxon) {
                $ids []=$taxon->id;
            }

            if (!empty($ids)) {

                print "Sleep 4s \n";
                sleep(4);
                $data = $api->get(implode(",", $ids));
                $data = $data->decode_response();


                foreach ($data->results as $index => $taxon) {

                    if (isset($taxon->default_photo)) {
                        $description = $client->get($taxon->default_photo->medium_url);
                        $records [] = [
                            'id' => $taxon->default_photo->id,
                            'taxon_id' => $taxon->id,
                            'author' => $taxon->default_photo->attribution_name,
                            'license' =>  $taxon->default_photo->license_code,
                            'created_at' => now(),
                            'updated_at' => now()

                        ];
                    }

                }
                if (!empty($records)) {
                    DB::table('photos')->upsert($records,['id']);
             }
            }

        });

    }

/*
        foreach ($records as $record) {

                if ($record['taxon_id']!="") {
                    if ($previous_taxon_id!=$record['taxon_id']) {
                        $previous_taxon_id=$record['taxon_id'];

                           // if (! Storage::disk('public')->exists($record['taxon_id'].'.jpg')) {

                            echo $record['taxon_id']."\n";
                            echo "Pause 5 secondes \n";
                            sleep(5); // 60 requests per minute max for Inat.

                            $result = $api->get($record['taxon_id']);

                            if($result->info->http_code == 200) {
                                print_r($result->decode_response()->results[0]);
                                if (isset($result->decode_response()->results[0]->default_photo)) {
                                    //$default_photo_url=$result->decode_response()->results[0]->default_photo->medium_url;
                                    //$response = $client->get($default_photo_url);
                                    //$content=$response->getBody()->getContents();
                                    //Storage::disk('public')->put($record['taxon_id'].'.jpg',  $content);
                                }
                                //photo
                                //attribution_name
                                //license_code
                                exit;
                            }
                        //}

                    }
                }

        }*/

}
