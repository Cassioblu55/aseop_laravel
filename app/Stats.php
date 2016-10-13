<?php
/**
 * Created by PhpStorm.
 * User: cbhudson
 * Date: 10/12/16
 * Time: 10:37 PM
 */

namespace app;


class Stats
{
	const STRENGTH = "strength", DEXTERITY = "dexterity", CONSTITUTION="constitution", INTELLIGENCE = "intelligence", WISDOM  = "wisdom", CHARISMA = "charisma";

	const STATS_ARRAY = [self::STRENGTH, self::DEXTERITY, self::CONSTITUTION, self::INTELLIGENCE, self::WISDOM, self::CHARISMA];

	public static function validStatsArray($stats){
		$jsonObject = json_decode($stats);

		if($jsonObject == null || count((array) $jsonObject) != count(self::STATS_ARRAY)){return false;}

		foreach (self::STATS_ARRAY as $stat){
			if(!array_has($jsonObject, $stat)){
				return false;
			}else{
				$statValue = $jsonObject->{$stat};

				if(!is_integer($statValue) || is_integer($statValue) && $statValue < 0){
					return false;
				}
			}
		}
		return true;
	}

}