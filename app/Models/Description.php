<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Description extends Model
{

    public function taxon () {
        return $this->belongsTo(Taxon::class);
    }


}
