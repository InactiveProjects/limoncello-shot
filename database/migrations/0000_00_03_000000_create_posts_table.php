<?php

use \App\Models\PDO\Post;
use \App\Models\PDO\Site;
use \App\Models\PDO\Author;
use \Illuminate\Database\Schema\Blueprint;
use \Illuminate\Database\Migrations\Migration;

/**
 * Posts migration
 */
class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(Post::TABLE_NAME, function (Blueprint $table) {
            $table->increments(Post::FIELD_ID);
            $table->unsignedInteger(Post::FIELD_AUTHOR_ID);
            $table->unsignedInteger(Post::FIELD_SITE_ID);
            $table->string(Post::FIELD_TITLE);
            $table->text(Post::FIELD_BODY);

            $table->foreign(Post::FIELD_AUTHOR_ID)->references(Author::FIELD_ID)->on(Author::TABLE_NAME)
                ->onDelete('cascade');

            $table->foreign(Post::FIELD_SITE_ID)->references(Site::FIELD_ID)->on(Site::TABLE_NAME)->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop(Post::TABLE_NAME);
    }
}
