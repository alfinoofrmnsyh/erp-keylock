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
        // 1. Orders table: add margin and procurement fields
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'vendor_cost')) {
                $table->decimal('vendor_cost', 15, 2)->default(0.00)->after('shipping_fee');
            }
            if (!Schema::hasColumn('orders', 'logistics_cost')) {
                $table->decimal('logistics_cost', 15, 2)->default(0.00)->after('vendor_cost');
            }
            if (!Schema::hasColumn('orders', 'gross_profit')) {
                $table->decimal('gross_profit', 15, 2)->default(0.00)->after('total_amount');
            }
            if (!Schema::hasColumn('orders', 'gross_margin_percent')) {
                $table->decimal('gross_margin_percent', 5, 2)->default(0.00)->after('gross_profit');
            }
            if (!Schema::hasColumn('orders', 'procurement_type')) {
                $table->string('procurement_type', 50)->nullable()->after('quotation_type');
            }
        });

        // 2. Order items: add cost_price and vendor_id
        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'cost_price')) {
                $table->decimal('cost_price', 15, 2)->default(0.00)->after('unit_price');
            }
            if (!Schema::hasColumn('order_items', 'vendor_id')) {
                $table->foreignId('vendor_id')->nullable()->after('asset_id')->constrained('contacts')->nullOnDelete();
            }
        });

        // 3. Invoices table: add customer_order_id and vendor_ref_no
        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'customer_order_id')) {
                $table->foreignId('customer_order_id')->nullable()->after('order_id')->constrained('orders')->nullOnDelete();
            }
            if (!Schema::hasColumn('invoices', 'vendor_ref_no')) {
                $table->string('vendor_ref_no', 100)->nullable()->after('invoice_number');
            }
        });

        // 4. Payments table: add payment_type (in / out)
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'payment_type')) {
                $table->enum('payment_type', ['in', 'out'])->default('in')->after('payment_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'payment_type')) {
                $table->dropColumn('payment_type');
            }
        });

        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'customer_order_id')) {
                $table->dropForeign(['customer_order_id']);
                $table->dropColumn('customer_order_id');
            }
            if (Schema::hasColumn('invoices', 'vendor_ref_no')) {
                $table->dropColumn('vendor_ref_no');
            }
        });

        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'vendor_id')) {
                $table->dropForeign(['vendor_id']);
                $table->dropColumn('vendor_id');
            }
            if (Schema::hasColumn('order_items', 'cost_price')) {
                $table->dropColumn('cost_price');
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            $cols = ['vendor_cost', 'logistics_cost', 'gross_profit', 'gross_margin_percent', 'procurement_type'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('orders', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
