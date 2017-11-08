<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFollowTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('follow', function (Blueprint $table) {
            $table->integer('follower_id')->unsigned();
            $table->integer('followee_id')->unsigned();
            $table->foreign('follower_id')->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->foreign('followee_id')->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->primary(['follower_id', 'followee_id']);
            $table->unique(['follower_id', 'followee_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('follow');
    }
}
