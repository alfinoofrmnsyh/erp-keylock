<?php

namespace App\Http\Controllers\Erp;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\LeadFollowUp;
use App\Models\LeadStageHistory;
use App\Models\User;
use App\Models\Contact;
use App\Models\Item;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CrmController extends Controller
{
    /**
     * Main CRM Hub (Ringkasan, Pipeline, Semua Leads, Aktivitas & Follow-up)
     */
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'pipeline');
        if (!in_array($tab, ['pipeline', 'leads', 'activities', 'ringkasan'])) {
            $tab = 'pipeline';
        }

        $currentUser = Auth::user();
        $isAdmin = $this->checkIsAdmin($currentUser);

        // Filters
        $period = $request->query('period', 'this_month');
        $salesFilter = $request->query('sales_id', 'all');
        $stageFilter = $request->query('stage', 'all');
        $sourceFilter = $request->query('source', 'all');
        $typeFilter = $request->query('deal_type', 'all');
        $search = $request->query('search', '');

        // Base Query with Filters
        $leadsQuery = Lead::with(['sales', 'convertedCustomer', 'pendingFollowUps']);

        // Role-based scoping: Sales officers (e.g., Bella) only see their own leads!
        if (!$isAdmin && $currentUser) {
            $salesFilter = (string)$currentUser->id;
            $leadsQuery->where('sales_id', $currentUser->id);
            $salesUsers = User::where('id', $currentUser->id)->get();
        } else {
            $salesUsers = User::whereIn('role', ['sales', 'admin'])->orWhere('id', 1)->orderBy('name')->get();
            if ($salesFilter !== 'all' && !empty($salesFilter)) {
                $leadsQuery->where('sales_id', $salesFilter);
            }
        }

        if ($sourceFilter !== 'all' && !empty($sourceFilter)) {
            $leadsQuery->where('source', $sourceFilter);
        }

        if ($typeFilter !== 'all' && !empty($typeFilter)) {
            $leadsQuery->where('deal_type', $typeFilter);
        }

        if ($stageFilter !== 'all' && !empty($stageFilter)) {
            $leadsQuery->where('pipeline_stage', $stageFilter);
        }

        if (!empty($search)) {
            $leadsQuery->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('pic_name', 'like', "%{$search}%")
                  ->orWhere('pic_phone', 'like', "%{$search}%")
                  ->orWhere('product_interest', 'like', "%{$search}%");
            });
        }

        // Period Filtering for Dashboard metrics
        $periodQuery = clone $leadsQuery;
        if ($period === 'this_month') {
            $periodQuery->whereMonth('entry_date', Carbon::now()->month)
                        ->whereYear('entry_date', Carbon::now()->year);
        } elseif ($period === 'this_quarter') {
            $periodQuery->whereBetween('entry_date', [
                Carbon::now()->firstOfQuarter(),
                Carbon::now()->lastOfQuarter()
            ]);
        } elseif ($period === 'this_year') {
            $periodQuery->whereYear('entry_date', Carbon::now()->year);
        }

        // ==========================================
        // 1. KPI & Summary Metrics (Database driven)
        // ==========================================
        $allLeads = (clone $leadsQuery)->get();
        $totalLeads = $allLeads->count();
        $newLeads = $allLeads->where('pipeline_stage', 'new')->count();
        $activeDeals = $allLeads->whereIn('pipeline_stage', ['contacted', 'qualified', 'proposal', 'negotiation'])->count();
        $runningProposals = $allLeads->whereIn('pipeline_stage', ['proposal', 'negotiation'])->count();
        $wonLeads = $allLeads->where('pipeline_stage', 'won')->count();
        $lostLeads = $allLeads->where('pipeline_stage', 'lost')->count();
        $conversionRate = $totalLeads > 0 ? round(($wonLeads / $totalLeads) * 100, 1) : 0;
        $totalPipelineValue = $allLeads->whereIn('pipeline_stage', ['new', 'contacted', 'qualified', 'proposal', 'negotiation'])->sum('estimated_value');
        $wonPipelineValue = $allLeads->where('pipeline_stage', 'won')->sum('estimated_value');

        // Stage Distribution Summary
        $stagesList = ['new', 'contacted', 'qualified', 'proposal', 'negotiation', 'won', 'lost'];
        $stageDistribution = [];
        foreach ($stagesList as $stg) {
            $stgLeads = $allLeads->where('pipeline_stage', $stg);
            $stageDistribution[$stg] = [
                'count' => $stgLeads->count(),
                'value' => $stgLeads->sum('estimated_value'),
            ];
        }

        // Follow-ups Breakdown
        $todayFollowUpsCount = LeadFollowUp::where('status', 'pending')
            ->whereDate('scheduled_at', Carbon::today())
            ->when(!$isAdmin && $currentUser, function ($q) use ($currentUser) {
                $q->where(function ($sub) use ($currentUser) {
                    $sub->where('user_id', $currentUser->id)
                        ->orWhereHas('lead', function ($lq) use ($currentUser) {
                            $lq->where('sales_id', $currentUser->id);
                        });
                });
            }, function ($q) use ($salesFilter) {
                if ($salesFilter !== 'all' && !empty($salesFilter)) {
                    $q->where('user_id', $salesFilter);
                }
            })->count();

        $overdueFollowUps = LeadFollowUp::with(['lead', 'user'])
            ->where('status', 'pending')
            ->where('scheduled_at', '<', Carbon::today())
            ->when(!$isAdmin && $currentUser, function ($q) use ($currentUser) {
                $q->where(function ($sub) use ($currentUser) {
                    $sub->where('user_id', $currentUser->id)
                        ->orWhereHas('lead', function ($lq) use ($currentUser) {
                            $lq->where('sales_id', $currentUser->id);
                        });
                });
            }, function ($q) use ($salesFilter) {
                if ($salesFilter !== 'all' && !empty($salesFilter)) {
                    $q->where('user_id', $salesFilter);
                }
            })
            ->orderBy('scheduled_at', 'asc')
            ->limit(10)
            ->get();

        $todayFollowUpsList = LeadFollowUp::with(['lead', 'user'])
            ->where('status', 'pending')
            ->whereDate('scheduled_at', Carbon::today())
            ->when(!$isAdmin && $currentUser, function ($q) use ($currentUser) {
                $q->where(function ($sub) use ($currentUser) {
                    $sub->where('user_id', $currentUser->id)
                        ->orWhereHas('lead', function ($lq) use ($currentUser) {
                            $lq->where('sales_id', $currentUser->id);
                        });
                });
            }, function ($q) use ($salesFilter) {
                if ($salesFilter !== 'all' && !empty($salesFilter)) {
                    $q->where('user_id', $salesFilter);
                }
            })
            ->orderBy('scheduled_at', 'asc')
            ->get();

        $upcomingFollowUpsList = LeadFollowUp::with(['lead', 'user'])
            ->where('status', 'pending')
            ->where('scheduled_at', '>', Carbon::today()->endOfDay())
            ->when(!$isAdmin && $currentUser, function ($q) use ($currentUser) {
                $q->where(function ($sub) use ($currentUser) {
                    $sub->where('user_id', $currentUser->id)
                        ->orWhereHas('lead', function ($lq) use ($currentUser) {
                            $lq->where('sales_id', $currentUser->id);
                        });
                });
            }, function ($q) use ($salesFilter) {
                if ($salesFilter !== 'all' && !empty($salesFilter)) {
                    $q->where('user_id', $salesFilter);
                }
            })
            ->orderBy('scheduled_at', 'asc')
            ->limit(10)
            ->get();

        // Recent Activities
        $recentActivities = LeadActivity::with(['lead', 'user'])
            ->when(!$isAdmin && $currentUser, function ($q) use ($currentUser) {
                $q->whereHas('lead', function ($lq) use ($currentUser) {
                    $lq->where('sales_id', $currentUser->id);
                });
            })
            ->latest('activity_date')
            ->limit(10)
            ->get();

        // Top Deals with Largest Value
        $topDeals = (clone $leadsQuery)
            ->whereNotIn('pipeline_stage', ['lost'])
            ->orderBy('estimated_value', 'desc')
            ->limit(5)
            ->get();

        // Monthly Growth (last 6 months)
        $monthlyGrowth = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthDate = Carbon::now()->subMonths($i);
            $monthKey = $monthDate->format('M Y');
            $countQuery = Lead::whereMonth('entry_date', $monthDate->month)
                ->whereYear('entry_date', $monthDate->year);
            if (!$isAdmin && $currentUser) {
                $countQuery->where('sales_id', $currentUser->id);
            }
            $count = $countQuery->count();
            $monthlyGrowth[] = [
                'month' => $monthKey,
                'count' => $count,
            ];
        }

        // ==========================================
        // 2. Kanban Board Data (Grouped by Stage)
        // ==========================================
        $kanbanColumns = [
            'new' => ['title' => 'Prospek Baru', 'zh' => '新线索', 'color' => 'slate', 'leads' => collect()],
            'contacted' => ['title' => 'Sudah Dihubungi', 'zh' => '已初次联系', 'color' => 'blue', 'leads' => collect()],
            'qualified' => ['title' => 'Qualified / Tertarik', 'zh' => '已合格 / 意向明确', 'color' => 'amber', 'leads' => collect()],
            'proposal' => ['title' => 'Penawaran', 'zh' => '已发报价', 'color' => 'purple', 'leads' => collect()],
            'negotiation' => ['title' => 'Negosiasi', 'zh' => '商务谈判', 'color' => 'indigo', 'leads' => collect()],
            'won' => ['title' => 'Won (Berhasil)', 'zh' => '赢单成交', 'color' => 'emerald', 'leads' => collect()],
            'lost' => ['title' => 'Lost (Gagal)', 'zh' => '输单流失', 'color' => 'rose', 'leads' => collect()],
        ];

        foreach ($kanbanColumns as $stageKey => $col) {
            $kanbanColumns[$stageKey]['leads'] = $allLeads->where('pipeline_stage', $stageKey)->values();
        }

        // ==========================================
        // 3. Table Data (Semua Leads)
        // ==========================================
        $leadsTable = (clone $leadsQuery)->latest('id')->paginate(15)->withQueryString();

        // Supporting reference lists
        // Note: $salesUsers is already configured above based on role!
        $catalogItems = Item::where('is_active', true)->orderBy('name')->get();
        $customers = Contact::where('type', 'customer')->orderBy('name')->get();

        return view('erp.crm', compact(
            'isAdmin',
            'tab',
            'period',
            'salesFilter',
            'stageFilter',
            'sourceFilter',
            'typeFilter',
            'search',
            // Metrics
            'totalLeads',
            'newLeads',
            'activeDeals',
            'todayFollowUpsCount',
            'runningProposals',
            'wonLeads',
            'lostLeads',
            'conversionRate',
            'totalPipelineValue',
            'wonPipelineValue',
            'stageDistribution',
            'overdueFollowUps',
            'todayFollowUpsList',
            'upcomingFollowUpsList',
            'recentActivities',
            'topDeals',
            'monthlyGrowth',
            // Kanban & Table
            'kanbanColumns',
            'leadsTable',
            'allLeads',
            // References
            'salesUsers',
            'catalogItems',
            'customers'
        ));
    }

    /**
     * Store a newly created Lead
     */
    public function storeLead(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'industry' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'website' => 'nullable|string|max:255',
            'source' => 'required|string|max:50',
            'entry_date' => 'required|date',
            'sales_id' => 'nullable|exists:users,id',
            'pic_name' => 'required|string|max:150',
            'pic_title' => 'nullable|string|max:100',
            'pic_phone' => 'required|string|max:50',
            'pic_email' => 'nullable|email|max:150',
            'additional_pics' => 'nullable|array',
            'deal_type' => 'required|string|max:50',
            'product_interest' => 'nullable|string|max:150',
            'specification' => 'nullable|string|max:255',
            'items' => 'nullable|array',
            'quantity' => 'nullable|integer|min:1',
            'rental_duration' => 'nullable|string|max:100',
            'project_location' => 'nullable|string|max:255',
            'estimated_budget' => 'nullable|numeric|min:0',
            'estimated_value' => 'nullable|numeric|min:0',
            'expected_closing_date' => 'nullable|date',
            'customer_notes' => 'nullable|string',
            'pipeline_stage' => 'required|string|max:50',
            'priority' => 'required|string|max:50',
            'internal_notes' => 'nullable|string',
        ]);

        // Process items if provided
        $items = $request->input('items', []);
        $productInterest = $validated['product_interest'] ?? null;
        $specification = $validated['specification'] ?? null;
        $quantity = $validated['quantity'] ?? 1;
        $rentalDuration = $validated['rental_duration'] ?? null;
        $calculatedEstimatedValue = $validated['estimated_value'] ?? 0;

        if (is_array($items) && count($items) > 0) {
            $validItems = array_values(array_filter($items, function($it) {
                return !empty($it['name']);
            }));

            if (count($validItems) > 0) {
                $items = $validItems;

                // Auto-detect deal_type from items if present
                $hasRental = false;
                $hasSales = false;
                foreach ($validItems as $it) {
                    if (($it['deal_type'] ?? '') === 'sales') {
                        $hasSales = true;
                    } else {
                        $hasRental = true;
                    }
                }
                if ($hasRental && $hasSales) {
                    $validated['deal_type'] = 'both';
                } elseif ($hasSales) {
                    $validated['deal_type'] = 'sales';
                } else {
                    $validated['deal_type'] = 'rental';
                }

                $names = array_map(function($it) use ($validated) {
                    $qty = $it['quantity'] ?? 1;
                    $typeLabel = '';
                    if ($validated['deal_type'] === 'both') {
                        $typeLabel = (($it['deal_type'] ?? '') === 'sales') ? ' [Beli]' : ' [Sewa]';
                    }
                    return ($it['name'] ?? '') . $typeLabel . ($qty > 1 ? " ({$qty} Unit)" : '');
                }, $validItems);
                $productInterest = implode(', ', $names);

                $specs = array_filter(array_column($validItems, 'specification'));
                if (!empty($specs)) {
                    $specification = implode(' | ', $specs);
                }

                $quantity = array_sum(array_column($validItems, 'quantity')) ?: 1;

                if (in_array($validated['deal_type'], ['rental', 'both']) && !empty($validItems[0]['duration'])) {
                    $rentalDuration = ($validItems[0]['duration'] ?? 1) . ' ' . ($validItems[0]['duration_unit'] ?? 'Bulan');
                }

                if (empty($calculatedEstimatedValue) || $calculatedEstimatedValue == 0) {
                    $sum = 0;
                    foreach ($validItems as $it) {
                        $q = (int)($it['quantity'] ?? 1);
                        $p = (float)($it['unit_price'] ?? 0);
                        $itemType = $it['deal_type'] ?? ($validated['deal_type'] === 'sales' ? 'sales' : 'rental');
                        $isRental = ($validated['deal_type'] === 'rental') || ($validated['deal_type'] === 'both' && $itemType === 'rental');
                        if ($isRental) {
                            $dur = (int)($it['duration'] ?? 1);
                            $sum += ($q * $dur * $p);
                        } else {
                            $sum += ($q * $p);
                        }
                    }
                    if ($sum > 0) {
                        $calculatedEstimatedValue = $sum;
                    }
                }
            }
        }

        // Check role and assign sales_id
        $currentUser = Auth::user();
        $isAdmin = $this->checkIsAdmin($currentUser);

        if (!$isAdmin && $currentUser) {
            $validated['sales_id'] = $currentUser->id;
        } else {
            $validated['sales_id'] = $validated['sales_id'] ?? ($currentUser->id ?? 1);
        }

        // Auto-generate Unique Code
        $code = $this->generateLeadCode();

        DB::beginTransaction();
        try {
            $lead = Lead::create([
                'code' => $code,
                'company_name' => $validated['company_name'],
                'industry' => $validated['industry'] ?? null,
                'address' => $validated['address'] ?? null,
                'website' => $validated['website'] ?? null,
                'source' => $validated['source'],
                'entry_date' => $validated['entry_date'],
                'sales_id' => $validated['sales_id'],
                'pic_name' => $validated['pic_name'],
                'pic_title' => $validated['pic_title'] ?? null,
                'pic_phone' => $validated['pic_phone'],
                'pic_email' => $validated['pic_email'] ?? null,
                'additional_pics' => $validated['additional_pics'] ?? null,
                'deal_type' => $validated['deal_type'],
                'items' => $items,
                'product_interest' => $productInterest,
                'specification' => $specification,
                'quantity' => $quantity,
                'rental_duration' => $rentalDuration,
                'project_location' => $validated['project_location'] ?? null,
                'estimated_budget' => $validated['estimated_budget'] ?? null,
                'estimated_value' => $calculatedEstimatedValue,
                'expected_closing_date' => $validated['expected_closing_date'] ?? null,
                'customer_notes' => $validated['customer_notes'] ?? null,
                'pipeline_stage' => $validated['pipeline_stage'],
                'priority' => $validated['priority'],
                'internal_notes' => $validated['internal_notes'] ?? null,
            ]);

            // Record Initial Stage History
            LeadStageHistory::create([
                'lead_id' => $lead->id,
                'user_id' => Auth::id() ?? $lead->sales_id,
                'from_stage' => 'new',
                'to_stage' => $lead->pipeline_stage,
                'change_reason' => 'Pencatatan awal calon prospek di CRM.',
                'created_at' => now(),
            ]);

            // If initial follow-up task is scheduled in the request
            if ($request->filled('initial_follow_up_date')) {
                LeadFollowUp::create([
                    'lead_id' => $lead->id,
                    'user_id' => $lead->sales_id,
                    'follow_up_type' => $request->input('initial_follow_up_type', 'Telepon'),
                    'scheduled_at' => $request->input('initial_follow_up_date'),
                    'purpose_notes' => $request->input('initial_follow_up_notes', 'Follow up perdana kontak calon customer.'),
                    'status' => 'pending',
                ]);
            }

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Leads {$lead->code} ({$lead->company_name}) berhasil dibuat!",
                    'data' => $lead->load('sales'),
                ]);
            }

            return redirect()->route('erp.crm.index', ['tab' => 'leads'])
                ->with('success', "Leads {$lead->code} ({$lead->company_name}) berhasil dibuat!");
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Update an existing Lead
     */
    public function updateLead(Request $request, $id)
    {
        $lead = Lead::findOrFail($id);
        $currentUser = Auth::user();
        $isAdmin = $this->checkIsAdmin($currentUser);

        // Security check: non-admin sales cannot edit other sales' leads
        if (!$isAdmin && $currentUser && $lead->sales_id != $currentUser->id) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak. Anda hanya dapat mengubah data leads milik Anda sendiri.'], 403);
            }
            abort(403, 'Akses ditolak. Anda hanya dapat mengubah data leads milik Anda sendiri.');
        }

        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'industry' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'website' => 'nullable|string|max:255',
            'source' => 'required|string|max:50',
            'entry_date' => 'required|date',
            'sales_id' => 'nullable|exists:users,id',
            'pic_name' => 'required|string|max:150',
            'pic_title' => 'nullable|string|max:100',
            'pic_phone' => 'required|string|max:50',
            'pic_email' => 'nullable|email|max:150',
            'additional_pics' => 'nullable|array',
            'deal_type' => 'required|string|max:50',
            'product_interest' => 'nullable|string|max:150',
            'specification' => 'nullable|string|max:255',
            'items' => 'nullable|array',
            'quantity' => 'nullable|integer|min:1',
            'rental_duration' => 'nullable|string|max:100',
            'project_location' => 'nullable|string|max:255',
            'estimated_budget' => 'nullable|numeric|min:0',
            'estimated_value' => 'nullable|numeric|min:0',
            'expected_closing_date' => 'nullable|date',
            'customer_notes' => 'nullable|string',
            'pipeline_stage' => 'required|string|max:50',
            'priority' => 'required|string|max:50',
            'internal_notes' => 'nullable|string',
        ]);

        // Process items if provided
        $items = $request->input('items', []);
        if (is_array($items) && count($items) > 0) {
            $validItems = array_values(array_filter($items, function($it) {
                return !empty($it['name']);
            }));

            if (count($validItems) > 0) {
                $validated['items'] = $validItems;

                // Auto-detect deal_type from items if present
                $hasRental = false;
                $hasSales = false;
                foreach ($validItems as $it) {
                    if (($it['deal_type'] ?? '') === 'sales') {
                        $hasSales = true;
                    } else {
                        $hasRental = true;
                    }
                }
                if ($hasRental && $hasSales) {
                    $validated['deal_type'] = 'both';
                } elseif ($hasSales) {
                    $validated['deal_type'] = 'sales';
                } else {
                    $validated['deal_type'] = 'rental';
                }

                $names = array_map(function($it) use ($validated) {
                    $qty = $it['quantity'] ?? 1;
                    $typeLabel = '';
                    if ($validated['deal_type'] === 'both') {
                        $typeLabel = (($it['deal_type'] ?? '') === 'sales') ? ' [Beli]' : ' [Sewa]';
                    }
                    return ($it['name'] ?? '') . $typeLabel . ($qty > 1 ? " ({$qty} Unit)" : '');
                }, $validItems);
                $validated['product_interest'] = implode(', ', $names);

                $specs = array_filter(array_column($validItems, 'specification'));
                if (!empty($specs)) {
                    $validated['specification'] = implode(' | ', $specs);
                }

                $validated['quantity'] = array_sum(array_column($validItems, 'quantity')) ?: 1;

                if (in_array($validated['deal_type'], ['rental', 'both']) && !empty($validItems[0]['duration'])) {
                    $validated['rental_duration'] = ($validItems[0]['duration'] ?? 1) . ' ' . ($validItems[0]['duration_unit'] ?? 'Bulan');
                }

                if (empty($validated['estimated_value']) || $validated['estimated_value'] == 0) {
                    $sum = 0;
                    foreach ($validItems as $it) {
                        $q = (int)($it['quantity'] ?? 1);
                        $p = (float)($it['unit_price'] ?? 0);
                        $itemType = $it['deal_type'] ?? ($validated['deal_type'] === 'sales' ? 'sales' : 'rental');
                        $isRental = ($validated['deal_type'] === 'rental') || ($validated['deal_type'] === 'both' && $itemType === 'rental');
                        if ($isRental) {
                            $dur = (int)($it['duration'] ?? 1);
                            $sum += ($q * $dur * $p);
                        } else {
                            $sum += ($q * $p);
                        }
                    }
                    if ($sum > 0) {
                        $validated['estimated_value'] = $sum;
                    }
                }
            } else {
                $validated['items'] = [];
            }
        }

        if (!$isAdmin && $currentUser) {
            $validated['sales_id'] = $currentUser->id;
        }

        $oldStage = $lead->pipeline_stage;
        $newStage = $validated['pipeline_stage'];

        DB::beginTransaction();
        try {
            $lead->update($validated);

            // If stage changed, record audit history
            if ($oldStage !== $newStage) {
                LeadStageHistory::create([
                    'lead_id' => $lead->id,
                    'user_id' => Auth::id() ?? $lead->sales_id,
                    'from_stage' => $oldStage,
                    'to_stage' => $newStage,
                    'change_reason' => $request->input('change_reason', 'Pembaruan tahapan pipeline dari form edit leads.'),
                    'created_at' => now(),
                ]);
            }

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Data Leads {$lead->code} berhasil diperbarui!",
                    'data' => $lead->load('sales'),
                ]);
            }

            return redirect()->route('erp.crm.index', ['tab' => 'leads'])
                ->with('success', "Data Leads {$lead->code} berhasil diperbarui!");
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Update Pipeline Stage (e.g. from Kanban drag & drop or quick stage move)
     */
    public function updateStage(Request $request, $id)
    {
        $lead = Lead::findOrFail($id);
        $currentUser = Auth::user();
        $isAdmin = $this->checkIsAdmin($currentUser);

        if (!$isAdmin && $currentUser && $lead->sales_id != $currentUser->id) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak. Anda hanya dapat memindahkan tahapan leads milik Anda sendiri.'], 403);
        }

        $validated = $request->validate([
            'pipeline_stage' => 'required|string|in:new,contacted,qualified,proposal,negotiation,won,lost',
            'change_reason' => 'nullable|string|max:255',
        ]);

        $oldStage = $lead->pipeline_stage;
        $newStage = $validated['pipeline_stage'];

        if ($oldStage === $newStage) {
            return response()->json(['success' => true, 'message' => 'Tahapan pipeline tidak berubah.']);
        }

        DB::beginTransaction();
        try {
            $lead->pipeline_stage = $newStage;

            $convertedCustomer = null;
            if ($newStage === 'won') {
                if (empty($lead->converted_at)) {
                    $lead->converted_at = now();
                }

                // Auto-convert to Customer in Master Data with Sales PIC
                $existing = !empty($lead->converted_customer_id) ? Contact::find($lead->converted_customer_id) : null;
                $isMatch = $existing && $this->isCustomerMatchingLead($existing, $lead);

                if (!$existing || !$isMatch) {
                    $salesUser = $lead->sales ?? \App\Models\User::find($lead->sales_id);
                    $salesName = $salesUser ? $salesUser->name : (Auth::user()?->name ?? 'Sales');
                    $salesId = $salesUser ? $salesUser->id : Auth::id();

                    $nextCustId = Contact::where('type', 'customer')->count() + 1;
                    $code = 'CUST-' . str_pad($nextCustId, 3, '0', STR_PAD_LEFT);
                    while (Contact::where('code', $code)->exists()) {
                        $nextCustId++;
                        $code = 'CUST-' . str_pad($nextCustId, 3, '0', STR_PAD_LEFT);
                    }

                    $convertedCustomer = Contact::create([
                        'type' => 'customer',
                        'code' => $code,
                        'name' => $lead->company_name,
                        'pic_name' => $lead->pic_name,
                        'sales_id' => $salesId,
                        'sales_name' => $salesName,
                        'lead_id' => $lead->id,
                        'lead_code' => $lead->code,
                        'phone' => $lead->pic_phone,
                        'email' => $lead->pic_email,
                        'billing_address' => $lead->address,
                        'shipping_address' => $lead->project_location ?? $lead->address,
                        'lead_source' => $lead->source,
                        'notes' => "Sales PIC: {$salesName}. Dikonversi otomatis dari CRM Lead Won: {$lead->code}.",
                        'is_active' => true,
                    ]);

                    $lead->converted_customer_id = $convertedCustomer->id;
                } else {
                    $convertedCustomer = $existing;
                    $existingUpdated = false;
                    if (empty($existing->sales_id) && !empty($lead->sales_id)) {
                        $existing->sales_id = $lead->sales_id;
                        $existing->sales_name = $lead->sales?->name;
                        $existingUpdated = true;
                    }
                    if (empty($existing->lead_id)) {
                        $existing->lead_id = $lead->id;
                        $existing->lead_code = $lead->code;
                        $existingUpdated = true;
                    }
                    if ($existingUpdated) {
                        $existing->save();
                    }
                }
            }

            $lead->save();

            // Record stage history
            LeadStageHistory::create([
                'lead_id' => $lead->id,
                'user_id' => Auth::id() ?? $lead->sales_id,
                'from_stage' => $oldStage,
                'to_stage' => $newStage,
                'change_reason' => $validated['change_reason'] ?? 'Perpindahan tahap pipeline melalui Kanban / Quick Action.',
                'created_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Tahapan {$lead->code} berhasil dipindahkan ke: " . (Lead::stageLabels()[$newStage] ?? $newStage) . ($convertedCustomer ? " dan otomatis terdaftar sebagai Customer ({$convertedCustomer->code})!" : ""),
                'from_stage' => $oldStage,
                'to_stage' => $newStage,
                'is_won' => ($newStage === 'won'),
                'customer' => $convertedCustomer,
                'lead' => $lead->fresh(['sales']),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Store an Activity for a Lead (Telepon, WhatsApp, Email, Brosur Terkirim, dll)
     */
    public function storeActivity(Request $request, $id)
    {
        $lead = Lead::findOrFail($id);
        $currentUser = Auth::user();
        $isAdmin = $this->checkIsAdmin($currentUser);

        if (!$isAdmin && $currentUser && $lead->sales_id != $currentUser->id) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $validated = $request->validate([
            'activity_type' => 'required|string|max:50',
            'activity_date' => 'required|date',
            'summary' => 'required|string|max:255',
            'communication_notes' => 'nullable|string',
            'attachment' => 'nullable|string|max:255',
        ]);

        $activity = LeadActivity::create([
            'lead_id' => $lead->id,
            'user_id' => Auth::id() ?? $lead->sales_id,
            'activity_type' => $validated['activity_type'],
            'activity_date' => $validated['activity_date'],
            'summary' => $validated['summary'],
            'communication_notes' => $validated['communication_notes'] ?? null,
            'attachment' => $validated['attachment'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Aktivitas berhasil dicatat di timeline!',
            'data' => $activity->load('user'),
        ]);
    }

    /**
     * Schedule a Follow-up Task for a Lead
     */
    public function storeFollowUp(Request $request, $id)
    {
        $lead = Lead::findOrFail($id);
        $currentUser = Auth::user();
        $isAdmin = $this->checkIsAdmin($currentUser);

        if (!$isAdmin && $currentUser && $lead->sales_id != $currentUser->id) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $validated = $request->validate([
            'follow_up_type' => 'required|string|max:50',
            'scheduled_at' => 'required|date',
            'purpose_notes' => 'required|string',
            'user_id' => 'nullable|exists:users,id',
        ]);

        if (!$isAdmin && $currentUser) {
            $validated['user_id'] = $currentUser->id;
        }

        $followUp = LeadFollowUp::create([
            'lead_id' => $lead->id,
            'user_id' => $validated['user_id'] ?? ($lead->sales_id ?? (Auth::id() ?? 1)),
            'follow_up_type' => $validated['follow_up_type'],
            'scheduled_at' => $validated['scheduled_at'],
            'purpose_notes' => $validated['purpose_notes'],
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Jadwal follow-up berhasil dibuat!',
            'data' => $followUp->load('user'),
        ]);
    }

    /**
     * Mark a Follow-up Task as Completed
     */
    public function completeFollowUp(Request $request, $id)
    {
        $followUp = LeadFollowUp::with('lead')->findOrFail($id);
        $currentUser = Auth::user();
        $isAdmin = $this->checkIsAdmin($currentUser);

        if (!$isAdmin && $currentUser && $followUp->user_id != $currentUser->id && optional($followUp->lead)->sales_id != $currentUser->id) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $validated = $request->validate([
            'result_notes' => 'required|string',
            // Optional next follow-up
            'next_follow_up_date' => 'nullable|date',
            'next_follow_up_type' => 'nullable|string|max:50',
            'next_follow_up_notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $followUp->update([
                'status' => 'completed',
                'completed_at' => now(),
                'result_notes' => $validated['result_notes'],
            ]);

            // Also record an activity entry in timeline for traceability
            LeadActivity::create([
                'lead_id' => $followUp->lead_id,
                'user_id' => Auth::id() ?? $followUp->user_id,
                'activity_type' => 'Follow-up',
                'activity_date' => now(),
                'summary' => "Follow-up {$followUp->follow_up_type} Selesai Dilakukan",
                'communication_notes' => $validated['result_notes'],
            ]);

            // If user wants to schedule next follow-up right away
            if (!empty($validated['next_follow_up_date'])) {
                LeadFollowUp::create([
                    'lead_id' => $followUp->lead_id,
                    'user_id' => $followUp->user_id,
                    'follow_up_type' => $validated['next_follow_up_type'] ?? 'Telepon',
                    'scheduled_at' => $validated['next_follow_up_date'],
                    'purpose_notes' => $validated['next_follow_up_notes'] ?? 'Follow-up lanjutan.',
                    'status' => 'pending',
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tugas follow-up berhasil diselesaikan dan dicatat ke histori!',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get Complete Details of a Lead (for Slide-over Drawer / Modal)
     */
    public function getLeadDetails(Request $request, $id)
    {
        $lead = Lead::with([
            'sales',
            'convertedCustomer',
            'convertedOrder',
            'activities.user',
            'followUps.user',
            'stageHistories.user'
        ])->findOrFail($id);

        $currentUser = Auth::user();
        $isAdmin = $this->checkIsAdmin($currentUser);

        if (!$isAdmin && $currentUser && $lead->sales_id != $currentUser->id) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak. Anda tidak memiliki izin untuk melihat data leads ini.'], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $lead,
        ]);
    }

    /**
     * Verify if contact matches lead company name to prevent mismatched customer assignment.
     */
    private function isCustomerMatchingLead($customer, $lead): bool
    {
        if (!$customer || empty($customer->name) || empty($lead->company_name)) {
            return false;
        }

        $leadName = trim($lead->company_name);
        $custName = trim($customer->name);

        if (strcasecmp($leadName, $custName) === 0) {
            return true;
        }

        // Strip common Indonesian legal entity prefixes/suffixes (PT, CV, UD, Tbk)
        $clean = function($str) {
            $s = preg_replace('/\b(pt|cv|ud|tbk)\b\.?/i', '', $str);
            $s = preg_replace('/[^a-zA-Z0-9\s]/', ' ', $s);
            return strtolower(trim(preg_replace('/\s+/', ' ', $s)));
        };

        $cleanLead = $clean($leadName);
        $cleanCust = $clean($custName);

        if (!empty($cleanLead) && !empty($cleanCust)) {
            if ($cleanLead === $cleanCust || strpos($cleanCust, $cleanLead) !== false || strpos($cleanLead, $cleanCust) !== false) {
                return true;
            }

            // Word overlap check for key brand words (length >= 4)
            $leadWords = array_values(array_filter(explode(' ', $cleanLead), fn($w) => strlen($w) >= 4));
            $custWords = array_values(array_filter(explode(' ', $cleanCust), fn($w) => strlen($w) >= 4));
            $common = array_intersect($leadWords, $custWords);
            if (count($common) > 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Ensure Lead is registered in Customer Master Data when generating a Quotation
     */
    public function prepareQuotation(Request $request, $id)
    {
        $lead = Lead::findOrFail($id);
        $currentUser = Auth::user();
        $isAdmin = $this->checkIsAdmin($currentUser);

        if (!$isAdmin && $currentUser && $lead->sales_id != $currentUser->id) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        // 1. If already linked to a customer, verify that it actually matches this lead
        if (!empty($lead->converted_customer_id)) {
            $existing = Contact::find($lead->converted_customer_id);
            if ($existing && $this->isCustomerMatchingLead($existing, $lead)) {
                return response()->json([
                    'success' => true,
                    'customer' => $existing,
                    'lead' => $lead,
                ]);
            } else {
                // Invalidate mismatched customer link (e.g. from wrong seeder or corrupted foreign key)
                $lead->converted_customer_id = null;
            }
        }

        // 2. Check if a customer with the same name already exists in contacts
        $matching = Contact::where('type', 'customer')
            ->where('name', $lead->company_name)
            ->first();

        if ($matching) {
            $matchingUpdated = false;
            if (empty($matching->sales_id) && !empty($lead->sales_id)) {
                $matching->sales_id = $lead->sales_id;
                $matching->sales_name = $lead->sales?->name;
                $matchingUpdated = true;
            }
            if (empty($matching->lead_id)) {
                $matching->lead_id = $lead->id;
                $matching->lead_code = $lead->code;
                $matchingUpdated = true;
            }
            if ($matchingUpdated) {
                $matching->save();
            }

            $lead->converted_customer_id = $matching->id;
            if (in_array($lead->pipeline_stage, ['new', 'contacted', 'qualified'])) {
                $lead->pipeline_stage = 'proposal';
            }
            $lead->save();

            return response()->json([
                'success' => true,
                'customer' => $matching,
                'lead' => $lead,
            ]);
        }

        // 3. Otherwise create new Customer in Master Data Contact
        DB::beginTransaction();
        try {
            $nextCustId = Contact::where('type', 'customer')->count() + 1;
            $code = 'CUST-' . str_pad($nextCustId, 3, '0', STR_PAD_LEFT);
            while (Contact::where('code', $code)->exists()) {
                $nextCustId++;
                $code = 'CUST-' . str_pad($nextCustId, 3, '0', STR_PAD_LEFT);
            }

            $salesUser = $lead->sales ?? \App\Models\User::find($lead->sales_id);
            $salesName = $salesUser ? $salesUser->name : (Auth::user()?->name ?? 'Sales');
            $salesId = $salesUser ? $salesUser->id : Auth::id();

            $customer = Contact::create([
                'type' => 'customer',
                'sales_id' => $salesId,
                'sales_name' => $salesName,
                'lead_id' => $lead->id,
                'lead_code' => $lead->code,
                'code' => $code,
                'name' => $lead->company_name,
                'pic_name' => $lead->pic_name,
                'phone' => $lead->pic_phone,
                'email' => $lead->pic_email,
                'billing_address' => $lead->address,
                'shipping_address' => $lead->project_location ?? $lead->address,
                'lead_source' => $lead->source,
                'notes' => "Dikonversi otomatis saat generate penawaran dari CRM Lead: {$lead->code}. " . ($lead->customer_notes ?? ''),
                'is_active' => true,
            ]);

            $lead->converted_customer_id = $customer->id;
            if (in_array($lead->pipeline_stage, ['new', 'contacted', 'qualified'])) {
                $lead->pipeline_stage = 'proposal';
            }
            $lead->save();

            // Record stage history
            LeadStageHistory::create([
                'lead_id' => $lead->id,
                'user_id' => Auth::id() ?? $lead->sales_id,
                'from_stage' => $lead->getOriginal('pipeline_stage') ?? 'new',
                'to_stage' => $lead->pipeline_stage,
                'change_reason' => "Leads didaftarkan ke Master Data Customer ({$customer->name}) untuk pembuatan penawaran harga.",
                'created_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'customer' => $customer,
                'lead' => $lead,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendaftarkan customer: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Convert Lead to Official Customer in contacts table
     */
    public function convertToCustomer(Request $request, $id)
    {
        $lead = Lead::findOrFail($id);
        $currentUser = Auth::user();
        $isAdmin = $this->checkIsAdmin($currentUser);

        if (!$isAdmin && $currentUser && $lead->sales_id != $currentUser->id) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        if (!empty($lead->converted_customer_id)) {
            $existing = Contact::find($lead->converted_customer_id);
            if ($existing && $this->isCustomerMatchingLead($existing, $lead)) {
                return response()->json([
                    'success' => true,
                    'message' => "Leads ini sudah terdaftar sebagai Customer: {$existing->name} ({$existing->code})",
                    'data' => $existing,
                ]);
            } else {
                $lead->converted_customer_id = null;
            }
        }

        DB::beginTransaction();
        try {
            // Generate Customer Code: CUST-XXX
            $nextCustId = Contact::where('type', 'customer')->count() + 1;
            $code = 'CUST-' . str_pad($nextCustId, 3, '0', STR_PAD_LEFT);

            $salesUser = $lead->sales ?? \App\Models\User::find($lead->sales_id);
            $salesName = $salesUser ? $salesUser->name : (Auth::user()?->name ?? 'Sales');
            $salesId = $salesUser ? $salesUser->id : Auth::id();

            $customer = Contact::create([
                'type' => 'customer',
                'sales_id' => $salesId,
                'sales_name' => $salesName,
                'lead_id' => $lead->id,
                'lead_code' => $lead->code,
                'code' => $code,
                'name' => $lead->company_name,
                'pic_name' => $lead->pic_name,
                'phone' => $lead->pic_phone,
                'email' => $lead->pic_email,
                'billing_address' => $lead->address,
                'shipping_address' => $lead->project_location ?? $lead->address,
                'lead_source' => $lead->source,
                'notes' => "Dikonversi dari CRM Lead: {$lead->code}. " . ($lead->customer_notes ?? ''),
                'is_active' => true,
            ]);

            $lead->converted_customer_id = $customer->id;
            $lead->pipeline_stage = 'won';
            $lead->converted_at = now();
            $lead->save();

            // Record stage history
            LeadStageHistory::create([
                'lead_id' => $lead->id,
                'user_id' => Auth::id() ?? $lead->sales_id,
                'from_stage' => $lead->pipeline_stage,
                'to_stage' => 'won',
                'change_reason' => "Leads berhasil dikonversi menjadi Customer Resmi: {$customer->name} ({$customer->code}).",
                'created_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Leads berhasil dikonversi menjadi Customer Resmi ({$customer->code})!",
                'data' => $customer,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete a Lead
     */
    public function deleteLead($id)
    {
        $lead = Lead::findOrFail($id);
        $currentUser = Auth::user();
        $isAdmin = $this->checkIsAdmin($currentUser);

        if (!$isAdmin && $currentUser && $lead->sales_id != $currentUser->id) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak. Hanya administrator atau pemilik leads yang dapat menghapus.'], 403);
        }

        $leadCode = $lead->code;
        $lead->delete();

        return response()->json([
            'success' => true,
            'message' => "Leads {$leadCode} berhasil dihapus!",
        ]);
    }

    /**
     * Check if a given user is an administrator
     */
    private function checkIsAdmin($user): bool
    {
        if (!$user) return false;
        return (method_exists($user, 'isAdmin') && $user->isAdmin()) 
            || in_array(strtolower($user->role ?? ''), ['admin', 'owner', 'administrator', 'superadmin']) 
            || (int)($user->usergrpid ?? 0) === 1;
    }

    /**
     * Helper to generate unique sequential Lead Code (e.g. LEAD-202610-001)
     */
    private function generateLeadCode(): string
    {
        $prefix = 'LEAD-' . date('Ym') . '-';
        $latest = Lead::where('code', 'like', $prefix . '%')->latest('id')->first();
        $nextNum = 1;
        if ($latest && preg_match('/-(\d+)$/', $latest->code, $matches)) {
            $nextNum = intval($matches[1]) + 1;
        }
        return $prefix . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
    }
}
