<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use MatanYadaev\EloquentSpatial\Traits\HasSpatial;

class Commune extends Model
{
    use HasSpatial;

    protected $primaryKey = 'OGR_FID';

    protected $fillable = ['code', 'nom', 'departement', 'region', 'commune', 'plm', 'epci', 'geom'];

    protected $casts = [
        'geom' => \MatanYadaev\EloquentSpatial\Objects\Geometry::class,
    ];

    public function observations() {
        return $this->hasMany(Observation::class, 'code', 'code');
    }
}
