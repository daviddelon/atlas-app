<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
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

    public function getSlugAttribute(): string
    {
        return Str::slug($this->nom) . '-' . $this->code;
    }

    public static function findBySlug(string $slug): ?self
    {
        $code = substr(strrchr($slug, '-'), 1);
        return static::where('code', $code)->first();
    }

    public static function resolveSlug(string $slug): ?string
    {
        $commune = static::findBySlug($slug);
        return $commune?->code;
    }
}
