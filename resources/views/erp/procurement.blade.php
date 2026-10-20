@extends('layouts.erp')

@section('title', 'Procurement & Vendor - Keylock Indonesia')

@section('breadcrumbs')
    <a href="{{ route('erp.index') }}" class="hover:text-slate-800">Home</a>
    <span>›</span>
    <span class="text-slate-800 font-semibold" x-text="lang === 'zh' ? '采购与供应商管理' : 'Procurement & Vendor'">Procurement & Vendor</span>
@endsection

@section('content')
<div x-data="erpProcurementManager()">

    <!-- ========================================================================= -->
    <!-- MOBILE HEADER & TOOLBAR (SERAGAM PENAWARAN & ORDER)                       -->
    <!-- ========================================================================= -->
    <div class="block md:hidden mb-4 space-y-3">
        <!-- 1. Header: Back button + Title & Subtitle + Tambah PO Button -->
        <div class="flex items-center space-x-3">
            <a href="{{ route('erp.index') }}" 
               class="w-10 h-10 rounded-full border border-slate-200 bg-white flex items-center justify-center text-slate-700 hover:bg-slate-50 active:scale-95 transition shadow-2xs shrink-0"
               aria-label="Kembali ke Dashboard">
                <svg class="w-5 h-5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div class="min-w-0 flex-1">
                <h1 class="text-lg font-black text-[#17243B] tracking-tight leading-tight" x-text="lang === 'zh' ? '采购与供应商管理' : 'Procurement & Vendor'">
                    Procurement & Vendor
                </h1>
                <p class="text-xs text-slate-500 truncate" x-text="lang === 'zh' ? '管理外部设备转租、采购及供应商发票' : 'Kelola PO sewa lempar vendor, beli unit, dan tagihan'">
                    Kelola PO sewa lempar vendor, beli unit, dan tagihan
                </p>
            </div>
            <!-- Quick Add Button on Mobile Header -->
            <button type="button" 
                    @click="openCreateModal()" 
                    class="w-9 h-9 rounded-2xl bg-blue-600 text-white flex items-center justify-center shadow-xs active:scale-95 transition shrink-0"
                    title="Buat PO / Sub-Rent Baru">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
            </button>
        </div>

        <!-- 2. Search Field + Filter Button (Seragam Penawaran & Order) -->
        <div class="flex items-center space-x-2">
            <div class="relative flex-1">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input type="text" 
                       x-model="searchQuery" 
                       @input="onSearchInput()"
                       @keydown.enter.prevent="submitSearch()"
                       :placeholder="lang === 'zh' ? '搜索 PO 编号、供应商、关联合同...' : 'Cari nomor PO, vendor, proyek customer...'"
                       placeholder="Cari nomor PO, vendor, proyek customer..." 
                       class="w-full bg-white border border-slate-200/90 rounded-2xl pl-10 pr-4 py-2.5 text-xs text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-blue-500 shadow-2xs">
            </div>
            <button type="button" 
                    @click="mobileFilterOpen = !mobileFilterOpen"
                    class="w-10 h-10 rounded-2xl border border-slate-200/90 bg-white flex items-center justify-center text-slate-600 hover:text-slate-900 shadow-2xs transition active:scale-95 shrink-0"
                    :class="mobileFilterOpen || (statusFilter && statusFilter !== 'all') ? 'border-blue-500 text-blue-600 bg-blue-50/50' : ''"
                    aria-label="Filter Lanjutan">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                </svg>
            </button>
        </div>

        <!-- Collapsible Mobile Advanced Status Filter -->
        <div x-show="mobileFilterOpen" 
             x-cloak 
             x-transition 
             class="bg-white p-3.5 rounded-2xl border border-slate-200 shadow-xs space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-700">Filter Lanjutan:</span>
                <button type="button" @click="clearSearch(); mobileFilterOpen = false" class="text-[11px] text-blue-600 font-semibold hover:underline">
                    Reset Filter
                </button>
            </div>
            <div class="space-y-2">
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Status PO</label>
                    <select x-model="statusFilter" @change="submitSearch()" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold text-slate-700 outline-none focus:border-blue-500">
                        <option value="all">Semua Status ({{ $totalPoCount }})</option>
                        <option value="approved">Disetujui / Aktif</option>
                        <option value="completed">Selesai</option>
                        <option value="cancelled">Dibatalkan</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- 3. Dua Tab Status Utama di Mobile (Sewa Sub-Rent & Semua Pengadaan) -->
        <div class="grid grid-cols-2 gap-2 py-0.5">
            <a href="{{ route('erp.procurement.index', array_merge(request()->except(['page']), ['type' => 'sub_rental'])) }}" 
               class="py-2.5 px-3 rounded-xl text-xs text-center transition flex items-center justify-center space-x-1.5 {{ $typeFilter === 'sub_rental' ? 'bg-[#142A57] text-white font-bold shadow-xs' : 'bg-white text-slate-700 hover:text-slate-900 border border-[#E5EAF2] font-semibold shadow-2xs' }}"
               aria-label="Tab Sewa Sub-Rent">
                <svg class="w-3.5 h-3.5 {{ $typeFilter === 'sub_rental' ? 'text-amber-300' : 'text-amber-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                </svg>
                <span x-text="lang === 'zh' ? '转租订单' : 'Sewa (Sub-Rent)'">Sewa (Sub-Rent)</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] font-black {{ $typeFilter === 'sub_rental' ? 'bg-white/20 text-white' : 'bg-amber-100 text-amber-800' }}">
                    {{ $subRentalCount ?? 0 }}
                </span>
            </a>

            <a href="{{ route('erp.procurement.index', array_merge(request()->except(['page']), ['type' => 'all'])) }}" 
               class="py-2.5 px-3 rounded-xl text-xs text-center transition flex items-center justify-center space-x-1.5 {{ $typeFilter === 'all' ? 'bg-[#142A57] text-white font-bold shadow-xs' : 'bg-white text-slate-700 hover:text-slate-900 border border-[#E5EAF2] font-semibold shadow-2xs' }}"
               aria-label="Tab Semua Order PO">
                <svg class="w-3.5 h-3.5 {{ $typeFilter === 'all' ? 'text-blue-300' : 'text-slate-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                <span x-text="lang === 'zh' ? '全部采购' : 'Semua PO'">Semua PO</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] font-black {{ $typeFilter === 'all' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-800' }}">
                    {{ $totalPoCount }}
                </span>
            </a>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- DESKTOP HEADER & CONTROLS (VISIBLE ON SCREENS >= MD)                      -->
    <!-- ========================================================================= -->
    <div class="hidden md:block">
        <!-- 1. PAGE HEADER                                                            -->
        <!-- ========================================================================= -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-2xl bg-amber-400 text-slate-950 flex items-center justify-center font-black shadow-xs">
                    <svg class="w-5 h-5 text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-900 tracking-tight" x-text="lang === 'zh' ? '采购与供应商管理 (Procurement)' : 'Procurement & Vendor'">
                        Procurement & Vendor
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5" x-text="lang === 'zh' ? '管理外部设备转租、商品采购、供应商发票及交易成本 (HPP)。' : 'Pencatatan pembelian barang, sewa lempar unit vendor (sub-rent), harga modal (HPP), dan tagihan vendor.'">
                        Pencatatan pembelian barang, sewa lempar unit vendor (sub-rent), harga modal (HPP), dan tagihan vendor.
                    </p>
                </div>
            </div>
        </div>

        <!-- Top Right Action: Tambah Pengadaan / PO Baru -->
        <button type="button" 
                @click="openCreateModal()" 
                class="bg-blue-600 hover:bg-blue-700 text-white font-extrabold px-5 py-2.5 rounded-full shadow-md hover:shadow-lg text-xs transition flex items-center space-x-2 self-start md:self-auto active:scale-95">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
            <span x-text="lang === 'zh' ? '创建采购 / 转租订单' : 'Buat PO / Sub-Rent Baru'">Buat PO / Sub-Rent Baru</span>
        </button>
    </div>

    <!-- ========================================================================= -->
    <!-- 2. KPI METRICS CARDS                                                      -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Card 1: Total PO -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1" x-text="lang === 'zh' ? '采购订单总数' : 'Total Order Pengadaan'">Total Order Pengadaan</p>
                <h3 class="text-2xl font-black text-slate-900">{{ $totalPoCount }}</h3>
                <p class="text-[11px] text-slate-500 mt-0.5" x-text="lang === 'zh' ? '包含转租与现货采购' : 'PO Aktif & Selesai'"></p>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
        </div>

        <!-- Card 2: Total Nilai PO -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1" x-text="lang === 'zh' ? '采购与成本总额' : 'Total Nilai Pengadaan'">Total Nilai Pengadaan</p>
                <h3 class="text-xl font-black text-slate-900 font-mono">Rp {{ number_format($totalPoAmount, 0, ',', '.') }}</h3>
                <p class="text-[11px] text-emerald-600 font-semibold mt-0.5" x-text="lang === 'zh' ? '经批准的有效成本' : 'Disetujui & Berjalan'"></p>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>

        <!-- Card 3: Sub-Rent Aktif -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1" x-text="lang === 'zh' ? '在租转租设备 (Sub-Rent)' : 'Unit Sewa Lempar Aktif'">Unit Sewa Lempar Aktif</p>
                <h3 class="text-2xl font-black text-amber-500">{{ $activeSubRentCount ?? 0 }}</h3>
                <p class="text-[11px] text-slate-500 mt-0.5" x-text="lang === 'zh' ? '由外部供应商支持' : 'Unit vendor melayani customer'"></p>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
            </div>
        </div>

        <!-- Card 4: Tagihan Vendor Menunggu -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1" x-text="lang === 'zh' ? '待付供应商账单' : 'Tagihan Vendor Belum Lunas'">Tagihan Vendor Belum Lunas</p>
                <h3 class="text-xl font-black text-rose-600 font-mono">Rp {{ number_format($pendingBillsAmount, 0, ',', '.') }}</h3>
                <p class="text-[11px] text-slate-500 mt-0.5" x-text="'{{ $pendingBillsCount }}' + (lang === 'zh' ? ' 笔待付款账单' : ' Tagihan Vendor')"></p>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 3. FILTER PILLS BAR (4 TIPE PENGADAAN + SEMUA)                            -->
    <!-- ========================================================================= -->
    <div class="flex flex-wrap items-center gap-3 mb-5">
        <!-- Semua PO Pill -->
        <a href="{{ route('erp.procurement.index', ['type' => 'all']) }}" 
           class="px-4 py-2 rounded-2xl text-xs font-bold transition flex items-center space-x-2.5 {{ $typeFilter === 'all' ? 'bg-white text-slate-900 shadow-sm border border-slate-200/90' : 'bg-slate-100/80 text-slate-600 hover:bg-white hover:text-slate-900 border border-transparent' }}">
            <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            <span x-text="lang === 'zh' ? '所有采购 / 转租' : 'Semua Order Pengadaan'">Semua Order Pengadaan</span>
            <span class="w-5 h-5 rounded-full bg-slate-100 text-slate-800 text-[10px] flex items-center justify-center font-black">
                {{ $totalPoCount }}
            </span>
        </a>

        <!-- 1. Sewa (Sub-Rent) Pill -->
        <a href="{{ route('erp.procurement.index', ['type' => 'sub_rental']) }}" 
           class="px-4 py-2 rounded-2xl text-xs font-bold transition flex items-center space-x-2.5 {{ $typeFilter === 'sub_rental' ? 'bg-white text-slate-900 shadow-sm border border-slate-200/90' : 'bg-slate-100/80 text-slate-600 hover:bg-white hover:text-slate-900 border border-transparent' }}">
            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
            <span x-text="lang === 'zh' ? '转租订单 (Sewa)' : 'Sewa (Sub-Rent)'">Sewa (Sub-Rent)</span>
            <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[10px] font-black">
                {{ $subRentalCount ?? 0 }}
            </span>
        </a>

        <!-- 2. Pembelian Pill -->
        <a href="{{ route('erp.procurement.index', ['type' => 'purchase_unit']) }}" 
           class="px-4 py-2 rounded-2xl text-xs font-bold transition flex items-center space-x-2.5 {{ $typeFilter === 'purchase_unit' ? 'bg-white text-slate-900 shadow-sm border border-slate-200/90' : 'bg-slate-100/80 text-slate-600 hover:bg-white hover:text-slate-900 border border-transparent' }}">
            <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
            <span x-text="lang === 'zh' ? '设备购置 (Pembelian)' : 'Pembelian'">Pembelian</span>
            <span class="w-5 h-5 rounded-full bg-purple-100 text-purple-800 text-[10px] flex items-center justify-center font-black">
                {{ $purchaseCount ?? 0 }}
            </span>
        </a>

        <!-- 3. Jasa Pill -->
        <a href="{{ route('erp.procurement.index', ['type' => 'service']) }}" 
           class="px-4 py-2 rounded-2xl text-xs font-bold transition flex items-center space-x-2.5 {{ $typeFilter === 'service' ? 'bg-white text-slate-900 shadow-sm border border-slate-200/90' : 'bg-slate-100/80 text-slate-600 hover:bg-white hover:text-slate-900 border border-transparent' }}">
            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg>
            <span x-text="lang === 'zh' ? '服务与物流 (Jasa)' : 'Jasa'">Jasa</span>
            <span class="w-5 h-5 rounded-full bg-blue-100 text-blue-800 text-[10px] flex items-center justify-center font-black">
                {{ $serviceCount ?? 0 }}
            </span>
        </a>

        <!-- 4. Non Order / OPEX Pill -->
        <a href="{{ route('erp.procurement.index', ['type' => 'operational']) }}" 
           class="px-4 py-2 rounded-2xl text-xs font-bold transition flex items-center space-x-2.5 {{ $typeFilter === 'operational' ? 'bg-white text-slate-900 shadow-sm border border-slate-200/90' : 'bg-slate-100/80 text-slate-600 hover:bg-white hover:text-slate-900 border border-transparent' }}">
            <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            <span x-text="lang === 'zh' ? '日常运营与 IT (Non Order / OPEX)' : 'Non Order / OPEX'">Non Order / OPEX</span>
            <span class="w-5 h-5 rounded-full bg-orange-100 text-orange-800 text-[10px] flex items-center justify-center font-black">
                {{ $operationalCount ?? 0 }}
            </span>
        </a>
    </div>

    <!-- ========================================================================= -->
    <!-- 4. SEARCH & CONTROLS BAR                                                  -->
    <!-- ========================================================================= -->
    <div class="bg-white rounded-3xl p-3 border border-slate-200/80 shadow-xs mb-6 flex flex-col md:flex-row items-center justify-between gap-3">
        <div class="relative w-full md:w-96">
            <svg class="w-4 h-4 text-slate-400 absolute left-4 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input type="text" 
                   x-model="searchQuery" 
                   @input="onSearchInput()"
                   @keydown.enter.prevent="submitSearch()"
                   :placeholder="lang === 'zh' ? '搜索 PO 编号、供应商、关联合同或 PIC...' : 'Cari nomor PO, vendor, proyek customer, PIC...'"
                   placeholder="Cari nomor PO, vendor, proyek customer, PIC..." 
                   class="w-full bg-transparent pl-11 pr-4 py-2 text-xs text-slate-900 placeholder:text-slate-400 focus:outline-none">
        </div>

        <div class="flex items-center space-x-2.5 self-end md:self-auto w-full md:w-auto justify-end">
            <select x-model="statusFilter" @change="submitSearch()" class="bg-slate-50 border border-slate-200 rounded-2xl px-4 py-2 text-xs font-semibold text-slate-700 outline-none focus:border-blue-400">
                <option value="all" x-text="lang === 'zh' ? '所有状态' : 'Semua Status'">Semua Status</option>
                <option value="approved" x-text="lang === 'zh' ? '已批准 / 执行中' : 'Disetujui / Aktif'">Disetujui / Aktif</option>
                <option value="completed" x-text="lang === 'zh' ? '已完成' : 'Selesai'">Selesai</option>
                <option value="cancelled" x-text="lang === 'zh' ? '已取消' : 'Dibatalkan'">Dibatalkan</option>
            </select>

            <button type="button" @click="clearSearch()" class="w-9 h-9 rounded-2xl bg-slate-50 hover:bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-500 hover:text-slate-800 transition" title="Reset / Refresh Data">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            </button>
        </div>
    </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 5. PROCUREMENT TABLE & MOBILE CARDS                                       -->
    <!-- ========================================================================= -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden mb-8">
        <!-- DESKTOP TABLE VIEW (VISIBLE ON SCREENS >= MD, HIDDEN ON MOBILE) -->
        <div class="hidden md:block overflow-x-auto min-h-[360px]">
            <table class="w-full text-left border-collapse text-xs whitespace-nowrap">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/90 text-slate-400 font-black uppercase text-[10px] tracking-wider">
                        <th class="py-3.5 px-3 w-16 text-center">#</th>
                        <th class="py-3.5 px-4 w-44 font-black text-slate-700" x-text="lang === 'zh' ? '采购单号' : 'No. PO / Sub-Rent'">No. PO / Sub-Rent</th>
                        <th class="py-3.5 px-4 min-w-[180px]" x-text="lang === 'zh' ? '供应商 (Vendor)' : 'Vendor & Mitra'">Vendor & Mitra</th>
                        <th class="py-3.5 px-4 w-36" x-text="lang === 'zh' ? '采购类型' : 'Tipe Pengadaan'">Tipe Pengadaan</th>
                        <th class="py-3.5 px-4 min-w-[180px]" x-text="lang === 'zh' ? '关联合同 (客户 SO/RO)' : 'Proyek Pelanggan (SO/RO)'">Proyek Pelanggan (SO/RO)</th>
                        <th class="py-3.5 px-4 w-36 font-black text-slate-700 bg-slate-100/60" x-text="lang === 'zh' ? '供应商原始发票' : 'Invoice Vendor'">Invoice Vendor</th>
                        <th class="py-3.5 px-4 w-36 font-black text-purple-900 bg-purple-50/40" x-text="lang === 'zh' ? '系统账单号' : 'No. Bill'">No. Bill</th>
                        <th class="py-3.5 px-4 w-36 text-right" x-text="lang === 'zh' ? '采购总额 (HPP)' : 'Nilai Total (HPP)'">Nilai Total (HPP)</th>
                        <th class="py-3.5 px-4 w-28 text-center" x-text="lang === 'zh' ? '订单状态' : 'Status PO'">Status PO</th>
                        <th class="py-3.5 px-4 w-28 text-center" x-text="lang === 'zh' ? '账单状态' : 'Status Tagihan'">Status Tagihan</th>
                        <th class="py-3.5 px-4 w-28 text-center sticky right-0 bg-slate-50 z-10 shadow-[-6px_0_8px_-4px_rgba(0,0,0,0.06)] border-l border-slate-200" x-text="lang === 'zh' ? '操作' : 'Aksi'">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($purchaseOrders as $index => $po)
                        @php
                            $itemNamesStr = strtolower($po->items->pluck('description')->join(' '));
                            $vendorBill = $po->invoices ? $po->invoices->where('invoice_type', 'vendor_bill')->first() : null;
                            $hasBill = ($vendorBill !== null) || in_array($po->invoice_status, ['invoiced', 'partial', 'paid']);
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition group"
                            x-show="matchesFilter('{{ strtolower($po->order_number) }}', '{{ strtolower($po->contact->name ?? '') }}', '{{ strtolower($po->parent->order_number ?? '') }}', '{{ addslashes($itemNamesStr) }}', '{{ $po->status }}', '{{ strtolower($vendorBill->vendor_ref_no ?? '') }}', '{{ strtolower($vendorBill->invoice_number ?? '') }}')">
                            
                            <!-- 1. Index with Chevron Expand Toggle -->
                            <td class="py-4 px-3 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center space-x-1.5">
                                    <button type="button" 
                                            @click="toggleExpand({{ $po->id }})" 
                                            class="w-7 h-7 rounded-xl flex items-center justify-center transition border"
                                            :class="isExpanded({{ $po->id }}) ? 'bg-amber-100 border-amber-300 text-amber-800 rotate-90 shadow-xs' : 'bg-slate-50 hover:bg-slate-100 border-slate-200 text-slate-400 hover:text-slate-700'"
                                            :title="isExpanded({{ $po->id }}) ? (lang === 'zh' ? '收起产品明细' : 'Tutup Rincian Produk') : (lang === 'zh' ? '展开产品明细' : 'Lihat Rincian Produk')">
                                        <svg class="w-3.5 h-3.5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                                    </button>
                                    <span class="font-mono text-[11px] text-slate-400 font-semibold">{{ ($purchaseOrders->firstItem() ?? 1) + $index }}</span>
                                </div>
                            </td>

                            <!-- 2. No PO / Sub-Rent -->
                            <td class="py-4 px-4 font-mono">
                                <div class="inline-flex items-center space-x-1.5">
                                    <button type="button" 
                                            @click="toggleExpand({{ $po->id }})" 
                                            class="font-mono font-black text-slate-900 hover:text-amber-600 transition flex items-center space-x-1.5 text-left text-xs"
                                            title="Klik untuk lihat rincian produk">
                                        <span>{{ $po->order_number }}</span>
                                    </button>
                                    <a href="{{ route('erp.procurement.pdf', $po->id) }}" 
                                       target="_blank"
                                       class="p-1 rounded-md text-slate-400 hover:text-slate-800 hover:bg-slate-100 transition shadow-2xs border border-slate-200/60"
                                       title="Cetak PO ({{ $po->order_number }})">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                    </a>
                                </div>
                                <div class="text-[10px] text-slate-400 mt-0.5">
                                    {{ $po->order_date ? $po->order_date->format('d/m/Y') : '-' }}
                                </div>
                            </td>

                            <!-- 3. Vendor & Mitra -->
                            <td class="py-4 px-4">
                                <div class="font-bold text-slate-800 truncate max-w-[170px]" title="{{ $po->contact->name ?? '-' }}">
                                    {{ $po->contact->name ?? '-' }}
                                </div>
                                <div class="text-[10px] text-slate-400">
                                    PIC: {{ $po->contact->pic_name ?: '-' }}
                                </div>
                            </td>

                            <!-- 4. Tipe Pengadaan (4 Kategori) -->
                            <td class="py-4 px-4 whitespace-nowrap">
                                @if($po->procurement_type === 'sub_rental')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-amber-50 text-amber-800 border border-amber-200/80 inline-flex items-center space-x-1">
                                        <span x-text="lang === 'zh' ? '转租设备 (Sewa)' : 'Sewa (Sub-Rent)'">Sewa (Sub-Rent)</span>
                                    </span>
                                @elseif($po->procurement_type === 'purchase_unit')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-purple-50 text-purple-800 border border-purple-200/80 inline-flex items-center space-x-1">
                                        <span x-text="lang === 'zh' ? '购买设备' : 'Pembelian'">Pembelian</span>
                                    </span>
                                @elseif(in_array($po->procurement_type, ['service', 'logistics', 'sparepart']))
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-blue-50 text-blue-800 border border-blue-200/80 inline-flex items-center space-x-1">
                                        <span x-text="lang === 'zh' ? '服务物流 (Jasa)' : 'Jasa'">Jasa</span>
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-orange-50 text-orange-800 border border-orange-200/80 inline-flex items-center space-x-1">
                                        <span x-text="lang === 'zh' ? '日常运营 (OPEX)' : 'Non Order / OPEX'">Non Order / OPEX</span>
                                    </span>
                                @endif
                            </td>

                            <!-- 5. Proyek Pelanggan (SO/RO) - Clickable Link to Orders -->
                            <td class="py-4 px-4">
                                @if($po->parent)
                                    <div class="flex items-center space-x-1.5">
                                        <a href="{{ route('erp.orders.index', ['type' => 'transaksi', 'search' => $po->parent->order_number]) }}" 
                                           class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-md font-mono font-black text-[11px] transition shadow-2xs {{ $po->parent->order_type === 'rental_order' ? 'bg-blue-100 text-blue-800 hover:bg-blue-200 border border-blue-300' : 'bg-purple-100 text-purple-800 hover:bg-purple-200 border border-purple-300' }}"
                                           title="Klik untuk membuka transaksi {{ $po->parent->order_number }} di menu Penawaran & Order">
                                            <span>{{ $po->parent->order_number }}</span>
                                            <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                        </a>
                                    </div>
                                    <div class="text-[10px] text-slate-700 font-bold truncate max-w-[150px] mt-0.5" title="{{ $po->parent->contact->name ?? '' }}">
                                        {{ $po->parent->contact->name ?? '-' }}
                                    </div>
                                @else
                                    <div class="inline-flex items-center space-x-1 px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 border border-slate-200/80 font-bold text-[10px]">
                                        <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                        <span x-text="lang === 'zh' ? '独立运营 (OPEX)' : 'Non-Order / OPEX'">Non-Order / OPEX</span>
                                    </div>
                                @endif
                            </td>

                            <!-- 5b. Invoice Vendor (No. Surat Tagihan Asli Vendor) -->
                            <td class="py-4 px-4 font-mono text-xs">
                                @if($vendorBill && $vendorBill->vendor_ref_no)
                                    <a href="{{ route('erp.finance.index', ['tab' => 'bills', 'search' => $vendorBill->invoice_number]) }}" 
                                       class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold border border-slate-300 transition text-[11px] group/inv"
                                       title="Buka Tagihan {{ $vendorBill->vendor_ref_no }} di menu Finance">
                                        <span>{{ $vendorBill->vendor_ref_no }}</span>
                                        <svg class="w-3 h-3 text-slate-400 group-hover/inv:text-slate-700 transition shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    </a>
                                @else
                                    <span class="text-slate-400 italic text-[11px]">-</span>
                                @endif
                            </td>

                            <!-- 5c. No. Bill (System Vendor Bill Identifier) -->
                            <td class="py-4 px-4 font-mono text-xs">
                                @if($vendorBill && $vendorBill->invoice_number)
                                    <a href="{{ route('erp.finance.index', ['tab' => 'bills', 'search' => $vendorBill->invoice_number]) }}" 
                                       class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-lg bg-purple-50 hover:bg-purple-100 text-purple-900 font-black border border-purple-200 transition text-[11px] group/bill"
                                       title="Buka Tagihan {{ $vendorBill->invoice_number }} di menu Finance">
                                        <span>{{ $vendorBill->invoice_number }}</span>
                                        <svg class="w-3 h-3 text-purple-400 group-hover/bill:text-purple-700 transition shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    </a>
                                @else
                                    <span class="text-slate-400 italic text-[11px]">-</span>
                                @endif
                            </td>

                            <!-- 6. Nilai Total (HPP) -->
                            <td class="py-4 px-4 text-right font-mono font-black text-slate-900 text-xs">
                                Rp {{ number_format($po->total_amount, 0, ',', '.') }}
                            </td>

                            <!-- 7. Status PO -->
                            <td class="py-4 px-4 text-center">
                                @if($po->status === 'approved' || $po->status === 'in_progress')
                                    <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                        <span x-text="lang === 'zh' ? '执行中' : 'Aktif'">Aktif</span>
                                    </span>
                                @elseif($po->status === 'completed')
                                    <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span x-text="lang === 'zh' ? '已完成' : 'Selesai'">Selesai</span>
                                    </span>
                                @elseif($po->status === 'cancelled')
                                    <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        <span x-text="lang === 'zh' ? '已取消' : 'Dibatalkan'">Dibatalkan</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        <span x-text="lang === 'zh' ? '草稿' : 'Draft'">Draft</span>
                                    </span>
                                @endif
                            </td>

                            <!-- 8. Status Tagihan Vendor -->
                            <td class="py-4 px-4 text-center">
                                @if($po->invoice_status === 'paid')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800" x-text="lang === 'zh' ? '账单已付清' : 'Lunas'">
                                        Lunas
                                    </span>
                                @elseif(in_array($po->invoice_status, ['invoiced', 'partial']))
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800" x-text="lang === 'zh' ? '账单待支付' : 'Ditagihkan'">
                                        Ditagihkan
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500" x-text="lang === 'zh' ? '未出账单' : 'Belum Ditagih'">
                                        Belum Ditagih
                                    </span>
                                @endif
                            </td>

                            <!-- 9. Aksi Cepat (Hanya Icon Saja, Tanpa Tombol Detail) -->
                            <td class="py-4 px-4 text-center sticky right-0 bg-white group-hover:bg-slate-50 transition z-10 shadow-[-6px_0_8px_-4px_rgba(0,0,0,0.06)] border-l border-slate-100">
                                <div class="flex items-center justify-center space-x-1.5">
                                    <!-- 1. Terbitkan Tagihan Vendor Icon Button -->
                                    @if($hasBill)
                                        <div class="relative group/tip inline-flex">
                                            <button type="button" 
                                                    disabled
                                                    class="w-8 h-8 rounded-xl bg-slate-100 text-slate-400 border border-slate-200 flex items-center justify-center cursor-not-allowed opacity-60"
                                                    :title="lang === 'zh' ? '供应商账单已生成' : 'Tagihan Vendor Sudah Dibuat'">
                                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            </button>
                                            <div class="absolute bottom-full mb-1.5 left-1/2 -translate-x-1/2 hidden group-hover/tip:flex flex-col items-center z-30 pointer-events-none">
                                                <div class="bg-slate-900 text-white text-[10px] font-bold px-2 py-1 rounded-lg shadow-lg whitespace-nowrap">
                                                    <span>Tagihan Vendor Sudah Dibuat ({{ $vendorBill?->invoice_number ?? 'Ditagihkan' }})</span>
                                                </div>
                                                <div class="w-1.5 h-1.5 bg-slate-900 rotate-45 -mt-1"></div>
                                            </div>
                                        </div>
                                    @elseif($po->status !== 'cancelled')
                                        <div class="relative group/tip inline-flex">
                                            <button type="button" 
                                                    @click="openBillModal(@js($po))" 
                                                    class="w-8 h-8 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-300 flex items-center justify-center transition active:scale-95 shadow-xs"
                                                    :title="lang === 'zh' ? '生成供应商账单 (Buat Tagihan)' : 'Buat Tagihan Vendor'">
                                                <svg class="w-4 h-4 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            </button>
                                            <div class="absolute bottom-full mb-1.5 left-1/2 -translate-x-1/2 hidden group-hover/tip:flex flex-col items-center z-30 pointer-events-none">
                                                <div class="bg-slate-900 text-white text-[10px] font-bold px-2 py-1 rounded-lg shadow-lg whitespace-nowrap">
                                                    <span x-text="lang === 'zh' ? '生成供应商账单' : 'Buat Tagihan Vendor'">Buat Tagihan Vendor</span>
                                                </div>
                                                <div class="w-1.5 h-1.5 bg-slate-900 rotate-45 -mt-1"></div>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- 2. Batalkan PO Icon Button -->
                                    @if($po->invoice_status === 'unbilled' && $po->status !== 'cancelled')
                                        <div class="relative group/tip inline-flex">
                                            <button type="button" 
                                                    @click="openCancelPoModal(@js($po))" 
                                                    class="w-8 h-8 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 hover:text-rose-800 border border-rose-200 flex items-center justify-center transition active:scale-95 shadow-xs"
                                                    :title="lang === 'zh' ? '取消采购订单 (Batalkan PO)' : 'Batalkan PO'">
                                                <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                            <div class="absolute bottom-full mb-1.5 left-1/2 -translate-x-1/2 hidden group-hover/tip:flex flex-col items-center z-30 pointer-events-none">
                                                <div class="bg-slate-900 text-white text-[10px] font-bold px-2 py-1 rounded-lg shadow-lg whitespace-nowrap">
                                                    <span x-text="lang === 'zh' ? '取消采购订单' : 'Batalkan PO'">Batalkan PO</span>
                                                </div>
                                                <div class="w-1.5 h-1.5 bg-slate-900 rotate-45 -mt-1"></div>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Cetak PO PDF Button -->
                                    <div class="relative group/tip inline-flex">
                                        <a href="{{ route('erp.procurement.pdf', $po->id) }}" 
                                           target="_blank" 
                                           class="w-8 h-8 rounded-xl bg-purple-50 hover:bg-purple-100 text-purple-700 border border-purple-200 flex items-center justify-center transition active:scale-95 shadow-xs"
                                           :title="lang === 'zh' ? '打印采购订单 (PDF)' : 'Cetak PO'">
                                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                        </a>
                                        <div class="absolute bottom-full mb-1.5 left-1/2 -translate-x-1/2 hidden group-hover/tip:flex flex-col items-center z-30 pointer-events-none">
                                            <div class="bg-slate-900 text-white text-[10px] font-bold px-2 py-1 rounded-lg shadow-lg whitespace-nowrap">
                                                <span x-text="lang === 'zh' ? '打印采购订单 (PO)' : 'Cetak PO'">Cetak PO</span>
                                            </div>
                                            <div class="w-1.5 h-1.5 bg-slate-900 rotate-45 -mt-1"></div>
                                        </div>
                                    </div>

                                    @if($po->status === 'cancelled')
                                        <span class="text-[10px] font-bold text-rose-500 italic">Batal</span>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        <!-- ========================================================================= -->
                        <!-- EXPANDABLE PRODUCT BREAKDOWN ACCORDION ROW                                 -->
                        <!-- ========================================================================= -->
                        <tr x-show="isExpanded({{ $po->id }})" 
                            x-cloak 
                            class="bg-slate-50/90 border-y-2 border-slate-200">
                            <td colspan="11" class="p-4 sm:p-5">
                                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
                                    <!-- Header of Expanded Card -->
                                    <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex flex-wrap items-center justify-between gap-3">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-black shrink-0">
                                                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                            </div>
                                            <div>
                                                <div class="flex items-center space-x-2">
                                                    <h4 class="font-black text-slate-900 text-xs flex items-center space-x-1.5">
                                                        <span x-text="lang === 'zh' ? '采购订单产品明细' : 'Rincian Produk & Item Pengadaan'">Rincian Produk & Item Pengadaan</span>
                                                        <span class="font-mono text-amber-700">({{ $po->order_number }})</span>
                                                    </h4>
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                                        {{ $po->items->count() }} Item Produk
                                                    </span>
                                                </div>
                                                <p class="text-[11px] text-slate-500 mt-0.5">
                                                    Vendor: <strong>{{ $po->contact->name ?? '-' }}</strong> • 
                                                    Tgl PO: {{ $po->order_date ? $po->order_date->format('d M Y') : '-' }}
                                                    @if($po->rental_start_date)
                                                        • Periode Sewa: {{ $po->rental_start_date->format('d/m/Y') }} s/d {{ $po->rental_end_date ? $po->rental_end_date->format('d/m/Y') : '-' }} ({{ $po->rental_duration }} {{ ucfirst($po->rental_duration_unit ?? 'Bulan') }})
                                                    @endif
                                                </p>
                                            </div>
                                        </div>

                                        <div class="flex items-center space-x-3 text-xs">
                                            <div class="flex items-center space-x-1.5">
                                                <span class="text-slate-500 font-semibold">Termin:</span>
                                                <span class="font-bold text-slate-800 bg-slate-100 px-2 py-0.5 rounded border border-slate-200">{{ $po->payment_term ?: 'Net 30 Days' }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Table of Products -->
                                    <div class="overflow-x-auto">
                                        <table class="w-full text-left text-xs border-collapse">
                                            <thead>
                                                <tr class="bg-slate-100/70 border-b border-slate-200 text-slate-500 font-bold uppercase text-[10px] tracking-wider">
                                                    <th class="py-2.5 px-4 w-10 text-center">#</th>
                                                    <th class="py-2.5 px-4">Deskripsi / Nama Produk</th>
                                                    <th class="py-2.5 px-4">Spesifikasi</th>
                                                    <th class="py-2.5 px-3 text-center">Qty</th>
                                                    <th class="py-2.5 px-3 text-center">Durasi</th>
                                                    <th class="py-2.5 px-4 text-right">Harga Satuan (HPP)</th>
                                                    <th class="py-2.5 px-3 text-right">Diskon</th>
                                                    <th class="py-2.5 px-4 text-right">Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                                @forelse($po->items as $itemIdx => $item)
                                                    <tr class="hover:bg-slate-50/60 transition">
                                                        <td class="py-2.5 px-4 text-center font-mono text-slate-400 text-[11px]">{{ $itemIdx + 1 }}</td>
                                                        <td class="py-2.5 px-4 font-bold text-slate-900">
                                                            {{ $item->description }}
                                                        </td>
                                                        <td class="py-2.5 px-4 text-slate-500 text-[11px]">
                                                            {{ $item->specification ?: '-' }}
                                                        </td>
                                                        <td class="py-2.5 px-3 text-center font-mono font-semibold">
                                                            {{ (float)$item->quantity }} {{ $item->unit ?: 'Unit' }}
                                                        </td>
                                                        <td class="py-2.5 px-3 text-center text-slate-600">
                                                            @if($item->duration && $po->procurement_type === 'sub_rental')
                                                                <span class="font-bold">{{ $item->duration }}</span> {{ $item->duration_unit ?: 'Bulan' }}
                                                            @else
                                                                <span class="text-slate-400">-</span>
                                                            @endif
                                                        </td>
                                                        <td class="py-2.5 px-4 text-right font-mono text-slate-800">
                                                            Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                                                        </td>
                                                        <td class="py-2.5 px-3 text-right font-mono text-slate-500">
                                                            {{ $item->discount > 0 ? 'Rp ' . number_format($item->discount, 0, ',', '.') : '-' }}
                                                        </td>
                                                        <td class="py-2.5 px-4 text-right font-mono font-black text-slate-900">
                                                            Rp {{ number_format($item->line_total, 0, ',', '.') }}
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="8" class="text-center py-6 text-slate-400 italic">Tidak ada rincian produk</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                            <tfoot class="bg-slate-50 border-t border-slate-200 text-xs">
                                                <tr>
                                                    <td colspan="5" class="py-2.5 px-4 text-slate-500 italic">
                                                        @if($po->notes)
                                                            <strong>Catatan:</strong> {{ $po->notes }}
                                                        @endif
                                                        @if($po->cancel_reason)
                                                            <div class="text-rose-600 font-bold mt-0.5">Alasan Batal: {{ $po->cancel_reason }}</div>
                                                        @endif
                                                    </td>
                                                    <td colspan="2" class="py-2.5 px-4 text-right font-bold text-slate-700">Subtotal:</td>
                                                    <td class="py-2.5 px-4 text-right font-mono font-bold text-slate-900">Rp {{ number_format($po->subtotal, 0, ',', '.') }}</td>
                                                </tr>
                                                @if($po->tax_amount > 0)
                                                    <tr>
                                                        <td colspan="5" class="py-1 px-4"></td>
                                                        <td colspan="2" class="py-1 px-4 text-right font-semibold text-slate-600">PPN ({{ (float)$po->tax_percent }}%):</td>
                                                        <td class="py-1 px-4 text-right font-mono text-slate-800">Rp {{ number_format($po->tax_amount, 0, ',', '.') }}</td>
                                                    </tr>
                                                @endif
                                                @if($po->shipping_fee > 0)
                                                    <tr>
                                                        <td colspan="5" class="py-1 px-4"></td>
                                                        <td colspan="2" class="py-1 px-4 text-right font-semibold text-slate-600">Biaya Kirim / Logistik:</td>
                                                        <td class="py-1 px-4 text-right font-mono text-slate-800">Rp {{ number_format($po->shipping_fee, 0, ',', '.') }}</td>
                                                    </tr>
                                                @endif
                                                <tr class="border-t border-slate-200 font-black">
                                                    <td colspan="5" class="py-2.5 px-4"></td>
                                                    <td colspan="2" class="py-2.5 px-4 text-right text-slate-900">Total HPP:</td>
                                                    <td class="py-2.5 px-4 text-right font-mono text-amber-700 text-sm">Rp {{ number_format($po->total_amount, 0, ',', '.') }}</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center py-12 text-slate-400">
                                <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                <p class="font-bold text-slate-600" x-text="lang === 'zh' ? '暂无采购或转租记录。' : 'Belum ada data pengadaan atau sewa lempar vendor.'">Belum ada data pengadaan atau sewa lempar vendor.</p>
                                <button type="button" @click="openCreateModal()" class="mt-3 text-blue-600 font-extrabold hover:underline">
                                    <span x-text="lang === 'zh' ? '+ 创建首个采购订单' : '+ Buat Order Pengadaan Pertama'">+ Buat Order Pengadaan Pertama</span>
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- MOBILE CARD VIEW (VISIBLE ON MOBILE < MD, HIDDEN ON DESKTOP) -->
        <div class="block md:hidden p-2.5 sm:p-3.5 space-y-3 sm:space-y-3.5 bg-[#F5F7FB] border-b border-[#E5EAF2]">
            @include('erp.partials.procurement-mobile-cards')
        </div>

        <!-- Pagination Bar -->
        {{ $purchaseOrders->links('erp.pagination') }}
    </div>

    <!-- ========================================================================= -->
    <!-- 6. MODAL BUAT PO / SUB-RENT BARU                                          -->
    <!-- ========================================================================= -->
    <div x-show="modalCreateOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto"
         @click.self="modalCreateOpen = false" style="display: none;">

        <div class="bg-white rounded-3xl max-w-4xl w-full shadow-2xl border border-slate-200 overflow-hidden my-8 animate-in zoom-in-95 duration-150 flex flex-col max-h-[90vh]">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50 shrink-0">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center font-black shrink-0">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-900" x-text="lang === 'zh' ? '新建采购 / 转租订单 (Purchase Order)' : 'Buat Order Pengadaan / Sub-Rent Baru'">
                            Buat Order Pengadaan / Sub-Rent Baru
                        </h3>
                        <p class="text-[11px] text-slate-500 mt-0.5" x-text="lang === 'zh' ? '记录供应商成本并与客户订单建立联动关联。' : 'Catat harga modal (HPP) vendor dan hubungkan dengan transaksi pelanggan.'"></p>
                    </div>
                </div>
                <button type="button" @click="modalCreateOpen = false" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-200/60 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form action="{{ route('erp.procurement.store') }}" method="POST" class="p-6 space-y-5">
                @csrf

                <!-- Row 1: Vendor Mitra & Tipe Pengadaan (Hubungkan ke Transaksi Pelanggan dihapus karena alokasi transaksi pelanggan langsung di menu Penawaran & Order) -->
                <input type="hidden" name="parent_id" value="">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            <span x-text="lang === 'zh' ? '选择供应商 (Vendor) *' : 'Pilih Vendor Mitra *'">Pilih Vendor Mitra *</span>
                        </label>
                        <select name="contact_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold text-slate-800 focus:outline-none focus:border-blue-500">
                            <option value="">-- Pilih Vendor --</option>
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}">{{ $vendor->name }} ({{ $vendor->category ?: 'Vendor' }})</option>
                            @endforeach
                        </select>
                        <p class="text-[10px] text-slate-400 mt-1" x-text="lang === 'zh' ? '注：物流/货运商已隔离于物流配送模块。' : 'Catatan: Vendor pengiriman/ekspedisi dikelola khusus di modul logistik & DO.'"></p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            <span x-text="lang === 'zh' ? '采购类型 *' : 'Tipe Pengadaan *'">Tipe Pengadaan *</span>
                        </label>
                        <select name="procurement_type" x-model="formData.procurement_type" @change="onProcurementTypeChange()" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold text-slate-800 focus:outline-none focus:border-blue-500">
                            <option value="sub_rental">1. Sewa (Sub-Rent Unit Forklift / Alat Vendor)</option>
                            <option value="purchase_unit">2. Pembelian (Beli Unit / Sparepart / Material)</option>
                            <option value="service">3. Jasa (Servis Armada / Bengkel / Operator / Maintenance)</option>
                            <option value="operational">4. Non Order / OPEX (Operasional Kantor / Umum)</option>
                        </select>
                        <p class="text-[10px] text-slate-400 mt-1" x-text="lang === 'zh' ? '“服务”类型包括自有车队维修、技术工及设备维护。' : 'Tipe Jasa mencakup servis forklift armada internal, bengkel, operator, dan perawatan.'"></p>
                    </div>
                </div>

                <!-- Helper Note for Operational Procurement -->
                <div x-show="isOperationalType(formData.procurement_type)" class="text-[11px] text-amber-900 bg-amber-50 border border-amber-200/90 rounded-2xl p-3.5 flex items-start space-x-2.5">
                    <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div>
                        <span class="font-bold">Pengadaan Operasional Internal Perusahaan:</span>
                        Biaya ini merupakan pengeluaran kantor / OPEX (misal ATK, Google Ads, sistem ERP, sewa mobil kantor, servis forklift milik sendiri). 
                        Biaya ini <strong>tidak dibebankan ke order pelanggan tertentu</strong> sehingga kolom Transaksi Pelanggan di atas otomatis diset ke <em>Tanpa Relasi</em>.
                    </div>
                </div>

                <!-- Row 2: Tanggal, Durasi Sewa (if sub_rental) -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 bg-slate-50 p-4 rounded-2xl border border-slate-200/80">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '订单日期 *' : 'Tanggal PO *'">Tanggal PO *</label>
                        <input type="date" name="order_date" value="{{ date('Y-m-d') }}" required class="w-full bg-white border border-slate-200 rounded-xl px-3 py-1.5 text-xs text-slate-800">
                    </div>

                    <div x-show="formData.procurement_type === 'sub_rental'">
                        <label class="block text-xs font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '租赁开始日期' : 'Mulai Sewa'">Mulai Sewa</label>
                        <input type="date" name="rental_start_date" value="{{ date('Y-m-d') }}" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-1.5 text-xs text-slate-800">
                    </div>

                    <div x-show="formData.procurement_type === 'sub_rental'">
                        <label class="block text-xs font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '租赁期限' : 'Durasi Sewa'">Durasi Sewa</label>
                        <div class="flex space-x-2">
                            <input type="number" name="rental_duration" x-model="formData.rental_duration" min="1" value="1" class="w-20 bg-white border border-slate-200 rounded-xl px-3 py-1.5 text-xs text-slate-800 font-bold">
                            <select name="rental_duration_unit" x-model="formData.rental_duration_unit" class="flex-1 bg-white border border-slate-200 rounded-xl px-2 py-1.5 text-xs font-bold text-slate-800">
                                <option value="month">Bulan</option>
                                <option value="day">Hari</option>
                                <option value="week">Minggu</option>
                                <option value="year">Tahun</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '付款账期 (TOP)' : 'Termin Pembayaran'">Termin Pembayaran</label>
                        <input type="text" name="payment_term" value="Net 30 Days" placeholder="Misal: Net 30 Days" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-1.5 text-xs text-slate-800">
                    </div>
                </div>

                <!-- Items Repeater -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-extrabold text-slate-900 uppercase tracking-wide" x-text="lang === 'zh' ? '采购明细清单' : 'Rincian Barang & Biaya Vendor'">Rincian Barang & Biaya Vendor</span>
                        <button type="button" @click="addItemRow()" class="text-xs font-extrabold text-blue-600 hover:text-blue-800">
                            + <span x-text="lang === 'zh' ? '添加明细' : 'Tambah Baris Item'">Tambah Baris Item</span>
                        </button>
                    </div>

                    <div class="space-y-3">
                        <template x-for="(item, idx) in formData.items" :key="idx">
                            <div class="bg-slate-50 p-3 rounded-2xl border border-slate-200 flex flex-wrap md:flex-nowrap items-center gap-3">
                                <div class="flex-1 min-w-[200px]">
                                    <label class="block text-[10px] font-bold text-slate-500 mb-0.5" x-text="lang === 'zh' ? '商品/设备名称 *' : 'Nama Barang / Unit *'">Nama Barang / Unit *</label>
                                    <input type="text" :name="'items[' + idx + '][name]'" x-model="item.name" required placeholder="Contoh: Forklift 3T Diesel / Oli Hidrolik" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-1.5 text-xs font-bold text-slate-800">
                                </div>

                                <div class="w-24">
                                    <label class="block text-[10px] font-bold text-slate-500 mb-0.5" x-text="lang === 'zh' ? '数量 *' : 'Jumlah (Qty) *'">Qty *</label>
                                    <input type="number" step="0.01" min="0.01" :name="'items[' + idx + '][quantity]'" x-model.number="item.quantity" @input="recalcTotals()" required class="w-full bg-white border border-slate-200 rounded-xl px-3 py-1.5 text-xs font-bold text-slate-800 text-center">
                                </div>

                                <div class="w-24">
                                    <label class="block text-[10px] font-bold text-slate-500 mb-0.5" x-text="lang === 'zh' ? '单位' : 'Satuan'">Satuan</label>
                                    <input type="text" :name="'items[' + idx + '][unit]'" x-model="item.unit" placeholder="Unit/Pcs" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-1.5 text-xs text-slate-800 text-center">
                                </div>

                                <div class="w-36">
                                    <label class="block text-[10px] font-bold text-slate-500 mb-0.5" x-text="lang === 'zh' ? '采购单价 (Rp) *' : 'Harga Modal Satuan (Rp) *'">Harga Satuan (Rp) *</label>
                                    <input type="hidden" :name="'items[' + idx + '][unit_price]'" :value="item.unit_price">
                                    <input type="text"
                                           inputmode="numeric"
                                           data-rupiah
                                           :value="formatNumberWithDots(item.unit_price)"
                                           @input="item.unit_price = parseNumberFromDots($event.target.value); recalcTotals()"
                                           placeholder="0"
                                           required
                                           class="w-full bg-white border border-slate-200 rounded-xl px-3 py-1.5 text-xs font-mono font-bold text-slate-800 text-right">
                                </div>

                                <div class="w-36 text-right">
                                    <label class="block text-[10px] font-bold text-slate-500 mb-0.5" x-text="lang === 'zh' ? '小计' : 'Total Baris'">Total Baris</label>
                                    <div class="font-mono font-black text-slate-900 text-xs py-1.5" x-text="'Rp ' + (item.quantity * item.unit_price * (formData.procurement_type === 'sub_rental' ? formData.rental_duration : 1)).toLocaleString('id-ID')"></div>
                                </div>

                                <button type="button" @click="removeItemRow(idx)" class="text-rose-500 hover:text-rose-700 p-1.5 rounded-lg" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Footer Summary (Ongkir Towing, PPN, Total) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-3 border-t border-slate-100">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '备注说明' : 'Catatan Tambahan'">Catatan Tambahan</label>
                        <textarea name="notes" rows="3" placeholder="Informasi spesifikasi unit vendor, PIC pengiriman, atau catatan teknis..." class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs text-slate-800"></textarea>
                    </div>

                    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 space-y-2 text-xs">
                        <div class="flex items-center justify-between text-slate-600">
                            <span x-text="lang === 'zh' ? '采购小计 (Subtotal)' : 'Subtotal Barang/Unit'">Subtotal Barang/Unit</span>
                            <span class="font-mono font-bold" x-text="'Rp ' + calculatedSubtotal.toLocaleString('id-ID')"></span>
                        </div>
                        <div class="flex items-center justify-between text-slate-600">
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" name="include_ppn" value="1" x-model="formData.include_ppn" @change="recalcTotals()" class="rounded text-blue-600">
                                <span x-text="lang === 'zh' ? '含增值税 (PPN 11%)' : 'PPN Vendor (11%)'">PPN Vendor (11%)</span>
                            </label>
                            <span class="font-mono font-bold" x-text="'Rp ' + calculatedTax.toLocaleString('id-ID')"></span>
                        </div>
                        <div class="flex items-center justify-between text-slate-600">
                            <span x-text="lang === 'zh' ? '拖运/物流费 (Towing)' : 'Ongkir / Towing Vendor'">Ongkir / Towing Vendor</span>
                            <input type="hidden" name="shipping_fee" :value="formData.shipping_fee">
                            <input type="text"
                                   inputmode="numeric"
                                   data-rupiah
                                   :value="formatNumberWithDots(formData.shipping_fee)"
                                   @input="formData.shipping_fee = parseNumberFromDots($event.target.value); recalcTotals()"
                                   placeholder="0"
                                   class="w-28 text-right bg-white border border-slate-200 rounded-lg px-2 py-1 text-xs font-mono font-bold">
                        </div>
                        <div class="flex items-center justify-between font-black text-slate-900 text-sm pt-2 border-t border-slate-200">
                            <span x-text="lang === 'zh' ? '采购总成本 (Total HPP)' : 'Total Biaya Pengadaan (HPP)'">Total Biaya Pengadaan (HPP)</span>
                            <span class="font-mono text-emerald-600" x-text="'Rp ' + calculatedTotal.toLocaleString('id-ID')"></span>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons Footer (CRM Style) -->
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end space-x-3 -mx-6 -mb-6 mt-6 rounded-b-3xl">
                    <button type="button" @click="modalCreateOpen = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-100 transition" x-text="lang === 'zh' ? '取消' : 'Batal'">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-black shadow-sm transition active:scale-95 flex items-center space-x-2" x-text="lang === 'zh' ? '保存并创建 PO' : 'Simpan & Terbitkan PO'">
                        Simpan & Terbitkan PO
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 7. MODAL TERBITKAN TAGIHAN VENDOR (GENERATE BILL) - CRM STYLE             -->
    <!-- ========================================================================= -->
    <div x-show="modalBillOpen" 
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto"
         @click.self="modalBillOpen = false" style="display: none;">

        <div class="bg-white rounded-3xl max-w-lg w-full shadow-2xl border border-slate-200 overflow-hidden my-8 animate-in zoom-in-95 duration-150 flex flex-col">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50 shrink-0">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center font-black shrink-0">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-900" x-text="lang === 'zh' ? '生成供应商账单 (Vendor Bill)' : 'Terbitkan Tagihan Vendor (Vendor Bill)'">
                            Terbitkan Tagihan Vendor (Vendor Bill)
                        </h3>
                        <p class="text-[11px] text-slate-500 mt-0.5" x-text="lang === 'zh' ? '记录供应商原始发票编号并列入应付账款。' : 'Catat nomor surat tagihan asli vendor dan masukkan ke utang usaha (AP).'"></p>
                    </div>
                </div>
                <button type="button" @click="modalBillOpen = false" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-200/60 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form :action="'/erp/procurement/orders/' + activePo?.id + '/bill'" method="POST" @submit="submittingBill = true" class="p-6 space-y-4 text-xs">
                @csrf
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 space-y-1.5 font-bold">
                    <div class="flex items-center justify-between text-slate-600">
                        <span>No. PO: <span class="font-mono text-slate-900 font-black" x-text="activePo?.order_number || '-'"></span></span>
                        <span class="text-slate-900" x-text="activePo?.contact?.name || '-'"></span>
                    </div>
                    <div class="flex items-center justify-between pt-1.5 border-t border-slate-200 text-slate-900 font-mono text-xs">
                        <span class="font-sans font-bold text-slate-500">Total Tagihan Vendor:</span>
                        <span class="font-black text-rose-600 text-sm" x-text="'Rp ' + Number(activePo?.total_amount || 0).toLocaleString('id-ID')"></span>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '供应商发票编号 (Vendor Ref / No. Kwitansi)' : 'No. Surat Tagihan Asli Vendor'">No. Surat Tagihan Asli Vendor</label>
                    <input type="text" name="vendor_ref_no" placeholder="Contoh: INV/VD/2026/089" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-900 font-mono font-bold outline-none focus:border-yellow-400 focus:bg-white text-xs">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '账单开具日期 *' : 'Tanggal Tagihan *'">Tanggal Tagihan *</label>
                        <input type="date" name="issue_date" value="{{ date('Y-m-d') }}" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-900 font-semibold outline-none focus:border-yellow-400 focus:bg-white text-xs">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '付款截止日期 (Due Date) *' : 'Jatuh Tempo Pembayaran *'">Jatuh Tempo *</label>
                        <input type="date" name="due_date" value="{{ date('Y-m-d', strtotime('+30 days')) }}" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-900 font-semibold outline-none focus:border-yellow-400 focus:bg-white text-xs">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '备注' : 'Catatan'">Catatan</label>
                    <textarea name="notes" rows="2" placeholder="Catatan tagihan vendor..." class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-slate-900 font-medium outline-none focus:border-yellow-400 focus:bg-white text-xs"></textarea>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-3 border-t border-slate-100">
                    <button type="button" @click="modalBillOpen = false" :disabled="submittingBill" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-100 transition" x-text="lang === 'zh' ? '取消' : 'Batal'">Batal</button>
                    <button type="submit" :disabled="submittingBill" class="px-6 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-black shadow-sm transition active:scale-95 disabled:opacity-50" x-text="submittingBill ? (lang === 'zh' ? '正在处理...' : 'Memproses...') : (lang === 'zh' ? '确认生成账单' : 'Terbitkan Tagihan')">Terbitkan Tagihan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 7b. MODAL BATALKAN ORDER PENGADAAN (CANCEL PO)                           -->
    <!-- ========================================================================= -->
    <div x-show="modalCancelOpen" 
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto"
         @click.self="modalCancelOpen = false" style="display: none;">

        <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl border border-rose-200 overflow-hidden my-8 animate-in zoom-in-95 duration-150 flex flex-col">
            <div class="px-6 py-5 border-b border-rose-100 flex items-center justify-between bg-rose-50/70 shrink-0">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center font-black shrink-0">
                        <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-rose-950" x-text="lang === 'zh' ? '取消采购订单 (Batalkan PO)' : 'Batalkan Order Pengadaan (PO)'">
                            Batalkan Order Pengadaan (PO)
                        </h3>
                        <p class="text-[11px] text-rose-700 mt-0.5" x-text="lang === 'zh' ? '撤销未开票的采购订单并更新成本核算。' : 'Batalkan PO yang belum ditagihkan vendor.'"></p>
                    </div>
                </div>
                <button type="button" @click="modalCancelOpen = false" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-200/60 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form :action="'/erp/procurement/orders/' + cancelForm.po_id + '/cancel'" method="POST" class="p-6 space-y-4 text-xs">
                @csrf
                <div class="bg-rose-50/60 p-4 rounded-2xl border border-rose-200/80 space-y-1.5">
                    <div class="flex items-center justify-between text-slate-700">
                        <span>No. PO: <span class="font-mono text-rose-900 font-black" x-text="cancelForm.po_number || '-'"></span></span>
                        <span class="font-bold text-slate-800" x-text="cancelForm.vendor_name || '-'"></span>
                    </div>
                    <div class="flex items-center justify-between pt-1.5 border-t border-rose-200/60 font-mono text-xs">
                        <span class="font-sans font-semibold text-slate-600">Total Nilai HPP:</span>
                        <span class="font-black text-rose-700" x-text="'Rp ' + Number(cancelForm.total_amount || 0).toLocaleString('id-ID')"></span>
                    </div>
                </div>

                <div class="text-[11px] text-slate-600 bg-slate-50 border border-slate-200 rounded-xl p-3">
                    <span class="font-bold text-slate-800">Perhatian:</span> Pembatalan PO ini akan menghapus beban modal vendor dari kalkulasi laba-rugi transaksi pelanggan. Tindakan ini hanya dapat dilakukan jika belum ada tagihan vendor yang diterbitkan.
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '取消原因 (可选)' : 'Alasan Pembatalan (Opsional)'">Alasan Pembatalan</label>
                    <textarea name="cancel_reason" x-model="cancelForm.cancel_reason" rows="3" placeholder="Contoh: Unit vendor tidak tersedia, perubahan spesifikasi, atau salah input..." class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-slate-900 font-medium outline-none focus:border-rose-400 focus:bg-white text-xs"></textarea>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-3 border-t border-slate-100">
                    <button type="button" @click="modalCancelOpen = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-100 transition" x-text="lang === 'zh' ? '保留' : 'Kembali'">Kembali</button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-black shadow-sm transition active:scale-95 flex items-center space-x-1.5" x-text="lang === 'zh' ? '确认取消该 PO' : 'Ya, Batalkan PO'">
                        Ya, Batalkan PO
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 8. SLIDE-UP DRAWER DETAIL PENGADAAN                                       -->
    <!-- ========================================================================= -->
    <div x-show="drawerOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-end bg-black/40 drawer-overlay"
         @click.self="drawerOpen = false" style="display: none;">
        
        <div class="w-full max-w-lg bg-white h-full shadow-2xl flex flex-col justify-between overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-900 text-white">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-2xl bg-amber-400 text-slate-950 flex items-center justify-center font-black">
                        PO
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold" x-text="selectedPo?.order_number || 'Detail PO'"></h3>
                        <p class="text-xs text-slate-400" x-text="'Vendor: ' + (selectedPo?.contact?.name || '-')"></p>
                    </div>
                </div>
                <button @click="drawerOpen = false" class="text-slate-400 hover:text-white p-1 rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="p-6 overflow-y-auto space-y-5 text-xs flex-1">
                <!-- Info Grid -->
                <div class="grid grid-cols-2 gap-3 bg-slate-50 p-4 rounded-2xl border border-slate-200">
                    <div>
                        <span class="text-slate-400 font-bold block mb-0.5" x-text="lang === 'zh' ? '采购类型' : 'Tipe Pengadaan'">Tipe Pengadaan</span>
                        <span class="font-bold text-slate-900 capitalize" x-text="selectedPo?.procurement_type"></span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-bold block mb-0.5" x-text="lang === 'zh' ? '订单状态' : 'Status PO'">Status PO</span>
                        <span class="font-bold text-slate-900 capitalize" x-text="selectedPo?.status"></span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-bold block mb-0.5" x-text="lang === 'zh' ? '订单日期' : 'Tanggal PO'">Tanggal PO</span>
                        <span class="font-medium text-slate-800" x-text="selectedPo?.order_date"></span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-bold block mb-0.5" x-text="lang === 'zh' ? '付款账期' : 'Termin Bayar'">Termin Bayar</span>
                        <span class="font-medium text-slate-800" x-text="selectedPo?.payment_term || 'Net 30'"></span>
                    </div>
                </div>

                <!-- Linked Customer Transaction vs Non-Order -->
                <template x-if="selectedPo?.parent">
                    <div class="bg-blue-50 p-4 rounded-2xl border border-blue-200 space-y-2">
                        <span class="text-[10px] font-black uppercase tracking-wider text-blue-900 block" x-text="lang === 'zh' ? '服务客户项目 (关联合同)' : 'Melayani Proyek Pelanggan'">
                            Melayani Proyek Pelanggan
                        </span>
                        <div class="flex items-center justify-between">
                            <span class="font-bold font-mono text-slate-900" x-text="selectedPo.parent.order_number"></span>
                            <span class="text-xs font-bold text-slate-700" x-text="selectedPo.parent.contact?.name"></span>
                        </div>
                    </div>
                </template>
                <template x-if="!selectedPo?.parent">
                    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 space-y-1">
                        <span class="text-[10px] font-black uppercase tracking-wider text-slate-500 block">
                            Tujuan Pengadaan
                        </span>
                        <div class="font-bold text-slate-800 text-xs flex items-center space-x-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span>Non-Order / Operasional Internal Perusahaan</span>
                        </div>
                        <p class="text-[11px] text-slate-400">Pengadaan ini tidak dibebankan langsung ke transaksi sewa/jual pelanggan tertentu (Biaya Operasional / OPEX / Stok Bebas).</p>
                    </div>
                </template>

                <!-- Items Breakdown -->
                <div>
                    <h4 class="font-black text-slate-900 uppercase text-[10px] tracking-wider mb-2" x-text="lang === 'zh' ? '采购商品清单' : 'Daftar Barang Pengadaan'">
                        Daftar Barang Pengadaan
                    </h4>
                    <div class="space-y-2">
                        <template x-for="item in selectedPo?.items || []" :key="item.id">
                            <div class="bg-slate-50 p-3 rounded-xl border border-slate-200 flex items-center justify-between">
                                <div>
                                    <span class="font-bold text-slate-900 block" x-text="item.description"></span>
                                    <span class="text-[10px] text-slate-500" x-text="item.quantity + ' ' + (item.unit || 'Unit') + ' @ Rp ' + Number(item.unit_price).toLocaleString('id-ID')"></span>
                                </div>
                                <span class="font-mono font-bold text-slate-900" x-text="'Rp ' + Number(item.line_total).toLocaleString('id-ID')"></span>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Financial Summary -->
                <div class="bg-slate-900 text-white p-4 rounded-2xl space-y-2">
                    <div class="flex items-center justify-between text-xs text-slate-400">
                        <span x-text="lang === 'zh' ? '商品小计' : 'Subtotal'">Subtotal</span>
                        <span class="font-mono" x-text="'Rp ' + Number(selectedPo?.subtotal || 0).toLocaleString('id-ID')"></span>
                    </div>
                    <div class="flex items-center justify-between text-xs text-slate-400">
                        <span x-text="lang === 'zh' ? '增值税 (PPN)' : 'PPN'">PPN</span>
                        <span class="font-mono" x-text="'Rp ' + Number(selectedPo?.tax_amount || 0).toLocaleString('id-ID')"></span>
                    </div>
                    <div class="flex items-center justify-between text-sm font-black pt-2 border-t border-slate-800 text-amber-400">
                        <span x-text="lang === 'zh' ? '采购总额' : 'Total Biaya PO'">Total Biaya PO</span>
                        <span class="font-mono" x-text="'Rp ' + Number(selectedPo?.total_amount || 0).toLocaleString('id-ID')"></span>
                    </div>
                </div>
            </div>

            <div class="p-4 border-t border-slate-100 bg-slate-50 flex items-center justify-between">
                <template x-if="selectedPo">
                    <a :href="'/erp/procurement/orders/' + selectedPo.id + '/pdf'" 
                       target="_blank" 
                       class="px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold transition flex items-center space-x-1.5 shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        <span x-text="lang === 'zh' ? '打印采购订单 (PDF)' : 'Cetak PDF PO'">Cetak PDF PO</span>
                    </a>
                </template>
                <button @click="drawerOpen = false" class="px-5 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-white transition ml-auto" x-text="lang === 'zh' ? '关闭' : 'Tutup'">
                    Tutup
                </button>
            </div>
        </div>
    </div>

