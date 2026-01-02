<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;
use League\Csv\Statement;

class SeedTaxonObservations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:taxon-observations {code : The commune code to seed observations for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed taxon observations for a specific commune from CSV';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $code = $this->argument('code');

        if (!$code) {
            $this->error('Code commune requis.');
            return 1;
        }

        $this->info("Seeding pour la commune code: $code");

        $communeGeom = DB::table('communes')
            ->where('code', $code)
            ->value('geom');

        if (!$communeGeom) {
            $this->error("Commune $code introuvable.");
            return 1;
        }

        DB::disableQueryLog();

        // Supprimer les observations existantes pour cette commune
        DB::table('observations')->where('code', $code)->delete();

        $stream = fopen('/var/www/html/data/observations-658902.csv', 'r');
        $csv = Reader::createFromStream($stream);
        $csv->setHeaderOffset(0);
        $csv->setEscape('');

        $stmt = new Statement();
        $stmt = $stmt->orderByAsc('taxon_id');

        $all = $stmt->process($csv);

        $previous_taxon_id = null;

        $i = 0;
        foreach ($all->chunkBy(10000) as $chunk) {
            print "$i\n";
            $i = $i + 10000;

            $observation_records = [];
            $observation_taxon_records = [];
            $taxon_records = [];

            foreach ($chunk as $record) {
                if ($record['taxon_id'] != "" && $record['taxon_species_name'] != "" && $record['taxon_kingdom_name'] == 'Plantae') {
                    $pointWKT = "POINT({$record['longitude']} {$record['latitude']})";

                    $isInside = DB::selectOne("
                        SELECT ST_Contains(?, ST_SRID(ST_PointFromText(?), 4326)) AS inside", [$communeGeom, $pointWKT]);

                    if ($isInside->inside) {
                        $observation_records[] = [
                            'id' => $record['id'],
                            'taxon_id' => $record['taxon_id'],
                            'observed_on' => $record['observed_on'],
                            'observed_by' => $record['user_id'],
                            'license' => $record['license'],
                            'latitude' => $record['latitude'],
                            'longitude' => $record['longitude'],
                            'quality' => ($record['quality_grade'] == 'research') ? 'R' : 'N',
                            'code' => $code,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                        $observation_taxon_records[] = [
                            'id' => $record['taxon_id'],
                            'scientific_name' => $record['scientific_name'],
                            'common_name' => $record['common_name'],
                            'kingdom' => $record['taxon_kingdom_name'],
                            'phylum' => $record['taxon_phylum_name'],
                            'subphylum' => $record['taxon_subphylum_name'],
                            'class' => $record['taxon_class_name'],
                            'subclass' => $record['taxon_subclass_name'],
                            'order' => $record['taxon_order_name'],
                            'suborder' => $record['taxon_suborder_name'],
                            'family' => $record['taxon_family_name'],
                            'subfamily' => $record['taxon_subfamily_name'],
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                    }
                }
            }

            if (!empty($observation_records)) {
                foreach ($observation_records as $key => $observation) {
                    if ($previous_taxon_id != $observation['taxon_id']) {
                        $previous_taxon_id = $observation['taxon_id'];

                        $taxon_records[] = [
                            'id' => $observation_taxon_records[$key]['id'],
                            'scientific_name' => $observation_taxon_records[$key]['scientific_name'],
                            'common_name' => $observation_taxon_records[$key]['common_name'],
                            'kingdom' => $observation_taxon_records[$key]['kingdom'],
                            'phylum' => $observation_taxon_records[$key]['phylum'],
                            'subphylum' => $observation_taxon_records[$key]['subphylum'],
                            'class' => $observation_taxon_records[$key]['class'],
                            'subclass' => $observation_taxon_records[$key]['subclass'],
                            'order' => $observation_taxon_records[$key]['order'],
                            'suborder' => $observation_taxon_records[$key]['suborder'],
                            'family' => $observation_taxon_records[$key]['family'],
                            'subfamily' => $observation_taxon_records[$key]['subfamily'],
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                    }
                }
            }

            if (!empty($taxon_records)) {
                DB::table('taxa')->insertOrIgnore($taxon_records);
            }

            if (!empty($observation_records)) {
                DB::table('observations')->insert($observation_records);
            }
        }

        $this->info('Seeding completed.');
        return 0;
    }
}
