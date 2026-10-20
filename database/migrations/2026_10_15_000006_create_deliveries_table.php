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
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->string('delivery_number', 50)->unique();
            $table->enum('delivery_type', ['delivery_order', 'return'])->default('delivery_order');
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('contact_id')->constrained('contacts')->cascadeOnDelete();
            $table->date('delivery_date');
            $table->string('driver_name')->nullable();
            $table->string('vehicle_number', 30)->nullable();
            $table->text('destination_address')->nullable();
            $table->string('recipient_name')->nullable();
            $table->string('recipient_phone', 50)->nullable();
            $table->enum('status', ['draft', 'scheduled', 'on_delivery', 'delivered', 'returned', 'cancelled'])->default('draft');
            $table->timestamp('received_at')->nullable();
            $table->string('recipient_signature')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['delivery_type', 'status']);
            $table->index('order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
