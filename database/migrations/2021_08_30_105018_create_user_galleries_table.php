<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserGalleriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_galleries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index('user_id');
            $table->enum('gallery_type', ['1', '2'])->default('1')->comment('1=Image, 2=Video');
            $table->text('gallery_url')->nullable();
            $table->enum('gallery_status', ['1', '2'])->default('1')->comment('1=Active, 2=Deactive');
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
        Schema::dropIfExists('user_galleries');
    }
}