</div>

<!-- Alpine Logic for Procurement Hub -->
<script>
function erpProcurementManager() {
    return {
        searchQuery: '{{ $search ?? "" }}',
        statusFilter: '{{ $statusFilter ?? "all" }}',
        mobileFilterOpen: false,
        modalCreateOpen: false,
        modalBillOpen: false,
        submittingBill: false,
        modalCancelOpen: false,
        drawerOpen: false,
        formatNumberWithDots: window.formatNumberWithDots,
        parseNumberFromDots: window.parseNumberFromDots,
        maskRupiah: window.maskRupiah,
        selectedPo: null,
        activePo: null,
        expandedPOs: [],
        _searchTimer: null,

        submitSearch() {
            const url = new URL(window.location.href);
            const q = (this.searchQuery || '').trim();
            if (q) {
                url.searchParams.set('search', q);
            } else {
                url.searchParams.delete('search');
            }
            if (this.statusFilter && this.statusFilter !== 'all') {
                url.searchParams.set('status', this.statusFilter);
            } else {
                url.searchParams.delete('status');
            }
            url.searchParams.set('page', '1');
            window.location.href = url.toString();
        },

        clearSearch() {
            this.searchQuery = '';
            this.statusFilter = 'all';
            const url = new URL(window.location.href);
            url.searchParams.delete('search');
            url.searchParams.delete('status');
            url.searchParams.set('page', '1');
            window.location.href = url.toString();
        },

        onSearchInput() {
            if ((!this.searchQuery || this.searchQuery.trim() === '') && window.location.search.includes('search=')) {
                clearTimeout(this._searchTimer);
                this._searchTimer = setTimeout(() => {
                    this.clearSearch();
                }, 500);
            }
        },

        cancelForm: {
            po_id: null,
            po_number: '',
            cancel_reason: '',
            total_amount: 0,
            vendor_name: ''
        },

        formData: {
            procurement_type: 'sub_rental',
            parent_id: '',
            rental_duration: 1,
            rental_duration_unit: 'month',
            include_ppn: false,
            shipping_fee: 0,
            items: [
                { name: '', quantity: 1, unit: 'Unit', unit_price: 0, discount: 0 }
            ]
        },

        calculatedSubtotal: 0,
        calculatedTax: 0,
        calculatedTotal: 0,

        toggleExpand(id) {
            const idx = this.expandedPOs.indexOf(id);
            if (idx > -1) {
                this.expandedPOs.splice(idx, 1);
            } else {
                this.expandedPOs.push(id);
            }
        },

        isExpanded(id) {
            return this.expandedPOs.includes(id);
        },

        isOperationalType(type) {
            return ['operational', 'office_supplies', 'marketing', 'it_software', 'internal_fleet', 'internal_service', 'other'].includes(type);
        },

        onProcurementTypeChange() {
            if (this.isOperationalType(this.formData.procurement_type)) {
                this.formData.parent_id = '';
            }
            this.recalcTotals();
        },

        openCreateModal() {
            this.modalCreateOpen = true;
            this.recalcTotals();
        },

        openBillModal(po) {
            this.activePo = po;
            this.modalBillOpen = true;
        },

        openCancelPoModal(po) {
            this.cancelForm = {
                po_id: po.id,
                po_number: po.order_number,
                cancel_reason: '',
                total_amount: po.total_amount,
                vendor_name: po.contact?.name || '-'
            };
            this.modalCancelOpen = true;
        },

        openDetailsDrawer(po) {
            this.selectedPo = po;
            this.drawerOpen = true;
        },

        addItemRow() {
            this.formData.items.push({ name: '', quantity: 1, unit: 'Unit', unit_price: 0, discount: 0 });
            this.recalcTotals();
        },

        removeItemRow(idx) {
            if (this.formData.items.length > 1) {
                this.formData.items.splice(idx, 1);
                this.recalcTotals();
            }
        },

        recalcTotals() {
            let sub = 0;
            const duration = (this.formData.procurement_type === 'sub_rental') ? Math.max(1, this.formData.rental_duration || 1) : 1;
            
            this.formData.items.forEach(it => {
                const line = (Number(it.quantity || 0) * Number(it.unit_price || 0) * duration) - Number(it.discount || 0);
                sub += Math.max(0, line);
            });

            this.calculatedSubtotal = sub;
            this.calculatedTax = this.formData.include_ppn ? Math.round(sub * 0.11) : 0;
            this.calculatedTotal = this.calculatedSubtotal + this.calculatedTax + Number(this.formData.shipping_fee || 0);
        },

        matchesFilter(poNum, vendorName, parentNum, itemDesc, status, vendorRef, billNum) {
            const query = (this.searchQuery || '').toLowerCase();
            const matchesQuery = !query || 
                poNum.includes(query) || 
                vendorName.includes(query) || 
                parentNum.includes(query) || 
                (vendorRef && vendorRef.includes(query)) ||
                (billNum && billNum.includes(query)) ||
                (itemDesc && itemDesc.includes(query));
            const matchesStatus = (this.statusFilter === 'all') || (status === this.statusFilter);
            return matchesQuery && matchesStatus;
        }
    }
}
</script>
@endsection
