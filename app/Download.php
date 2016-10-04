<?php
/**
 * Created by PhpStorm.
 * User: cbhudson
 * Date: 9/27/16
 * Time: 11:50 PM
 */

namespace App;

interface Download
{
	public static function download($fileName, $ext ='csv');
}