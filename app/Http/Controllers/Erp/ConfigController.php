<?php

namespace App\Http\Controllers\Erp;

use App\Http\Controllers\Controller;
use App\Models\CompanyConfig;
use App\Models\DropdownConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConfigController extends Controller
{
    /**
     * Display Master Configuration & Dropdowns Hub
     */
    public function index(Request $request)
    {
        $currentGroup = $request->query('group', 'procurement_type');
        $search = $request->query('search');

        // Groups available in system
        $groupLabels = [
            'procurement_type' => [
                'name' => 'Tipe Pengadaan PO',
                'name_zh' => '采购订单类型',
                'desc' => 'Pilihan tipe pengadaan pada PO Vendor (Sewa, Pembelian, Jasa, OPEX)'
            ],
            'service_type' => [
                'name' => 'Jenis Servis & Maintenance',
                'name_zh' => '维保与检修类型',
                'desc' => 'Kategori tindakan perawatan unit armada (Berkala, Ganti Oli, Overhaul, dll.)'
            ],
            'payment_term' => [
                'name' => 'Termin Pembayaran (TOP)',
                'name_zh' => '付款账期 (TOP)',
                'desc' => 'Syarat termin pembayaran transaksi (Net 30 Days, COD, DP, dll.)'
            ],
            'unit_satuan' => [
                'name' => 'Satuan Unit & Waktu',
                'name_zh' => '单位与时间计量',
                'desc' => 'Satuan kuantitas dan durasi (Unit, Bulan, Hari, Jam, Pcs)'
            ],
            'asset_status' => [
                'name' => 'Status Operasional Armada',
                'name_zh' => '车队运营状态',
                'desc' => 'Status unit armada (Available, Rented, Maintenance, Reserved)'
            ],
            'company' => [
                'name' => 'Profil Perusahaan & Bank',
                'name_zh' => '公司资料与银行账户',
                'desc' => 'Identitas resmi perusahaan, NPWP, Rekening Bank, dan Direksi'
            ],
        ];

        // If not company tab, query DropdownConfig
        if ($currentGroup !== 'company') {
            $query = DropdownConfig::where('group', $currentGroup);
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('key', 'like', "%{$search}%")
                      ->orWhere('label_id', 'like', "%{$search}%")
                      ->orWhere('label_zh', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }
            $options = $query->orderBy('order_num')->orderBy('id')->get();
        } else {
            $options = collect();
        }

        // Group counts
        $groupCounts = DropdownConfig::select('group', DB::raw('count(*) as total'))
            ->groupBy('group')
            ->pluck('total', 'group')
            ->toArray();

        // Company config records
        $companyConfigs = CompanyConfig::all()->keyBy('key');

        return view('erp.configs', compact(
            'currentGroup',
            'groupLabels',
            'options',
            'groupCounts',
            'companyConfigs',
            'search'
        ));
    }

    /**
     * Store new dropdown configuration option
     */
    public function store(Request $request)
    {
        abort_if(!auth()->user()?->isAdmin(), 403, 'Akses Ditolak: Hanya Administrator yang dapat mengubah konfigurasi.');

        $validated = $request->validate([
            'group' => 'required|string|max:50',
            'key' => 'required|string|max:100',
            'label_id' => 'required|string|max:255',
            'label_zh' => 'nullable|string|max:255',
            'badge_color' => 'nullable|string|max:50',
            'order_num' => 'nullable|integer',
            'description' => 'nullable|string',
        ]);

        $exists = DropdownConfig::where('group', $validated['group'])
            ->where('key', $validated['key'])
            ->exists();

        if ($exists) {
            return back()->with('error', "Kode '{$validated['key']}' sudah ada di grup ini.");
        }

        DropdownConfig::create([
            'group' => $validated['group'],
            'key' => $validated['key'],
            'label_id' => $validated['label_id'],
            'label_zh' => $validated['label_zh'] ?? null,
            'badge_color' => $validated['badge_color'] ?? 'slate',
            'order_num' => $validated['order_num'] ?? 0,
            'is_active' => true,
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->route('erp.configs.index', ['group' => $validated['group']])
            ->with('success', "Opsi '{$validated['label_id']}' berhasil ditambahkan!");
    }

    /**
     * Update dropdown configuration option
     */
    public function update(Request $request, $id)
    {
        abort_if(!auth()->user()?->isAdmin(), 403, 'Akses Ditolak: Hanya Administrator yang dapat mengubah konfigurasi.');

        $option = DropdownConfig::findOrFail($id);

        $validated = $request->validate([
            'label_id' => 'required|string|max:255',
            'label_zh' => 'nullable|string|max:255',
            'badge_color' => 'nullable|string|max:50',
            'order_num' => 'nullable|integer',
            'description' => 'nullable|string',
        ]);

        $option->update($validated);

        return redirect()->route('erp.configs.index', ['group' => $option->group])
            ->with('success', "Opsi '{$option->label_id}' berhasil diperbarui!");
    }

    /**
     * Toggle active/inactive status
     */
    public function toggleStatus($id)
    {
        abort_if(!auth()->user()?->isAdmin(), 403, 'Akses Ditolak: Hanya Administrator yang dapat mengubah konfigurasi.');

        $option = DropdownConfig::findOrFail($id);
        $option->is_active = !$option->is_active;
        $option->save();

        $statusText = $option->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Opsi '{$option->label_id}' berhasil {$statusText}!");
    }

    /**
     * Delete option
     */
    public function destroy($id)
    {
        abort_if(!auth()->user()?->isAdmin(), 403, 'Akses Ditolak: Hanya Administrator yang dapat mengubah konfigurasi.');

        $option = DropdownConfig::findOrFail($id);
        $label = $option->label_id;
        $group = $option->group;
        $option->delete();

        return redirect()->route('erp.configs.index', ['group' => $group])
            ->with('success', "Opsi '{$label}' berhasil dihapus!");
    }

    /**
     * Update Company Profile, NPWP & Bank Accounts
     */
    public function updateCompany(Request $request)
    {
        abort_if(!auth()->user()?->isAdmin(), 403, 'Akses Ditolak: Hanya Administrator yang dapat mengubah identitas perusahaan dan rekening bank.');
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'company_address' => 'required|string',
            'company_phone' => 'nullable|string|max:100',
            'company_email' => 'nullable|string|max:100',
            'company_npwp' => 'nullable|string|max:100',
            'director_name' => 'nullable|string|max:255',
            'director_title' => 'nullable|string|max:255',
            'bank_bca_account' => 'nullable|string|max:100',
            'bank_bri_account' => 'nullable|string|max:100',
        ]);

        CompanyConfig::set('company_name', $validated['company_name'], 'company', 'Nama Resmi Perusahaan');
        CompanyConfig::set('company_address', $validated['company_address'], 'company', 'Alamat Resmi Perusahaan');
        CompanyConfig::set('company_phone', $validated['company_phone'] ?? '', 'company', 'Telepon / WA');
        CompanyConfig::set('company_email', $validated['company_email'] ?? '', 'company', 'Email Resmi');
        CompanyConfig::set('company_npwp', $validated['company_npwp'] ?? '', 'company', 'NPWP Perusahaan');
        CompanyConfig::set('director_name', $validated['director_name'] ?? '', 'company', 'Nama Direktur');
        CompanyConfig::set('director_title', $validated['director_title'] ?? '', 'company', 'Jabatan Direktur');

        // Update Bank Accounts JSON
        $bankAccounts = [
            [
                'bank' => 'BCA',
                'account_number' => $validated['bank_bca_account'] ?? '7260555116',
                'holder' => 'KEY LOCK INDONESIA',
                'branch' => 'BCA KCP Grand Wisata'
            ],
            [
                'bank' => 'BRI',
                'account_number' => $validated['bank_bri_account'] ?? '609900978',
                'holder' => 'KEY LOCK INDONESIA',
                'branch' => 'BRI Biro Klasifikasi Indonesia'
            ]
        ];
        CompanyConfig::set('bank_accounts', json_encode($bankAccounts), 'bank', 'Rekening Bank');

        return redirect()->route('erp.configs.index', ['group' => 'company'])
            ->with('success', 'Profil Perusahaan & Rekening Bank berhasil diperbarui!');
    }
}
