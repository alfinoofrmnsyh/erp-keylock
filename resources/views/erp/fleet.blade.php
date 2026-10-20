@extends('layouts.erp')

@section('title', 'Armada & Unit Management - Keylock ERP')

@section('breadcrumbs')
    <a href="{{ route('erp.index') }}" class="hover:text-slate-800">Home</a>
    <span>›</span>
    <span class="text-slate-800 font-semibold" x-text="lang === 'zh' ? '车队与设备管理' : 'Armada & Unit'">Armada & Unit</span>
@endsection

@section('content')
<div x-data="erpFleetManager()">

    <!-- ========================================================================= -->
    <!-- MOBILE HEADER & TOOLBAR (SERAGAM DENGAN PENAWARAN & ORDER)                -->
    <!-- ========================================================================= -->
    <div class="block md:hidden mb-4 space-y-3">
        <!-- 1. Header: Back button + Title & Subtitle + Action Buttons -->
        <div class="flex items-center space-x-3">
            <a href="{{ route('erp.index') }}" 
               class="w-10 h-10 rounded-full border border-slate-200 bg-white flex items-center justify-center text-slate-700 hover:bg-slate-50 active:scale-95 transition shadow-2xs shrink-0"
               aria-label="Kembali ke Dashboard">
                <svg class="w-5 h-5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div class="min-w-0 flex-1">
                <h1 class="text-lg font-black text-[#17243B] tracking-tight leading-tight" x-text="lang === 'zh' ? '车队与设备管理' : 'Armada & Unit'">
                    Armada & Unit
                </h1>
                <p class="text-xs text-slate-500 truncate" x-text="lang === 'zh' ? '监控自有设备、租赁期限及维保跟踪' : 'Monitoring unit, status sewa & servis'">
                    Monitoring unit, status sewa & servis
                </p>
            </div>
            <!-- Quick Action Buttons on Mobile -->
            <div class="flex items-center space-x-1.5 shrink-0">
                <button type="button" 
                        @click="openAssetModal()" 
                        class="w-9 h-9 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white flex items-center justify-center shadow-xs transition active:scale-95"
                        title="Tambah Unit Baru">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                </button>
                <button type="button" 
                        @click="openServiceModal(null)" 
                        class="w-9 h-9 rounded-2xl bg-amber-500 hover:bg-amber-600 text-slate-950 flex items-center justify-center shadow-xs transition active:scale-95"
                        title="Catat Servis Unit">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg>
                </button>
            </div>
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
                       :placeholder="lang === 'zh' ? '搜索设备代码、车牌、型号、客户...' : 'Cari kode, plat, model unit, customer...'"
                       placeholder="Cari kode, plat, model unit, customer..." 
                       class="w-full bg-white border border-slate-200/90 rounded-2xl pl-10 pr-4 py-2.5 text-xs text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-blue-500 shadow-2xs">
            </div>
            <button type="button" 
                    @click="mobileFilterOpen = !mobileFilterOpen"
                    class="w-10 h-10 rounded-2xl border border-slate-200/90 bg-white flex items-center justify-center text-slate-600 hover:text-slate-900 shadow-2xs transition active:scale-95 shrink-0"
                    :class="mobileFilterOpen || (statusFilter && statusFilter !== 'all') || (ownershipFilter && ownershipFilter !== 'all') ? 'border-blue-500 text-blue-600 bg-blue-50/50' : ''"
                    aria-label="Filter Lanjutan">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                </svg>
            </button>
        </div>

        <!-- Collapsible Mobile Advanced Status & Ownership Filter -->
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
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Status Operasional</label>
                    <select x-model="statusFilter" @change="submitSearch()" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold text-slate-700 outline-none focus:border-blue-500">
                        <option value="all">Semua Status ({{ $totalFleetCount }})</option>
                        <option value="available">Siap Operasi ({{ $availableCount }})</option>
                        <option value="rented">Sedang Dalam Order ({{ $rentedCount }})</option>
                        <option value="maintenance">Servis / Perawatan ({{ $maintenanceCount }})</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Kepemilikan Unit</label>
                    <select x-model="ownershipFilter" @change="submitSearch()" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold text-slate-700 outline-none focus:border-blue-500">
                        <option value="all">Semua Kepemilikan</option>
                        <option value="company">Milik Sendiri (Keylock Indonesia)</option>
                        <option value="partner">Mitra (Partner / Sub-Rent)</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- 3. Dua Tab Status Utama di Mobile (Seragam Penawaran & Order) -->
        <div class="grid grid-cols-2 gap-2 py-0.5">
            <a href="{{ route('erp.fleet.index', array_merge(request()->except(['page']), ['status' => 'available'])) }}" 
               class="py-2.5 px-3 rounded-xl text-xs text-center transition flex items-center justify-center space-x-1.5 {{ $statusFilter === 'available' ? 'bg-[#142A57] text-white font-bold shadow-xs' : 'bg-white text-slate-700 hover:text-slate-900 border border-[#E5EAF2] font-semibold shadow-2xs' }}"
               aria-label="Tab Siap Operasi">
                <span class="w-2 h-2 rounded-full {{ $statusFilter === 'available' ? 'bg-emerald-300' : 'bg-emerald-500' }}"></span>
                <span x-text="lang === 'zh' ? '空闲可用' : 'Siap Operasi'">Siap Operasi</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] font-black {{ $statusFilter === 'available' ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-800' }}">
                    {{ $availableCount }}
                </span>
            </a>

            <a href="{{ route('erp.fleet.index', array_merge(request()->except(['page']), ['status' => 'rented'])) }}" 
               class="py-2.5 px-3 rounded-xl text-xs text-center transition flex items-center justify-center space-x-1.5 {{ $statusFilter === 'rented' ? 'bg-[#142A57] text-white font-bold shadow-xs' : 'bg-white text-slate-700 hover:text-slate-900 border border-[#E5EAF2] font-semibold shadow-2xs' }}"
               aria-label="Tab Sedang Disewa">
                <span class="w-2 h-2 rounded-full {{ $statusFilter === 'rented' ? 'bg-blue-300' : 'bg-blue-500' }}"></span>
                <span x-text="lang === 'zh' ? '在租订单' : 'Sedang Order'">Sedang Order</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] font-black {{ $statusFilter === 'rented' ? 'bg-white/20 text-white' : 'bg-blue-100 text-blue-800' }}">
                    {{ $rentedCount }}
                </span>
            </a>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- DESKTOP HEADER & CONTROLS (VISIBLE ON SCREENS >= MD)                      -->
    <!-- ========================================================================= -->
    <div class="hidden md:block">
        <!-- 1. PAGE HEADER -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-2xl bg-amber-400 text-slate-950 flex items-center justify-center font-black shadow-xs">
                        <svg class="w-5 h-5 text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-slate-900 tracking-tight" x-text="lang === 'zh' ? '车队与设备管理 (Fleet & Assets)' : 'Armada & Unit Management'">
                            Armada & Unit Management
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5" x-text="lang === 'zh' ? '监控自有设备可用状态、客户租赁期限到期日、以及设备定期维保跟踪记录。' : 'Monitoring ketersediaan unit, status sewa customer, tanggal akhir sewa, dan pencatatan riwayat servis.'">
                            Monitoring ketersediaan unit, status sewa customer, tanggal akhir sewa, dan pencatatan riwayat servis.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Top Right Actions -->
            <div class="flex items-center space-x-2.5 self-start md:self-auto">
                <button type="button" 
                        @click="openServiceModal(null)" 
                        class="bg-amber-500 hover:bg-amber-600 text-slate-950 font-black px-4 py-2.5 rounded-full shadow-xs hover:shadow-md text-xs transition flex items-center space-x-2 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span x-text="lang === 'zh' ? '登记设备维保' : 'Catat Servis Unit'">Catat Servis Unit</span>
                </button>

                <button type="button" 
                        @click="openAssetModal()" 
                        class="bg-blue-600 hover:bg-blue-700 text-white font-extrabold px-5 py-2.5 rounded-full shadow-md hover:shadow-lg text-xs transition flex items-center space-x-2 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    <span x-text="lang === 'zh' ? '新增设备/车辆' : 'Tambah Armada Baru'">Tambah Armada Baru</span>
                </button>
            </div>
        </div>

        <!-- 2. KPI METRICS CARDS -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5 mb-6">
            <!-- Card 1: Total Armada -->
            <div class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1" x-text="lang === 'zh' ? '总设备数量' : 'Total Armada Unit'">Total Armada Unit</p>
                    <h3 class="text-2xl font-black text-slate-900">{{ $totalFleetCount }}</h3>
                    <p class="text-[11px] text-slate-500 mt-0.5" x-text="lang === 'zh' ? '全部登记设备' : 'Total Unit Terdaftar'">Total Unit Terdaftar</p>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-slate-100 text-slate-800 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </div>
            </div>

            <!-- Card 2: Siap Operasi / Available -->
            <a href="{{ route('erp.fleet.index', ['status' => 'available']) }}" class="bg-white rounded-2xl p-4 border border-emerald-200/80 shadow-xs flex items-center justify-between hover:border-emerald-400 transition group">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-wider text-emerald-600 mb-1" x-text="lang === 'zh' ? '空闲待租 (Available)' : 'Siap Operasi (Available)'">Siap Operasi (Available)</p>
                    <h3 class="text-2xl font-black text-emerald-600">{{ $availableCount }}</h3>
                    <p class="text-[11px] text-slate-500 mt-0.5" x-text="lang === 'zh' ? '停放仓库随时出车' : 'Standby di Pool'"></p>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold group-hover:scale-105 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </a>

            <!-- Card 3: Sedang Dalam Order (Rented) -->
            <a href="{{ route('erp.fleet.index', ['status' => 'rented']) }}" class="bg-white rounded-2xl p-4 border border-blue-200/80 shadow-xs flex items-center justify-between hover:border-blue-400 transition group">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-wider text-blue-600 mb-1" x-text="lang === 'zh' ? '租赁中 (In Order)' : 'Sedang Dalam Order'">Sedang Dalam Order</p>
                    <h3 class="text-2xl font-black text-blue-600">{{ $rentedCount }}</h3>
                    <p class="text-[11px] text-slate-500 mt-0.5" x-text="lang === 'zh' ? '客户现场作业中' : 'Di Site Proyek Customer'"></p>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold group-hover:scale-105 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
            </a>

            <!-- Card 4: Dalam Perawatan (Maintenance) -->
            <a href="{{ route('erp.fleet.index', ['status' => 'maintenance']) }}" class="bg-white rounded-2xl p-4 border border-amber-200/80 shadow-xs flex items-center justify-between hover:border-amber-400 transition group">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-wider text-amber-700 mb-1" x-text="lang === 'zh' ? '维保检修中' : 'Servis & Perawatan'">Servis & Perawatan</p>
                    <h3 class="text-2xl font-black text-amber-600">{{ $maintenanceCount }}</h3>
                    <p class="text-[11px] text-slate-500 mt-0.5" x-text="lang === 'zh' ? '在修设备 / 检修中' : 'Sedang di Bengkel'"></p>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-amber-50 text-amber-700 flex items-center justify-center font-bold group-hover:scale-105 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                </div>
            </a>

            <!-- Card 5: Alert Servis / Overdue -->
            <div class="bg-white rounded-2xl p-4 border {{ $overdueServiceCount > 0 ? 'border-rose-300 bg-rose-50/20' : 'border-slate-200/90' }} shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-wider {{ $overdueServiceCount > 0 ? 'text-rose-600' : 'text-slate-400' }} mb-1" x-text="lang === 'zh' ? '维保到期提醒' : 'Perlu Servis / Overdue'">Perlu Servis / Overdue</p>
                    <div class="flex items-baseline space-x-2">
                        <h3 class="text-2xl font-black {{ $overdueServiceCount > 0 ? 'text-rose-600' : 'text-slate-900' }}">{{ $overdueServiceCount }}</h3>
                        @if($upcomingServiceCount > 0)
                            <span class="text-[11px] font-bold text-amber-600 bg-amber-100 px-1.5 py-0.5 rounded-md">+{{ $upcomingServiceCount }} Dekat</span>
                        @endif
                    </div>
                    <p class="text-[11px] text-slate-500 mt-0.5" x-text="lang === 'zh' ? '超过推荐保养周期' : 'Jadwal servis terlewat'"></p>
                </div>
                <div class="w-11 h-11 rounded-2xl {{ $overdueServiceCount > 0 ? 'bg-rose-100 text-rose-600 animate-pulse' : 'bg-slate-100 text-slate-500' }} flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
            </div>
        </div>

        <!-- 3. FILTER & SEARCH CONTROL BAR (DESKTOP) -->
        <div class="bg-white rounded-2xl border border-slate-200/90 p-4 mb-6 shadow-xs space-y-4">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
                <!-- Quick Status Filter Pills -->
                <div class="flex flex-wrap items-center gap-1.5">
                    <a href="{{ route('erp.fleet.index', array_merge(request()->query(), ['status' => 'all'])) }}"
                       class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ ($statusFilter === 'all' || empty($statusFilter)) ? 'bg-slate-900 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        <span x-text="lang === 'zh' ? '全部状态' : 'Semua Status'">Semua Status</span> ({{ $totalFleetCount }})
                    </a>
                    <a href="{{ route('erp.fleet.index', array_merge(request()->query(), ['status' => 'available'])) }}"
                       class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $statusFilter === 'available' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                        <span x-text="lang === 'zh' ? '空闲可用' : 'Siap Operasi'">Siap Operasi</span> ({{ $availableCount }})
                    </a>
                    <a href="{{ route('erp.fleet.index', array_merge(request()->query(), ['status' => 'rented'])) }}"
                       class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $statusFilter === 'rented' ? 'bg-blue-600 text-white shadow-xs' : 'bg-blue-50 text-blue-700 hover:bg-blue-100' }}">
                        <span x-text="lang === 'zh' ? '在租订单' : 'Sedang Dalam Order'">Sedang Dalam Order</span> ({{ $rentedCount }})
                    </a>
                    <a href="{{ route('erp.fleet.index', array_merge(request()->query(), ['status' => 'maintenance'])) }}"
                       class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $statusFilter === 'maintenance' ? 'bg-amber-600 text-white shadow-xs' : 'bg-amber-50 text-amber-700 hover:bg-amber-100' }}">
                        <span x-text="lang === 'zh' ? '检修维护' : 'Servis / Perawatan'">Servis / Perawatan</span> ({{ $maintenanceCount }})
                    </a>
                </div>

                <!-- Ownership Dropdown Filter & Search Bar -->
                <div class="flex flex-col sm:flex-row items-center gap-2.5">
                    <select @change="filterByOwnership($event.target.value)" 
                            class="h-10 bg-slate-50 border border-slate-200 rounded-xl px-3 text-xs font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-amber-400">
                        <option value="all" {{ ($ownershipFilter === 'all' || empty($ownershipFilter)) ? 'selected' : '' }}>Semua Kepemilikan</option>
                        <option value="company" {{ $ownershipFilter === 'company' ? 'selected' : '' }}>Milik Sendiri (Company)</option>
                        <option value="partner" {{ $ownershipFilter === 'partner' ? 'selected' : '' }}>Mitra (Partner / Sub-Rent)</option>
                    </select>

                    <form method="GET" action="{{ route('erp.fleet.index') }}" class="relative w-full sm:w-72">
                        <input type="hidden" name="status" value="{{ $statusFilter }}">
                        <input type="hidden" name="ownership" value="{{ $ownershipFilter }}">
                        <input type="text" 
                               name="search" 
                               value="{{ $search ?? '' }}" 
                               placeholder="Cari kode, plat, model, customer..." 
                               class="w-full h-10 bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-14 text-xs font-medium text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:bg-white shadow-2xs">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </span>
                        <button type="submit" class="absolute right-1.5 top-1/2 -translate-y-1/2 px-2.5 py-1 bg-slate-900 text-white rounded-lg text-[10px] font-bold hover:bg-slate-800 transition">
                            Cari
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 4. ARMADA & UNIT TABLE                                                    -->
    <!-- ========================================================================= -->
        <!-- DESKTOP TABLE VIEW (VISIBLE ON SCREENS >= MD, HIDDEN ON MOBILE) -->
        <div class="hidden md:block overflow-x-auto min-h-[360px]">
            <table class="w-full text-left border-collapse text-xs whitespace-nowrap">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase tracking-wider text-[10px] font-black">
                        <th class="py-3.5 px-4 w-48 font-black">Kode & Model Unit</th>
                        <th class="py-3.5 px-4 w-36">Status & HM</th>
                        <th class="py-3.5 px-4 w-40">Lokasi Saat Ini</th>
                        <th class="py-3.5 px-4 min-w-[220px]">Info Sewa & Akhir Sewa</th>
                        <th class="py-3.5 px-4 w-44">Servis & Jadwal</th>
                        <th class="py-3.5 px-4 w-28 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($armada as $unit)
                        @php
                            $activeItem = $unit->activeOrderItem;
                            $activeOrder = $activeItem ? $activeItem->order : null;
                            $customer = $activeOrder ? $activeOrder->contact : null;
                            $latestService = $unit->latestService;
                            
                            // Calculate Akhir Sewa and Remaining Days
                            $endDate = $activeOrder ? $activeOrder->effective_end_date : null;
                            $remainingDays = null;
                            $isOverdueRental = false;
                            if ($endDate) {
                                $endCarbon = \Carbon\Carbon::parse($endDate)->startOfDay();
                                $nowCarbon = \Carbon\Carbon::today();
                                $diff = $nowCarbon->diffInDays($endCarbon, false);
                                $remainingDays = $diff;
                                if ($diff < 0) {
                                    $isOverdueRental = true;
                                }
                            }

                            // Check Service Overdue
                            $isServiceOverdue = false;
                            $isServiceDueSoon = false;
                            if ($unit->next_service_due) {
                                $dueCarbon = \Carbon\Carbon::parse($unit->next_service_due)->startOfDay();
                                $nowCarbon = \Carbon\Carbon::today();
                                $dueDiff = $nowCarbon->diffInDays($dueCarbon, false);
                                if ($dueDiff < 0) {
                                    $isServiceOverdue = true;
                                } elseif ($dueDiff <= 14) {
                                    $isServiceDueSoon = true;
                                }
                            }
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition group">
                            
                            <!-- 1. Kode & Model Unit -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-start space-x-3">
                                    <div class="w-9 h-9 rounded-xl {{ $unit->status === 'rented' ? 'bg-blue-100 text-blue-700' : ($unit->status === 'available' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700') }} flex items-center justify-center font-black text-xs shrink-0 shadow-2xs">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="flex items-center space-x-2">
                                            <span class="font-mono font-black text-slate-900 text-xs">{{ $unit->asset_code }}</span>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ str_contains(strtolower($unit->ownership), 'company') ? 'bg-slate-100 text-slate-700' : 'bg-purple-100 text-purple-800' }}">
                                                {{ ucfirst($unit->ownership) }}
                                            </span>
                                        </div>
                                        <p class="font-bold text-slate-800 text-xs mt-0.5">{{ $unit->item->name ?? 'Armada / Alat Berat' }}</p>
                                        <div class="flex items-center space-x-2 text-[11px] text-slate-500 mt-0.5">
                                            @if($unit->license_plate)
                                                <span class="font-mono font-bold text-slate-700 bg-slate-100 px-1.5 py-0.2 rounded border border-slate-200">{{ $unit->license_plate }}</span>
                                            @endif
                                            <span>S/N: <strong class="font-mono text-slate-700">{{ $unit->serial_number ?: '-' }}</strong></span>
                                            @if($unit->year)
                                                <span>({{ $unit->year }})</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- 2. Status & HM -->
                            <td class="py-3.5 px-4">
                                <div class="space-y-1">
                                    @if($unit->status === 'available')
                                        <span class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                            <span>Siap Operasi</span>
                                        </span>
                                    @elseif($unit->status === 'rented')
                                        <span class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-blue-100 text-blue-800 border border-blue-200">
                                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                            <span>Sedang Dalam Order</span>
                                        </span>
                                    @elseif($unit->status === 'maintenance')
                                        <span class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-amber-100 text-amber-900 border border-amber-300">
                                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                            <span>Servis / Perawatan</span>
                                        </span>
                                    @elseif($unit->status === 'reserved')
                                        <span class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-purple-100 text-purple-900 border border-purple-200">
                                            <span>Booking (Reserved)</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-slate-100 text-slate-700">
                                            {{ ucfirst($unit->status) }}
                                        </span>
                                    @endif

                                    <div class="text-[11px] text-slate-600 font-medium flex items-center space-x-1">
                                        <span class="text-slate-400">HM:</span>
                                        <span class="font-mono font-bold text-slate-800">{{ number_format($unit->hour_meter ?? 0, 1) }} Jam</span>
                                    </div>
                                </div>
                            </td>

                            <!-- 3. Lokasi Saat Ini -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center space-x-1.5 text-slate-700">
                                    <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    <span class="font-semibold truncate max-w-[180px]" title="{{ $unit->current_location ?: 'Pool Pusat Keylock' }}">
                                        {{ $unit->current_location ?: 'Pool Pusat Keylock' }}
                                    </span>
                                </div>
                            </td>

                            <!-- 4. Info Sewa & Akhir Sewa -->
                            <td class="py-3.5 px-4">
                                @if($unit->status === 'rented' && $activeOrder)
                                    <div class="bg-blue-50/70 p-2.5 rounded-xl border border-blue-200/80 space-y-1.5 shadow-2xs">
                                        <div class="flex items-center justify-between">
                                            <span class="font-bold text-slate-900 truncate max-w-[140px]" title="{{ $customer->name ?? 'Pelanggan' }}">
                                                {{ $customer->name ?? 'Pelanggan' }}
                                            </span>
                                            <a href="{{ route('erp.orders.index', ['search' => $activeOrder->order_number]) }}" 
                                               class="font-mono font-bold text-[10px] text-blue-700 bg-blue-100 hover:bg-blue-200 px-1.5 py-0.5 rounded transition">
                                                #{{ $activeOrder->order_number }}
                                            </a>
                                        </div>

                                        <!-- Tanggal Sewa & Akhir Sewa -->
                                        <div class="text-[11px] text-slate-600 space-y-0.5">
                                            <div class="flex items-center justify-between">
                                                <span class="text-slate-400">Mulai:</span>
                                                <span class="font-bold text-slate-700">{{ $activeOrder->rental_start_date ? $activeOrder->rental_start_date->format('d M Y') : '-' }}</span>
                                            </div>
                                            <div class="flex items-center justify-between pt-0.5 border-t border-blue-200/60">
                                                <span class="font-bold text-slate-800">Akhir Sewa:</span>
                                                <span class="font-black text-slate-900">{{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d M Y') : '-' }}</span>
                                            </div>
                                        </div>

                                        <!-- Countdown / Remaining Days Badge -->
                                        @if($remainingDays !== null)
                                            <div class="pt-0.5">
                                                @if($isOverdueRental)
                                                    <span class="inline-flex items-center space-x-1 px-2 py-0.5 rounded-md text-[10px] font-black bg-rose-100 text-rose-800 border border-rose-300 animate-pulse">
                                                        <span>⚠️ Lewat {{ abs($remainingDays) }} hari!</span>
                                                    </span>
                                                @elseif($remainingDays <= 5)
                                                    <span class="inline-flex items-center space-x-1 px-2 py-0.5 rounded-md text-[10px] font-black bg-amber-100 text-amber-900 border border-amber-300">
                                                        <span>⏰ Sisa {{ $remainingDays }} hari lagi</span>
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center space-x-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                        <span>✓ Sisa {{ $remainingDays }} hari</span>
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @elseif($unit->status === 'rented')
                                    <div class="text-[11px] text-blue-700 bg-blue-50 p-2 rounded-xl border border-blue-200">
                                        <span class="font-bold">Sedang disewa customer</span>
                                        <p class="text-[10px] text-slate-500 mt-0.5">Alokasi order internal</p>
                                    </div>
                                @else
                                    <div class="text-[11px] text-slate-400 flex items-center space-x-1.5">
                                        <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        <span>Standby di pool, siap dialokasikan</span>
                                    </div>
                                @endif
                            </td>

                            <!-- 5. Servis & Jadwal Perawatan -->
                            <td class="py-3.5 px-4">
                                <div class="space-y-1 text-[11px]">
                                    <div class="flex items-center justify-between text-slate-600">
                                        <span class="text-slate-400">Servis Terakhir:</span>
                                        <span class="font-bold text-slate-800">{{ $unit->last_service_at ? $unit->last_service_at->format('d M Y') : '-' }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-slate-400">Jadwal Berikut:</span>
                                        @if($unit->next_service_due)
                                            <span class="font-bold {{ $isServiceOverdue ? 'text-rose-600' : ($isServiceDueSoon ? 'text-amber-600' : 'text-slate-800') }}">
                                                {{ $unit->next_service_due->format('d M Y') }}
                                            </span>
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </div>

                                    <!-- Service Alert Badges -->
                                    @if($isServiceOverdue)
                                        <span class="inline-flex items-center space-x-1 px-1.5 py-0.5 rounded text-[10px] font-black bg-rose-100 text-rose-800 border border-rose-300">
                                            <span>⚠️ Servis Terlambat</span>
                                        </span>
                                    @elseif($isServiceDueSoon)
                                        <span class="inline-flex items-center space-x-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-900 border border-amber-300">
                                            <span>⏰ Segera Servis</span>
                                        </span>
                                    @endif

                                    @if($latestService)
                                        <p class="text-[10px] text-slate-500 truncate max-w-[160px]" title="{{ $latestService->work_description }}">
                                            Histori: {{ $latestService->service_type_label }}
                                        </p>
                                    @endif
                                </div>
                            </td>

                            <!-- 6. Aksi Cepat -->
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center space-x-1.5">
                                    <!-- Catat Servis Button -->
                                    <button type="button" 
                                            @click="openServiceModal({{ json_encode($unit) }})" 
                                            title="Catat Servis Unit" 
                                            class="p-2 rounded-xl bg-amber-50 text-amber-700 hover:bg-amber-100 hover:text-amber-800 border border-amber-200 transition shadow-2xs">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    </button>

                                    <!-- Unit Kembali Button (if rented) -->
                                    @if($unit->status === 'rented')
                                        <button type="button" 
                                                @click="openReturnModal({{ json_encode($unit) }})" 
                                                title="Tandai Selesai Sewa / Unit Kembali ke Pool" 
                                                class="p-2 rounded-xl bg-emerald-50 text-emerald-700 hover:bg-emerald-100 hover:text-emerald-800 border border-emerald-200 transition shadow-2xs">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                                        </button>
                                    @endif

                                    <!-- Detail / History Drawer Button -->
                                    <button type="button" 
                                            @click="openDetailsDrawer({{ $unit->id }})" 
                                            title="Lihat Detail & Riwayat Servis" 
                                            class="p-2 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 hover:text-slate-900 border border-slate-200 transition shadow-2xs">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </button>

                                    <!-- Edit Unit Button -->
                                    <button type="button" 
                                            @click="openEditModal({{ json_encode($unit) }})" 
                                            title="Edit Data Unit" 
                                            class="p-2 rounded-xl bg-blue-50 text-blue-700 hover:bg-blue-100 hover:text-blue-800 border border-blue-200 transition shadow-2xs">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <div class="max-w-sm mx-auto space-y-3">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 mx-auto flex items-center justify-center">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                    </div>
                                    <h4 class="font-black text-slate-800 text-sm">Tidak ada data armada yang cocok</h4>
                                    <p class="text-xs text-slate-500">Coba ubah filter status atau kata kunci pencarian Anda.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- MOBILE CARD VIEW (VISIBLE ON MOBILE < MD, HIDDEN ON DESKTOP) -->
        <div class="block md:hidden p-2.5 sm:p-3.5 space-y-3 sm:space-y-3.5 bg-[#F5F7FB] border-b border-[#E5EAF2]">
            @include('erp.partials.fleet-mobile-cards')
        </div>

        <!-- Pagination Footer -->
        @if($armada->hasPages())
            <div class="px-5 py-3.5 bg-slate-50 border-t border-slate-200">
                {{ $armada->links('erp.pagination') }}
            </div>
        @endif
    </div>

    <!-- ========================================================================= -->
    <!-- 5. MODAL TAMBAH / EDIT ARMADA                                             -->
    <!-- ========================================================================= -->
    <div x-show="modalAssetOpen" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-5 bg-slate-900/60 backdrop-blur-xs overflow-y-auto"
         @click.self="modalAssetOpen = false">
        <div class="bg-white w-full max-w-2xl rounded-3xl shadow-2xl border border-slate-200 overflow-hidden my-auto animate-in zoom-in-95 duration-150 flex flex-col max-h-[92vh]">
            
            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between shrink-0">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-black">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-slate-900" x-text="isEditAsset ? 'Edit Data Armada / Unit' : 'Tambah Armada / Unit Baru'">Tambah Armada / Unit Baru</h3>
                        <p class="text-xs text-slate-500">Isi data identitas spesifikasi fisik dan kepemilikan unit armada.</p>
                    </div>
                </div>
                <button type="button" @click="modalAssetOpen = false" class="text-slate-400 hover:text-slate-600 p-2 rounded-xl hover:bg-slate-200/60 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form :action="isEditAsset ? '/erp/fleet/' + assetForm.id + '/update' : '{{ route('erp.fleet.store') }}'" method="POST" class="flex-1 overflow-y-auto p-6 space-y-4 text-xs">
                @csrf
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Model / Item Catalog -->
                    <div class="sm:col-span-2">
                        <label class="block font-bold text-slate-700 mb-1">Model / Katalog Barang *</label>
                        <select name="item_id" x-model="assetForm.item_id" required class="w-full h-10 bg-slate-50 border border-slate-300 rounded-xl px-3 font-semibold text-xs focus:bg-white focus:border-amber-400 focus:outline-none">
                            <option value="">-- Pilih Model Barang / Unit --</option>
                            @foreach($catalogItems as $item)
                                <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->category->name ?? 'Unit' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Hidden Asset Code & Info Display on Edit -->
                    <input type="hidden" name="asset_code" x-model="assetForm.asset_code">
                    <div x-show="isEditAsset" class="sm:col-span-2 bg-amber-50/80 border border-amber-200/90 rounded-2xl p-3 flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <span class="text-[11px] font-bold text-amber-900">Kode Unit / Asset:</span>
                            <span class="font-mono font-black text-xs text-amber-950 bg-amber-200/80 px-2 py-0.5 rounded-lg" x-text="assetForm.asset_code"></span>
                        </div>
                        <span class="text-[10px] text-amber-700 font-semibold">(Otomatis dikelola sistem)</span>
                    </div>

                    <!-- Nomor Seri / Sasis -->
                    <div class="sm:col-span-2">
                        <label class="block font-bold text-slate-700 mb-1">Nomor Seri / Sasis *</label>
                        <input type="text" name="serial_number" x-model="assetForm.serial_number" required placeholder="Contoh: FD30-109283 / S/N Unit" class="w-full h-10 bg-slate-50 border border-slate-300 rounded-xl px-3 font-mono text-xs focus:bg-white focus:border-amber-400 focus:outline-none">
                    </div>

                    <!-- Tahun Pembuatan -->
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Tahun Pembuatan</label>
                        <input type="number" name="year" x-model="assetForm.year" placeholder="Contoh: 2023" min="1990" max="{{ date('Y') + 1 }}" class="w-full h-10 bg-slate-50 border border-slate-300 rounded-xl px-3 font-mono text-xs focus:bg-white focus:border-amber-400 focus:outline-none">
                    </div>

                    <!-- Hour Meter Terakhir -->
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Hour Meter Saat Ini (Jam)</label>
                        <input type="number" step="0.1" name="hour_meter" x-model="assetForm.hour_meter" placeholder="0.0" class="w-full h-10 bg-slate-50 border border-slate-300 rounded-xl px-3 font-mono text-xs focus:bg-white focus:border-amber-400 focus:outline-none">
                    </div>

                    <!-- Kepemilikan -->
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Kepemilikan Unit *</label>
                        <select name="ownership" x-model="assetForm.ownership" required class="w-full h-10 bg-slate-50 border border-slate-300 rounded-xl px-3 font-bold text-xs focus:bg-white focus:border-amber-400 focus:outline-none">
                            <option value="company">Milik Sendiri (Keylock Indonesia)</option>
                            <option value="partner">Unit Mitra Kerjasama (Sub-Rent)</option>
                            <option value="third_party">Sewa Beli / Pihak Ketiga</option>
                        </select>
                    </div>

                    <!-- Lokasi Saat Ini -->
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Lokasi Saat Ini</label>
                        <input type="text" name="current_location" x-model="assetForm.current_location" placeholder="Pool Pusat Keylock / Site Proyek" class="w-full h-10 bg-slate-50 border border-slate-300 rounded-xl px-3 text-xs focus:bg-white focus:border-amber-400 focus:outline-none">
                    </div>

                    <!-- Status Operasional -->
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Status Operasional *</label>
                        <select name="status" x-model="assetForm.status" required class="w-full h-10 bg-slate-50 border border-slate-300 rounded-xl px-3 font-bold text-xs focus:bg-white focus:border-amber-400 focus:outline-none">
                            <option value="available">Siap Operasi (Available)</option>
                            <option value="rented">Sedang Dalam Order (Rented)</option>
                            <option value="maintenance">Dalam Perawatan (Maintenance)</option>
                            <option value="reserved">Booking (Reserved)</option>
                        </select>
                    </div>

                    <!-- Jadwal Servis Berikutnya -->
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Jadwal Servis Berikutnya</label>
                        <input type="date" name="next_service_due" x-model="assetForm.next_service_due" class="w-full h-10 bg-slate-50 border border-slate-300 rounded-xl px-3 font-bold text-xs focus:bg-white focus:border-amber-400 focus:outline-none">
                    </div>

                    <!-- Catatan Kondisi & Spesifikasi -->
                    <div class="sm:col-span-2">
                        <label class="block font-bold text-slate-700 mb-1">Catatan Kondisi / Attachment</label>
                        <textarea name="condition_notes" x-model="assetForm.condition_notes" rows="2" placeholder="Kondisi ban 90%, include garpu standar 120cm, battery charger..." class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-xs focus:bg-white focus:border-amber-400 focus:outline-none"></textarea>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-200 flex items-center justify-end space-x-2">
                    <button type="button" @click="modalAssetOpen = false" class="px-4 py-2.5 rounded-xl border border-slate-300 font-bold text-slate-600 hover:bg-slate-100 transition">Batal</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold shadow-md transition">Simpan Unit</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 6. MODAL CATAT SERVIS & PERAWATAN (TRACKING SERVICE)                       -->
    <!-- ========================================================================= -->
    <div x-show="modalServiceOpen" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-5 bg-slate-900/60 backdrop-blur-xs overflow-y-auto"
         @click.self="modalServiceOpen = false">
        <div class="bg-white w-full max-w-2xl rounded-3xl shadow-2xl border border-slate-200 overflow-hidden my-auto animate-in zoom-in-95 duration-150 flex flex-col max-h-[92vh]">
            
            <div class="px-6 py-4 border-b border-slate-200 bg-amber-50/70 flex items-center justify-between shrink-0">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-xl bg-amber-500 text-slate-950 flex items-center justify-center font-black">
                        <svg class="w-5 h-5 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-slate-900">Catat Servis & Perawatan Armada</h3>
                        <p class="text-xs text-slate-500">Log riwayat perawatan rutin, ganti oli, sparepart, dan histori teknisi.</p>
                    </div>
                </div>
                <button type="button" @click="modalServiceOpen = false" class="text-slate-400 hover:text-slate-600 p-2 rounded-xl hover:bg-slate-200/60 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form :action="'/erp/fleet/' + serviceForm.asset_id + '/service'" method="POST" class="flex-1 overflow-y-auto p-6 space-y-4 text-xs">
                @csrf
                
                <!-- Unit Armada Selector -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Unit Armada yang Diservis *</label>
                    <select name="asset_id" x-model="serviceForm.asset_id" required class="w-full h-10 bg-slate-50 border border-slate-300 rounded-xl px-3 font-bold text-xs focus:bg-white focus:border-amber-400 focus:outline-none">
                        <option value="">-- Pilih Unit Armada --</option>
                        @foreach($armada as $ast)
                            <option value="{{ $ast->id }}">[{{ $ast->asset_code }}] {{ $ast->item->name ?? 'Unit' }} - {{ $ast->license_plate ?: $ast->serial_number }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Tanggal Servis -->
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Tanggal Servis *</label>
                        <input type="date" name="service_date" x-model="serviceForm.service_date" required class="w-full h-10 bg-slate-50 border border-slate-300 rounded-xl px-3 font-bold text-xs focus:bg-white focus:border-amber-400 focus:outline-none">
                    </div>

                    <!-- Jenis Servis -->
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Jenis Servis *</label>
                        <select name="service_type" x-model="serviceForm.service_type" required class="w-full h-10 bg-slate-50 border border-slate-300 rounded-xl px-3 font-bold text-xs focus:bg-white focus:border-amber-400 focus:outline-none">
                            <option value="periodic">Servis Berkala (Periodic Maintenance)</option>
                            <option value="repair">Perbaikan Kerusakan (Repair)</option>
                            <option value="oil_change">Ganti Oli & Filter Mesin</option>
                            <option value="overhaul">Major Overhaul Mesin</option>
                            <option value="electrical">Sistem Kelistrikan & Baterai</option>
                            <option value="tires">Ban & Undercarriage</option>
                            <option value="sparepart">Penggantian Sparepart</option>
                            <option value="inspection">Inspeksi & Kalibrasi Tahunan</option>
                        </select>
                    </div>

                    <!-- Hour Meter Saat Servis -->
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Hour Meter (HM) Saat Servis</label>
                        <input type="number" step="0.1" name="hour_meter" x-model="serviceForm.hour_meter" placeholder="0.0" class="w-full h-10 bg-slate-50 border border-slate-300 rounded-xl px-3 font-mono text-xs focus:bg-white focus:border-amber-400 focus:outline-none">
                    </div>

                    <!-- Bengkel / Vendor Rekanan -->
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Bengkel Vendor Rekanan</label>
                        <select name="vendor_id" x-model="serviceForm.vendor_id" class="w-full h-10 bg-slate-50 border border-slate-300 rounded-xl px-3 font-semibold text-xs focus:bg-white focus:border-amber-400 focus:outline-none">
                            <option value="">-- Dikerjakan Teknisi Internal Keylock --</option>
                            @foreach($serviceVendors as $ven)
                                <option value="{{ $ven->id }}">{{ $ven->name }} ({{ $ven->category ?: 'Mitra Bengkel' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Nama Teknisi -->
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Nama Teknisi Pelaksana</label>
                        <input type="text" name="technician_name" x-model="serviceForm.technician_name" placeholder="Contoh: Pak Budi / Bengkel Sahabat" class="w-full h-10 bg-slate-50 border border-slate-300 rounded-xl px-3 text-xs focus:bg-white focus:border-amber-400 focus:outline-none">
                    </div>

                    <!-- Total Biaya Servis (Rp) -->
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Total Biaya Servis (Rp)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 font-bold">Rp</span>
                            <input type="hidden" name="cost" :value="serviceForm.cost">
                            <input type="text"
                                   inputmode="numeric"
                                   data-rupiah
                                   :value="formatNumberWithDots(serviceForm.cost)"
                                   @input="serviceForm.cost = parseNumberFromDots($event.target.value)"
                                   placeholder="0"
                                   class="w-full h-10 bg-slate-50 border border-slate-300 rounded-xl pl-9 pr-3 text-right font-mono font-bold text-xs focus:bg-white focus:border-amber-400 focus:outline-none">
                        </div>
                    </div>

                    <!-- Status Pengerjaan -->
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Status Pengerjaan *</label>
                        <select name="status" x-model="serviceForm.status" required class="w-full h-10 bg-slate-50 border border-slate-300 rounded-xl px-3 font-bold text-xs focus:bg-white focus:border-amber-400 focus:outline-none">
                            <option value="completed">Selesai (Completed - Siap Pakai)</option>
                            <option value="in_progress">Sedang Dikerjakan (In Progress - Set Status Maintenance)</option>
                            <option value="scheduled">Terjadwal (Scheduled)</option>
                        </select>
                    </div>

                    <!-- Jadwal Servis Berikutnya -->
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Jadwal Servis Berikutnya</label>
                        <input type="date" name="next_service_due" x-model="serviceForm.next_service_due" class="w-full h-10 bg-slate-50 border border-slate-300 rounded-xl px-3 font-bold text-xs focus:bg-white focus:border-amber-400 focus:outline-none">
                    </div>

                    <!-- Sparepart Diganti -->
                    <div class="sm:col-span-2">
                        <label class="block font-bold text-slate-700 mb-1">Daftar Sparepart yang Diganti</label>
                        <input type="text" name="parts_replaced" x-model="serviceForm.parts_replaced" placeholder="Contoh: Oli Mesin 15W-40 (4L), Filter Oli Toyota, Selang Hidrolik 1/2 inch..." class="w-full h-10 bg-slate-50 border border-slate-300 rounded-xl px-3 text-xs focus:bg-white focus:border-amber-400 focus:outline-none">
                    </div>

                    <!-- Uraian Pekerjaan / Keluhan -->
                    <div class="sm:col-span-2">
                        <label class="block font-bold text-slate-700 mb-1">Deskripsi Pengerjaan & Temuan</label>
                        <textarea name="work_description" x-model="serviceForm.work_description" rows="2" placeholder="Detail pengecekan rem, ganti oli rutin 250 jam, tes angkat beban..." class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-xs focus:bg-white focus:border-amber-400 focus:outline-none"></textarea>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-200 flex items-center justify-end space-x-2">
                    <button type="button" @click="modalServiceOpen = false" class="px-4 py-2.5 rounded-xl border border-slate-300 font-bold text-slate-600 hover:bg-slate-100 transition">Batal</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-black shadow-md transition">Simpan Catatan Servis</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 7. MODAL UNIT KEMBALI (SELESAI SEWA DARI CUSTOMER)                        -->
    <!-- ========================================================================= -->
    <div x-show="modalReturnOpen" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-5 bg-slate-900/60 backdrop-blur-xs"
         @click.self="modalReturnOpen = false">
        <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl border border-slate-200 overflow-hidden my-auto animate-in zoom-in-95 duration-150">
            <div class="px-6 py-4 border-b border-slate-200 bg-emerald-50/80 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-black">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-slate-900">Konfirmasi Unit Selesai Sewa</h3>
                        <p class="text-xs text-slate-500">Tandai unit telah ditarik kembali dari customer ke Pool.</p>
                    </div>
                </div>
                <button type="button" @click="modalReturnOpen = false" class="text-slate-400 hover:text-slate-600 p-2 rounded-xl hover:bg-slate-200/60 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form :action="'/erp/fleet/' + returnForm.asset_id + '/return'" method="POST" class="p-6 space-y-4 text-xs">
                @csrf
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 flex items-center space-x-3">
                    <span class="font-mono font-black text-slate-900 text-sm" x-text="returnForm.asset_code"></span>
                    <span class="text-slate-500">•</span>
                    <span class="font-bold text-slate-800" x-text="returnForm.item_name"></span>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Hour Meter Saat Pengembalian (Jam)</label>
                    <input type="number" step="0.1" name="hour_meter" x-model="returnForm.hour_meter" class="w-full h-10 bg-white border border-slate-300 rounded-xl px-3 font-mono text-xs focus:border-emerald-500 focus:outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Lokasi Pengembalian *</label>
                    <input type="text" name="location" x-model="returnForm.location" value="Pool Pusat Keylock" required class="w-full h-10 bg-white border border-slate-300 rounded-xl px-3 text-xs focus:border-emerald-500 focus:outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Catatan Kondisi Unit Saat Diterima</label>
                    <textarea name="condition_notes" x-model="returnForm.condition_notes" rows="2" placeholder="Unit kembali dalam kondisi bersih, mesin normal, solar 1/2 tangki..." class="w-full border border-slate-300 rounded-xl p-3 text-xs focus:border-emerald-500 focus:outline-none"></textarea>
                </div>

                <p class="text-[11px] text-emerald-800 bg-emerald-50 p-3 rounded-xl border border-emerald-200 font-medium">
                    Status unit akan otomatis diperbarui menjadi <strong>"Siap Operasi (Available)"</strong> dan dapat dialokasikan untuk order baru.
                </p>

                <div class="pt-3 border-t border-slate-200 flex items-center justify-end space-x-2">
                    <button type="button" @click="modalReturnOpen = false" class="px-4 py-2.5 rounded-xl border border-slate-300 font-bold text-slate-600 hover:bg-slate-100 transition">Batal</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold shadow-md transition">Konfirmasi Selesai & Kembali ke Pool</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 8. DRAWER DETAIL ARMADA & RIWAYAT SERVIS LENGKAP                           -->
    <!-- ========================================================================= -->
    <div x-show="detailsDrawerOpen" 
         x-cloak 
         class="fixed inset-0 z-50 flex justify-end bg-slate-900/50 backdrop-blur-xs transition"
         @click.self="detailsDrawerOpen = false">
        
        <div class="bg-white w-full max-w-xl h-full shadow-2xl border-l border-slate-200 flex flex-col animate-in slide-in-from-right duration-200 overflow-hidden">
            <!-- Drawer Header -->
            <div class="p-5 border-b border-slate-200 bg-slate-50 flex items-center justify-between shrink-0">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-2xl bg-amber-400 text-slate-950 flex items-center justify-center font-black">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <div>
                        <div class="flex items-center space-x-2">
                            <h3 class="text-base font-black text-slate-900" x-text="selectedAsset?.asset_code || 'Detail Unit'"></h3>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase" 
                                  :class="selectedAsset?.status === 'rented' ? 'bg-blue-100 text-blue-800' : (selectedAsset?.status === 'available' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800')"
                                  x-text="selectedAsset?.status || ''"></span>
                        </div>
                        <p class="text-xs text-slate-500" x-text="selectedAsset?.item?.name || ''"></p>
                    </div>
                </div>
                <button type="button" @click="detailsDrawerOpen = false" class="text-slate-400 hover:text-slate-600 p-2 rounded-xl hover:bg-slate-200 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Drawer Body -->
            <div class="flex-1 overflow-y-auto p-5 space-y-5 text-xs">
                
                <!-- Unit Quick Specs Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 bg-slate-50 p-4 rounded-2xl border border-slate-200">
                    <div>
                        <span class="text-[10px] text-slate-400 font-bold uppercase block">Plat Nomor</span>
                        <span class="font-mono font-bold text-slate-800" x-text="selectedAsset?.license_plate || '-'"></span>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-400 font-bold uppercase block">Nomor Seri</span>
                        <span class="font-mono font-bold text-slate-800" x-text="selectedAsset?.serial_number || '-'"></span>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-400 font-bold uppercase block">Tahun</span>
                        <span class="font-bold text-slate-800" x-text="selectedAsset?.year || '-'"></span>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-400 font-bold uppercase block">Hour Meter</span>
                        <span class="font-mono font-bold text-slate-800" x-text="Number(selectedAsset?.hour_meter || 0).toLocaleString('id-ID') + ' Jam'"></span>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-400 font-bold uppercase block">Kepemilikan</span>
                        <span class="font-bold text-slate-800" x-text="selectedAsset?.ownership || '-'"></span>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-400 font-bold uppercase block">Lokasi</span>
                        <span class="font-bold text-slate-800 truncate block" x-text="selectedAsset?.current_location || '-'"></span>
                    </div>
                </div>

                <!-- Active Rental Order Card (if rented) -->
                <template x-if="selectedAsset?.status === 'rented' && selectedAsset?.active_order_item?.order">
                    <div class="bg-blue-50/80 p-4 rounded-2xl border border-blue-200 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="font-black text-blue-900 text-xs uppercase tracking-wider flex items-center space-x-1">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                <span>Informasi Sewa Pelanggan Aktif</span>
                            </span>
                            <span class="font-mono font-bold text-blue-700 bg-blue-100 px-2 py-0.5 rounded text-[10px]" x-text="'#' + selectedAsset.active_order_item.order.order_number"></span>
                        </div>
                        <div class="text-xs space-y-1 pt-1">
                            <div class="flex justify-between">
                                <span class="text-slate-500">Customer:</span>
                                <span class="font-extrabold text-slate-900" x-text="selectedAsset.active_order_item.order.contact?.name || '-'"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Mulai Sewa:</span>
                                <span class="font-bold text-slate-800" x-text="selectedAsset.active_order_item.order.rental_start_date ? String(selectedAsset.active_order_item.order.rental_start_date).split('T')[0] : '-'"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Akhir Sewa:</span>
                                <span class="font-black text-slate-900" x-text="selectedAsset.active_order_item.order.rental_end_date ? String(selectedAsset.active_order_item.order.rental_end_date).split('T')[0] : '-'"></span>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Riwayat Servis & Maintenance Log -->
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <h4 class="font-black text-slate-900 text-xs uppercase tracking-wider flex items-center space-x-1.5">
                            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span>Riwayat Servis & Perawatan (Tracking Service)</span>
                        </h4>
                        <span class="text-[10px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full" x-text="(selectedAsset?.services?.length || 0) + ' Servis'"></span>
                    </div>

                    <div class="space-y-3">
                        <template x-for="(srv, sIdx) in (selectedAsset?.services || [])" :key="sIdx">
                            <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/60 space-y-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <span class="font-mono font-black text-slate-900 text-xs" x-text="srv.service_number"></span>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold" 
                                              :class="srv.status === 'completed' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-900'"
                                              x-text="srv.status === 'completed' ? 'Selesai' : 'Sedang Dikerjakan'"></span>
                                    </div>
                                    <span class="font-bold text-slate-600 text-xs" x-text="srv.service_date ? String(srv.service_date).split('T')[0] : ''"></span>
                                </div>

                                <p class="font-bold text-slate-800 text-xs" x-text="srv.work_description || srv.service_type"></p>

                                <div class="grid grid-cols-2 gap-2 text-[11px] text-slate-500 pt-1 border-t border-slate-200/60">
                                    <div>
                                        <span class="text-slate-400">Hour Meter:</span>
                                        <span class="font-mono font-bold text-slate-700" x-text="Number(srv.hour_meter || 0) + ' Jam'"></span>
                                    </div>
                                    <div>
                                        <span class="text-slate-400">Biaya:</span>
                                        <span class="font-mono font-bold text-emerald-700" x-text="'Rp ' + Number(srv.cost || 0).toLocaleString('id-ID')"></span>
                                    </div>
                                    <div class="col-span-2" x-show="srv.parts_replaced">
                                        <span class="text-slate-400">Sparepart:</span>
                                        <span class="font-medium text-slate-700" x-text="srv.parts_replaced"></span>
                                    </div>
                                    <div class="col-span-2" x-show="srv.technician_name || srv.vendor">
                                        <span class="text-slate-400">Teknisi/Bengkel:</span>
                                        <span class="font-medium text-slate-700" x-text="srv.technician_name || (srv.vendor ? srv.vendor.name : '')"></span>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <div x-show="!selectedAsset?.services || selectedAsset.services.length === 0" class="p-6 text-center text-slate-400 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                            Belum ada riwayat servis tercatat untuk armada ini.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Drawer Footer -->
            <div class="p-4 border-t border-slate-200 bg-slate-50 flex items-center justify-between shrink-0">
                <button type="button" 
                        @click="openServiceModal(selectedAsset); detailsDrawerOpen = false" 
                        class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-black rounded-xl text-xs transition flex items-center space-x-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span>Catat Servis Unit Ini</span>
                </button>
                <button type="button" @click="detailsDrawerOpen = false" class="px-4 py-2 border border-slate-300 rounded-xl font-bold text-slate-600 hover:bg-slate-100 transition text-xs">
                    Tutup
                </button>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    function erpFleetManager() {
        return {
            modalAssetOpen: false,
            modalServiceOpen: false,
            modalReturnOpen: false,
            detailsDrawerOpen: false,
            isEditAsset: false,
            selectedAsset: null,
            formatNumberWithDots: window.formatNumberWithDots,
            parseNumberFromDots: window.parseNumberFromDots,
            maskRupiah: window.maskRupiah,
            mobileFilterOpen: false,
            searchQuery: '{{ request('search') }}',
            statusFilter: '{{ $statusFilter ?? 'all' }}',
            ownershipFilter: '{{ $ownershipFilter ?? 'all' }}',
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
                if (this.ownershipFilter && this.ownershipFilter !== 'all') {
                    url.searchParams.set('ownership', this.ownershipFilter);
                } else {
                    url.searchParams.delete('ownership');
                }
                url.searchParams.delete('page');
                window.location.href = url.toString();
            },

            clearSearch() {
                this.searchQuery = '';
                this.statusFilter = 'all';
                this.ownershipFilter = 'all';
                const url = new URL(window.location.href);
                url.searchParams.delete('search');
                url.searchParams.delete('status');
                url.searchParams.delete('ownership');
                url.searchParams.delete('page');
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

            matchesRow(haystack, status, ownership) {
                const q = (this.searchQuery || '').toLowerCase().trim();
                const matchQuery = !q || haystack.includes(q);
                const matchStatus = !this.statusFilter || this.statusFilter === 'all' || status === this.statusFilter;
                const matchOwnership = !this.ownershipFilter || this.ownershipFilter === 'all' || ownership === this.ownershipFilter;
                return matchQuery && matchStatus && matchOwnership;
            },

            assetForm: {
                id: null,
                item_id: '',
                asset_code: '',
                serial_number: '',
                license_plate: '',
                year: '{{ date('Y') }}',
                hour_meter: 0,
                ownership: 'company',
                current_location: 'Pool Pusat Keylock',
                status: 'available',
                condition_notes: '',
                next_service_due: ''
            },

            serviceForm: {
                asset_id: '',
                service_date: '{{ date('Y-m-d') }}',
                service_type: 'periodic',
                hour_meter: 0,
                vendor_id: '',
                technician_name: '',
                cost: 0,
                status: 'completed',
                parts_replaced: '',
                work_description: '',
                next_service_due: ''
            },

            returnForm: {
                asset_id: '',
                asset_code: '',
                item_name: '',
                hour_meter: 0,
                location: 'Pool Pusat Keylock',
                condition_notes: ''
            },

            openAssetModal() {
                this.isEditAsset = false;
                this.assetForm = {
                    id: null,
                    item_id: '',
                    asset_code: '',
                    serial_number: '',
                    license_plate: '',
                    year: '{{ date('Y') }}',
                    hour_meter: 0,
                    ownership: 'company',
                    current_location: 'Pool Pusat Keylock',
                    status: 'available',
                    condition_notes: '',
                    next_service_due: ''
                };
                this.modalAssetOpen = true;
            },

            openEditModal(unit) {
                this.isEditAsset = true;
                this.assetForm = {
                    id: unit.id,
                    item_id: unit.item_id,
                    asset_code: unit.asset_code,
                    serial_number: unit.serial_number,
                    license_plate: unit.license_plate,
                    year: unit.year,
                    hour_meter: unit.hour_meter,
                    ownership: unit.ownership,
                    current_location: unit.current_location,
                    status: unit.status,
                    condition_notes: unit.condition_notes,
                    next_service_due: unit.next_service_due ? String(unit.next_service_due).split('T')[0] : ''
                };
                this.modalAssetOpen = true;
            },

            openServiceModal(unit = null) {
                this.serviceForm = {
                    asset_id: unit ? unit.id : '',
                    service_date: '{{ date('Y-m-d') }}',
                    service_type: 'periodic',
                    hour_meter: unit ? unit.hour_meter : 0,
                    vendor_id: '',
                    technician_name: '',
                    cost: 0,
                    status: 'completed',
                    parts_replaced: '',
                    work_description: '',
                    next_service_due: ''
                };
                this.modalServiceOpen = true;
            },

            openReturnModal(unit) {
                this.returnForm = {
                    asset_id: unit.id,
                    asset_code: unit.asset_code,
                    item_name: unit.item ? unit.item.name : 'Armada',
                    hour_meter: unit.hour_meter || 0,
                    location: 'Pool Pusat Keylock',
                    condition_notes: ''
                };
                this.modalReturnOpen = true;
            },

            async openDetailsDrawer(assetId) {
                try {
                    const res = await fetch('/erp/fleet/' + assetId + '/details', {
                        headers: { 'Accept': 'application/json' }
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.selectedAsset = data.asset;
                        this.detailsDrawerOpen = true;
                    }
                } catch (e) {
                    alert('Gagal memuat detail armada.');
                }
            },

            filterByOwnership(val) {
                const url = new URL(window.location.href);
                if (val && val !== 'all') {
                    url.searchParams.set('ownership', val);
                } else {
                    url.searchParams.delete('ownership');
                }
                window.location.href = url.toString();
            }
        };
    }
</script>
@endpush
@endsection
