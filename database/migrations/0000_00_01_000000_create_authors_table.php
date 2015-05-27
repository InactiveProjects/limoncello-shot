<?php

use \App\Models\PDO\Author;
use \Illuminate\Database\Schema\Blueprint;
use \Illuminate\Database\Migrations\Migration;

/**
 * Authors migration
 */
class CreateAuthorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(Author::TABLE_NAME, function (Blueprint $table) {
            $table->increments(Author::FIELD_ID);
            $table->string(Author::FIELD_FIRST_NAME);
            $table->string(Author::FIELD_LAST_NAME);
            /** @noinspection PhpUndefinedMethodInspection */
            $table->string(Author::FIELD_TWITTER)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop(Author::TABLE_NAME);
    }
}
