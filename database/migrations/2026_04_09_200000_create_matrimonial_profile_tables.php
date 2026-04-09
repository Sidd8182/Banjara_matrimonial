<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMatrimonialProfileTables extends Migration
{
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('profile_id', 30)->unique();

            $table->string('first_name', 80)->nullable();
            $table->string('last_name', 80)->nullable();
            $table->string('gender', 20)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->unsignedSmallInteger('height_cm')->nullable();
            $table->decimal('weight_kg', 5, 2)->nullable();
            $table->string('marital_status', 30)->nullable();
            $table->string('mother_tongue', 80)->nullable();
            $table->string('religion', 80)->nullable();
            $table->string('caste', 120)->nullable();
            $table->string('sub_caste', 120)->nullable();
            $table->string('gotra', 120)->nullable();
            $table->string('profile_created_by', 30)->nullable();

            $table->string('country', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('area_locality', 120)->nullable();
            $table->string('pincode', 10)->nullable();
            $table->text('current_address')->nullable();
            $table->text('permanent_address')->nullable();
            $table->boolean('willing_to_relocate')->nullable();

            $table->string('profile_picture_path')->nullable();
            $table->string('video_intro_path')->nullable();
            $table->string('media_privacy', 20)->default('protected');

            $table->string('contact_mobile', 20)->nullable();
            $table->string('contact_email', 120)->nullable();
            $table->string('whatsapp_number', 20)->nullable();
            $table->string('contact_visibility', 30)->default('premium_only');
            $table->boolean('mobile_verified')->default(false);
            $table->boolean('email_verified')->default(false);

            $table->unsignedTinyInteger('last_completed_step')->default(1);
            $table->timestamps();
        });

        Schema::create('family_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->unique()->constrained('profiles')->cascadeOnDelete();
            $table->string('father_name', 120)->nullable();
            $table->string('father_occupation', 120)->nullable();
            $table->string('mother_name', 120)->nullable();
            $table->string('mother_occupation', 120)->nullable();
            $table->unsignedTinyInteger('brothers_count')->nullable();
            $table->unsignedTinyInteger('sisters_count')->nullable();
            $table->string('family_type', 30)->nullable();
            $table->string('family_status', 40)->nullable();
            $table->string('family_values', 30)->nullable();
            $table->timestamps();
        });

        Schema::create('education_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->unique()->constrained('profiles')->cascadeOnDelete();
            $table->string('highest_qualification', 120)->nullable();
            $table->string('degree', 120)->nullable();
            $table->string('college_university', 150)->nullable();
            $table->string('field_of_study', 120)->nullable();
            $table->timestamps();
        });

        Schema::create('career_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->unique()->constrained('profiles')->cascadeOnDelete();
            $table->string('occupation_type', 30)->nullable();
            $table->string('company_name', 150)->nullable();
            $table->string('job_title', 120)->nullable();
            $table->string('annual_income_range', 60)->nullable();
            $table->string('work_location', 120)->nullable();
            $table->string('work_type', 20)->nullable();
            $table->timestamps();
        });

        Schema::create('lifestyle_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->unique()->constrained('profiles')->cascadeOnDelete();
            $table->string('diet', 30)->nullable();
            $table->string('smoking', 20)->nullable();
            $table->string('drinking', 20)->nullable();
            $table->json('hobbies')->nullable();
            $table->json('interests')->nullable();
            $table->text('about_me')->nullable();
            $table->timestamps();
        });

        Schema::create('horoscope_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->unique()->constrained('profiles')->cascadeOnDelete();
            $table->date('date_of_birth')->nullable();
            $table->string('time_of_birth', 20)->nullable();
            $table->string('place_of_birth', 150)->nullable();
            $table->string('rashi', 60)->nullable();
            $table->string('nakshatra', 60)->nullable();
            $table->string('manglik', 20)->nullable();
            $table->string('horoscope_path')->nullable();
            $table->timestamps();
        });

        Schema::create('partner_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->unique()->constrained('profiles')->cascadeOnDelete();
            $table->unsignedTinyInteger('age_min')->nullable();
            $table->unsignedTinyInteger('age_max')->nullable();
            $table->unsignedSmallInteger('height_min_cm')->nullable();
            $table->unsignedSmallInteger('height_max_cm')->nullable();
            $table->string('religion_preference', 120)->nullable();
            $table->string('caste_preference', 120)->nullable();
            $table->string('location_preference', 180)->nullable();
            $table->string('minimum_qualification', 120)->nullable();
            $table->string('preferred_profession', 120)->nullable();
            $table->string('income_expectation', 80)->nullable();
            $table->string('diet_preference', 30)->nullable();
            $table->string('smoking_preference', 30)->nullable();
            $table->string('drinking_preference', 30)->nullable();
            $table->string('manglik_preference', 30)->nullable();
            $table->boolean('relocate_preference')->nullable();
            $table->text('expectations')->nullable();
            $table->timestamps();
        });

        Schema::create('media_gallery', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained('profiles')->cascadeOnDelete();
            $table->string('file_path');
            $table->string('media_type', 30)->default('image');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('verification', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->unique()->constrained('profiles')->cascadeOnDelete();
            $table->boolean('profile_verified_badge')->default(false);
            $table->string('id_proof_type', 30)->nullable();
            $table->string('id_proof_path')->nullable();
            $table->boolean('photo_verified')->default(false);
            $table->boolean('mobile_verified')->default(false);
            $table->boolean('email_verified')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('verification');
        Schema::dropIfExists('media_gallery');
        Schema::dropIfExists('partner_preferences');
        Schema::dropIfExists('horoscope_details');
        Schema::dropIfExists('lifestyle_details');
        Schema::dropIfExists('career_details');
        Schema::dropIfExists('education_details');
        Schema::dropIfExists('family_details');
        Schema::dropIfExists('profiles');
    }
}
