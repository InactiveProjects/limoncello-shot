<?php

use \App\Models\Site;
use \Illuminate\Database\Seeder;

class SitesTableSeeder extends Seeder
{
	/**
	 * Seeds the table.
	 *
	 * @return void
	 */
	public function run()
	{
        $site = new Site();
        $site->name = 'JSON API Samples';
        $site->save();
	}
}
