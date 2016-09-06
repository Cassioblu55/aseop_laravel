<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DungeonTrait extends AssetTrait
{

	protected $guarded = [];

	public function user()
	{
		return $this->belongsTo('App\User', 'owner_id');
	}

}
