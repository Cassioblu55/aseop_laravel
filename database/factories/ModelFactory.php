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


$factory->define(App\DungeonTrait::class, function (Faker\Generator $faker) {
	return [
		'type' => "name",
		'trait' => "bar",
		'public' => false,
		'owner_id' => Auth::user()->id,
		'approved' => false
	];
});

$factory->define(App\VillainTrait::class, function (Faker\Generator $faker) {
	return [
		'type' => "foo",
		'kind' => "kind",
		'description' => "description",
		'public' => false,
		'owner_id' => Auth::user()->id,
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

$factory->define(App\Dungeon::class, function (Faker\Generator $faker) {
	return [
		'name' => "foo",
		'map' => '[["w","w","w","s","w","w","w","w"],["x","w","x","w","x","w","x","w"],["w","w","w","w","w","w","w","w"],["w","x","x","w","x","w","x","x"],["w","w","w","x","w","w","w","x"],["w","x","x","x","x","w","x","w"],["w","x","t","w","w","w","w","w"],["x","x","x","x","x","t","x","x"]]',
		'traps' => '[["1","2","6"],["3","5","7"]]',
		'size' => 'L',

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

$factory->define(App\SettlementTrait::class, function (Faker\Generator $faker){
	return [
		'type' => 'name',
		'trait' => 'Coolsville',

		'public' => false,
		'owner_id' => Auth::user()->id,
		'approved' => false
	];

});
