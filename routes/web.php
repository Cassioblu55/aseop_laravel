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

Route::get('/', function () {
    return view('welcome');
});

Route::resource('dungeons', 'DungeonController',[
	'parameters' => 'singular'
]);

Route::get('api/dungeons', function (){
	return Dungeon::all();
});

Route::get('api/dungeons/{dungeon}', function(Dungeon $dungeon){
	return $dungeon;
});

Route::get('api/traps', function (){
	return [['name'=> "test", 'id'=>1]];
});

Route::get('api/traps/{trap}', function (Trap $trap){
	return $trap;
});


Route::get('api/profile/defaultAccess', function(){
	return "0";
});
Auth::routes();

Route::get('/home', 'HomeController@index');
