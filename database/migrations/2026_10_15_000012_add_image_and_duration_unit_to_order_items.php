<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->string('duration_unit', 30)->default('Bulan')->after('duration')->nullable();
            $table->string('image', 255)->nullable()->after('description');
            if (!Schema::hasColumn('order_items', 'specification')) {
                $table->text('specification')->nullable()->after('description');
            }
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $cols = ['duration_unit', 'image'];
            if (Schema::hasColumn('order_items', 'specification')) {
                $cols[] = 'specification';
            }
            $table->dropColumn($cols);
        });
    }
};
