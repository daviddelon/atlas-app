<?php

/**
 * DescriptionSeeder
 *
 * Ce seeder ajoute des descriptions enrichies à la table descriptions en utilisant des extraits de Wikipedia
 * améliorés par intelligence artificielle via Openrouter. Seuls les taxons marqués d'un "like" sont traités.
 *
 * Fonctionnement :
 * - Récupération des taxons par chunks de 50 pour optimisation
 * - Consultation de l'API Wikipedia pour obtenir un extrait si non existant
 * - Génération d'une description structurée en HTML via IA
 * - Mise à jour upsert dans la table descriptions
 *
 * Dépendances :
 * - API Wikipedia (fr.wikipedia.org)
 * - Service Openrouter pour l'IA
 * - Modèles Taxon et Description
 *
 *  Ce seeder n'est plus utilisé, car : les pages Wikipedia sont souvents absentes, pas forcemment en français, et le résultat du passage en LLM comprenait
 *  souvent des grossières erreurs, pas systématiquement, mais de façon aléatoire ce qui rend difficile l'obtention d'un résultat fiable. Le marquaqe par un
 *  Like, par l'administrateur, permettait de ne mettre à jour que les taxons marqués comme tels, les autres déscriptions pouvant être considérées comme bonne, ou
 *  alors pouvant être modifiées manuellement.
 *  Ce seeder est gardé comme référence, pour une reprise avec un autre base de description (flore de Coste)
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

class DescriptionSeederFromWikiPedia extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {



       // DB::table('descriptions')->delete();


        $api = new RestClient([
            'base_url' => "https://fr.wikipedia.org/api/rest_v1/page/summary",
            'user_agent' => env('wikipedia_user_agent')
        ]);


        $i=0;


        Taxon::with('description','like')
        ->orderBy('id')
        ->chunk(50, function (Collection $taxa) use ($api,&$i){

            print $i."\n";

            $i=$i+50;


            $records=array();

            foreach ($taxa as $taxon) {


                    if (isset ($taxon->like)) { // On ne cherche que les taxons marques comme à mettre à jour


                    $wikipedia_extract=null;
                    if (!isset ($taxon->description->wikipedia_extract)) {
                        $result = $api->get(urlencode(str_replace(' ', '_', ($taxon->common_name))));
                        if ($result->info->http_code == 200) {
                            $data = $result->decode_response();
                            $wikipedia_extract=$data->extract;
                        }
                    }
                    else {
                        print "Existing Wikipeda extract \n";
                        $wikipedia_extract=$taxon->description->wikipedia_extract;
                    }


                    if ($wikipedia_extract!=null) {

                        print $taxon->common_name."\n";
                        print $taxon->id."\n";

                        $content = <<<EOT



                        Tu es un expert naturaliste. Rédige une fiche descriptive pour "$taxon->common_name", en HTML uniquement.

                        Contraintes :
                        - Le texte ne doit pas être encadré par des balises ```html ou autre (aucun encadrement).
                        - Ne répète ni le nom scientifique, ni le nom français, ni de titre dans la description.
                        - Ne commence pas par un titre global.
                        - Structure le contenu avec des paragraphes thématiques.
                        - Chaque paragraphe doit commencer par un titre fort en gras, suivi d’un court texte informatif.
                        - Titres suggérés : Aspect, Habitat, Floraison (si plante), Comportement (si animal), Toxicité (si pertinent), Cycle, etc.
                        - Adapte les rubriques en fonction de l’espèce (plante, animal, etc.).
                        - N’ajoute aucune source, balise, note, ou commentaire.
                        - Sois très concis : tout le texte doit tenir en 400 caractères (espaces compris).
                        - Utilise un français simple, rigoureux, sans fioritures, ni sources, ni balises techniques.
                        - Toutes les phrases doivent être compréhensibles en français courant.
                        - Utilise un vocabulaire simple, adapté au grand public, sans jargon scientifique.
                        - Rédige uniquement en français. N’utilise aucun mot étranger.
                        - Aucun commentaire ou texte explicatif hors contenu.


                        Voici un résumé Wikipédia :
                        $wikipedia_extract


                        EOT;


                        //$model = 'mistralai/mistral-7b-instruct:free';
                        //$model = 'deepseek/deepseek-r1-0528-qwen3-8b:free';
                        $model = 'openai/gpt-4.1-nano';
                        $messageData = new MessageData(
                            content: $content,
                            role: RoleType::USER,
                        );

                        $chatData = new ChatData(
                        messages: [
                            $messageData,
                        ],
                        model: $model,
                        max_tokens: 3000,
                        );

                        $chatResponse = LaravelOpenRouter::chatRequest($chatData);
                        $content = Arr::get($chatResponse->choices[0], 'message.content');

                        print $content."\n";

                        $records [] = [
                        'id' => $taxon->description->id ?? null,
                        'taxon_id' => $taxon->id,
                        'content'=> $content,
                        'wikipedia_extract' => $wikipedia_extract,
                        'created_at' => now(),
                        'updated_at' => now()

                    ];

                    }
                    else {
                        print "No Wikipedia extract for $taxon->common_name\n";
                    }

                }

            }


            if (!empty($records)) {
                 DB::table('descriptions')->upsert($records,['id']);
            }


        });

    }



}
