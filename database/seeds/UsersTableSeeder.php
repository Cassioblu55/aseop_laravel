<?php

use Illuminate\Database\Seeder;
use App\User;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$userToDelete = User::query()->where('email', 'cassioblubyrd@gmail.com');
	    $userToDelete->delete();

	    User::create(array(
		    'name'     => 'cbhudson',
		    'email'    => 'cassioblubyrd@gmail.com',
		    'password' => Hash::make('password'),
	    ));
    }
}
