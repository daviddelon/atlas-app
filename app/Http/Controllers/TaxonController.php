<?php

namespace App\Http\Controllers;

use App\Models\Taxon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

;

class TaxonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request$request )
    {


    }

     public function plantes(Request $request, string $class)
    {

        $classes=null;
        switch ($class) {
            case "angiospermes":
                $classes=array("Magnoliopsida","Liliopsida");
                break;
            case "gymnospermes":
                $classes=array("Pinopsida");
                break;
            case "fougeres":
                $classes=array("Polypodiopsida");
                break;
            case "mousses":
                $classes=array("Bryopsida","Jungermanniopsida","Marchantiopsida");
                break;
            default:
                break;
        }
        $taxa = Taxon::whereHas('observations', function (Builder $query) use ($classes) {
                $query
                ->where('kingdom', "Plantae")
                ->whereIn('class', $classes)
                ;
            })
            ->with(['observations','photo'])
            ->withCount('observations')
            ->orderBy('observations_count', 'desc')
            ->orderBy('id', 'asc')  // pour éviter affichage en doublon sur la pagination !
            ->paginate(10);

        return view('home', [
            'taxa' => $taxa,
        ]);
    }

         public function animaux(Request $request, string $class)
    {

        $classes=null;
        switch ($class) {
            case "mammiferes":
                $classes=array("Mammalia");
                break;
            case "oiseaux":
                $classes=array("Aves");
                break;
            case "insectes":
                $classes=array("Insecta");
                break;
            case "reptiles":
                $classes=array("Reptilia");
                break;
            default:
                break;
        }
        $taxa = Taxon::whereHas('observations', function (Builder $query) use ($classes) {
                $query
                ->where('kingdom', "Animalia")
                ->whereIn('class', $classes)
                ;
            })
            ->with(['observations','photo'])
            ->withCount('observations')
            ->orderBy('observations_count', 'desc')
            ->orderBy('id', 'asc')  // pour éviter affichage en doublon sur la pagination !
            ->paginate(10);

        return view('home', [
            'taxa' => $taxa,
        ]);
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
