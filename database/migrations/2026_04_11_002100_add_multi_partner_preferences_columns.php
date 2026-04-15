<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('partner_preferences', function (Blueprint $table) {
            $table->json('preferred_cities')->nullable()->after('location_preference');
            $table->json('preferred_qualifications')->nullable()->after('minimum_qualification');
            $table->json('preferred_professions')->nullable()->after('preferred_profession');
        });
    }

    public function down(): void
    {
        Schema::table('partner_preferences', function (Blueprint $table) {
            $table->dropColumn([
                'preferred_cities',
                'preferred_qualifications',
                'preferred_professions',
            ]);
        });
    }
};
