<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCmsPageSectionsTable extends Migration
{
    public function up()
    {
        Schema::create('cms_page_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cms_page_id')->nullable()->constrained('cms_pages')->nullOnDelete();
            $table->string('section_name', 120);
            $table->string('section_type', 40)->default('faq');
            $table->string('title', 255)->nullable();
            $table->text('body')->nullable();
            $table->json('target_pages')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cms_page_sections');
    }
}
