<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToWebSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('web_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('web_settings', 'android_app_icon')) {
                $table->string('android_app_icon')->nullable();
            }
            if (!Schema::hasColumn('web_settings', 'ios_app_icon')) {
                $table->string('ios_app_icon')->nullable();
            }
            if (!Schema::hasColumn('web_settings', 'ios_fcm_key')) {
                $table->string('ios_fcm_key')->nullable();
            }
            if (!Schema::hasColumn('web_settings', 'android_fcm_key')) {
                $table->string('android_fcm_key')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('web_settings', function (Blueprint $table) {
            //
        });
    }
}
