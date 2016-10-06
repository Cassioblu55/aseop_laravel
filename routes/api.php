<?php

use Illuminate\Http\Request;
use App\Dungeon;
use App\Trap;
use App\DungeonTrait;
use App\NonPlayerCharacter;
use App\NonPlayerCharacterTrait;
use App\SettlementTrait;
use App\Settlement;
use App\Monster;
use App\Tavern;
use App\TavernTrait;
use App\Riddle;

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

Route::get('/profile/defaultAccess', function(){
	return "0";
});

Route::get('/dungeons', function (){
	return Dungeon::all();
});

Route::get('/dungeons/download/{fileName}.{ext}', function ($fileName, $ext){
	$file = Dungeon::download($fileName);
	\App\Services\DownloadHelper::export($file, $ext);
});

Route::get('/dungeons/{dungeon}', function(Dungeon $dungeon){
	return $dungeon;
});

Route::get('/dungeonTraits', function (){
	return DungeonTrait::all();
});

Route::get('/dungeonTraits/types', function (){
	return DungeonTrait::getValidTraitTypes();
});

Route::get('/dungeonTraits/download/{fileName}.{ext}', function ($fileName, $ext){
	$file = DungeonTrait::download($fileName);
	\App\Services\DownloadHelper::export($file, $ext);
});

Route::get('/dungeonTraits/{dungeonTrait}', function(DungeonTrait $dungeonTrait){
	return $dungeonTrait;
});

Route::get('/npcs', function (){
	return NonPlayerCharacter::all();
});

Route::get('/npcs/download/{fileName}.{ext}', function ($fileName, $ext){
	$file = NonPlayerCharacter::download($fileName);
	\App\Services\DownloadHelper::export($file, $ext);
});

Route::get('/npcs/names', function (){
	$queryData = NonPlayerCharacter::all(['id', 'first_name', 'last_name'], false);
	$data = [];
	foreach ($queryData as $row){
		$newRow = [];
		$newRow['name'] = $row['first_name'] . " ". $row['last_name'];
		$newRow['id'] = $row['id'];
		array_push($data, $newRow);
	}

	return $data;
});

Route::get('/npcs/{npc}', function(NonPlayerCharacter $npc){
	return $npc;
});

Route::get('/npcTraits', function (){
	return NonPlayerCharacterTrait::all();
});

Route::get('/npcTraits/download/{fileName}.{ext}', function ($fileName, $ext){
	$file = NonPlayerCharacterTrait::download($fileName);
	\App\Services\DownloadHelper::export($file, $ext);
});

Route::get('/npcTraits/types', function (){
	return NonPlayerCharacterTrait::getValidTraitTypes();
});

Route::get('/npcTraits/{npcTrait}', function(NonPlayerCharacterTrait $npcTrait){
	return $npcTrait;
});

Route::get('/settlements', function (){
	return Settlement::all();
});

Route::get('/settlements/download/{fileName}.{ext}', function ($fileName, $ext){
	$file = Settlement::download($fileName);
	\App\Services\DownloadHelper::export($file, $ext);
});

Route::get('/settlements/{settlement}', function(Settlement $settlement){
	return $settlement;
});

Route::get('/settlementTraits', function (){
	return SettlementTrait::all();
});

Route::get('/settlementTraits/download/{fileName}.{ext}', function ($fileName, $ext){
	$file = SettlementTrait::download($fileName);
	\App\Services\DownloadHelper::export($file, $ext);
});

Route::get('/settlementTraits/types', function (){
	return SettlementTrait::getValidTraitTypes();
});

Route::get('/settlementTraits/{settlementTrait}', function(SettlementTrait $settlementTrait){
	return $settlementTrait;
});

Route::get('/traps', function (){
	return Trap::all();
});

Route::get('/traps/download/{fileName}.{ext}', function ($fileName, $ext){
	$file = Trap::download($fileName);
	\App\Services\DownloadHelper::export($file, $ext);
});

