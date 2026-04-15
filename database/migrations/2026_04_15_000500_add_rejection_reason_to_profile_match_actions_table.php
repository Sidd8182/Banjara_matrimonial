<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profile_match_actions', function (Blueprint $table) {
            $table->string('rejection_reason', 255)->nullable()->after('action');
        });
    }

    public function down(): void
    {
        Schema::table('profile_match_actions', function (Blueprint $table) {
            $table->dropColumn('rejection_reason');
        });
    }
};
