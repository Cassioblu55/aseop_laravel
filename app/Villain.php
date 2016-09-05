<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Villain extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo('App\User', 'owner_id');
    }

	public function npc()
	{
		return $this->belongsTo('App\NonPlayerCharacter', 'npc_id');
	}

}