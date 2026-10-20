<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('assets') && !Schema::hasColumn('assets', 'ownership')) {
            Schema::table('assets', function (Blueprint $table) {
                $table->string('ownership')->default('Milik Sendiri (Internal)')->after('serial_number');
            });
        }

        if (Schema::hasTable('contacts') && !Schema::hasColumn('contacts', 'category')) {
            Schema::table('contacts', function (Blueprint $table) {
                $table->string('category')->nullable()->after('type');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('assets') && Schema::hasColumn('assets', 'ownership')) {
            Schema::table('assets', function (Blueprint $table) {
                $table->dropColumn('ownership');
            });
        }

        if (Schema::hasTable('contacts') && Schema::hasColumn('contacts', 'category')) {
            Schema::table('contacts', function (Blueprint $table) {
                $table->dropColumn('category');
            });
        }
    }
};
