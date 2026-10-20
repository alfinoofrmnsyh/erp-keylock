<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add is_active to users table
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'is_active')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_active')->default(true)->after('usergrpid');
            });
        }

        // 2. Create roles table
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name', 50)->unique();
                $table->string('label', 100);
                $table->text('description')->nullable();
                $table->boolean('is_system')->default(false);
                $table->timestamps();
            });

            // Seed standard initial roles matching usergrpid
            DB::table('roles')->insert([
                [
                    'id' => 1,
                    'name' => 'admin',
                    'label' => 'Administrator',
                    'description' => 'Akses penuh ke seluruh modul sistem, ERP, CMS, dan konfigurasi master.',
                    'is_system' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 2,
                    'name' => 'sales',
                    'label' => 'Sales & Marketing',
                    'description' => 'Akses ke modul transaksi, pembuatan penawaran harga, dan pelanggan CRM.',
                    'is_system' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 3,
                    'name' => 'operasional',
                    'label' => 'Operasional & Logistik',
                    'description' => 'Akses ke pengelolaan surat jalan (DO), pengiriman armada, dan master vendor.',
                    'is_system' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'is_active')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('is_active');
            });
        }

        Schema::dropIfExists('roles');
    }
};
