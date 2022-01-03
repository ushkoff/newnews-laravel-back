<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * Run with --seed to call UserSeeder with default users.
     * see seeders/DatabaseSeeder
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {

            $table->bigIncrements('id');

            $table->string('username');

            $table->string('email')->unique()->index();
            $table->timestamp('email_verified_at')->nullable();

            $table->string('password');

            $table->integer('news_num')->default(0);
            $table->integer('verified_news_num')->default(0);

            $table->string('country')->nullable();
            $table->string('country_code')->nullable();
            $table->string('timezone')->nullable(); // UTC +02:00

            // ID of users, news from whom you don't want to see
            $table->text('blocklist')->default('[]');

            // Will user get a notice on email after confirmation his article
            $table->boolean('news_confirm_notice')->default(1);

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
        Schema::dropIfExists('users');
    }
}
