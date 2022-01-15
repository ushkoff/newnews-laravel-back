<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {

            $table->bigIncrements('id');

            $table->bigInteger('category_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();

            $table->string('title');

            $table->text('content_html');

            $table->string('country')->nullable(); // for Local News
            $table->string('country_code')->nullable();

            // Link (or list of links) to news resources
            $table->string('refs')->nullable();

            // Some crypto info...
            $table->string('author_pubkey')->nullable();
            $table->string('signature')->nullable();

            $table->integer('rating')->default(0);
            $table->boolean('is_confirmed')->default(0);

            $table->boolean('is_edited')->default(0);
            $table->timestamps();

            $table->foreign('category_id')->references('id')
                ->on('categories');
            $table->foreign('user_id')->references('id')
                ->on('users');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('articles');
    }
}
