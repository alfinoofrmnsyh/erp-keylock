<?php

namespace App\Http\Controllers\Erp;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contact;
use App\Models\Item;
use App\Models\Asset;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Invoice;
use App\Models\CompanyConfig;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProcurementController extends Controller
{
    /**
     * Display Procurement & Vendor Hub
     */
    public function index(Request $request)
    {
        $typeFilter = $request->query('type', 'all');
        $statusFilter = $request->query('status', 'all');
        $search = $request->query('search');

        $query = Order::purchaseOrders()
            ->with(['contact', 'items.item', 'parent.contact', 'invoices.payments']);

        $operationalTypes = ['operational', 'office_supplies', 'marketing', 'it_software', 'internal_fleet', 'internal_service', 'other'];
        $serviceTypes = ['service', 'logistics', 'sparepart'];

        if ($typeFilter !== 'all') {
            if ($typeFilter === 'operational') {
                $query->whereIn('procurement_type', $operationalTypes);
            } elseif ($typeFilter === 'service' || $typeFilter === 'sparepart') {
                $query->whereIn('procurement_type', $serviceTypes);
            } elseif ($typeFilter === 'purchase_unit') {
                $query->where('procurement_type', 'purchase_unit');
            } elseif ($typeFilter === 'sub_rental') {
                $query->where('procurement_type', 'sub_rental');
            } else {
                $query->where('procurement_type', $typeFilter);
            }
        }

        if ($statusFilter !== 'all' && !empty($statusFilter)) {
            $query->where('status', $statusFilter);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('contact', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%")
                         ->orWhere('pic_name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('parent', function ($pq) use ($search) {
                      $pq->where('order_number', 'like', "%{$search}%")
                         ->orWhereHas('contact', function ($pcq) use ($search) {
                             $pcq->where('name', 'like', "%{$search}%");
                         });
                  })
                  ->orWhereHas('items', function ($iq) use ($search) {
                      $iq->where('description', 'like', "%{$search}%")
                         ->orWhere('specification', 'like', "%{$search}%");
                  });
            });
        }

        // 1. KPI Metrics & Category Counts
        $totalPoCount = Order::purchaseOrders()->count();
        $totalPoAmount = Order::purchaseOrders()->whereIn('status', ['approved', 'in_progress', 'completed'])->sum('total_amount');
        $subRentalCount = Order::purchaseOrders()->where('procurement_type', 'sub_rental')->count();
        $activeSubRentCount = Order::purchaseOrders()->where('procurement_type', 'sub_rental')->whereIn('status', ['approved', 'in_progress'])->count();
        $purchaseCount = Order::purchaseOrders()->where('procurement_type', 'purchase_unit')->count();
        $serviceCount = Order::purchaseOrders()->whereIn('procurement_type', $serviceTypes)->count();
        $operationalCount = Order::purchaseOrders()->whereIn('procurement_type', $operationalTypes)->count();
        
        // Vendor bills pending payment
        $pendingBillsCount = Invoice::vendorBills()->whereIn('status', ['draft', 'sent', 'partial', 'overdue'])->count();
        $pendingBillsAmount = Invoice::vendorBills()->whereIn('status', ['draft', 'sent', 'partial', 'overdue'])->sum('balance_due');

        $perPage = (int) $request->query('per_page', 15);
        if (!in_array($perPage, [10, 15, 25, 50, 100])) {
            $perPage = 15;
        }

        $purchaseOrders = $query->latest('order_date')->latest('id')->paginate($perPage)->withQueryString();
        
        // Exclude vendor pengiriman/ekspedisi/logistik from Unit/Service/PO Vendor List
        $allVendors = Contact::where('type', 'vendor')->orderBy('name')->get();
        $vendors = $allVendors->filter(function($v) {
            $cat = strtolower($v->category ?? '');
            $name = strtolower($v->name ?? '');
            return !str_contains($cat, 'pengiriman') && 
                   !str_contains($cat, 'ekspedisi') && 
                   !str_contains($cat, 'logistik') &&
                   !str_contains($name, 'ekspedisi') &&
                   !str_contains($name, 'lalamove');
        })->values();
        
        // Customer orders available to link (active SO & RO)
        $activeCustomerOrders = Order::whereIn('order_type', ['sales_order', 'rental_order'])
            ->with(['contact', 'vendorOrders'])
            ->latest('id')
            ->get();

        $catalogItems = Item::where('is_active', true)->orderBy('name')->get();

        return view('erp.procurement', compact(
            'purchaseOrders',
            'typeFilter',
            'statusFilter',
            'search',
            'totalPoCount',
            'totalPoAmount',
            'subRentalCount',
            'activeSubRentCount',
            'purchaseCount',
            'serviceCount',
            'operationalCount',
            'pendingBillsCount',
            'pendingBillsAmount',
            'vendors',
            'activeCustomerOrders',
            'catalogItems'
        ));
    }

    /**
     * Official Purchase Order (PO) PDF / Print View
     */
    public function poPdf($id)
    {
        $user = auth()->user();
        if ($user && !$user->isAdmin() && !in_array($user->role, ['procurement', 'operasional', 'finance', 'admin_op'])) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk melihat dokumen PO ini.');
        }

        $po = Order::with(['contact', 'items.item', 'parent.contact', 'creator'])->findOrFail($id);

        $company = [
            'name' => CompanyConfig::get('company_name', 'PT KEY LOCK INDONESIA'),
            'address' => CompanyConfig::get('company_address', 'Jalan Arteri KIIC Rolling Hills Ruko Pacific Plaza No. 91, Desa Margakaya, Kecamatan Telukjambe Barat, Kabupaten Karawang 41361.'),
            'npwp' => CompanyConfig::get('company_npwp', '1000 0000 0573 6973'),
            'phone' => CompanyConfig::get('company_phone', '0811 2345 6789'),
            'email' => CompanyConfig::get('company_email', 'keylockindo@outlook.com'),
            'director' => CompanyConfig::get('director_name', 'QI SHUJUN 齐书骏'),
            'director_title' => CompanyConfig::get('director_title', 'Presiden Direktur 董事长'),
        ];

        return view('erp.po-pdf', compact('po', 'company'));
    }

    /**
     * Store a new Purchase Order / Sub-Rental
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        if ($user && !$user->isAdmin() && !in_array($user->role, ['procurement', 'operasional', 'admin_op'])) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki wewenang untuk membuat Order Pengadaan.');
        }

        $validated = $request->validate([
            'contact_id' => 'required|exists:contacts,id',
            'procurement_type' => 'required|in:sub_rental,purchase_unit,service,operational,sparepart,logistics,office_supplies,marketing,it_software,internal_fleet,internal_service,other',
            'parent_id' => 'nullable|exists:orders,id',
            'order_date' => 'required|date',
            'rental_start_date' => 'nullable|date',
            'rental_end_date' => 'nullable|date',
            'rental_duration' => 'nullable|integer|min:1',
            'rental_duration_unit' => 'nullable|string',
            'shipping_fee' => 'nullable|numeric|min:0',
            'include_ppn' => 'nullable|boolean',
            'payment_term' => 'nullable|string|max:150',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'nullable|integer',
            'items.*.name' => 'required|string|max:255',
            'items.*.specification' => 'nullable|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit' => 'nullable|string|max:30',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.duration' => 'nullable|integer|min:1',
            'items.*.duration_unit' => 'nullable|string',
            'items.*.discount' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $isSubRent = ($validated['procurement_type'] === 'sub_rental');
            $prefix = $isSubRent ? 'SR' : 'PO';
            $orderNumber = $this->generateOrderNumber($prefix);

            $subtotal = 0;
            $totalDiscount = 0;

            foreach ($validated['items'] as $itemData) {
                $duration = $isSubRent ? max(1, $itemData['duration'] ?? 1) : 1;
                $lineSubtotal = ($itemData['quantity'] * $itemData['unit_price'] * $duration);
                $discount = $itemData['discount'] ?? 0;
                $subtotal += ($lineSubtotal - $discount);
                $totalDiscount += $discount;
            }

            $includePpn = filter_var($request->input('include_ppn', false), FILTER_VALIDATE_BOOLEAN);
            $taxPercent = $includePpn ? 11.00 : 0.00;
            $taxAmount = $includePpn ? round($subtotal * ($taxPercent / 100), 2) : 0.00;
            $shippingFee = $validated['shipping_fee'] ?? 0;
            $totalAmount = $subtotal + $taxAmount + $shippingFee;

            $unitMap = ['bulan' => 'month', 'month' => 'month', 'hari' => 'day', 'day' => 'day', 'minggu' => 'week', 'week' => 'week', 'tahun' => 'year', 'year' => 'year'];
            $durationUnit = $unitMap[strtolower($validated['rental_duration_unit'] ?? '')] ?? 'month';

            $po = Order::create([
                'order_number' => $orderNumber,
                'order_type' => 'purchase_order',
                'procurement_type' => $validated['procurement_type'],
                'contact_id' => $validated['contact_id'],
                'parent_id' => $validated['parent_id'] ?? null,
                'order_date' => $validated['order_date'],
                'rental_start_date' => $validated['rental_start_date'] ?? null,
                'rental_end_date' => $validated['rental_end_date'] ?? null,
                'rental_duration' => $isSubRent ? ($validated['rental_duration'] ?? 1) : null,
                'rental_duration_unit' => $isSubRent ? $durationUnit : 'month',
                'status' => 'approved',
                'delivery_status' => 'delivered',
                'invoice_status' => 'unbilled',
                'subtotal' => $subtotal,
                'discount_amount' => $totalDiscount,
                'tax_percent' => $taxPercent,
                'tax_amount' => $taxAmount,
                'shipping_fee' => $shippingFee,
                'total_amount' => $totalAmount,
                'payment_term' => $validated['payment_term'] ?? 'Net 30 Days',
                'notes' => $validated['notes'] ?? null,
                'created_by' => Auth::id() ?? 1,
            ]);

            foreach ($validated['items'] as $itemData) {
                $duration = $isSubRent ? max(1, $itemData['duration'] ?? 1) : 1;
                $lineTotal = ($itemData['quantity'] * $itemData['unit_price'] * $duration) - ($itemData['discount'] ?? 0);

                OrderItem::create([
                    'order_id' => $po->id,
                    'item_id' => $itemData['item_id'] ?? null,
                    'vendor_id' => $validated['contact_id'],
                    'description' => $itemData['name'],
                    'specification' => $itemData['specification'] ?? null,
                    'quantity' => $itemData['quantity'],
                    'unit' => $itemData['unit'] ?? 'Unit',
                    'unit_price' => $itemData['unit_price'],
                    'cost_price' => $itemData['unit_price'],
                    'duration' => $duration,
                    'duration_unit' => $isSubRent ? ($itemData['duration_unit'] ?? 'Bulan') : null,
                    'discount' => $itemData['discount'] ?? 0,
                    'tax_amount' => round($lineTotal * ($taxPercent / 100), 2),
                    'line_total' => $lineTotal,
                ]);
            }

            // If linked to customer order, update customer order's real-time margin!
            if (!empty($validated['parent_id'])) {
                $customerOrder = Order::find($validated['parent_id']);
                if ($customerOrder) {
                    $customerOrder->recalculateMargin();
                }
            }

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Order Pengadaan {$orderNumber} berhasil dibuat!",
                    'order' => $po->load(['contact', 'items.item', 'parent.contact']),
                ]);
            }

            return redirect()->route('erp.procurement.index')
                ->with('success', "Order Pengadaan {$orderNumber} berhasil dibuat!");
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
            return back()->with('error', 'Gagal membuat Pengadaan: ' . $e->getMessage());
        }
    }

    /**
     * Generate Vendor Bill from Purchase Order
     */
    public function generateVendorBill(Request $request, $id)
    {
        $user = auth()->user();
        if ($user && !$user->isAdmin() && !in_array($user->role, ['procurement', 'finance', 'admin_op'])) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki wewenang untuk menerbitkan tagihan vendor.');
        }

        $po = Order::with(['contact', 'parent'])->findOrFail($id);

        if ($po->order_type !== 'purchase_order') {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Hanya PO yang dapat dibuatkan tagihan vendor.'], 400);
            }
            return redirect()->back()->with('error', 'Hanya PO yang dapat dibuatkan tagihan vendor.');
        }

        if (in_array($po->status, ['cancelled', 'draft'])) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => "PO dengan status '{$po->status}' tidak dapat dibuatkan tagihan vendor."], 422);
            }
            return redirect()->back()->with('error', "PO dengan status '{$po->status}' tidak dapat dibuatkan tagihan vendor.");
        }

        if ($po->invoices()->where('invoice_type', 'vendor_bill')->where('status', '!=', 'cancelled')->exists()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Tagihan vendor untuk PO ini sudah dibuat sebelumnya.'], 400);
            }
            return redirect()->back()->with('error', 'Tagihan vendor untuk PO ini sudah dibuat sebelumnya.');
        }

        $validated = $request->validate([
            'vendor_ref_no' => 'nullable|string|max:100',
            'issue_date' => 'required|date',
            'due_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $billNumber = $this->generateBillNumber();

            $bill = Invoice::create([
                'invoice_number' => $billNumber,
                'vendor_ref_no' => $validated['vendor_ref_no'] ?? null,
                'invoice_type' => 'vendor_bill',
                'order_id' => $po->id,
                'customer_order_id' => $po->parent_id,
                'contact_id' => $po->contact_id,
                'issue_date' => $validated['issue_date'],
                'due_date' => $validated['due_date'],
                'subtotal' => $po->subtotal,
                'discount_amount' => $po->discount_amount,
                'tax_amount' => $po->tax_amount,
                'total_amount' => $po->total_amount,
                'paid_amount' => 0.00,
                'balance_due' => $po->total_amount,
                'status' => 'sent',
                'payment_terms' => $po->payment_term ?? 'Net 30',
                'notes' => $validated['notes'] ?? ("Tagihan atas PO {$po->order_number}"),
                'created_by' => Auth::id() ?? 1,
            ]);

            $po->update(['invoice_status' => 'invoiced']);

            DB::commit();

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Tagihan Vendor {$billNumber} berhasil diterbitkan!",
                    'bill' => $bill->load(['contact', 'order']),
                ]);
            }

            return redirect()->route('erp.procurement.index')
                ->with('success', "Tagihan Vendor {$billNumber} berhasil diterbitkan!");
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Gagal membuat tagihan vendor: ' . $e->getMessage());
        }
    }

    /**
     * Allocate vendor or direct cost directly to a Customer Order (SO/RO)
     * Supports both multi-product allocation and single-item allocation
     */
    public function allocateToOrder(Request $request)
    {
        $user = auth()->user();
        if ($user && !$user->isAdmin() && !in_array($user->role, ['procurement', 'operasional', 'admin_op'])) {
            return response()->json(['success' => false, 'message' => 'Akses Ditolak: Anda tidak memiliki wewenang untuk mengalokasikan pengadaan.'], 403);
        }

        // 1. Multi-Product Allocation (Revised Modal)
        if ($request->has('products') && is_array($request->input('products'))) {
            $validated = $request->validate([
                'order_id' => 'required|exists:orders,id',
                'po_date' => 'required|date',
                'products' => 'required|array|min:1',
                'products.*.order_item_id' => 'nullable|exists:order_items,id',
                'products.*.asset_id' => 'nullable|exists:assets,id',
                'products.*.item_name' => 'required|string|max:255',
                'products.*.procurement_type' => 'required|in:sub_rental,purchase_unit,sparepart,logistics,service,office_supplies,marketing,it_software,internal_fleet,internal_service,other',
                'products.*.rental_start_date' => 'nullable|date',
                'products.*.duration' => 'nullable|numeric|min:1',
                'products.*.duration_unit' => 'nullable|string',
                'products.*.vendor_id' => 'nullable|exists:contacts,id',
                'products.*.quantity' => 'required|numeric|min:0.01',
                'products.*.unit_cost' => 'required|numeric|min:0',
                'products.*.include_ppn' => 'nullable|boolean',
                'products.*.shipping_fee' => 'nullable|numeric|min:0',
                'products.*.payment_term' => 'nullable|string|max:100',
                'products.*.notes' => 'nullable|string',
            ]);

            DB::beginTransaction();
            try {
                $customerOrder = Order::with('items')->findOrFail($validated['order_id']);

                // Filter products that have a vendor selected
                $allocatedProducts = array_filter($validated['products'], function ($p) {
                    return !empty($p['vendor_id']);
                });

                $allocatedAssets = array_filter($validated['products'], function ($p) {
                    return !empty($p['asset_id']);
                });

                if (empty($allocatedProducts) && empty($allocatedAssets)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Silakan pilih Vendor Mitra atau pilih Armada Internal untuk minimal satu produk yang dialokasikan.'
                    ], 422);
                }

                // Delete previous unbilled POs linked to this customer order to prevent duplicates when updating
                $oldPOs = Order::where('parent_id', $customerOrder->id)
                    ->where('order_type', 'purchase_order')
                    ->where('invoice_status', 'unbilled')
                    ->get();

                foreach ($oldPOs as $oldPo) {
                    OrderItem::where('order_id', $oldPo->id)->delete();
                    $oldPo->delete();
                }

                // Group allocated products by vendor_id and procurement_type
                $grouped = [];
                foreach ($allocatedProducts as $prod) {
                    $groupKey = $prod['vendor_id'] . '_' . $prod['procurement_type'];
                    $grouped[$groupKey][] = $prod;
                }

                $createdPOs = [];
                $unitMap = [
                    'bulan' => 'month', 'month' => 'month', 
                    'hari' => 'day', 'day' => 'day', 
                    'minggu' => 'week', 'week' => 'week', 
                    'tahun' => 'year', 'year' => 'year'
                ];

                foreach ($grouped as $groupKey => $items) {
                    $firstItem = $items[0];
                    $vendorId = $firstItem['vendor_id'];
                    $procType = $firstItem['procurement_type'];
                    $isSubRent = ($procType === 'sub_rental');

                    $prefix = $isSubRent ? 'SR' : 'PO';
                    $orderNumber = $this->generateOrderNumber($prefix);

                    $poSubtotal = 0;
                    $poShipping = 0;
                    $hasPpn = false;
                    $poStartDate = $firstItem['rental_start_date'] ?? $customerOrder->rental_start_date ?? $validated['po_date'];
                    $poDuration = $firstItem['duration'] ?? ($customerOrder->rental_duration ?? 1);
                    $poDurationUnit = $unitMap[strtolower($firstItem['duration_unit'] ?? 'month')] ?? 'month';
                    $poPaymentTerm = $firstItem['payment_term'] ?? 'Net 30 Days';
                    $poNotesArr = [];

                    foreach ($items as $it) {
                        $qty = (float) $it['quantity'];
                        $unitCost = (float) $it['unit_cost'];
                        $dur = $isSubRent ? max(1, (float) ($it['duration'] ?? 1)) : 1;
                        $lineTotal = $qty * $unitCost * $dur;

                        $poSubtotal += $lineTotal;
                        $poShipping += (float) ($it['shipping_fee'] ?? 0);
                        if (!empty($it['include_ppn'])) {
                            $hasPpn = true;
                        }
                        if (!empty($it['notes'])) {
                            $poNotesArr[] = $it['notes'];
                        }
                    }

                    $taxPercent = $hasPpn ? 11 : 0;
                    $taxAmount = $hasPpn ? round($poSubtotal * 0.11, 2) : 0;
                    $totalAmount = $poSubtotal + $taxAmount + $poShipping;

                    $combinedNotes = !empty($poNotesArr) 
                        ? implode('; ', $poNotesArr) 
                        : "Dialokasikan dari pesanan {$customerOrder->order_number}";

                    $po = Order::create([
                        'order_number' => $orderNumber,
                        'order_type' => 'purchase_order',
                        'procurement_type' => $procType,
                        'contact_id' => $vendorId,
                        'parent_id' => $customerOrder->id,
                        'order_date' => $validated['po_date'],
                        'rental_start_date' => $isSubRent ? $poStartDate : null,
                        'rental_end_date' => $customerOrder->rental_end_date ?? null,
                        'rental_duration' => $isSubRent ? $poDuration : null,
                        'rental_duration_unit' => $isSubRent ? $poDurationUnit : 'month',
                        'status' => 'approved',
                        'delivery_status' => 'delivered',
                        'invoice_status' => 'unbilled',
                        'subtotal' => $poSubtotal,
                        'discount_amount' => 0,
                        'tax_percent' => $taxPercent,
                        'tax_amount' => $taxAmount,
                        'shipping_fee' => $poShipping,
                        'total_amount' => $totalAmount,
                        'payment_term' => $poPaymentTerm,
                        'notes' => $combinedNotes,
                        'created_by' => Auth::id() ?? 1,
                    ]);

                    foreach ($items as $it) {
                        $qty = (float) $it['quantity'];
                        $unitCost = (float) $it['unit_cost'];
                        $dur = $isSubRent ? max(1, (float) ($it['duration'] ?? 1)) : 1;
                        $lineTotal = $qty * $unitCost * $dur;
                        $itemTax = !empty($it['include_ppn']) ? round($lineTotal * 0.11, 2) : 0;

                        // Create line item under the PO (duration cannot be null in DB)
                        OrderItem::create([
                            'order_id' => $po->id,
                            'vendor_id' => $vendorId,
                            'description' => $it['item_name'],
                            'quantity' => $qty,
                            'unit' => $it['unit'] ?? 'Unit',
                            'unit_price' => $unitCost,
                            'cost_price' => $unitCost,
                            'duration' => $isSubRent ? max(1, (int) $dur) : 1,
                            'duration_unit' => $isSubRent ? ($it['duration_unit'] ?? 'Bulan') : ($it['duration_unit'] ?? 'Kali'),
                            'tax_amount' => $itemTax,
                            'line_total' => $lineTotal,
                            'notes' => $it['notes'] ?? null,
                        ]);

                        // Sync vendor_id and cost_price on customer order item if provided
                        if (!empty($it['order_item_id'])) {
                            $custItem = OrderItem::find($it['order_item_id']);
                            if ($custItem && $custItem->order_id == $customerOrder->id) {
                                $custItem->vendor_id = $vendorId;
                                $custItem->cost_price = $unitCost;
                                $custItem->saveQuietly();
                            }
                        }
                    }

                    $createdPOs[] = $po;
                }

                // Sync vendor_id, asset_id and cost_price on customer order items
                foreach ($validated['products'] as $p) {
                    if (!empty($p['order_item_id'])) {
                        $custItem = OrderItem::find($p['order_item_id']);
                        if ($custItem && $custItem->order_id == $customerOrder->id) {
                            if (!empty($p['asset_id'])) {
                                $custItem->asset_id = $p['asset_id'];
                                // Update asset status to rented
                                Asset::where('id', $p['asset_id'])->update([
                                    'status' => 'rented',
                                    'current_location' => 'Site Proyek ' . ($customerOrder->contact->name ?? 'Pelanggan')
                                ]);
                            }
                            if (empty($p['vendor_id'])) {
                                $custItem->vendor_id = null;
                                $custItem->cost_price = 0;
                            }
                            $custItem->saveQuietly();
                        }
                    }
                }

                // Recalculate customer order margin automatically
                $customerOrder->recalculateMargin();

                DB::commit();

                $msg = count($createdPOs) > 0 
                    ? "Berhasil menerbitkan " . count($createdPOs) . " PO Vendor untuk pesanan {$customerOrder->order_number}!"
                    : "Alokasi Armada Internal untuk pesanan {$customerOrder->order_number} berhasil disimpan!";

                return response()->json([
                    'success' => true,
                    'message' => $msg,
                    'customer_order' => $customerOrder->fresh(['vendorOrders.contact', 'vendorOrders.items']),
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
        }

        // 2. Fallback Single-Item Allocation
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'vendor_id' => 'required|exists:contacts,id',
            'procurement_type' => 'required|in:sub_rental,purchase_unit,sparepart,logistics,other',
            'item_name' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0.01',
            'unit_cost' => 'required|numeric|min:0',
            'duration' => 'nullable|integer|min:1',
            'duration_unit' => 'nullable|string',
            'logistics_cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $customerOrder = Order::findOrFail($validated['order_id']);

            $isSubRent = ($validated['procurement_type'] === 'sub_rental');
            $prefix = $isSubRent ? 'SR' : 'PO';
            $orderNumber = $this->generateOrderNumber($prefix);

            $duration = $isSubRent ? max(1, $validated['duration'] ?? ($customerOrder->rental_duration ?? 1)) : 1;
            $subtotal = ($validated['quantity'] * $validated['unit_cost'] * $duration);
            $logisticsCost = $validated['logistics_cost'] ?? 0;
            $totalAmount = $subtotal;

            $unitMap = ['bulan' => 'month', 'month' => 'month', 'hari' => 'day', 'day' => 'day', 'minggu' => 'week', 'week' => 'week', 'tahun' => 'year', 'year' => 'year'];
            $durationUnit = $unitMap[strtolower($validated['duration_unit'] ?? '')] ?? 'month';

            $po = Order::create([
                'order_number' => $orderNumber,
                'order_type' => 'purchase_order',
                'procurement_type' => $validated['procurement_type'],
                'contact_id' => $validated['vendor_id'],
                'parent_id' => $customerOrder->id,
                'order_date' => now()->format('Y-m-d'),
                'rental_start_date' => $customerOrder->rental_start_date ?? now()->format('Y-m-d'),
                'rental_end_date' => $customerOrder->rental_end_date ?? null,
                'rental_duration' => $isSubRent ? $duration : null,
                'rental_duration_unit' => $isSubRent ? $durationUnit : 'month',
                'status' => 'approved',
                'delivery_status' => 'delivered',
                'invoice_status' => 'unbilled',
                'subtotal' => $subtotal,
                'discount_amount' => 0,
                'tax_percent' => 0,
                'tax_amount' => 0,
                'shipping_fee' => 0,
                'total_amount' => $totalAmount,
                'payment_term' => 'Net 30 Days',
                'notes' => $validated['notes'] ?? "Dialokasikan untuk pesanan {$customerOrder->order_number}",
                'created_by' => Auth::id() ?? 1,
            ]);

            OrderItem::create([
                'order_id' => $po->id,
                'vendor_id' => $validated['vendor_id'],
                'description' => $validated['item_name'],
                'quantity' => $validated['quantity'],
                'unit' => 'Unit',
                'unit_price' => $validated['unit_cost'],
                'cost_price' => $validated['unit_cost'],
                'duration' => $duration,
                'duration_unit' => $isSubRent ? ($validated['duration_unit'] ?? 'Bulan') : null,
                'line_total' => $subtotal,
            ]);

            // Update logistics cost if specified
            if ($logisticsCost > 0) {
                $customerOrder->logistics_cost = ($customerOrder->logistics_cost ?? 0) + $logisticsCost;
                $customerOrder->saveQuietly();
            }

            // Recalculate customer order margin
            $customerOrder->recalculateMargin();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Vendor berhasil dialokasikan ke {$customerOrder->order_number}! Gross Profit & Margin telah diperbarui.",
                'po' => $po->load(['contact']),
                'customer_order' => $customerOrder->fresh(['vendorOrders.contact']),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get PO Details for Modal Drawer
     */
    public function getDetails($id)
    {
        $user = auth()->user();
        if ($user && !$user->isAdmin() && !in_array($user->role, ['procurement', 'operasional', 'finance', 'admin_op'])) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki wewenang untuk mengakses rincian PO ini.');
        }

        $po = Order::with(['contact', 'items.item', 'parent.contact', 'invoices.payments'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'order' => $po,
        ]);
    }

    /**
     * Generate unique procurement order number (SR or PO)
     */
    private function generateOrderNumber(string $prefix): string
    {
        $ym = date('Ym');
        $basePattern = "{$prefix}-{$ym}-";

        $latest = Order::where('order_number', 'like', "{$basePattern}%")
            ->orderByDesc('order_number')
            ->first();

        $nextNum = 1;
        if ($latest && preg_match('/' . preg_quote($basePattern, '/') . '(\d+)/', $latest->order_number, $matches)) {
            $nextNum = intval($matches[1]) + 1;
        }

        do {
            $orderNumber = $basePattern . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
            $exists = Order::where('order_number', $orderNumber)->exists();
            if ($exists) {
                $nextNum++;
            }
        } while ($exists);

        return $orderNumber;
    }

    /**
     * Generate unique vendor bill invoice number
     */
    private function generateBillNumber(): string
    {
        $ym = date('Ym');
        $basePattern = "BILL-{$ym}-";

        $latest = Invoice::where('invoice_number', 'like', "{$basePattern}%")
            ->orderByDesc('invoice_number')
            ->first();

        $nextNum = 1;
        if ($latest && preg_match('/' . preg_quote($basePattern, '/') . '(\d+)/', $latest->invoice_number, $matches)) {
            $nextNum = intval($matches[1]) + 1;
        }

        do {
            $billNumber = $basePattern . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
            $exists = Invoice::where('invoice_number', $billNumber)->exists();
            if ($exists) {
                $nextNum++;
            }
        } while ($exists);

        return $billNumber;
    }

    /**
     * Cancel a Purchase Order (Only allowed if unbilled)
     */
    public function cancelOrder(Request $request, $id)
    {
        $po = Order::with(['contact', 'parent', 'invoices'])->findOrFail($id);

        $user = auth()->user();
        if ($user && !$user->isAdmin() && !in_array($user->role, ['procurement', 'operasional', 'admin_op'])) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki otoritas untuk membatalkan Purchase Order.');
        }

        if ($po->order_type !== 'purchase_order') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya Order Pengadaan (PO) yang dapat diproses di sini.'
            ], 400);
        }

        if (in_array($po->status, ['completed', 'cancelled'])) {
            return response()->json([
                'success' => false,
                'message' => "PO dengan status '{$po->status}' tidak dapat dibatalkan."
            ], 422);
        }

        // Check if invoice exists and is billed or paid
        $hasBilledInvoices = $po->invoices()->where('status', '!=', 'cancelled')->exists();
        if ($po->invoice_status !== 'unbilled' || $hasBilledInvoices) {
            return response()->json([
                'success' => false,
                'message' => 'PO ini tidak dapat dibatalkan karena sudah memiliki tagihan vendor (Vendor Bill). Batalkan atau hapus tagihan vendor terlebih dahulu di menu Keuangan.'
            ], 422);
        }

        $validated = $request->validate([
            'cancel_reason' => 'required|string|max:1000',
        ], [
            'cancel_reason.required' => 'Mohon isi alasan pembatalan PO.',
        ]);

        DB::beginTransaction();
        try {
            $po->update([
                'status' => 'cancelled',
                'cancel_reason' => $validated['cancel_reason'],
            ]);

            // If linked to a customer order (SO / RO), recalculate customer order margin
            if ($po->parent_id) {
                $customerOrder = Order::find($po->parent_id);
                if ($customerOrder) {
                    $customerOrder->recalculateMargin();
                }
            }

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Order Pengadaan {$po->order_number} berhasil dibatalkan.",
                ]);
            }

            return redirect()->route('erp.procurement.index')
                ->with('success', "Order Pengadaan {$po->order_number} berhasil dibatalkan.");
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', 'Gagal membatalkan PO: ' . $e->getMessage());
        }
    }
}

