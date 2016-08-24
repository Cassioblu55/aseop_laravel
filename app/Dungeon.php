<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dungeon extends Model
{
	protected $fillable = [
		'name', 'purpose', 'history', 'location', 'creator', 'map', 'traps', 'size', 'other_information', 'owner_id',
		'public'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'owner_id');
	}

}
