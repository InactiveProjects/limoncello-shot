<?php

use \App\Models\PDO\Post;
use \App\Models\PDO\Comment;
use \Illuminate\Database\Schema\Blueprint;
use \Illuminate\Database\Migrations\Migration;

/**
 * Comments migration
 */
class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(Comment::TABLE_NAME, function (Blueprint $table) {
            $table->increments(Comment::FIELD_ID);
            $table->unsignedInteger(Comment::FIELD_POST_ID);
            $table->string(Comment::FIELD_BODY);

            $table->foreign(Comment::FIELD_POST_ID)->references(Post::FIELD_ID)->on(Post::TABLE_NAME)
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop(Comment::TABLE_NAME);
    }
}
