<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTripsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('name');
            $table->dateTime('start_date')->unique();
            $table->dateTime('end_date')->nullable();
            $table->integer('total_km')->nullable()->default(0);
            $table->string('cover_photo_path')->default('/assets/images/defaul_trip_cover.jpg');
            $table->integer('likes')->default(0);
            $table->enum('privacy',['public','private','followers'])->default('followers');
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
        Schema::dropIfExists('trips');
    }
}
