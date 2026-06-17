<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommuneSeeder extends Seeder
{
    public function run(): void
    {
        $file = base_path('data/communes-5m-2025-01-08.geojson');

        if (! file_exists($file)) {
            throw new \Exception("GeoJSON file not found at $file");
        }

        $this->command->info("Importing communes from $file...");

        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        DB::table('communes')->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

        $handle = fopen($file, 'rb');
        if (! $handle) {
            throw new \Exception("Cannot open file: $file");
        }

        $depth = 0;
        $buffer = '';
        $records = [];
        $total = 0;
        $insideString = false;
        $prevChar = '';
        $featuresStarted = false;
        $featuresEnded = false;

        $bar = $this->command->getOutput()->createProgressBar(35000);
        $bar->start();

        while (! feof($handle) && ! $featuresEnded) {
            $chunk = fread($handle, 65536);
            if ($chunk === false) break;

            for ($i = 0; $i < strlen($chunk); $i++) {
                $char = $chunk[$i];

                if ($char === '"' && $prevChar !== '\\') {
                    $insideString = ! $insideString;
                }

                if ($insideString) {
                    if ($featuresStarted && $depth > 0) {
                        $buffer .= $char;
                    }
                    $prevChar = $char;
                    continue;
                }

                if (! $featuresStarted) {
                    if ($char === '[') {
                        $featuresStarted = true;
                    }
                    $prevChar = $char;
                    continue;
                }

                if ($char === ']' && $depth === 0) {
                    $featuresEnded = true;
                    break;
                }

                if ($char === '{') {
                    if ($depth === 0) {
                        $buffer = '';
                    }
                    $buffer .= $char;
                    $depth++;
                } elseif ($char === '}') {
                    $buffer .= $char;
                    $depth--;

                    if ($depth === 0) {
                        $record = $this->processFeature($buffer);
                        $buffer = '';
                        if ($record) {
                            $records[] = $record;
                            $total++;
                            $bar->advance();

                            if (count($records) >= 100) {
                                DB::beginTransaction();
                                $this->insertBatch($records);
                                DB::commit();
                                $records = [];
                            }
                        }
                    }
                } elseif ($depth > 0) {
                    $buffer .= $char;
                }

                $prevChar = $char;
            }
        }

        if (! empty($records)) {
            DB::beginTransaction();
            $this->insertBatch($records);
            DB::commit();
        }

        fclose($handle);

        $bar->finish();
        $this->command->line('');
        $this->command->info("Inserted $total communes.");
    }

    private function processFeature(string $json): ?array
    {
        $feature = json_decode($json, true);

        if (! $feature || ! isset($feature['geometry'])) {
            return null;
        }

        $props = $feature['properties'] ?? [];
        $geom = $feature['geometry'];
        $type = $geom['type'] ?? '';
        $coords = $geom['coordinates'] ?? [];

        $wkt = match ($type) {
            'Polygon' => $this->validateCoords($coords) ? 'MULTIPOLYGON(' . $this->polygonCoords($coords) . ')' : null,
            'MultiPolygon' => (($inner = $this->multiPolygonCoords($coords)) !== '' ? 'MULTIPOLYGON(' . $inner . ')' : null),
            default => throw new \RuntimeException("Unsupported geometry type: $type"),
        };

        if ($wkt === null) return null;

        return [
            'wkt' => $wkt,
            'code' => $props['code'] ?? null,
            'nom' => $props['nom'] ?? null,
            'departement' => $props['departement'] ?? null,
            'region' => $props['region'] ?? null,
            'epci' => $props['epci'] ?? null,
        ];
    }

    private function validateCoords(array $coordinates): bool
    {
        foreach ($coordinates as $ring) {
            foreach ($ring as $point) {
                $lon = $point[0];
                $lat = $point[1];
                if ($lat < -90 || $lat > 90 || $lon < -180 || $lon > 180) {
                    return false;
                }
            }
        }
        return true;
    }

    private function polygonCoords(array $coordinates): string
    {
        $rings = [];
        foreach ($coordinates as $ring) {
            $points = array_map(fn ($p) => $p[0] . ' ' . $p[1], $ring);
            $rings[] = '(' . implode(', ', $points) . ')';
        }
        return '(' . implode(', ', $rings) . ')';
    }

    private function multiPolygonCoords(array $coordinates): string
    {
        $polygons = [];
        foreach ($coordinates as $poly) {
            if (! $this->validateCoords($poly)) {
                return '';
            }
            $polygons[] = $this->polygonCoords($poly);
        }
        return implode(', ', $polygons);
    }

    private function insertBatch(array $records): void
    {
        try {
            $this->doInsert($records);
        } catch (\Exception $e) {
            if (count($records) === 1) {
                $this->command->warn("Skipping commune {$records[0]['code']} (invalid geometry)");
                return;
            }
            $mid = intdiv(count($records), 2);
            $this->insertBatch(array_slice($records, 0, $mid));
            $this->insertBatch(array_slice($records, $mid));
        }
    }

    private function doInsert(array $records): void
    {
        $values = [];
        $bindings = [];

        foreach ($records as $r) {
            $values[] = '(ST_GeomFromText(?, 4326), ?, ?, ?, ?, ?)';
            $bindings[] = $r['wkt'];
            $bindings[] = $r['code'];
            $bindings[] = $r['nom'];
            $bindings[] = $r['departement'];
            $bindings[] = $r['region'];
            $bindings[] = $r['epci'];
        }

        $sql = 'INSERT INTO communes (geom, code, nom, departement, region, epci) VALUES ' . implode(', ', $values);
        DB::statement($sql, $bindings);
    }
}
