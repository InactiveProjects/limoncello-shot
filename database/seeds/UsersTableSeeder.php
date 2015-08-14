<?php

use \App\Models\User;
use \Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /** Sample login */
    const SAMPLE_LOGIN = 'user@example.com';

    /** Sample password */
    const SAMPLE_PASSWORD = 'password';

	/**
	 * Seeds the table.
	 *
	 * @return void
	 */
	public function run()
	{
        (new User([
            'name'     => 'John Dow',
            'email'    => self::SAMPLE_LOGIN,
            'password' => self::SAMPLE_PASSWORD,
        ]))->save();
	}
}
