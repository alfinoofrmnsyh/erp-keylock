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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['customer', 'vendor', 'lead'])->default('customer');
            $table->string('code', 30)->unique();
            $table->string('name');
            $table->string('pic_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 50)->nullable();
            $table->text('billing_address')->nullable();
            $table->text('shipping_address')->nullable();
            $table->decimal('credit_limit', 15, 2)->default(0);
            $table->integer('payment_term_days')->default(30);
            $table->string('npwp', 50)->nullable();
            $table->string('lead_source', 100)->nullable();
            $table->enum('lead_status', ['new', 'contacted', 'qualified', 'proposal', 'won', 'lost'])->default('new');
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['type', 'is_active']);
            $table->index('lead_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
