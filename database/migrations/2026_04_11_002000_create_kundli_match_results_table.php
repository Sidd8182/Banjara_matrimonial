<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kundli_match_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('male_profile_id')->constrained('profiles')->cascadeOnDelete();
            $table->foreignId('female_profile_id')->constrained('profiles')->cascadeOnDelete();
            $table->decimal('guna_score', 5, 2)->nullable();
            $table->unsignedTinyInteger('guna_total')->default(36);
            $table->decimal('percentage', 5, 2)->nullable();
            $table->json('koota_breakdown')->nullable();
            $table->string('source', 30)->default('custom-engine');
            $table->timestamp('computed_at')->nullable();
            $table->timestamps();

            $table->unique(['male_profile_id', 'female_profile_id'], 'kundli_male_female_unique');
            $table->index('percentage');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kundli_match_results');
    }
};
