<?php

namespace App;
use Illuminate\Database\Eloquent\Model;



class Trap extends Model
{
	protected $fillable = [
		'name', 'description', 'rolls', 'weight', 'owner_id', 'public'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'owner_id');
	}
}