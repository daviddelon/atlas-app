<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Taxon extends Model
{
    use HasFactory;

    protected $fillable = [ 'scientific_name', 'common_name'];

    public function observations() {
        return $this->hasMany(Observation::class);
    }

}
