<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\User::class, function (Faker\Generator $faker) {
	static $password;

	return [
		'name' => $faker->name,
		'email' => $faker->safeEmail,
		'password' => $password ?: $password = bcrypt('secret'),
		'remember_token' => str_random(10),
	];
});

$factory->define(App\Dungeon::class, function (Faker\Generator $faker) {
	return [
		'name' => "foo",
		'map' => '[["w","w","w","s","w","w","t","w"],["x","w","x","w","x","w","x","w"],["t","w","w","w","w","w","w","w"],["w","x","x","w","x","w","x","x"],["w","w","w","x","w","w","w","x"],["w","x","x","x","x","w","x","w"],["w","x","w","w","w","w","w","w"],["x","x","x","x","x","w","x","x"]]',
		'traps' => '[["1","6","0"],["1","0","2"]]',
		'size' => 'M',

		'public' => false,
		'owner_id' => Auth::user()->id,
		'approved' => false
	];
});

$factory->define(App\DungeonTrait::class, function (Faker\Generator $faker) {
	return [
		'type' => "name",
		'trait' => "bar",
		'public' => false,
		'owner_id' => Auth::user()->id,
		'approved' => false
	];
});

$factory->define(App\ForestEncounter::class, function (Faker\Generator $faker) {
	return [
		'description' => "description",
		'title' => "title",
		'rolls' => "1d6+2,2d5+10",

		'public' => false,
		'owner_id' =>  Auth::user()->id,
		'approved' => false
	];
});

$factory->define(App\Monster::class, function (Faker\Generator $faker) {
	return [
		"armor" => 10,
		"hit_points" => "1d5+6",
		"speed" => 30,
		"xp" => 20,
		"challenge" => 1.5,
		"name" => "foo monster",
		"stats" => '{"strength":16,"dexterity":13,"constitution":13,"intelligence":7,"wisdom":11,"charisma":8}',

		'public' => false,
		'owner_id' => Auth::user()->id,
		'approved' => false
	];

});

$factory->define(App\NonPlayerCharacter::class, function (Faker\Generator $faker){
	return [
		'first_name' => 'Bill',
		'sex' => 'M',
		'height' => 65,
		'weight' => 185,
		'age' => 35,

		'public' => false,
		'owner_id' => Auth::user()->id,
		'approved' => false
	];

});

$factory->define(App\NonPlayerCharacterTrait::class, function (Faker\Generator $faker){
	return [
		'type' => 'mannerism',
		'trait' => 'blinks very slowly',

		'public' => false,
		'owner_id' => Auth::user()->id,
		'approved' => false
	];

});

$factory->define(App\Riddle::class, function (Faker\Generator $faker) {
	return [
		'solution' => 'solution',
		'riddle' => 'riddle',
		'name' => 'name',

		'public' => false,
		'owner_id' =>  Auth::user()->id,
		'approved' => false
	];
});

$factory->define(App\Settlement::class, function (Faker\Generator $faker){
	return [
		"name" => $faker->name,
		"population" => 52,
		"size" => 'S',
		'ruler_id' => 1,

		'public' => false,
		'owner_id' => Auth::user()->id,
		'approved' => false
		];
});

$factory->define(App\SettlementTrait::class, function (Faker\Generator $faker){
	return [
		'type' => 'name',
		'trait' => 'Coolsville',

		'public' => false,
		'owner_id' => Auth::user()->id,
		'approved' => false
	];
});

$factory->define(App\Spell::class, function (Faker\Generator $faker){
	return [
		'name' => 'Test Name',
		'type' => 'abjuration',
		'class' => 'fighter',
		'level' => 6,
		'range' => 30,
		'description' => 'description',

		'public' => false,
		'owner_id' => Auth::user()->id,
		'approved' => false
	];
});

$factory->define(App\Tavern::class, function (Faker\Generator $faker){
	return [
		'name' => 'The Golden Rat',
		'type' => 'Thieves Guild Hangout',
		'tavern_owner_id' => 1,

		'public' => false,
		'owner_id' => Auth::user()->id,
		'approved' => false
	];

});

$factory->define(App\TavernTrait::class, function (Faker\Generator $faker){
	return [
		'trait' => 'Dive Bar',
		'type' => 'type',

		'public' => false,
		'owner_id' => Auth::user()->id,
		'approved' => false
	];

});

$factory->define(App\Trap::class, function (Faker\Generator $faker){
	return [
		"name" => "Fire Trap",
		"description" => "If a weight of 50lbs is placed on this title it will activate. Doing 2d6+5 fire damage.",

		'public' => false,
		'owner_id' => Auth::user()->id,
		'approved' => false
	];
});

$factory->define(App\UrbanEncounter::class, function (Faker\Generator $faker) {
	return [
		'description' => "description",
		'title' => "title",
		'rolls' => "1d6+2,2d5+10",

		'public' => false,
		'owner_id' =>  Auth::user()->id,
		'approved' => false
	];
});

$factory->define(App\Villain::class, function (Faker\Generator $faker) {
	return [
		'npc_id' => 1,

		'public' => false,
		'owner_id' => Auth::user()->id,
		'approved' => false
	];
});

$factory->define(App\VillainTrait::class, function (Faker\Generator $faker) {
	return [
		'type' => "scheme",
		'kind' => "Immortality",
		'description' => "description",

		'public' => false,
		'owner_id' => Auth::user()->id,
		'approved' => false
	];
});
