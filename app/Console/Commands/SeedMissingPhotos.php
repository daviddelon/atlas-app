<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Client;
use RestClient;

class SeedMissingPhotos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:missing-photos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed photos table records for image files in storage that lack DB entries, using iNaturalist API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Scanning storage for photo files without DB records...');

        $files = Storage::disk('public')->files();
        $jpgFiles = array_filter($files, fn($file) => str_ends_with($file, '.jpg'));

        $taxonIdsFromFiles = [];
        foreach ($jpgFiles as $file) {
            $taxonId = (int) pathinfo($file, PATHINFO_FILENAME);
            if ($taxonId > 0) {
                $taxonIdsFromFiles[] = $taxonId;
            }
        }

        $existingTaxonIds = \App\Models\Photo::whereIn('taxon_id', $taxonIdsFromFiles)->pluck('taxon_id')->toArray();

        $missingTaxonIds = array_diff($taxonIdsFromFiles, $existingTaxonIds);

        $this->info('Found ' . count($missingTaxonIds) . ' photo files without DB records.');

        if (!empty($missingTaxonIds)) {
            $this->info('Creating DB records from iNaturalist API...');

            $api_taxon = new RestClient([
                'base_url' => "https://api.inaturalist.org/v1/taxa",
            ]);

            $api_observations = new RestClient([
                'base_url' => "https://api.inaturalist.org/v1/observations",
            ]);

            $newRecords = [];

            foreach ($missingTaxonIds as $taxonId) {
                $this->line("Processing Taxon ID: $taxonId");

                // Try taxon API first
                sleep(1);
                $result = $api_taxon->get($taxonId);

                if ($result->info->http_code == 200) {
                    $data = $result->decode_response();

                    if (!empty($data->results) && !empty($data->results[0]->taxon_photos)) {
                        $preferredLicenses = ['cc-by-sa', 'cc-by', 'cc0'];
                        $selectedPhoto = null;
                        foreach ($data->results[0]->taxon_photos as $photoData) {
                            if (in_array($photoData->photo->license_code ?? '', $preferredLicenses)) {
                                $selectedPhoto = $photoData;
                                break;
                            }
                        }

                        if ($selectedPhoto) {
                            $newRecords[] = [
                                'id' => $selectedPhoto->photo->id,
                                'taxon_id' => $taxonId,
                                'author' => $selectedPhoto->photo->attribution_name ?? '',
                                'license' => $selectedPhoto->photo->license_code ?? '',
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];

                            $this->line("Created record for Taxon ID: $taxonId");
                        }
                    }
                }

                // If not found, try observations API
                if (empty($newRecords) || end($newRecords)['taxon_id'] !== $taxonId) {
                    sleep(1);
                    $result = $api_observations->get('', [
                        'taxon_id' => $taxonId,
                        'preferred_place_id' => 6753,
                        'order_by' => 'votes',
                        'quality_grade' => 'research',
                        'photo_license' => 'cc-by-sa,cc-by,cc0',
                        'per_page' => 1
                    ]);

                    if ($result->info->http_code == 200) {
                        $data = $result->decode_response();

                        if (!empty($data->results) && isset($data->results[0]->observation_photos[0])) {
                            $observationPhoto = $data->results[0]->observation_photos[0];

                            $newRecords[] = [
                                'id' => $observationPhoto->photo->id,
                                'taxon_id' => $taxonId,
                                'author' => $observationPhoto->photo->attribution ?? '',
                                'license' => $observationPhoto->photo->license_code ?? '',
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];

                            $this->line("Created record from observation for Taxon ID: $taxonId");
                        }
                    }
                }
            }

            if (!empty($newRecords)) {
                \Illuminate\Support\Facades\DB::table('photos')->insertOrIgnore($newRecords);
                $this->info('Created ' . count($newRecords) . ' new photo records.');
            } else {
                $this->warn('No new records could be created.');
            }
        } else {
            $this->info('All photo files have corresponding DB records.');
        }

        return 0;
    }
}
