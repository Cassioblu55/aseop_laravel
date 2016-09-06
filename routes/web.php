<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Auth::routes();

Route::get('/home', 'HomeController@index');

Route::get('/', function () {
    return view('welcome');
});

Route::get('dungeons/generate', 'DungeonController@generate');
Route::post('dungeons/createWithIdReturn', 'DungeonController@createWithIdReturn');

Route::resource('dungeons', 'DungeonController',[
	'parameters' => 'singular'
]);

Route::resource('dungeonTraits', 'DungeonTraitController',[
	'parameters' => 'singular'
]);

Route::resource('traps', 'TrapController',[
	'parameters' => 'singular'
]);

Route::get('npcs/generate', 'NpcController@generate');
Route::resource('npcs', 'NpcController',[
	'parameters' => 'singular'
]);

Route::resource('npcTraits', 'NpcTraitController',[
	'parameters' => 'singular'
]);

Route::get('settlements/generate', 'SettlementController@generate');
Route::resource('settlements', 'SettlementController',[
	'parameters' => 'singular'
]);

Route::resource('settlementTraits', 'SettlementTraitController',[
	'parameters' => 'singular'
]);

Route::resource('monsters', 'MonsterController', [
	'parameters' => 'singular'
]);

Route::resource('taverns', 'TavernController',[
	'parameters' => 'singular'
]);

Route::resource('tavernTraits', 'TavernTraitController',[
	'parameters' => 'singular'
]);

Route::resource('riddles', 'RiddleController',[
	'parameters' => 'singular'
]);

Route::resource('spells', 'SpellController',[
'parameters' => 'singular'
]);

Route::resource('villains', 'VillainController',[
'parameters' => 'singular'
]);

Route::resource('villainTraits', 'VillainTraitController',[
'parameters' => 'singular'
]);

Route::resource('forestEncounters', 'ForestEncounterController',[
'parameters' => 'singular'
]);

Route::resource('urbanEncounters', 'UrbanEncounterController',[
'parameters' => 'singular'
]);
