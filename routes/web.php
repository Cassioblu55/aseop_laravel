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

use App\Dungeon;
use App\Trap;

Auth::routes();

Route::get('/home', 'HomeController@index');

Route::get('/', function () {
    return view('welcome');
});

Route::resource('dungeons', 'DungeonController',[
	'parameters' => 'singular'
]);

Route::resource('dungeonTraits', 'DungeonTraitController',[
	'parameters' => 'singular'
]);

Route::resource('traps', 'TrapController',[
	'parameters' => 'singular'
]);

Route::resource('npcs', 'NpcController',[
	'parameters' => 'singular'
]);

Route::resource('npcTraits', 'NpcTraitController',[
	'parameters' => 'singular'
]);

Route::resource('settlements', 'SettlementController',[
	'parameters' => 'singular'
]);

Route::resource('settlementTraits', 'SettlementTraitController',[
	'parameters' => 'singular'
]);



