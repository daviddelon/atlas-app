<?php

namespace App\Http\Controllers;

use App\Models\Taxon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TaxonController extends Controller
{
    // Exemple : /ferrieres-les-verreries-34099/plantes/angiospermes/asteraceae

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

    public function taxaFiltre(Request $request, string $location_slug, string $kingdom_slug, string $class_slug, ?string $family_slug = '')
    {

        $kingdom = self::KINGDOMS[$kingdom_slug] ?? null;
        $classes = self::CLASSES[$class_slug] ?? null;

        $family = $family_slug;

        $communeCode = config('app.default_commune_code');

        $zoomLevel = config('app.commune_zooms')[$communeCode] ?? 12;

        if (! $classes) {
            abort(404, 'Groupe taxonomique inconnu');
        }

        $categories = [];

        $topFamilies = DB::table('observations as o')
            ->join('taxa as t', 'o.taxon_id', '=', 't.id')
            ->where('o.code', $communeCode)
            ->whereIn('t.class', $classes)
            ->where('t.kingdom', $kingdom)
            ->whereNotNull('t.family')
            ->where('t.scientific_name', 'like', '% %')
            ->select('t.family', DB::raw('count(distinct o.taxon_id) as count'))
            ->groupBy('t.family')
            ->orderBy('count', 'desc')
            ->get();

        $families = $topFamilies->pluck('family')->toArray();

        if (empty($families)) {
            $taxa = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
            return view('atlas', compact(
                'categories', 'zoomLevel', 'family', 'family_slug',
                'kingdom_slug', 'class_slug', 'location_slug', 'communeCode'
            ) + ['taxa' => $taxa]);
        }

        $rankedQuery = '
            SELECT t.*, cnt.observations_count
            FROM taxa t
            JOIN (
                SELECT o.taxon_id, t2.family, t2.id AS taxon_pk,
                       COUNT(*) AS observations_count,
                       ROW_NUMBER() OVER (
                           PARTITION BY t2.family
                           ORDER BY COUNT(*) DESC, t2.id ASC
                       ) AS rn
                FROM observations o
                JOIN taxa t2 ON t2.id = o.taxon_id
                WHERE t2.family IN ('.str_repeat('?,', count($families) - 1).'?)
                  AND o.code = ?
                  AND EXISTS (SELECT 1 FROM photos p WHERE p.taxon_id = t2.id)
                GROUP BY o.taxon_id, t2.family, t2.id
            ) cnt ON cnt.taxon_id = t.id AND cnt.rn = 1
        ';
        $mostObservedTaxa = DB::select($rankedQuery, array_merge($families, [$communeCode]));
        $mostObservedTaxa = collect($mostObservedTaxa)->keyBy('family');

        $taxonIds = $mostObservedTaxa->pluck('id');
        $taxaWithPhotos = Taxon::whereIn('id', $taxonIds)->with('photo')->get()->keyBy('id');

        // Si pas de family_slug spécifiée, rediriger vers la famille la plus observée
        if (! $family_slug && $topFamilies->isNotEmpty()) {
            $topFamily = $topFamilies->first()->family;
            $slug = Str::slug($topFamily);

            return redirect("/$location_slug/plantes/$class_slug/{$slug}");
        }

        foreach ($topFamilies as $fam) {
            $mostObservedTaxon = $mostObservedTaxa->get($fam->family);

            if (! $mostObservedTaxon || ! $taxaWithPhotos->get($mostObservedTaxon->id)?->photo) {
                continue;
            }

            $img = $taxaWithPhotos->get($mostObservedTaxon->id)->default_photo_url();

            $url = '/'.$location_slug.'/plantes/'.$class_slug.'/'.Str::slug($fam->family);

            $categories[] = [
                'url' => $url,
                'img' => $img,
                'label' => $fam->family,
                'count' => $fam->count,
            ];

        }

        $taxa = Taxon::where('scientific_name', 'like', '% %')
            ->whereHas('observations', function (Builder $query) use ($classes, $kingdom, $family, $communeCode) {
                $query
                    ->where('kingdom', $kingdom)
                    ->whereIn('class', $classes)
                    ->whereRaw('LOWER(family) LIKE LOWER(?)', ['%'.$family.'%'])
                    ->where('code', $communeCode);

            })
            ->with(['observations' => function ($q) use ($communeCode) {
                $q->where('code', $communeCode);
            }, 'photo', 'description', 'like'])
            ->withCount(['observations' => function ($q) use ($communeCode) {
                $q->where('code', $communeCode);
            }])
            ->orderBy('observations_count', 'desc')
            ->orderBy('id', 'asc')
            ->paginate(10);

        return view('atlas', compact(
            'taxa', 'categories', 'zoomLevel', 'family', 'family_slug',
            'kingdom_slug', 'class_slug', 'location_slug', 'communeCode'
        ));
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
