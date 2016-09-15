<?php

namespace App;

class Riddle extends Random
{
	protected $guarded = [];

	public function user()
	{
		return $this->belongsTo('App\User', 'owner_id');
	}




}
