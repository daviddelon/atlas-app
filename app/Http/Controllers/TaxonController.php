<?php

namespace App\Http\Controllers;

use App\Models\Taxon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TaxonController extends Controller
{
    // Exemple : /plantes/angiospermes/asteraceae
    // /   kingdom/classes/families

    private const KINGDOMS = [
        'plantes' => 'Plantae',
        'animaux' => 'Animalia',
    ];

    public const CLASSES = [

        // plantes

        'angiospermes' => [
            'Magnoliopsida',
            'Liliopsida',
        ],
        'gymnospermes' => [
            'Pinopsida',
        ],
        'fougeres' => [
            'Polypodiopsida',
        ],
        'mousses' => [
            'Bryopsida',
            'Jungermanniopsida',
            'Marchantiopsida',
        ],

        // animaux
        'mammiferes' => [
            'Mammalia',
        ],
        'oiseaux' => [
            'Aves',
        ],
        'insectes' => [
            'Insecta',
        ],
        'reptiles' => [
            'Reptilia',
        ],
    ];

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) {}

    public function taxaFiltre(Request $request, string $kingdom_slug, string $class_slug, ?string $family_slug = '')
    {

        // Traduction slug / kingdom/classe stockee dans taxa

        $kingdom = self::KINGDOMS[$kingdom_slug] ?? null;
        $classes = self::CLASSES[$class_slug] ?? null;

        // Pas de traduction pour la famille
        $family = $family_slug;

        // On n'affiche que les observations de la commune en cours
        $communeCode = session('current_commune_code');

        $zoomLevel = config('app.commune_zooms')[$communeCode] ?? 12;

        if (! $classes) {
            abort(404, 'Groupe taxonomique inconnu');
        }

        $categories = [];

        $communeCode = session('current_commune_code');
        $topFamilies = DB::table('observations as o')
            ->join('taxa as t', 'o.taxon_id', '=', 't.id')
            ->where('o.code', session('current_commune_code'))
            ->whereIn('t.class', $classes)
            ->where('t.kingdom', $kingdom)
            ->whereNotNull('t.family')
            ->where('t.scientific_name', 'like', '% %') // Nom d'espèce complet (contient un espace)
            ->select('t.family', DB::raw('count(distinct o.taxon_id) as count'))
            ->groupBy('t.family')
            ->orderBy('count', 'desc')
            ->get();

        $families = $topFamilies->pluck('family');
        $mostObservedTaxa = Taxon::whereIn('family', $families)
            ->whereHas('photo')
            ->with('photo')
            ->withCount(['observations' => function ($q) use ($communeCode) {
                $q->where('code', $communeCode);
            }])
            ->having('observations_count', '>', 0)
            ->get()
            ->groupBy('family')
            ->map(function ($group) {
                return $group->sortByDesc('observations_count')->first();
            });

        // Si pas de family_slug spécifiée, rediriger vers la famille la plus observée
        if (! $family_slug && $topFamilies->isNotEmpty()) {
            $topFamily = $topFamilies->first()->family;
            $slug = Str::slug($topFamily);

            return redirect("/plantes/$class_slug/{$slug}");
        }

        foreach ($topFamilies as $fam) {
            $mostObservedTaxon = $mostObservedTaxa->get($fam->family);

            if (! $mostObservedTaxon || ! $mostObservedTaxon->photo) {
                continue; // Skip families without a photo for the most observed taxon
            }

            $img = $mostObservedTaxon->default_photo_url();

            $url = '/plantes/'.$class_slug.'/'.$fam->family;

            $categories[] = [
                'url' => $url,
                'img' => $img,
                'label' => $fam->family,
                'count' => $fam->count,
            ];

        }

        $taxa = Taxon::where('scientific_name', 'like', '% %') // Nom d'espèce complet (contient un espace)
            ->whereHas('observations', function (Builder $query) use ($classes, $kingdom, $family) {
                $query
                    ->where('kingdom', $kingdom)
                    ->whereIn('class', $classes)
                    ->whereLike('family', '%'.$family.'%')
                    ->where('code', session('current_commune_code'));

            })
            ->with(['observations' => function ($q) {
                $q->where('code', session('current_commune_code'));
            }, 'photo', 'description', 'like'])
            ->withCount(['observations' => function ($q) {
                $q->where('code', session('current_commune_code'));
            }])
            ->orderBy('observations_count', 'desc')
            ->orderBy('id', 'asc')
            ->paginate(10);

        return view('atlas', compact('taxa', 'categories', 'zoomLevel', 'family', 'family_slug', 'kingdom_slug', 'class_slug'));
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
    public function show(Taxon $taxon) {}

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
