<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tavern extends Model
{
	protected $guarded = [];

	public function user()
	{
		return $this->belongsTo('App\User', 'owner_id');
	}

	public function owner()
	{
		return $this->belongsTo('App\NonPlayerCharacter', 'tavern_owner_id');
	}
}
