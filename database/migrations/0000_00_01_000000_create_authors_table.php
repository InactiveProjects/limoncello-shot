<?php

use \Illuminate\Database\Schema\Blueprint;
use \Illuminate\Database\Migrations\Migration;

class CreateAuthorsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('authors', function(Blueprint $table)
		{
			$table->increments('id');
            $table->string('first_name');
            $table->string('last_name');
            /** @noinspection PhpUndefinedMethodInspection */
            $table->string('twitter')->nullable();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('authors');
	}
}
