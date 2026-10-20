<?php

namespace App\Http\Controllers\Erp;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetService;
use App\Models\CompanyConfig;
use App\Models\Delivery;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Role;
use App\Models\User;
use App\Models\AppMenu;
use App\Models\AppMenuAccess;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * Get configured permissions for report tabs from AppMenuAccess and CompanyConfig
     */
    protected function getTabPermissions(): array
    {
        $tabMenuPages = [
            'sales' => '/erp/reports?tab=sales',
            'operational' => '/erp/reports?tab=operational',
            'owner' => '/erp/reports?tab=owner',
            'incentive' => '/erp/reports?tab=incentive',
        ];

        $permissions = [
            'sales' => [1, 2, 5],
            'operational' => [1, 3, 5, 6],
            'owner' => [1, 5],
            'incentive' => [1, 5],
        ];

        // 1. Prioritize dynamic menu permissions from appmenuaccess (Menu Management)
        $tabMenus = AppMenu::whereIn('menu_page', array_values($tabMenuPages))->get()->keyBy('menu_page');
        if ($tabMenus->isNotEmpty()) {
            foreach ($tabMenuPages as $tabKey => $page) {
                $menu = $tabMenus->get($page);
                if ($menu) {
                    $rolesWithAccess = AppMenuAccess::where('menu_id', $menu->menu_id)->pluck('usergrpid')->map('intval')->toArray();
                    if (!empty($rolesWithAccess)) {
                        $permissions[$tabKey] = $rolesWithAccess;
                    }
                }
            }
        }

        // 2. Fallback to CompanyConfig if appmenuaccess has no specific entries
        $config = CompanyConfig::where('key', 'report_tab_permissions')->first();
        if ($config && !empty($config->value)) {
            $decoded = json_decode($config->value, true);
            if (is_array($decoded)) {
                foreach (['sales', 'operational', 'owner', 'incentive'] as $k) {
                    if (isset($decoded[$k]) && is_array($decoded[$k]) && empty($tabMenus->get($tabMenuPages[$k] ?? ''))) {
                        $permissions[$k] = array_map('intval', $decoded[$k]);
                    }
                }
            }
        }

        return $permissions;
    }

    /**
     * Get allowed tabs for current user
     */
    protected function getAllowedTabs($user, array $permissions): array
    {
        $userRoleId = (int) ($user->usergrpid ?? 1);
        $isAdmin = $user->isAdmin();

        $allReportTabs = ['sales', 'operational', 'owner', 'incentive'];

        if ($isAdmin) {
            $allowed = $allReportTabs;
            $allowed[] = 'access';
            return $allowed;
        }

        $allowed = [];
        foreach ($allReportTabs as $tabKey) {
            $allowedRoles = array_map('intval', $permissions[$tabKey] ?? []);
            if (in_array($userRoleId, $allowedRoles, true)) {
                $allowed[] = $tabKey;
            }
        }

        return $allowed;
    }

    /**
     * Main Report Hub
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }

        $permissions = $this->getTabPermissions();
        $allowedTabs = $this->getAllowedTabs($user, $permissions);

        if (empty($allowedTabs)) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk melihat laporan sistem.');
        }

        $tab = $request->query('tab', $allowedTabs[0]);
        if (!in_array($tab, $allowedTabs, true)) {
            $tab = $allowedTabs[0];
        }

        // Date range filter (defaults to current month)
        $startDate = $request->query('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', Carbon::now()->endOfMonth()->toDateString());

        // Ensure valid date strings
        try {
            $startCarbon = Carbon::parse($startDate)->startOfDay();
            $endCarbon = Carbon::parse($endDate)->endOfDay();
        } catch (\Exception $e) {
            $startCarbon = Carbon::now()->startOfMonth()->startOfDay();
            $endCarbon = Carbon::now()->endOfMonth()->endOfDay();
            $startDate = $startCarbon->toDateString();
            $endDate = $endCarbon->toDateString();
        }

        $reportData = [];

        // Tab: Sales
        if ($tab === 'sales') {
            $quotations = Order::where('order_type', 'quotation')
                ->whereBetween('order_date', [$startDate, $endDate])
                ->with(['contact', 'creator'])
                ->orderByDesc('order_date')
                ->get();

            $confirmedOrders = Order::whereIn('order_type', ['rental_order', 'sales_order'])
                ->whereBetween('order_date', [$startDate, $endDate])
                ->with(['contact', 'creator'])
                ->orderByDesc('order_date')
                ->get();

            $leads = Lead::whereBetween('created_at', [$startCarbon, $endCarbon])
                ->with('sales')
                ->get();

            $totalQuotationsCount = $quotations->count();
            $totalQuotationsAmount = (float) $quotations->sum('total_amount');

            $acceptedQuotations = $quotations->where('status', 'accepted');
            $acceptedCount = $acceptedQuotations->count();
            $acceptedAmount = (float) $acceptedQuotations->sum('total_amount');

            $confirmedRevenue = (float) $confirmedOrders->sum('total_amount');
            $closingRate = $totalQuotationsCount > 0 ? round(($acceptedCount / $totalQuotationsCount) * 100, 1) : 0;

            // Sales person performance matrix
            $salesRepGroup = [];
            foreach ($quotations as $q) {
                $repName = $q->creator?->name ?? 'Tim Sales';
                if (!isset($salesRepGroup[$repName])) {
                    $salesRepGroup[$repName] = [
                        'name' => $repName,
                        'quotations_count' => 0,
                        'quotations_total' => 0,
                        'won_count' => 0,
                        'won_total' => 0,
                    ];
                }
                $salesRepGroup[$repName]['quotations_count']++;
                $salesRepGroup[$repName]['quotations_total'] += (float) $q->total_amount;
                if ($q->status === 'accepted') {
                    $salesRepGroup[$repName]['won_count']++;
                    $salesRepGroup[$repName]['won_total'] += (float) $q->total_amount;
                }
            }

            $reportData = [
                'quotations' => $quotations,
                'confirmed_orders' => $confirmedOrders,
                'leads' => $leads,
                'total_quotations_count' => $totalQuotationsCount,
                'total_quotations_amount' => $totalQuotationsAmount,
                'accepted_count' => $acceptedCount,
                'accepted_amount' => $acceptedAmount,
                'confirmed_revenue' => $confirmedRevenue,
                'closing_rate' => $closingRate,
                'sales_reps' => array_values($salesRepGroup),
            ];
        }

        // Tab: Operational
        if ($tab === 'operational') {
            $assets = Asset::with('item')->orderBy('asset_code')->get();
            $deliveries = Delivery::whereBetween('delivery_date', [$startDate, $endDate])
                ->with(['order.contact', 'items.asset'])
                ->orderByDesc('delivery_date')
                ->get();

            $services = AssetService::whereBetween('service_date', [$startDate, $endDate])
                ->with('asset')
                ->orderByDesc('service_date')
                ->get();

            $totalUnits = $assets->count();
            $rentedUnits = $assets->where('status', 'rented')->count();
            $availableUnits = $assets->whereIn('status', ['available', 'standby'])->count();
            $maintenanceUnits = $assets->whereIn('status', ['maintenance', 'service'])->count();
            $utilizationRate = $totalUnits > 0 ? round(($rentedUnits / $totalUnits) * 100, 1) : 0;
            $totalMaintenanceCost = (float) $services->sum('cost');

            $reportData = [
                'assets' => $assets,
                'deliveries' => $deliveries,
                'services' => $services,
                'total_units' => $totalUnits,
                'rented_units' => $rentedUnits,
                'available_units' => $availableUnits,
                'maintenance_units' => $maintenanceUnits,
                'utilization_rate' => $utilizationRate,
                'total_maintenance_cost' => $totalMaintenanceCost,
            ];
        }

        // Tab: Owner
        if ($tab === 'owner') {
            $orders = Order::whereIn('order_type', ['rental_order', 'sales_order'])
                ->whereBetween('order_date', [$startDate, $endDate])
                ->with('contact')
                ->orderByDesc('order_date')
                ->get();

            $totalRevenue = (float) $orders->sum('total_amount');
            $totalVendorCost = (float) $orders->sum('vendor_cost');
            $totalLogisticsCost = (float) $orders->sum(function ($o) {
                return (float) ($o->logistics_cost ?: $o->shipping_fee);
            });
            $grossProfit = $totalRevenue - $totalVendorCost - $totalLogisticsCost;
            $grossMarginPercent = $totalRevenue > 0 ? round(($grossProfit / $totalRevenue) * 100, 1) : 0;

            // Cash In & Cash Out
            $payments = Payment::whereBetween('payment_date', [$startDate, $endDate])
                ->with('invoice')
                ->get();

            $cashIn = (float) $payments->where('invoice.invoice_type', 'customer_invoice')->sum('amount');
            $cashOut = (float) $payments->where('invoice.invoice_type', 'vendor_bill')->sum('amount');

            // Receivables AR Aging
            $invoices = Invoice::where('invoice_type', 'customer_invoice')
                ->where('status', '!=', 'paid')
                ->where('status', '!=', 'cancelled')
                ->get();

            $arAgingCurrent = 0;
            $arAging30 = 0;
            $arAging60 = 0;
            $arAgingOver60 = 0;

            $today = Carbon::today();
            foreach ($invoices as $inv) {
                $balance = (float) $inv->outstanding_balance;
                if ($balance <= 0) {
                    continue;
                }
                $dueDate = $inv->due_date ? Carbon::parse($inv->due_date) : Carbon::parse($inv->invoice_date);
                if ($dueDate->gte($today)) {
                    $arAgingCurrent += $balance;
                } else {
                    $daysOverdue = $dueDate->diffInDays($today);
                    if ($daysOverdue <= 30) {
                        $arAging30 += $balance;
                    } elseif ($daysOverdue <= 60) {
                        $arAging60 += $balance;
                    } else {
                        $arAgingOver60 += $balance;
                    }
                }
            }

            // Vendor Payables AP
            $vendorBills = Invoice::where('invoice_type', 'vendor_bill')
                ->where('status', '!=', 'paid')
                ->where('status', '!=', 'cancelled')
                ->get();
            $totalApOutstanding = (float) $vendorBills->sum('outstanding_balance');

            $reportData = [
                'orders' => $orders,
                'total_revenue' => $totalRevenue,
                'total_vendor_cost' => $totalVendorCost,
                'total_logistics_cost' => $totalLogisticsCost,
                'gross_profit' => $grossProfit,
                'gross_margin_percent' => $grossMarginPercent,
                'cash_in' => $cashIn,
                'cash_out' => $cashOut,
                'ar_aging_current' => $arAgingCurrent,
                'ar_aging_30' => $arAging30,
                'ar_aging_60' => $arAging60,
                'ar_aging_over60' => $arAgingOver60,
                'total_ap_outstanding' => $totalApOutstanding,
            ];
        }

        // Tab: Sales Incentive (Owner & Admin audit report)
        if ($tab === 'incentive') {
            $filterSalesId = $request->query('sales_id');

            $incentiveOrdersQuery = Order::whereIn('order_type', ['rental_order', 'sales_order', 'quotation'])
                ->whereBetween('order_date', [$startDate, $endDate])
                ->where(function ($q) {
                    $q->whereIn('order_type', ['rental_order', 'sales_order'])
                      ->orWhere(function ($sub) {
                          $sub->where('order_type', 'quotation')->where('status', 'accepted');
                      });
                })
                ->with(['contact.sales', 'contact.lead', 'creator', 'items', 'invoices.payments']);

            if (!empty($filterSalesId)) {
                $incentiveOrdersQuery->where(function($q) use ($filterSalesId) {
                    $q->where('created_by', $filterSalesId)
                      ->orWhereHas('contact', function($cq) use ($filterSalesId) {
                          $cq->where('sales_id', $filterSalesId);
                      });
                });
            }

            $incentiveOrders = $incentiveOrdersQuery->orderByDesc('order_date')->get();

            $totalIncentiveRevenue = 0;
            $totalIncentiveNominal = 0;
            $totalPaidIncentive = 0;
            $totalPendingIncentive = 0;
            $salesRepSummary = [];
            $breakdownItems = [];

            foreach ($incentiveOrders as $ord) {
                // Attribution Rule: If customer has active sales PIC, incentive permanently belongs to them
                $salesUser = ($ord->contact && $ord->contact->sales && $ord->contact->sales->is_active)
                    ? $ord->contact->sales
                    : ($ord->creator ?? User::find($ord->created_by));

                $salesName = $salesUser?->name ?? 'Sales Belum Ditentukan';
                $salesId = $salesUser?->id ?? 0;
                $isSalesActive = $salesUser?->is_active ?? true;

                $ratePercent = ($ord->order_type === 'sales_order' || ($ord->order_type === 'quotation' && $ord->quotation_type === 'sales')) ? 1.5 : 2.0;
                $revenue = (float) $ord->total_amount;
                $incentiveNominal = round(($revenue * $ratePercent) / 100, 2);

                $isPaid = ($ord->invoice_status === 'paid') || ($ord->invoices->where('status', 'paid')->count() > 0);

                $totalIncentiveRevenue += $revenue;
                $totalIncentiveNominal += $incentiveNominal;
                if ($isPaid) {
                    $totalPaidIncentive += $incentiveNominal;
                } else {
                    $totalPendingIncentive += $incentiveNominal;
                }

                if (!isset($salesRepSummary[$salesName])) {
                    $salesRepSummary[$salesName] = [
                        'sales_id' => $salesId,
                        'name' => $salesName,
                        'is_active' => $isSalesActive,
                        'deals_count' => 0,
                        'total_revenue' => 0,
                        'total_incentive' => 0,
                        'paid_incentive' => 0,
                        'pending_incentive' => 0,
                    ];
                }
                $salesRepSummary[$salesName]['deals_count']++;
                $salesRepSummary[$salesName]['total_revenue'] += $revenue;
                $salesRepSummary[$salesName]['total_incentive'] += $incentiveNominal;
                if ($isPaid) {
                    $salesRepSummary[$salesName]['paid_incentive'] += $incentiveNominal;
                } else {
                    $salesRepSummary[$salesName]['pending_incentive'] += $incentiveNominal;
                }

                $isPICAttributed = ($ord->contact && $ord->contact->sales_id && $ord->contact->sales_id == $salesId);
                $createdByAdmin = ($ord->creator && $ord->creator->isAdmin() && $ord->creator->id != $salesId);

                $breakdownItems[] = [
                    'order' => $ord,
                    'sales_name' => $salesName,
                    'sales_is_active' => $isSalesActive,
                    'customer_name' => $ord->contact?->name ?? 'Pelanggan Umum',
                    'lead_code' => $ord->contact?->lead_code ?? ($ord->contact?->lead?->code ?? '-'),
                    'revenue' => $revenue,
                    'rate_percent' => $ratePercent,
                    'incentive_nominal' => $incentiveNominal,
                    'is_paid' => $isPaid,
                    'is_pic_attributed' => $isPICAttributed,
                    'created_by_admin' => $createdByAdmin,
                    'created_by_name' => $ord->creator?->name ?? 'Admin',
                ];
            }

            uasort($salesRepSummary, fn($a, $b) => $b['total_incentive'] <=> $a['total_incentive']);
            $topSalesRep = !empty($salesRepSummary) ? reset($salesRepSummary)['name'] : '-';

            $allSalesUsers = User::where('is_active', true)
                ->where(function($q) {
                    $q->whereIn('role', ['sales', 'marketing', 'admin'])->orWhere('id', 1);
                })
                ->orderBy('name')
                ->get(['id', 'name']);

            $reportData = [
                'breakdown' => $breakdownItems,
                'sales_reps' => array_values($salesRepSummary),
                'total_revenue' => $totalIncentiveRevenue,
                'total_incentive' => $totalIncentiveNominal,
                'total_paid_incentive' => $totalPaidIncentive,
                'total_pending_incentive' => $totalPendingIncentive,
                'total_deals' => count($breakdownItems),
                'top_sales_rep' => $topSalesRep,
                'all_sales_users' => $allSalesUsers,
                'selected_sales_id' => $filterSalesId,
            ];
        }

        // Tab: Access Configuration (Admin only)
        $roles = [];
        if ($tab === 'access' && $user->isAdmin()) {
            $roles = Role::orderBy('id')->get();
        }

        return view('erp.reports', compact(
            'tab',
            'allowedTabs',
            'permissions',
            'startDate',
            'endDate',
            'reportData',
            'roles'
        ));
    }

    /**
     * Download CSV / Excel formatted Report
     */
    public function export(Request $request): StreamedResponse
    {
        $user = auth()->user();
        if (!$user) {
            abort(401);
        }

        $permissions = $this->getTabPermissions();
        $allowedTabs = $this->getAllowedTabs($user, $permissions);

        $tab = $request->query('tab', 'sales');
        if (!in_array($tab, $allowedTabs, true)) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk mengunduh laporan ini.');
        }

        $startDate = $request->query('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', Carbon::now()->endOfMonth()->toDateString());

        $tabName = ($tab === 'incentive') ? 'Insentif_Sales' : $tab;
        $filename = "Laporan_{$tabName}_Keylock_{$startDate}_{$endDate}.csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($tab, $startDate, $endDate) {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM so Excel opens IDR / Chinese / special chars without mojibake
            fputs($handle, "\xEF\xBB\xBF");

            if ($tab === 'sales') {
                fputcsv($handle, ['LAPORAN PENJUALAN & CRM - PT KEY LOCK INDONESIA']);
                fputcsv($handle, ['Periode:', $startDate . ' s/d ' . $endDate]);
                fputcsv($handle, ['Dicetak Oleh:', auth()->user()->name ?? 'System', 'Tanggal:', now()->toDateTimeString()]);
                fputcsv($handle, []);

                // Header
                fputcsv($handle, [
                    'No',
                    'Tipe Dokumen',
                    'Nomor Dokumen',
                    'Tanggal',
                    'Nama Pelanggan',
                    'Sales Person',
                    'Masa Sewa',
                    'Subtotal (IDR)',
                    'Pajak (IDR)',
                    'Total Nilai (IDR)',
                    'Status',
                ]);

                $quotations = Order::where('order_type', 'quotation')
                    ->whereBetween('order_date', [$startDate, $endDate])
                    ->with(['contact', 'creator'])
                    ->orderByDesc('order_date')
                    ->get();

                $i = 1;
                $sumTotal = 0;
                foreach ($quotations as $q) {
                    $total = (float) $q->total_amount;
                    $sumTotal += $total;
                    fputcsv($handle, [
                        $i++,
                        'Penawaran Harga',
                        $q->order_number,
                        $q->order_date?->format('d/m/Y') ?? '-',
                        $q->contact?->name ?? 'Umum',
                        $q->creator?->name ?? '-',
                        ($q->rental_duration ?? 1) . ' ' . ($q->rental_duration_unit ?? 'Bulan'),
                        number_format((float) $q->subtotal, 2, ',', '.'),
                        number_format((float) $q->tax_amount, 2, ',', '.'),
                        number_format($total, 2, ',', '.'),
                        strtoupper($q->status),
                    ]);
                }

                fputcsv($handle, []);
                fputcsv($handle, ['', '', '', '', '', '', 'TOTAL PENAWARAN (IDR)', '', '', number_format($sumTotal, 2, ',', '.')]);
            } elseif ($tab === 'operational') {
                fputcsv($handle, ['LAPORAN OPERASIONAL & UTILISASI ARMADA - PT KEY LOCK INDONESIA']);
                fputcsv($handle, ['Periode:', $startDate . ' s/d ' . $endDate]);
                fputcsv($handle, ['Dicetak Oleh:', auth()->user()->name ?? 'System', 'Tanggal:', now()->toDateTimeString()]);
                fputcsv($handle, []);

                // 1. Fleet Assets Status
                fputcsv($handle, ['--- STATUS KETERSEDIAAN ARMADA UNIT ---']);
                fputcsv($handle, ['No', 'Kode Unit', 'Nama & Model', 'Kategori', 'No Serial', 'Hour Meter (HM)', 'Status', 'Lokasi Saat Ini']);

                $assets = Asset::with('item')->orderBy('asset_code')->get();
                $idx = 1;
                foreach ($assets as $a) {
                    fputcsv($handle, [
                        $idx++,
                        $a->asset_code,
                        $a->item?->name ?? 'Unit Forklift',
                        $a->item?->category ?? '-',
                        $a->serial_number ?? '-',
                        $a->hour_meter ?? 0,
                        strtoupper($a->status),
                        $a->current_location ?? 'Pool Karawang',
                    ]);
                }

                fputcsv($handle, []);
                // 2. Delivery Orders
                fputcsv($handle, ['--- SURAT JALAN & PENGIRIMAN UNIT (DO) ---']);
                fputcsv($handle, ['No', 'Nomor DO', 'Tanggal Kirim', 'Pelanggan', 'No Order', 'Sopir / Driver', 'No Polisi Armada', 'Lokasi Proyek', 'Status']);

                $deliveries = Delivery::whereBetween('delivery_date', [$startDate, $endDate])
                    ->with(['order.contact'])
                    ->orderByDesc('delivery_date')
                    ->get();

                $dIdx = 1;
                foreach ($deliveries as $d) {
                    fputcsv($handle, [
                        $dIdx++,
                        $d->delivery_number,
                        $d->delivery_date?->format('d/m/Y') ?? '-',
                        $d->order?->contact?->name ?? '-',
                        $d->order?->order_number ?? '-',
                        $d->driver_name ?? '-',
                        $d->vehicle_number ?? '-',
                        $d->destination_address ?? ($d->order?->shipping_address ?? '-'),
                        strtoupper($d->status),
                    ]);
                }
            } elseif ($tab === 'owner') {
                fputcsv($handle, ['LAPORAN FINANSIAL & PROFITABILITAS OWNER - PT KEY LOCK INDONESIA']);
                fputcsv($handle, ['Periode:', $startDate . ' s/d ' . $endDate]);
                fputcsv($handle, ['Dicetak Oleh:', auth()->user()->name ?? 'System', 'Tanggal:', now()->toDateTimeString()]);
                fputcsv($handle, []);

                fputcsv($handle, [
                    'No',
                    'Nomor Order Rental',
                    'Tanggal Order',
                    'Nama Pelanggan',
                    'Omset Rental (IDR)',
                    'HPP Vendor Subkon (IDR)',
                    'Biaya Logistik Kirim (IDR)',
                    'Gross Profit (IDR)',
                    'Gross Margin (%)',
                    'Status Pembayaran',
                ]);

                $orders = Order::whereIn('order_type', ['rental_order', 'sales_order'])
                    ->whereBetween('order_date', [$startDate, $endDate])
                    ->with('contact')
                    ->orderByDesc('order_date')
                    ->get();

                $oIdx = 1;
                $sumRevenue = 0;
                $sumVendor = 0;
                $sumLogistics = 0;
                $sumProfit = 0;

                foreach ($orders as $o) {
                    $rev = (float) $o->total_amount;
                    $vendor = (float) $o->vendor_cost;
                    $logistics = (float) ($o->logistics_cost ?: $o->shipping_fee);
                    $profit = $rev - $vendor - $logistics;
                    $margin = $rev > 0 ? round(($profit / $rev) * 100, 1) : 0;

                    $sumRevenue += $rev;
                    $sumVendor += $vendor;
                    $sumLogistics += $logistics;
                    $sumProfit += $profit;

                    fputcsv($handle, [
                        $oIdx++,
                        $o->order_number,
                        $o->order_date?->format('d/m/Y') ?? '-',
                        $o->contact?->name ?? 'Umum',
                        number_format($rev, 2, ',', '.'),
                        number_format($vendor, 2, ',', '.'),
                        number_format($logistics, 2, ',', '.'),
                        number_format($profit, 2, ',', '.'),
                        $margin . '%',
                        strtoupper($o->invoice_status ?? 'UNINVOICED'),
                    ]);
                }

                $totalMarginPercent = $sumRevenue > 0 ? round(($sumProfit / $sumRevenue) * 100, 1) : 0;
                fputcsv($handle, []);
                fputcsv($handle, [
                    '',
                    '',
                    '',
                    'TOTAL RINGKASAN',
                    number_format($sumRevenue, 2, ',', '.'),
                    number_format($sumVendor, 2, ',', '.'),
                    number_format($sumLogistics, 2, ',', '.'),
                    number_format($sumProfit, 2, ',', '.'),
                    $totalMarginPercent . '%',
                    '',
                ]);
            }

            // Tab: Incentive
            if ($tab === 'incentive') {
                fputcsv($handle, ['LAPORAN INSENTIF & KOMISI SALES - PT KEY LOCK INDONESIA']);
                fputcsv($handle, ['Periode:', $startDate . ' s/d ' . $endDate]);
                fputcsv($handle, ['Dicetak Oleh:', auth()->user()->name ?? 'System', 'Tanggal:', now()->toDateTimeString()]);
                fputcsv($handle, []);
                fputcsv($handle, ['No', 'No Transaksi', 'Tanggal', 'Nama Pelanggan', 'No Leads CRM', 'Sales PIC (Penerima Hak)', 'Dibuat Oleh', 'Total Nilai (IDR)', 'Rate Komisi (%)', 'Nominal Insentif (IDR)', 'Status Pembayaran']);

                $orders = Order::whereIn('order_type', ['rental_order', 'sales_order', 'quotation'])
                    ->whereBetween('order_date', [$startDate, $endDate])
                    ->where(function ($q) {
                        $q->whereIn('order_type', ['rental_order', 'sales_order'])
                          ->orWhere(function ($sub) {
                              $sub->where('order_type', 'quotation')->where('status', 'accepted');
                          });
                    })
                    ->with(['contact.sales', 'creator', 'invoices'])
                    ->orderByDesc('order_date')
                    ->get();

                $no = 1;
                $grandRevenue = 0;
                $grandIncentive = 0;

                foreach ($orders as $ord) {
                    $salesUser = ($ord->contact && $ord->contact->sales && $ord->contact->sales->is_active)
                        ? $ord->contact->sales
                        : ($ord->creator ?? User::find($ord->created_by));

                    $ratePercent = ($ord->order_type === 'sales_order' || ($ord->order_type === 'quotation' && $ord->quotation_type === 'sales')) ? 1.5 : 2.0;
                    $revenue = (float) $ord->total_amount;
                    $incentive = round(($revenue * $ratePercent) / 100, 2);
                    $isPaid = ($ord->invoice_status === 'paid') || ($ord->invoices->where('status', 'paid')->count() > 0);

                    $grandRevenue += $revenue;
                    $grandIncentive += $incentive;

                    fputcsv($handle, [
                        $no++,
                        $ord->order_number,
                        $ord->order_date?->format('d/m/Y') ?? '-',
                        $ord->contact?->name ?? 'Pelanggan Umum',
                        $ord->contact?->lead_code ?? ($ord->contact?->lead?->code ?? '-'),
                        $salesUser?->name ?? '-',
                        $ord->creator?->name ?? 'Admin',
                        number_format($revenue, 2, ',', '.'),
                        $ratePercent . '%',
                        number_format($incentive, 2, ',', '.'),
                        $isPaid ? 'Lunas (Paid)' : 'Belum Lunas (Pending)'
                    ]);
                }

                fputcsv($handle, []);
                fputcsv($handle, ['', '', '', '', '', '', 'TOTAL INSENTIF', number_format($grandRevenue, 2, ',', '.'), '', number_format($grandIncentive, 2, ',', '.'), '']);
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Printable Document View (for PDF / Browser Print)
     */
    public function print(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            abort(401);
        }

        $permissions = $this->getTabPermissions();
        $allowedTabs = $this->getAllowedTabs($user, $permissions);

        $tab = $request->query('tab', 'sales');
        if (!in_array($tab, $allowedTabs, true)) {
            abort(403);
        }

        $startDate = $request->query('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', Carbon::now()->endOfMonth()->toDateString());

        // Gather same data as index
        $company = [
            'name' => CompanyConfig::where('key', 'company_name')->value('value') ?? 'PT KEY LOCK INDONESIA',
            'address' => CompanyConfig::where('key', 'company_address')->value('value') ?? 'Karawang, Jawa Barat',
            'phone' => CompanyConfig::where('key', 'company_phone')->value('value') ?? '+62 21 8990 1234',
            'email' => CompanyConfig::where('key', 'company_email')->value('value') ?? 'keylockindo@outlook.com',
        ];

        $data = [];
        if ($tab === 'sales') {
            $data['quotations'] = Order::where('order_type', 'quotation')
                ->whereBetween('order_date', [$startDate, $endDate])
                ->with(['contact', 'creator'])
                ->orderByDesc('order_date')
                ->get();
            $data['total_amount'] = (float) $data['quotations']->sum('total_amount');
            $data['accepted_count'] = $data['quotations']->where('status', 'accepted')->count();
        } elseif ($tab === 'operational') {
            $data['assets'] = Asset::with('item')->orderBy('asset_code')->get();
            $data['deliveries'] = Delivery::whereBetween('delivery_date', [$startDate, $endDate])
                ->with(['order.contact'])
                ->orderByDesc('delivery_date')
                ->get();
        } elseif ($tab === 'owner') {
            $data['orders'] = Order::whereIn('order_type', ['rental_order', 'sales_order'])
                ->whereBetween('order_date', [$startDate, $endDate])
                ->with('contact')
                ->orderByDesc('order_date')
                ->get();
            $data['total_revenue'] = (float) $data['orders']->sum('total_amount');
            $data['total_vendor_cost'] = (float) $data['orders']->sum('vendor_cost');
            $data['total_logistics'] = (float) $data['orders']->sum(function ($o) {
                return (float) ($o->logistics_cost ?: $o->shipping_fee);
            });
            $data['gross_profit'] = $data['total_revenue'] - $data['total_vendor_cost'] - $data['total_logistics'];
        } elseif ($tab === 'incentive') {
            $filterSalesId = $request->query('sales_id');
            $incentiveOrdersQuery = Order::whereIn('order_type', ['rental_order', 'sales_order', 'quotation'])
                ->whereBetween('order_date', [$startDate, $endDate])
                ->where(function ($q) {
                    $q->whereIn('order_type', ['rental_order', 'sales_order'])
                      ->orWhere(function ($sub) {
                          $sub->where('order_type', 'quotation')->where('status', 'accepted');
                      });
                })
                ->with(['contact.sales', 'contact.lead', 'creator', 'invoices']);

            if (!empty($filterSalesId)) {
                $incentiveOrdersQuery->where(function($q) use ($filterSalesId) {
                    $q->where('created_by', $filterSalesId)
                      ->orWhereHas('contact', function($cq) use ($filterSalesId) {
                          $cq->where('sales_id', $filterSalesId);
                      });
                });
            }

            $orders = $incentiveOrdersQuery->orderByDesc('order_date')->get();

            $data['orders'] = $orders;
            $data['total_revenue'] = (float) $orders->sum('total_amount');
            $data['total_incentive'] = (float) $orders->sum(function($o) {
                $rate = ($o->order_type === 'sales_order' || ($o->order_type === 'quotation' && $o->quotation_type === 'sales')) ? 1.5 : 2.0;
                return round(($o->total_amount * $rate) / 100, 2);
            });
            $data['paid_incentive'] = 0;
            $data['pending_incentive'] = 0;
            foreach ($orders as $o) {
                $rate = ($o->order_type === 'sales_order' || ($o->order_type === 'quotation' && $o->quotation_type === 'sales')) ? 1.5 : 2.0;
                $inc = round(($o->total_amount * $rate) / 100, 2);
                $isPaid = ($o->invoice_status === 'paid') || ($o->invoices->where('status', 'paid')->count() > 0);
                if ($isPaid) {
                    $data['paid_incentive'] += $inc;
                } else {
                    $data['pending_incentive'] += $inc;
                }
            }
        }

        return view('erp.report-print', compact('tab', 'startDate', 'endDate', 'company', 'data'));
    }

    /**
     * Synchronize tab permissions to AppMenuAccess
     */
    protected function syncReportMenuAccess(array $permissions): void
    {
        $tabMenuPages = [
            'sales' => '/erp/reports?tab=sales',
            'operational' => '/erp/reports?tab=operational',
            'owner' => '/erp/reports?tab=owner',
            'incentive' => '/erp/reports?tab=incentive',
        ];

        $tabMenus = AppMenu::whereIn('menu_page', array_values($tabMenuPages))->get()->keyBy('menu_page');
        $rootMenu = AppMenu::where('menu_name', 'TAB LAPORAN & REKAPITULASI')->first();

        foreach ($tabMenuPages as $tabKey => $page) {
            $menu = $tabMenus->get($page);
            if (!$menu) continue;

            $targetRoles = $permissions[$tabKey] ?? [1];
            AppMenuAccess::where('menu_id', $menu->menu_id)
                ->whereNotIn('usergrpid', $targetRoles)
                ->delete();

            foreach ($targetRoles as $rId) {
                AppMenuAccess::firstOrCreate([
                    'usergrpid' => $rId,
                    'menu_id' => $menu->menu_id,
                ], ['createdtime' => now()]);

                if ($rootMenu) {
                    AppMenuAccess::firstOrCreate([
                        'usergrpid' => $rId,
                        'menu_id' => $rootMenu->menu_id,
                    ], ['createdtime' => now()]);
                }
            }
        }
    }

    /**
     * Update Tab Access Configuration (Admin only)
     */
    public function updateAccessConfig(Request $request)
    {
        $user = auth()->user();
        abort_if(!$user?->isAdmin(), 403, 'Akses Ditolak: Hanya Administrator yang dapat mengubah konfigurasi hak akses laporan.');

        $validated = $request->validate([
            'sales_roles' => 'nullable|array',
            'sales_roles.*' => 'integer',
            'operational_roles' => 'nullable|array',
            'operational_roles.*' => 'integer',
            'owner_roles' => 'nullable|array',
            'owner_roles.*' => 'integer',
            'incentive_roles' => 'nullable|array',
            'incentive_roles.*' => 'integer',
        ]);

        $newPermissions = [
            'sales' => array_map('intval', $validated['sales_roles'] ?? [1]),
            'operational' => array_map('intval', $validated['operational_roles'] ?? [1]),
            'owner' => array_map('intval', $validated['owner_roles'] ?? [1]),
            'incentive' => array_map('intval', $validated['incentive_roles'] ?? [1, 5]),
        ];

        // Ensure Admin (role ID 1) always retains access
        foreach (['sales', 'operational', 'owner', 'incentive'] as $k) {
            if (!in_array(1, $newPermissions[$k], true)) {
                $newPermissions[$k][] = 1;
            }
        }

        CompanyConfig::updateOrCreate(
            ['key' => 'report_tab_permissions'],
            [
                'value' => json_encode($newPermissions),
                'group' => 'system',
                'description' => 'Hak akses per role untuk tab laporan (sales, operational, owner, incentive)',
            ]
        );

        $this->syncReportMenuAccess($newPermissions);

        return redirect()->route('erp.reports.index', ['tab' => 'access'])
            ->with('success', 'Konfigurasi hak akses tab laporan berhasil diperbarui dan disinkronkan ke Menu Management!');
    }
}
