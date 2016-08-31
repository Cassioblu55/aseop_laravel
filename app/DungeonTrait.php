<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DungeonTrait extends Model
{
	protected $fillable = [
		'type', 'trait', 'description', 'weight', 'owner_id', 'public', 'description'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'owner_id');
	}
}
