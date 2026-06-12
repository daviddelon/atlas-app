<?php

/**
 * DescriptionFromCosteSeeder
 *
 * Ce seeder ajoute des descriptions enrichies à la table descriptions en utilisant les descriptions issues de la flore de Coste (domaine public). Ces descriptions
 *  sont stockes dans un base sqlite, calque du fichier export de la bdtxf (version V9_00) de Tela Botanica, augmenté des description récupérés depuis l'api
 *  (voir data/bdtfx)
 *
 * Fonctionnement :
 * - Récupération des taxons par chunks de 50 pour optimisation
 * - Insertion dans la table descriptions pour les taxons en correspondance sur le nom scientifique
 *
 * Dépendances :
 * - Base bdtfx
 * - Modèles Taxon et Description
 *
 *
 * @package Database\Seeders
 */

namespace Database\Seeders;

use App\Models\Taxon;
use DragonCode\Support\Facades\Helpers\Arr;
use GuzzleHttp\Client;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;
use League\Csv\Statement;
use MoeMizrak\LaravelOpenrouter\DTO\ChatData;
use MoeMizrak\LaravelOpenrouter\DTO\MessageData;
use MoeMizrak\LaravelOpenrouter\DTO\ResponseData;
use MoeMizrak\LaravelOpenrouter\Facades\LaravelOpenRouter;
use MoeMizrak\LaravelOpenrouter\Types\RoleType;
use RestClient;

class DescriptionFromCosteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Connect to the external bdtfx database using PDO
        $pdo = new \PDO('sqlite:/var/www/html/data/bdtfx/bdtfx_v9_00/bdtfx.db');

        DB::table('descriptions')->delete();

        $totalMatches = 0;
        $totalInserted = 0;

        Taxon::orderBy('id')
        ->chunk(50, function (Collection $taxa) use ($pdo, &$totalMatches, &$totalInserted){

            $records=array();
            $chunkMatches = 0;

            foreach ($taxa as $taxon) {
                // Fetch description from bdtfx.db where nom_sci matches scientific_name
                $stmt = $pdo->prepare('SELECT description FROM plants WHERE nom_sci = ?');
                $stmt->execute([$taxon->scientific_name]);
                $plant = $stmt->fetch(\PDO::FETCH_OBJ);

                if ($plant && !empty($plant->description)) {
                    $records [] = [
                        'taxon_id' => $taxon->id,
                        'content' => $plant->description,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                    $chunkMatches++;
                    $totalMatches++;
                }
                else {
                     $records [] = [
                        'taxon_id' => $taxon->id,
                        'content' => 'No description available (unknow name)',
                        'created_at' => now(),
                        'updated_at' => now()
                    ];

                }
            }

            if (!empty($records)) {
                DB::table('descriptions')->insert($records);
                $totalInserted += count($records);
                print "Inserted " . count($records) . " records in this chunk.\n";
            }

            print "Matches found in this chunk: " . $chunkMatches . "\n";
            print "Total matches so far: " . $totalMatches . "\n";
            print "Total inserted so far: " . $totalInserted . "\n\n";

        });

        print "Seeding completed. Total matches: " . $totalMatches . ", Total inserted: " . $totalInserted . "\n";
    }

}
