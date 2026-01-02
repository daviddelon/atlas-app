<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommuneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $geojsonFile = base_path('data/communes-5m-2025-01-08.geojson'); // Use test file for quick testing
        $tableName = 'communes';

        if (!file_exists($geojsonFile)) {
            throw new \Exception('GeoJSON file not found at ' . $geojsonFile);
        }

        $dbConfig = config('database.connections.mysql');
        $dbName = $dbConfig['database'];
        $dbUser = $dbConfig['username'];
        $dbPass = $dbConfig['password'];
        $dbHost = $dbConfig['host'];
        $dbPort = $dbConfig['port'] ?? 3306;

        $this->command->info('Starting communes import using ogr2ogr...');

        // Delete existing data to ensure clean import
        DB::table($tableName)->delete();

        // Étape 1: Import avec ogr2ogr
        $ogrCommand = "ogr2ogr -progress -f 'MySQL' MYSQL:'{$dbName},user={$dbUser},password={$dbPass},host={$dbHost},port={$dbPort}' '{$geojsonFile}' -nln '{$tableName}' -nlt GEOMETRY -lco GEOMETRY_NAME=geom -lco ENGINE=InnoDB -lco SPATIAL_INDEX=YES";
        $output = [];
        $returnVar = 0;
        exec($ogrCommand . " 2>&1", $output, $returnVar);

        foreach ($output as $line) {
            $this->command->info($line);
        }

        if ($returnVar !== 0) {
            throw new \Exception("ogr2ogr import failed with exit code $returnVar.");
        }

        $this->command->info('ogr2ogr import completed. Running post-processing SQL...');

        // Étape 2: Post-traitement SQL
        DB::statement("ALTER TABLE {$tableName} MODIFY geom GEOMETRY NOT NULL SRID 4326;");
        DB::statement("UPDATE {$tableName} SET geom = ST_SRID(geom, 4326) WHERE ST_SRID(geom) != 4326;");

        $this->command->info('Post-processing completed. Communes import successful.');
    }
}
