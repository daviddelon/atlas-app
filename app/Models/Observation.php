<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Observation extends Model
{

    use HasFactory;

    protected $fillable = ['taxon_id','observed_on','observed_by', 'license', 'longitude', 'latitude', 'code'];

    public function taxon () {
        return $this->belongsTo(Taxon::class);
    }

    public function commune() {
        return $this->belongsTo(Commune::class, 'code', 'code');
    }


}
