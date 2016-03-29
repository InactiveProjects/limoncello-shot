<?php

use Illuminate\Database\Migrations\Migration;
use Neomerx\LimoncelloIlluminate\Database\Migrations\Runner;

/**
 * @package App
 */
class Initial extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.ShortMethodName)
     */
    public function up()
    {
        Runner::apply();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Runner::rollback();
    }
}
