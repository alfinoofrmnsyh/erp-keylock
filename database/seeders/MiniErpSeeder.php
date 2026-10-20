<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Contact;
use App\Models\Item;
use App\Models\Asset;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CompanyConfig;
use App\Models\User;

class MiniErpSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Company Configs
        $configs = [
            ['key' => 'company_name', 'value' => 'PT Keylock Indonesia Perkasa', 'group' => 'company', 'description' => 'Nama resmi perusahaan'],
            ['key' => 'company_address', 'value' => 'Kawasan Industri GIIC Blok AB No. 12, Cikarang Pusat, Bekasi, Jawa Barat', 'group' => 'company', 'description' => 'Alamat operasional pusat'],
            ['key' => 'company_phone', 'value' => '+62 21 8990 1234 / +62 811 2345 6789', 'group' => 'company', 'description' => 'Nomor kontak resmi & WA hotline'],
            ['key' => 'company_email', 'value' => 'sales@keylock.co.id', 'group' => 'company', 'description' => 'Email sales & penawaran'],
            ['key' => 'bank_accounts', 'value' => json_encode([
                ['bank' => 'BCA', 'account_number' => '842-099-1234', 'holder' => 'PT KEYLOCK INDONESIA PERKASA'],
                ['bank' => 'MANDIRI', 'account_number' => '156-00-1892837-1', 'holder' => 'PT KEYLOCK INDONESIA PERKASA']
            ]), 'group' => 'bank', 'description' => 'Daftar rekening bank pembayaran'],
            ['key' => 'default_tax_rate', 'value' => '11', 'group' => 'tax', 'description' => 'Tarif standar PPN (%)'],
            ['key' => 'quotation_terms', 'value' => "1. Harga belum termasuk PPN 11% kecuali dinyatakan lain.\n2. Pembayaran DP 50% saat PO diterbitkan, pelunasan sebelum unit dikirim.\n3. Biaya mobilisasi & demobilisasi ditanggung oleh penyewa.\n4. Jam kerja standar 200 jam/bulan atau 8 jam/hari.", 'group' => 'quotation', 'description' => 'Syarat & ketentuan default penawaran'],
        ];

        foreach ($configs as $cfg) {
            CompanyConfig::updateOrCreate(['key' => $cfg['key']], $cfg);
        }

        // 2. Contacts (Customers, Leads, Vendors)
        $contacts = [
            [
                'type' => 'customer',
                'code' => 'CUST-001',
                'name' => 'PT Surya Semesta Internusa Tbk',
                'pic_name' => 'Bambang Sudiro',
                'email' => 'bambang.s@suryasemesta.com',
                'phone' => '081298765432',
                'billing_address' => 'Surya Tower Lt. 15, Jl. MH Thamrin No. 9, Jakarta Pusat',
                'shipping_address' => 'Suryacipta City of Industry, Karawang Timur',
                'credit_limit' => 250000000,
                'payment_term_days' => 30,
                'npwp' => '01.234.567.8-012.000',
            ],
            [
                'type' => 'customer',
                'code' => 'CUST-002',
                'name' => 'PT Waskita Beton Precast Tbk',
                'pic_name' => 'Ir. Hendra Gunawan',
                'email' => 'procurement@waskitaprecast.co.id',
                'phone' => '081387654321',
                'billing_address' => 'Gedung Teraskita Lt. 3, Jl. MT Haryono Kav. No. 10A, Jakarta Timur',
                'shipping_address' => 'Plant Sadang, Purwakarta, Jawa Barat',
                'credit_limit' => 500000000,
                'payment_term_days' => 45,
                'npwp' => '02.345.678.9-034.000',
            ],
            [
                'type' => 'customer',
                'code' => 'CUST-003',
                'name' => 'PT Mayora Indah Tbk',
                'pic_name' => 'Dewi Sartika',
                'email' => 'dewi.purchasing@mayora.co.id',
                'phone' => '081576543210',
                'billing_address' => 'Gedung Mayora, Jl. Tomang Raya 21-23, Jakarta Barat',
                'shipping_address' => 'Kawasan Industri MM2100, Cikarang Barat',
                'credit_limit' => 150000000,
                'payment_term_days' => 30,
                'npwp' => '01.333.444.5-055.000',
            ],
            [
                'type' => 'lead',
                'code' => 'LEAD-001',
                'name' => 'PT Indofood CBP Sukses Makmur',
                'pic_name' => 'Arif Rachman',
                'email' => 'arif.r@icbp.indofood.co.id',
                'phone' => '08119887766',
                'shipping_address' => 'Jl. Kp. Jarakosta, Danau Indah, Cikarang Barat',
                'lead_source' => 'Website Company Profile',
                'lead_status' => 'qualified',
                'notes' => 'Kebutuhan rental 2 unit Scissor Lift 12 meter durasi 2 bulan untuk maintenance pabrik',
            ],
            [
                'type' => 'vendor',
                'code' => 'VEND-001',
                'name' => 'PT Traktor Nusantara',
                'pic_name' => 'Suhendra',
                'email' => 'spareparts@traknus.co.id',
                'phone' => '0214608888',
                'billing_address' => 'Jl. Pulo Gadung No. 32, Kawasan Industri Pulogadung, Jakarta',
                'payment_term_days' => 30,
            ]
        ];

        foreach ($contacts as $c) {
            Contact::updateOrCreate(['code' => $c['code']], $c);
        }

        // 3. Catalog Items (Rental & Sale Units)
        $items = [
            [
                'sku' => 'FL-TOY-3T-D',
                'name' => 'Forklift Diesel 3 Ton Toyota FD30',
                'category' => 'Forklift',
                'brand' => 'Toyota',
                'model' => '8FD30 (Mast 3-4.5m)',
                'unit' => 'Unit',
                'item_type' => 'rental_unit',
                'cost_price' => 280000000,
                'sale_price' => 345000000,
                'rental_rate_daily' => 950000,
                'rental_rate_monthly' => 14500000,
                'overtime_rate_per_hour' => 85000,
                'description' => 'Engine Diesel 1DZ-II, Kapasitas angkat 3.000 kg, Solid Tyre, Duplex/Triplex mast opsional.',
            ],
            [
                'sku' => 'FL-KOM-5T-D',
                'name' => 'Forklift Diesel 5 Ton Komatsu FD50',
                'category' => 'Forklift',
                'brand' => 'Komatsu',
                'model' => 'FD50AYT-10',
                'unit' => 'Unit',
                'item_type' => 'rental_unit',
                'cost_price' => 520000000,
                'sale_price' => 640000000,
                'rental_rate_daily' => 1800000,
                'rental_rate_monthly' => 26000000,
                'overtime_rate_per_hour' => 125000,
                'description' => 'Heavy duty diesel engine, kapasitas angkat 5.000 kg, cocok untuk pabrik baja dan precast.',
            ],
            [
                'sku' => 'SL-GEN-10M-E',
                'name' => 'Scissor Lift Electric 10 Meter Genie GS-2632',
                'category' => 'Scissor Lift',
                'brand' => 'Genie',
                'model' => 'GS-2632',
                'unit' => 'Unit',
                'item_type' => 'rental_unit',
                'cost_price' => 210000000,
                'sale_price' => 265000000,
                'rental_rate_daily' => 850000,
                'rental_rate_monthly' => 12500000,
                'overtime_rate_per_hour' => 75000,
                'description' => 'Tinggi platform 7.9m (working height 9.92m), non-marking tyre, zero emission cocok untuk indoor.',
            ],
            [
                'sku' => 'SL-DIN-14M-E',
                'name' => 'Scissor Lift Electric 14 Meter Dingli JCPT1412DC',
                'category' => 'Scissor Lift',
                'brand' => 'Dingli',
                'model' => 'JCPT1412DC',
                'unit' => 'Unit',
                'item_type' => 'rental_unit',
                'cost_price' => 275000000,
                'sale_price' => 330000000,
                'rental_rate_daily' => 1100000,
                'rental_rate_monthly' => 16500000,
                'overtime_rate_per_hour' => 90000,
                'description' => 'Working height 13.8m, kapasitas platform 320 kg, lipat compact masuk pintu standar.',
            ],
            [
                'sku' => 'SP-OIL-HYD-20L',
                'name' => 'Oli Hidrolik Shell Tellus S2 MX 68 (20 Liter)',
                'category' => 'Sparepart',
                'brand' => 'Shell',
                'model' => 'Tellus S2 MX 68',
                'unit' => 'Pail',
                'item_type' => 'sparepart',
                'cost_price' => 850000,
                'sale_price' => 1150000,
                'description' => 'Pelumas hidrolik industri berkualitas tinggi untuk sistem mast dan steering.',
            ]
        ];

        foreach ($items as $it) {
            Item::updateOrCreate(['sku' => $it['sku']], $it);
        }

        // 4. Physical Assets (Unit Fisik Forklift & Scissor Lift)
        $toyotaItem = Item::where('sku', 'FL-TOY-3T-D')->first();
        $komatsuItem = Item::where('sku', 'FL-KOM-5T-D')->first();
        $genieItem = Item::where('sku', 'SL-GEN-10M-E')->first();
        $dingliItem = Item::where('sku', 'SL-DIN-14M-E')->first();

        $assets = [
            [
                'item_id' => $toyotaItem->id,
                'asset_code' => 'AST-FL-001',
                'serial_number' => '8FD30-681920',
                'year' => 2023,
                'hour_meter' => 1420.5,
                'current_location' => 'Site Proyek PT Surya Semesta Karawang',
                'status' => 'rented',
                'condition_notes' => 'Unit prima, jadwal servis 250 jam berkala terlaksana.',
            ],
            [
                'item_id' => $toyotaItem->id,
                'asset_code' => 'AST-FL-002',
                'serial_number' => '8FD30-710442',
                'year' => 2024,
                'hour_meter' => 610.0,
                'current_location' => 'Gudang Utama Cikarang',
                'status' => 'available',
                'condition_notes' => 'Siap operasi, baterai baru diganti, ban solid 90%.',
            ],
            [
                'item_id' => $komatsuItem->id,
                'asset_code' => 'AST-FL-003',
                'serial_number' => 'FD50AY-80129',
                'year' => 2022,
                'hour_meter' => 2850.0,
                'current_location' => 'Workshop Pusat Cikarang',
                'status' => 'maintenance',
                'condition_notes' => 'Sedang penggantian seal silinder tilt dan ganti oli hidrolik.',
            ],
            [
                'item_id' => $genieItem->id,
                'asset_code' => 'AST-SL-001',
                'serial_number' => 'GS32-114902',
                'year' => 2023,
                'hour_meter' => 380.0,
                'current_location' => 'Gudang Utama Cikarang',
                'status' => 'available',
                'condition_notes' => 'Kondisi 98%, charger onboard normal.',
            ],
            [
                'item_id' => $dingliItem->id,
                'asset_code' => 'AST-SL-002',
                'serial_number' => 'JCPT14-99021',
                'year' => 2024,
                'hour_meter' => 195.5,
                'current_location' => 'Site PT Mayora MM2100',
                'status' => 'rented',
                'condition_notes' => 'Unit dalam masa sewa 3 bulan.',
            ],
        ];

        foreach ($assets as $ast) {
            Asset::updateOrCreate(['asset_code' => $ast['asset_code']], $ast);
        }

        // 5. Sample Quotation & Orders
        $user = User::first();
        $userId = $user ? $user->id : null;
        $cust1 = Contact::where('code', 'CUST-001')->first();
        $cust2 = Contact::where('code', 'CUST-002')->first();
        $lead1 = Contact::where('code', 'LEAD-001')->first();

        // Quotation 1 (Approved -> Rental Order)
        $qtn1 = Order::updateOrCreate(
            ['order_number' => 'QTN-2026-10-001'],
            [
                'order_type' => 'quotation',
                'contact_id' => $cust1->id,
                'order_date' => now()->subDays(10)->format('Y-m-d'),
                'valid_until' => now()->addDays(20)->format('Y-m-d'),
                'rental_start_date' => now()->subDays(5)->format('Y-m-d'),
                'rental_end_date' => now()->addDays(25)->format('Y-m-d'),
                'rental_duration' => 1,
                'rental_duration_unit' => 'month',
                'status' => 'approved',
                'delivery_status' => 'delivered',
                'invoice_status' => 'invoiced',
                'subtotal' => 14500000,
                'discount_amount' => 500000,
                'tax_percent' => 11,
                'tax_amount' => 1540000,
                'shipping_fee' => 1200000,
                'total_amount' => 16740000,
                'deposit_amount' => 5000000,
                'payment_term' => 'DP 50%, Pelunasan H+30',
                'shipping_address' => 'Suryacipta City of Industry, Karawang Timur',
                'notes' => 'Termasuk operator non-standby, overtime dihitung Rp 85.000/jam.',
                'created_by' => $userId,
            ]
        );

        OrderItem::updateOrCreate(
            ['order_id' => $qtn1->id, 'description' => 'Rental Forklift Diesel 3 Ton Toyota FD30 (1 Bulan)'],
            [
                'item_id' => $toyotaItem->id,
                'quantity' => 1,
                'unit' => 'Bulan',
                'unit_price' => 14500000,
                'duration' => 1,
                'discount' => 500000,
                'tax_amount' => 1540000,
                'line_total' => 14000000,
            ]
        );

        // Quotation 2 (Sent to Lead - Prospek Hangat)
        $qtn2 = Order::updateOrCreate(
            ['order_number' => 'QTN-2026-10-002'],
            [
                'order_type' => 'quotation',
                'contact_id' => $lead1->id,
                'order_date' => now()->subDays(2)->format('Y-m-d'),
                'valid_until' => now()->addDays(12)->format('Y-m-d'),
                'rental_start_date' => now()->addDays(5)->format('Y-m-d'),
                'rental_end_date' => now()->addDays(65)->format('Y-m-d'),
                'rental_duration' => 2,
                'rental_duration_unit' => 'month',
                'status' => 'sent',
                'delivery_status' => 'pending',
                'invoice_status' => 'unbilled',
                'subtotal' => 33000000,
                'discount_amount' => 1000000,
                'tax_percent' => 11,
                'tax_amount' => 3520000,
                'shipping_fee' => 1500000,
                'total_amount' => 37020000,
                'deposit_amount' => 10000000,
                'payment_term' => 'DP 50% sebelum mobilisasi, Sisa H+14',
                'shipping_address' => 'Jl. Kp. Jarakosta, Danau Indah, Cikarang Barat',
                'notes' => 'Rental 2 unit Scissor Lift 14 Meter Dingli untuk proyek maintenance plafon warehouse.',
                'created_by' => $userId,
            ]
        );

        OrderItem::updateOrCreate(
            ['order_id' => $qtn2->id, 'description' => 'Rental Scissor Lift Electric 14 Meter Dingli (2 Unit x 2 Bulan)'],
            [
                'item_id' => $dingliItem->id,
                'quantity' => 2,
                'unit' => 'Unit',
                'unit_price' => 16500000,
                'duration' => 2,
                'discount' => 1000000,
                'tax_amount' => 3520000,
                'line_total' => 32000000,
            ]
        );

        // Rental Order (Child of QTN 1)
        Order::updateOrCreate(
            ['order_number' => 'RO-2026-10-001'],
            [
                'order_type' => 'rental_order',
                'contact_id' => $cust1->id,
                'parent_id' => $qtn1->id,
                'order_date' => now()->subDays(5)->format('Y-m-d'),
                'rental_start_date' => now()->subDays(5)->format('Y-m-d'),
                'rental_end_date' => now()->addDays(25)->format('Y-m-d'),
                'rental_duration' => 1,
                'rental_duration_unit' => 'month',
                'status' => 'in_progress',
                'delivery_status' => 'delivered',
                'invoice_status' => 'invoiced',
                'subtotal' => 14500000,
                'discount_amount' => 500000,
                'tax_percent' => 11,
                'tax_amount' => 1540000,
                'shipping_fee' => 1200000,
                'total_amount' => 16740000,
                'deposit_amount' => 5000000,
                'payment_term' => 'DP 50%, Pelunasan H+30',
                'shipping_address' => 'Suryacipta City of Industry, Karawang Timur',
                'created_by' => $userId,
            ]
        );
    }
}
