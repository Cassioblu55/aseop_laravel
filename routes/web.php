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

Route::get('dungeons/upload', 'DungeonController@upload');
Route::post('dungeons/upload', 'DungeonController@saveBatch');

Route::get('dungeons/generate', 'DungeonController@generate');
Route::post('dungeons/createWithIdReturn', 'DungeonController@createWithIdReturn');

Route::resource('dungeons', 'DungeonController',[
	'parameters' => 'singular'
]);

Route::get('dungeonTraits/upload', 'DungeonTraitController@upload');
Route::post('dungeonTraits/upload', 'DungeonTraitController@saveBatch');

Route::resource('dungeonTraits', 'DungeonTraitController',[
	'parameters' => 'singular'
]);

Route::get('traps/upload', 'TrapController@upload');
Route::post('traps/upload', 'TrapController@saveBatch');

Route::resource('traps', 'TrapController',[
	'parameters' => 'singular'
]);

Route::get('npcs/upload', 'NpcController@upload');
Route::post('npcs/upload', 'NpcController@saveBatch');

Route::get('npcs/generate', 'NpcController@generate');

Route::resource('npcs', 'NpcController',[
	'parameters' => 'singular'
]);

Route::get('npcTraits/upload', 'NpcTraitController@upload');
Route::post('npcTraits/upload', 'NpcTraitController@saveBatch');
Route::resource('npcTraits', 'NpcTraitController',[
	'parameters' => 'singular'
]);

Route::get('settlements/upload', 'SettlementController@upload');
Route::post('settlements/upload', 'SettlementController@saveBatch');

Route::get('settlements/generate', 'SettlementController@generate');
Route::resource('settlements', 'SettlementController',[
	'parameters' => 'singular'
]);

Route::get('settlementTraits/upload', 'SettlementTraitController@upload');
Route::post('settlementTraits/upload', 'SettlementTraitController@saveBatch');

Route::resource('settlementTraits', 'SettlementTraitController',[
	'parameters' => 'singular'
]);

Route::get('monsters/upload', 'MonsterController@upload');
Route::post('monsters/upload', 'MonsterController@saveBatch');

Route::resource('monsters', 'MonsterController', [
	'parameters' => 'singular'
]);

Route::get('taverns/upload', 'TavernController@upload');
Route::post('taverns/upload', 'TavernController@saveBatch');

Route::get('taverns/generate', 'TavernController@generate');
Route::resource('taverns', 'TavernController',[
	'parameters' => 'singular'
]);

Route::get('tavernTraits/upload', 'TavernTraitController@upload');
Route::post('tavernTraits/upload', 'TavernTraitController@saveBatch');

Route::get('tavernTraits/upload', 'TavernTraitController@upload');
Route::post('tavernTraits/upload', 'TavernTraitController@saveBatch');
Route::resource('tavernTraits', 'TavernTraitController',[
	'parameters' => 'singular'
]);

Route::get('riddles/upload', 'RiddleController@upload');
Route::post('riddles/upload', 'RiddleController@saveBatch');

Route::get('riddles/random', 'RiddleController@random');
Route::resource('riddles', 'RiddleController',[
	'parameters' => 'singular'
]);

Route::get('spells/upload', 'SpellController@upload');
Route::post('spells/upload', 'SpellController@saveBatch');

Route::resource('spells', 'SpellController',[
'parameters' => 'singular'
]);

Route::get('villains/upload', 'VillainController@upload');
Route::post('villains/upload', 'VillainController@saveBatch');

Route::get('villains/generate', 'VillainController@generate');
Route::resource('villains', 'VillainController',[
'parameters' => 'singular'
]);

Route::get('villainTraits/upload', 'VillainTraitController@upload');
Route::post('villainTraits/upload', 'VillainTraitController@saveBatch');

Route::resource('villainTraits', 'VillainTraitController',[
'parameters' => 'singular'
]);

Route::get('dungeons/upload', 'DungeonController@upload');
Route::post('dungeons/upload', 'DungeonController@saveBatch');

Route::get('forestEncounters/upload', 'ForestEncounterController@upload');
Route::post('forestEncounters/upload', 'ForestEncounterController@saveBatch');

Route::get('forestEncounters/random', 'ForestEncounterController@random');
Route::resource('forestEncounters', 'ForestEncounterController',[
'parameters' => 'singular'
]);

Route::get('urbanEncounters/upload', 'UrbanEncounterController@upload');
Route::post('urbanEncounters/upload', 'UrbanEncounterController@saveBatch');

Route::get('urbanEncounters/random', 'UrbanEncounterController@random');
Route::resource('urbanEncounters', 'UrbanEncounterController',[
'parameters' => 'singular'
]);
