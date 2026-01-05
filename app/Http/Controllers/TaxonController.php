<?php

namespace App\Http\Controllers;

use App\Models\Taxon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\CommonMark\CommonMarkConverter;

;

class TaxonController extends Controller
{


    private const KINGDOMS = [
        'plantes' => 'Plantae',
        'animaux' => 'Animalia',
    ];


//Magnoliopsida
//Orchidaceae


//Liliopsida
//Lamiaceae

    public const CLASSES = [


        // plantes

        'angiospermes' => [
            'Magnoliopsida',
            'Liliopsida'
        ],
        'gymnospermes' => [
            'Pinopsida'
        ],
        'fougeres'    => [
            'Polypodiopsida'
        ],
        'mousses'    => [
            'Bryopsida',
            'Jungermanniopsida',
            'Marchantiopsida'
        ],

        //animaux
        'mammiferes' => [
            'Mammalia'
        ],
        'oiseaux'    => [
            'Aves'
        ],
        'insectes'   => [
            'Insecta'
        ],
        'reptiles'   => [
            'Reptilia'
        ],
    ];


     public const FAMILIES = [


        // plantes

        'orchidees' =>  'Orchidaceae',
        'lamiacees' =>  'Lamiaceae',
        'asteracees' =>  'Asteraceae',




    ];



    /**
     * Display a listing of the resource.
     */
    public function index(Request$request )
    {


    }



    public function taxaFiltre(Request $request, string $kingdom_slug, string $class_slug, ?string $family_slug="")
    {



        $kingdom = self::KINGDOMS[$kingdom_slug] ?? null;
        $classes = self::CLASSES[$class_slug] ?? null;
        $family = self::FAMILIES[$family_slug] ?? null;

        // If family not found in FAMILIES, assume family_slug is a slug of the family name
        if (!$family && $family_slug) {
            $family = Str::title(str_replace('-', ' ', $family_slug));
        }

        $communeCode = session('current_commune_code');

        $zoomLevel = config('app.commune_zooms')[$communeCode] ?? 12;

        if (!$classes) {
            abort(404, "Groupe taxonomique inconnu");
        }

        $categories = [];
        if ($class_slug === 'angiospermes') {
            $communeCode = session('current_commune_code');
            $topFamilies = DB::table('observations as o')
                ->join('taxa as t', 'o.taxon_id', '=', 't.id')
                ->where('o.code', session('current_commune_code'))
                ->whereIn('t.class', $classes)
                ->where('t.kingdom', $kingdom)
                ->whereNotNull('t.family')
                ->select('t.family', DB::raw('count(distinct o.taxon_id) as count'))
                ->groupBy('t.family')
                ->orderBy('count', 'desc')
                ->get();

            // Si pas de family_slug spécifiée, rediriger vers la famille la plus observée
            if (!$family_slug && $topFamilies->isNotEmpty()) {
                $topFamily = $topFamilies->first()->family;
                $slug = array_search($topFamily, self::FAMILIES) ?: Str::slug($topFamily);
                return redirect("/plantes/angiospermes/{$slug}");
            }

            foreach ($topFamilies as $fam) {
                $mostObservedTaxon = Taxon::where('family', $fam->family)
                    ->whereHas('photo')
                    ->with('photo')
                    ->withCount(['observations' => function ($q) use ($communeCode) {
                        $q->where('code', $communeCode);
                    }])
                    ->havingRaw('observations_count > 0')
                    ->orderBy('observations_count', 'desc')
                    ->first();

                if (!$mostObservedTaxon || !$mostObservedTaxon->photo) {
                    continue; // Skip families without a photo for the most observed taxon
                }

                $img = $mostObservedTaxon->default_photo_url();

                // Find the slug key from FAMILIES if exists, else use slug of family
                $slug = array_search($fam->family, self::FAMILIES) ?: Str::slug($fam->family);
                $url = '/plantes/angiospermes/' . $slug;

                $categories[] = [
                    'url' => $url,
                    'img' => $img,
                    'label' => $fam->family,
                    'count' => $fam->count,
                ];
            }
        }

        $taxa = Taxon::whereHas('observations', function (Builder $query) use ($classes, $kingdom, $family) {
                $query
                    ->where('kingdom', $kingdom)
                    ->whereIn('class', $classes)
                    ->whereLike('family', '%'.$family.'%')
                    ->where('code', session('current_commune_code'));

            })
            ->with(['observations' => function ($q) { $q->where('code', session('current_commune_code')); }, 'photo', 'description', 'like'])
            ->withCount(['observations' => function ($q) { $q->where('code', session('current_commune_code')); }])
            ->orderBy('observations_count', 'desc')
            ->orderBy('id', 'asc')
            ->paginate(10);

        $vue = $request->query('vue', 'default');
        return view('atlas', compact('taxa', 'vue', 'categories', 'zoomLevel'));
    }






    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Taxon $taxon)
    {


    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Taxon $taxon)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Taxon $taxon)
    {
        //
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Taxon $taxon)
    {
        //
    }
}
