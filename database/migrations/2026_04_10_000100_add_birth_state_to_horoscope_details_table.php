<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBirthStateToHoroscopeDetailsTable extends Migration
{
    public function up()
    {
        Schema::table('horoscope_details', function (Blueprint $table) {
            $table->string('birth_state', 120)->nullable()->after('place_of_birth');
        });
    }

    public function down()
    {
        Schema::table('horoscope_details', function (Blueprint $table) {
            $table->dropColumn('birth_state');
        });
    }
}
