<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('first_name');
            $table->string('email')->unique();
            $table->string('password');
            $table->integer('fb_id')->nullable();
            $table->string('fb_token')->nullable();
            $table->string('time_zone')->nullable();
            $table->boolean('unit_is_km')->default(true);
            $table->boolean('temperature_is_celsius')->default(true);
            $table->string('profile_image')->nullable();
            $table->string('profile_image_cover')->nullable();
            $table->string('profile_image_thumb')->nullable();
            $table->enum('role',['admin','traveler','demo'])->default('traveler');
            $table->boolean('is_verified')->default(0);
            $table->rememberToken();
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
        Schema::dropIfExists('users');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_verified');
        });
    }
}
