<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('horoscope_details', function (Blueprint $table) {
            $table->string('lagna', 80)->nullable()->after('nakshatra');
        });
    }

    public function down(): void
    {
        Schema::table('horoscope_details', function (Blueprint $table) {
            $table->dropColumn('lagna');
        });
    }
};
