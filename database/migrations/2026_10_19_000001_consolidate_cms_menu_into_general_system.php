<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\AppMenu;
use App\Models\AppMenuAccess;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Deactivate separate CMS group (menu_id = 2) and its submenus from ERP sidebar
        DB::table('appmenu')
            ->whereIn('menu_id', [2, 20, 21, 22, 23, 37, 38, 39])
            ->update(['erp' => 'N']);

        // 2. Ensure /erp/cms exists under General System (menu_parent = 3)
        $cmsMenu = DB::table('appmenu')->where('menu_page', '/erp/cms')->first();
        if (!$cmsMenu) {
            $cmsMenuId = DB::table('appmenu')->insertGetId([
                'menu_parent' => 3,
                'menu_name' => 'Content Management',
                'menu_page' => '/erp/cms',
                'menu_order' => 4,
                'menu_icon' => 'pe-7s-browser',
                'erp' => 'Y',
                'project' => 'N',
                'mobile' => 'N',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $cmsMenuId = $cmsMenu->menu_id;
            DB::table('appmenu')->where('menu_id', $cmsMenuId)->update([
                'menu_parent' => 3,
                'menu_name' => 'Content Management',
                'menu_page' => '/erp/cms',
                'menu_order' => 4,
                'menu_icon' => 'pe-7s-browser',
                'erp' => 'Y',
                'updated_at' => now(),
            ]);
        }

        // 3. Ensure role access is granted for the consolidated Content Management menu
        // Grant to roles 1 (Superadmin), 2 (Admin), 3 (Manager), 5 (Marketing), 6 (Staff)
        $targetRoles = [1, 2, 3, 5, 6];
        foreach ($targetRoles as $roleId) {
            $exists = DB::table('appmenuaccess')
                ->where('usergrpid', $roleId)
                ->where('menu_id', $cmsMenuId)
                ->exists();

            if (!$exists) {
                DB::table('appmenuaccess')->insert([
                    'usergrpid' => $roleId,
                    'menu_id' => $cmsMenuId,
                    'createdtime' => now(),
                ]);
            }

            // Also ensure these roles have access to the parent menu_id = 3 (General System) if it exists
            $hasParentMenu = DB::table('appmenu')->where('menu_id', 3)->exists();
            if ($hasParentMenu) {
                $parentExists = DB::table('appmenuaccess')
                    ->where('usergrpid', $roleId)
                    ->where('menu_id', 3)
                    ->exists();

                if (!$parentExists) {
                    DB::table('appmenuaccess')->insert([
                        'usergrpid' => $roleId,
                        'menu_id' => 3,
                        'createdtime' => now(),
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-enable CMS group 2
        DB::table('appmenu')
            ->whereIn('menu_id', [2, 20, 21, 22, 23, 37, 38, 39])
            ->update(['erp' => 'Y']);

        // Remove /erp/cms under General System
        $cmsMenu = DB::table('appmenu')->where('menu_page', '/erp/cms')->first();
        if ($cmsMenu) {
            DB::table('appmenuaccess')->where('menu_id', $cmsMenu->menu_id)->delete();
            DB::table('appmenu')->where('menu_id', $cmsMenu->menu_id)->delete();
        }
    }
};
