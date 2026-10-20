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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('sku', 50)->unique();
            $table->string('name');
            $table->string('category', 100);
            $table->string('brand', 100)->nullable();
            $table->string('model', 100)->nullable();
            $table->string('unit', 30)->default('Unit');
            $table->enum('item_type', ['rental_unit', 'sale_unit', 'sparepart', 'service'])->default('rental_unit');
            $table->decimal('cost_price', 15, 2)->default(0);
            $table->decimal('sale_price', 15, 2)->default(0);
            $table->decimal('rental_rate_daily', 15, 2)->default(0);
            $table->decimal('rental_rate_monthly', 15, 2)->default(0);
            $table->decimal('overtime_rate_per_hour', 15, 2)->default(0);
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['item_type', 'is_active']);
            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
