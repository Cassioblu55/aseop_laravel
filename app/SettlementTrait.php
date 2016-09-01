<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SettlementTrait extends Model
{
	protected $guarded = [];

	public function user()
	{
		return $this->belongsTo('App\User', 'owner_id');
	}
}
