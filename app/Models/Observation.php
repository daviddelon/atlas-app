<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Observation extends Model
{

    use HasFactory;

    protected $fillable = ['observation_id','taxon_id','observed_on','observed_by', 'license', 'longitude', 'latitude'];

}
