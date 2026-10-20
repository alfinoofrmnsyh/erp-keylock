<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create or retrieve Root Menu for Report Tabs Grouping
        $rootMenu = DB::table('appmenu')->where('menu_name', 'TAB LAPORAN & REKAPITULASI')->first();
        if (!$rootMenu) {
            $rootMenuId = DB::table('appmenu')->insertGetId([
                'menu_parent' => 0,
                'menu_name' => 'TAB LAPORAN & REKAPITULASI',
                'menu_page' => null,
                'menu_order' => 4,
                'menu_icon' => 'pe-7s-graph2',
                'erp' => 'N',
                'project' => 'N',
                'mobile' => 'N',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $rootMenuId = $rootMenu->menu_id;
        }

        // 2. Define Tab Items in appmenu
        $reportTabs = [
            [
                'menu_name' => 'Laporan Sales & CRM',
                'menu_page' => '/erp/reports?tab=sales',
                'menu_order' => 1,
                'menu_icon' => 'pe-7s-graph1',
            ],
            [
                'menu_name' => 'Laporan Operasional & Unit',
                'menu_page' => '/erp/reports?tab=operational',
                'menu_order' => 2,
                'menu_icon' => 'pe-7s-car',
            ],
            [
                'menu_name' => 'Laporan Finansial Owner',
                'menu_page' => '/erp/reports?tab=owner',
                'menu_order' => 3,
                'menu_icon' => 'pe-7s-cash',
            ],
            [
                'menu_name' => 'Laporan Insentif Sales',
                'menu_page' => '/erp/reports?tab=incentive',
                'menu_order' => 4,
                'menu_icon' => 'pe-7s-medal',
            ],
        ];

        $tabMenuIds = [];
        foreach ($reportTabs as $tab) {
            $existing = DB::table('appmenu')->where('menu_page', $tab['menu_page'])->first();
            if (!$existing) {
                $id = DB::table('appmenu')->insertGetId([
                    'menu_parent' => $rootMenuId,
                    'menu_name' => $tab['menu_name'],
                    'menu_page' => $tab['menu_page'],
                    'menu_order' => $tab['menu_order'],
                    'menu_icon' => $tab['menu_icon'],
                    'erp' => 'N',
                    'project' => 'N',
                    'mobile' => 'N',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $tabMenuIds[$tab['menu_page']] = $id;
            } else {
                $tabMenuIds[$tab['menu_page']] = $existing->menu_id;
                DB::table('appmenu')->where('menu_id', $existing->menu_id)->update([
                    'menu_parent' => $rootMenuId,
                    'menu_name' => $tab['menu_name'],
                    'menu_order' => $tab['menu_order'],
                    'menu_icon' => $tab['menu_icon'],
                    'updated_at' => now(),
                ]);
            }
        }

        // 3. Setup default role access in appmenuaccess
        // Role mappings:
        // 1: admin (all tabs)
        // 2: sales (sales tab)
        // 3: operasional (operational tab)
        // 5: owner (owner, incentive, sales, operational tabs)
        // 6: admin_op (operational tab)
        $rolePermissions = [
            1 => [$rootMenuId, $tabMenuIds['/erp/reports?tab=sales'], $tabMenuIds['/erp/reports?tab=operational'], $tabMenuIds['/erp/reports?tab=owner'], $tabMenuIds['/erp/reports?tab=incentive']],
            2 => [$rootMenuId, $tabMenuIds['/erp/reports?tab=sales']],
            3 => [$rootMenuId, $tabMenuIds['/erp/reports?tab=operational']],
            5 => [$rootMenuId, $tabMenuIds['/erp/reports?tab=owner'], $tabMenuIds['/erp/reports?tab=incentive'], $tabMenuIds['/erp/reports?tab=sales']],
            6 => [$rootMenuId, $tabMenuIds['/erp/reports?tab=operational']],
        ];

        foreach ($rolePermissions as $roleId => $menuIds) {
            foreach ($menuIds as $menuId) {
                $hasAccess = DB::table('appmenuaccess')
                    ->where('usergrpid', $roleId)
                    ->where('menu_id', $menuId)
                    ->exists();

                if (!$hasAccess) {
                    DB::table('appmenuaccess')->insert([
                        'usergrpid' => $roleId,
                        'menu_id' => $menuId,
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
        $pages = [
            '/erp/reports?tab=sales',
            '/erp/reports?tab=operational',
            '/erp/reports?tab=owner',
            '/erp/reports?tab=incentive',
        ];

        $menuIds = DB::table('appmenu')->whereIn('menu_page', $pages)->pluck('menu_id')->toArray();
        $rootMenu = DB::table('appmenu')->where('menu_name', 'TAB LAPORAN & REKAPITULASI')->first();
        if ($rootMenu) {
            $menuIds[] = $rootMenu->menu_id;
        }

        DB::table('appmenuaccess')->whereIn('menu_id', $menuIds)->delete();
        DB::table('appmenu')->whereIn('menu_id', $menuIds)->delete();
    }
};
