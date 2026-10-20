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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->string('asset_code', 50)->unique();
            $table->string('serial_number', 100)->unique();
            $table->string('license_plate', 30)->nullable();
            $table->integer('year')->nullable();
            $table->decimal('hour_meter', 10, 2)->default(0);
            $table->string('current_location')->default('Gudang Utama Cikarang');
            $table->enum('status', ['available', 'rented', 'maintenance', 'reserved', 'sold'])->default('available');
            $table->text('condition_notes')->nullable();
            $table->date('last_service_at')->nullable();
            $table->date('next_service_due')->nullable();
            $table->timestamps();

            $table->index(['item_id', 'status']);
            $table->index('serial_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
