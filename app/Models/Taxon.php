<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\Storage;
use Maize\Markable\Markable;
use Maize\Markable\Models\Like;

class Taxon extends Model
{
    use HasFactory;

    use Markable;

    protected $fillable = [ 'scientific_name', 'common_name'];

    public function observations() {
        return $this->hasMany(Observation::class);
    }

    public function photo() {
        return $this->hasOne(Photo::class);
    }

    public function description() {
        return $this->hasOne(Description::class);
    }

    public function default_photo_url() {

        if (Storage::disk('public')->exists($this->id.'.jpg')) {
            return Storage::url($this->id.'.jpg');
        }
        else return null;
    }

    public function observedPeriod()
    {
        $dates = $this->observations->pluck('observed_on')->filter(); // filter pour enlelever les nulls
        return $dates->isNotEmpty() ? ['start' => $dates->min(), 'end' => $dates->max()] : null;
    }

    public function observersCount()
    {
        return $this->observations->pluck('observed_by')->filter()->unique()->count(); // filter pour enlelever les nulls
    }

    public function like(): MorphOne {
        return $this->morphOne(Like::class, 'markable');
    }

    protected static $marks = [
        Like::class
    ];


}
