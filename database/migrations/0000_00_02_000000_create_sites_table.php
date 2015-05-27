<?php

use \App\Models\PDO\Site;
use \Illuminate\Database\Schema\Blueprint;
use \Illuminate\Database\Migrations\Migration;

/**
 * Sites migration
 */
class CreateSitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(Site::TABLE_NAME, function (Blueprint $table) {
            $table->increments(Site::FIELD_ID);
            $table->string(Site::FIELD_NAME);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop(Site::TABLE_NAME);
    }
}
