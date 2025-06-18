<?php

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

class DescriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {



       // DB::table('descriptions')->delete();


        $api = new RestClient([
            'base_url' => "https://fr.wikipedia.org/api/rest_v1/page/summary",
        ]);


        $i=0;


        Taxon::with('description')
        ->orderBy('id')
        ->chunk(100, function (Collection $taxa) use ($api,&$i){

            print $i."\n";

            $i=$i+20;

            foreach ($taxa as $taxon) {

                print "Sleep 4s \n";
                sleep(2);

                $wikipedia_extract=null;
                if (!isset ($taxon->description->wikipedia_extract)) {
                    $result = $api->get(str_replace(' ', '_', ($taxon->common_name)));
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

                    $content = <<<EOT
                    Tu es un expert naturaliste. Rédige une fiche descriptive au format markdown pour "$taxon->common_name".

                    Contraintes :
                    - N'encadre pas le résultat avec des balises ```markdown ou ``` (aucun encadrement).
                    - Ne commence pas par un titre général.
                    - Le contenu concerne uniquement la description
                    - N'utilise pas le nom scientifique ni le nom français dans la description, donne juste la description
                    - Sois très concis : tout le texte doit tenir en 400 caractères (espaces compris).
                    - Utilise un style naturaliste rigoureux, informatif, sans fioritures ni sources.
                    - Aucun ajout hors contenu : pas de balise, ni commentaire.
                    - Veille à ce que chaque phrase est une signification en langue française.
                    - Respecte l’orthographe scientifique et syntaxe claire.
                    - Utilise uniquement la langue française, n'utilise pas de mots anglais ni d'une autre langue.
                    - Retourne à la ligne quand il le faut pour rendre plus lisible le texte à l'écran. Utilise la syntaxe Markdown avec \ en fin de ligne pour cela.
                    - Utilse un vocabulaire grand public sans terme technique.

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

                    print($content);
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


            if (!empty($records)) {
                 DB::table('descriptions')->upsert($records,['id']);
            }
            exit;


        });

    }



}
