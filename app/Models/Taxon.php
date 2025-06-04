<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Taxon extends Model
{
    use HasFactory;

    protected $fillable = [ 'scientific_name', 'common_name'];

    public function observations() {
        return $this->hasMany(Observation::class);
    }

    public function default_photo_url() {

        if (Storage::disk('public')->exists($this->id.'.jpg')) {
            return Storage::url($this->id.'.jpg');
        }
        else return null;
    }

}
