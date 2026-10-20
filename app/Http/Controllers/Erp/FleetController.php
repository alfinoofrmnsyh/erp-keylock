<?php

namespace App\Http\Controllers\Erp;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetService;
use App\Models\Contact;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FleetController extends Controller
{
    /**
     * Display Fleet & Armada Management Hub
     */
    public function index(Request $request)
    {
        $statusFilter = $request->query('status', 'all');
        $ownershipFilter = $request->query('ownership', 'all');
        $search = $request->query('search');

        $query = Asset::with([
            'item.product',
            'latestService.vendor',
            'activeOrderItem.order.contact',
            'services.vendor',
            'services.creator',
        ]);

        // Status Filter
        if ($statusFilter !== 'all' && !empty($statusFilter)) {
            $query->where('status', $statusFilter);
        }

        // Ownership Filter
        if ($ownershipFilter !== 'all' && !empty($ownershipFilter)) {
            $query->where('ownership', 'like', "%{$ownershipFilter}%");
        }

        // Search Filter
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('asset_code', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%")
                  ->orWhere('license_plate', 'like', "%{$search}%")
                  ->orWhere('current_location', 'like', "%{$search}%")
                  ->orWhereHas('item', function ($iq) use ($search) {
                      $iq->where('name', 'like', "%{$search}%")
                         ->orWhere('model', 'like', "%{$search}%")
                         ->orWhere('brand', 'like', "%{$search}%");
                  })
                  ->orWhereHas('activeOrderItem.order.contact', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('activeOrderItem.order', function ($oq) use ($search) {
                      $oq->where('order_number', 'like', "%{$search}%");
                  });
            });
        }

        // KPI Counts
        $totalFleetCount = Asset::count();
        $availableCount = Asset::available()->count();
        $rentedCount = Asset::rented()->count();
        $maintenanceCount = Asset::maintenance()->count();

        // Service Alerts
        $overdueServiceCount = Asset::whereNotNull('next_service_due')
            ->where('next_service_due', '<', now()->format('Y-m-d'))
            ->count();
        $upcomingServiceCount = Asset::whereNotNull('next_service_due')
            ->whereBetween('next_service_due', [now()->format('Y-m-d'), now()->addDays(14)->format('Y-m-d')])
            ->count();

        // Paginate Armada
        $perPage = (int) $request->query('per_page', 15);
        if (!in_array($perPage, [10, 15, 25, 50, 100])) {
            $perPage = 15;
        }

        $armada = $query->latest('id')->paginate($perPage)->withQueryString();

        // Catalog items for unit creation/editing
        $catalogItems = Item::where('is_active', true)->orderBy('name')->get();

        // Vendors for service/workshop
        $serviceVendors = Contact::where('type', 'vendor')->orderBy('name')->get();

        // Active rental orders for direct allocation
        $activeCustomerOrders = Order::whereIn('order_type', ['rental_order', 'sales_order'])
            ->whereIn('status', ['approved', 'in_progress'])
            ->with(['contact', 'items'])
            ->latest('id')
            ->get();

        return view('erp.fleet', compact(
            'armada',
            'statusFilter',
            'ownershipFilter',
            'search',
            'totalFleetCount',
            'availableCount',
            'rentedCount',
            'maintenanceCount',
            'overdueServiceCount',
            'upcomingServiceCount',
            'catalogItems',
            'serviceVendors',
            'activeCustomerOrders'
        ));
    }

    private function checkOperationalAccess(): void
    {
        $user = auth()->user();
        abort_if(!$user || (!$user->isAdmin() && !in_array($user->role, ['operasional', 'admin_op'])), 403, 'Akses Ditolak: Anda tidak memiliki wewenang untuk mengelola armada.');
    }

    /**
     * Store new armada unit
     */
    public function store(Request $request)
    {
        $this->checkOperationalAccess();

        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'asset_code' => 'nullable|string|max:50|unique:assets,asset_code',
            'serial_number' => 'required|string|max:100',
            'ownership' => 'required|string|max:100',
            'license_plate' => 'nullable|string|max:30',
            'year' => 'nullable|integer|min:1990|max:' . (date('Y') + 1),
            'hour_meter' => 'nullable|numeric|min:0',
            'current_location' => 'nullable|string|max:255',
            'status' => 'required|in:available,rented,maintenance,reserved,sold',
            'condition_notes' => 'nullable|string',
            'last_service_at' => 'nullable|date',
            'next_service_due' => 'nullable|date',
        ]);

        if (empty($validated['asset_code'])) {
            $item = Item::find($validated['item_id']);
            $prefix = 'AST-';
            if ($item && str_contains(strtolower($item->name), 'scissor')) {
                $prefix = 'AST-SL-';
            } elseif ($item && str_contains(strtolower($item->name), 'boom')) {
                $prefix = 'AST-BL-';
            } else {
                $prefix = 'AST-FL-';
            }
            $existingCodes = Asset::where('asset_code', 'like', "{$prefix}%")->pluck('asset_code')->toArray();
            $maxNum = 0;
            foreach ($existingCodes as $c) {
                if (preg_match('/(\d+)$/', $c, $m)) {
                    $num = intval($m[1]);
                    if ($num > $maxNum) $maxNum = $num;
                }
            }
            $validated['asset_code'] = $prefix . str_pad($maxNum + 1, 3, '0', STR_PAD_LEFT);
        }

        if (empty($validated['current_location'])) {
            $validated['current_location'] = 'Pool Utama Cikarang';
        }

        $asset = Asset::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Armada {$asset->asset_code} berhasil ditambahkan!",
                'asset' => $asset->load('item')
            ]);
        }

        return redirect()->route('erp.fleet.index')
            ->with('success', "Armada {$asset->asset_code} berhasil ditambahkan!");
    }

    /**
     * Update armada unit details
     */
    public function update(Request $request, $id)
    {
        $this->checkOperationalAccess();

        $asset = Asset::findOrFail($id);

        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'asset_code' => 'nullable|string|max:50|unique:assets,asset_code,' . $asset->id,
            'serial_number' => 'required|string|max:100',
            'ownership' => 'required|string|max:100',
            'license_plate' => 'nullable|string|max:30',
            'year' => 'nullable|integer|min:1990|max:' . (date('Y') + 1),
            'hour_meter' => 'nullable|numeric|min:0',
            'current_location' => 'nullable|string|max:255',
            'status' => 'required|in:available,rented,maintenance,reserved,sold',
            'condition_notes' => 'nullable|string',
            'last_service_at' => 'nullable|date',
            'next_service_due' => 'nullable|date',
        ]);

        if (empty($validated['asset_code'])) {
            $validated['asset_code'] = $asset->asset_code;
        }

        $asset->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Armada {$asset->asset_code} berhasil diperbarui!",
                'asset' => $asset->load('item')
            ]);
        }

        return redirect()->route('erp.fleet.index')
            ->with('success', "Data Armada {$asset->asset_code} berhasil diperbarui!");
    }

    /**
     * Record Service / Maintenance Log for an Armada Unit
     */
    public function storeService(Request $request, $id)
    {
        $this->checkOperationalAccess();

        $asset = Asset::findOrFail($id);

        $validated = $request->validate([
            'service_date' => 'required|date',
            'service_type' => 'required|string|max:50',
            'hour_meter' => 'nullable|numeric|min:0',
            'technician_name' => 'nullable|string|max:150',
            'vendor_contact_id' => 'nullable|exists:contacts,id',
            'vendor_id' => 'nullable|exists:contacts,id',
            'cost' => 'nullable|numeric|min:0',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled',
            'description' => 'nullable|string',
            'work_description' => 'nullable|string',
            'spareparts_replaced' => 'nullable|string',
            'parts_replaced' => 'nullable|string',
            'next_service_date' => 'nullable|date',
            'next_service_due' => 'nullable|date',
            'next_service_hour_meter' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'update_asset_status' => 'nullable|string|in:keep,available,maintenance',
        ]);

        DB::beginTransaction();
        try {
            $serviceNumber = $this->generateServiceNumber();
            $desc = !empty($validated['work_description']) ? $validated['work_description'] : (!empty($validated['description']) ? $validated['description'] : 'Servis & Perawatan Unit');
            $parts = !empty($validated['parts_replaced']) ? $validated['parts_replaced'] : ($validated['spareparts_replaced'] ?? null);
            $vendorId = !empty($validated['vendor_id']) ? $validated['vendor_id'] : ($validated['vendor_contact_id'] ?? null);
            $nextDate = !empty($validated['next_service_due']) ? $validated['next_service_due'] : ($validated['next_service_date'] ?? null);

            $service = AssetService::create([
                'asset_id' => $asset->id,
                'service_number' => $serviceNumber,
                'service_date' => $validated['service_date'],
                'service_type' => $validated['service_type'],
                'hour_meter' => $validated['hour_meter'] ?? $asset->hour_meter,
                'technician_name' => $validated['technician_name'] ?? null,
                'vendor_contact_id' => $vendorId,
                'cost' => $validated['cost'] ?? 0.00,
                'status' => $validated['status'],
                'description' => $desc,
                'spareparts_replaced' => $parts,
                'next_service_date' => $nextDate,
                'next_service_hour_meter' => $validated['next_service_hour_meter'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'created_by' => Auth::id() ?? 1,
            ]);

            // Update parent asset if service is completed
            if ($validated['status'] === 'completed') {
                $assetUpdates = [
                    'last_service_at' => $validated['service_date'],
                ];

                if (!empty($nextDate)) {
                    $assetUpdates['next_service_due'] = $nextDate;
                }

                if (!empty($validated['hour_meter']) && $validated['hour_meter'] >= $asset->hour_meter) {
                    $assetUpdates['hour_meter'] = $validated['hour_meter'];
                }

                // If user selected to return to available or unit was in maintenance
                if (($request->input('update_asset_status') === 'available') || 
                    ($asset->status === 'maintenance' && $request->input('update_asset_status') !== 'keep')) {
                    $assetUpdates['status'] = 'available';
                }

                $asset->update($assetUpdates);
            } elseif ($validated['status'] === 'in_progress' || $request->input('update_asset_status') === 'maintenance') {
                $asset->update(['status' => 'maintenance']);
            }

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Servis {$serviceNumber} untuk Armada {$asset->asset_code} berhasil dicatat!",
                    'service' => $service->load('vendor'),
                    'asset' => $asset->fresh()
                ]);
            }

            return redirect()->route('erp.fleet.index')
                ->with('success', "Catatan Servis {$serviceNumber} untuk Armada {$asset->asset_code} berhasil disimpan!");
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Gagal mencatat servis: ' . $e->getMessage());
        }
    }

    /**
     * Mark an Armada Unit as returned from Customer Order (back to pool)
     */
    public function returnUnit(Request $request, $id)
    {
        $this->checkOperationalAccess();

        $asset = Asset::findOrFail($id);

        $validated = $request->validate([
            'return_date' => 'nullable|date',
            'hour_meter' => 'nullable|numeric|min:0',
            'current_location' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'condition_notes' => 'nullable|string',
        ]);

        $loc = !empty($validated['location']) ? $validated['location'] : (!empty($validated['current_location']) ? $validated['current_location'] : 'Pool Pusat Keylock');

        $asset->update([
            'status' => 'available',
            'hour_meter' => !empty($validated['hour_meter']) ? $validated['hour_meter'] : $asset->hour_meter,
            'current_location' => $loc,
            'condition_notes' => $validated['condition_notes'] ?? $asset->condition_notes,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Unit {$asset->asset_code} berhasil ditandai selesai sewa dan kembali ke pool!",
                'asset' => $asset
            ]);
        }

        return redirect()->route('erp.fleet.index')
            ->with('success', "Unit {$asset->asset_code} berhasil ditandai selesai sewa dan kembali ke pool!");
    }

    /**
     * Direct allocation of an available unit to a Customer Rental Order
     */
    public function assignOrder(Request $request, $id)
    {
        $this->checkOperationalAccess();

        $asset = Asset::findOrFail($id);

        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'order_item_id' => 'nullable|exists:order_items,id',
        ]);

        $order = Order::with('contact')->findOrFail($validated['order_id']);

        DB::beginTransaction();
        try {
            if (!empty($validated['order_item_id'])) {
                $orderItem = OrderItem::where('order_id', $order->id)->findOrFail($validated['order_item_id']);
                $orderItem->update(['asset_id' => $asset->id]);
            } else {
                // Link to first rental item without asset_id or create link
                $item = $order->items()->whereNull('asset_id')->first() ?: $order->items()->first();
                if ($item) {
                    $item->update(['asset_id' => $asset->id]);
                }
            }

            $asset->update([
                'status' => 'rented',
                'current_location' => 'Site Proyek ' . ($order->contact->name ?? 'Customer')
            ]);

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Armada {$asset->asset_code} berhasil dialokasikan ke pesanan {$order->order_number}!",
                    'asset' => $asset->fresh(['activeOrderItem.order.contact'])
                ]);
            }

            return redirect()->route('erp.fleet.index')
                ->with('success', "Armada {$asset->asset_code} berhasil dialokasikan ke pesanan {$order->order_number}!");
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Gagal mengalokasikan armada: ' . $e->getMessage());
        }
    }

    /**
     * Get JSON details of an armada unit including service history and active rental
     */
    public function getDetails($id)
    {
        $asset = Asset::with([
            'item.product',
            'services.vendor',
            'services.creator',
            'activeOrderItem.order.contact',
            'orderItems.order.contact'
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'asset' => $asset
        ]);
    }

    /**
     * Generate unique service number: SRV-YYYYMM-001
     */
    private function generateServiceNumber(): string
    {
        $prefix = 'SRV-' . date('Ym') . '-';
        $latest = AssetService::where('service_number', 'like', "{$prefix}%")
            ->orderBy('service_number', 'desc')
            ->first();

        if ($latest) {
            $lastNumber = (int) substr($latest->service_number, -3);
            $nextNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '001';
        }

        return $prefix . $nextNumber;
    }
}
