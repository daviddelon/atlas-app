<?php

namespace App\Http\Controllers;

use App\Models\Taxon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use League\CommonMark\CommonMarkConverter;

;

class TaxonController extends Controller
{


    private const KINGDOMS = [
        'plantes' => 'Plantae',
        'animaux' => 'Animalia',
    ];

    public const GROUPS = [
        'Plantae' => [
            'angiospermes' => ['Magnoliopsida', 'Liliopsida'],
            'gymnospermes' => ['Pinopsida'],
            'fougeres'      => ['Polypodiopsida'],
            'mousses'       => ['Bryopsida', 'Jungermanniopsida', 'Marchantiopsida'],
        ],
        'Animalia' => [
            'mammiferes' => ['Mammalia'],
            'oiseaux'    => ['Aves'],
            'insectes'   => ['Insecta'],
            'reptiles'   => ['Reptilia'],
        ]
    ];

    /**
     * Display a listing of the resource.
     */
    public function index(Request$request )
    {


    }



    public function taxaParClasse(Request $request, string $kingdom, string $group)
    {

        $kingdom = self::KINGDOMS[$kingdom] ?? null;
        $classes = self::GROUPS[$kingdom][$group] ?? null;


        if (!$classes) {
            abort(404, "Groupe taxonomique inconnu");
        }

        $taxa = Taxon::whereHas('observations', function (Builder $query) use ($classes, $kingdom) {
                $query
                    ->where('kingdom', $kingdom)
                    ->whereIn('class', $classes);
            })
            ->with(['observations', 'photo', 'description', 'like'])
            ->withCount('observations')
            ->orderBy('id', 'asc')
            ->paginate(10);

        return view('home', compact('taxa'));
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
