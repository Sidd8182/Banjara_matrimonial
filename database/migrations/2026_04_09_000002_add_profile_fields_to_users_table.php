<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProfileFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('profile_for', 30)->nullable()->after('gender');
            $table->string('marital_status', 30)->nullable()->after('profile_for');
            $table->date('date_of_birth')->nullable()->after('marital_status');
            $table->unsignedSmallInteger('height_cm')->nullable()->after('date_of_birth');
            $table->string('religion', 50)->nullable()->after('height_cm');
            $table->string('mother_tongue', 50)->nullable()->after('religion');

            $table->string('current_city', 100)->nullable()->after('mother_tongue');
            $table->string('current_state', 100)->nullable()->after('current_city');
            $table->string('current_country', 100)->nullable()->after('current_state');
            $table->string('diet', 30)->nullable()->after('current_country');
            $table->string('smoke', 20)->nullable()->after('diet');
            $table->string('drink', 20)->nullable()->after('smoke');
            $table->text('about_me')->nullable()->after('drink');

            $table->string('education', 100)->nullable()->after('about_me');
            $table->string('education_detail', 150)->nullable()->after('education');
            $table->string('occupation', 100)->nullable()->after('education_detail');
            $table->string('income', 50)->nullable()->after('occupation');
            $table->string('company_name', 120)->nullable()->after('income');

            $table->string('family_type', 50)->nullable()->after('company_name');
            $table->string('father_occupation', 120)->nullable()->after('family_type');
            $table->string('mother_occupation', 120)->nullable()->after('father_occupation');
            $table->unsignedTinyInteger('brothers')->nullable()->after('mother_occupation');
            $table->unsignedTinyInteger('sisters')->nullable()->after('brothers');
            $table->string('family_values', 50)->nullable()->after('sisters');

            $table->string('manglik', 20)->nullable()->after('family_values');
            $table->string('rashi', 50)->nullable()->after('manglik');
            $table->string('nakshatra', 50)->nullable()->after('rashi');
            $table->string('time_of_birth', 20)->nullable()->after('nakshatra');
            $table->string('place_of_birth', 120)->nullable()->after('time_of_birth');
            $table->string('gotra', 80)->nullable()->after('place_of_birth');

            $table->unsignedTinyInteger('profile_completion_step')->default(1)->after('gotra');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'profile_for',
                'marital_status',
                'date_of_birth',
                'height_cm',
                'religion',
                'mother_tongue',
                'current_city',
                'current_state',
                'current_country',
                'diet',
                'smoke',
                'drink',
                'about_me',
                'education',
                'education_detail',
                'occupation',
                'income',
                'company_name',
                'family_type',
                'father_occupation',
                'mother_occupation',
                'brothers',
                'sisters',
                'family_values',
                'manglik',
                'rashi',
                'nakshatra',
                'time_of_birth',
                'place_of_birth',
                'gotra',
                'profile_completion_step',
            ]);
        });
    }
}
