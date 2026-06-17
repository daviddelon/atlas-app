<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SeedCommune extends Command
{
    protected $signature = 'seed:commune {code : The commune code to extract} {file? : The master GeoJSON file path} {output? : The output GeoJSON file path}';
    protected $description = 'Extract a single commune GeoJSON from the master file';

    public function handle(): int
    {
        $code = $this->argument('code');
        $master = $this->argument('file') ?? base_path('data/communes-5m-2025-01-08.geojson');
        $output = $this->argument('output') ?? base_path("data/{$code}.geojson");

        if (! file_exists($master)) {
            $this->error("Master file not found: $master");
            return 1;
        }

        $this->info("Extracting commune $code from master file...");

        $handle = fopen($master, 'rb');
        if (! $handle) {
            $this->error("Cannot open master file: $master");
            return 1;
        }

        $depth = 0;
        $buffer = '';
        $insideString = false;
        $isEscaped = false;
        $featuresStarted = false;

        while (! feof($handle)) {
            $chunk = fread($handle, 65536);
            if ($chunk === false) break;

            for ($i = 0; $i < strlen($chunk); $i++) {
                $char = $chunk[$i];

                if ($char === '"' && ! $insideString) {
                    $insideString = true;
                    $isEscaped = false;
                } elseif ($char === '"' && $insideString && ! $isEscaped) {
                    $insideString = false;
                } elseif ($char === '\\' && $insideString) {
                    $isEscaped = ! $isEscaped;
                } else {
                    $isEscaped = false;
                }

                if ($featuresStarted && $depth > 0) {
                    $buffer .= $char;
                }

                if (! $featuresStarted) {
                    if ($char === '[') {
                        $featuresStarted = true;
                    }
                    continue;
                }

                if ($insideString) {
                    continue;
                }

                if ($char === '{') {
                    $depth++;
                    if ($depth === 1) {
                        $buffer = '{';
                    }
                } elseif ($char === '}') {
                    if ($depth === 1) {
                        $feature = json_decode($buffer, true);
                        $buffer = '';
                        if (($feature['properties']['code'] ?? null) === $code) {
                            $outputJson = json_encode([
                                'type' => 'FeatureCollection',
                                'features' => [$feature],
                            ], JSON_UNESCAPED_UNICODE);
                            file_put_contents($output, $outputJson);
                            $this->info("Extracted commune $code to $output");
                            fclose($handle);
                            return 0;
                        }
                    }
                    $depth--;
                }
            }
        }

        fclose($handle);
        $this->error("Commune $code not found in master file.");
        return 1;
    }
}
