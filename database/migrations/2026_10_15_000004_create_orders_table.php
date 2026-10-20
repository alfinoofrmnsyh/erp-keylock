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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 50)->unique();
            $table->enum('order_type', ['quotation', 'sales_order', 'rental_order', 'purchase_order'])->default('quotation');
            $table->foreignId('contact_id')->constrained('contacts')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->date('order_date');
            $table->date('valid_until')->nullable();
            $table->date('rental_start_date')->nullable();
            $table->date('rental_end_date')->nullable();
            $table->integer('rental_duration')->nullable();
            $table->enum('rental_duration_unit', ['day', 'week', 'month', 'year'])->default('month');
            $table->enum('status', ['draft', 'sent', 'approved', 'rejected', 'in_progress', 'completed', 'cancelled'])->default('draft');
            $table->enum('delivery_status', ['pending', 'partial', 'delivered', 'returned'])->default('pending');
            $table->enum('invoice_status', ['unbilled', 'partial', 'invoiced', 'paid'])->default('unbilled');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('tax_percent', 5, 2)->default(11.00);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('shipping_fee', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('deposit_amount', 15, 2)->default(0);
            $table->string('payment_term', 150)->nullable();
            $table->text('shipping_address')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['order_type', 'status']);
            $table->index(['contact_id', 'order_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
