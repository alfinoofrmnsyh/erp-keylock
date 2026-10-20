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
        Schema::table('contacts', function (Blueprint $table) {
            if (!Schema::hasColumn('contacts', 'lead_id')) {
                $table->unsignedBigInteger('lead_id')->nullable()->after('sales_id');
                $table->foreign('lead_id')->references('id')->on('leads')->nullOnDelete();
            }
            if (!Schema::hasColumn('contacts', 'lead_code')) {
                $table->string('lead_code')->nullable()->after('lead_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            if (Schema::hasColumn('contacts', 'lead_id')) {
                $table->dropForeign(['lead_id']);
                $table->dropColumn('lead_id');
            }
            if (Schema::hasColumn('contacts', 'lead_code')) {
                $table->dropColumn('lead_code');
            }
        });
    }
};
