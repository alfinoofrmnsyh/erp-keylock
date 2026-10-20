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
        if (!Schema::hasTable('cms_contacts')) {
            Schema::create('cms_contacts', function (Blueprint $table) {
                $table->id();
                $table->string('contact1')->nullable();
                $table->string('contact2')->nullable();
                $table->string('wechat_qr')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cms_contacts');
    }
};
