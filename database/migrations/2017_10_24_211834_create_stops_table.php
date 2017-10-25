<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stops', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('trip_id')->unsigned();
            $table->foreign('trip_id')
                ->references('id')->on('trips')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->integer('location_id')->unsigned();
            $table->foreign('location_id')
                ->references('id')->on('locations')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->integer('views')->default(0);
            $table->integer('likes')->default(0);
            $table->dateTime('arrival_time');
            $table->softDeletes();
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
        Schema::dropIfExists('stops');
    }
}
