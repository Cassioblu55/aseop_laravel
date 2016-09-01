<?php

use Illuminate\Http\Request;
use App\Dungeon;
use App\Trap;
use App\DungeonTrait;
use App\NonPlayerCharacter;
use App\NonPlayerCharacterTrait;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::get('/dungeons', function (){
	return Dungeon::all();
});

Route::get('/dungeons/{dungeon}', function(Dungeon $dungeon){
	return $dungeon;
});

Route::get('/dungeonTraits', function (){
	return DungeonTrait::all();
});

Route::get('/dungeonTraits/{dungeonTrait}', function(DungeonTrait $dungeonTrait){
	return $dungeonTrait;
});

Route::get('/npcs', function (){
	return NonPlayerCharacter::all();
});

Route::get('/npcs/{npc}', function(NonPlayerCharacter $npc){
	return $npc;
});

Route::get('/npcTraits', function (){
	return NonPlayerCharacterTrait::all();
});

Route::get('/npcTraits/{npcTrait}', function(NonPlayerCharacterTrait $npcTrait){
	return $npcTrait;
});

Route::get('/traps', function (){
	return Trap::all();
});

Route::get('/traps/{trap}', function (Trap $trap){
	return $trap;
});


Route::get('/profile/defaultAccess', function(){
	return "0";
});
