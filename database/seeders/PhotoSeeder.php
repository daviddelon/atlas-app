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

class PhotoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {




        DB::table('photos')->delete();

        $api_taxon = new RestClient([
            'base_url' => "https://api.inaturalist.org/v1/taxa",
        ]);


        $api_observations = new RestClient([
            'base_url' => "https://api.inaturalist.org/v1/observations",
        ]);


        $client = new Client(); // Http client

        $i=0;

        $not_found_ids=array();


        DB::table('taxa')->orderBy('id')->chunk(25, function (Collection $taxa) use ($api_taxon, $client,&$i, &$not_found_ids){

            print $i."\n";

            $i=$i+25;


            $records=array();
            $ids=array();
            foreach ($taxa as $taxon) {
                if (!Storage::disk('public')->exists($taxon->id.'.jpg')) {
                            $ids []=$taxon->id;
                }
            }

            // Requete sur plusieurs ids, pour limiter le nombre d'appel
            if (!empty($ids)) {

                print "Sleep 4s \n";
                sleep(4);
                $result = $api_taxon->get(implode(",", $ids));


                if($result->info->http_code == 200) {

                    $data = $result->decode_response();

                    foreach ($data->results as $index => $taxon) {

                        // Find the first photo with preferred license in order: cc-by-sa, cc-by, cc0
                        $preferredLicenses = ['cc-by-sa', 'cc-by', 'cc0'];
                        $selectedPhoto = null;
                        foreach ($taxon->taxon_photos as $photoData) {
                            if (in_array($photoData->photo->license_code ?? '', $preferredLicenses)) {
                                $selectedPhoto = $photoData;
                                break;
                            }
                        }

                        if ($selectedPhoto) {
                            // Download the medium image
                            $response = $client->get($selectedPhoto->photo->medium_url);
                            $content = $response->getBody()->getContents();
                            Storage::disk('public')->put($taxon->id . '.jpg', $content);

                            $records[] = [
                                'id' => $selectedPhoto->photo->id,
                                'taxon_id' => $taxon->id,
                                'author' => $selectedPhoto->photo->attribution_name ?? '',
                                'license' => $selectedPhoto->photo->license_code ?? '',
                                'created_at' => now(),
                                'updated_at' => now()
                            ];

                            print "Selected Photo ID: " . $selectedPhoto->photo->id . " for Taxon ID: " . $taxon->id . " with License: " . ($selectedPhoto->photo->license_code ?? 'N/A') . "\n";
                        } else {
                            $not_found_ids []=$taxon->id;
                        }

                    }
                    if (!empty($records)) {
                        DB::table('photos')->upsert($records,['id']);
                    }
                }
            }

        });




        if (!empty($not_found_ids)) {

            foreach ($not_found_ids as $taxonId){

                print "Processing taxon ID: " . $taxonId . "\n";
                print "Sleep 1s \n";
                sleep(1);
                 $result = $api_observations->get('',[
                        'taxon_id' => $taxonId,
                        'preferred_place_id' => 6753,
                        'order_by' => 'votes',
                        'quality_grade' => 'research',
                        'photo_license' => 'cc-by-sa,cc-by,cc0',
                        'per_page' => 1
                 ]);


                if($result->info->http_code == 200) {
                    $data = $result->decode_response();

                    if (!empty($data->results) && isset($data->results[0]->observation_photos[0])) {
                        $observationPhoto = $data->results[0]->observation_photos[0];

                        // Construct medium URL from square URL
                        $squareUrl = $observationPhoto->photo->url;
                        $mediumUrl = str_replace('/square.', '/medium.', $squareUrl);

                        // Download the photo
                        $response = $client->get($mediumUrl);
                        $content = $response->getBody()->getContents();
                        Storage::disk('public')->put($taxonId . '.jpg', $content);

                        $records[] = [
                            'id' => $observationPhoto->photo->id,
                            'taxon_id' => $taxonId,
                            'author' => $observationPhoto->photo->attribution ?? '',
                            'license' => $observationPhoto->photo->license_code ?? '',
                            'created_at' => now(),
                            'updated_at' => now()
                        ];

                        print "Downloaded observation photo ID: " . $observationPhoto->photo->id . " for Taxon ID: " . $taxonId . "\n";
                    } else {
                        print "No suitable observation photos found for Taxon ID: " . $taxonId . "\n";
                    }
                } else {
                    print "API error for Taxon ID: " . $taxonId . "\n";
                }

            }

            if (!empty($records)) {
                DB::table('photos')->insert($records);
                print "Inserted " . count($records) . " observation photos.\n";
            }

       }

/*
                print "Sleep 4s \n";
                sleep(4);
                $result = $api_taxon->get(implode(",", $ids));

        print "Total taxa without suitable photos: " . count($not_found_ids) . "\n";
        foreach ($not_found_ids as $id) {
            print($id."\n");
        }*/

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
