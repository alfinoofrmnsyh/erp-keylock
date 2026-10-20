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
        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'shipping_fee')) {
                $table->decimal('shipping_fee', 15, 2)->default(0.00)->after('subtotal');
            }
            if (!Schema::hasColumn('invoices', 'tax_percent')) {
                $table->decimal('tax_percent', 5, 2)->default(11.00)->after('discount_amount');
            }
            if (!Schema::hasColumn('invoices', 'pph_percent')) {
                $table->decimal('pph_percent', 5, 2)->default(0.00)->after('tax_amount');
            }
            if (!Schema::hasColumn('invoices', 'pph_amount')) {
                $table->decimal('pph_amount', 15, 2)->default(0.00)->after('pph_percent');
            }
            if (!Schema::hasColumn('invoices', 'tax_invoice_document')) {
                $table->string('tax_invoice_document', 255)->nullable()->after('payment_terms');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $columns = ['shipping_fee', 'tax_percent', 'pph_percent', 'pph_amount', 'tax_invoice_document'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('invoices', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
