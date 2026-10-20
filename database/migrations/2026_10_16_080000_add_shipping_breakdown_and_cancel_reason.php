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
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'shipping_delivery_fee')) {
                $table->decimal('shipping_delivery_fee', 15, 2)->default(0)->after('shipping_fee');
            }
            if (!Schema::hasColumn('orders', 'shipping_return_fee')) {
                $table->decimal('shipping_return_fee', 15, 2)->default(0)->after('shipping_delivery_fee');
            }
            if (!Schema::hasColumn('orders', 'cancel_reason')) {
                $table->text('cancel_reason')->nullable()->after('notes');
            }
        });

        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'shipping_delivery_fee')) {
                $table->decimal('shipping_delivery_fee', 15, 2)->default(0)->after('shipping_fee');
            }
            if (!Schema::hasColumn('invoices', 'shipping_return_fee')) {
                $table->decimal('shipping_return_fee', 15, 2)->default(0)->after('shipping_delivery_fee');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'shipping_delivery_fee')) {
                $table->dropColumn('shipping_delivery_fee');
            }
            if (Schema::hasColumn('orders', 'shipping_return_fee')) {
                $table->dropColumn('shipping_return_fee');
            }
            if (Schema::hasColumn('orders', 'cancel_reason')) {
                $table->dropColumn('cancel_reason');
            }
        });

        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'shipping_delivery_fee')) {
                $table->dropColumn('shipping_delivery_fee');
            }
            if (Schema::hasColumn('invoices', 'shipping_return_fee')) {
                $table->dropColumn('shipping_return_fee');
            }
        });
    }
};
