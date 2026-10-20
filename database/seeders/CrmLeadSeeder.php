<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\LeadFollowUp;
use App\Models\LeadStageHistory;
use App\Models\User;
use App\Models\Contact;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CrmLeadSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        LeadStageHistory::truncate();
        LeadFollowUp::truncate();
        LeadActivity::truncate();
        Lead::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $sales1 = User::where('role', 'sales')->first() ?? User::first();
        $admin = User::where('role', 'admin')->first() ?? User::first();
        $salesId1 = $sales1->id;
        $adminId = $admin->id;

        $waskitaCustomer = Contact::where('type', 'customer')->where('name', 'like', '%Waskita%')->first();

        $leadsData = [
            [
                'code' => 'LEAD-202610-001',
                'company_name' => 'PT Indofood CBP Sukses Makmur',
                'industry' => 'Food & Beverages',
                'address' => 'Kawasan Industri MM2100 Blok B1, Cikarang Barat, Bekasi',
                'website' => 'https://www.indofoodcbp.com',
                'source' => 'Website',
                'entry_date' => Carbon::now()->subDays(12),
                'sales_id' => $salesId1,
                'pic_name' => 'Arif Rachman',
                'pic_title' => 'Logistics & Warehouse Manager',
                'pic_phone' => '0811-9887-766',
                'pic_email' => 'arif.rachman@indofood.co.id',
                'additional_pics' => [
                    ['name' => 'Siti Nurhaliza', 'title' => 'Procurement Officer', 'phone' => '0812-3344-5566', 'email' => 'siti.proc@indofood.co.id']
                ],
                'deal_type' => 'rental',
                'product_interest' => 'Forklift Diesel 3 Ton Toyota FD30',
                'specification' => 'Mast 4.5 Meter, Solid Tires, Side Shifter',
                'quantity' => 2,
                'rental_duration' => '6 Bulan',
                'project_location' => 'Gudang Cikarang Barat',
                'estimated_budget' => 50000000,
                'estimated_value' => 45000000,
                'expected_closing_date' => Carbon::now()->addDays(10),
                'customer_notes' => 'Kebutuhan mendesak untuk ekspansi gudang bumbu instan akhir bulan ini.',
                'pipeline_stage' => 'qualified',
                'priority' => 'high',
                'internal_notes' => 'Customer existing grup Indofood. Track record payment selalu tepat waktu.',
            ],
            [
                'code' => 'LEAD-202610-002',
                'company_name' => 'PT Astra Honda Motor',
                'industry' => 'Automotive Manufacturing',
                'address' => 'Jl. Raya Pegangsaan Dua Km. 2.2, Kelapa Gading, Jakarta Utara',
                'website' => 'https://www.astra-honda.com',
                'source' => 'Referral',
                'entry_date' => Carbon::now()->subDays(9),
                'sales_id' => $salesId1,
                'pic_name' => 'Bambang Sudiro',
                'pic_title' => 'Maintenance Facility Lead',
                'pic_phone' => '0812-9876-5432',
                'pic_email' => 'bambang.sudiro@ahm.astra.co.id',
                'additional_pics' => null,
                'deal_type' => 'rental',
                'product_interest' => 'Scissor Lift Electric 10 Meter Genie GS-2632',
                'specification' => 'Working height 10M, Non-marking tires, Platform extension',
                'quantity' => 3,
                'rental_duration' => '3 Bulan',
                'project_location' => 'Pabrik AHM Plant 3 Cikarang',
                'estimated_budget' => 80000000,
                'estimated_value' => 75000000,
                'expected_closing_date' => Carbon::now()->addDays(5),
                'customer_notes' => 'Untuk perawatan plafon dan instalasi kabel jalur perakitan motor.',
                'pipeline_stage' => 'proposal',
                'priority' => 'high',
                'internal_notes' => 'Sudah dikirimkan penawaran resmi QTN-202610-001. Menunggu approval Dept Head.',
            ],
            [
                'code' => 'LEAD-202610-003',
                'company_name' => 'PT Mayora Indah Cikarang',
                'industry' => 'FMCG Confectionery',
                'address' => 'Kawasan Industri Jababeka 1, Cikarang, Jawa Barat',
                'website' => 'https://www.mayoraindah.co.id',
                'source' => 'WhatsApp',
                'entry_date' => Carbon::now()->subDays(18),
                'sales_id' => $adminId,
                'pic_name' => 'Dewi Sartika',
                'pic_title' => 'Purchasing Supervisor',
                'pic_phone' => '0815-7654-3210',
                'pic_email' => 'dewi.sartika@mayora.co.id',
                'additional_pics' => [
                    ['name' => 'Hendra Saputra', 'title' => 'Site Manager', 'phone' => '0811-2233-4455', 'email' => 'hendra.site@mayora.co.id']
                ],
                'deal_type' => 'rental',
                'product_interest' => 'Forklift Diesel 5 Ton Komatsu FD50',
                'specification' => 'Fork 1800mm, Mast 4 Meter, Fork Positioner',
                'quantity' => 2,
                'rental_duration' => '1 Tahun (Kontrak Panjang)',
                'project_location' => 'Plant Mayora Biskuit Cikarang',
                'estimated_budget' => 200000000,
                'estimated_value' => 180000000,
                'expected_closing_date' => Carbon::now()->addDays(8),
                'customer_notes' => 'Meminta diskon kontrak tahunan dan free sparepart rutin servis berkala.',
                'pipeline_stage' => 'negotiation',
                'priority' => 'high',
                'internal_notes' => 'Sedang negosiasi penyesuaian biaya mobilisasi pulang pergi dan klausul overtime operator.',
            ],
            [
                'code' => 'LEAD-202610-004',
                'company_name' => 'PT Waskita Karya Proyek LRT',
                'industry' => 'Infrastruktur & Konstruksi',
                'address' => 'Jl. MT Haryono Kav. 10, Cawang, Jakarta Timur',
                'website' => 'https://www.waskita.co.id',
                'source' => 'Kunjungan',
                'entry_date' => Carbon::now()->subDays(25),
                'sales_id' => $salesId1,
                'pic_name' => 'Ir. Hendra Gunawan',
                'pic_title' => 'Kepala Proyek Depo LRT',
                'pic_phone' => '0813-8765-4321',
                'pic_email' => 'hendra.gunawan@waskita.co.id',
                'additional_pics' => null,
                'deal_type' => 'rental',
                'product_interest' => 'Scissor Lift Electric 14 Meter Dingli JCPT1412DC',
                'specification' => '14 Meter Platform Height, AC drive, Heavy duty',
                'quantity' => 2,
                'rental_duration' => '3 Bulan',
                'project_location' => 'Stasiun Depo LRT Jabodebek Dukuh Atas',
                'estimated_budget' => 95000000,
                'estimated_value' => 90000000,
                'expected_closing_date' => Carbon::now()->subDays(2),
                'customer_notes' => 'Unit langsung dikirimkan ke lokasi stasiun.',
                'pipeline_stage' => 'won',
                'priority' => 'normal',
                'internal_notes' => 'Deal closed! PO resmi sudah terbit. Sudah dibuatkan Surat Jalan Pengiriman DO.',
                'converted_customer_id' => $waskitaCustomer ? $waskitaCustomer->id : null,
                'converted_at' => Carbon::now()->subDays(2),
            ],
            [
                'code' => 'LEAD-202610-005',
                'company_name' => 'PT Gudang Garam Tbk',
                'industry' => 'Tobacco & Cigarette',
                'address' => 'Jl. Jenderal A. Yani No. 79, Jakarta Pusat',
                'website' => 'https://www.gudanggaramtbk.com',
                'source' => 'WhatsApp',
                'entry_date' => Carbon::now()->subDays(4),
                'sales_id' => $salesId1,
                'pic_name' => 'Agus Pratama',
                'pic_title' => 'Warehouse Staff',
                'pic_phone' => '0821-4567-8901',
                'pic_email' => 'agus.pratama@gudanggaramtbk.com',
                'additional_pics' => null,
                'deal_type' => 'rental',
                'product_interest' => 'Forklift EP Electric ELF353',
                'specification' => 'Electric Lithium, zero emission, clean warehouse',
                'quantity' => 1,
                'rental_duration' => '1 Bulan',
                'project_location' => 'Gudang Distribusi Cakung',
                'estimated_budget' => 25000000,
                'estimated_value' => 22000000,
                'expected_closing_date' => Carbon::now()->addDays(14),
                'customer_notes' => 'Menanyakan ketersediaan unit forklift elektrik ramah lingkungan untuk gudang tertutup.',
                'pipeline_stage' => 'contacted',
                'priority' => 'normal',
                'internal_notes' => 'Brosur unit ELF353 sudah dikirimkan via WhatsApp. Menunggu konfirmasi jadwal video call presentasi.',
            ],
            [
                'code' => 'LEAD-202610-006',
                'company_name' => 'PT Kalbe Farma Cikarang',
                'industry' => 'Farmasi & Kesehatan',
                'address' => 'Kawasan Industri Delta Silicon 2, Lippo Cikarang',
                'website' => 'https://www.kalbe.co.id',
                'source' => 'Website',
                'entry_date' => Carbon::now()->subHours(18),
                'sales_id' => $salesId1,
                'pic_name' => 'Fitria Anggraini',
                'pic_title' => 'QA / Facility Specialist',
                'pic_phone' => '0878-1234-5678',
                'pic_email' => 'fitria.anggraini@kalbe.co.id',
                'additional_pics' => null,
                'deal_type' => 'both',
                'product_interest' => 'Forklift Electric Cleanroom',
                'specification' => 'Battery Lithium, white non-marking tires, cleanroom certification',
                'quantity' => 2,
                'rental_duration' => 'Rental / Beli Opsi',
                'project_location' => 'Pabrik Kalbe Farma Lippo Cikarang',
                'estimated_budget' => 90000000,
                'estimated_value' => 85000000,
                'expected_closing_date' => Carbon::now()->addDays(20),
                'customer_notes' => 'Inquiry masuk melalui formulir website resmi KEYLOCK tadi malam.',
                'pipeline_stage' => 'new',
                'priority' => 'high',
                'internal_notes' => 'Leads baru, belum dihubungi sama sekali. Perlu segera di-follow up pagi ini.',
            ],
            [
                'code' => 'LEAD-202610-007',
                'company_name' => 'PT Unilever Indonesia Rungkut',
                'industry' => 'Consumer Goods',
                'address' => 'Kawasan Industri Rungkut, Surabaya',
                'website' => 'https://www.unilever.co.id',
                'source' => 'Email',
                'entry_date' => Carbon::now()->subDays(20),
                'sales_id' => $adminId,
                'pic_name' => 'Dimas Wicaksono',
                'pic_title' => 'Capex Procurement',
                'pic_phone' => '0817-6543-2199',
                'pic_email' => 'dimas.wicaksono@unilever.com',
                'additional_pics' => null,
                'deal_type' => 'penjualan',
                'product_interest' => 'Forklift Diesel 5 Ton Komatsu',
                'specification' => 'Unit baru OTR Jawa Timur',
                'quantity' => 1,
                'rental_duration' => null,
                'project_location' => 'Surabaya',
                'estimated_budget' => 450000000,
                'estimated_value' => 420000000,
                'expected_closing_date' => Carbon::now()->subDays(5),
                'customer_notes' => 'Membatalkan rencana pembelian karena pergeseran anggaran capex regional.',
                'pipeline_stage' => 'lost',
                'priority' => 'low',
                'internal_notes' => 'Lost deal karena pemotongan anggaran tahunan dari kantor pusat regional.',
            ],
            [
                'code' => 'LEAD-202610-008',
                'company_name' => 'PT Cikarang Dry Port Logistics',
                'industry' => 'Logistik Pelabuhan Darat',
                'address' => 'Jl. Kenari Raya Blok G6, Jababeka Cikarang',
                'website' => 'https://www.cikarangdryport.com',
                'source' => 'Telepon',
                'entry_date' => Carbon::now()->subDays(1),
                'sales_id' => $salesId1,
                'pic_name' => 'Surya Saputra',
                'pic_title' => 'Depo Yard Supervisor',
                'pic_phone' => '0819-8765-4321',
                'pic_email' => 'surya@cikarangdryport.com',
                'additional_pics' => null,
                'deal_type' => 'rental',
                'product_interest' => 'Forklift Diesel 5 Ton Komatsu FD50',
                'specification' => 'Heavy duty container yard',
                'quantity' => 1,
                'rental_duration' => '1 Bulan',
                'project_location' => 'Depo Kontainer Jababeka',
                'estimated_budget' => 45000000,
                'estimated_value' => 40000000,
                'expected_closing_date' => Carbon::now()->addDays(12),
                'customer_notes' => 'Menghubungi via hotline minta penawaran sewa 1 bulan pengganti unit mereka yang sedang overhaul.',
                'pipeline_stage' => 'new',
                'priority' => 'normal',
                'internal_notes' => 'Perlu cek ketersediaan unit Komatsu FD50 di pool sebelum kirim draft harga.',
            ],
            [
                'code' => 'LEAD-202610-009',
                'company_name' => 'PT Toyota Boshoku Indonesia',
                'industry' => 'Komponen Otomotif',
                'address' => 'Kawasan Industri MM2100, Gandamekar, Bekasi',
                'website' => 'https://www.toyota-boshoku.com',
                'source' => 'LinkedIn',
                'entry_date' => Carbon::now()->subDays(15),
                'sales_id' => $salesId1,
                'pic_name' => 'Kuncoro Adi',
                'pic_title' => 'Purchasing Section Head',
                'pic_phone' => '0812-1122-3344',
                'pic_email' => 'kuncoro.adi@tbina.com',
                'additional_pics' => null,
                'deal_type' => 'penjualan',
                'product_interest' => 'Forklift EP Electric ELF353',
                'specification' => '2 Unit Pembelian Baru + Garansi 2 Tahun',
                'quantity' => 2,
                'rental_duration' => null,
                'project_location' => 'Pabrik MM2100',
                'estimated_budget' => 250000000,
                'estimated_value' => 220000000,
                'expected_closing_date' => Carbon::now()->addDays(7),
                'customer_notes' => 'Pembelian unit forklift elektrik untuk penggantian unit lama.',
                'pipeline_stage' => 'negotiation',
                'priority' => 'high',
                'internal_notes' => 'Kompetitor menawarkan brand Toyota. Keunggulan kita di baterai lithium Li-Ion maintenance free dan delivery cepat.',
            ],
            [
                'code' => 'LEAD-202610-010',
                'company_name' => 'PT Schneider Electric Manufacturing',
                'industry' => 'Electrical Equipment',
                'address' => 'Kawasan Industri EJIP Plot 4B, Cikarang Selatan',
                'website' => 'https://www.se.com/id',
                'source' => 'Website',
                'entry_date' => Carbon::now()->subDays(7),
                'sales_id' => $salesId1,
                'pic_name' => 'Yudi Permana',
                'pic_title' => 'Safety & Facility Manager',
                'pic_phone' => '0813-9988-7766',
                'pic_email' => 'yudi.permana@se.com',
                'additional_pics' => null,
                'deal_type' => 'rental',
                'product_interest' => 'Scissor Lift Electric 10 Meter Genie GS-2632',
                'specification' => 'Unit bersih, sertifikat Depnaker/SIA masih aktif',
                'quantity' => 2,
                'rental_duration' => '6 Bulan',
                'project_location' => 'EJIP Industrial Park',
                'estimated_budget' => 60000000,
                'estimated_value' => 54000000,
                'expected_closing_date' => Carbon::now()->addDays(6),
                'customer_notes' => 'Wajib lolos safety clearance HSE Schneider.',
                'pipeline_stage' => 'proposal',
                'priority' => 'normal',
                'internal_notes' => 'Dokumen SILO/SIA unit sudah kita kirimkan bersama proposal penawaran.',
            ]
        ];

        foreach ($leadsData as $data) {
            $lead = Lead::create($data);

            // Create initial stage history
            LeadStageHistory::create([
                'lead_id' => $lead->id,
                'user_id' => $lead->sales_id,
                'from_stage' => 'new',
                'to_stage' => $lead->pipeline_stage,
                'change_reason' => 'Pencatatan awal calon prospek di sistem CRM KEYLOCK.',
                'created_at' => $lead->entry_date,
            ]);

            // Add activities
            if ($lead->pipeline_stage !== 'new') {
                LeadActivity::create([
                    'lead_id' => $lead->id,
                    'user_id' => $lead->sales_id,
                    'activity_type' => 'Telepon',
                    'activity_date' => Carbon::parse($lead->entry_date)->addHours(4),
                    'summary' => 'Kontak Awal via Telepon dengan ' . $lead->pic_name,
                    'communication_notes' => 'Melakukan perkenalan dan kualifikasi kebutuhan alat berat. Calon customer menjelaskan detail proyek dan estimasi waktu penggunaan.',
                    'attachment' => null,
                ]);

                LeadActivity::create([
                    'lead_id' => $lead->id,
                    'user_id' => $lead->sales_id,
                    'activity_type' => 'Brosur Terkirim',
                    'activity_date' => Carbon::parse($lead->entry_date)->addHours(6),
                    'summary' => 'Pengiriman E-Brosur & Spesifikasi Teknis ' . ($lead->product_interest ?: 'Unit'),
                    'communication_notes' => 'E-Brosur resmi KEYLOCK dan lembar spesifikasi unit telah dikirimkan via WhatsApp dan Email PIC.',
                    'attachment' => 'brosur-keylock-indonesia-2026.pdf',
                ]);
            }

            if (in_array($lead->pipeline_stage, ['proposal', 'negotiation', 'won'])) {
                LeadActivity::create([
                    'lead_id' => $lead->id,
                    'user_id' => $lead->sales_id,
                    'activity_type' => 'Email',
                    'activity_date' => Carbon::parse($lead->entry_date)->addDays(2),
                    'summary' => 'Kirim Surat Penawaran Resmi & Surat Pernyataan Kelaikan Unit',
                    'communication_notes' => 'Penawaran harga resmi (Quotation) terkirim. PIC menerima dengan baik dan meneruskan ke tim Finance/Purchasing.',
                    'attachment' => 'quotation-' . strtolower($lead->code) . '.pdf',
                ]);

                LeadActivity::create([
                    'lead_id' => $lead->id,
                    'user_id' => $lead->sales_id,
                    'activity_type' => 'Meeting',
                    'activity_date' => Carbon::parse($lead->entry_date)->addDays(4),
                    'summary' => 'Meeting Koordinasi Teknis Lapangan',
                    'communication_notes' => 'Membahas akses jalan trailer saat pengiriman (delivery) dan jadwal loading alat ke lokasi proyek.',
                    'attachment' => null,
                ]);
            }

            // Add Follow-up tasks
            if ($lead->id == 1) {
                // Today's pending follow up
                LeadFollowUp::create([
                    'lead_id' => $lead->id,
                    'user_id' => $lead->sales_id,
                    'follow_up_type' => 'WhatsApp',
                    'scheduled_at' => Carbon::today()->setHour(14)->setMinute(30),
                    'purpose_notes' => 'Follow-up keputusan manajemen Indofood terkait draft kontrak sewa 6 bulan Forklift Toyota.',
                    'status' => 'pending',
                ]);
            } elseif ($lead->id == 2) {
                // Overdue follow up
                LeadFollowUp::create([
                    'lead_id' => $lead->id,
                    'user_id' => $lead->sales_id,
                    'follow_up_type' => 'Telepon',
                    'scheduled_at' => Carbon::yesterday()->setHour(10)->setMinute(0),
                    'purpose_notes' => 'Konfirmasi approval PO 3 Unit Scissor Lift 10M untuk perawatan pabrik AHM.',
                    'status' => 'pending',
                ]);
            } elseif ($lead->id == 3) {
                // Upcoming tomorrow follow up
                LeadFollowUp::create([
                    'lead_id' => $lead->id,
                    'user_id' => $lead->sales_id,
                    'follow_up_type' => 'Meeting',
                    'scheduled_at' => Carbon::tomorrow()->setHour(11)->setMinute(0),
                    'purpose_notes' => 'Meeting finalisasi klausul kontrak tahunan dan jadwal inspeksi unit Komatsu FD50.',
                    'status' => 'pending',
                ]);
            } elseif ($lead->id == 4) {
                // Completed follow up
                LeadFollowUp::create([
                    'lead_id' => $lead->id,
                    'user_id' => $lead->sales_id,
                    'follow_up_type' => 'Kunjungan',
                    'scheduled_at' => Carbon::now()->subDays(3)->setHour(9)->setMinute(0),
                    'purpose_notes' => 'Kunjungan site inspeksi serah terima unit di depo LRT Cawang.',
                    'status' => 'completed',
                    'completed_at' => Carbon::now()->subDays(3)->setHour(11)->setMinute(30),
                    'result_notes' => 'Unit Scissor Lift diterima oleh tim Waskita dalam kondisi prima. Dokumen BAST ditandatangani.',
                ]);
            } elseif ($lead->id == 6) {
                // Today's pending for new lead
                LeadFollowUp::create([
                    'lead_id' => $lead->id,
                    'user_id' => $lead->sales_id,
                    'follow_up_type' => 'Telepon',
                    'scheduled_at' => Carbon::today()->setHour(16)->setMinute(0),
                    'purpose_notes' => 'Kontak perdana Ibu Fitria (Kalbe Farma) untuk kualifikasi forklift cleanroom lithium.',
                    'status' => 'pending',
                ]);
            }
        }
    }
}
