<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldToUserProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->string('first_name',25)->nullable()->after('user_id');
            $table->string('last_name',25)->nullable()->after('first_name');
            $table->string('email',50)->unique()->nullable()->after('last_name');
            $table->integer('country_id')->nullable()->after('email');
            $table->integer('state_id')->nullable()->after('country_id');
            $table->integer('city_id')->nullable()->after('state_id');
            $table->string('face_rate',25)->nullable()->after('city_id');
            $table->string('video_rate',25)->nullable()->after('face_rate');
            $table->string('voice_rate',25)->nullable()->after('video_rate');
            $table->string('chat_rate',25)->nullable()->after('voice_rate');
            $table->text('video_url')->nullable()->after('chat_rate');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            //
        });
    }
}
