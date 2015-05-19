<?php

use \App\Models\Site;
use \Illuminate\Database\Seeder;

/**
 * Sites seeder
 */
class SitesTableSeeder extends Seeder
{
    /**
     * Seeds the table.
     *
     * @return void
     */
    public function run()
    {
        $site = Site::newInstance();
        $site->setName('JSON API Samples');
        $site->save();
    }
}
