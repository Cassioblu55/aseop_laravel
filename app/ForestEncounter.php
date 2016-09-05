<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ForestEncounter extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo('App\User', 'owner_id');
    }
}