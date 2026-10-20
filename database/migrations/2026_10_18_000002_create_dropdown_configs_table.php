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
        Schema::create('dropdown_configs', function (Blueprint $table) {
            $table->id();
            $table->string('group', 50)->index(); // procurement_type, service_type, payment_term, unit_satuan, etc.
            $table->string('key', 100);          // technical code, e.g. sub_rental, oil_change, Net 30 Days
            $table->string('label_id', 255);      // Label in Indonesian
            $table->string('label_zh', 255)->nullable(); // Label in Chinese
            $table->string('badge_color', 50)->nullable()->default('slate'); // blue, emerald, amber, purple, rose, slate
            $table->integer('order_num')->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['group', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dropdown_configs');
    }
};
