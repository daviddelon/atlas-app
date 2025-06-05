<?php

namespace App\Http\Controllers;

use App\Models\Taxon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

;

class TaxonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {



        $taxa = Taxon::has('observations') // Important ! Sinon pas de sous-selection
        ->with('observations')
        ->withCount('observations')
        ->orderBy('observations_count', 'desc')
        ->orderBy('id', 'asc') // pour éviter affichage en doublon sur la pagination !
        ->simplePaginate(6);


        return view('home', [
            'taxa'=>$taxa
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
        //
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
