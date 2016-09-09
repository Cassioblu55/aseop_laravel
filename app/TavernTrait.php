<?php

namespace App;

class TavernTrait extends AssetTrait
{
	protected $guarded = [];

	public function user()
	{
		return $this->belongsTo('App\User', 'owner_id');
	}
}
