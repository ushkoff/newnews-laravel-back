<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticleDislikesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * Таблица логирования дизлайков каждой записи новостей.
     * То есть, записи типа userID=1 дизлайкнул articleID=12
     * Эта информация будет использована для подсчета рейтинга новости.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_dislikes', function (Blueprint $table) {

            $table->bigIncrements('id');

            $table->bigInteger('article_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();

            $table->timestamps();

            $table->foreign('article_id')->references('id')
                ->on('articles');
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
        Schema::dropIfExists('article_dislikes');
    }
}
