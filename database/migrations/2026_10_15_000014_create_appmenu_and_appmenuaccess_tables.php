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
        // 1. Create appmenu table (matching Image 3)
        if (!Schema::hasTable('appmenu')) {
            Schema::create('appmenu', function (Blueprint $table) {
                $table->increments('menu_id');
                $table->integer('menu_parent')->default(0)->index();
                $table->string('menu_name', 100);
                $table->string('menu_page', 191)->nullable();
                $table->integer('menu_order')->default(0);
                $table->string('menu_icon', 100)->nullable();
                $table->char('erp', 1)->default('Y');
                $table->char('project', 1)->default('N');
                $table->char('mobile', 1)->default('N');
                $table->timestamps();
            });
        }

        // 2. Create appmenuaccess table (matching Image 4)
        if (!Schema::hasTable('appmenuaccess')) {
            Schema::create('appmenuaccess', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('usergrpid')->default(1)->index();
                $table->unsignedInteger('menu_id')->index();
                $table->timestamp('createdtime')->nullable();

                $table->foreign('menu_id')->references('menu_id')->on('appmenu')->onDelete('cascade');
            });
        }

        // 3. Add usergrpid to users table if not exists
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'usergrpid')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedInteger('usergrpid')->default(1)->after('role');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appmenuaccess');
        Schema::dropIfExists('appmenu');
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'usergrpid')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('usergrpid');
            });
        }
    }
};
