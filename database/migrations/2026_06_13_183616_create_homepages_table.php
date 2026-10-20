<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('homepages')) {
            Schema::create('homepages', function (Blueprint $table) {
                $table->id();
                $table->json('hero_section')->nullable();
                $table->json('service_section')->nullable();
                $table->json('advantages_section')->nullable();
                $table->json('products_section')->nullable();
                $table->json('workflow_section')->nullable();
                $table->json('testimonials_section')->nullable();
                $table->json('coverage_section')->nullable();
                $table->json('faq_section')->nullable();
                $table->json('cta_section')->nullable();
                $table->json('footer_section')->nullable();
                $table->timestamps();
            });
        } else {
            Schema::table('homepages', function (Blueprint $table) {
                $table->json('hero_section')->nullable()->change();
                $table->json('service_section')->nullable()->change();
                $table->json('advantages_section')->nullable()->change();
                $table->json('products_section')->nullable()->change();
                $table->json('workflow_section')->nullable()->change();
                $table->json('testimonials_section')->nullable()->change();
                $table->json('coverage_section')->nullable()->change();
                $table->json('faq_section')->nullable()->change();
                $table->json('cta_section')->nullable()->change();
                $table->json('footer_section')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('homepages');
    }
};
