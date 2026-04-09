<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateAstrologyMasterTables extends Migration
{
    public function up()
    {
        Schema::create('rashi_master', function (Blueprint $table) {
            $table->id();
            $table->string('name', 80)->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('nakshatra_master', function (Blueprint $table) {
            $table->id();
            $table->string('name', 80)->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $now = now();

        DB::table('rashi_master')->insert([
            ['name' => 'Mesh (Aries)', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Vrishabh (Taurus)', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Mithun (Gemini)', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Kark (Cancer)', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Singh (Leo)', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Kanya (Virgo)', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Tula (Libra)', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Vrishchik (Scorpio)', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Dhanu (Sagittarius)', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Makar (Capricorn)', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Kumbh (Aquarius)', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Meen (Pisces)', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);

        DB::table('nakshatra_master')->insert([
            ['name' => 'Ashwini', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Bharani', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Krittika', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Rohini', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Mrigashira', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Ardra', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Punarvasu', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Pushya', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Ashlesha', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Magha', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Purva Phalguni', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Uttara Phalguni', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Hasta', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Chitra', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Swati', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Vishakha', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Anuradha', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Jyeshtha', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Moola', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Purva Ashadha', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Uttara Ashadha', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Shravana', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Dhanishta', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Shatabhisha', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Purva Bhadrapada', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Uttara Bhadrapada', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Revati', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('nakshatra_master');
        Schema::dropIfExists('rashi_master');
    }
}