Route::get('/traps/{trap}', function (Trap $trap){
	return $trap;
});

Route::get('/monsters', function (){
	return Monster::all();
});

Route::get('/monsters/download/{fileName}.{ext}', function ($fileName, $ext){
	$file = Monster::download($fileName);
	\App\Services\DownloadHelper::export($file, $ext);
});

Route::get('/monsters/{monster}', function (Monster $monster){
	return $monster;
});

Route::get('/taverns', function (){
	return Tavern::all();
});

Route::get('/taverns/download/{fileName}.{ext}', function ($fileName, $ext){
	$file = Tavern::download($fileName);
	\App\Services\DownloadHelper::export($file, $ext);
});

Route::get('/taverns/{tavern}', function (Tavern $tavern){
	return $tavern;
});


Route::get('/tavernTraits', function (){
	return TavernTrait::all();
});

Route::get('/tavernTraits/download/{fileName}.{ext}', function ($fileName, $ext){
	$file = TavernTrait::download($fileName);
	\App\Services\DownloadHelper::export($file, $ext);
});

Route::get('/tavernTraits/types', function (){
	return TavernTrait::getValidTraitTypes();
});

Route::get('/tavernTraits/{tavernTrait}', function (TavernTrait $tavernTrait){
	return $tavernTrait;
});

Route::get('/riddles', function (){
	return Riddle::all();
});

Route::get('/riddles/download/{fileName}.{ext}', function ($fileName, $ext){
	$file = Riddle::download($fileName);
	\App\Services\DownloadHelper::export($file, $ext);
});

Route::get('/riddles/{riddle}', function(Riddle $riddle){
	return $riddle;
});

Route::get('/spells', function (){
return \App\Spell::all();
});

Route::get('/spells/download/{fileName}.{ext}', function ($fileName, $ext){
	$file = \App\Spell::download($fileName);
	\App\Services\DownloadHelper::export($file, $ext);
});

Route::get('/spells/types', function (){
	return \App\Spell::VALID_SPELL_TYPES;
});

Route::get('/spells/classes', function (){
	return \App\Spell::VALID_CLASS_TYPES;
});

Route::get('/spells/{spell}', function(\App\Spell $spell){
return $spell;
});

Route::get('/villains', function (){
return \App\Villain::all();
});

Route::get('/villains/download/{fileName}.{ext}', function ($fileName, $ext){
	$file = \App\Villain::download($fileName);
	\App\Services\DownloadHelper::export($file, $ext);
});

Route::get('/villains/{villain}', function(\App\Villain $villain){
return $villain;
});

Route::get('/villainTraits', function (){
return \App\VillainTrait::all();
});

Route::get('/villainTraits/download/{fileName}.{ext}', function ($fileName, $ext){
	$file = \App\VillainTrait::download($fileName);
	\App\Services\DownloadHelper::export($file, $ext);
});

Route::get('/villainTraits/kinds', function (){
	return \App\VillainTrait::getValidKindsByType();
});

Route::get('/villainTraits/{villainTrait}', function(\App\VillainTrait $villainTrait){
return $villainTrait;
});

Route::get('/forestEncounters', function (){
return \App\ForestEncounter::all();
});

Route::get('/forestEncounters/download/{fileName}.{ext}', function ($fileName, $ext){
	$file = \App\ForestEncounter::download($fileName);
	\App\Services\DownloadHelper::export($file, $ext);
});

Route::get('/forestEncounters/{forestEncounter}', function(\App\ForestEncounter $forestEncounter){
return $forestEncounter;
});

Route::get('/urbanEncounters', function (){
return \App\UrbanEncounter::all();
});

Route::get('/urbanEncounters/download/{fileName}.{ext}', function ($fileName, $ext){
	$file = \App\UrbanEncounter::download($fileName);
	\App\Services\DownloadHelper::export($file, $ext);
});

Route::get('/urbanEncounters/{urbanEncounter}', function(\App\UrbanEncounter $urbanEncounter){
return $urbanEncounter;
});
