@extends('layouts.erp')

@section('title', 'CRM - Customer Relationship Management - Keylock Indonesia')

@section('breadcrumbs')
    <a href="{{ route('erp.index') }}" class="hover:text-slate-800">Home</a>
    <span>›</span>
    <span class="text-slate-800 font-semibold" x-text="lang === 'zh' ? '客户关系管理 (CRM)' : 'CRM & Pipeline Penjualan'">CRM & Pipeline Penjualan</span>
@endsection


@section('content')
<div x-data="erpCrmHub(
    '{{ $tab }}',
    '{{ $period }}',
    '{{ $salesFilter }}',
    @js($allLeads),
    @js($salesUsers),
    @js($catalogItems),
    @js($customers)
)">

    <!-- ========================================================================= -->
    <!-- MOBILE HEADER & TOOLBAR (PHASE 4D.1B - MODERN ERP STYLE MATCHING ORDERS)  -->
    <!-- ========================================================================= -->
    <div class="block md:hidden mb-4 space-y-3">
        <!-- 1. Header: Back button + Title & Subtitle + Tambah Leads -->
        <div class="flex items-center space-x-3">
            <a href="{{ route('erp.index') }}" 
               class="w-10 h-10 rounded-full border border-slate-200 bg-white flex items-center justify-center text-slate-700 hover:bg-slate-50 active:scale-95 transition shadow-2xs shrink-0"
               aria-label="Kembali ke Dashboard">
                <svg class="w-5 h-5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div class="min-w-0 flex-1">
                <h1 class="text-lg font-black text-[#17243B] tracking-tight leading-tight" x-text="lang === 'zh' ? '客户关系管理 (CRM)' : 'CRM & Pipeline Penjualan'">
                    CRM & Pipeline Penjualan
                </h1>
                <p class="text-xs text-slate-500 truncate" x-text="lang === 'zh' ? '全方位管理潜在客户与商机管道' : 'Kelola prospek dan pipeline penjualan armada'">
                    Kelola prospek dan pipeline penjualan armada
                </p>
            </div>
            <!-- Quick Tambah Leads Button on Mobile -->
            <button type="button" 
                    @click="openCreateLeadModal()" 
                    class="w-10 h-10 rounded-2xl bg-blue-600 text-white flex items-center justify-center shadow-sm hover:bg-blue-700 active:scale-95 transition shrink-0"
                    aria-label="Tambah Leads">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
            </button>
        </div>

        <!-- 2. Search Field + Filter Button (Matching Orders Page Style) -->
        <div class="flex items-center space-x-2">
            <div class="relative flex-1">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input type="text" 
                       x-model="searchKanban" 
                       :placeholder="lang === 'zh' ? '搜索公司、联系人、电话或产品...' : 'Cari perusahaan, PIC, nomor telepon, produk...'"
                       placeholder="Cari perusahaan, PIC, nomor telepon, produk..." 
                       class="w-full bg-white border border-slate-200/90 rounded-2xl pl-10 pr-4 py-2.5 text-xs text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-blue-500 shadow-2xs">
            </div>
            <button type="button" 
                    @click="mobileFilterOpen = !mobileFilterOpen"
                    class="w-10 h-10 rounded-2xl border border-slate-200/90 bg-white flex items-center justify-center text-slate-600 hover:text-slate-900 shadow-2xs transition active:scale-95 shrink-0"
                    :class="mobileFilterOpen || filterSource !== 'all' || filterDealType !== 'all' || (salesFilter && salesFilter !== 'all') ? 'border-blue-500 text-blue-600 bg-blue-50/50' : ''"
                    aria-label="Filter Lanjutan">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                </svg>
            </button>
        </div>

        <!-- Collapsible Mobile Advanced Filter Drawer -->
        <div x-show="mobileFilterOpen" 
             x-cloak 
             x-transition 
             class="bg-white p-3.5 rounded-2xl border border-slate-200 shadow-xs space-y-2.5">
            <div class="flex items-center justify-between pb-1 border-b border-slate-100">
                <span class="text-xs font-bold text-slate-700">Filter Pipeline CRM:</span>
                <button type="button" @click="resetFilters(); mobileFilterOpen = false" class="text-[11px] text-blue-600 font-semibold hover:underline">
                    Reset Filter
                </button>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-500 mb-1">Sumber Leads:</label>
                <select x-model="filterSource" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold text-slate-700 outline-none focus:border-blue-500">
                    <option value="all" x-text="lang === 'zh' ? '所有线索来源' : 'Semua Sumber Leads'">Semua Sumber Leads</option>
                    <option value="Website">Website</option>
                    <option value="WhatsApp">WhatsApp</option>
                    <option value="Referral">Referral</option>
                    <option value="Kunjungan">Kunjungan</option>
                    <option value="Telepon">Telepon</option>
                    <option value="LinkedIn">LinkedIn</option>
                    <option value="Email">Email</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-500 mb-1">Jenis Kebutuhan:</label>
                <select x-model="filterDealType" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold text-slate-700 outline-none focus:border-blue-500">
                    <option value="all" x-text="lang === 'zh' ? '所有交易种类' : 'Semua Transaksi'">Semua Transaksi</option>
                    <option value="rental" x-text="lang === 'zh' ? '设备租赁' : 'Rental / Sewa'">Rental / Sewa</option>
                    <option value="sales" x-text="lang === 'zh' ? '设备销售' : 'Penjualan Unit'">Penjualan Unit</option>
                    <option value="both" x-text="lang === 'zh' ? '租赁与销售' : 'Keduanya (Rental & Jual)'">Keduanya (Rental & Jual)</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-500 mb-1">Periode:</label>
                <select x-model="period" @change="applyFilters()" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold text-slate-700 outline-none focus:border-blue-500">
                    <option value="this_month">Bulan Ini ({{ date('F Y') }})</option>
                    <option value="this_quarter">Kuartal Ini (Q{{ ceil(date('n')/3) }})</option>
                    <option value="this_year">Tahun Ini ({{ date('Y') }})</option>
                    <option value="all">Semua Waktu</option>
                </select>
            </div>

            @if($isAdmin)
            <div>
                <label class="block text-[11px] font-bold text-slate-500 mb-1">Sales Person:</label>
                <select x-model="salesFilter" @change="applyFilters()" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold text-slate-700 outline-none focus:border-blue-500">
                    <option value="all" x-text="lang === 'zh' ? '所有销售人员' : 'Semua Sales'">Semua Sales</option>
                    <template x-for="s in salesUsers" :key="s.id">
                        <option :value="s.id" x-text="s.name"></option>
                    </template>
                </select>
            </div>
            @endif

            <div class="pt-1 flex items-center justify-between border-t border-slate-100">
                <span class="text-xs font-bold text-slate-600">Mode Tampilan:</span>
                <div class="inline-flex items-center bg-slate-100 p-1 rounded-xl border border-slate-200 text-xs font-bold">
                    <button type="button" 
                            @click="pipelineViewMode = 'kanban'" 
                            :class="pipelineViewMode === 'kanban' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-500 hover:text-slate-800'"
                            class="px-3 py-1 rounded-lg transition flex items-center space-x-1">
                        <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path></svg>
                        <span>Kanban</span>
                    </button>
                    <button type="button" 
                            @click="pipelineViewMode = 'table'" 
                            :class="pipelineViewMode === 'table' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-500 hover:text-slate-800'"
                            class="px-3 py-1 rounded-lg transition flex items-center space-x-1">
                        <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                        <span>Tabel</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- DESKTOP HEADER & CONTROLS (VISIBLE ON SCREENS >= MD)                      -->
    <!-- ========================================================================= -->
    <div class="hidden md:block">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
            <div>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight mt-1" x-text="lang === 'zh' ? '客户关系与商机管理 (CRM)' : 'CRM & Pipeline Penjualan'">
                    CRM & Pipeline Penjualan
                </h2>
                <p class="text-xs text-slate-500 mt-1" x-text="lang === 'zh' ? '全方位管理潜在客户、销售阶段管线、沟通活动履历、画册寄送以及跟进转成交全流程。' : 'Kelola calon pelanggan, pipeline penjualan, aktivitas komunikasi, follow-up, hingga konversi order.'">
                    Kelola calon pelanggan, pipeline penjualan, aktivitas komunikasi, follow-up, hingga konversi order.
                </p>
            </div>

            <!-- Top Right Action Controls -->
            <div class="flex items-center flex-wrap sm:flex-nowrap gap-2.5 shrink-0 self-start lg:self-center">
                <!-- Filter Period -->
                <div class="flex items-center bg-white border border-slate-200/90 rounded-full px-3 py-1.5 shadow-2xs text-xs shrink-0">
                    <svg class="w-3.5 h-3.5 text-slate-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <select x-model="period" @change="applyFilters()" class="bg-transparent font-bold text-slate-800 text-xs outline-none cursor-pointer">
                        <option value="this_month">Bulan Ini ({{ date('F Y') }})</option>
                        <option value="this_quarter">Kuartal Ini (Q{{ ceil(date('n')/3) }})</option>
                        <option value="this_year">Tahun Ini ({{ date('Y') }})</option>
                        <option value="all">Semua Waktu</option>
                    </select>
                </div>

                <!-- Filter Sales Rep -->
                @if($isAdmin)
                <div class="flex items-center bg-white border border-slate-200/90 rounded-full px-3 py-1.5 shadow-2xs text-xs shrink-0">
                    <svg class="w-3.5 h-3.5 text-slate-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    <select x-model="salesFilter" @change="applyFilters()" class="bg-transparent font-bold text-slate-800 text-xs outline-none cursor-pointer max-w-[140px] truncate">
                        <option value="all" x-text="lang === 'zh' ? '所有销售人员' : 'Semua Sales'">Semua Sales</option>
                        <template x-for="s in salesUsers" :key="s.id">
                            <option :value="s.id" x-text="s.name"></option>
                        </template>
                    </select>
                </div>
                @else
                <div class="flex items-center bg-white border border-slate-200/90 rounded-full px-3.5 py-1.5 shadow-2xs text-xs shrink-0">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2 animate-pulse"></span>
                    <span class="text-slate-400 mr-1" x-text="lang === 'zh' ? '业务员:' : 'Sales:'">Sales:</span>
                    <span class="font-extrabold text-slate-800">{{ auth()->user()->name }}</span>
                </div>
                @endif

                <!-- Button Tambah Leads Baru -->
                <button type="button" 
                        @click="openCreateLeadModal()" 
                        class="bg-blue-600 hover:bg-blue-700 text-white font-extrabold px-4 py-2 rounded-full shadow-md hover:shadow-lg text-xs transition flex items-center space-x-2 active:scale-95 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    <span x-text="lang === 'zh' ? '新建线索' : 'Tambah Leads'">Tambah Leads</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs font-bold flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- MAIN PIPELINE PENJUALAN (KANBAN BOARD & TABLE VIEW)                       -->
    <!-- ========================================================================= -->
    <div class="space-y-6">

        <!-- Desktop Kanban Filters & Search Bar (Visible on md+) -->
        <div class="hidden md:flex bg-white p-3.5 rounded-3xl border border-slate-200/80 shadow-xs flex-col md:flex-row items-center justify-between gap-3">
            <div class="relative w-full md:w-96 flex items-center">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <input type="text" 
                       x-model="searchKanban"
                       :placeholder="lang === 'zh' ? '搜索公司、联系人、电话或意向产品...' : 'Cari perusahaan, PIC, nomor telepon, produk...'"
                       placeholder="Cari perusahaan, PIC, nomor telepon, produk..."
                       class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs font-medium text-slate-800 placeholder:text-slate-400 outline-none focus:border-amber-400 focus:bg-white transition">
            </div>

            <div class="flex flex-wrap items-center gap-2.5 w-full md:w-auto text-xs justify-end">
                <!-- Filter Sumber Leads -->
                <select x-model="filterSource" class="bg-slate-50 border border-slate-200/90 rounded-2xl px-3.5 py-2 font-bold text-slate-700 text-xs outline-none focus:border-amber-400 focus:bg-white transition shadow-2xs">
                    <option value="all" x-text="lang === 'zh' ? '所有线索来源' : 'Semua Sumber Leads'">Semua Sumber Leads</option>
                    <option value="Website">Website</option>
                    <option value="WhatsApp">WhatsApp</option>
                    <option value="Referral">Referral</option>
                    <option value="Kunjungan">Kunjungan</option>
                    <option value="Telepon">Telepon</option>
                    <option value="LinkedIn">LinkedIn</option>
                    <option value="Email">Email</option>
                </select>

                <!-- Filter Jenis Kebutuhan -->
                <select x-model="filterDealType" class="bg-slate-50 border border-slate-200/90 rounded-2xl px-3.5 py-2 font-bold text-slate-700 text-xs outline-none focus:border-amber-400 focus:bg-white transition shadow-2xs">
                    <option value="all" x-text="lang === 'zh' ? '所有交易种类' : 'Semua Transaksi'">Semua Transaksi</option>
                    <option value="rental" x-text="lang === 'zh' ? '设备租赁' : 'Rental / Sewa'">Rental / Sewa</option>
                    <option value="sales" x-text="lang === 'zh' ? '设备销售' : 'Penjualan Unit'">Penjualan Unit</option>
                    <option value="both" x-text="lang === 'zh' ? '租赁与销售' : 'Keduanya (Rental & Jual)'">Keduanya (Rental & Jual)</option>
                </select>

                <button type="button" @click="resetFilters()" class="px-3 py-1.5 rounded-xl text-xs font-bold text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition">
                    <span x-text="lang === 'zh' ? '重置' : 'Reset'">Reset</span>
                </button>

                <!-- Mode Tampilan: Kanban vs Tabel -->
                <div class="inline-flex items-center bg-slate-100 p-1 rounded-2xl border border-slate-200 text-xs font-bold">
                    <button type="button" 
                            @click="pipelineViewMode = 'kanban'" 
                            :class="pipelineViewMode === 'kanban' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-500 hover:text-slate-800'"
                            class="px-3 py-1.5 rounded-xl transition flex items-center space-x-1.5">
                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path></svg>
                        <span x-text="lang === 'zh' ? '看板视图' : 'Kanban Board'">Kanban Board</span>
                    </button>
                    <button type="button" 
                            @click="pipelineViewMode = 'table'" 
                            :class="pipelineViewMode === 'table' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-500 hover:text-slate-800'"
                            class="px-3 py-1.5 rounded-xl transition flex items-center space-x-1.5">
                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                        <span x-text="lang === 'zh' ? '表格视图' : 'Tabel Data'">Tabel Data</span>
                    </button>
                </div>
            </div>
        </div>

        <style>
            .crm-lead-card,
            .crm-lead-card * {
                -webkit-user-select: none !important;
                user-select: none !important;
                -webkit-user-drag: none !important;
                -khtml-user-drag: none !important;
                -moz-user-drag: none !important;
                -o-user-drag: none !important;
                user-drag: none !important;
                touch-action: none;
            }
            .crm-lead-card:active,
            .crm-lead-card:focus,
            .crm-lead-card:hover {
                opacity: 1 !important;
            }
            .cursor-grabbing {
                opacity: 1 !important;
            }
        </style>

        <!-- The 7 Columns Kanban Board with Drag & Drop -->
        <div x-show="pipelineViewMode === 'kanban'" 
             x-ref="kanbanScrollContainer"
             class="overflow-x-auto pb-6">
            <div class="flex items-stretch gap-4 min-w-[1750px]">
                
                @php
                    $stagesConfig = [
                        'new' => [
                            'label' => 'Prospek Baru', 
                            'zh' => '新线索', 
                            'dot_color' => '#475569',
                            'bg_column' => 'bg-slate-100/80',
                            'border_column' => 'border-slate-300/80',
                        ],
                        'contacted' => [
                            'label' => 'Sudah Dihubungi', 
                            'zh' => '已初次联系', 
                            'dot_color' => '#0284c7',
                            'bg_column' => 'bg-sky-50/70',
                            'border_column' => 'border-sky-200/80',
                        ],
                        'qualified' => [
                            'label' => 'Qualified / Tertarik', 
                            'zh' => '已合格/意向', 
                            'dot_color' => '#f59e0b',
                            'bg_column' => 'bg-amber-50/70',
                            'border_column' => 'border-amber-200/80',
                        ],
                        'proposal' => [
                            'label' => 'Penawaran', 
                            'zh' => '已发报价', 
                            'dot_color' => '#2d5d4b',
                            'bg_column' => 'bg-emerald-50/70',
                            'border_column' => 'border-emerald-200/80',
                        ],
                        'negotiation' => [
                            'label' => 'Negosiasi', 
                            'zh' => '商务谈判', 
                            'dot_color' => '#c84924',
                            'bg_column' => 'bg-orange-50/70',
                            'border_column' => 'border-orange-200/80',
                        ],
                        'won' => [
                            'label' => 'Won (Berhasil)', 
                            'zh' => '赢单成交', 
                            'dot_color' => '#1c1d21',
                            'bg_column' => 'bg-slate-100/90',
                            'border_column' => 'border-slate-300/80',
                        ],
                        'lost' => [
                            'label' => 'Lost (Gagal)', 
                            'zh' => '输单流失', 
                            'dot_color' => '#881337',
                            'bg_column' => 'bg-rose-50/70',
                            'border_column' => 'border-rose-200/80',
                        ],
                    ];
                @endphp

                @foreach($stagesConfig as $sKey => $cfg)
                    <div class="w-80 shrink-0 {{ $cfg['bg_column'] }} rounded-3xl border {{ $cfg['border_column'] }} shadow-2xs flex flex-col min-h-[720px] max-h-[860px] overflow-hidden transition-all duration-300"
                         data-stage-column="{{ $sKey }}"
                         :class="{ 'ring-4 ring-offset-2 scale-[1.01] bg-white shadow-xl': hoveredStage === '{{ $sKey }}' }"
                         :style="hoveredStage === '{{ $sKey }}' ? { ringColor: '{{ $cfg['dot_color'] }}', borderColor: '{{ $cfg['dot_color'] }}' } : {}">
                        
                        <!-- Column Header matching Reference Style -->
                        <div class="bg-white/95 backdrop-blur-xs px-4 py-3 border-b border-slate-200/80 flex items-center justify-between select-none">
                            <div class="flex items-center space-x-2 min-w-0">
                                <span class="w-2.5 h-2.5 rounded-full shrink-0 shadow-2xs" style="background-color: {{ $cfg['dot_color'] }}"></span>
                                <h4 class="text-xs font-black text-slate-800 tracking-tight truncate uppercase" x-text="lang === 'zh' ? '{{ $cfg['zh'] }}' : '{{ $cfg['label'] }}'">
                                    {{ $cfg['label'] }}
                                </h4>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black font-mono bg-slate-100 text-slate-600 border border-slate-200 shrink-0"
                                      x-text="getStageLeads('{{ $sKey }}').length">
                                    0
                                </span>
                            </div>
                            <div class="flex items-center space-x-2 shrink-0">
                                <span class="text-xs font-black font-mono text-slate-900" x-text="formatRupiahCompact(getStageSum('{{ $sKey }}'))"></span>
                                <button type="button" 
                                        @click="openCreateLeadModal('{{ $sKey }}')"
                                        :title="lang === 'zh' ? '在此阶段新建线索' : 'Tambah Prospek ke Tahap Ini'"
                                        title="Tambah Prospek ke Tahap Ini"
                                        class="w-6 h-6 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center text-xs font-black transition active:scale-95 shadow-2xs">
                                    +
                                </button>
                            </div>
                        </div>

                        <!-- Cards Container -->
                        <div class="p-3 space-y-3 overflow-y-auto pr-2 flex-1 min-h-[500px]">
                            
                            <!-- Dynamic Drop Placeholder -->
                            <div x-show="hoveredStage === '{{ $sKey }}' && draggedLead && draggedLead.pipeline_stage !== '{{ $sKey }}'"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
                                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                 class="p-3.5 rounded-2xl border-2 border-dashed flex items-center justify-center space-x-2 text-xs font-black shadow-inner my-1 transition-all duration-200"
                                 :style="{ borderColor: '{{ $cfg['dot_color'] }}', backgroundColor: '{{ $cfg['dot_color'] }}18', color: '{{ $cfg['dot_color'] }}' }">
                                <svg class="w-4 h-4 animate-bounce shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                </svg>
                                <span x-text="lang === 'zh' ? '在此释放卡片' : 'Lepaskan di sini'">Lepaskan di sini</span>
                            </div>

                            <template x-for="lead in getStageLeads('{{ $sKey }}')" :key="lead.id">
                                <div>
                                    <!-- The Card: Root element stays in DOM so pointer capture & drag never aborts -->
                                    <div @pointerdown="handleCardPointerDown($event, lead)"
                                         @dragstart.prevent
                                         draggable="false"
                                         class="crm-lead-card p-4 rounded-2xl border cursor-grab active:cursor-grabbing group relative select-none shadow-2xs hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200"
                                         :class="isPointerDragging && draggedLead && draggedLead.id === lead.id ? 'border-2 border-dashed border-slate-300/80 bg-slate-200/40 text-slate-400 shadow-none' : [
                                             getCardTheme(lead).bg,
                                             getCardTheme(lead).textColor,
                                             getCardTheme(lead).borderColor
                                         ]"
                                         :style="isPointerDragging && draggedLead && draggedLead.id === lead.id ? { minHeight: (cardHeight || 160) + 'px', opacity: '1' } : {
                                             backgroundColor: getCardTheme(lead).bgHex,
                                             borderColor: getCardTheme(lead).borderHex,
                                             color: getCardTheme(lead).textHex,
                                             opacity: '1'
                                         }">
                                        
                                        <!-- 1. Placeholder slot inside column when this card is being dragged -->
                                        <div x-show="isPointerDragging && draggedLead && draggedLead.id === lead.id"
                                             class="p-6 flex flex-col items-center justify-center space-y-1 text-slate-400 select-none my-1 pointer-events-none">
                                            <svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                            </svg>
                                            <span class="text-[11px] font-bold" x-text="lang === 'zh' ? '正在移动卡片...' : 'Memindahkan kartu...'">Memindahkan kartu...</span>
                                        </div>

                                        <!-- 2. The Normal Card Content -->
                                        <div x-show="!isPointerDragging || !draggedLead || draggedLead.id !== lead.id">
                                            <!-- Card Header: Code, Date & Priority -->
                                        <div class="flex items-center justify-between gap-2 mb-2">
                                            <div class="flex items-center space-x-1.5 truncate">
                                                <span class="text-[10px] font-mono font-black px-2 py-0.5 rounded-lg border shadow-2xs"
                                                      :class="getCardTheme(lead).pillBg" 
                                                      x-text="lead.code"></span>
                                                <span class="text-[10px] font-mono font-medium opacity-80 truncate" 
                                                      x-text="lead.expected_closing_date ? ('Due: ' + formatDateShort(lead.expected_closing_date)) : formatDateShort(lead.entry_date)"></span>
                                            </div>
                                            <span class="text-[9px] font-black uppercase tracking-wider px-2 py-0.5 rounded-full border shadow-2xs shrink-0"
                                                  :class="getPriorityBadgeClass(lead.priority, getCardTheme(lead).isDark)"
                                                  x-text="lead.priority === 'high' ? (lang === 'zh' ? '紧急' : 'TINGGI') : (lead.priority === 'normal' ? (lang === 'zh' ? '标准' : 'NORMAL') : (lang === 'zh' ? '较低' : 'RENDAH'))">
                                            </span>
                                        </div>

                                        <!-- Company Name (Clickable) -->
                                        <h5 class="text-sm font-black tracking-tight leading-snug line-clamp-2 mt-2 cursor-pointer hover:underline"
                                            @click="openLeadDetail(lead.id)"
                                            x-text="lead.company_name">
                                        </h5>
                                        
                                        <!-- Industry / Deal Type Subtitle -->
                                        <div class="flex items-center justify-between text-[11px] font-medium mt-1" :class="getCardTheme(lead).subTextColor">
                                            <span class="truncate" x-text="lead.industry || 'Industri Prospek'"></span>
                                            <span class="font-bold shrink-0 ml-1 opacity-90" x-text="getDealTypeLabel(lead.deal_type)"></span>
                                        </div>

                                        <!-- PIC & Quick WhatsApp -->
                                        <div class="flex items-center justify-between text-xs py-1.5 px-2.5 rounded-xl mt-2.5 border shadow-2xs" :class="getCardTheme(lead).pillBg">
                                            <div class="flex items-center space-x-1.5 truncate min-w-0">
                                                <svg class="w-3.5 h-3.5 shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                                <span class="truncate text-[11px] font-bold" x-text="lead.pic_name || 'PIC'"></span>
                                            </div>
                                            <template x-if="lead.pic_phone">
                                                <a :href="`https://wa.me/${cleanPhone(lead.pic_phone)}`" target="_blank" @click.stop title="Chat WhatsApp" 
                                                   class="inline-flex items-center space-x-1 bg-emerald-500 hover:bg-emerald-600 text-white px-2 py-0.5 rounded-lg transition text-[10px] font-black shrink-0 ml-1 shadow-2xs">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                                    <span>WA</span>
                                                </a>
                                            </template>
                                        </div>

                                        <!-- Product Interest & Qty Card -->
                                        <div class="mt-2 text-[11px] flex items-center justify-between px-2.5 py-1.5 rounded-xl border shadow-2xs" :class="getCardTheme(lead).pillBg">
                                            <span class="truncate font-bold" x-text="lead.product_interest || 'Kebutuhan Belum Spesifik'"></span>
                                            <span class="font-black font-mono shrink-0 ml-2" x-text="`${lead.quantity || 1} Unit`"></span>
                                        </div>

                                        <!-- Estimated Value & Move Stage Select -->
                                        <div class="flex items-center justify-between mt-3 pt-2.5 border-t" :class="getCardTheme(lead).isDark ? 'border-white/15' : 'border-black/10'">
                                            <div>
                                                <span class="text-[9px] font-black uppercase tracking-wider block opacity-75" x-text="lang === 'zh' ? '预估金额' : 'ESTIMASI NILAI'">ESTIMASI NILAI</span>
                                                <span class="text-xs font-black font-mono" :class="getCardTheme(lead).priceColor" x-text="formatRupiah(lead.estimated_value)"></span>
                                            </div>

                                            <div class="flex items-center space-x-1">
                                                <select @change="changeStageDirect(lead.id, $event.target.value)" @click.stop
                                                        class="text-[10px] font-bold rounded-xl px-2 py-1 outline-none border transition cursor-pointer shadow-2xs"
                                                        :class="getCardTheme(lead).selectBg">
                                                    <option value="" disabled selected x-text="lang === 'zh' ? '变更阶段' : 'Pindah Tahap'">Pindah Tahap</option>
                                                    <option value="new" :selected="lead.pipeline_stage === 'new'" x-text="lang === 'zh' ? '1. 新建线索' : '1. Prospek Baru'">1. Prospek Baru</option>
                                                    <option value="contacted" :selected="lead.pipeline_stage === 'contacted'" x-text="lang === 'zh' ? '2. 已联系' : '2. Sudah Dihubungi'">2. Sudah Dihubungi</option>
                                                    <option value="qualified" :selected="lead.pipeline_stage === 'qualified'" x-text="lang === 'zh' ? '3. 合格意向' : '3. Qualified / Tertarik'">3. Qualified / Tertarik</option>
                                                    <option value="proposal" :selected="lead.pipeline_stage === 'proposal'" x-text="lang === 'zh' ? '4. 方案报价' : '4. Penawaran'">4. Penawaran</option>
                                                    <option value="negotiation" :selected="lead.pipeline_stage === 'negotiation'" x-text="lang === 'zh' ? '5. 商务谈判' : '5. Negosiasi'">5. Negosiasi</option>
                                                    <option value="won" :selected="lead.pipeline_stage === 'won'" x-text="lang === 'zh' ? '6. 赢单成交' : '6. Won (Deal)'">6. Won (Deal)</option>
                                                    <option value="lost" :selected="lead.pipeline_stage === 'lost'" x-text="lang === 'zh' ? '战败流失' : 'Lost (Gagal)'">Lost (Gagal)</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Quick Action Buttons Row (Unified Style Across ERP) -->
                                        <div class="mt-2.5 pt-2 border-t flex items-center justify-between gap-1.5" :class="getCardTheme(lead).isDark ? 'border-white/15' : 'border-black/10'">
                                            <!-- 1. Lihat Detail -->
                                            <button type="button" 
                                                    @click.stop="openLeadDetail(lead.id)" 
                                                    :title="lang === 'zh' ? '查看线索详情' : 'Lihat Detail Prospek'" 
                                                    title="Lihat Detail Prospek" 
                                                    class="h-8 flex-1 flex items-center justify-center rounded-xl transition active:scale-95 shadow-2xs border"
                                                    :class="getCardTheme(lead).btnBg">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            </button>

                                            <!-- 2. Edit Leads -->
                                            <button type="button" 
                                                    @click.stop="openEditLeadModal(lead)" 
                                                    :title="lang === 'zh' ? '编辑线索资料' : 'Edit Data Leads'" 
                                                    title="Edit Data Leads" 
                                                    class="h-8 flex-1 flex items-center justify-center rounded-xl transition active:scale-95 shadow-2xs border"
                                                    :class="getCardTheme(lead).btnBg">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                            </button>

                                            <!-- 3. Catat Aktivitas -->
                                            <button type="button" 
                                                    @click.stop="openActivityModal(lead)" 
                                                    :title="lang === 'zh' ? '记录沟通活动' : 'Catat Aktivitas Komunikasi'" 
                                                    title="Catat Aktivitas Komunikasi" 
                                                    class="h-8 flex-1 flex items-center justify-center rounded-xl transition active:scale-95 shadow-2xs border"
                                                    :class="getCardTheme(lead).btnBg">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                            </button>

                                            <!-- 4. Jadwalkan Follow-up -->
                                            <button type="button" 
                                                    @click.stop="openFollowUpModal(lead)" 
                                                    :title="lang === 'zh' ? '预约跟进日程' : 'Jadwalkan Follow-up'" 
                                                    title="Jadwalkan Follow-up" 
                                                    class="h-8 flex-1 flex items-center justify-center rounded-xl transition active:scale-95 shadow-2xs border"
                                                    :class="getCardTheme(lead).btnBg">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            </button>

                                            <!-- 5. Buat Penawaran Resmi -->
                                            <button type="button" 
                                                    @click.stop="createQuotationFromLead(lead)" 
                                                    :title="lang === 'zh' ? '创建正式报价单' : 'Buat Penawaran Resmi (Quotation)'" 
                                                    title="Buat Penawaran Resmi (Quotation)" 
                                                    class="h-8 flex-1 flex items-center justify-center rounded-xl transition active:scale-95 shadow-2xs border"
                                                    :class="getCardTheme(lead).btnBg">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            </button>
                                        </div>

                                        </div>
                                    </div>
                                </div>
                            </template>

                            <!-- Empty State Dropzone -->
                            <template x-if="getStageLeads('{{ $sKey }}').length === 0">
                                <div class="p-8 text-center text-slate-400 text-xs font-semibold border-2 border-dashed border-slate-300/80 rounded-2xl bg-white/70 flex flex-col items-center justify-center space-y-2">
                                    <svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                    <span x-text="lang === 'zh' ? '暂无数据，拖拽卡片至此' : 'Tarik atau pindahkan kartu ke sini'">Tarik atau pindahkan kartu ke sini</span>
                                </div>
                            </template>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>

        <!-- Floating Draggable Lead Card (Pure DOM, 100% Solid & Opaque, Exactly like Image 1) -->
        <template x-teleport="body">
            <div x-show="isPointerDragging && draggedLead"
                 class="fixed pointer-events-none z-[999999] select-none rounded-2xl border p-4 will-change-transform"
                 :style="{
                     left: dragX + 'px',
                     top: dragY + 'px',
                     width: cardWidth + 'px',
                     backgroundColor: draggedLead ? getCardTheme(draggedLead).bgHex : '#334155',
                     borderColor: draggedLead ? getCardTheme(draggedLead).borderHex : '#475569',
                     color: draggedLead ? getCardTheme(draggedLead).textHex : '#ffffff',
                     transform: `rotate(${dragRotation}deg) scale(1.04)`,
                     boxShadow: '0 25px 45px -8px rgba(0, 0, 0, 0.45), 0 12px 20px -6px rgba(0, 0, 0, 0.25)',
                     transition: 'transform 0.12s cubic-bezier(0.2, 0.8, 0.2, 1), background-color 0.22s ease, border-color 0.22s ease',
                     opacity: '1'
                 }">
                
                <template x-if="draggedLead">
                    <div>
                        <!-- Card Header: Code, Date & Priority -->
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <div class="flex items-center space-x-1.5 truncate">
                                <span class="text-[10px] font-mono font-black px-2 py-0.5 rounded-lg border shadow-2xs"
                                      :class="getCardTheme(draggedLead).pillBg" 
                                      x-text="draggedLead.code"></span>
                                <span class="text-[10px] font-mono font-medium opacity-80 truncate" 
                                      x-text="draggedLead.expected_closing_date ? ('Due: ' + formatDateShort(draggedLead.expected_closing_date)) : formatDateShort(draggedLead.entry_date)"></span>
                            </div>
                            <span class="text-[9px] font-black uppercase tracking-wider px-2 py-0.5 rounded-full border shadow-2xs shrink-0"
                                  :class="getPriorityBadgeClass(draggedLead.priority, getCardTheme(draggedLead).isDark)"
                                  x-text="draggedLead.priority === 'high' ? (lang === 'zh' ? '紧急' : 'TINGGI') : (draggedLead.priority === 'normal' ? (lang === 'zh' ? '标准' : 'NORMAL') : (lang === 'zh' ? '较低' : 'RENDAH'))">
                            </span>
                        </div>

                        <!-- Company Name -->
                        <h5 class="text-sm font-black tracking-tight leading-snug line-clamp-2 mt-2"
                            x-text="draggedLead.company_name">
                        </h5>
                        
                        <!-- Industry / Deal Type Subtitle -->
                        <div class="flex items-center justify-between text-[11px] font-medium mt-1" :class="getCardTheme(draggedLead).subTextColor">
                            <span class="truncate" x-text="draggedLead.industry || 'Industri Prospek'"></span>
                            <span class="font-bold shrink-0 ml-1 opacity-90" x-text="getDealTypeLabel(draggedLead.deal_type)"></span>
                        </div>

                        <!-- PIC & Quick WhatsApp -->
                        <div class="flex items-center justify-between text-xs py-1.5 px-2.5 rounded-xl mt-2.5 border shadow-2xs" :class="getCardTheme(draggedLead).pillBg">
                            <div class="flex items-center space-x-1.5 truncate min-w-0">
                                <svg class="w-3.5 h-3.5 shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                <span class="truncate text-[11px] font-bold" x-text="draggedLead.pic_name || 'PIC'"></span>
                            </div>
                            <span class="inline-flex items-center space-x-1 bg-emerald-500 text-white px-2 py-0.5 rounded-lg text-[10px] font-black shrink-0 ml-1 shadow-2xs">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                <span>WA</span>
                            </span>
                        </div>

                        <!-- Product Interest & Qty Card -->
                        <div class="mt-2 text-[11px] flex items-center justify-between px-2.5 py-1.5 rounded-xl border shadow-2xs" :class="getCardTheme(draggedLead).pillBg">
                            <span class="truncate font-bold" x-text="draggedLead.product_interest || 'Kebutuhan Belum Spesifik'"></span>
                            <span class="font-black font-mono shrink-0 ml-2" x-text="`${draggedLead.quantity || 1} Unit`"></span>
                        </div>

                        <!-- Estimated Value & Target Stage -->
                        <div class="flex items-center justify-between mt-3 pt-2.5 border-t" :class="getCardTheme(draggedLead).isDark ? 'border-white/15' : 'border-black/10'">
                            <div>
                                <span class="text-[9px] font-black uppercase tracking-wider block opacity-75" x-text="lang === 'zh' ? '预估金额' : 'ESTIMASI NILAI'">ESTIMASI NILAI</span>
                                <span class="text-xs font-black font-mono" :class="getCardTheme(draggedLead).priceColor" x-text="formatRupiah(draggedLead.estimated_value)"></span>
                            </div>
                            <div class="flex items-center space-x-1">
                                <span class="text-[10px] font-black uppercase tracking-wider rounded-xl px-2.5 py-1 border shadow-2xs" 
                                      :class="getCardTheme(draggedLead).selectBg"
                                      x-text="getStageLabel(hoveredStage || draggedLead.pipeline_stage)"></span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </template>

        <!-- Table View inside Tab 2 (Pipeline Penjualan) -->
        <div x-show="pipelineViewMode === 'table'" class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-black text-slate-900" x-text="lang === 'zh' ? '全量商机线索明细表' : 'Tabel Database Semua Leads (CRM)'">
                        Tabel Database Semua Leads (CRM)
                    </h3>
                    <p class="text-xs text-slate-500">Mencakup riwayat calon pelanggan, kebutuhan spesifik, estimasi closing, dan aksi penjualan.</p>
                </div>
                <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-800 text-xs font-bold font-mono" x-text="`${filteredTableLeads.length} leads`">
                    {{ $totalLeads }} leads
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs whitespace-nowrap">
                    <thead class="bg-slate-50 text-slate-600 text-[11px] uppercase tracking-wider font-bold border-b border-slate-200">
                        <tr>
                            <th class="py-3.5 px-4 font-black">Kode Leads</th>
                            <th class="py-3.5 px-6 font-black">Perusahaan</th>
                            <th class="py-3.5 px-6 font-black">Nama PIC</th>
                            <th class="py-3.5 px-4 font-black">No. Telepon</th>
                            <th class="py-3.5 px-4 font-black">Sumber</th>
                            <th class="py-3.5 px-6 font-black">Kebutuhan Unit</th>
                            <th class="py-3.5 px-5 text-right font-black">Nilai Peluang</th>
                            <th class="py-3.5 px-4 text-center font-black">Status Pipeline</th>
                            <th class="py-3.5 px-4 font-black">Sales PIC</th>
                            <th class="py-3.5 px-4 font-black">Follow-up</th>
                            <th class="py-3.5 px-4 text-center font-black">Aksi Cepat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-800 font-medium">
                        <template x-for="lead in filteredTableLeads" :key="lead.id">
                            <tr class="hover:bg-amber-50/20 transition">
                                <!-- Kode Leads -->
                                <td class="py-3.5 px-4 font-mono font-bold text-slate-900">
                                    <span class="hover:text-blue-600 cursor-pointer" @click="openLeadDetail(lead.id)" x-text="lead.code"></span>
                                </td>

                                <!-- Perusahaan -->
                                <td class="py-3.5 px-6">
                                    <p class="font-extrabold text-slate-900 hover:text-blue-600 cursor-pointer" @click="openLeadDetail(lead.id)" x-text="lead.company_name"></p>
                                    <span class="text-[10px] text-slate-400 block" x-text="lead.industry || 'Umum'"></span>
                                </td>

                                <!-- PIC -->
                                <td class="py-3.5 px-6">
                                    <p class="font-bold text-slate-800" x-text="lead.pic_name"></p>
                                    <span class="text-[10px] text-slate-400 block" x-text="lead.pic_title || 'PIC'"></span>
                                </td>

                                <!-- Phone -->
                                <td class="py-3.5 px-4 font-mono">
                                    <template x-if="lead.pic_phone">
                                        <a :href="`https://wa.me/${cleanPhone(lead.pic_phone)}`" target="_blank" class="text-emerald-600 hover:underline flex items-center space-x-1">
                                            <span x-text="lead.pic_phone"></span>
                                        </a>
                                    </template>
                                </td>

                                <!-- Sumber -->
                                <td class="py-3.5 px-4">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700" x-text="lead.source"></span>
                                </td>

                                <!-- Kebutuhan -->
                                <td class="py-3.5 px-6">
                                    <p class="font-bold text-slate-900" x-text="lead.product_interest || '-'"></p>
                                    <span class="text-[10px] text-slate-500 font-medium" x-text="`${lead.quantity} Unit • ${lead.deal_type}`"></span>
                                </td>

                                <!-- Nilai Peluang -->
                                <td class="py-3.5 px-5 text-right font-black text-slate-900 font-mono" x-text="formatRupiah(lead.estimated_value)"></td>

                                <!-- Status Pipeline -->
                                <td class="py-3.5 px-4 text-center">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black"
                                          :class="getStageBadgeClass(lead.pipeline_stage)"
                                          x-text="getStageLabel(lead.pipeline_stage)">
                                    </span>
                                </td>

                                <!-- Sales PIC -->
                                <td class="py-3.5 px-4">
                                    <span class="font-bold text-slate-800 text-xs" x-text="getSalesName(lead.sales_id)"></span>
                                </td>

                                <!-- Follow-up Berikutnya -->
                                <td class="py-3.5 px-4 text-[11px]">
                                    <template x-if="lead.pending_follow_ups && lead.pending_follow_ups.length > 0">
                                        <div class="space-y-0.5">
                                            <span class="px-1.5 py-0.2 rounded text-[9px] font-black bg-blue-100 text-blue-800" x-text="lead.pending_follow_ups[0].follow_up_type"></span>
                                            <span class="block text-slate-600 font-mono text-[10px]" x-text="formatDateShort(lead.pending_follow_ups[0].scheduled_at)"></span>
                                        </div>
                                    </template>
                                    <template x-if="!lead.pending_follow_ups || lead.pending_follow_ups.length === 0">
                                        <span class="text-slate-400 italic">Belum ada</span>
                                    </template>
                                </td>

                                <!-- Aksi Cepat (Standard Size Icon Buttons) -->
                                <td class="py-3 px-3 text-center">
                                    <div class="flex items-center justify-center space-x-1.5">
                                        <!-- Lihat Detail -->
                                        <button type="button" 
                                                @click="openLeadDetail(lead.id)"
                                                :title="lang === 'zh' ? '查看线索详情' : 'Lihat Detail Prospek'"
                                                title="Lihat Detail Prospek"
                                                class="w-8 h-8 flex items-center justify-center rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition active:scale-95 shadow-2xs">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </button>

                                        <!-- Edit Leads -->
                                        <button type="button" 
                                                @click="openEditLeadModal(lead)"
                                                :title="lang === 'zh' ? '编辑线索资料' : 'Edit Leads'"
                                                title="Edit Leads"
                                                class="w-8 h-8 flex items-center justify-center rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200/70 transition active:scale-95 shadow-2xs">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        </button>

                                        <!-- Catat Aktivitas -->
                                        <button type="button" 
                                                @click="openActivityModal(lead)"
                                                :title="lang === 'zh' ? '记录沟通活动' : 'Catat Aktivitas Komunikasi'"
                                                title="Catat Aktivitas Komunikasi"
                                                class="w-8 h-8 flex items-center justify-center rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-600 border border-blue-200/70 transition active:scale-95 shadow-2xs">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                        </button>

                                        <!-- Jadwalkan Follow-up -->
                                        <button type="button" 
                                                @click="openFollowUpModal(lead)"
                                                :title="lang === 'zh' ? '预约跟进日程' : 'Jadwalkan Follow-up'"
                                                title="Jadwalkan Follow-up"
                                                class="w-8 h-8 flex items-center justify-center rounded-xl bg-purple-50 hover:bg-purple-100 text-purple-600 border border-purple-200/70 transition active:scale-95 shadow-2xs">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </button>

                                        <!-- Buat Penawaran / Convert -->
                                        <button type="button" 
                                                @click="createQuotationFromLead(lead)"
                                                :title="lang === 'zh' ? '创建正式报价单' : 'Buat Penawaran Resmi (Quotation)'"
                                                title="Buat Penawaran Resmi (Quotation)"
                                                class="w-8 h-8 flex items-center justify-center rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200/70 transition active:scale-95 shadow-2xs">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>

                        <template x-if="filteredTableLeads.length === 0">
                            <tr>
                                <td colspan="11" class="py-12 text-center text-slate-400">
                                    <p class="font-bold text-sm">Tidak ada leads yang sesuai kriteria pencarian.</p>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- 7. SLIDE-OVER DETAIL LEADS DRAWER (MODUL DETAIL LENGKAP - SEKSI 6)        -->
    <!-- ========================================================================= -->
    <div x-show="detailDrawerOpen" 
         x-cloak 
         style="display: none;"
         class="fixed inset-0 z-50 overflow-hidden">
        
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" 
             @click="detailDrawerOpen = false"></div>

        <div class="fixed inset-y-0 right-0 pl-10 max-w-full flex">
            <div class="w-screen max-w-2xl bg-white shadow-2xl border-l border-slate-200 flex flex-col animate-in slide-in-from-right duration-200">
                
                <!-- Drawer Top Header (Section 6.A) -->
                <div class="p-6 bg-slate-900 text-white flex items-start justify-between">
                    <div class="space-y-1 min-w-0">
                        <div class="flex items-center space-x-2">
                            <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-amber-400 text-slate-950 font-mono" x-text="activeLead?.code"></span>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-slate-800 text-amber-300 border border-slate-700" x-text="getStageLabel(activeLead?.pipeline_stage)"></span>
                        </div>
                        <h3 class="text-xl font-black tracking-tight text-white truncate" x-text="activeLead?.company_name"></h3>
                        <p class="text-xs text-slate-400 flex items-center space-x-2">
                            <span x-text="`${lang === 'zh' ? '销售负责人: ' : 'Sales: '}${activeLead?.sales?.name || 'Unassigned'}`"></span>
                            <span>•</span>
                            <span x-text="`${lang === 'zh' ? '线索来源: ' : 'Sumber: '}${activeLead?.source || '-'}`"></span>
                        </p>
                    </div>

                    <button type="button" @click="detailDrawerOpen = false" class="text-slate-400 hover:text-white transition p-1">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Drawer Action Buttons Strip -->
                <div class="px-6 py-3.5 bg-slate-100 border-b border-slate-200 flex flex-wrap items-center gap-2.5 text-xs">
                    <button type="button" @click="openActivityModal(activeLead)" class="px-3.5 py-2 rounded-xl bg-white hover:bg-blue-600 hover:text-white border border-slate-200 text-slate-800 font-black transition flex items-center space-x-2 shadow-2xs active:scale-95">
                        <svg class="w-4 h-4 text-blue-600 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                        <span x-text="lang === 'zh' ? '+ 记录活动' : '+ Catat Aktivitas'">+ Catat Aktivitas</span>
                    </button>

                    <button type="button" @click="openFollowUpModal(activeLead)" class="px-3.5 py-2 rounded-xl bg-white hover:bg-purple-600 hover:text-white border border-slate-200 text-slate-800 font-black transition flex items-center space-x-2 shadow-2xs active:scale-95">
                        <svg class="w-4 h-4 text-purple-600 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span x-text="lang === 'zh' ? '+ 安排跟进' : '+ Jadwal Follow-up'">+ Jadwal Follow-up</span>
                    </button>

                    <button type="button" @click="openEditLeadModal(activeLead)" class="px-3.5 py-2 rounded-xl bg-white hover:bg-yellow-400 hover:text-slate-950 border border-slate-200 text-slate-800 font-black transition flex items-center space-x-2 shadow-2xs active:scale-95">
                        <svg class="w-4 h-4 text-amber-500 group-hover:text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                        <span x-text="lang === 'zh' ? '编辑线索' : 'Edit Leads'">Edit Leads</span>
                    </button>

                    <button type="button" @click="createQuotationFromLead(activeLead)" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black transition flex items-center space-x-2 shadow-2xs active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span x-text="lang === 'zh' ? '创建报价单' : 'Buat Penawaran'">Buat Penawaran</span>
                    </button>

                    <template x-if="!activeLead?.converted_customer_id">
                        <button type="button" @click="convertLeadToCustomer(activeLead?.id)" class="px-3.5 py-2 rounded-xl bg-amber-400 hover:bg-yellow-300 text-slate-950 font-black transition shadow-2xs active:scale-95" x-text="lang === 'zh' ? '转为正式客户' : 'Jadikan Customer'">
                            Jadikan Customer
                        </button>
                    </template>
                </div>

                <!-- Drawer Scrollable Body -->
                <div class="flex-1 overflow-y-auto p-6 space-y-6 text-xs">

                    <!-- Section 6.D: Tahapan Pipeline (Interactive Visual Stepper) -->
                    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200">
                        <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block mb-2" x-text="lang === 'zh' ? '销售漏斗阶段进展' : 'PROGRES TAHAPAN PIPELINE'">PROGRES TAHAPAN PIPELINE</span>
                        
                        <div class="flex items-center justify-between gap-1 overflow-x-auto pb-1 text-[10px]">
                            @php
                                $stepKeys = ['new', 'contacted', 'qualified', 'proposal', 'negotiation', 'won', 'lost'];
                            @endphp
                            @foreach($stepKeys as $stgKey)
                                <button type="button" 
                                        @click="changeStageDirect(activeLead.id, '{{ $stgKey }}')"
                                        :class="activeLead?.pipeline_stage === '{{ $stgKey }}' ? 'bg-amber-400 text-slate-950 font-black shadow-xs ring-2 ring-amber-300' : 'bg-white text-slate-600 hover:bg-slate-200 font-bold'"
                                        class="px-2.5 py-1.5 rounded-lg border border-slate-200 transition text-center shrink-0"
                                        x-text="getStageStepLabel('{{ $stgKey }}')">
                                    {{ $stgKey }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Section 6.B: Informasi Perusahaan & PIC -->
                    <div class="bg-white p-4 rounded-2xl border border-slate-200 space-y-3">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                            <span class="font-black text-slate-900 uppercase tracking-wide text-xs" x-text="lang === 'zh' ? '公司与联系人信息' : 'Informasi Perusahaan & PIC'">Informasi Perusahaan & PIC</span>
                            
                            <template x-if="activeLead?.converted_customer_id">
                                <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[10px] font-black" x-text="lang === 'zh' ? '已转正式客户' : 'Customer Terdaftar'">
                                    Customer Terdaftar
                                </span>
                            </template>
                        </div>

                        <div class="grid grid-cols-2 gap-3 text-slate-700">
                            <div>
                                <span class="text-slate-400 block text-[10px]" x-text="lang === 'zh' ? '公司名称:' : 'Perusahaan:'">Perusahaan:</span>
                                <span class="font-extrabold text-slate-900" x-text="activeLead?.company_name"></span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[10px]" x-text="lang === 'zh' ? '所属行业:' : 'Bidang Usaha:'">Bidang Usaha:</span>
                                <span class="font-bold text-slate-800" x-text="activeLead?.industry || '-'"></span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[10px]" x-text="lang === 'zh' ? '主要联系人:' : 'PIC Utama:'">PIC Utama:</span>
                                <span class="font-extrabold text-slate-900" x-text="`${activeLead?.pic_name || '-'} (${activeLead?.pic_title || 'PIC'})`"></span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[10px]" x-text="lang === 'zh' ? '联系方式:' : 'Kontak PIC:'">Kontak PIC:</span>
                                <a :href="`https://wa.me/${cleanPhone(activeLead?.pic_phone)}`" target="_blank" class="font-bold text-emerald-600 hover:underline" x-text="activeLead?.pic_phone"></a>
                                <span class="text-slate-400 text-[11px] block" x-text="activeLead?.pic_email || ''"></span>
                            </div>
                            <div class="col-span-2">
                                <span class="text-slate-400 block text-[10px]" x-text="lang === 'zh' ? '办公/工厂地址:' : 'Alamat:'">Alamat:</span>
                                <span class="font-medium text-slate-800" x-text="activeLead?.address || '-'"></span>
                            </div>
                            <template x-if="activeLead?.website">
                                <div class="col-span-2">
                                    <span class="text-slate-400 block text-[10px]" x-text="lang === 'zh' ? '官方网址:' : 'Website:'">Website:</span>
                                    <a :href="activeLead?.website" target="_blank" class="text-blue-600 hover:underline font-bold" x-text="activeLead?.website"></a>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Section 6.C: Kebutuhan Customer & Nilai Transaksi -->
                    <div class="bg-white p-4 rounded-2xl border border-slate-200 space-y-3">
                        <span class="font-black text-slate-900 uppercase tracking-wide text-xs block border-b border-slate-100 pb-2" x-text="lang === 'zh' ? '客户需求与交易明细' : 'Kebutuhan Customer & Transaksi'">
                            Kebutuhan Customer & Transaksi
                        </span>

                        <div class="grid grid-cols-2 gap-3 text-slate-700">
                            <div>
                                <span class="text-slate-400 block text-[10px]" x-text="lang === 'zh' ? '需求类型:' : 'Jenis Kebutuhan:'">Jenis Kebutuhan:</span>
                                <span class="font-extrabold text-slate-900" x-text="getDealTypeLabel(activeLead?.deal_type)"></span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[10px]" x-text="lang === 'zh' ? '预估金额:' : 'Estimasi Nilai:'">Estimasi Nilai:</span>
                                <span class="font-black text-base text-slate-900" x-text="formatRupiah(activeLead?.estimated_value)"></span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[10px]" x-text="lang === 'zh' ? '意向产品:' : 'Produk Diminati:'">Produk Diminati:</span>
                                <span class="font-bold text-slate-900" x-text="activeLead?.product_interest || '-'"></span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[10px]" x-text="lang === 'zh' ? '数量与说明:' : 'Jumlah & Keterangan:'">Jumlah & Keterangan:</span>
                                <span class="font-bold text-slate-800" x-text="getLeadQuantityDescription(activeLead)"></span>
                            </div>
                            <div class="col-span-2">
                                <span class="text-slate-400 block text-[10px]" x-text="lang === 'zh' ? '规格要求:' : 'Spesifikasi:'">Spesifikasi:</span>
                                <span class="font-medium text-slate-800" x-text="activeLead?.specification || '-'"></span>
                            </div>
                            <div class="col-span-2">
                                <span class="text-slate-400 block text-[10px]" x-text="lang === 'zh' ? '项目/交货地点:' : 'Lokasi Proyek / Pengiriman:'">Lokasi Proyek / Pengiriman:</span>
                                <span class="font-medium text-slate-800" x-text="activeLead?.project_location || '-'"></span>
                            </div>

                            <!-- Multi-Product Items Breakdown (If Present) -->
                            <template x-if="activeLead?.items && activeLead.items.length > 0">
                                <div class="col-span-2 space-y-2 pt-2 border-t border-slate-100">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block" x-text="lang === 'zh' ? '意向设备明细清单:' : 'Rincian Unit Diminati:'">Rincian Unit Diminati:</span>
                                    <div class="space-y-2">
                                        <template x-for="(it, i) in activeLead.items" :key="i">
                                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/90 text-xs flex items-center justify-between gap-3 shadow-2xs">
                                                <div class="min-w-0">
                                                    <div class="flex items-center space-x-1.5 mb-0.5">
                                                        <template x-if="it.deal_type === 'sales' || activeLead.deal_type === 'sales'">
                                                            <span class="px-1.5 py-0.5 rounded bg-blue-100 text-blue-800 text-[9px] font-black uppercase" x-text="lang === 'zh' ? '购买' : 'Beli'">Beli</span>
                                                        </template>
                                                        <template x-if="it.deal_type === 'rental' || ((activeLead.deal_type === 'rental' || activeLead.deal_type === 'both') && it.deal_type !== 'sales')">
                                                            <span class="px-1.5 py-0.5 rounded bg-amber-100 text-amber-800 text-[9px] font-black uppercase" x-text="lang === 'zh' ? '租赁' : 'Sewa'">Sewa</span>
                                                        </template>
                                                        <p class="font-extrabold text-slate-900 truncate" x-text="it.name"></p>
                                                    </div>
                                                    <p class="text-[10px] text-slate-500 truncate" x-text="it.specification || '-'"></p>
                                                </div>
                                                <div class="text-right shrink-0">
                                                    <span class="font-black text-slate-900" x-text="`${it.quantity || 1} ${lang === 'zh' ? '台' : 'Unit'}`"></span>
                                                    <template x-if="(it.deal_type === 'rental' || (activeLead.deal_type !== 'sales' && it.deal_type !== 'sales')) && it.duration">
                                                        <span class="text-[10px] text-slate-500 block" x-text="`${it.duration} ${it.duration_unit || (lang === 'zh' ? '月' : 'Bulan')}`"></span>
                                                    </template>
                                                    <template x-if="it.unit_price">
                                                        <span class="text-[10px] font-mono font-bold text-emerald-700 block" x-text="formatRupiah(it.unit_price)"></span>
                                                    </template>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>

                            <div class="col-span-2">
                                <span class="text-slate-400 block text-[10px]" x-text="lang === 'zh' ? '客户需求备注:' : 'Catatan Kebutuhan Customer:'">Catatan Kebutuhan Customer:</span>
                                <p class="text-slate-700 italic bg-slate-50 p-2.5 rounded-xl border border-slate-100 mt-1" x-text="activeLead?.customer_notes || (lang === 'zh' ? '暂无特殊备注。' : 'Tidak ada catatan.')"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Section 6.E: Timeline Aktivitas Kronologis -->
                    <div class="bg-white p-4 rounded-2xl border border-slate-200 space-y-3">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                            <span class="font-black text-slate-900 uppercase tracking-wide text-xs" x-text="lang === 'zh' ? '沟通活动历史记录' : 'Histori Aktivitas Komunikasi'">Histori Aktivitas Komunikasi</span>
                            <button type="button" @click="openActivityModal(activeLead)" class="text-blue-600 hover:underline font-bold text-xs" x-text="lang === 'zh' ? '+ 添加活动' : 'Tambah'">
                                Tambah
                            </button>
                        </div>

                        <div class="space-y-3 pt-1">
                            <template x-for="act in (activeLead?.activities || [])" :key="act.id">
                                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 space-y-1">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-2">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-black bg-blue-100 text-blue-800" x-text="act.activity_type"></span>
                                            <span class="font-extrabold text-slate-900" x-text="act.summary"></span>
                                        </div>
                                        <span class="text-[10px] text-slate-400 font-mono" x-text="formatDateShort(act.activity_date)"></span>
                                    </div>
                                    <p class="text-slate-700 text-xs" x-text="act.communication_notes"></p>
                                    <template x-if="act.attachment">
                                        <div class="pt-1 flex items-center space-x-1 text-[11px] text-blue-600 font-bold">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                            <span x-text="act.attachment"></span>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            <template x-if="!activeLead?.activities || activeLead.activities.length === 0">
                                <p class="text-slate-400 text-center py-3 text-xs italic" x-text="lang === 'zh' ? '暂无沟通记录。' : 'Belum ada aktivitas tercatat.'">Belum ada aktivitas tercatat.</p>
                            </template>
                        </div>
                    </div>

                    <!-- Section 6.F: Jadwal Follow-up Prospek Ini -->
                    <div class="bg-white p-4 rounded-2xl border border-slate-200 space-y-3">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                            <span class="font-black text-slate-900 uppercase tracking-wide text-xs" x-text="lang === 'zh' ? '跟进任务日程' : 'Jadwal Tugas Follow-up'">Jadwal Tugas Follow-up</span>
                            <button type="button" @click="openFollowUpModal(activeLead)" class="text-purple-600 hover:underline font-bold text-xs" x-text="lang === 'zh' ? '+ 安排跟进' : '+ Jadwalkan'">
                                + Jadwalkan
                            </button>
                        </div>

                        <div class="space-y-2 pt-1">
                            <template x-for="fu in (activeLead?.follow_ups || [])" :key="fu.id">
                                <div class="p-3 rounded-xl border flex items-start justify-between gap-3"
                                     :class="fu.status === 'completed' ? 'bg-slate-50 border-slate-200 opacity-70' : 'bg-purple-50/40 border-purple-200'">
                                    <div class="space-y-1">
                                        <div class="flex items-center space-x-2">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-black"
                                                  :class="fu.status === 'completed' ? 'bg-emerald-100 text-emerald-800' : 'bg-purple-100 text-purple-800'"
                                                  x-text="fu.status === 'completed' ? (lang === 'zh' ? '已完成' : 'SELESAI') : (lang === 'zh' ? '待执行' : 'BELUM DILAKUKAN')">
                                            </span>
                                            <span class="font-bold text-slate-800" x-text="fu.follow_up_type"></span>
                                            <span class="text-[10px] text-slate-400 font-mono" x-text="formatDateShort(fu.scheduled_at)"></span>
                                        </div>
                                        <p class="text-xs text-slate-700" x-text="fu.purpose_notes"></p>
                                        <template x-if="fu.result_notes">
                                            <p class="text-xs text-emerald-700 bg-emerald-50 p-2 rounded-lg font-medium" x-text="`${lang === 'zh' ? '跟进结果: ' : 'Hasil: '}${fu.result_notes}`"></p>
                                        </template>
                                    </div>

                                    <template x-if="fu.status === 'pending'">
                                        <button type="button" 
                                                @click="openCompleteFollowUpModal(fu.id, activeLead.company_name)"
                                                class="shrink-0 px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs shadow-xs"
                                                x-text="lang === 'zh' ? '标记完成' : 'Selesai'">
                                            Selesai
                                        </button>
                                    </template>
                                </div>
                            </template>

                            <template x-if="!activeLead?.follow_ups || activeLead.follow_ups.length === 0">
                                <p class="text-slate-400 text-center py-3 text-xs italic" x-text="lang === 'zh' ? '暂无跟进任务。' : 'Belum ada tugas follow-up.'">Belum ada tugas follow-up.</p>
                            </template>
                        </div>
                    </div>

                    <!-- Riwayat Perpindahan Status Pipeline -->
                    <div class="bg-white p-4 rounded-2xl border border-slate-200 space-y-2">
                        <span class="font-black text-slate-900 uppercase tracking-wide text-xs block border-b border-slate-100 pb-2" x-text="lang === 'zh' ? '阶段变更历史记录' : 'Histori Perubahan Status Pipeline'">
                            Histori Perubahan Status Pipeline
                        </span>

                        <div class="space-y-2">
                            <template x-for="hist in (activeLead?.stage_histories || [])" :key="hist.id">
                                <div class="text-[11px] p-2 bg-slate-50 rounded-lg flex items-center justify-between">
                                    <div>
                                        <span class="font-bold text-slate-900" x-text="`${getStageLabel(hist.from_stage)} ➔ ${getStageLabel(hist.to_stage)}`"></span>
                                        <span class="text-slate-400 block text-[10px]" x-text="hist.change_reason || (lang === 'zh' ? '阶段变更' : 'Perubahan status')"></span>
                                    </div>
                                    <span class="text-slate-400 font-mono text-[10px]" x-text="formatDateShort(hist.created_at)"></span>
                                </div>
                            </template>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 8. MODAL TAMBAH & EDIT LEADS (FORMULIR LENGKAP - SEKSI 5)                 -->
    <!-- ========================================================================= -->
    <div x-show="leadModalOpen" 
         x-cloak 
         style="display: none;"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        
        <div @click.away="leadModalOpen = false" 
             class="bg-white w-full max-w-3xl rounded-3xl shadow-2xl border border-slate-200 overflow-hidden my-8 animate-in zoom-in-95 duration-150">
            
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center font-black">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-900" x-text="leadModalMode === 'create' ? (lang === 'zh' ? '新建销售线索 (Leads)' : 'Tambah Calon Prospek (Leads Baru)') : (lang === 'zh' ? '编辑线索资料' : 'Edit Data Leads')"></h3>
                        <p class="text-[11px] text-slate-500" x-text="lang === 'zh' ? '填写潜在客户资料、联系人信息以及设备需求。' : 'Isi data calon customer, kontak PIC, dan kebutuhan unit.'"></p>
                    </div>
                </div>
                <button type="button" @click="leadModalOpen = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form @submit.prevent="submitLeadForm" class="p-6 space-y-6 text-xs max-h-[75vh] overflow-y-auto">
                
                <!-- Grup 1: Informasi Prospek -->
                <div class="space-y-4">
                    <h4 class="font-black text-slate-900 uppercase tracking-wider text-[11px] pb-1 border-b border-slate-100 flex items-center space-x-2">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        <span x-text="lang === 'zh' ? '1. 公司 / 客户基本信息' : '1. Informasi Perusahaan / Prospek'">1. Informasi Perusahaan / Prospek</span>
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '公司名称 *' : 'Nama Perusahaan *'">Nama Perusahaan *</label>
                            <input type="text" x-model="leadForm.company_name" required :placeholder="lang === 'zh' ? '例: PT Surya Semesta Internusa' : 'Contoh: PT Surya Semesta Internusa'" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-900 font-semibold outline-none focus:border-yellow-400 focus:bg-white">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '所属行业' : 'Bidang Usaha'">Bidang Usaha</label>
                            <input type="text" x-model="leadForm.industry" :placeholder="lang === 'zh' ? '例: 物流仓储 / 食品制造' : 'Contoh: Logistik / Manufaktur Makanan'" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-900 font-semibold outline-none focus:border-yellow-400 focus:bg-white">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '线索来源 *' : 'Sumber Leads *'">Sumber Leads *</label>
                            <select x-model="leadForm.source" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-900 font-semibold outline-none focus:border-yellow-400 focus:bg-white">
                                <option value="Website" x-text="lang === 'zh' ? '官方网站 (Website)' : 'Website'">Website</option>
                                <option value="WhatsApp" x-text="lang === 'zh' ? 'WhatsApp咨询' : 'WhatsApp'">WhatsApp</option>
                                <option value="Referral" x-text="lang === 'zh' ? '客户推荐 (Referral)' : 'Referral / Rekomendasi'">Referral / Rekomendasi</option>
                                <option value="Kunjungan" x-text="lang === 'zh' ? '地推拜访开拓' : 'Kunjungan Lapangan'">Kunjungan Lapangan</option>
                                <option value="Telepon" x-text="lang === 'zh' ? '热线电话 (Telepon)' : 'Telepon Hotline'">Telepon Hotline</option>
                                <option value="LinkedIn" x-text="lang === 'zh' ? '领英 (LinkedIn)' : 'LinkedIn'">LinkedIn</option>
                                <option value="Email" x-text="lang === 'zh' ? '邮件咨询 (Email)' : 'Email Inquiry'">Email Inquiry</option>
                                <option value="Lainnya" x-text="lang === 'zh' ? '其他渠道 (Lainnya)' : 'Lainnya'">Lainnya</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '销售负责人 (Sales PIC)' : 'Sales Penanggung Jawab'">Sales Penanggung Jawab</label>
                            @if($isAdmin)
                            <select x-model="leadForm.sales_id" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-900 font-semibold outline-none focus:border-yellow-400 focus:bg-white">
                                <template x-for="s in salesUsers" :key="s.id">
                                    <option :value="s.id" x-text="s.name"></option>
                                </template>
                            </select>
                            @else
                            <div class="w-full bg-slate-100 border border-slate-200 rounded-xl px-3 py-2 text-slate-800 font-bold flex items-center justify-between">
                                <span>{{ auth()->user()->name }}</span>
                                <span class="text-[10px] text-slate-500 font-normal" x-text="lang === 'zh' ? '当前销售' : 'Sales Anda'">Sales Anda</span>
                            </div>
                            @endif
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '线索录入日期 *' : 'Tanggal Masuk Leads *'">Tanggal Masuk Leads *</label>
                            <input type="date" x-model="leadForm.entry_date" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-900 font-semibold outline-none focus:border-yellow-400 focus:bg-white">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '官方网址 (选填)' : 'Website (Opsional)'">Website (Opsional)</label>
                            <input type="text" x-model="leadForm.website" placeholder="https://..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-900 font-semibold outline-none focus:border-yellow-400 focus:bg-white">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '办公/工厂地址' : 'Alamat Kantor / Pabrik'">Alamat Kantor / Pabrik</label>
                            <textarea x-model="leadForm.address" rows="2" :placeholder="lang === 'zh' ? '客户详细地址...' : 'Alamat lengkap calon customer...'" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-900 font-semibold outline-none focus:border-yellow-400 focus:bg-white"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Grup 2: Informasi PIC -->
                <div class="space-y-4">
                    <h4 class="font-black text-slate-900 uppercase tracking-wider text-[11px] pb-1 border-b border-slate-100 flex items-center space-x-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                        <span x-text="lang === 'zh' ? '2. 联系人信息 (PIC)' : '2. Informasi PIC (Person In Charge)'">2. Informasi PIC (Person In Charge)</span>
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '主要联系人姓名 *' : 'Nama PIC Utama *'">Nama PIC Utama *</label>
                            <input type="text" x-model="leadForm.pic_name" required :placeholder="lang === 'zh' ? '例: Bpk. Bambang Sudiro' : 'Contoh: Bpk. Bambang Sudiro'" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-900 font-semibold outline-none focus:border-yellow-400 focus:bg-white">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '联系人职务' : 'Jabatan PIC'">Jabatan PIC</label>
                            <input type="text" x-model="leadForm.pic_title" :placeholder="lang === 'zh' ? '例: 仓储经理 / 采购主管' : 'Contoh: Warehouse Manager / Purchasing'" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-900 font-semibold outline-none focus:border-yellow-400 focus:bg-white">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '联系电话 / WhatsApp *' : 'Nomor Telepon / WhatsApp *'">Nomor Telepon / WhatsApp *</label>
                            <input type="text" x-model="leadForm.pic_phone" required placeholder="0812xxxxxxxx" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-900 font-semibold outline-none focus:border-yellow-400 focus:bg-white">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '联系人邮箱' : 'Email PIC'">Email PIC</label>
                            <input type="email" x-model="leadForm.pic_email" placeholder="pic@perusahaan.com" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-900 font-semibold outline-none focus:border-yellow-400 focus:bg-white">
                        </div>
                    </div>
                </div>

                <!-- Grup 3: Kebutuhan Bisnis -->
                <div class="space-y-4">
                    <h4 class="font-black text-slate-900 uppercase tracking-wider text-[11px] pb-1 border-b border-slate-100 flex items-center space-x-2">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        <span x-text="lang === 'zh' ? '3. 重型设备需求与交易明细' : '3. Kebutuhan Alat Berat & Transaksi'">3. Kebutuhan Alat Berat & Transaksi</span>
                    </h4>

                    <div class="space-y-4">
                        <!-- Repeater Section Header -->
                        <div class="flex items-center justify-between pt-1">
                            <div>
                                <span class="font-bold text-slate-800 text-xs block" x-text="lang === 'zh' ? '意向设备明细清单 (支持添加多款产品)' : 'Rincian Unit Diminati (Bisa Lebih dari 1 Produk)'">Rincian Unit Diminati (Bisa Lebih dari 1 Produk)</span>
                                <span class="text-[11px] text-slate-400" x-text="lang === 'zh' ? '选择每款设备是租赁或购买，系统将根据所选自动汇总需求类型。' : 'Pilih apakah tiap unit disewa atau dibeli. Sistem akan otomatis mendeteksi jenis kebutuhan.'">Pilih apakah tiap unit disewa atau dibeli. Sistem akan otomatis mendeteksi jenis kebutuhan.</span>
                            </div>
                            <button type="button" @click="addLeadItem()" class="text-amber-600 hover:text-amber-700 font-bold text-xs flex items-center space-x-1 px-3 py-1.5 rounded-xl bg-amber-50 hover:bg-amber-100 border border-amber-200 transition active:scale-95 shadow-2xs">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                                <span x-text="lang === 'zh' ? '+ 添加设备产品' : 'Tambah Baris Unit'">Tambah Baris Unit</span>
                            </button>
                        </div>

                        <!-- Dynamic Items Repeater Container -->
                        <div class="space-y-3">
                            <template x-for="(row, idx) in leadForm.items" :key="idx">
                                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 space-y-3 relative shadow-2xs">
                                    
                                    <!-- Card Header: Unit Number + Deal Type Toggle [Sewa vs Beli] + Delete Button -->
                                    <div class="flex items-center justify-between pb-2 border-b border-slate-200/80">
                                        <div class="flex items-center space-x-2">
                                            <span class="text-xs font-black text-slate-700" x-text="`${lang === 'zh' ? '设备 #' : 'Unit #'}${idx + 1}`"></span>
                                            <span class="text-slate-300">•</span>
                                            <span class="text-[10px] font-bold text-slate-500" x-text="lang === 'zh' ? '需求类型:' : 'Tipe Kebutuhan:'">Tipe Kebutuhan:</span>
                                            <div class="inline-flex p-0.5 bg-slate-200/80 rounded-xl">
                                                <button type="button" 
                                                        @click="setRowDealType(row, 'rental')"
                                                        :class="(row.deal_type || 'rental') === 'rental' ? 'bg-amber-500 text-white shadow-xs font-black' : 'text-slate-600 hover:text-slate-900 font-bold'"
                                                        class="px-2.5 py-0.5 rounded-lg text-[10px] transition"
                                                        x-text="lang === 'zh' ? '租赁 (Sewa)' : 'Sewa (Rental)'">
                                                    Sewa (Rental)
                                                </button>
                                                <button type="button" 
                                                        @click="setRowDealType(row, 'sales')"
                                                        :class="row.deal_type === 'sales' ? 'bg-blue-600 text-white shadow-xs font-black' : 'text-slate-600 hover:text-slate-900 font-bold'"
                                                        class="px-2.5 py-0.5 rounded-lg text-[10px] transition"
                                                        x-text="lang === 'zh' ? '购买 (Beli)' : 'Beli (Jual)'">
                                                    Beli (Jual)
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Delete row button -->
                                        <button type="button" @click="removeLeadItem(idx)" class="w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 inline-flex items-center justify-center transition shadow-2xs active:scale-95" :title="lang === 'zh' ? '删除此设备' : 'Hapus Baris Unit'">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>

                                    <!-- Row 1: Nama Produk & Spesifikasi (Ketik) -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 items-start">
                                        <!-- Nama Produk / Model Unit -->
                                        <div>
                                            <label class="text-[10px] text-slate-700 font-bold block mb-1" x-text="lang === 'zh' ? '设备型号 / 产品名称 * (手填)' : 'Nama Produk / Model Unit * (Ketik)'">
                                                Nama Produk / Model Unit * (Ketik)
                                            </label>
                                            <input type="text" 
                                                   x-model="row.name" 
                                                   @input="recalculateLeadEstimatedValue()"
                                                   :placeholder="lang === 'zh' ? '例: Forklift Diesel 3.5 Ton Toyota' : 'Contoh: Forklift Diesel 3.5 Ton Toyota'"
                                                   class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-slate-900 text-xs font-bold outline-none focus:border-amber-500 shadow-2xs">
                                        </div>

                                        <!-- Spesifikasi Unit (Ketik) -->
                                        <div>
                                            <label class="text-[10px] text-slate-700 font-bold block mb-1" x-text="lang === 'zh' ? '技术规格 (手填)' : 'Spesifikasi Unit (Ketik)'">
                                                Spesifikasi Unit (Ketik)
                                            </label>
                                            <input type="text" 
                                                   x-model="row.specification" 
                                                   :placeholder="lang === 'zh' ? '例: 门架3米, 实心胎, 货叉1220mm 等' : 'Contoh: Mast 3M, Ban Solid Rubber, Fork 1220mm, dsb'"
                                                   class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-slate-900 text-xs outline-none focus:border-amber-500 shadow-2xs">
                                        </div>
                                    </div>

                                    <!-- Row 2: Beda Kolom Sesuai Deal Type Item (Rental vs Sales) -->
                                    <div class="grid grid-cols-2 md:grid-cols-12 gap-3 items-center pt-2.5 border-t border-slate-200/70">
                                        <!-- Qty (Jumlah Unit) -->
                                        <div class="col-span-1 md:col-span-2">
                                            <label class="text-[10px] text-slate-500 font-bold block mb-1" x-text="lang === 'zh' ? '数量' : 'Qty'">Qty</label>
                                            <input type="number" min="1" x-model.number="row.quantity" @input="recalculateLeadEstimatedValue()" class="w-full bg-white border border-slate-300 rounded-xl px-3 py-1.5 text-slate-900 text-xs text-center font-bold outline-none focus:border-amber-400">
                                        </div>

                                        <!-- JIKA ITEM INI SEWA: Durasi, Satuan Durasi, Tarif Sewa -->
                                        <template x-if="(row.deal_type || 'rental') === 'rental'">
                                            <div class="col-span-1 md:col-span-2">
                                                <label class="text-[10px] text-slate-500 font-bold block mb-1" x-text="lang === 'zh' ? '租赁时长' : 'Durasi'">Durasi</label>
                                                <input type="number" min="1" x-model.number="row.duration" @input="recalculateLeadEstimatedValue()" class="w-full bg-white border border-slate-300 rounded-xl px-3 py-1.5 text-slate-900 text-xs text-center font-bold outline-none focus:border-amber-400">
                                            </div>
                                        </template>

                                        <template x-if="(row.deal_type || 'rental') === 'rental'">
                                            <div class="col-span-2 md:col-span-2">
                                                <label class="text-[10px] text-slate-500 font-bold block mb-1" x-text="lang === 'zh' ? '时长单位' : 'Satuan Durasi'">Satuan Durasi</label>
                                                <select x-model="row.duration_unit" @change="recalculateLeadEstimatedValue()" class="w-full bg-white border border-slate-300 rounded-xl px-2 py-1.5 text-slate-900 text-xs font-semibold outline-none focus:border-amber-400">
                                                    <option value="Bulan">Bulan (月)</option>
                                                    <option value="Minggu">Minggu (周)</option>
                                                    <option value="Hari">Hari (天)</option>
                                                    <option value="Jam">Jam (小时)</option>
                                                    <option value="Tahun">Tahun (年)</option>
                                                </select>
                                            </div>
                                        </template>

                                        <template x-if="(row.deal_type || 'rental') === 'rental'">
                                            <div class="col-span-2 md:col-span-3">
                                                <label class="text-[10px] text-slate-500 font-bold block mb-1" x-text="lang === 'zh' ? '租金单价 (Rp)' : 'Tarif Sewa (Rp)'">Tarif Sewa (Rp)</label>
                                                <input type="text"
                                                       inputmode="numeric"
                                                       data-rupiah
                                                       :value="formatNumberWithDots(row.unit_price)"
                                                       @input="row.unit_price = parseNumberFromDots($event.target.value); recalculateLeadEstimatedValue()"
                                                       placeholder="0"
                                                       class="w-full bg-white border border-slate-300 rounded-xl px-3 py-1.5 text-slate-900 text-xs font-mono font-bold outline-none focus:border-amber-400">
                                            </div>
                                        </template>

                                        <!-- JIKA ITEM INI PENJUALAN UNIT: Harga Jual (Rp) -->
                                        <template x-if="row.deal_type === 'sales'">
                                            <div class="col-span-2 md:col-span-6">
                                                <label class="text-[10px] text-slate-500 font-bold block mb-1" x-text="lang === 'zh' ? '售价单价 (Rp)' : 'Harga Jual (Rp)'">Harga Jual (Rp)</label>
                                                <input type="text"
                                                       inputmode="numeric"
                                                       data-rupiah
                                                       :value="formatNumberWithDots(row.unit_price)"
                                                       @input="row.unit_price = parseNumberFromDots($event.target.value); recalculateLeadEstimatedValue()"
                                                       placeholder="0"
                                                       class="w-full bg-white border border-slate-300 rounded-xl px-3 py-1.5 text-slate-900 text-xs font-mono font-bold outline-none focus:border-amber-400">
                                            </div>
                                        </template>

                                        <!-- Foto Unit (Opsional) -->
                                        <div :class="row.deal_type === 'sales' ? 'col-span-2 md:col-span-4' : 'col-span-2 md:col-span-3'">
                                            <label class="text-[10px] text-slate-500 font-bold block mb-1 truncate" x-text="lang === 'zh' ? '设备照片 (选填)' : 'Foto Unit (Opsional)'">Foto Unit (Opsional)</label>
                                            <div class="flex items-center space-x-2">
                                                <label class="cursor-pointer px-3 py-1.5 rounded-xl border border-slate-300 hover:border-amber-400 bg-white hover:bg-amber-50 text-slate-700 transition inline-flex items-center space-x-1.5 text-xs font-semibold shrink-0 shadow-2xs" :title="lang === 'zh' ? '上传设备照片' : 'Upload Foto Unit'">
                                                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                    <span x-text="lang === 'zh' ? '上传照片' : 'Upload Foto'">Upload Foto</span>
                                                    <input type="file" accept="image/*" @change="uploadLeadRowImage(idx, $event)" class="hidden">
                                                </label>
                                                <template x-if="row.image_preview">
                                                    <img :src="row.image_preview" alt="Preview" class="w-7 h-7 rounded-lg object-cover border border-slate-300 shrink-0 shadow-2xs">
                                                </template>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </template>

                            <template x-if="!leadForm.items || leadForm.items.length === 0">
                                <div class="p-6 text-center text-slate-400 text-xs italic border-2 border-dashed border-slate-200 rounded-2xl bg-white/50">
                                    <p class="font-bold text-slate-500 mb-1" x-text="lang === 'zh' ? '尚未添加具体设备产品（选填）。' : 'Belum ada unit produk yang ditambahkan (Opsional).'"></p>
                                    <p class="text-[11px] text-slate-400 mb-3" x-text="lang === 'zh' ? '若客户已有明确设备型号需求，请点击下方按钮添加。' : 'Klik tombol di bawah jika calon customer sudah menyebutkan unit tertentu.'"></p>
                                    <button type="button" @click="addLeadItem()" class="px-3.5 py-1.5 rounded-xl bg-amber-400 hover:bg-yellow-300 text-slate-950 font-black text-xs transition inline-flex items-center space-x-1.5 shadow-2xs">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                                        <span x-text="lang === 'zh' ? '+ 添加设备产品' : 'Tambah Baris Unit'">Tambah Baris Unit</span>
                                    </button>
                                </div>
                            </template>
                        </div>

                        <!-- Informasi Nilai Estimasi & Lokasi -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">
                                    <span x-text="lang === 'zh' ? '预估总交易金额 (Rp)' : 'Total Estimasi Nilai (Rp)'">Total Estimasi Nilai (Rp)</span>
                                    <span class="text-[10px] text-slate-400 font-normal block" x-text="lang === 'zh' ? '系统自动计算 / 支持手动填写' : 'Dihitung otomatis / ketik langsung'">Dihitung otomatis / ketik langsung</span>
                                </label>
                                <input type="text"
                                       inputmode="numeric"
                                       data-rupiah
                                       :value="formatNumberWithDots(leadForm.estimated_value)"
                                       @input="leadForm.estimated_value = parseNumberFromDots($event.target.value)"
                                       placeholder="0"
                                       class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-900 font-bold font-mono outline-none focus:border-yellow-400 focus:bg-white">
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '预计成交日期' : 'Target Closing'">Target Closing</label>
                                <input type="date" x-model="leadForm.expected_closing_date" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-900 font-semibold outline-none focus:border-yellow-400 focus:bg-white">
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '项目/交货地点' : 'Lokasi Proyek / Pengiriman'">Lokasi Proyek / Pengiriman</label>
                                <input type="text" x-model="leadForm.project_location" :placeholder="lang === 'zh' ? '例: MM2100工业区' : 'Contoh: Kawasan Industri MM2100'" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-900 font-semibold outline-none focus:border-yellow-400 focus:bg-white">
                            </div>

                            <div class="md:col-span-3">
                                <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '客户特殊需求说明' : 'Catatan Kebutuhan Customer'">Catatan Kebutuhan Customer</label>
                                <textarea x-model="leadForm.customer_notes" rows="2" :placeholder="lang === 'zh' ? '客户特殊工况或具体要求说明...' : 'Catatan spesifik kebutuhan calon customer...'" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-900 font-semibold outline-none focus:border-yellow-400 focus:bg-white"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Grup 4: Status Pipeline & Prioritas -->
                <div class="space-y-4">
                    <h4 class="font-black text-slate-900 uppercase tracking-wider text-[11px] pb-1 border-b border-slate-100 flex items-center space-x-2">
                        <span class="w-2 h-2 rounded-full bg-purple-600"></span>
                        <span x-text="lang === 'zh' ? '4. 漏斗状态与优先级' : '4. Status Pipeline & Prioritas'">4. Status Pipeline & Prioritas</span>
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '漏斗阶段 *' : 'Status Pipeline *'">Status Pipeline *</label>
                            <select x-model="leadForm.pipeline_stage" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-900 font-semibold outline-none focus:border-yellow-400 focus:bg-white">
                                <option value="new" x-text="lang === 'zh' ? '新线索 (未联系)' : 'Prospek Baru (Belum Dihubungi)'">Prospek Baru (Belum Dihubungi)</option>
                                <option value="contacted" x-text="lang === 'zh' ? '已初步沟通' : 'Sudah Dihubungi'">Sudah Dihubungi</option>
                                <option value="qualified" x-text="lang === 'zh' ? '明确意向 / 合格' : 'Qualified / Tertarik'">Qualified / Tertarik</option>
                                <option value="proposal" x-text="lang === 'zh' ? '方案报价已发送' : 'Penawaran (Quotation Terkirim)'">Penawaran (Quotation Terkirim)</option>
                                <option value="negotiation" x-text="lang === 'zh' ? '商务谈判中' : 'Negosiasi'">Negosiasi</option>
                                <option value="won" x-text="lang === 'zh' ? '成功赢单 (Closing)' : 'Won (Berhasil Closing)'">Won (Berhasil Closing)</option>
                                <option value="lost" x-text="lang === 'zh' ? '输单流失 (Lost)' : 'Lost (Gagal / Batal)'">Lost (Gagal / Batal)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '优先级 *' : 'Tingkat Prioritas *'">Tingkat Prioritas *</label>
                            <select x-model="leadForm.priority" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-900 font-semibold outline-none focus:border-yellow-400 focus:bg-white">
                                <option value="normal" x-text="lang === 'zh' ? '普通 (Normal)' : 'Normal'">Normal</option>
                                <option value="high" x-text="lang === 'zh' ? '高优先级 (High Priority)' : 'Tinggi (High Priority)'">Tinggi (High Priority)</option>
                                <option value="low" x-text="lang === 'zh' ? '低优先级 (Low Priority)' : 'Rendah (Low Priority)'">Rendah (Low Priority)</option>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '销售内部备注' : 'Catatan Internal Sales'">Catatan Internal Sales</label>
                            <textarea x-model="leadForm.internal_notes" rows="2" :placeholder="lang === 'zh' ? '仅销售团队可见的内部跟进备忘...' : 'Catatan internal yang hanya terlihat oleh tim sales...'" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-900 font-semibold outline-none focus:border-yellow-400 focus:bg-white"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Footer Buttons -->
                <div class="pt-4 flex items-center justify-end space-x-3 border-t border-slate-100">
                    <button type="button" @click="leadModalOpen = false" class="px-5 py-2.5 rounded-xl text-slate-600 font-bold hover:bg-slate-100 transition" x-text="lang === 'zh' ? '取消' : 'Batal'">
                        Batal
                    </button>
                    <button type="submit" 
                            :disabled="isSubmitting"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-extrabold px-6 py-2.5 rounded-xl shadow-md transition disabled:opacity-50">
                        <span x-text="isSubmitting ? (lang === 'zh' ? '正在保存...' : 'Menyimpan...') : (leadModalMode === 'create' ? (lang === 'zh' ? '+ 保存线索' : '+ Simpan Leads') : (lang === 'zh' ? '保存更改' : 'Simpan Perubahan'))"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 9. MODAL CATAT AKTIVITAS (CALL, WA, BROSUR, MEETING, DLL)                  -->
    <!-- ========================================================================= -->
    <div x-show="activityModalOpen" 
         x-cloak 
         style="display: none;"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        
        <div @click.away="activityModalOpen = false" 
             class="bg-white w-full max-w-lg rounded-3xl shadow-2xl border border-slate-200 overflow-hidden animate-in zoom-in-95 duration-150">
            
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                <div>
                    <h3 class="text-sm font-black text-slate-900" x-text="lang === 'zh' ? '记录客户沟通活动' : 'Catat Aktivitas Komunikasi'">Catat Aktivitas Komunikasi</h3>
                    <p class="text-[11px] text-slate-500 truncate" x-text="`${lang === 'zh' ? '跟进客户: ' : 'Prospek: '}${activeLead?.company_name || '-'}`"></p>
                </div>
                <button type="button" @click="activityModalOpen = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form @submit.prevent="submitActivityForm" class="p-6 space-y-4 text-xs">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '活动类型 *' : 'Jenis Aktivitas *'">Jenis Aktivitas *</label>
                        <select x-model="activityForm.activity_type" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-900 font-semibold outline-none focus:border-yellow-400 focus:bg-white">
                            <option value="Telepon" x-text="lang === 'zh' ? '电话沟通 (Telepon)' : 'Telepon'">Telepon</option>
                            <option value="WhatsApp" x-text="lang === 'zh' ? 'WhatsApp沟通' : 'WhatsApp'">WhatsApp</option>
                            <option value="Email" x-text="lang === 'zh' ? '电子邮件 (Email)' : 'Email'">Email</option>
                            <option value="Meeting" x-text="lang === 'zh' ? '商务洽谈会议 (Meeting)' : 'Meeting'">Meeting</option>
                            <option value="Kunjungan" x-text="lang === 'zh' ? '现场拜访考察 (Kunjungan)' : 'Kunjungan Lapangan'">Kunjungan Lapangan</option>
                            <option value="Brosur Terkirim" x-text="lang === 'zh' ? '已发送产品画册 (Brosur)' : 'Brosur Terkirim'">Brosur Terkirim</option>
                            <option value="Company Profile Terkirim" x-text="lang === 'zh' ? '已发送公司简介 (Compro)' : 'Company Profile Terkirim'">Company Profile Terkirim</option>
                            <option value="Follow-up" x-text="lang === 'zh' ? '常规跟进 (Follow-up)' : 'Follow-up'">Follow-up</option>
                            <option value="Catatan Lainnya" x-text="lang === 'zh' ? '其他沟通记录' : 'Catatan Lainnya'">Catatan Lainnya</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '活动日期与时间 *' : 'Tanggal & Waktu *'">Tanggal & Waktu *</label>
                        <input type="datetime-local" x-model="activityForm.activity_date" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-900 font-semibold outline-none focus:border-yellow-400 focus:bg-white">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '活动主题摘要 *' : 'Ringkasan Aktivitas *'">Ringkasan Aktivitas *</label>
                    <input type="text" x-model="activityForm.summary" required :placeholder="lang === 'zh' ? '例: 微信发送丰田FD30叉车图册' : 'Contoh: Kirim Brosur Katalog Forklift Toyota FD30 via WA'" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-900 font-semibold outline-none focus:border-yellow-400 focus:bg-white">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '沟通详情与客户反馈' : 'Catatan Hasil Komunikasi'">Catatan Hasil Komunikasi</label>
                    <textarea x-model="activityForm.communication_notes" rows="3" :placeholder="lang === 'zh' ? '填写客户关切点、答复、预算意向、疑问等...' : 'Tuliskan respon calon customer, pertanyaan spesifik, dll...'" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-900 font-semibold outline-none focus:border-yellow-400 focus:bg-white"></textarea>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '附件 / 发送文件名称 (选填)' : 'Nama Dokumen / Lampiran (Opsional)'">Nama Dokumen / Lampiran (Opsional)</label>
                    <input type="text" x-model="activityForm.attachment" :placeholder="lang === 'zh' ? '例: brosur-forklift-toyota.pdf' : 'Contoh: brosur-forklift-toyota.pdf'" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-900 font-semibold outline-none focus:border-yellow-400 focus:bg-white">
                </div>

                <div class="pt-3 flex items-center justify-end space-x-3 border-t border-slate-100">
                    <button type="button" @click="activityModalOpen = false" class="px-4 py-2 rounded-xl text-slate-600 font-bold hover:bg-slate-100 transition" x-text="lang === 'zh' ? '取消' : 'Batal'">
                        Batal
                    </button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-extrabold px-6 py-2 rounded-xl shadow-md transition" x-text="lang === 'zh' ? '保存活动记录' : 'Simpan Aktivitas'">
                        Simpan Aktivitas
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 10. MODAL JADWALKAN FOLLOW-UP                                             -->
    <!-- ========================================================================= -->
    <div x-show="followUpModalOpen" 
         x-cloak 
         style="display: none;"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        
        <div @click.away="followUpModalOpen = false" 
             class="bg-white w-full max-w-lg rounded-3xl shadow-2xl border border-slate-200 overflow-hidden animate-in zoom-in-95 duration-150">
            
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                <div>
                    <h3 class="text-sm font-black text-slate-900" x-text="lang === 'zh' ? '安排跟进任务日程' : 'Jadwalkan Tugas Follow-up'">Jadwalkan Tugas Follow-up</h3>
                    <p class="text-[11px] text-slate-500 truncate" x-text="`${lang === 'zh' ? '跟进客户: ' : 'Prospek: '}${activeLead?.company_name || '-'}`"></p>
                </div>
                <button type="button" @click="followUpModalOpen = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form @submit.prevent="submitFollowUpForm" class="p-6 space-y-4 text-xs">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '跟进方式 *' : 'Jenis Tindak Lanjut *'">Jenis Tindak Lanjut *</label>
                        <select x-model="followUpForm.follow_up_type" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-900 font-semibold outline-none focus:border-yellow-400 focus:bg-white">
                            <option value="Telepon" x-text="lang === 'zh' ? '电话回访 (Telepon)' : 'Telepon'">Telepon</option>
                            <option value="WhatsApp" x-text="lang === 'zh' ? 'WhatsApp联系' : 'WhatsApp'">WhatsApp</option>
                            <option value="Email" x-text="lang === 'zh' ? '邮件跟进 (Email)' : 'Email'">Email</option>
                            <option value="Meeting" x-text="lang === 'zh' ? '会议商谈 (Meeting)' : 'Meeting'">Meeting</option>
                            <option value="Kunjungan" x-text="lang === 'zh' ? '现场拜访 (Kunjungan)' : 'Kunjungan Lapangan'">Kunjungan Lapangan</option>
                            <option value="Kirim Penawaran" x-text="lang === 'zh' ? '发送报价单 (Penawaran)' : 'Kirim Penawaran'">Kirim Penawaran</option>
                            <option value="Lainnya" x-text="lang === 'zh' ? '其他方式' : 'Lainnya'">Lainnya</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '预约跟进时间 *' : 'Tanggal & Waktu Jadwal *'">Tanggal & Waktu Jadwal *</label>
                        <input type="datetime-local" x-model="followUpForm.scheduled_at" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-900 font-semibold outline-none focus:border-yellow-400 focus:bg-white">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '跟进负责人 (Sales PIC)' : 'Sales Penanggung Jawab'">Sales Penanggung Jawab</label>
                    @if($isAdmin)
                    <select x-model="followUpForm.user_id" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-900 font-semibold outline-none focus:border-yellow-400 focus:bg-white">
                        <template x-for="s in salesUsers" :key="s.id">
                            <option :value="s.id" x-text="s.name"></option>
                        </template>
                    </select>
                    @else
                    <div class="w-full bg-slate-100 border border-slate-200 rounded-xl px-3 py-2 text-slate-800 font-bold flex items-center justify-between">
                        <span>{{ auth()->user()->name }}</span>
                        <span class="text-[10px] text-slate-500 font-normal" x-text="lang === 'zh' ? '当前销售' : 'Sales Anda'">Sales Anda</span>
                    </div>
                    @endif
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '跟进目的与事项清单 *' : 'Tujuan & Catatan Tindak Lanjut *'">Tujuan & Catatan Tindak Lanjut *</label>
                    <textarea x-model="followUpForm.purpose_notes" rows="3" required :placeholder="lang === 'zh' ? '填写本次跟进的重点事项、需确认的方案或谈判要点...' : 'Tuliskan tujuan follow-up, materi yang perlu dikonfirmasi...'" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-900 font-semibold outline-none focus:border-yellow-400 focus:bg-white"></textarea>
                </div>

                <div class="pt-3 flex items-center justify-end space-x-3 border-t border-slate-100">
                    <button type="button" @click="followUpModalOpen = false" class="px-4 py-2 rounded-xl text-slate-600 font-bold hover:bg-slate-100 transition" x-text="lang === 'zh' ? '取消' : 'Batal'">
                        Batal
                    </button>
                    <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white font-extrabold px-6 py-2 rounded-xl shadow-md transition" x-text="lang === 'zh' ? '确认安排日程' : 'Jadwalkan Follow-up'">
                        Jadwalkan Follow-up
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 11. MODAL SELESAIKAN FOLLOW-UP                                            -->
    <!-- ========================================================================= -->
    <div x-show="completeFollowUpModalOpen" 
         x-cloak 
         style="display: none;"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        
        <div @click.away="completeFollowUpModalOpen = false" 
             class="bg-white w-full max-w-lg rounded-3xl shadow-2xl border border-slate-200 overflow-hidden animate-in zoom-in-95 duration-150">
            
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                <div>
                    <h3 class="text-sm font-black text-slate-900" x-text="lang === 'zh' ? '确认完成跟进任务' : 'Konfirmasi Penyelesaian Follow-up'">Konfirmasi Penyelesaian Follow-up</h3>
                    <p class="text-[11px] text-slate-500 truncate" x-text="`${lang === 'zh' ? '客户: ' : ''}${completeTargetCompanyName}`"></p>
                </div>
                <button type="button" @click="completeFollowUpModalOpen = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form @submit.prevent="submitCompleteFollowUp" class="p-6 space-y-4 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '跟进结果与客户反馈 *' : 'Catatan Hasil Komunikasi / Follow-up *'">Catatan Hasil Komunikasi / Follow-up *</label>
                    <textarea x-model="completeForm.result_notes" rows="3" required :placeholder="lang === 'zh' ? '详细记录客户答复、意向进展、价格决策反馈...' : 'Jelaskan respon calon customer, keputusan, kesepakatan harga...'" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-900 font-semibold outline-none focus:border-yellow-400 focus:bg-white"></textarea>
                </div>

                <!-- Opsi langsung buat follow up berikutnya -->
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 space-y-3">
                    <span class="font-bold text-slate-800 block text-xs" x-text="lang === 'zh' ? '同时预约下一次跟进 (选填)' : 'Jadwalkan Follow-up Lanjutan Berikutnya (Opsional)'">Jadwalkan Follow-up Lanjutan Berikutnya (Opsional)</span>
                    
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-600 mb-1 text-[11px]" x-text="lang === 'zh' ? '跟进方式' : 'Jenis Lanjutan'">Jenis Lanjutan</label>
                            <select x-model="completeForm.next_follow_up_type" class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs font-semibold outline-none">
                                <option value="Telepon" x-text="lang === 'zh' ? '电话回访 (Telepon)' : 'Telepon'">Telepon</option>
                                <option value="WhatsApp" x-text="lang === 'zh' ? 'WhatsApp联系' : 'WhatsApp'">WhatsApp</option>
                                <option value="Meeting" x-text="lang === 'zh' ? '会议商谈 (Meeting)' : 'Meeting'">Meeting</option>
                                <option value="Kunjungan" x-text="lang === 'zh' ? '现场拜访 (Kunjungan)' : 'Kunjungan'">Kunjungan</option>
                                <option value="Kirim Penawaran" x-text="lang === 'zh' ? '发送报价单 (Penawaran)' : 'Kirim Penawaran'">Kirim Penawaran</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-600 mb-1 text-[11px]" x-text="lang === 'zh' ? '预约时间' : 'Tanggal & Jam Lanjutan'">Tanggal & Jam Lanjutan</label>
                            <input type="datetime-local" x-model="completeForm.next_follow_up_date" class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs font-semibold outline-none">
                        </div>
                    </div>
                </div>

                <div class="pt-3 flex items-center justify-end space-x-3 border-t border-slate-100">
                    <button type="button" @click="completeFollowUpModalOpen = false" class="px-4 py-2 rounded-xl text-slate-600 font-bold hover:bg-slate-100 transition" x-text="lang === 'zh' ? '取消' : 'Batal'">
                        Batal
                    </button>
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold px-6 py-2 rounded-xl shadow-md transition" x-text="lang === 'zh' ? '保存并标记完成' : 'Simpan & Selesaikan'">
                        Simpan & Selesaikan
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
    function erpCrmHub(initialTab, initialPeriod, initialSalesFilter, initialLeads, salesUsers, catalogItems, customers) {
        return {
            lang: localStorage.getItem('keylock_lang') || 'id',
            init() {
                window.addEventListener('lang-changed', (e) => {
                    this.lang = e.detail;
                });
                window.addEventListener('storage', (e) => {
                    if (e.key === 'keylock_lang') {
                        this.lang = e.newValue || 'id';
                    }
                });
            },
            activeTab: initialTab || 'pipeline',
            mobileFilterOpen: false,
            period: initialPeriod || 'this_month',
            salesFilter: initialSalesFilter || 'all',
            leads: initialLeads || [],
            salesUsers: salesUsers || [],
            catalogItems: catalogItems || [],
            customers: customers || [],

            // Global Number Formatting with Dots
            formatNumberWithDots(val) {
                if (typeof window.formatNumberWithDots === 'function') {
                    return window.formatNumberWithDots(val);
                }
                if (val === undefined || val === null || val === '') return '';
                const clean = String(val).replace(/\D/g, '');
                return clean ? Number(clean).toLocaleString('id-ID') : '';
            },
            parseNumberFromDots(val) {
                if (typeof window.parseNumberFromDots === 'function') {
                    return window.parseNumberFromDots(val);
                }
                if (!val && val !== 0) return 0;
                const clean = String(val).replace(/\D/g, '');
                return clean ? parseInt(clean, 10) : 0;
            },

            // Filter states
            searchKanban: '',
            filterSource: 'all',
            filterDealType: 'all',
            pipelineViewMode: 'kanban', // 'kanban' or 'table'

            searchTable: '',
            filterTableStage: 'all',
            filterTableDealType: 'all',

            // Active Lead Detail Drawer
            detailDrawerOpen: false,
            activeLead: null,

            // Modals
            leadModalOpen: false,
            leadModalMode: 'create',
            isSubmitting: false,

            activityModalOpen: false,
            followUpModalOpen: false,
            completeFollowUpModalOpen: false,
            completeTargetId: null,
            completeTargetCompanyName: '',
            // Custom Smooth Pointer Drag & Drop (100% Solid & Opaque like Image 1)
            isPointerDragging: false,
            draggedLead: null,
            originalStage: null,
            previewStage: null,
            hoveredStage: null,
            dragRotation: 0,
            dragX: 0,
            dragY: 0,
            dragOffsetX: 0,
            dragOffsetY: 0,
            cardWidth: 320,
            cardHeight: 180,
            lastPointerX: null,
            pointerStartX: 0,
            pointerStartY: 0,
            pendingLead: null,
            pendingCardEl: null,
            tiltTimeout: null,

            // Forms
            leadForm: {
                id: null,
                company_name: '',
                industry: '',
                source: 'Website',
                entry_date: new Date().toISOString().split('T')[0],
                sales_id: (salesUsers[0]?.id || 1),
                website: '',
                address: '',
                pic_name: '',
                pic_title: '',
                pic_phone: '',
                pic_email: '',
                deal_type: 'rental',
                items: [],
                product_interest: '',
                specification: '',
                quantity: 1,
                rental_duration: '',
                project_location: '',
                estimated_value: 0,
                expected_closing_date: '',
                customer_notes: '',
                pipeline_stage: 'new',
                priority: 'normal',
                internal_notes: ''
            },

            activityForm: {
                activity_type: 'Telepon',
                activity_date: new Date().toISOString().slice(0, 16),
                summary: '',
                communication_notes: '',
                attachment: ''
            },

            followUpForm: {
                follow_up_type: 'Telepon',
                scheduled_at: new Date(Date.now() + 86400000).toISOString().slice(0, 16),
                user_id: 1,
                purpose_notes: ''
            },

            completeForm: {
                result_notes: '',
                next_follow_up_type: 'Telepon',
                next_follow_up_date: '',
                next_follow_up_notes: ''
            },

            switchTab(tab) {
                this.activeTab = tab;
                const url = new URL(window.location);
                url.searchParams.set('tab', tab);
                window.history.replaceState({}, '', url);
            },

            applyFilters() {
                const url = new URL(window.location);
                url.searchParams.set('tab', this.activeTab);
                url.searchParams.set('period', this.period);
                url.searchParams.set('sales_id', this.salesFilter);
                window.location.href = url.toString();
            },

            resetFilters() {
                this.searchKanban = '';
                this.filterSource = 'all';
                this.filterDealType = 'all';
                this.searchTable = '';
                this.filterTableStage = 'all';
                this.filterTableDealType = 'all';
            },

            getStageLeads(stageKey) {
                return this.leads.filter(lead => {
                    if (lead.pipeline_stage !== stageKey) return false;

                    // Search filter
                    if (this.searchKanban) {
                        const q = this.searchKanban.toLowerCase();
                        const matchCompany = lead.company_name?.toLowerCase().includes(q);
                        const matchPic = lead.pic_name?.toLowerCase().includes(q);
                        const matchPhone = lead.pic_phone?.includes(q);
                        const matchProduct = lead.product_interest?.toLowerCase().includes(q);
                        if (!matchCompany && !matchPic && !matchPhone && !matchProduct) return false;
                    }

                    // Source filter
                    if (this.filterSource !== 'all' && lead.source !== this.filterSource) return false;

                    // Deal type filter
                    if (this.filterDealType !== 'all' && lead.deal_type !== this.filterDealType) return false;

                    return true;
                });
            },

            getStageSum(stageKey) {
                const list = this.getStageLeads(stageKey);
                return list.reduce((acc, lead) => acc + Number(lead.estimated_value || 0), 0);
            },

            get filteredTableLeads() {
                const query = (this.searchKanban || this.searchTable || '').toLowerCase();
                return this.leads.filter(lead => {
                    // Search
                    if (query) {
                        const matchCompany = lead.company_name?.toLowerCase().includes(query);
                        const matchCode = lead.code?.toLowerCase().includes(query);
                        const matchPic = lead.pic_name?.toLowerCase().includes(query);
                        const matchPhone = lead.pic_phone?.includes(query);
                        const matchProduct = lead.product_interest?.toLowerCase().includes(query);
                        if (!matchCompany && !matchCode && !matchPic && !matchPhone && !matchProduct) return false;
                    }

                    // Stage filter
                    if (this.filterTableStage !== 'all' && lead.pipeline_stage !== this.filterTableStage) return false;

                    // Deal type filter
                    const dt = this.filterDealType !== 'all' ? this.filterDealType : this.filterTableDealType;
                    if (dt !== 'all' && lead.deal_type !== dt) return false;

                    // Source filter
                    if (this.filterSource !== 'all' && lead.source !== this.filterSource) return false;

                    return true;
                });
            },

            getSalesName(salesId) {
                const found = this.salesUsers.find(s => s.id == salesId);
                return found ? found.name : 'Sales';
            },

            getStageLabel(stg) {
                if (this.lang === 'zh') {
                    const mapZh = {
                        'new': '新建线索',
                        'contacted': '已初次联系',
                        'qualified': '明确意向',
                        'proposal': '方案报价',
                        'negotiation': '商务谈判',
                        'won': '成功签约 (Won)',
                        'lost': '输单流失 (Lost)'
                    };
                    return mapZh[stg] || stg || '-';
                }
                const map = {
                    'new': 'Prospek Baru',
                    'contacted': 'Sudah Dihubungi',
                    'qualified': 'Qualified / Tertarik',
                    'proposal': 'Penawaran',
                    'negotiation': 'Negosiasi',
                    'won': 'Won (Berhasil)',
                    'lost': 'Lost (Gagal)'
                };
                return map[stg] || stg || '-';
            },

            getStageStepLabel(key) {
                if (this.lang === 'zh') {
                    const zh = {
                        'new': '1. 新建 (Baru)',
                        'contacted': '2. 已联系 (Contacted)',
                        'qualified': '3. 合格意向 (Qualified)',
                        'proposal': '4. 方案报价 (Proposal)',
                        'negotiation': '5. 商务谈判 (Negotiation)',
                        'won': '6. 赢单 (Won)',
                        'lost': '战败 (Lost)'
                    };
                    return zh[key] || key;
                }
                const idMap = {
                    'new': '1. Baru',
                    'contacted': '2. Dihubungi',
                    'qualified': '3. Qualified',
                    'proposal': '4. Penawaran',
                    'negotiation': '5. Negosiasi',
                    'won': '6. Won',
                    'lost': 'Lost'
                };
                return idMap[key] || key;
            },

            getDealTypeLabel(type) {
                if (type === 'rental') return this.lang === 'zh' ? '设备租赁 (Rental)' : 'Rental / Sewa Unit';
                if (type === 'sales') return this.lang === 'zh' ? '设备买卖 (Sales)' : 'Penjualan Unit (Beli)';
                if (type === 'both') return this.lang === 'zh' ? '租售并举 (Both)' : 'Keduanya (Sewa & Jual)';
                return type || '-';
            },

            getLeadQuantityDescription(lead) {
                if (!lead) return '-';
                const qty = lead.quantity || 1;
                if (lead.deal_type === 'sales') {
                    return `${qty} Unit (Pembelian / Beli)`;
                }
                if (lead.deal_type === 'both') {
                    return `${qty} Unit (Campuran Sewa & Beli)`;
                }
                return `${qty} Unit (${lead.rental_duration || 'Sewa'})`;
            },

            getStageBadgeClass(stg) {
                const map = {
                    'new': 'bg-slate-100 text-slate-800 border border-slate-200',
                    'contacted': 'bg-blue-100 text-blue-800 border border-blue-200',
                    'qualified': 'bg-amber-100 text-amber-800 border border-amber-200',
                    'proposal': 'bg-purple-100 text-purple-800 border border-purple-200',
                    'negotiation': 'bg-indigo-100 text-indigo-800 border border-indigo-200',
                    'won': 'bg-emerald-100 text-emerald-800 border border-emerald-300',
                    'lost': 'bg-rose-100 text-rose-800 border border-rose-200'
                };
                return map[stg] || 'bg-slate-100 text-slate-700';
            },

            formatRupiah(num) {
                if (!num) return 'Rp 0';
                return 'Rp ' + Number(num).toLocaleString('id-ID');
            },

            formatDateShort(dateStr) {
                if (!dateStr) return '-';
                return new Date(dateStr).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' });
            },

            cleanPhone(ph) {
                if (!ph) return '';
                return ph.replace(/[^0-9]/g, '');
            },

            // ==========================================
            // ==========================================
            // KANBAN DRAG AND DROP & COLOR MORPHING (POINTER EVENTS - 100% SOLID)
            // ==========================================
            handleCardPointerDown(e, lead) {
                if (e.button !== 0) return; // Only primary mouse button
                if (e.target.closest('button, select, a, input, textarea, [data-no-drag]')) {
                    return; // Ignore clicks on interactive controls
                }

                const cardEl = e.currentTarget;
                const pointerId = e.pointerId;

                this.pendingLead = lead;
                this.pendingCardEl = cardEl;
                this.pointerStartX = e.clientX;
                this.pointerStartY = e.clientY;
                this.lastPointerX = e.clientX;

                const cleanup = () => {
                    window.removeEventListener('pointermove', onMove);
                    window.removeEventListener('pointerup', onUp);
                    window.removeEventListener('pointercancel', onCancel);
                    document.body.classList.remove('select-none');
                    document.body.style.cursor = '';
                };

                const onCancel = (cancelEvt) => {
                    cleanup();
                    this.isPointerDragging = false;
                    this.draggedLead = null;
                    this.pendingLead = null;
                    this.pendingCardEl = null;
                    this.hoveredStage = null;
                    this.previewStage = null;
                    this.dragRotation = 0;
                    this.lastPointerX = null;
                    if (this.tiltTimeout) clearTimeout(this.tiltTimeout);
                };

                const onMove = (moveEvt) => {
                    if (!this.pendingLead) return;

                    const dist = Math.hypot(moveEvt.clientX - this.pointerStartX, moveEvt.clientY - this.pointerStartY);
                    if (!this.isPointerDragging && dist > 5) {
                        this.isPointerDragging = true;
                        this.draggedLead = this.pendingLead;
                        this.originalStage = this.draggedLead.pipeline_stage;
                        this.previewStage = this.originalStage;
                        this.hoveredStage = this.originalStage;

                        const rect = this.pendingCardEl.getBoundingClientRect();
                        this.cardWidth = rect.width;
                        this.cardHeight = rect.height;
                        this.dragOffsetX = this.pointerStartX - rect.left;
                        this.dragOffsetY = this.pointerStartY - rect.top;

                        document.body.classList.add('select-none');
                        document.body.style.cursor = 'grabbing';
                    }

                    if (this.isPointerDragging) {
                        this.dragX = moveEvt.clientX - this.dragOffsetX;
                        this.dragY = moveEvt.clientY - this.dragOffsetY;

                        // Directional tilt physics (left vs right)
                        if (this.lastPointerX !== null) {
                            const deltaX = moveEvt.clientX - this.lastPointerX;
                            if (deltaX < -2) {
                                this.dragRotation = -5; // tilted left
                                if (this.tiltTimeout) clearTimeout(this.tiltTimeout);
                                this.tiltTimeout = setTimeout(() => {
                                    if (this.isPointerDragging) this.dragRotation = -2;
                                }, 200);
                            } else if (deltaX > 2) {
                                this.dragRotation = 5; // tilted right
                                if (this.tiltTimeout) clearTimeout(this.tiltTimeout);
                                this.tiltTimeout = setTimeout(() => {
                                    if (this.isPointerDragging) this.dragRotation = 2;
                                }, 200);
                            }
                        }
                        this.lastPointerX = moveEvt.clientX;

                        // Column detection & real-time color morph
                        const targetStage = this.detectColumnUnderPointer(moveEvt.clientX, moveEvt.clientY);
                        if (targetStage) {
                            this.hoveredStage = targetStage;
                            this.previewStage = targetStage;
                        }

                        // Auto scroll Kanban board horizontally
                        this.autoScrollKanban(moveEvt.clientX);
                    }
                };

                const onUp = (upEvt) => {
                    cleanup();

                    if (this.isPointerDragging && this.draggedLead) {
                        const targetStage = this.detectColumnUnderPointer(upEvt.clientX, upEvt.clientY) || this.hoveredStage;
                        this.handleDropOnStage(targetStage);
                    } else {
                        this.isPointerDragging = false;
                        this.draggedLead = null;
                        this.pendingLead = null;
                        this.pendingCardEl = null;
                        this.hoveredStage = null;
                        this.previewStage = null;
                        this.dragRotation = 0;
                        this.lastPointerX = null;
                    }
                };

                window.addEventListener('pointermove', onMove);
                window.addEventListener('pointerup', onUp);
                window.addEventListener('pointercancel', onCancel);
            },

            handleDropOnStage(targetStage) {
                if (!this.isPointerDragging || !this.draggedLead) return;

                const movedLead = this.draggedLead;
                const fromStage = this.originalStage;

                // Reset drag states immediately so UI snaps clean
                this.isPointerDragging = false;
                this.draggedLead = null;
                this.pendingLead = null;
                this.pendingCardEl = null;
                this.hoveredStage = null;
                this.previewStage = null;
                this.dragRotation = 0;
                this.lastPointerX = null;
                if (this.tiltTimeout) clearTimeout(this.tiltTimeout);

                // If dropped onto a different column, update stage!
                if (targetStage && targetStage !== fromStage) {
                    // 1. Instant Optimistic UI update
                    const found = this.leads.find(l => l.id == movedLead.id);
                    if (found) {
                        found.pipeline_stage = targetStage;
                    }
                    if (this.activeLead && this.activeLead.id == movedLead.id) {
                        this.activeLead.pipeline_stage = targetStage;
                    }

                    // 2. Persist to server
                    this.changeStageDirect(movedLead.id, targetStage, fromStage);
                }
            },

            detectColumnUnderPointer(clientX, clientY) {
                // Precise geometric column lane detection across all Kanban lanes
                const columns = document.querySelectorAll('[data-stage-column]');
                for (const col of columns) {
                    const rect = col.getBoundingClientRect();
                    // Horizontal bounds with 10px overlap buffer
                    if (clientX >= rect.left - 10 && clientX <= rect.right + 10) {
                        return col.getAttribute('data-stage-column');
                    }
                }
                return null;
            },

            autoScrollKanban(clientX) {
                const container = this.$refs.kanbanScrollContainer;
                if (!container) return;
                const rect = container.getBoundingClientRect();
                const edgeMargin = 80;
                if (clientX < rect.left + edgeMargin) {
                    container.scrollLeft -= 14;
                } else if (clientX > rect.right - edgeMargin) {
                    container.scrollLeft += 14;
                }
            },

            getEffectiveStage(lead) {
                if (this.draggedLead && this.draggedLead.id === lead.id && (this.hoveredStage || this.previewStage)) {
                    return this.hoveredStage || this.previewStage;
                }
                return lead.pipeline_stage;
            },

            getCardTheme(lead) {
                const stage = this.getEffectiveStage(lead);
                const themes = {
                    'new': {
                        bg: 'bg-[#334155]',
                        bgHex: '#334155',
                        borderColor: 'border-[#475569]',
                        borderHex: '#475569',
                        textColor: 'text-white',
                        textHex: '#ffffff',
                        subTextColor: 'text-slate-300',
                        pillBg: 'bg-white/15 text-white border-white/20',
                        priceColor: 'text-yellow-300',
                        btnBg: 'bg-white/15 hover:bg-white/25 text-white border-white/20',
                        selectBg: 'bg-slate-800 text-white border-white/20',
                        dotColor: '#475569',
                        isDark: true
                    },
                    'contacted': {
                        bg: 'bg-[#0369a1]',
                        bgHex: '#0369a1',
                        borderColor: 'border-[#0284c7]',
                        borderHex: '#0284c7',
                        textColor: 'text-white',
                        textHex: '#ffffff',
                        subTextColor: 'text-sky-200',
                        pillBg: 'bg-white/15 text-white border-white/20',
                        priceColor: 'text-yellow-300',
                        btnBg: 'bg-white/15 hover:bg-white/25 text-white border-white/20',
                        selectBg: 'bg-sky-900 text-white border-white/20',
                        dotColor: '#0284c7',
                        isDark: true
                    },
                    'qualified': {
                        bg: 'bg-[#f5c344]',
                        bgHex: '#f5c344',
                        borderColor: 'border-[#e0b037]',
                        borderHex: '#e0b037',
                        textColor: 'text-slate-950',
                        textHex: '#0f172a',
                        subTextColor: 'text-slate-800',
                        pillBg: 'bg-black/10 text-slate-900 border-black/10',
                        priceColor: 'text-slate-950',
                        btnBg: 'bg-black/10 hover:bg-black/20 text-slate-900 border-black/10',
                        selectBg: 'bg-[#e5b334] text-slate-950 border-black/15',
                        dotColor: '#f59e0b',
                        isDark: false
                    },
                    'proposal': {
                        bg: 'bg-[#245846]',
                        bgHex: '#245846',
                        borderColor: 'border-[#1b4435]',
                        borderHex: '#1b4435',
                        textColor: 'text-white',
                        textHex: '#ffffff',
                        subTextColor: 'text-emerald-200',
                        pillBg: 'bg-white/15 text-white border-white/20',
                        priceColor: 'text-yellow-300',
                        btnBg: 'bg-white/15 hover:bg-white/25 text-white border-white/20',
                        selectBg: 'bg-[#1a4234] text-white border-white/20',
                        dotColor: '#2d5d4b',
                        isDark: true
                    },
                    'negotiation': {
                        bg: 'bg-[#c84924]',
                        bgHex: '#c84924',
                        borderColor: 'border-[#b13c19]',
                        borderHex: '#b13c19',
                        textColor: 'text-white',
                        textHex: '#ffffff',
                        subTextColor: 'text-orange-200',
                        pillBg: 'bg-white/15 text-white border-white/20',
                        priceColor: 'text-yellow-300',
                        btnBg: 'bg-white/15 hover:bg-white/25 text-white border-white/20',
                        selectBg: 'bg-[#a33516] text-white border-white/20',
                        dotColor: '#c84924',
                        isDark: true
                    },
                    'won': {
                        bg: 'bg-[#18181b]',
                        bgHex: '#18181b',
                        borderColor: 'border-[#27272a]',
                        borderHex: '#27272a',
                        textColor: 'text-white',
                        textHex: '#ffffff',
                        subTextColor: 'text-zinc-300',
                        pillBg: 'bg-white/15 text-white border-white/20',
                        priceColor: 'text-emerald-400',
                        btnBg: 'bg-white/15 hover:bg-white/25 text-white border-white/20',
                        selectBg: 'bg-[#27272a] text-white border-white/20',
                        dotColor: '#1c1d21',
                        isDark: true
                    },
                    'lost': {
                        bg: 'bg-[#7f1d1d]',
                        bgHex: '#7f1d1d',
                        borderColor: 'border-[#991b1b]',
                        borderHex: '#991b1b',
                        textColor: 'text-white',
                        textHex: '#ffffff',
                        subTextColor: 'text-rose-200',
                        pillBg: 'bg-white/15 text-white border-white/20',
                        priceColor: 'text-rose-200',
                        btnBg: 'bg-white/15 hover:bg-white/25 text-white border-white/20',
                        selectBg: 'bg-[#6b1414] text-white border-white/20',
                        dotColor: '#881337',
                        isDark: true
                    }
                };
                return themes[stage] || themes['new'];
            },

            getPriorityBadgeClass(priority, isDark) {
                const p = (priority || 'normal').toLowerCase();
                if (p === 'high' || p === 'urgent' || p === 'darurat' || p === 'tinggi') {
                    return 'bg-rose-500 text-white border-rose-600';
                }
                if (p === 'low' || p === 'rendah') {
                    return isDark ? 'bg-white/15 text-white border-white/20' : 'bg-black/10 text-slate-800 border-black/15';
                }
                return isDark ? 'bg-white/20 text-white border-white/25' : 'bg-black/10 text-slate-800 border-black/15';
            },

            formatRupiahCompact(num) {
                if (!num || num === 0) return 'Rp 0';
                const n = Number(num);
                if (n >= 1000000000) {
                    return 'Rp ' + (n / 1000000000).toFixed(1).replace('.0', '') + ' M';
                }
                if (n >= 1000000) {
                    return 'Rp ' + (n / 1000000).toFixed(1).replace('.0', '') + ' jt';
                }
                if (n >= 1000) {
                    return 'Rp ' + (n / 1000).toFixed(0) + ' rb';
                }
                return 'Rp ' + n.toLocaleString('id-ID');
            },

            async changeStageDirect(leadId, targetStage, fromStage) {
                if (!targetStage) return;
                try {
                    const res = await fetch(`/erp/crm/leads/${leadId}/stage`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            pipeline_stage: targetStage,
                            change_reason: `Pindah tahap ke ${this.getStageLabel(targetStage)}.`
                        })
                    });
                    const result = await res.json();
                    if (result.success) {
                        const found = this.leads.find(l => l.id == leadId);
                        if (found) {
                            found.pipeline_stage = targetStage;
                            if (result.customer) {
                                found.converted_customer_id = result.customer.id;
                            }
                        }
                        if (this.activeLead && this.activeLead.id == leadId) {
                            this.activeLead.pipeline_stage = targetStage;
                            if (result.customer) {
                                this.activeLead.converted_customer_id = result.customer.id;
                            }
                        }

                        if (targetStage === 'won' || result.prompt_quotation) {
                            const custName = result.customer ? result.customer.name : (found ? found.company_name : '');
                            const salesName = (result.customer && result.customer.sales_name) ? result.customer.sales_name : (found?.sales?.name || '');
                            const wantQuotation = await window.confirmDialog({
                                title: this.lang === 'zh' ? '线索已成功转为赢单 (Won)！' : '🎉 Status Berhasil Dipindahkan ke WON!',
                                message: `Customer "${custName}" telah resmi didaftarkan ke Master Data Customer dengan Sales PIC: "${salesName}".\n\nApakah Anda ingin LANGSUNG BUAT PENAWARAN HARGA sekarang untuk customer ini?`,
                                type: 'success',
                                confirmText: this.lang === 'zh' ? '立即新建报价单' : 'Buat Penawaran Harga',
                                cancelText: this.lang === 'zh' ? '稍后处理' : 'Nanti Saja'
                            });
                            if (wantQuotation) {
                                this.createQuotationFromLead(found || this.activeLead, result.customer);
                                return;
                            }
                        }
                    } else {
                        // Rollback optimistic update on error
                        if (fromStage) {
                            const found = this.leads.find(l => l.id == leadId);
                            if (found) found.pipeline_stage = fromStage;
                            if (this.activeLead && this.activeLead.id == leadId) {
                                this.activeLead.pipeline_stage = fromStage;
                            }
                        }
                        alert(result.message || 'Gagal memindahkan stage.');
                    }
                } catch(e) {
                    // Rollback optimistic update on error
                    if (fromStage) {
                        const found = this.leads.find(l => l.id == leadId);
                        if (found) found.pipeline_stage = fromStage;
                        if (this.activeLead && this.activeLead.id == leadId) {
                            this.activeLead.pipeline_stage = fromStage;
                        }
                    }
                    alert('Terjadi kesalahan pada server.');
                }
            },

            // ==========================================
            // DETAIL DRAWER
            // ==========================================
            async openLeadDetail(leadId) {
                try {
                    const res = await fetch(`/erp/crm/leads/${leadId}/details`, {
                        headers: { 'Accept': 'application/json' }
                    });
                    const result = await res.json();
                    if (result.success) {
                        this.activeLead = result.data;
                        this.detailDrawerOpen = true;
                    }
                } catch(e) {
                    alert('Gagal mengambil detail leads.');
                }
            },

            // ==========================================
            // MODALS & FORMS
            // ==========================================
            setRowDealType(row, type) {
                row.deal_type = type;
                this.syncDealTypeFromItems();
            },

            setMainDealType(type) {
                this.leadForm.deal_type = type;
                if (type === 'rental') {
                    if (this.leadForm.items && this.leadForm.items.length > 0) {
                        this.leadForm.items.forEach(it => it.deal_type = 'rental');
                    }
                } else if (type === 'sales') {
                    if (this.leadForm.items && this.leadForm.items.length > 0) {
                        this.leadForm.items.forEach(it => it.deal_type = 'sales');
                    }
                }
                this.recalculateLeadEstimatedValue();
            },

            syncDealTypeFromItems() {
                if (!this.leadForm.items || this.leadForm.items.length === 0) return;
                const hasRental = this.leadForm.items.some(it => (it.deal_type || 'rental') === 'rental');
                const hasSales = this.leadForm.items.some(it => it.deal_type === 'sales');
                if (hasRental && hasSales) {
                    this.leadForm.deal_type = 'both';
                } else if (hasSales) {
                    this.leadForm.deal_type = 'sales';
                } else {
                    this.leadForm.deal_type = 'rental';
                }
                this.recalculateLeadEstimatedValue();
            },

            addLeadItem() {
                if (!this.leadForm.items) this.leadForm.items = [];
                const defaultDealType = this.leadForm.deal_type === 'sales' ? 'sales' : 'rental';
                this.leadForm.items.push({
                    deal_type: defaultDealType,
                    name: '',
                    specification: '',
                    quantity: 1,
                    duration: 1,
                    duration_unit: 'Bulan',
                    unit_price: 0,
                    image: '',
                    image_preview: ''
                });
                this.syncDealTypeFromItems();
            },

            removeLeadItem(index) {
                const defaultDealType = this.leadForm.deal_type === 'sales' ? 'sales' : 'rental';
                if (this.leadForm.items && this.leadForm.items.length > 1) {
                    this.leadForm.items.splice(index, 1);
                } else if (this.leadForm.items && this.leadForm.items.length === 1) {
                    this.leadForm.items[0] = {
                        deal_type: defaultDealType,
                        name: '',
                        specification: '',
                        quantity: 1,
                        duration: 1,
                        duration_unit: 'Bulan',
                        unit_price: 0,
                        image: '',
                        image_preview: ''
                    };
                }
                this.syncDealTypeFromItems();
            },

            uploadLeadRowImage(idx, event) {
                const file = event.target.files[0];
                if (!file) return;
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.leadForm.items[idx].image_preview = e.target.result;
                };
                reader.readAsDataURL(file);
            },

            recalculateLeadEstimatedValue() {
                let sum = 0;
                if (this.leadForm.items && this.leadForm.items.length > 0) {
                    for (const it of this.leadForm.items) {
                        const q = Number(it.quantity) || 1;
                        const p = Number(it.unit_price) || 0;
                        const isRental = (it.deal_type || 'rental') === 'rental';
                        if (isRental) {
                            const dur = Number(it.duration) || 1;
                            sum += (q * dur * p);
                        } else {
                            sum += (q * p);
                        }
                    }
                }
                if (sum > 0) {
                    this.leadForm.estimated_value = sum;
                }
            },

            openCreateLeadModal(defaultStage = 'new') {
                this.leadModalMode = 'create';
                this.leadForm = {
                    id: null,
                    company_name: '',
                    industry: '',
                    source: 'Website',
                    entry_date: new Date().toISOString().split('T')[0],
                    sales_id: this.salesUsers[0]?.id || 1,
                    website: '',
                    address: '',
                    pic_name: '',
                    pic_title: '',
                    pic_phone: '',
                    pic_email: '',
                    deal_type: 'rental',
                    items: [
                        {
                            deal_type: 'rental',
                            name: '',
                            specification: '',
                            quantity: 1,
                            duration: 1,
                            duration_unit: 'Bulan',
                            unit_price: 0,
                            image: '',
                            image_preview: ''
                        }
                    ],
                    product_interest: '',
                    specification: '',
                    quantity: 1,
                    rental_duration: '',
                    project_location: '',
                    estimated_value: 0,
                    expected_closing_date: '',
                    customer_notes: '',
                    pipeline_stage: defaultStage || 'new',
                    priority: 'normal',
                    internal_notes: ''
                };
                this.leadModalOpen = true;
            },

            openEditLeadModal(lead) {
                this.leadModalMode = 'edit';
                let loadedItems = [];
                if (lead.items && Array.isArray(lead.items) && lead.items.length > 0) {
                    loadedItems = JSON.parse(JSON.stringify(lead.items)).map(it => ({
                        ...it,
                        deal_type: it.deal_type || (lead.deal_type === 'sales' ? 'sales' : 'rental')
                    }));
                } else if (lead.product_interest) {
                    loadedItems = [
                        {
                            deal_type: lead.deal_type === 'sales' ? 'sales' : 'rental',
                            name: lead.product_interest,
                            specification: lead.specification || '',
                            quantity: lead.quantity || 1,
                            duration: 1,
                            duration_unit: 'Bulan',
                            unit_price: lead.estimated_value ? (lead.estimated_value / (lead.quantity || 1)) : 0,
                            image: '',
                            image_preview: ''
                        }
                    ];
                } else {
                    loadedItems = [
                        {
                            deal_type: lead.deal_type === 'sales' ? 'sales' : 'rental',
                            name: '',
                            specification: '',
                            quantity: 1,
                            duration: 1,
                            duration_unit: 'Bulan',
                            unit_price: 0,
                            image: '',
                            image_preview: ''
                        }
                    ];
                }

                this.leadForm = {
                    id: lead.id,
                    company_name: lead.company_name,
                    industry: lead.industry || '',
                    source: lead.source || 'Website',
                    entry_date: lead.entry_date ? lead.entry_date.split('T')[0] : '',
                    sales_id: lead.sales_id || (this.salesUsers[0]?.id || 1),
                    website: lead.website || '',
                    address: lead.address || '',
                    pic_name: lead.pic_name,
                    pic_title: lead.pic_title || '',
                    pic_phone: lead.pic_phone,
                    pic_email: lead.pic_email || '',
                    deal_type: lead.deal_type || 'rental',
                    items: loadedItems,
                    product_interest: lead.product_interest || '',
                    specification: lead.specification || '',
                    quantity: lead.quantity || 1,
                    rental_duration: lead.rental_duration || '',
                    project_location: lead.project_location || '',
                    estimated_value: lead.estimated_value || 0,
                    expected_closing_date: lead.expected_closing_date ? lead.expected_closing_date.split('T')[0] : '',
                    customer_notes: lead.customer_notes || '',
                    pipeline_stage: lead.pipeline_stage || 'new',
                    priority: lead.priority || 'normal',
                    internal_notes: lead.internal_notes || ''
                };
                this.syncDealTypeFromItems();
                this.leadModalOpen = true;
            },

            async submitLeadForm() {
                this.isSubmitting = true;
                try {
                    const url = this.leadModalMode === 'edit'
                        ? `/erp/crm/leads/${this.leadForm.id}/update`
                        : '{{ route("erp.crm.leads.store") }}';

                    const res = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(this.leadForm)
                    });
                    const result = await res.json();
                    if (result.success) {
                        alert(result.message);
                        window.location.reload();
                    } else {
                        alert('Gagal: ' + (result.message || 'Periksa kembali input form.'));
                    }
                } catch(e) {
                    alert('Terjadi kesalahan pada server.');
                } finally {
                    this.isSubmitting = false;
                }
            },

            openActivityModal(lead) {
                this.activeLead = lead;
                this.activityForm = {
                    activity_type: 'Telepon',
                    activity_date: new Date().toISOString().slice(0, 16),
                    summary: '',
                    communication_notes: '',
                    attachment: ''
                };
                this.activityModalOpen = true;
            },

            async submitActivityForm() {
                try {
                    const res = await fetch(`/erp/crm/leads/${this.activeLead.id}/activities`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(this.activityForm)
                    });
                    const result = await res.json();
                    if (result.success) {
                        alert(result.message);
                        this.activityModalOpen = false;
                        if (this.detailDrawerOpen) {
                            this.openLeadDetail(this.activeLead.id);
                        } else {
                            window.location.reload();
                        }
                    } else {
                        alert('Gagal: ' + result.message);
                    }
                } catch(e) {
                    alert('Terjadi kesalahan pada server.');
                }
            },

            openFollowUpModal(lead) {
                this.activeLead = lead;
                this.followUpForm = {
                    follow_up_type: 'Telepon',
                    scheduled_at: new Date(Date.now() + 86400000).toISOString().slice(0, 16),
                    user_id: lead.sales_id || (this.salesUsers[0]?.id || 1),
                    purpose_notes: ''
                };
                this.followUpModalOpen = true;
            },

            async submitFollowUpForm() {
                try {
                    const res = await fetch(`/erp/crm/leads/${this.activeLead.id}/follow-ups`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(this.followUpForm)
                    });
                    const result = await res.json();
                    if (result.success) {
                        alert(result.message);
                        this.followUpModalOpen = false;
                        if (this.detailDrawerOpen) {
                            this.openLeadDetail(this.activeLead.id);
                        } else {
                            window.location.reload();
                        }
                    } else {
                        alert('Gagal: ' + result.message);
                    }
                } catch(e) {
                    alert('Terjadi kesalahan pada server.');
                }
            },

            openCompleteFollowUpModal(followUpId, companyName) {
                this.completeTargetId = followUpId;
                this.completeTargetCompanyName = companyName;
                this.completeForm = {
                    result_notes: '',
                    next_follow_up_type: 'Telepon',
                    next_follow_up_date: '',
                    next_follow_up_notes: ''
                };
                this.completeFollowUpModalOpen = true;
            },

            async submitCompleteFollowUp() {
                try {
                    const res = await fetch(`/erp/crm/follow-ups/${this.completeTargetId}/complete`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(this.completeForm)
                    });
                    const result = await res.json();
                    if (result.success) {
                        alert(result.message);
                        this.completeFollowUpModalOpen = false;
                        window.location.reload();
                    } else {
                        alert('Gagal: ' + result.message);
                    }
                } catch(e) {
                    alert('Terjadi kesalahan pada server.');
                }
            },

            async convertLeadToCustomer(leadId) {
                const okConvert = await window.confirmDialog({
                    title: this.lang === 'zh' ? '确认转为正式客户' : 'Konversi ke Master Customer',
                    message: this.lang === 'zh' ? '您确定要将此线索直接转为系统主数据中的正式客户吗？' : 'Apakah Anda yakin ingin mengonversi prospek ini menjadi Customer Resmi di Master Data?',
                    type: 'warning',
                    confirmText: this.lang === 'zh' ? '确认转为客户' : 'Ya, Konversi',
                    cancelText: this.lang === 'zh' ? '取消' : 'Batal'
                });
                if (!okConvert) return;

                try {
                    const res = await fetch(`/erp/crm/leads/${leadId}/convert-customer`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                    const result = await res.json();
                    if (result.success) {
                        alert(result.message);
                        if (this.detailDrawerOpen) {
                            this.openLeadDetail(leadId);
                        } else {
                            window.location.reload();
                        }
                    } else {
                        alert('Gagal: ' + result.message);
                    }
                } catch(e) {
                    alert('Terjadi kesalahan pada server.');
                }
            },

            async createQuotationFromLead(lead, customer = null) {
                if (!lead) return;

                let targetCustomer = customer;

                // Auto-register lead into Master Data Customer if not provided
                if (!targetCustomer) {
                    try {
                        const res = await fetch(`/erp/crm/leads/${lead.id}/prepare-quotation`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });
                        const result = await res.json();
                        if (result.success && result.customer) {
                            targetCustomer = result.customer;
                            lead.converted_customer_id = targetCustomer.id;
                            if (result.lead && result.lead.pipeline_stage) {
                                lead.pipeline_stage = result.lead.pipeline_stage;
                            }
                        }
                    } catch (e) {
                        console.error('Failed to auto-register customer from CRM lead', e);
                    }
                }

                const custId = targetCustomer ? targetCustomer.id : (lead.converted_customer_id || null);
                const custName = targetCustomer ? targetCustomer.name : lead.company_name;
                const custCode = targetCustomer ? targetCustomer.code : null;

                // Dispatch global event to open quotation modal immediately with registered customer selected
                window.dispatchEvent(new CustomEvent('open-quotation-modal', {
                    detail: {
                        customer_id: custId,
                        customer_name: custName,
                        customer_code: custCode,
                        pic_name: targetCustomer ? targetCustomer.pic_name : (lead.pic_name || ''),
                        phone: targetCustomer ? targetCustomer.phone : (lead.pic_phone || ''),
                        shipping_address: (targetCustomer && targetCustomer.shipping_address) ? targetCustomer.shipping_address : (lead.address || lead.project_location || ''),
                        product_name: lead.product_interest || '',
                        specification: lead.specification || '',
                        items: lead.items || [],
                        deal_type: lead.deal_type || 'rental'
                    }
                }));
            }
        };
    }
</script>
@endpush
@endsection
