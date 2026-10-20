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
        if (!Schema::hasTable('asset_services')) {
            Schema::create('asset_services', function (Blueprint $table) {
                $table->id();
                $table->foreignId('asset_id')->constrained('assets')->onDelete('cascade');
                $table->string('service_number', 50)->unique();
                $table->date('service_date');
                $table->string('service_type', 50)->default('rutin'); // rutin, perbaikan, overhaul, ganti_oli, ganti_ban, inspeksi
                $table->decimal('hour_meter', 10, 2)->nullable();
                $table->string('technician_name', 150)->nullable();
                $table->foreignId('vendor_contact_id')->nullable()->constrained('contacts')->onDelete('set null');
                $table->decimal('cost', 15, 2)->default(0.00);
                $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('completed');
                $table->text('description')->nullable();
                $table->text('spareparts_replaced')->nullable();
                $table->date('next_service_date')->nullable();
                $table->decimal('next_service_hour_meter', 10, 2)->nullable();
                $table->text('notes')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_services');
    }
};
