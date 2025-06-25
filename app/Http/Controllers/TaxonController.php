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


        if (!$classes) {
            abort(404, "Groupe taxonomique inconnu");
        }

        $taxa = Taxon::whereHas('observations', function (Builder $query) use ($classes, $kingdom, $family) {
                $query
                    ->where('kingdom', $kingdom)
                    ->whereIn('class', $classes)
                    ->whereLike('family', '%'.$family.'%');

            })
            ->with(['observations', 'photo', 'description', 'like'])
            ->withCount('observations')
            ->orderBy('observations_count', 'desc')
            ->orderBy('id', 'asc')
            ->paginate(10);

        return view('atlas', compact('taxa'));
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
