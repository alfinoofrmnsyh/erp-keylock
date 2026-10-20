<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AppMenu;
use App\Models\AppMenuAccess;
use Illuminate\Support\Facades\DB;

class AppMenuSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        AppMenuAccess::truncate();
        AppMenu::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $menus = [
            // 1. Group: OPERASIONAL ERP
            [
                'menu_id' => 1,
                'menu_parent' => 0,
                'menu_name' => 'OPERASIONAL ERP',
                'menu_page' => null,
                'menu_order' => 1,
                'menu_icon' => 'pe-7s-rocket',
                'erp' => 'Y',
                'project' => 'N',
                'mobile' => 'N',
                'children' => [
                    [
                        'menu_id' => 10,
                        'menu_name' => 'Dashboard',
                        'menu_page' => '/erp',
                        'menu_order' => 1,
                        'menu_icon' => 'pe-7s-home',
                        'erp' => 'Y',
                    ],
                    [
                        'menu_id' => 15,
                        'menu_name' => 'Master Data',
                        'menu_page' => '/erp/master-data',
                        'menu_order' => 2,
                        'menu_icon' => 'pe-7s-server',
                        'erp' => 'Y',
                    ],
                    [
                        'menu_id' => 13,
                        'menu_name' => 'CRM',
                        'menu_page' => '/erp/crm',
                        'menu_order' => 3,
                        'menu_icon' => 'pe-7s-id',
                        'erp' => 'Y',
                    ],
                    [
                        'menu_id' => 11,
                        'menu_name' => 'Penawaran & Transaksi',
                        'menu_page' => '/erp/orders',
                        'menu_order' => 4,
                        'menu_icon' => 'pe-7s-cart',
                        'erp' => 'Y',
                    ],
                ]
            ],

            // 2. Group: KONTEN CMS WEBSITE
            [
                'menu_id' => 2,
                'menu_parent' => 0,
                'menu_name' => 'KONTEN CMS WEBSITE',
                'menu_page' => null,
                'menu_order' => 2,
                'menu_icon' => 'pe-7s-browser',
                'erp' => 'Y',
                'project' => 'N',
                'mobile' => 'N',
                'children' => [
                    [
                        'menu_id' => 20,
                        'menu_name' => 'Galeri Dokumentasi',
                        'menu_page' => '/erp/galleries',
                        'menu_order' => 1,
                        'menu_icon' => 'pe-7s-photo',
                        'erp' => 'Y',
                    ],
                    [
                        'menu_id' => 21,
                        'menu_name' => 'Mitra Kerjasama',
                        'menu_page' => '/erp/partners',
                        'menu_order' => 2,
                        'menu_icon' => 'pe-7s-id',
                        'erp' => 'Y',
                    ],
                    [
                        'menu_id' => 22,
                        'menu_name' => 'Testimoni Klien',
                        'menu_page' => '/erp/testimonials',
                        'menu_order' => 3,
                        'menu_icon' => 'pe-7s-comment',
                        'erp' => 'Y',
                    ],
                    [
                        'menu_id' => 23,
                        'menu_name' => 'Tentang Perusahaan',
                        'menu_page' => '/erp/manage-about-page',
                        'menu_order' => 4,
                        'menu_icon' => 'pe-7s-info',
                        'erp' => 'Y',
                    ],
                ]
            ],

            // 3. Group: GENERAL SYSTEM (matching Image 3)
            [
                'menu_id' => 3,
                'menu_parent' => 0,
                'menu_name' => 'General System',
                'menu_page' => null,
                'menu_order' => 3,
                'menu_icon' => 'pe-7s-config',
                'erp' => 'Y',
                'project' => 'N',
                'mobile' => 'N',
                'children' => [
                    [
                        'menu_id' => 31,
                        'menu_name' => 'User Management',
                        'menu_page' => '/erp/users',
                        'menu_order' => 1,
                        'menu_icon' => 'pe-7s-user',
                        'erp' => 'Y',
                    ],
                    [
                        'menu_id' => 30,
                        'menu_name' => 'Menu Management',
                        'menu_page' => '/erp/system-menus',
                        'menu_order' => 2,
                        'menu_icon' => 'pe-7s-menu',
                        'erp' => 'Y',
                    ],
                ]
            ],
        ];

        foreach ($menus as $group) {
            $children = $group['children'] ?? [];
            unset($group['children']);

            AppMenu::create($group);
            AppMenuAccess::create([
                'usergrpid' => 1,
                'menu_id' => $group['menu_id'],
                'createdtime' => now(),
            ]);

            foreach ($children as $child) {
                $child['menu_parent'] = $group['menu_id'];
                AppMenu::create($child);
                AppMenuAccess::create([
                    'usergrpid' => 1,
                    'menu_id' => $child['menu_id'],
                    'createdtime' => now(),
                ]);
            }
        }
    }
}
