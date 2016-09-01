<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Settlement extends Model
{
	protected $guarded = [];

	public function user()
	{
		return $this->belongsTo('App\User', 'owner_id');
	}

	public function ruler()
	{
		return $this->belongsTo('App\NonPlayerCharacter', 'ruler_id');
	}
}
