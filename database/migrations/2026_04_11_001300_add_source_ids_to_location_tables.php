<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSourceIdsToLocationTables extends Migration
{
    public function up()
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->unsignedInteger('source_id')->nullable()->unique()->after('id');
        });

        Schema::table('states', function (Blueprint $table) {
            $table->unsignedInteger('source_id')->nullable()->unique()->after('id');
        });

        Schema::table('cities', function (Blueprint $table) {
            $table->unsignedInteger('source_id')->nullable()->unique()->after('id');
        });
    }

    public function down()
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->dropUnique(['source_id']);
            $table->dropColumn('source_id');
        });

        Schema::table('states', function (Blueprint $table) {
            $table->dropUnique(['source_id']);
            $table->dropColumn('source_id');
        });

        Schema::table('countries', function (Blueprint $table) {
            $table->dropUnique(['source_id']);
            $table->dropColumn('source_id');
        });
    }
}
