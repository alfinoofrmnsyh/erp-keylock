@extends('layouts.erp')

@section('title', 'Master Data - Keylock Indonesia')

@section('breadcrumbs')
    <a href="{{ route('erp.index') }}" class="hover:text-slate-800">Home</a>
    <span>›</span>
    <span class="text-slate-800 font-semibold" x-text="lang === 'zh' ? '主数据管理' : 'Master Data'">Master Data</span>
@endsection

@section('content')
<div x-data="erpMasterDataManager('{{ $tab }}', @js($vendorCategories), @js($customers), @js($vendors), @js($salesUsers), @js($availableLeads))">

    <!-- ========================================================================= -->
    <!-- 1. PAGE HEADER                                                            -->
    <!-- ========================================================================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-black text-slate-900 tracking-tight" x-text="lang === 'zh' ? '主数据管理中心' : 'Master Data Management'">
                Master Data Management
            </h2>
            <p class="text-xs text-slate-500 mt-1" x-text="lang === 'zh' ? '集中管理客户与多类别供应商（含承运运输商与物流）。' : 'Pusat pengelolaan master data Customer dan Vendor (termasuk ekspedisi & armada pengiriman).'">
                Pusat pengelolaan master data Customer dan Vendor (termasuk ekspedisi & armada pengiriman).
            </p>
        </div>

        <!-- Top Right Action: Tambah Data Baru Button -->
        <button type="button" 
                @click="openCreateModal()" 
                class="bg-blue-600 hover:bg-blue-700 text-white font-extrabold px-5 py-2.5 rounded-full shadow-md hover:shadow-lg text-xs transition flex items-center space-x-2 self-start md:self-auto active:scale-95">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
            <span x-text="getCreateButtonText()">Tambah Data Baru</span>
        </button>
    </div>

    <!-- ========================================================================= -->
    <!-- 2. PILL TABS BAR (HANYA CUSTOMER & VENDOR)                                -->
    <!-- ========================================================================= -->
    <div class="flex flex-wrap items-center gap-3 mb-6">
        
        <!-- Tab 1: Customer -->
        <button type="button" 
                @click="switchTab('customer')"
                :class="activeTab === 'customer' ? 'bg-white text-slate-900 shadow-sm border border-slate-200/90 font-black' : 'bg-slate-100/80 text-slate-600 hover:bg-white hover:text-slate-900 border border-transparent font-bold'"
                class="px-4 py-2 rounded-2xl text-xs transition flex items-center space-x-2.5">
            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <span x-text="lang === 'zh' ? '客户 (Customer)' : 'Customer'">Customer</span>
            <span class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 text-[10px] font-black">
                {{ $stats['total_customers'] }}
            </span>
        </button>

        <!-- Tab 2: Vendor (Multiple Categories incl. Vendor Pengiriman) -->
        <button type="button" 
                @click="switchTab('vendor')"
                :class="activeTab === 'vendor' ? 'bg-white text-slate-900 shadow-sm border border-slate-200/90 font-black' : 'bg-slate-100/80 text-slate-600 hover:bg-white hover:text-slate-900 border border-transparent font-bold'"
                class="px-4 py-2 rounded-2xl text-xs transition flex items-center space-x-2.5">
            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
            <span x-text="lang === 'zh' ? '供应商 (Vendor)' : 'Vendor'">Vendor</span>
            <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-black">
                {{ $stats['total_vendors'] }}
            </span>
        </button>
    </div>

    <!-- ========================================================================= -->
    <!-- 3. STATS OVERVIEW CARDS                                                   -->
    <!-- ========================================================================= -->
    
    <!-- Customer Stats -->
    <div x-show="activeTab === 'customer'" class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-[11px] font-bold text-slate-400 block mb-1" x-text="lang === 'zh' ? '客户总数' : 'Total Customer'">Total Customer</span>
            <p class="text-2xl font-black text-slate-900">{{ $stats['total_customers'] }}</p>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-[11px] font-bold text-slate-400 block mb-1" x-text="lang === 'zh' ? '正常合作客户' : 'Customer Aktif'">Customer Aktif</span>
            <p class="text-2xl font-black text-emerald-600">{{ $stats['active_customers'] }}</p>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-[11px] font-bold text-slate-400 block mb-1" x-text="lang === 'zh' ? '已停用客户' : 'Customer Nonaktif'">Customer Nonaktif</span>
            <p class="text-2xl font-black text-slate-400">{{ $stats['inactive_customers'] }}</p>
        </div>
    </div>

    <!-- Vendor Stats: DYNAMIC SUMMARY CATEGORY CARDS (Requirement 2.c) -->
    <div x-show="activeTab === 'vendor'" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
        <!-- Card 1: Total Vendor -->
        <div class="bg-white p-3.5 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-[10px] font-bold text-slate-400 block mb-1" x-text="lang === 'zh' ? '供应商总数' : 'Total Vendor'">Total Vendor</span>
            <p class="text-xl font-black text-slate-900">{{ $stats['total_vendors'] }}</p>
        </div>

        <!-- Dynamic Category Cards -->
        @foreach($stats['vendor_category_counts'] as $catName => $catCount)
            @php
                $isShipping = ($catName === 'Vendor Pengiriman');
            @endphp
            <div class="{{ $isShipping ? 'bg-emerald-50/70 border-emerald-200' : 'bg-white border-slate-200/80' }} p-3.5 rounded-2xl border shadow-xs transition hover:shadow-sm">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-[10px] font-bold {{ $isShipping ? 'text-emerald-800' : 'text-slate-500' }} truncate max-w-[120px]" title="{{ $catName }}">
                        {{ $isShipping ? 'Pengiriman' : $catName }}
                    </span>
                    @if($isShipping)
                        <span class="text-[8px] bg-emerald-200 text-emerald-900 font-extrabold px-1 rounded">DO</span>
                    @endif
                </div>
                <p class="text-xl font-black {{ $isShipping ? 'text-emerald-700' : 'text-slate-900' }}">{{ $catCount }}</p>
            </div>
        @endforeach
    </div>

    <!-- ========================================================================= -->
    <!-- 4. CONTROLS BAR: SEARCH & VENDOR CATEGORY FILTER                          -->
    <!-- ========================================================================= -->
    <div class="bg-white rounded-3xl p-3 border border-slate-200/80 shadow-xs mb-6 flex flex-col md:flex-row items-center justify-between gap-3">
        <!-- Search Input -->
        <div class="relative w-full md:w-96">
            <svg class="w-4 h-4 text-slate-400 absolute left-4 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input type="text" 
                   x-model="searchQuery" 
                   :placeholder="getSearchPlaceholder()"
                   class="w-full bg-transparent pl-11 pr-4 py-2 text-xs text-slate-900 placeholder:text-slate-400 focus:outline-none">
        </div>

        <!-- Filter Controls -->
        <div class="flex items-center space-x-2.5 self-end md:self-auto w-full md:w-auto justify-end">
            <!-- Vendor Category Filter (Dynamic Options) -->
            <template x-if="activeTab === 'vendor'">
                <select x-model="vendorCategoryFilter" class="bg-slate-50 border border-slate-200 rounded-2xl px-4 py-2 text-xs font-semibold text-slate-700 outline-none focus:border-blue-400">
                    <option value="all" x-text="lang === 'zh' ? '所有供应商类别' : 'Semua Kategori Vendor'">Semua Kategori Vendor</option>
                    <template x-for="cat in availableCategories" :key="cat">
                        <option :value="cat" x-text="cat"></option>
                    </template>
                </select>
            </template>

            <!-- Reset / Refresh -->
            <button type="button" @click="resetFilters()" class="w-9 h-9 rounded-2xl bg-slate-50 hover:bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-500 hover:text-slate-800 transition" title="Reset Filter">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            </button>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 5. DATA TABLES                                                            -->
    <!-- ========================================================================= -->

    <!-- ========================== TAB 1: CUSTOMER ========================== -->
    <div x-show="activeTab === 'customer'" class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs whitespace-nowrap">
                <thead>
                    <tr class="border-b border-slate-100 text-[11px] font-black uppercase tracking-wider text-slate-400 bg-slate-50/50">
                        <th class="py-4 px-4 w-12 text-center" x-text="lang === 'zh' ? '序号' : 'SL'">SL</th>
                        <th class="py-4 px-4" x-text="lang === 'zh' ? '客户编号' : 'Kode Customer'">Kode Customer</th>
                        <th class="py-4 px-4" x-text="lang === 'zh' ? '客户 / 公司名称' : 'Nama Customer'">Nama Customer</th>
                        <th class="py-4 px-4" x-text="lang === 'zh' ? '负责人 / PIC' : 'Nama PIC'">Nama PIC</th>
                        <th class="py-4 px-4" x-text="lang === 'zh' ? '销售负责人' : 'Sales By'">Sales By</th>
                        <th class="py-4 px-4" x-text="lang === 'zh' ? '来源线索号' : 'No. Leads'">No. Leads</th>
                        <th class="py-4 px-4" x-text="lang === 'zh' ? '联系电话' : 'No. Telepon'">No. Telepon</th>
                        <th class="py-4 px-4" x-text="lang === 'zh' ? '电子邮箱' : 'Email'">Email</th>
                        <th class="py-4 px-4" x-text="lang === 'zh' ? '状态' : 'Status'">Status</th>
                        <th class="py-4 px-6 text-center sticky right-0 bg-slate-50 z-10 shadow-[-6px_0_8px_-4px_rgba(0,0,0,0.06)] border-l border-slate-200" x-text="lang === 'zh' ? '操作' : 'Aksi'">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    <template x-for="(cust, idx) in filteredCustomers" :key="cust.id">
                        <tr class="hover:bg-slate-50/80 transition group">
                            <td class="py-4 px-4 text-center font-bold text-slate-400" x-text="String(idx + 1).padStart(2, '0')"></td>
                            <td class="py-4 px-4 font-mono font-bold text-slate-900" x-text="cust.code || ('CUST-' + cust.id)"></td>
                            <td class="py-4 px-4">
                                <span class="font-extrabold text-slate-900 block" x-text="cust.name"></span>
                                <span class="block text-[10px] text-slate-400 truncate max-w-xs" x-text="cust.billing_address || ''"></span>
                            </td>
                            <td class="py-4 px-4 font-semibold text-slate-800" x-text="cust.pic_name || '-'"></td>
                            
                            <!-- Sales By (PIC Sales) with Active Dot -->
                            <td class="py-4 px-4">
                                <template x-if="cust.sales || cust.sales_name">
                                    <span class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-xl text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200 shadow-2xs">
                                        <span :class="(cust.sales && cust.sales.is_active) ? 'bg-emerald-500' : 'bg-slate-400'" class="w-1.5 h-1.5 rounded-full" :title="(cust.sales && cust.sales.is_active) ? 'Sales Aktif' : 'Sales Nonaktif'"></span>
                                        <span x-text="cust.sales ? cust.sales.name : cust.sales_name"></span>
                                    </span>
                                </template>
                                <template x-if="!cust.sales && !cust.sales_name">
                                    <span class="text-slate-400 font-mono text-[11px]">-</span>
                                </template>
                            </td>

                            <!-- No. Leads (Originating CRM Lead) -->
                            <td class="py-4 px-4">
                                <template x-if="cust.lead_code || (cust.lead && cust.lead.code)">
                                    <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-xl text-[11px] font-mono font-bold bg-amber-50 text-amber-800 border border-amber-200 shadow-2xs">
                                        <svg class="w-3 h-3 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                        <span x-text="cust.lead_code || cust.lead.code"></span>
                                    </span>
                                </template>
                                <template x-if="!cust.lead_code && (!cust.lead || !cust.lead.code)">
                                    <span class="text-slate-400 font-mono text-[11px]">-</span>
                                </template>
                            </td>

                            <td class="py-4 px-4 font-mono text-slate-600" x-text="cust.phone || '-'"></td>
                            <td class="py-4 px-4 text-slate-500" x-text="cust.email || '-'"></td>
                            <td class="py-4 px-4">
                                <span :class="cust.is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-500 border-slate-200'" class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold border">
                                    <span :class="cust.is_active ? 'bg-emerald-500' : 'bg-slate-400'" class="w-1.5 h-1.5 rounded-full"></span>
                                    <span x-text="cust.is_active ? (lang === 'zh' ? '正常' : 'Aktif') : (lang === 'zh' ? '已停用' : 'Nonaktif')"></span>
                                </span>
                            </td>
                            <!-- Action Column: ICON ONLY BUTTONS (Requirement 1.b) -->
                            <td class="py-4 px-6 text-center sticky right-0 bg-white group-hover:bg-slate-50 transition z-10 shadow-[-6px_0_8px_-4px_rgba(0,0,0,0.06)] border-l border-slate-100">
                                <div class="flex items-center justify-center space-x-2">
                                    <!-- Edit Icon Button -->
                                    <button type="button" 
                                            @click="openEditModal(cust)" 
                                            class="w-8 h-8 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-600 border border-blue-200 flex items-center justify-center transition shadow-xs" 
                                            :title="lang === 'zh' ? '编辑客户' : 'Edit Customer'">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                        </svg>
                                    </button>

                                    <!-- Toggle Active / Deactivate Icon Button -->
                                    <button type="button" 
                                            @click="toggleStatus(cust)" 
                                            :class="cust.is_active ? 'bg-rose-50 hover:bg-rose-100 text-rose-600 border-rose-200' : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-600 border-emerald-200'"
                                            class="w-8 h-8 rounded-xl border flex items-center justify-center transition shadow-xs" 
                                            :title="cust.is_active ? (lang === 'zh' ? '停用客户' : 'Nonaktifkan Customer') : (lang === 'zh' ? '启用客户' : 'Aktifkan Customer')">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="filteredCustomers.length === 0">
                        <td colspan="10" class="text-center py-10 text-slate-400" x-text="lang === 'zh' ? '暂无客户数据' : 'Belum ada data customer.'">
                            Belum ada data customer.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ========================== TAB 2: VENDOR ========================== -->
    <div x-show="activeTab === 'vendor'" class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs whitespace-nowrap">
                <thead>
                    <tr class="border-b border-slate-100 text-[11px] font-black uppercase tracking-wider text-slate-400 bg-slate-50/50">
                        <th class="py-4 px-4 w-12 text-center" x-text="lang === 'zh' ? '序号' : 'SL'">SL</th>
                        <th class="py-4 px-4" x-text="lang === 'zh' ? '供应商编号' : 'Kode Vendor'">Kode Vendor</th>
                        <th class="py-4 px-4" x-text="lang === 'zh' ? '供应商名称' : 'Nama Vendor'">Nama Vendor</th>
                        <th class="py-4 px-4" x-text="lang === 'zh' ? '供应商分类' : 'Kategori Vendor'">Kategori Vendor</th>
                        <th class="py-4 px-4" x-text="lang === 'zh' ? '司机 / 负责人' : 'Driver / PIC'">Driver / PIC</th>
                        <th class="py-4 px-4" x-text="lang === 'zh' ? '联系电话' : 'No. Telepon'">No. Telepon</th>
                        <th class="py-4 px-4" x-text="lang === 'zh' ? '备注说明 / 车队属性' : 'Catatan / Ekspedisi'">Catatan / Ekspedisi</th>
                        <th class="py-4 px-4" x-text="lang === 'zh' ? '状态' : 'Status'">Status</th>
                        <!-- Action: ICON ONLY (Requirement 2.d & 2.e) -->
                        <th class="py-4 px-6 text-center sticky right-0 bg-slate-50 z-10 shadow-[-6px_0_8px_-4px_rgba(0,0,0,0.06)] border-l border-slate-200" x-text="lang === 'zh' ? '操作' : 'Aksi'">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    <template x-for="(vnd, idx) in filteredVendors" :key="vnd.id">
                        <tr class="hover:bg-slate-50/80 transition group">
                            <td class="py-4 px-4 text-center font-bold text-slate-400" x-text="String(idx + 1).padStart(2, '0')"></td>
                            <td class="py-4 px-4 font-mono font-bold text-slate-900" x-text="vnd.code || ('VND-' + vnd.id)"></td>
                            <td class="py-4 px-4">
                                <div class="font-extrabold text-slate-900 flex items-center space-x-1.5">
                                    <span x-text="vnd.name"></span>
                                    <template x-if="vnd.category === 'Vendor Pengiriman'">
                                        <span class="px-1.5 py-0.2 rounded text-[9px] font-black bg-emerald-100 text-emerald-800 border border-emerald-300">
                                            🚚 DO
                                        </span>
                                    </template>
                                </div>
                                <span class="text-[10px] text-slate-400" x-text="vnd.email || '-'"></span>
                            </td>
                            <td class="py-4 px-4">
                                <span :class="vnd.category === 'Vendor Pengiriman' ? 'bg-emerald-50 text-emerald-800 border-emerald-300' : 'bg-slate-100 text-slate-800 border-slate-200'" class="px-2.5 py-1 rounded-full text-[10px] font-bold border">
                                    <span x-text="vnd.category || 'Vendor Umum'"></span>
                                </span>
                            </td>
                            <td class="py-4 px-4 font-semibold text-slate-800" x-text="vnd.pic_name || '-'"></td>
                            <td class="py-4 px-4 font-mono text-slate-600" x-text="vnd.phone || '-'"></td>
                            <td class="py-4 px-4 text-slate-500 max-w-xs truncate" :title="vnd.notes" x-text="vnd.notes || '-'"></td>
                            <td class="py-4 px-4">
                                <span :class="vnd.is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-500 border-slate-200'" class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold border">
                                    <span :class="vnd.is_active ? 'bg-emerald-500' : 'bg-slate-400'" class="w-1.5 h-1.5 rounded-full"></span>
                                    <span x-text="vnd.is_active ? (lang === 'zh' ? '正常' : 'Aktif') : (lang === 'zh' ? '已停用' : 'Nonaktif')"></span>
                                </span>
                            </td>
                            <!-- Action Column: ICON ONLY BUTTONS (Requirement 2.d & 2.e: Detail Vendor Dihapus) -->
                            <td class="py-4 px-6 text-center sticky right-0 bg-white group-hover:bg-slate-50 transition z-10 shadow-[-6px_0_8px_-4px_rgba(0,0,0,0.06)] border-l border-slate-100">
                                <div class="flex items-center justify-center space-x-2">
                                    <!-- Edit Icon Button -->
                                    <button type="button" 
                                            @click="openEditModal(vnd)" 
                                            class="w-8 h-8 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-600 border border-blue-200 flex items-center justify-center transition shadow-xs" 
                                            :title="lang === 'zh' ? '编辑供应商' : 'Edit Vendor'">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                        </svg>
                                    </button>

                                    <!-- Toggle Active / Deactivate Icon Button -->
                                    <button type="button" 
                                            @click="toggleStatus(vnd)" 
                                            :class="vnd.is_active ? 'bg-rose-50 hover:bg-rose-100 text-rose-600 border-rose-200' : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-600 border-emerald-200'"
                                            class="w-8 h-8 rounded-xl border flex items-center justify-center transition shadow-xs" 
                                            :title="vnd.is_active ? (lang === 'zh' ? '停用供应商' : 'Nonaktifkan Vendor') : (lang === 'zh' ? '启用供应商' : 'Aktifkan Vendor')">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="filteredVendors.length === 0">
                        <td colspan="9" class="text-center py-10 text-slate-400" x-text="lang === 'zh' ? '暂无供应商数据' : 'Belum ada data vendor.'">
                            Belum ada data vendor.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 6. MODAL TAMBAH & EDIT DATA (CUSTOMER & VENDOR)                           -->
    <!-- ========================================================================= -->
    <div x-show="contactModalOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 drawer-overlay"
         @click.self="contactModalOpen = false" 
         style="display: none;">
        
        <div class="w-full max-w-lg bg-white rounded-3xl border border-slate-200 shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
            <!-- Modal Header -->
            <div class="px-6 py-5 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-black text-slate-900" x-text="getModalTitle()"></h3>
                    <p class="text-xs text-slate-500 mt-0.5" x-text="lang === 'zh' ? '保存后将直接更新至 ERP 数据库。' : 'Data akan langsung tersimpan di Master Data ERP.'"></p>
                </div>
                <button @click="contactModalOpen = false" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-200/60 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Modal Form Body -->
            <form @submit.prevent="submitContactForm()" class="p-6 overflow-y-auto space-y-4 text-xs">
                
                <!-- Nama Customer / Vendor (Required) -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1" x-text="activeTab === 'customer' ? (lang === 'zh' ? '客户名称 / 公司全称 *' : 'Nama Customer / Perusahaan *') : (lang === 'zh' ? '供应商名称 / 车队名称 *' : 'Nama Vendor / Ekspedisi *')"></label>
                    <input type="text" x-model="contactForm.name" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-slate-900 font-semibold focus:border-blue-500 focus:bg-white outline-none">
                </div>

                <!-- KHUSUS CUSTOMER: SALES BY & NO LEADS (Requirement: Sales PIC Attribution & Leads Origin) -->
                <template x-if="activeTab === 'customer'">
                    <div class="space-y-3 p-3.5 bg-blue-50/60 rounded-2xl border border-blue-200/80">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <!-- No. Leads Selection -->
                            <div>
                                <label class="block font-bold text-slate-800 mb-1">
                                    <span x-text="lang === 'zh' ? '来源线索号 (CRM Leads)' : 'No. Leads (Asal CRM)'"></span>
                                    <span class="text-slate-400 font-normal text-[10px]">(Opsional)</span>
                                </label>
                                <select x-model="contactForm.lead_id" @change="onLeadSelected()" class="w-full bg-white border border-blue-300 rounded-xl px-3 py-2 text-slate-900 font-semibold focus:border-blue-500 outline-none">
                                    <option value="" x-text="lang === 'zh' ? '-- 非来自线索 (直接客户) --' : '-- Bukan dari Leads / Walk-in --'">-- Bukan dari Leads / Walk-in --</option>
                                    <template x-for="l in availableLeads" :key="l.id">
                                        <option :value="l.id" x-text="l.code + ' - ' + (l.company_name || 'Leads #' + l.id)"></option>
                                    </template>
                                </select>
                            </div>

                            <!-- Sales By (Sales PIC Attribution) -->
                            <div>
                                <label class="block font-bold text-slate-800 mb-1">
                                    <span x-text="lang === 'zh' ? '销售负责人 (Sales By / PIC) *' : 'Sales By (PIC Penerima Insentif)'"></span>
                                    <span class="text-blue-600 font-bold text-[10px]">★</span>
                                </label>
                                <select x-model="contactForm.sales_id" class="w-full bg-white border border-blue-300 rounded-xl px-3 py-2 text-slate-900 font-semibold focus:border-blue-500 outline-none">
                                    <option value="" x-text="lang === 'zh' ? '-- 选择业务负责人 --' : '-- Pilih Sales PIC (Opsional) --'">-- Pilih Sales PIC (Opsional) --</option>
                                    <template x-for="s in salesUsers" :key="s.id">
                                        <option :value="s.id" x-text="s.name + (s.role ? ' (' + s.role + ')' : '')"></option>
                                    </template>
                                </select>
                            </div>
                        </div>

                        <!-- Microcopy explanation of the continuous incentive attribution -->
                        <div class="flex items-start space-x-1.5 text-[10.5px] text-blue-800 bg-white/80 p-2.5 rounded-xl border border-blue-200/60 leading-relaxed">
                            <svg class="w-4 h-4 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span x-text="lang === 'zh' ? '规则说明：只要该客户有交易或开出报价单，只要此业务员账号处于启用状态，业绩与提成将持续归属于该业务员（即使由管理员制作报价单）。' : 'Ketentuan: Setiap transaksi & penawaran untuk customer ini akan selalu diatribusikan ke Sales PIC terpilih jika akunnya masih aktif, meskipun Admin yang membuat penawarannya.'"></span>
                        </div>
                    </div>
                </template>

                <!-- CATEGORY VENDOR: BISA DITAMBAH & DIHAPUS (Requirement 2.a) -->
                <template x-if="activeTab === 'vendor'">
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="block font-bold text-slate-700" x-text="lang === 'zh' ? '供应商分类 (Kategori) *' : 'Kategori Vendor *'"></label>
                            <button type="button" 
                                    @click="showNewCategoryInput = !showNewCategoryInput" 
                                    class="text-[11px] font-bold text-emerald-600 hover:text-emerald-700 flex items-center space-x-1">
                                <span x-text="showNewCategoryInput ? (lang === 'zh' ? '取消添加' : 'Tutup Input') : (lang === 'zh' ? '+ 新增分类' : 'Tambah Kategori')"></span>
                            </button>
                        </div>

                        <!-- Dropdown Select Category -->
                        <select x-model="contactForm.category" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-slate-900 font-semibold focus:border-blue-500 focus:bg-white outline-none">
                            <template x-for="cat in availableCategories" :key="cat">
                                <option :value="cat" x-text="cat"></option>
                            </template>
                        </select>

                        <!-- Inline Add Category Input -->
                        <div x-show="showNewCategoryInput" class="p-3 bg-emerald-50/60 rounded-2xl border border-emerald-200 space-y-2">
                            <span class="text-[10px] font-bold text-emerald-800 block" x-text="lang === 'zh' ? '创建并添加新分类：' : 'Ketik nama kategori baru:'"></span>
                            <div class="flex items-center space-x-2">
                                <input type="text" 
                                       x-model="newCategoryName" 
                                       placeholder="Misal: Vendor Genset & Lampu" 
                                       class="flex-1 bg-white border border-emerald-300 rounded-xl px-3 py-1.5 text-xs text-slate-900 outline-none focus:border-emerald-500">
                                <button type="button" 
                                        @click="addNewCategory()" 
                                        class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-xs shadow-xs transition">
                                    <span x-text="lang === 'zh' ? '添加' : 'Tambah'">Tambah</span>
                                </button>
                            </div>
                        </div>

                        <!-- Manage/Delete Custom Categories -->
                        <div class="pt-1">
                            <span class="text-[10px] text-slate-400 block mb-1" x-text="lang === 'zh' ? '所有可用类别 (点击 × 可删除自定义类别):' : 'Daftar Kategori Vendor (Klik × untuk menghapus):'"></span>
                            <div class="flex flex-wrap gap-1.5">
                                <template x-for="(cat, cIdx) in availableCategories" :key="cat">
                                    <span class="inline-flex items-center space-x-1 px-2 py-0.5 rounded-lg text-[10px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                        <span x-text="cat"></span>
                                        <!-- Only allow delete if not the active selected and is custom/deletable -->
                                        <button type="button" 
                                                @click="removeCategory(cat)" 
                                                class="text-slate-400 hover:text-rose-600 font-bold ml-1" 
                                                title="Hapus Kategori">
                                            &times;
                                        </button>
                                    </span>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- PIC & Phone: OPSIONAL (Requirement 1.c & 2.b) -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">
                            <span x-text="lang === 'zh' ? '负责人姓名 (PIC - 可选)' : 'Nama PIC (Opsional)'"></span>
                        </label>
                        <input type="text" x-model="contactForm.pic_name" placeholder="Opsional" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-slate-900 font-medium focus:border-blue-500 focus:bg-white outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">
                            <span x-text="lang === 'zh' ? '联系电话 (可选)' : 'No. Telepon / WA (Opsional)'"></span>
                        </label>
                        <input type="text" x-model="contactForm.phone" placeholder="Opsional" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-slate-900 font-medium focus:border-blue-500 focus:bg-white outline-none">
                    </div>
                </div>

                <!-- Email: OPSIONAL (Requirement 2.b) -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '电子邮箱 (可选)' : 'Email (Opsional)'"></label>
                    <input type="email" x-model="contactForm.email" placeholder="contoh@perusahaan.com" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-slate-900 font-medium focus:border-blue-500 focus:bg-white outline-none">
                </div>

                <!-- Alamat (Opsional) -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '地址 / 办公地点 (可选)' : 'Alamat Lengkap (Opsional)'"></label>
                    <textarea x-model="contactForm.billing_address" rows="2" placeholder="Alamat kantor, pool, atau workshop..." class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-slate-900 font-medium focus:border-blue-500 focus:bg-white outline-none"></textarea>
                </div>

                <!-- Catatan: OPSIONAL (Requirement 2.b) -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '备注 / 特殊说明 (可选)' : 'Catatan / Remaks (Opsional)'"></label>
                    <input type="text" x-model="contactForm.notes" placeholder="Catatan armada, jenis truk pengiriman, dsb..." class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-slate-900 font-medium focus:border-blue-500 focus:bg-white outline-none">
                </div>

                <!-- Modal Actions -->
                <div class="pt-3 border-t border-slate-200 flex items-center justify-end space-x-2">
                    <button type="button" @click="contactModalOpen = false" class="px-4 py-2 rounded-xl text-slate-600 hover:text-slate-800 font-semibold" x-text="lang === 'zh' ? '取消' : 'Batal'">Batal</button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-extrabold px-5 py-2 rounded-xl shadow-xs transition" x-text="modalMode === 'edit' ? (lang === 'zh' ? '更新保存' : 'Perbarui Data') : (lang === 'zh' ? '保存数据' : 'Simpan Data')">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
    function erpMasterDataManager(initialTab, initialCategories, rawCustomers, rawVendors, initialSalesUsers, initialLeads) {
        return {
            activeTab: initialTab || 'customer',
            searchQuery: '',
            vendorCategoryFilter: 'all',
            contactModalOpen: false,
            modalMode: 'create', // 'create' or 'edit'
            showNewCategoryInput: false,
            newCategoryName: '',

            // Reactive lists
            customersList: rawCustomers || [],
            vendorsList: rawVendors || [],
            salesUsers: initialSalesUsers || [],
            availableLeads: initialLeads || [],
            availableCategories: initialCategories || [
                'Vendor Pengiriman',
                'Vendor Unit & Alat Berat',
                'Vendor Sparepart & Ban',
                'Vendor Bahan Bakar & Pelumas',
                'Vendor Service & Karoseri',
                'Vendor Umum'
            ],

            // Form Model
            contactForm: {
                id: null,
                type: 'customer',
                sales_id: '',
                lead_id: '',
                name: '',
                category: 'Customer',
                pic_name: '',
                phone: '',
                email: '',
                billing_address: '',
                notes: ''
            },

            switchTab(tab) {
                this.activeTab = tab;
                this.searchQuery = '';
                const url = new URL(window.location);
                url.searchParams.set('tab', tab);
                window.history.replaceState({}, '', url);
            },

            resetFilters() {
                this.searchQuery = '';
                this.vendorCategoryFilter = 'all';
            },

            get filteredCustomers() {
                const q = this.searchQuery.toLowerCase();
                return this.customersList.filter(c => {
                    if (!q) return true;
                    const salesName = (c.sales ? c.sales.name : (c.sales_name || '')).toLowerCase();
                    const leadCode = (c.lead_code || (c.lead ? c.lead.code : '')).toLowerCase();
                    const haystack = `${c.code || ''} ${c.name || ''} ${c.pic_name || ''} ${c.phone || ''} ${c.email || ''} ${salesName} ${leadCode}`.toLowerCase();
                    return haystack.includes(q);
                });
            },

            get filteredVendors() {
                const q = this.searchQuery.toLowerCase();
                const cat = this.vendorCategoryFilter;
                return this.vendorsList.filter(v => {
                    const matchesSearch = !q || `${v.code || ''} ${v.name || ''} ${v.pic_name || ''} ${v.phone || ''} ${v.email || ''} ${v.category || ''} ${v.notes || ''}`.toLowerCase().includes(q);
                    const matchesCategory = (cat === 'all') || (v.category === cat);
                    return matchesSearch && matchesCategory;
                });
            },

            getCreateButtonText() {
                if (this.lang === 'zh') {
                    return this.activeTab === 'customer' ? '+ 新增客户' : '+ 新增供应商';
                }
                return this.activeTab === 'customer' ? 'Tambah Customer' : 'Tambah Vendor';
            },

            getSearchPlaceholder() {
                if (this.lang === 'zh') {
                    return this.activeTab === 'customer' ? '搜索客户名称、编号、联系人、Sales PIC、线索号...' : '搜索供应商名称、分类、司机、联系电话...';
                }
                return this.activeTab === 'customer' ? 'Cari nama customer, kode, PIC, Sales By, No Leads...' : 'Cari nama vendor, kategori, driver, catatan...';
            },

            getModalTitle() {
                if (this.modalMode === 'edit') {
                    if (this.lang === 'zh') {
                        return this.activeTab === 'customer' ? '编辑客户档案' : '编辑供应商档案';
                    }
                    return this.activeTab === 'customer' ? 'Edit Data Customer' : 'Edit Data Vendor';
                } else {
                    if (this.lang === 'zh') {
                        return this.activeTab === 'customer' ? '新增客户档案' : '新增供应商档案 (含承运物流)';
                    }
                    return this.activeTab === 'customer' ? 'Tambah Data Customer Baru' : 'Tambah Data Vendor Baru (Termasuk Pengiriman)';
                }
            },

            openCreateModal() {
                this.modalMode = 'create';
                this.contactForm = {
                    id: null,
                    type: this.activeTab,
                    sales_id: '',
                    lead_id: '',
                    name: '',
                    category: this.activeTab === 'customer' ? 'Customer' : (this.availableCategories[0] || 'Vendor Pengiriman'),
                    pic_name: '',
                    phone: '',
                    email: '',
                    billing_address: '',
                    notes: ''
                };
                this.showNewCategoryInput = false;
                this.newCategoryName = '';
                this.contactModalOpen = true;
            },

            openEditModal(contact) {
                this.modalMode = 'edit';
                this.contactForm = {
                    id: contact.id,
                    type: contact.type,
                    sales_id: contact.sales_id || '',
                    lead_id: contact.lead_id || '',
                    name: contact.name,
                    category: contact.category || (contact.type === 'customer' ? 'Customer' : 'Vendor Pengiriman'),
                    pic_name: contact.pic_name || '',
                    phone: contact.phone || '',
                    email: contact.email || '',
                    billing_address: contact.billing_address || '',
                    notes: contact.notes || ''
                };
                // Ensure category exists in list
                if (this.contactForm.category && !this.availableCategories.includes(this.contactForm.category)) {
                    this.availableCategories.push(this.contactForm.category);
                }
                this.showNewCategoryInput = false;
                this.contactModalOpen = true;
            },

            onLeadSelected() {
                if (!this.contactForm.lead_id) return;
                const selectedLead = this.availableLeads.find(l => l.id == this.contactForm.lead_id);
                if (!selectedLead) return;

                if (!this.contactForm.name && selectedLead.company_name) {
                    this.contactForm.name = selectedLead.company_name;
                }
                if (!this.contactForm.pic_name && selectedLead.pic_name) {
                    this.contactForm.pic_name = selectedLead.pic_name;
                }
                if (!this.contactForm.phone && selectedLead.pic_phone) {
                    this.contactForm.phone = selectedLead.pic_phone;
                }
                if (!this.contactForm.email && selectedLead.pic_email) {
                    this.contactForm.email = selectedLead.pic_email;
                }
                if (!this.contactForm.billing_address && selectedLead.address) {
                    this.contactForm.billing_address = selectedLead.address;
                }
                if (selectedLead.sales_id) {
                    this.contactForm.sales_id = selectedLead.sales_id;
                }
            },

            addNewCategory() {
                const name = this.newCategoryName.trim();
                if (!name) return;
                if (!this.availableCategories.includes(name)) {
                    this.availableCategories.push(name);
                }
                this.contactForm.category = name;
                this.newCategoryName = '';
                this.showNewCategoryInput = false;
            },

            removeCategory(name) {
                if (this.availableCategories.length <= 1) {
                    alert('Minimal harus ada 1 kategori vendor.');
                    return;
                }
                if (confirm(`Apakah Anda yakin ingin menghapus kategori '${name}' dari daftar?`)) {
                    this.availableCategories = this.availableCategories.filter(c => c !== name);
                    if (this.contactForm.category === name) {
                        this.contactForm.category = this.availableCategories[0] || '';
                    }
                }
            },

            async toggleStatus(contact) {
                const actionLabel = contact.is_active ? 'menonaktifkan' : 'mengaktifkan';
                if (!confirm(`Apakah Anda yakin ingin ${actionLabel} data ${contact.name}?`)) {
                    return;
                }

                try {
                    const res = await fetch(`/erp/master-data/contact/${contact.id}/toggle-status`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                    const result = await res.json();
                    if (result.success) {
                        contact.is_active = result.is_active;
                    } else {
                        alert(result.message || 'Gagal mengubah status.');
                    }
                } catch(e) {
                    alert('Terjadi kesalahan pada server saat memperbarui status.');
                }
            },

            async submitContactForm() {
                try {
                    const url = this.modalMode === 'edit'
                        ? `/erp/master-data/contact/${this.contactForm.id}/update`
                        : '{{ route("erp.master_data.contact.store") }}';

                    const res = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(this.contactForm)
                    });
                    const result = await res.json();
                    if (result.success) {
                        alert(result.message);
                        window.location.reload();
                    } else {
                        alert('Gagal menyimpan: ' + (result.message || 'Periksa kembali input Anda'));
                    }
                } catch(e) {
                    alert('Terjadi kesalahan pada server.');
                }
            }
        };
    }
</script>
@endpush
@endsection
