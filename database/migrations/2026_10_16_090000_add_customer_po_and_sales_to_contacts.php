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
            if (!Schema::hasColumn('orders', 'customer_po_number')) {
                $table->string('customer_po_number')->nullable()->after('order_number');
            }
        });

        Schema::table('contacts', function (Blueprint $table) {
            if (!Schema::hasColumn('contacts', 'sales_id')) {
                $table->unsignedBigInteger('sales_id')->nullable()->after('type');
            }
            if (!Schema::hasColumn('contacts', 'sales_name')) {
                $table->string('sales_name')->nullable()->after('pic_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'customer_po_number')) {
                $table->dropColumn('customer_po_number');
            }
        });

        Schema::table('contacts', function (Blueprint $table) {
            if (Schema::hasColumn('contacts', 'sales_id')) {
                $table->dropColumn('sales_id');
            }
            if (Schema::hasColumn('contacts', 'sales_name')) {
                $table->dropColumn('sales_name');
            }
        });
    }
};
