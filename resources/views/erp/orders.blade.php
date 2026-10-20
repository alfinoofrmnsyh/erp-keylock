@extends('layouts.erp')

@section('title', 'Penawaran & Order - Keylock Indonesia')

@section('breadcrumbs')
    <a href="{{ route('erp.index') }}" class="hover:text-slate-800">Home</a>
    <span>›</span>
    <span class="text-slate-800 font-semibold" x-text="lang === 'zh' ? '报价与订单' : 'Penawaran & Order'">Penawaran & Order</span>
@endsection

@section('content')
<div x-data="erpOrdersManager()">

    <!-- ========================================================================= -->
    <!-- MOBILE HEADER & TOOLBAR (PHASE 4D.1B - MODERN ERP STYLE)                  -->
    <!-- ========================================================================= -->
    <div class="block md:hidden mb-4 space-y-3">
        <!-- 1. Header: Back button + Title & Subtitle -->
        <div class="flex items-center space-x-3">
            <a href="{{ route('erp.index') }}" 
               class="w-10 h-10 rounded-full border border-slate-200 bg-white flex items-center justify-center text-slate-700 hover:bg-slate-50 active:scale-95 transition shadow-2xs shrink-0"
               aria-label="Kembali ke Dashboard">
                <svg class="w-5 h-5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div class="min-w-0 flex-1">
                <h1 class="text-lg font-black text-[#17243B] tracking-tight leading-tight" x-text="lang === 'zh' ? '报价与订单' : 'Penawaran & Order'">
                    Penawaran & Order
                </h1>
                <p class="text-xs text-slate-500 truncate" x-text="lang === 'zh' ? '管理客户报价单、销售订单和租赁订单' : 'Kelola quotation, sales order, dan rental order pelanggan'">
                    Kelola quotation, sales order, dan rental order pelanggan
                </p>
            </div>
        </div>

        <!-- 2. Search Field + Filter Button -->
        <div class="flex items-center space-x-2">
            <div class="relative flex-1">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input type="text" 
                       x-model="searchQuery" 
                       @input="onSearchInput()"
                       @keydown.enter.prevent="submitSearch()"
                       :placeholder="lang === 'zh' ? '搜索单据号、客户名称...' : 'Cari nomor dokumen, nama pelanggan...'"
                       placeholder="Cari nomor dokumen, nama pelanggan..." 
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
             class="bg-white p-3 rounded-2xl border border-slate-200 shadow-xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-700">Filter Status:</span>
                <button type="button" @click="clearSearch(); mobileFilterOpen = false" class="text-[11px] text-blue-600 font-semibold hover:underline">
                    Reset Filter
                </button>
            </div>
            <select x-model="statusFilter" @change="submitSearch()" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold text-slate-700 outline-none focus:border-blue-500">
                <option value="all" x-text="lang === 'zh' ? '所有状态' : 'Semua Status'">Semua Status</option>
                @if($typeFilter === 'quotation')
                    <option value="aktif">Aktif (Masa Berlaku Valid)</option>
                    <option value="tidak_aktif">Tidak Aktif (Masa Berlaku Habis)</option>
                    <option value="transaksi">Transaksi (Sudah Jadi Transaksi)</option>
                    <option value="cancel">Cancel (Dibatalkan)</option>
                @else
                    <option value="in_progress" x-text="lang === 'zh' ? '进行中 / 处理中' : 'Aktif / Proses'">Aktif / Proses</option>
                    <option value="completed" x-text="lang === 'zh' ? '已完成' : 'Selesai'">Selesai</option>
                    <option value="cancelled" x-text="lang === 'zh' ? '已取消' : 'Dibatalkan'">Dibatalkan</option>
                @endif
            </select>
        </div>

        <!-- 3. Dua Tab Transaksi Mobile: Penawaran Harga & Transaksi (Maks 10 Data Awal) -->
        <div class="grid grid-cols-2 gap-2 py-0.5" id="mobile-order-tabs">
            <!-- Tab 1: Penawaran Harga (Quotation) -->
            <a href="{{ route('erp.orders.index', array_merge(request()->except(['page']), ['type' => 'quotation', 'per_page' => 10])) }}" 
               class="py-2.5 px-3 rounded-xl text-xs text-center transition flex items-center justify-center space-x-1.5 {{ $typeFilter === 'quotation' ? 'bg-[#142A57] text-white font-bold shadow-xs' : 'bg-white text-slate-700 hover:text-slate-900 border border-[#E5EAF2] font-semibold shadow-2xs' }}"
               aria-label="Tab Penawaran Harga">
                <svg class="w-3.5 h-3.5 {{ $typeFilter === 'quotation' ? 'text-amber-300' : 'text-amber-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span x-text="lang === 'zh' ? '价格报价单' : 'Penawaran Harga'">Penawaran Harga</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] font-black {{ $typeFilter === 'quotation' ? 'bg-white/20 text-white' : 'bg-amber-100 text-amber-800' }}">
                    {{ $quotationCount }}
                </span>
            </a>

            <!-- Tab 2: Transaksi (Rental & Sales Orders) -->
            <a href="{{ route('erp.orders.index', array_merge(request()->except(['page']), ['type' => 'transaksi', 'per_page' => 10])) }}" 
               class="py-2.5 px-3 rounded-xl text-xs text-center transition flex items-center justify-center space-x-1.5 {{ in_array($typeFilter, ['transaksi', 'order', 'rental_order', 'sales_order']) ? 'bg-[#142A57] text-white font-bold shadow-xs' : 'bg-white text-slate-700 hover:text-slate-900 border border-[#E5EAF2] font-semibold shadow-2xs' }}"
               aria-label="Tab Transaksi">
                <svg class="w-3.5 h-3.5 {{ in_array($typeFilter, ['transaksi', 'order', 'rental_order', 'sales_order']) ? 'text-blue-300' : 'text-blue-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                </svg>
                <span x-text="lang === 'zh' ? '交易订单' : 'Transaksi'">Transaksi</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] font-black {{ in_array($typeFilter, ['transaksi', 'order', 'rental_order', 'sales_order']) ? 'bg-white/20 text-white' : 'bg-blue-100 text-blue-800' }}">
                    {{ $transactionCount }}
                </span>
            </a>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- DESKTOP HEADER & CONTROLS (VISIBLE ON SCREENS >= MD)                      -->
    <!-- ========================================================================= -->
    <div class="hidden md:block">
        <!-- 1. PAGE HEADER (MATCHING IMAGE 5: MANAJEMEN SISWA STYLE) -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight" x-text="lang === 'zh' ? '报价与订单' : 'Penawaran & Order'">
                    Penawaran & Order
                </h2>
                <p class="text-xs text-slate-500 mt-1" x-text="lang === 'zh' ? '集中管理主交易、设备租赁/销售报价单及客户合同状态。' : 'Kelola data transaksi induk, penawaran harga, dan order sewa/jual.'">
                    Kelola data transaksi induk, penawaran harga, dan order sewa/jual.
                </p>
            </div>

            <!-- Top Right Action Button (Tambah Penawaran Baru - Image 5 Pill Style, No Duplicate +) -->
            <button type="button" 
                    @click="modalQuotationOpen = true" 
                    class="bg-blue-600 hover:bg-blue-700 text-white font-extrabold px-5 py-2.5 rounded-full shadow-md hover:shadow-lg text-xs transition flex items-center space-x-2 self-start md:self-auto active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                <span x-text="lang === 'zh' ? '创建新报价单' : 'Tambah Penawaran Baru'">Tambah Penawaran Baru</span>
            </button>
        </div>

        <!-- 2. FILTER PILLS BAR (PENAWARAN HARGA & TRANSAKSI) -->
        <div class="flex flex-wrap items-center gap-3 mb-5">
            <!-- Penawaran Harga Pill -->
            <a href="{{ route('erp.orders.index', ['type' => 'quotation']) }}" 
               class="px-4 py-2 rounded-2xl text-xs font-bold transition flex items-center space-x-2.5 {{ $typeFilter === 'quotation' ? 'bg-white text-slate-900 shadow-sm border border-slate-200/90' : 'bg-slate-100/80 text-slate-600 hover:bg-white hover:text-slate-900 border border-transparent' }}">
                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span x-text="lang === 'zh' ? '价格报价单' : 'Penawaran Harga'">Penawaran Harga</span>
                <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[10px] font-black">
                    {{ $quotationCount }}
                </span>
            </a>

            <!-- Transaksi Pill (Gabungan Rental Order & Sales Order) -->
            <a href="{{ route('erp.orders.index', ['type' => 'transaksi']) }}" 
               class="px-4 py-2 rounded-2xl text-xs font-bold transition flex items-center space-x-2.5 {{ in_array($typeFilter, ['transaksi', 'order', 'rental_order', 'sales_order']) ? 'bg-white text-slate-900 shadow-sm border border-slate-200/90' : 'bg-slate-100/80 text-slate-600 hover:bg-white hover:text-slate-900 border border-transparent' }}">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                <span x-text="lang === 'zh' ? '交易订单' : 'Transaksi'">Transaksi</span>
                <span class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 text-[10px] font-black">
                    {{ $transactionCount ?? ($rentalCount + $salesCount) }}
                </span>
            </a>
        </div>

        <!-- 3. SEARCH & CONTROLS BAR (MATCHING IMAGE 5 PILL SEARCH WITH DROPDOWN) -->
        <div class="bg-white rounded-3xl p-3 border border-slate-200/80 shadow-xs mb-6 flex flex-col md:flex-row items-center justify-between gap-3">
            <!-- Search Input with Magnifier -->
            <div class="relative w-full md:w-96 flex items-center">
                <svg class="w-4 h-4 text-slate-400 absolute left-4 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input type="text" 
                       x-model="searchQuery" 
                       @input="onSearchInput()"
                       @keydown.enter.prevent="submitSearch()"
                       :placeholder="lang === 'zh' ? '搜索单据、客户、司机...' : 'Cari dokumen, DO, pelanggan, PIC...'"
                       placeholder="Cari dokumen, DO, pelanggan, PIC..." 
                       class="w-full bg-transparent pl-11 pr-4 py-2 text-xs text-slate-900 placeholder:text-slate-400 focus:outline-none">
            </div>

            <!-- Filter Dropdown & Refresh -->
            <div class="flex items-center space-x-2.5 self-end md:self-auto w-full md:w-auto justify-end">
                <select x-model="statusFilter" @change="submitSearch()" class="bg-slate-50 border border-slate-200 rounded-2xl px-4 py-2 text-xs font-semibold text-slate-700 outline-none focus:border-blue-400">
                    <option value="all" x-text="lang === 'zh' ? '所有状态' : 'Semua Status'">Semua Status</option>
                    @if($typeFilter === 'quotation')
                        <option value="aktif">Aktif (Masa Berlaku Valid)</option>
                        <option value="tidak_aktif">Tidak Aktif (Masa Berlaku Habis)</option>
                        <option value="transaksi">Transaksi (Sudah Jadi Transaksi)</option>
                        <option value="cancel">Cancel (Dibatalkan)</option>
                    @else
                        <option value="in_progress" x-text="lang === 'zh' ? '进行中 / 处理中' : 'Aktif / Proses'">Aktif / Proses</option>
                        <option value="completed" x-text="lang === 'zh' ? '已完成' : 'Selesai'">Selesai</option>
                        <option value="cancelled" x-text="lang === 'zh' ? '已取消' : 'Dibatalkan'">Dibatalkan</option>
                    @endif
                </select>

                <button type="button" @click="clearSearch()" class="w-9 h-9 rounded-2xl bg-slate-50 hover:bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-500 hover:text-slate-800 transition shadow-2xs" title="Reset / Refresh Data">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                </button>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 4. DATA TABLE CARD WITH SEPARATED DO COLUMNS (REVISION 1)                 -->
    <!-- ========================================================================= -->
    <!-- ========================================================================= -->
    <!-- 4. DATA TABLE & MOBILE CARDS (TRANSAKSI, PENAWARAN, RO, SO)               -->
    <!-- ========================================================================= -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <!-- DESKTOP TABLE VIEW (VISIBLE ON SCREENS >= MD, HIDDEN ON MOBILE) -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left text-xs whitespace-nowrap">
                    <thead>
                        <tr class="border-b border-slate-100 text-[11px] font-black uppercase tracking-wider text-slate-400 bg-slate-50/50">
                            <th class="py-4 px-2 w-8 text-center"></th>
                            <th class="py-4 px-3 w-12 text-center" x-text="lang === 'zh' ? '序号' : 'SL'">SL</th>
                            <!-- 1. NO. PENAWARAN (Kolom Dokumen Utama Pertama) -->
                            <th class="py-4 px-3 w-44 font-black text-amber-700 bg-amber-50/40" x-text="lang === 'zh' ? '报价单号' : 'No. Penawaran'">No. Penawaran</th>
                            <!-- 2. NO. TRANSAKSI (Menggantikan No. Dokumen) -->
                            <th class="py-4 px-4 w-44 font-black text-slate-700" x-text="lang === 'zh' ? '交易单号' : 'No. Transaksi'">No. Transaksi</th>
                            <!-- 3. JENIS TRANSAKSI DI HEADER -->
                            <th class="py-4 px-3 w-32 text-center font-black text-slate-700" x-text="lang === 'zh' ? '交易种类' : 'Jenis Transaksi'">Jenis Transaksi</th>
                            <th class="py-4 px-4 min-w-[200px]" x-text="lang === 'zh' ? '客户' : 'Pelanggan'">Pelanggan</th>
                            @if($typeFilter === 'quotation')
                                <th class="py-4 px-3 w-32 text-amber-800 bg-amber-50/40 font-black" x-text="lang === 'zh' ? '有效期' : 'Masa Berlaku'">Masa Berlaku</th>
                            @endif
                            <!-- REVISION: SEPARATED DO COLUMNS (HIDDEN ON QUOTATION TAB) -->
                            @if($typeFilter !== 'quotation')
                                <th class="py-4 px-3 w-36 text-emerald-700 bg-emerald-50/40 font-black" x-text="lang === 'zh' ? '送货单号' : 'No. DO'">No. DO</th>
                                <th class="py-4 px-3 w-28 text-emerald-700 bg-emerald-50/40" x-text="lang === 'zh' ? '发货日期' : 'Tgl Kirim'">Tgl Kirim</th>
                                <th class="py-4 px-3 w-32 text-emerald-700 bg-emerald-50/40" x-text="lang === 'zh' ? '收货人' : 'Nama PIC'">Nama PIC</th>
                                <th class="py-4 px-3 w-32 text-emerald-700 bg-emerald-50/40" x-text="lang === 'zh' ? '联系电话' : 'No. Telp PIC'">No. Telp PIC</th>
                                <th class="py-4 px-3 w-28 text-emerald-700 bg-emerald-50/40" x-text="lang === 'zh' ? '承运司机' : 'Driver'">Driver</th>
                                <th class="py-4 px-3 w-28 text-emerald-700 bg-emerald-50/40" x-text="lang === 'zh' ? '车牌号' : 'No. Plat'">No. Plat</th>
                                <th class="py-4 px-4 w-36 text-emerald-700 bg-emerald-50/40" x-text="lang === 'zh' ? '备注' : 'Remaks'">Remaks</th>
                                <th class="py-4 px-3 w-36 text-purple-700 bg-purple-50/40 font-black" x-text="lang === 'zh' ? '采购单号 (PO)' : 'No. PO Vendor'">No. PO Vendor</th>
                            @endif
                            <!-- 5. NO. INVOICE DI HEADER -->
                            <th class="py-4 px-4 w-40 font-black text-indigo-700 bg-indigo-50/40" x-text="lang === 'zh' ? '发票单号' : 'No. Invoice'">No. Invoice</th>
                            <!-- FINANCIAL & STATUS -->
                            <th class="py-4 px-4 w-36 text-right" x-text="lang === 'zh' ? '总金额' : 'Nilai Total'">Nilai Total</th>
                            <th class="py-4 px-4 w-28 text-center" x-text="lang === 'zh' ? '状态' : 'Status'">Status</th>
                            <!-- REVISION: FREEZE KOLOM AKSI (STICKY RIGHT) -->
                            <th class="py-4 px-4 w-28 text-center sticky right-0 bg-slate-50 z-20 shadow-[-6px_0_8px_-4px_rgba(0,0,0,0.06)] border-l border-slate-200" x-text="lang === 'zh' ? '操作' : 'Aksi'">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($orders as $index => $order)
                            @php
                                $hasDeliveries = $order->deliveries && $order->deliveries->isNotEmpty();
                                $firstDelivery = $hasDeliveries ? $order->deliveries->first() : null;
                                
                                // Lookup Customer Invoice
                                $orderInvoice = $order->invoice ?? $order->invoices->where('invoice_type', 'customer_invoice')->first();
                                if (!$orderInvoice && $order->children && $order->children->isNotEmpty()) {
                                    foreach ($order->children as $child) {
                                        $childInv = $child->invoices->where('invoice_type', 'customer_invoice')->first();
                                        if ($childInv) {
                                            $orderInvoice = $childInv;
                                            break;
                                        }
                                    }
                                }
                                
                                // Quotation status calculation (Aktif, Tidak Aktif, Transaksi, Cancel)
                                $isCancelled = ($order->status === 'cancelled');
                                $isConverted = ($order->children && $order->children->whereIn('order_type', ['rental_order', 'sales_order'])->isNotEmpty()) || $order->status === 'approved';
                                $isExpired = $order->valid_until && \Carbon\Carbon::parse($order->valid_until)->startOfDay()->lt(\Carbon\Carbon::today());

                                if ($isCancelled) {
                                    $computedQuotationStatus = 'cancel';
                                } elseif ($isConverted) {
                                    $computedQuotationStatus = 'transaksi';
                                } elseif ($isExpired) {
                                    $computedQuotationStatus = 'tidak_aktif';
                                } else {
                                    $computedQuotationStatus = 'aktif';
                                }
                                
                                $searchHaystack = strtolower(implode(' ', [
                                    $order->order_number,
                                    $order->customer_po_number ?? '',
                                    $order->parent?->order_number ?? '',
                                    $orderInvoice?->invoice_number ?? '',
                                    $order->contact?->name ?? '',
                                    $order->contact?->pic_name ?? '',
                                    $firstDelivery?->delivery_number ?? '',
                                    $firstDelivery?->driver_name ?? '',
                                    $firstDelivery?->vehicle_number ?? '',
                                    $firstDelivery?->recipient_name ?? '',
                                    $firstDelivery?->notes ?? ''
                                ]));
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition group"
                                x-show="matchesRow('{{ addslashes($searchHaystack) }}', '{{ $order->status }}', '{{ $order->order_type === 'quotation' ? $computedQuotationStatus : '' }}')">
                                
                                <!-- 0. Toggle Expand Button -->
                                <td class="py-4 px-2 text-center">
                                    <button type="button" 
                                            @click="toggleExpand({{ $order->id }})" 
                                            class="w-7 h-7 rounded-xl flex items-center justify-center transition border"
                                            :class="isExpanded({{ $order->id }}) ? 'bg-blue-50 border-blue-300 text-blue-600 rotate-90 shadow-xs' : 'bg-slate-50 hover:bg-slate-100 border-slate-200 text-slate-400 hover:text-slate-700'"
                                            :title="isExpanded({{ $order->id }}) ? (lang === 'zh' ? '收起商品明细' : 'Tutup Rincian Produk') : (lang === 'zh' ? '展开商品明细' : 'Lihat Rincian Produk')">
                                        <svg class="w-3.5 h-3.5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                                    </button>
                                </td>

                                <!-- 1. SL -->
                                <td class="py-4 px-3 text-center font-bold text-slate-400">
                                    {{ str_pad(($orders->firstItem() ?? 1) + $index, 2, '0', STR_PAD_LEFT) }}
                                </td>

                                <!-- 2. No. Penawaran (Awal Dokumen - Clickable to Tab Penawaran with Print Button) -->
                                <td class="py-4 px-3 bg-amber-50/20 font-mono text-xs">
                                    @if($order->order_type === 'quotation')
                                        <div class="inline-flex items-center space-x-1.5 font-bold text-amber-900 bg-amber-100/80 hover:bg-amber-100 px-2 py-0.5 rounded-lg border border-amber-300 transition shadow-2xs select-text">
                                            <a href="{{ route('erp.orders.index', ['type' => 'quotation', 'search' => $order->order_number]) }}" 
                                               class="hover:underline font-bold text-amber-950 cursor-pointer" 
                                               title="Tampilkan penawaran ini di Tab Penawaran">
                                                {{ $order->order_number }}
                                            </a>
                                            <a href="{{ route('erp.quotations.pdf', $order->id) }}" 
                                               target="_blank"
                                               class="text-amber-700 hover:text-amber-950 p-1 rounded hover:bg-amber-200/70 transition shrink-0"
                                               :title="lang === 'zh' ? '打印报价单 PDF ({{ $order->order_number }})' : 'Cetak PDF Penawaran ({{ $order->order_number }})'">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                            </a>
                                        </div>
                                    @elseif($order->parent)
                                        <div class="inline-flex items-center space-x-1.5 font-bold text-amber-900 bg-amber-100/80 hover:bg-amber-100 px-2 py-0.5 rounded-lg border border-amber-300 transition shadow-2xs select-text">
                                            <a href="{{ route('erp.orders.index', ['type' => 'quotation', 'search' => $order->parent->order_number]) }}" 
                                               class="hover:underline font-bold text-amber-950 cursor-pointer" 
                                               title="Buka Tab Penawaran dan tampilkan penawaran {{ $order->parent->order_number }}">
                                                {{ $order->parent->order_number }}
                                            </a>
                                            <a href="{{ route('erp.quotations.pdf', $order->parent->id) }}" 
                                               target="_blank"
                                               class="text-amber-700 hover:text-amber-950 p-1 rounded hover:bg-amber-200/70 transition shrink-0"
                                               :title="lang === 'zh' ? '打印源报价单 PDF ({{ $order->parent->order_number }})' : 'Cetak PDF Penawaran Harga ({{ $order->parent->order_number }})'">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                            </a>
                                        </div>
                                    @else
                                        <span class="text-slate-300 font-bold text-xs">—</span>
                                    @endif
                                </td>

                                <!-- 3. No. Transaksi (Menggantikan No. Dokumen - Clickable to Tab Transaksi) -->
                                <td class="py-4 px-4 font-mono">
                                    @if($order->order_type !== 'quotation')
                                        <a href="{{ route('erp.orders.index', ['type' => 'transaksi', 'search' => $order->order_number]) }}" 
                                           class="font-black text-slate-900 hover:text-blue-600 hover:underline transition text-xs block" 
                                           title="Filter transaksi ini">
                                            {{ $order->order_number }}
                                        </a>
                                    @elseif($order->children && $order->children->isNotEmpty())
                                        <div class="flex flex-col gap-1">
                                            @foreach($order->children as $child)
                                                <a href="{{ route('erp.orders.index', ['type' => 'transaksi', 'search' => $child->order_number]) }}" 
                                                   class="font-black text-blue-700 hover:text-blue-900 hover:underline text-xs inline-flex items-center space-x-1" 
                                                   title="Buka transaksi ini di Tab Transaksi">
                                                    <span>{{ $child->order_number }}</span>
                                                    <svg class="w-3 h-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                                </a>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-slate-400 font-medium text-[11px] italic" x-text="lang === 'zh' ? '（未生成交易）' : '(Belum Transaksi)'">(Belum Transaksi)</span>
                                    @endif
                                    <div class="flex items-center space-x-2 text-[10px] mt-0.5">
                                        <button type="button" 
                                                @click="toggleExpand({{ $order->id }})" 
                                                class="inline-flex items-center space-x-1 font-bold text-blue-600 hover:text-blue-800 transition bg-blue-50/80 hover:bg-blue-100 px-1.5 py-0.5 rounded-md border border-blue-200/60"
                                                title="Klik untuk buka / tutup rincian produk">
                                            <span>{{ $order->items->count() }} Produk</span>
                                            <svg class="w-2.5 h-2.5 transition-transform duration-200" :class="isExpanded({{ $order->id }}) ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                                        </button>
                                    </div>
                                </td>

                                <!-- 4. Jenis Transaksi di Header Table -->
                                <td class="py-4 px-3 text-center">
                                    @if($order->order_type === 'rental_order')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200 shadow-2xs">
                                            <span x-text="lang === 'zh' ? '租赁订单' : 'Sewa / Rental'">Sewa / Rental</span>
                                        </span>
                                    @elseif($order->order_type === 'sales_order')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-2xs">
                                            <span x-text="lang === 'zh' ? '销售订单' : 'Pembelian'">Pembelian</span>
                                        </span>
                                    @elseif($order->order_type === 'quotation')
                                        @if($order->quotation_type === 'sales')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-2xs">
                                                <span x-text="lang === 'zh' ? '报价-购买' : 'Pembelian'">Pembelian</span>
                                            </span>
                                        @elseif($order->quotation_type === 'both')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200 shadow-2xs">
                                                <span x-text="lang === 'zh' ? '综合 (租售)' : 'Keduanya (Sewa & Jual)'">Keduanya (Sewa & Jual)</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200 shadow-2xs">
                                                <span x-text="lang === 'zh' ? '报价-租赁' : 'Sewa / Rental'">Sewa / Rental</span>
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-slate-400 text-xs">—</span>
                                    @endif
                                </td>

                                <!-- 5. Pelanggan -->
                                <td class="py-4 px-4">
                                    <div class="font-bold text-slate-800 text-xs truncate max-w-[170px]" title="{{ $order->contact->name ?? '-' }}">
                                        {{ $order->contact->name ?? '-' }}
                                    </div>
                                    @if(!empty($order->customer_po_number))
                                        <div class="inline-flex items-center space-x-1 mt-0.5 px-2 py-0.5 rounded bg-blue-50 text-blue-700 border border-blue-200 text-[10px] font-mono font-bold" title="Nomor PO Customer">
                                            <span>PO: {{ $order->customer_po_number }}</span>
                                        </div>
                                    @endif
                                    <div class="text-[10px] text-slate-400">
                                        <span x-text="lang === 'zh' ? '日期: ' : 'Tgl: '">Tgl: </span>{{ $order->order_date ? $order->order_date->format('d/m/Y') : '-' }}
                                    </div>
                                </td>

                                <!-- 5. MASA BERLAKU (HANYA TAB PENAWARAN HARGA) -->
                                @if($typeFilter === 'quotation')
                                    <td class="py-4 px-3 bg-amber-50/10 text-xs font-mono">
                                        @if($order->valid_until)
                                            <div class="font-bold {{ $isExpired ? 'text-rose-600' : 'text-slate-800' }}">
                                                {{ \Carbon\Carbon::parse($order->valid_until)->format('d/m/Y') }}
                                            </div>
                                            <div class="text-[10px] {{ $isExpired ? 'text-rose-500 font-bold' : 'text-slate-400' }}">
                                                @if($isExpired)
                                                    <span class="inline-flex items-center space-x-1">
                                                        <svg class="w-2.5 h-2.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                        <span>Kedaluwarsa</span>
                                                    </span>
                                                @else
                                                    @php
                                                        $daysLeft = \Carbon\Carbon::today()->diffInDays(\Carbon\Carbon::parse($order->valid_until), false);
                                                    @endphp
                                                    <span>Sisa {{ max(0, $daysLeft) }} hari</span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-slate-300 font-bold text-xs">—</span>
                                        @endif
                                    </td>
                                @endif

                                <!-- 6 to 12: DO & LOGISTICS COLUMNS (HIDDEN ON QUOTATION TAB) -->
                                @if($typeFilter !== 'quotation')
                                    <!-- 5. NO. DO -->
                                    <td class="py-4 px-3 bg-emerald-50/20">
                                        @if($hasDeliveries)
                                            <a href="{{ route('erp.deliveries.pdf', $firstDelivery->id) }}" 
                                               target="_blank" 
                                               class="px-2.5 py-1 rounded-lg bg-emerald-100 hover:bg-emerald-200 text-emerald-800 font-mono font-black text-xs transition inline-flex items-center space-x-1"
                                               title="Klik untuk cetak Surat Jalan resmi">
                                                <svg class="w-3 h-3 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                <span>{{ $firstDelivery->delivery_number }}</span>
                                            </a>
                                        @else
                                            <span class="text-slate-300 font-bold text-xs">—</span>
                                        @endif
                                    </td>

                                    <!-- 6. TANGGAL PENGIRIMAN -->
                                    <td class="py-4 px-3 bg-emerald-50/20 text-slate-700 font-medium text-xs">
                                        @if($firstDelivery && $firstDelivery->delivery_date)
                                            {{ $firstDelivery->delivery_date->format('d M Y') }}
                                        @else
                                            <span class="text-slate-300 font-bold">—</span>
                                        @endif
                                    </td>

                                    <!-- 7. NAMA PIC (HANYA DARI DO YANG SUDAH DITERBITKAN) -->
                                    <td class="py-4 px-3 bg-emerald-50/20">
                                        @if($firstDelivery && !empty($firstDelivery->recipient_name))
                                            <span class="font-bold text-slate-800 text-xs">{{ $firstDelivery->recipient_name }}</span>
                                        @else
                                            <span class="text-slate-300 font-bold text-xs">—</span>
                                        @endif
                                    </td>

                                    <!-- 8. NO TELP PIC (HANYA DARI DO YANG SUDAH DITERBITKAN) -->
                                    <td class="py-4 px-3 bg-emerald-50/20 font-mono text-[11px] text-slate-600">
                                        @if($firstDelivery && !empty($firstDelivery->recipient_phone))
                                            {{ $firstDelivery->recipient_phone }}
                                        @else
                                            <span class="text-slate-300 font-bold text-xs">—</span>
                                        @endif
                                    </td>

                                    <!-- 9. DRIVER DARI MASTER DATA VENDOR -->
                                    <td class="py-4 px-3 bg-emerald-50/20 text-xs">
                                        @if($firstDelivery && $firstDelivery->driver_name)
                                            <span class="font-semibold text-slate-800">{{ $firstDelivery->driver_name }}</span>
                                        @else
                                            <span class="text-slate-300 font-bold">—</span>
                                        @endif
                                    </td>

                                    <!-- 10. NO PLAT -->
                                    <td class="py-4 px-3 bg-emerald-50/20">
                                        @if($firstDelivery && $firstDelivery->vehicle_number)
                                            <span class="px-2 py-0.5 rounded bg-white border border-emerald-200/80 font-mono font-bold text-slate-800 text-[11px]">
                                                {{ $firstDelivery->vehicle_number }}
                                            </span>
                                        @else
                                            <span class="text-slate-300 font-bold">—</span>
                                        @endif
                                    </td>

                                    <!-- 11. REMAKS -->
                                    <td class="py-4 px-4 bg-emerald-50/20 text-xs max-w-xs">
                                        @if($firstDelivery && $firstDelivery->notes)
                                            <span class="text-slate-700 truncate block max-w-[190px]" title="{{ $firstDelivery->notes }}">
                                                {{ $firstDelivery->notes }}
                                            </span>
                                        @else
                                            <span class="text-slate-300 font-bold">—</span>
                                        @endif
                                    </td>

                                    <!-- 12. NO. PO VENDOR (JIKA VENDOR SUDAH DIALOKASIKAN) -->
                                    <td class="py-4 px-3 bg-purple-50/20 font-mono text-xs">
                                        @if($order->vendorOrders && $order->vendorOrders->isNotEmpty())
                                            <div class="flex flex-col gap-1.5">
                                                @foreach($order->vendorOrders as $vo)
                                                    <div class="inline-flex items-center space-x-1.5 px-2 py-0.5 rounded-lg bg-purple-100 text-purple-900 font-bold text-[11px] border border-purple-200/80 shadow-2xs" title="Vendor: {{ $vo->contact->name ?? 'Vendor' }}">
                                                        <svg class="w-3 h-3 text-purple-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                                        <a href="{{ route('erp.procurement.index', ['search' => $vo->order_number]) }}" 
                                                           class="hover:underline font-bold text-purple-950 cursor-pointer" 
                                                           title="Buka PO di Pengadaan: {{ $vo->order_number }}">
                                                            {{ $vo->order_number }}
                                                        </a>
                                                        <a href="{{ route('erp.procurement.pdf', $vo->id) }}" 
                                                           target="_blank" 
                                                           class="p-0.5 rounded text-purple-700 hover:text-purple-950 hover:bg-purple-200 transition shrink-0 ml-0.5" 
                                                           title="Cetak PDF Purchase Order ({{ $vo->order_number }})">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                                        </a>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-slate-300 font-bold text-xs">—</span>
                                        @endif
                                    </td>
                                @endif

                                <!-- Kolom No. Invoice di Header Table -->
                                <td class="py-4 px-4 bg-indigo-50/10 font-mono text-xs">
                                    @if($orderInvoice)
                                        <div class="inline-flex items-center space-x-1.5 font-bold text-indigo-900 bg-indigo-50 hover:bg-indigo-100/90 px-2.5 py-1 rounded-lg border border-indigo-200 transition shadow-2xs">
                                            <svg class="w-3.5 h-3.5 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            <a href="{{ route('erp.finance.index', ['tab' => 'invoices', 'search' => $orderInvoice->invoice_number]) }}" 
                                               class="hover:underline font-bold text-indigo-950 cursor-pointer" 
                                               title="Buka Tab Invoices di Finance: {{ $orderInvoice->invoice_number }}">
                                                {{ $orderInvoice->invoice_number }}
                                            </a>
                                            <a href="{{ route('erp.finance.invoices.pdf', $orderInvoice->id) }}" 
                                               target="_blank" 
                                               class="p-0.5 rounded text-indigo-700 hover:text-indigo-950 hover:bg-indigo-200 transition shrink-0 ml-0.5" 
                                               title="Cetak PDF Invoice ({{ $orderInvoice->invoice_number }})">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                            </a>
                                        </div>
                                    @else
                                        <span class="text-slate-300 font-bold text-xs">—</span>
                                    @endif
                                </td>

                                <!-- 12. Nilai Total -->
                                <td class="py-4 px-4 text-right font-mono text-xs">
                                    <div class="font-black text-slate-900">
                                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                    </div>
                                </td>

                                <!-- 13. Status Pill -->
                                <td class="py-4 px-4 text-center">
                                    @if($order->order_type === 'quotation')
                                        @if($computedQuotationStatus === 'cancel')
                                            <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200"
                                                  @if($order->cancel_reason) title="Alasan: {{ $order->cancel_reason }}" @endif>
                                                <svg class="w-3 h-3 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                <span x-text="lang === 'zh' ? '已取消' : 'Cancel'">Cancel</span>
                                            </span>
                                        @elseif($computedQuotationStatus === 'transaksi')
                                            <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                                <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                                <span x-text="lang === 'zh' ? '已转交易' : 'Transaksi'">Transaksi</span>
                                            </span>
                                        @elseif($computedQuotationStatus === 'tidak_aktif')
                                            <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-300"
                                                  title="Kedaluwarsa pada {{ $order->valid_until ? date('d/m/Y', strtotime($order->valid_until)) : '-' }}">
                                                <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                <span x-text="lang === 'zh' ? '已过期' : 'Tidak Aktif'">Tidak Aktif</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200"
                                                  title="Berlaku hingga {{ $order->valid_until ? date('d/m/Y', strtotime($order->valid_until)) : '-' }}">
                                                <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                <span x-text="lang === 'zh' ? '有效' : 'Aktif'">Aktif</span>
                                            </span>
                                        @endif
                                    @else
                                        @if($order->status === 'completed')
                                            <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                <span x-text="lang === 'zh' ? '已完成' : 'Selesai'">Selesai</span>
                                            </span>
                                        @elseif($order->status === 'cancelled')
                                            <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200"
                                                  @if($order->cancel_reason) title="Alasan: {{ $order->cancel_reason }}" @endif>
                                                <svg class="w-3 h-3 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                <span x-text="lang === 'zh' ? '已取消' : 'Batal'">Batal</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                                <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                <span x-text="lang === 'zh' ? '处理中' : 'Aktif / Proses'">Aktif / Proses</span>
                                            </span>
                                        @endif
                                    @endif
                                </td>

                                <!-- 14. Aksi (Option A: Smart Contextual Button + Dropdown •••) -->
                                <td class="py-4 px-3 text-center sticky right-0 bg-white group-hover:bg-slate-50 transition shadow-[-6px_0_8px_-4px_rgba(0,0,0,0.06)] border-l border-slate-100"
                                    :class="activeActionMenuId === {{ $order->id }} ? 'z-40' : 'z-10'">
                                    <div class="flex items-center justify-center space-x-1.5 {{ $isCancelled ? 'opacity-40 pointer-events-none cursor-not-allowed select-none' : '' }}">
                                        
                                        <!-- 1. QUOTATION ACTIONS -->
                                        @if($order->order_type === 'quotation')
                                            @php
                                                $isConverted = ($order->children && $order->children->whereIn('order_type', ['rental_order', 'sales_order'])->isNotEmpty()) || $order->status === 'approved';
                                            @endphp

                                            <!-- Primary Button for Quotation -->
                                            @if(!$isConverted && $order->status !== 'cancelled')
                                                <!-- Generate Transaksi Button (Primary) -->
                                                <div class="relative group/tip inline-flex">
                                                    <button type="button" 
                                                            @click="generateTransaction({{ $order->id }}, '{{ $order->order_number }}')" 
                                                            class="w-8 h-8 rounded-xl bg-blue-600 hover:bg-blue-700 text-white flex items-center justify-center transition active:scale-95 shadow-xs"
                                                            :title="lang === 'zh' ? '生成交易订单' : 'Generate Transaksi'">
                                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                                    </button>
                                                    <div class="absolute {{ $index === 0 ? 'top-full mt-1.5' : 'bottom-full mb-1.5' }} left-1/2 -translate-x-1/2 hidden group-hover/tip:flex flex-col items-center z-30 pointer-events-none">
                                                        @if($index === 0)
                                                            <div class="w-1.5 h-1.5 bg-slate-900 rotate-45 -mb-1"></div>
                                                        @endif
                                                        <div class="bg-slate-900 text-white text-[10px] font-bold px-2 py-1 rounded-lg shadow-lg whitespace-nowrap">
                                                            <span x-text="lang === 'zh' ? '生成交易订单' : 'Generate Transaksi'">Generate Transaksi</span>
                                                        </div>
                                                        @if($index !== 0)
                                                            <div class="w-1.5 h-1.5 bg-slate-900 rotate-45 -mt-1"></div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @else
                                                <!-- Cetak PDF Penawaran (Primary if converted) -->
                                                <div class="relative group/tip inline-flex">
                                                    <a href="{{ route('erp.quotations.pdf', $order->id) }}" 
                                                       target="_blank" 
                                                       class="w-8 h-8 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 flex items-center justify-center transition active:scale-95 shadow-xs"
                                                       :title="lang === 'zh' ? '打印报价单 (PDF)' : 'Cetak Penawaran (PDF)'">
                                                        <svg class="w-4 h-4 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                                    </a>
                                                    <div class="absolute {{ $index === 0 ? 'top-full mt-1.5' : 'bottom-full mb-1.5' }} left-1/2 -translate-x-1/2 hidden group-hover/tip:flex flex-col items-center z-30 pointer-events-none">
                                                        @if($index === 0)
                                                            <div class="w-1.5 h-1.5 bg-slate-900 rotate-45 -mb-1"></div>
                                                        @endif
                                                        <div class="bg-slate-900 text-white text-[10px] font-bold px-2 py-1 rounded-lg shadow-lg whitespace-nowrap">
                                                            <span x-text="lang === 'zh' ? '打印报价单 (PDF)' : 'Cetak Penawaran (PDF)'">Cetak Penawaran (PDF)</span>
                                                        </div>
                                                        @if($index !== 0)
                                                            <div class="w-1.5 h-1.5 bg-slate-900 rotate-45 -mt-1"></div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- Dropdown Menu ••• for Quotation -->
                                            <div class="relative inline-block text-left">
                                                <button type="button" 
                                                        @click.stop="toggleActionMenu({{ $order->id }})" 
                                                        class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200/90 flex items-center justify-center transition active:scale-95 shadow-xs"
                                                        :class="activeActionMenuId === {{ $order->id }} ? 'bg-slate-200 text-slate-900 ring-2 ring-slate-300' : ''"
                                                        :title="lang === 'zh' ? '更多操作' : 'Aksi Lainnya'">
                                                    <svg class="w-4 h-4 text-slate-700" fill="currentColor" viewBox="0 0 24 24">
                                                        <circle cx="5" cy="12" r="2" />
                                                        <circle cx="12" cy="12" r="2" />
                                                        <circle cx="19" cy="12" r="2" />
                                                    </svg>
                                                </button>

                                                <!-- Dropdown Menu Popover (Opens Downward) -->
                                                <div x-show="activeActionMenuId === {{ $order->id }}" 
                                                     @click.outside="if (activeActionMenuId === {{ $order->id }}) closeActionMenu()" 
                                                     x-cloak
                                                     x-transition:enter="transition ease-out duration-150"
                                                     x-transition:enter-start="opacity-0 scale-95"
                                                     x-transition:enter-end="opacity-100 scale-100"
                                                     x-transition:leave="transition ease-in duration-100"
                                                     x-transition:leave-start="opacity-100 scale-100"
                                                     x-transition:leave-end="opacity-0 scale-95"
                                                     class="absolute right-0 {{ $loop->remaining < 2 ? 'bottom-full mb-1.5' : 'top-full mt-1.5' }} w-48 bg-white rounded-2xl shadow-xl border border-slate-200/90 py-1.5 z-50 text-left">
                                                    
                                                    <!-- Cetak Penawaran -->
                                                    <a href="{{ route('erp.quotations.pdf', $order->id) }}" 
                                                       target="_blank" 
                                                       @click="closeActionMenu()" 
                                                       class="flex items-center space-x-2.5 px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-amber-50 hover:text-amber-800 transition">
                                                        <span class="w-6 h-6 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center shrink-0">
                                                            <svg class="w-3.5 h-3.5 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                                        </span>
                                                        <span x-text="lang === 'zh' ? '打印报价单 (PDF)' : 'Cetak Penawaran (PDF)'">Cetak Penawaran (PDF)</span>
                                                    </a>

                                                    <!-- Edit Penawaran -->
                                                    <button type="button" 
                                                            @click="closeActionMenu(); editQuotation(@js($order))" 
                                                            class="w-full flex items-center space-x-2.5 px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-blue-50 hover:text-blue-700 transition text-left">
                                                        <span class="w-6 h-6 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center shrink-0">
                                                            <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                        </span>
                                                        <span x-text="lang === 'zh' ? '编辑报价单' : 'Edit Penawaran'">Edit Penawaran</span>
                                                    </button>

                                                    <!-- Generate Transaksi (jika belum dikonversi) -->
                                                    @if(!$isConverted && $order->status !== 'cancelled')
                                                        <button type="button" 
                                                                @click="closeActionMenu(); generateTransaction({{ $order->id }}, '{{ $order->order_number }}')" 
                                                                class="w-full flex items-center space-x-2.5 px-3.5 py-2 text-xs font-semibold text-blue-700 hover:bg-blue-50 transition text-left">
                                                            <span class="w-6 h-6 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center shrink-0">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                                            </span>
                                                            <span x-text="lang === 'zh' ? '生成交易订单' : 'Generate Transaksi'">Generate Transaksi</span>
                                                        </button>
                                                    @endif

                                                    <!-- Batalkan Penawaran (jika belum cancel) -->
                                                    @if($order->status !== 'cancelled')
                                                        <div class="my-1 border-t border-slate-100"></div>
                                                        <button type="button" 
                                                                @click="closeActionMenu(); openCancelModal(@js($order))" 
                                                                class="w-full flex items-center space-x-2.5 px-3.5 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 transition text-left">
                                                            <span class="w-6 h-6 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center shrink-0">
                                                                <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                            </span>
                                                            <span x-text="lang === 'zh' ? '取消报价单' : 'Batalkan Penawaran'">Batalkan Penawaran</span>
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif

                                        <!-- 2. SO & RO ACTIONS (Option A: Primary Next-Step Action + Dropdown •••) -->
                                        @if($order->order_type === 'rental_order' || $order->order_type === 'sales_order')
                                            @php
                                                $isVendorAllocated = $order->vendorOrders && $order->vendorOrders->isNotEmpty();
                                            @endphp

                                            <!-- Contextual Smart Primary Action Button -->
                                            @if(!$isVendorAllocated && !$isCancelled)
                                                <!-- Step 1: Buat PO Vendor -->
                                                <div class="relative group/tip inline-flex">
                                                    <button type="button" 
                                                            @click="openAllocateModal(@js($order))" 
                                                            class="w-8 h-8 rounded-xl bg-purple-50 hover:bg-purple-100 text-purple-700 border border-purple-200 flex items-center justify-center transition active:scale-95 shadow-xs"
                                                            :title="lang === 'zh' ? '创建供应商采购订单 (Buat PO)' : 'Buat PO Vendor'">
                                                        <svg class="w-4 h-4 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                                    </button>
                                                    <div class="absolute {{ $index === 0 ? 'top-full mt-1.5' : 'bottom-full mb-1.5' }} left-1/2 -translate-x-1/2 hidden group-hover/tip:flex flex-col items-center z-30 pointer-events-none">
                                                        @if($index === 0)
                                                            <div class="w-1.5 h-1.5 bg-slate-900 rotate-45 -mb-1"></div>
                                                        @endif
                                                        <div class="bg-slate-900 text-white text-[10px] font-bold px-2 py-1 rounded-lg shadow-lg whitespace-nowrap">
                                                            <span x-text="lang === 'zh' ? '创建采购单 (Buat PO)' : 'Buat PO Vendor'">Buat PO Vendor</span>
                                                        </div>
                                                        @if($index !== 0)
                                                            <div class="w-1.5 h-1.5 bg-slate-900 rotate-45 -mt-1"></div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @elseif(!$hasDeliveries && !$isCancelled)
                                                <!-- Step 2: Generate DO Pertama -->
                                                <div class="relative group/tip inline-flex">
                                                    <button type="button" 
                                                            @click="openDoModal(@js($order))" 
                                                            class="w-8 h-8 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white flex items-center justify-center transition active:scale-95 shadow-xs"
                                                            :title="lang === 'zh' ? '生成送货单 (Generate DO)' : 'Generate Surat Jalan (DO)'">
                                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h2"></path></svg>
                                                    </button>
                                                    <div class="absolute {{ $index === 0 ? 'top-full mt-1.5' : 'bottom-full mb-1.5' }} left-1/2 -translate-x-1/2 hidden group-hover/tip:flex flex-col items-center z-30 pointer-events-none">
                                                        @if($index === 0)
                                                            <div class="w-1.5 h-1.5 bg-slate-900 rotate-45 -mb-1"></div>
                                                        @endif
                                                        <div class="bg-slate-900 text-white text-[10px] font-bold px-2 py-1 rounded-lg shadow-lg whitespace-nowrap">
                                                            <span x-text="lang === 'zh' ? '生成送货单 (DO)' : 'Generate Surat Jalan (DO)'">Generate DO</span>
                                                        </div>
                                                        @if($index !== 0)
                                                            <div class="w-1.5 h-1.5 bg-slate-900 rotate-45 -mt-1"></div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @elseif(!$orderInvoice && !$isCancelled)
                                                <!-- Step 3: Generate Invoice Tagihan -->
                                                <div class="relative group/tip inline-flex">
                                                    <button type="button" 
                                                            @click="openGenerateInvoiceModal(@js($order))" 
                                                            class="w-8 h-8 rounded-xl bg-amber-400 hover:bg-amber-500 text-slate-950 flex items-center justify-center transition active:scale-95 shadow-xs font-black"
                                                            :title="lang === 'zh' ? '开具销售发票 (Generate Invoice)' : 'Buat Invoice Tagihan'">
                                                        <svg class="w-4 h-4 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z"></path></svg>
                                                    </button>
                                                    <div class="absolute {{ $index === 0 ? 'top-full mt-1.5' : 'bottom-full mb-1.5' }} left-1/2 -translate-x-1/2 hidden group-hover/tip:flex flex-col items-center z-30 pointer-events-none">
                                                        @if($index === 0)
                                                            <div class="w-1.5 h-1.5 bg-slate-900 rotate-45 -mb-1"></div>
                                                        @endif
                                                        <div class="bg-slate-900 text-white text-[10px] font-bold px-2 py-1 rounded-lg shadow-lg whitespace-nowrap">
                                                            <span x-text="lang === 'zh' ? '开具发票 (Generate Invoice)' : 'Buat Invoice'">Buat Invoice</span>
                                                        </div>
                                                        @if($index !== 0)
                                                            <div class="w-1.5 h-1.5 bg-slate-900 rotate-45 -mt-1"></div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @elseif($order->status !== 'completed' && $order->status !== 'cancelled')
                                                <!-- Step 4: Selesaikan Transaksi -->
                                                <div class="relative group/tip inline-flex">
                                                    <button type="button" 
                                                            @click="completeTransaction({{ $order->id }}, '{{ $order->order_number }}')" 
                                                            class="w-8 h-8 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-300 flex items-center justify-center transition active:scale-95 shadow-xs"
                                                            :title="lang === 'zh' ? '完成交易订单 (Tandai Selesai)' : 'Selesaikan Transaksi'">
                                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                    </button>
                                                    <div class="absolute {{ $index === 0 ? 'top-full mt-1.5' : 'bottom-full mb-1.5' }} left-1/2 -translate-x-1/2 hidden group-hover/tip:flex flex-col items-center z-30 pointer-events-none">
                                                        @if($index === 0)
                                                            <div class="w-1.5 h-1.5 bg-slate-900 rotate-45 -mb-1"></div>
                                                        @endif
                                                        <div class="bg-slate-900 text-white text-[10px] font-bold px-2 py-1 rounded-lg shadow-lg whitespace-nowrap">
                                                            <span x-text="lang === 'zh' ? '完成订单 (Selesai)' : 'Selesaikan Transaksi'">Selesaikan Transaksi</span>
                                                        </div>
                                                        @if($index !== 0)
                                                            <div class="w-1.5 h-1.5 bg-slate-900 rotate-45 -mt-1"></div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- Dropdown Menu ••• for Transaksi -->
                                            <div class="relative inline-block text-left">
                                                <button type="button" 
                                                        @click.stop="toggleActionMenu({{ $order->id }})" 
                                                        class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200/90 flex items-center justify-center transition active:scale-95 shadow-xs"
                                                        :class="activeActionMenuId === {{ $order->id }} ? 'bg-slate-200 text-slate-900 ring-2 ring-slate-300' : ''"
                                                        :title="lang === 'zh' ? '更多操作' : 'Aksi Lainnya'">
                                                    <svg class="w-4 h-4 text-slate-700" fill="currentColor" viewBox="0 0 24 24">
                                                        <circle cx="5" cy="12" r="2" />
                                                        <circle cx="12" cy="12" r="2" />
                                                        <circle cx="19" cy="12" r="2" />
                                                    </svg>
                                                </button>

                                                <!-- Dropdown Menu Popover (Opens Downward) -->
                                                <div x-show="activeActionMenuId === {{ $order->id }}" 
                                                     @click.outside="if (activeActionMenuId === {{ $order->id }}) closeActionMenu()" 
                                                     x-cloak
                                                     x-transition:enter="transition ease-out duration-150"
                                                     x-transition:enter-start="opacity-0 scale-95"
                                                     x-transition:enter-end="opacity-100 scale-100"
                                                     x-transition:leave="transition ease-in duration-100"
                                                     x-transition:leave-start="opacity-100 scale-100"
                                                     x-transition:leave-end="opacity-0 scale-95"
                                                     class="absolute right-0 {{ $loop->remaining < 2 ? 'bottom-full mb-1.5' : 'top-full mt-1.5' }} w-56 bg-white rounded-2xl shadow-xl border border-slate-200/90 py-1.5 z-50 text-left">
                                                    
                                                    <!-- 1. Buat PO Vendor -->
                                                    @if(!$isVendorAllocated)
                                                        <button type="button" 
                                                                @click="closeActionMenu(); openAllocateModal(@js($order))" 
                                                                class="w-full flex items-center space-x-2.5 px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-purple-50 hover:text-purple-800 transition text-left">
                                                            <span class="w-6 h-6 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center shrink-0">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                                            </span>
                                                            <span x-text="lang === 'zh' ? '创建供应商采购单 (Buat PO)' : 'Buat PO Vendor'">Buat PO Vendor</span>
                                                        </button>
                                                    @else
                                                        <button type="button" 
                                                                @click="closeActionMenu(); openAllocateModal(@js($order))" 
                                                                class="w-full flex items-center justify-between px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-purple-50 hover:text-purple-800 transition text-left">
                                                            <div class="flex items-center space-x-2.5 min-w-0">
                                                                <span class="w-6 h-6 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center shrink-0">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                                            </span>
                                                                <span class="truncate" x-text="lang === 'zh' ? '修改供应商采购单' : 'Ubah / Atur PO Vendor'">Ubah / Atur PO Vendor</span>
                                                            </div>
                                                            <span class="text-[10px] text-purple-700 font-bold bg-purple-50 px-1.5 py-0.5 rounded border border-purple-200 shrink-0 ml-1">Ada PO</span>
                                                        </button>
                                                    @endif

                                                    <!-- 2. Surat Jalan (DO) -->
                                                    @if($hasDeliveries)
                                                        <a href="{{ route('erp.deliveries.pdf', $firstDelivery->id) }}" 
                                                           target="_blank" 
                                                           @click="closeActionMenu()" 
                                                           class="flex items-center space-x-2.5 px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-amber-50 hover:text-amber-800 transition text-left">
                                                            <span class="w-6 h-6 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center shrink-0">
                                                                <svg class="w-3.5 h-3.5 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                                            </span>
                                                            <span x-text="lang === 'zh' ? '打印送货单 (DO)' : 'Cetak Surat Jalan (DO)'">Cetak Surat Jalan (DO)</span>
                                                        </a>

                                                        <button type="button" 
                                                                @click="closeActionMenu(); openDoModal(@js($order))" 
                                                                class="w-full flex items-center space-x-2.5 px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-cyan-50 hover:text-cyan-800 transition text-left">
                                                            <span class="w-6 h-6 rounded-lg bg-cyan-100 text-cyan-700 flex items-center justify-center shrink-0">
                                                                <svg class="w-3.5 h-3.5 text-cyan-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                                                            </span>
                                                            <span x-text="lang === 'zh' ? '新增送货单 (DO Baru)' : 'Terbitkan DO Baru'">Terbitkan DO Baru</span>
                                                        </button>
                                                    @else
                                                        <button type="button" 
                                                                @click="closeActionMenu(); openDoModal(@js($order))" 
                                                                class="w-full flex items-center space-x-2.5 px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 transition text-left">
                                                            <span class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                                                                <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h2"></path></svg>
                                                            </span>
                                                            <span x-text="lang === 'zh' ? '生成送货单 (DO)' : 'Generate Surat Jalan (DO)'">Generate Surat Jalan (DO)</span>
                                                        </button>
                                                    @endif

                                                    <!-- 3. Invoice Tagihan -->
                                                    @if(!$orderInvoice)
                                                        <button type="button" 
                                                                @click="closeActionMenu(); openGenerateInvoiceModal(@js($order))" 
                                                                class="w-full flex items-center space-x-2.5 px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-amber-50 hover:text-amber-800 transition text-left">
                                                            <span class="w-6 h-6 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center shrink-0">
                                                                <svg class="w-3.5 h-3.5 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z"></path></svg>
                                                            </span>
                                                            <span x-text="lang === 'zh' ? '开具发票 (Generate Invoice)' : 'Buat Invoice Tagihan'">Buat Invoice Tagihan</span>
                                                        </button>
                                                    @else
                                                        <a href="{{ route('erp.invoices.pdf', $orderInvoice->id) }}" 
                                                           target="_blank" 
                                                           @click="closeActionMenu()" 
                                                           class="flex items-center space-x-2.5 px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-800 transition text-left">
                                                            <span class="w-6 h-6 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center shrink-0">
                                                                <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                            </span>
                                                            <span class="truncate">Lihat Invoice ({{ $orderInvoice->invoice_number }})</span>
                                                        </a>
                                                    @endif

                                                    <div class="my-1 border-t border-slate-100"></div>

                                                    <!-- 4. Selesaikan Transaksi -->
                                                    @if($order->status !== 'completed' && $order->status !== 'cancelled')
                                                        <button type="button" 
                                                                @click="closeActionMenu(); completeTransaction({{ $order->id }}, '{{ $order->order_number }}')" 
                                                                class="w-full flex items-center space-x-2.5 px-3.5 py-2 text-xs font-semibold text-emerald-700 hover:bg-emerald-50 transition text-left">
                                                            <span class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                            </span>
                                                            <span x-text="lang === 'zh' ? '完成订单 (Selesai)' : 'Tandai Selesai'">Tandai Selesai</span>
                                                        </button>
                                                    @endif

                                                    <!-- 5. Batalkan Transaksi -->
                                                    @if($order->status !== 'cancelled')
                                                        <button type="button" 
                                                                @click="closeActionMenu(); openCancelModal(@js($order))" 
                                                                class="w-full flex items-center space-x-2.5 px-3.5 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 transition text-left">
                                                            <span class="w-6 h-6 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center shrink-0">
                                                                <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                            </span>
                                                            <span x-text="lang === 'zh' ? '取消交易订单' : 'Batalkan Transaksi'">Batalkan Transaksi</span>
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>

                            <!-- ========================================================================= -->
                            <!-- EXPANDABLE PRODUCT DETAILS ROW (ALL TABS: EXPAND & COLLAPSE)              -->
                            <!-- ========================================================================= -->
                            <tr x-show="isExpanded({{ $order->id }})" 
                                x-cloak 
                                class="bg-slate-50/90 border-y-2 border-slate-200">
                                <td colspan="{{ $typeFilter === 'quotation' ? 10 : 17 }}" class="p-4 sm:p-5">
                                    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
                                        <!-- Header of Expanded Card -->
                                        <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex flex-wrap items-center justify-between gap-3">
                                            <div class="flex items-center space-x-3">
                                                <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-black shrink-0">
                                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                                </div>
                                                <div>
                                                    <div class="flex items-center space-x-2">
                                                        <h4 class="font-black text-slate-900 text-xs flex items-center space-x-1.5">
                                                            <span x-text="lang === 'zh' ? '产品清单明细' : 'Rincian Produk Pesanan'">Rincian Produk Pesanan</span>
                                                            <span class="font-mono text-blue-600">({{ $order->order_number }})</span>
                                                        </h4>
                                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                                            {{ $order->items->count() }} Produk Dipesan
                                                        </span>
                                                    </div>
                                                    @if($order->parent)
                                                        <div class="text-[11px] text-slate-500 mt-0.5 flex items-center space-x-1.5">
                                                            <span>Berasal dari Penawaran:</span>
                                                            <div class="inline-flex items-center space-x-1 font-mono font-bold text-amber-900 bg-amber-50 hover:bg-amber-100 px-1.5 py-0.5 rounded border border-amber-300 transition">
                                                                <a href="{{ route('erp.orders.index', ['type' => 'quotation', 'search' => $order->parent->order_number]) }}" class="hover:underline font-bold text-amber-950" title="Buka Tab Penawaran">
                                                                    {{ $order->parent->order_number }}
                                                                </a>
                                                                <a href="{{ route('erp.quotations.pdf', $order->parent->id) }}" target="_blank" class="p-0.5 rounded text-amber-700 hover:text-amber-950 hover:bg-amber-200 transition" title="Cetak PDF Penawaran">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="flex items-center space-x-2">
                                                <button type="button" 
                                                        @click="toggleExpand({{ $order->id }})" 
                                                        class="px-3 py-1.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-100 text-slate-600 text-xs font-bold transition flex items-center space-x-1 shadow-xs">
                                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                                    <span x-text="lang === 'zh' ? '收起明细' : 'Tutup Rincian'">Tutup Rincian</span>
                                                </button>
                                            </div>
                                        </div>

                                        @if($order->status === 'cancelled' && $order->cancel_reason)
                                            <div class="px-5 py-3 bg-rose-50 border-b border-rose-200 flex items-start space-x-2.5 text-xs text-rose-800">
                                                <svg class="w-4 h-4 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                                <div>
                                                    <span class="font-bold">Transaksi Dibatalkan:</span>
                                                    <span class="text-rose-700 font-medium">{{ $order->cancel_reason }}</span>
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Product Table inside Card -->
                                        <div class="overflow-x-auto">
                                            <table class="w-full text-left text-xs whitespace-nowrap">
                                                <thead>
                                                    <tr class="bg-slate-100/70 text-slate-600 font-bold border-b border-slate-200 text-[10px] uppercase tracking-wider">
                                                        <th class="py-2.5 px-3 text-center w-8">#</th>
                                                        <th class="py-2.5 px-3 text-center w-14" x-text="lang === 'zh' ? '图片' : 'Foto'">Foto</th>
                                                        <th class="py-2.5 px-3 text-center" x-text="lang === 'zh' ? '交易类型' : 'Tipe Transaksi'">Tipe Transaksi</th>
                                                        <th class="py-2.5 px-4" x-text="lang === 'zh' ? '产品名称' : 'Nama Produk'">Nama Produk</th>
                                                        <th class="py-2.5 px-4" x-text="lang === 'zh' ? '规格型号' : 'Spesifikasi'">Spesifikasi</th>
                                                        <th class="py-2.5 px-3 text-center" x-text="lang === 'zh' ? '数量/单位' : 'Qty / Satuan'">Qty / Satuan</th>
                                                        @if($order->order_type === 'rental_order' || $order->quotation_type === 'rental' || $order->quotation_type === 'both')
                                                            <th class="py-2.5 px-3 text-center" x-text="lang === 'zh' ? '租赁期限' : 'Durasi Sewa'">Durasi Sewa</th>
                                                        @endif
                                                        <th class="py-2.5 px-4 text-right" x-text="lang === 'zh' ? '单价 (Rp)' : 'Harga Satuan (Rp)'">Harga Satuan (Rp)</th>
                                                        <th class="py-2.5 px-4 text-right font-black" x-text="lang === 'zh' ? '项目小计 (Rp)' : 'Subtotal Item (Rp)'">Subtotal Item (Rp)</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-slate-100">
                                                    @forelse($order->items as $itIndex => $it)
                                                        @php
                                                            $itemImg = $it->image ?? $it->item?->image ?? null;
                                                        @endphp
                                                        <tr class="hover:bg-slate-50/70 transition">
                                                            <td class="py-3 px-3 text-center text-slate-400 font-mono text-[11px]">{{ $itIndex + 1 }}</td>
                                                            <td class="py-3 px-3 text-center">
                                                                <div class="w-10 h-10 mx-auto rounded-xl bg-slate-100 border border-slate-200 overflow-hidden flex items-center justify-center shrink-0 shadow-xs">
                                                                    @if($itemImg)
                                                                        <img src="{{ asset($itemImg) }}" alt="Unit" class="w-full h-full object-cover">
                                                                    @else
                                                                        <svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                                    @endif
                                                                </div>
                                                            </td>
                                                            <!-- Tipe Transaksi (Detail) -->
                                                            <td class="py-3 px-3 text-center">
                                                                @if(($it->transaction_type ?? '') === 'sales' || $order->order_type === 'sales_order')
                                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                                        <span x-text="lang === 'zh' ? '购买' : 'Pembelian'">Pembelian</span>
                                                                    </span>
                                                                @else
                                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                                        <span x-text="lang === 'zh' ? '租赁' : 'Sewa / Rental'">Sewa / Rental</span>
                                                                    </span>
                                                                @endif
                                                            </td>
                                                            <!-- Nama Produk (Dipisahkan dari Spesifikasi) -->
                                                            <td class="py-3 px-4">
                                                                <span class="font-bold text-slate-900 text-xs">{{ $it->description ?: ($it->item?->name ?? 'Produk') }}</span>
                                                                @if($it->notes)
                                                                    <div class="text-[10px] text-amber-700 italic mt-0.5">{{ $it->notes }}</div>
                                                                @endif
                                                            </td>
                                                            <!-- Spesifikasi (Kolom Mandiri) -->
                                                            <td class="py-3 px-4 font-mono text-slate-600 text-xs">
                                                                {{ $it->specification ?: '—' }}
                                                            </td>
                                                            <!-- Qty / Satuan -->
                                                            <td class="py-3 px-3 text-center font-bold text-slate-800">
                                                                {{ (float)$it->quantity }} {{ $it->unit ?: 'Unit' }}
                                                            </td>
                                                            @if($order->order_type === 'rental_order' || $order->quotation_type === 'rental' || $order->quotation_type === 'both')
                                                                <td class="py-3 px-3 text-center">
                                                                    @if(($it->transaction_type ?? 'rental') === 'sales')
                                                                        <span class="text-slate-400 font-normal text-xs">-</span>
                                                                    @else
                                                                        <span class="px-2 py-0.5 rounded-lg bg-blue-50 text-blue-800 font-bold text-[10px] border border-blue-200">
                                                                            {{ $it->duration ?: ($order->rental_duration ?: 1) }} {{ $it->duration_unit ?: ($order->rental_duration_unit ?: 'Bulan') }}
                                                                        </span>
                                                                    @endif
                                                                </td>
                                                            @endif
                                                            <!-- Harga Satuan -->
                                                            <td class="py-3 px-4 text-right font-mono font-medium text-slate-700">
                                                                Rp {{ number_format($it->unit_price, 0, ',', '.') }}
                                                            </td>
                                                            <!-- Subtotal Item (Diskon dihapus) -->
                                                            <td class="py-3 px-4 text-right font-mono font-black text-slate-900">
                                                                Rp {{ number_format($it->line_total, 0, ',', '.') }}
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="{{ ($order->order_type === 'rental_order' || $order->quotation_type === 'rental' || $order->quotation_type === 'both') ? 9 : 8 }}" class="py-4 text-center text-slate-400 italic">Tidak ada rincian produk pada pesanan ini.</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                                <tfoot class="bg-slate-50 border-t border-slate-200 font-mono text-xs">
                                                    <tr>
                                                        <td colspan="{{ ($order->order_type === 'rental_order' || $order->quotation_type === 'rental' || $order->quotation_type === 'both') ? 8 : 7 }}" class="py-2.5 px-4 text-right font-bold text-slate-600 font-sans">
                                                            Subtotal Seluruh Produk:
                                                        </td>
                                                        <td class="py-2.5 px-4 text-right font-black text-slate-900">
                                                            Rp {{ number_format($order->subtotal ?: $order->items->sum('line_total'), 0, ',', '.') }}
                                                        </td>
                                                    </tr>
                                                    @if(($order->shipping_fee ?? 0) > 0)
                                                        <tr>
                                                            <td colspan="{{ ($order->order_type === 'rental_order' || $order->quotation_type === 'rental' || $order->quotation_type === 'both') ? 8 : 7 }}" class="py-2 px-4 text-right font-bold text-slate-600 font-sans">
                                                                Biaya Pengiriman:
                                                                @if(($order->shipping_delivery_fee ?? 0) > 0 || ($order->shipping_return_fee ?? 0) > 0)
                                                                    <span class="text-[10px] text-slate-400 font-normal block font-sans">(Berangkat: Rp {{ number_format($order->shipping_delivery_fee ?? 0, 0, ',', '.') }} | Pengembalian: Rp {{ number_format($order->shipping_return_fee ?? 0, 0, ',', '.') }})</span>
                                                                @endif
                                                            </td>
                                                            <td class="py-2 px-4 text-right font-bold text-slate-800">
                                                                + Rp {{ number_format($order->shipping_fee, 0, ',', '.') }}
                                                            </td>
                                                        </tr>
                                                    @endif
                                                    @if(($order->tax_amount ?? 0) > 0)
                                                        <tr>
                                                            <td colspan="{{ ($order->order_type === 'rental_order' || $order->quotation_type === 'rental' || $order->quotation_type === 'both') ? 8 : 7 }}" class="py-2 px-4 text-right font-bold text-slate-600 font-sans">
                                                                PPN ({{ (int)($order->tax_percent ?: 11) }}%):
                                                            </td>
                                                            <td class="py-2 px-4 text-right font-bold text-emerald-600">
                                                                + Rp {{ number_format($order->tax_amount, 0, ',', '.') }}
                                                            </td>
                                                        </tr>
                                                    @endif
                                                    @if(($order->pph_amount ?? 0) > 0)
                                                        <tr>
                                                            <td colspan="{{ ($order->order_type === 'rental_order' || $order->quotation_type === 'rental' || $order->quotation_type === 'both') ? 8 : 7 }}" class="py-2 px-4 text-right font-bold text-slate-600 font-sans">
                                                                PPh 23 ({{ (int)($order->pph_percent ?: 2) }}% Potongan):
                                                            </td>
                                                            <td class="py-2 px-4 text-right font-bold text-rose-600 italic">
                                                                - Rp {{ number_format($order->pph_amount, 0, ',', '.') }}
                                                            </td>
                                                        </tr>
                                                    @endif
                                                    <tr class="bg-slate-100/90 font-black text-slate-900">
                                                        <td colspan="{{ ($order->order_type === 'rental_order' || $order->quotation_type === 'rental' || $order->quotation_type === 'both') ? 8 : 7 }}" class="py-3 px-4 text-right font-black font-sans uppercase text-[11px] text-slate-700">
                                                            Grand Total Transaksi:
                                                        </td>
                                                        <td class="py-3 px-4 text-right text-sm text-blue-700">
                                                            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                                        </td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $typeFilter === 'quotation' ? 10 : 17 }}" class="text-center py-12 text-slate-400">
                                    <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    @if(in_array($typeFilter, ['transaksi', 'order', 'rental_order', 'sales_order']))
                                        <p class="font-black text-slate-800 text-sm">Belum Ada Transaksi</p>
                                        <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto">Tidak ada transaksi tanpa penawaran harga. Semua transaksi wajib diawali dari pembuatan Penawaran Harga terlebih dahulu.</p>
                                        <button type="button" @click="modalQuotationOpen = true" class="mt-4 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-xs transition inline-flex items-center space-x-1.5">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                                            <span>Buat Penawaran Harga Baru</span>
                                        </button>
                                    @else
                                        <p class="font-bold text-slate-600" x-text="lang === 'zh' ? '此分类下暂无交易记录。' : 'Belum ada data pada kategori ini.'">Belum ada data pada kategori ini.</p>
                                        <button type="button" @click="modalQuotationOpen = true" class="mt-3 text-blue-600 font-extrabold hover:underline">
                                            <span x-text="lang === 'zh' ? '+ 创建首张报价单' : '+ Buat Penawaran Harga Pertama'">+ Buat Penawaran Harga Pertama</span>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- MOBILE CARD VIEW (VISIBLE ON MOBILE < MD, HIDDEN ON DESKTOP) -->
            <div class="block md:hidden p-2.5 sm:p-3.5 space-y-3 sm:space-y-3.5 bg-[#F5F7FB] border-b border-[#E5EAF2]">
                @include('erp.partials.orders-mobile-cards')
            </div>

            <!-- Pagination Bar (SHARED FOR BOTH DESKTOP & MOBILE) -->
            {{ $orders->links('erp.pagination') }}
        </div>

    <!-- ========================================================================= -->
    <!-- 5. SLIDE-UP DETAIL DRAWER                                                 -->
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
            <!-- Drawer Header -->
            <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-2xl bg-amber-400 text-slate-950 flex items-center justify-center font-black"
                         :class="{
                            'bg-amber-400 text-slate-950': selectedOrder?.order_type === 'quotation',
                            'bg-blue-600 text-white': selectedOrder?.order_type === 'rental_order',
                            'bg-purple-600 text-white': selectedOrder?.order_type === 'sales_order'
                         }">
                        <span x-text="selectedOrder?.order_type === 'quotation' ? 'QTN' : (selectedOrder?.order_type === 'rental_order' ? 'RO' : 'SO')"></span>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900" x-text="selectedOrder?.order_number || 'Rincian Transaksi'"></h3>
                        <p class="text-xs text-slate-500" x-text="'Pelanggan: ' + (selectedOrder?.contact?.name || '-')"></p>
                    </div>
                </div>
                <button @click="drawerOpen = false" class="text-slate-400 hover:text-slate-700 p-1 rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Drawer Content -->
            <div class="p-6 overflow-y-auto space-y-5 text-xs flex-1">
                <!-- Info Grid -->
                <div class="grid grid-cols-2 gap-3 bg-slate-50 p-4 rounded-2xl border border-slate-200">
                    <div>
                        <span class="text-slate-400 font-bold block mb-0.5" x-text="lang === 'zh' ? '单据类型' : 'Tipe Dokumen'">Tipe Dokumen</span>
                        <span class="font-bold text-slate-900 uppercase" x-text="selectedOrder?.order_type === 'quotation' ? 'Penawaran Harga' : selectedOrder?.order_type"></span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-bold block mb-0.5" x-text="lang === 'zh' ? '订单状态' : 'Status Order'">Status Order</span>
                        <span class="font-bold text-slate-900 capitalize" x-text="selectedOrder?.status"></span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-bold block mb-0.5" x-text="lang === 'zh' ? '单据日期' : 'Tanggal Dokumen'">Tanggal Dokumen</span>
                        <span class="font-medium text-slate-800" x-text="selectedOrder?.order_date"></span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-bold block mb-0.5" x-text="lang === 'zh' ? '有效期 / 租期' : 'Masa Berlaku / Durasi'">Masa Berlaku / Durasi</span>
                        <span class="font-medium text-slate-800" x-text="selectedOrder?.valid_until || (selectedOrder?.rental_duration ? selectedOrder.rental_duration + ' Bulan' : '-')"></span>
                    </div>
                    <div x-show="selectedOrder?.parent" class="col-span-2 pt-2 border-t border-slate-200">
                        <span class="text-slate-400 font-bold block mb-1" x-text="lang === 'zh' ? '关联源报价单号 (Penawaran Asli):' : 'Berasal dari No. Penawaran Harga:'">Berasal dari No. Penawaran Harga:</span>
                        <div class="inline-flex items-center space-x-1.5 font-mono font-bold text-amber-900 bg-amber-100/90 hover:bg-amber-200 px-2.5 py-1 rounded-lg border border-amber-300 transition shadow-xs">
                            <svg class="w-3.5 h-3.5 text-amber-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <a :href="'/erp/orders?type=quotation&search=' + encodeURIComponent(selectedOrder?.parent?.order_number || '')" 
                               class="hover:underline text-amber-950 font-bold" 
                               title="Buka Tab Penawaran" 
                               x-text="selectedOrder?.parent?.order_number"></a>
                            <a :href="'/erp/quotations/' + (selectedOrder?.parent?.id || selectedOrder?.parent_id) + '/pdf'" 
                               target="_blank" 
                               class="p-0.5 rounded text-amber-800 hover:text-amber-950 hover:bg-amber-300/60 transition shrink-0 ml-1"
                               title="Cetak PDF Penawaran">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            </a>
                        </div>
                    </div>
                    <template x-if="selectedOrder?.invoices && selectedOrder.invoices.length > 0">
                        <div class="col-span-2 pt-2 border-t border-slate-200">
                            <span class="text-slate-400 font-bold block mb-1" x-text="lang === 'zh' ? '已开具发票号 (Invoice):' : 'No. Invoice Diterbitkan:'">No. Invoice Diterbitkan:</span>
                            <div class="inline-flex items-center space-x-1.5 font-mono font-bold text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-2.5 py-1 rounded-lg border border-indigo-300 transition shadow-xs">
                                <svg class="w-3.5 h-3.5 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <a :href="'/erp/finance?tab=invoices&search=' + encodeURIComponent(selectedOrder.invoices[0].invoice_number || '')" 
                                   class="hover:underline text-indigo-950 font-bold" 
                                   title="Buka di Tab Invoices Keuangan" 
                                   x-text="selectedOrder.invoices[0].invoice_number"></a>
                                <a :href="'/erp/invoices/' + selectedOrder.invoices[0].id + '/pdf'" 
                                   target="_blank" 
                                   class="p-0.5 rounded text-indigo-800 hover:text-indigo-950 hover:bg-indigo-200 transition shrink-0 ml-1"
                                   title="Cetak PDF Invoice">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                </a>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Deliveries / Surat Jalan Info with Separated Fields -->
                <template x-if="selectedOrder?.order_type === 'rental_order' || selectedOrder?.order_type === 'sales_order'">
                    <div class="bg-emerald-50/70 p-4 rounded-2xl border border-emerald-200 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="font-black text-emerald-900 flex items-center space-x-1.5">
                                <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h2"></path></svg>
                                <span x-text="lang === 'zh' ? '送货单 / Delivery Order (DO)' : 'Surat Jalan / Delivery Order (DO)'">Surat Jalan / Delivery Order (DO)</span>
                            </span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black"
                                  :class="selectedOrder?.deliveries?.length > 0 ? 'bg-emerald-200 text-emerald-900' : 'bg-amber-100 text-amber-800'"
                                  x-text="selectedOrder?.deliveries?.length > 0 ? (lang === 'zh' ? selectedOrder.deliveries.length + ' 份送货单' : selectedOrder.deliveries.length + ' DO Diterbitkan') : (lang === 'zh' ? '暂无送货单' : 'Belum Ada DO')">
                            </span>
                        </div>

                        <template x-if="selectedOrder?.deliveries && selectedOrder.deliveries.length > 0">
                            <div class="space-y-2 pt-1">
                                <template x-for="del in selectedOrder.deliveries" :key="del.id">
                                    <div class="bg-white p-3 rounded-xl border border-emerald-200/80 space-y-1.5">
                                        <div class="flex items-center justify-between">
                                            <span class="font-black text-slate-900 font-mono" x-text="del.delivery_number"></span>
                                            <a :href="'/erp/deliveries/' + del.id + '/pdf'" 
                                               target="_blank" 
                                               class="px-2 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] transition shadow-xs"
                                               x-text="lang === 'zh' ? '打印送货单' : 'Cetak PDF DO'">
                                                Cetak PDF DO
                                            </a>
                                        </div>
                                        <div class="grid grid-cols-2 gap-2 text-[11px] text-slate-600 pt-1 border-t border-slate-100">
                                            <p><strong x-text="lang === 'zh' ? '发货日期:' : 'Tgl Kirim:'">Tgl Kirim:</strong> <span x-text="del.delivery_date || '-'"></span></p>
                                            <p><strong x-text="lang === 'zh' ? '车牌号:' : 'No. Plat:'">No. Plat:</strong> <span class="font-mono font-bold" x-text="del.vehicle_number || '-'"></span></p>
                                            <p><strong x-text="lang === 'zh' ? '收货人:' : 'PIC Penerima:'">PIC Penerima:</strong> <span x-text="del.recipient_name || '-'"></span></p>
                                            <p><strong x-text="lang === 'zh' ? '联系电话:' : 'No. Telp PIC:'">No. Telp PIC:</strong> <span x-text="del.recipient_phone || '-'"></span></p>
                                            <p class="col-span-2"><strong x-text="lang === 'zh' ? '司机 (车队):' : 'Driver (Vendor):'">Driver (Vendor):</strong> <span x-text="del.driver_name || '-'"></span></p>
                                            <p class="col-span-2"><strong x-text="lang === 'zh' ? '备注:' : 'Remaks:'">Remaks:</strong> <span x-text="del.notes || '-'"></span></p>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </template>

                <!-- Unit Lines List -->
                <div>
                    <h4 class="font-extrabold text-slate-900 mb-2" x-text="lang === 'zh' ? '设备及重型机械明细' : 'Rincian Unit & Alat Berat'">Rincian Unit & Alat Berat</h4>
                    <div class="space-y-2">
                        <template x-for="(item, i) in selectedOrder?.items || []" :key="i">
                            <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200 flex items-center justify-between">
                                <div class="min-w-0 pr-3">
                                    <p class="font-bold text-slate-900 truncate" x-text="item.item?.name || item.description"></p>
                                    <p class="text-slate-500 text-[11px]" x-text="'Qty: ' + item.quantity + ' | ' + (item.duration ? 'Durasi: ' + item.duration + ' ' + (item.duration_unit || 'Bulan') : 'Satuan: ' + (item.unit || 'Unit'))"></p>
                                </div>
                                <span class="font-black text-slate-900 font-mono shrink-0" x-text="'Rp ' + Number(item.line_total).toLocaleString('id-ID')"></span>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Vendor & Sub-Rental Margin Breakdown -->
                <template x-if="selectedOrder?.order_type === 'rental_order' || selectedOrder?.order_type === 'sales_order'">
                    <div class="bg-purple-50/70 p-4 rounded-2xl border border-purple-200 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="font-black text-purple-950 flex items-center space-x-1.5">
                                <svg class="w-4 h-4 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span x-text="lang === 'zh' ? '供应商成本与交易毛利' : 'Biaya Vendor & Margin Laba'">Biaya Vendor & Margin Laba</span>
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-purple-200 text-purple-900"
                                  x-text="'Margin: ' + (selectedOrder?.gross_margin_percent || 0) + '%'">
                            </span>
                        </div>
                        <div class="grid grid-cols-3 gap-2 text-center pt-2 border-t border-purple-200/80">
                            <div>
                                <span class="text-slate-500 block text-[10px]" x-text="lang === 'zh' ? '营收' : 'Omset'">Omset</span>
                                <span class="font-mono font-bold text-slate-900" x-text="'Rp ' + Number(selectedOrder?.subtotal || 0).toLocaleString('id-ID')"></span>
                            </div>
                            <div>
                                <span class="text-rose-600 block text-[10px]" x-text="lang === 'zh' ? '供应商成本' : 'Biaya Vendor'">Biaya Vendor</span>
                                <span class="font-mono font-bold text-rose-600" x-text="'Rp ' + Number(selectedOrder?.vendor_cost || 0).toLocaleString('id-ID')"></span>
                            </div>
                            <div>
                                <span class="text-emerald-700 block text-[10px]" x-text="lang === 'zh' ? '毛利润' : 'Laba Kotor'">Laba Kotor</span>
                                <span class="font-mono font-bold text-emerald-700" x-text="'Rp ' + Number(selectedOrder?.gross_profit || 0).toLocaleString('id-ID')"></span>
                            </div>
                        </div>
                        <div class="pt-2 text-right">
                            <button type="button" @click="openAllocateModal(selectedOrder)" class="text-[11px] font-extrabold text-purple-700 hover:underline">
                                + <span x-text="lang === 'zh' ? '创建供应商采购单 / 转租' : 'Buat PO Vendor / Atur HPP'">Buat PO Vendor / Atur HPP</span>
                            </button>
                        </div>
                    </div>
                </template>

                <!-- Financial Calculation -->
                <div class="bg-amber-50/60 p-4 rounded-2xl border border-amber-200 space-y-1.5 font-medium">
                    <div class="flex justify-between text-slate-600">
                        <span x-text="lang === 'zh' ? '小计:' : 'Subtotal:'">Subtotal:</span>
                        <span class="font-mono text-slate-900 font-bold" x-text="'Rp ' + Number(selectedOrder?.subtotal || 0).toLocaleString('id-ID')"></span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span x-text="lang === 'zh' ? '增值税 (PPN 11%):' : 'PPN 11%:'">PPN 11%:</span>
                        <span class="font-mono text-slate-900 font-bold" x-text="'Rp ' + Number(selectedOrder?.tax_amount || 0).toLocaleString('id-ID')"></span>
                    </div>
                    <div class="flex justify-between text-sm font-black text-slate-900 pt-2 border-t border-amber-200">
                        <span x-text="lang === 'zh' ? '总计金额:' : 'Total Nilai:'">Total Nilai:</span>
                        <span class="font-mono text-amber-900 font-black" x-text="'Rp ' + Number(selectedOrder?.total_amount || 0).toLocaleString('id-ID')"></span>
                    </div>
                </div>
            </div>

            <!-- Drawer Footer Actions -->
            <div class="p-5 border-t border-slate-100 bg-slate-50 space-y-2" style="padding-bottom: max(env(safe-area-inset-bottom), 1.25rem);">
                <!-- Cetak PDF Dokumen Kontrak / Penawaran -->
                <a :href="'/erp/quotations/' + selectedOrder?.id + '/pdf'" 
                   target="_blank" 
                   class="w-full bg-amber-400 hover:bg-yellow-300 text-slate-950 font-black py-2.5 rounded-xl shadow transition text-center flex items-center justify-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    <span x-text="lang === 'zh' ? '打印正式 PDF 文件' : 'Cetak PDF Dokumen Resmi'">Cetak PDF Dokumen Resmi</span>
                </a>

                <!-- GENERATE KE TRANSAKSI (FOR QUOTATION) -->
                <template x-if="selectedOrder?.order_type === 'quotation'">
                    <div class="space-y-2 pt-1">
                        <template x-if="selectedOrder?.status === 'cancelled'">
                            <div class="p-2.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-center font-bold text-xs">
                                Penawaran harga ini telah dibatalkan (Cancel).
                            </div>
                        </template>
                        <button type="button" 
                                :disabled="selectedOrder?.status === 'cancelled'"
                                :class="selectedOrder?.status === 'cancelled' ? 'opacity-40 cursor-not-allowed pointer-events-none' : ''"
                                @click="generateTransaction(selectedOrder.id, selectedOrder.order_number)" 
                                class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-black rounded-xl shadow transition flex items-center justify-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            <span x-text="lang === 'zh' ? '生成交易订单 (Transaksi)' : 'Generate ke Transaksi'">Generate ke Transaksi</span>
                        </button>
                    </div>
                </template>

                <!-- GENERATE DO WITH MODAL (FOR RO & SO) -->
                <template x-if="selectedOrder?.order_type === 'rental_order' || selectedOrder?.order_type === 'sales_order'">
                    <div class="space-y-2 pt-1">
                        <template x-if="selectedOrder?.status === 'cancelled'">
                            <div class="p-2.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-center font-bold text-xs">
                                Transaksi ini telah dibatalkan (Cancel). Semua tindakan dinonaktifkan.
                            </div>
                        </template>
                        <button type="button" 
                                :disabled="selectedOrder?.status === 'cancelled'"
                                :class="selectedOrder?.status === 'cancelled' ? 'opacity-40 cursor-not-allowed pointer-events-none' : ''"
                                @click="openDoModal(selectedOrder)" 
                                class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-black rounded-xl shadow transition flex items-center justify-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h2"></path></svg>
                            <span x-text="lang === 'zh' ? (selectedOrder?.deliveries?.length > 0 ? '+ 开具新送货单 (DO)' : '🚚 开具送货单 (DO)') : (selectedOrder?.deliveries?.length > 0 ? '+ Terbitkan Surat Jalan Baru (DO)' : '🚚 Terbitkan Surat Jalan (DO)')"></span>
                        </button>

                        <!-- Cancel Transaksi Button in Drawer -->
                        <template x-if="selectedOrder?.status !== 'cancelled'">
                            <button type="button" 
                                    @click="openCancelModal(selectedOrder)" 
                                    class="w-full py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold rounded-xl border border-rose-200 transition flex items-center justify-center space-x-2 text-xs">
                                <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                <span x-text="lang === 'zh' ? '取消此交易订单' : 'Cancel Transaksi'">Cancel Transaksi</span>
                            </button>
                        </template>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 6. MODAL TERBITKAN SURAT JALAN (REVISION 2: DRIVER MASTER DATA VENDOR)   -->
    <!-- ========================================================================= -->
    @include('erp.partials.delivery-modal')

    <!-- ========================================================================= -->
    <!-- 7. MODAL ALOKASI VENDOR / SUB-RENT (HPP & REAL-TIME MARGIN TRACKING)     -->
    <!-- ========================================================================= -->
    @include('erp.partials.vendor-allocation-modal')

    <!-- ========================================================================= -->
    <!-- 8. MODAL GENERATE INVOICE (REVISION 3)                                    -->
    <!-- ========================================================================= -->
    @include('erp.partials.generate-invoice-modal')

    <!-- ========================================================================= -->
    <!-- 9. MODAL CANCEL TRANSAKSI                                                 -->
    <!-- ========================================================================= -->
    @include('erp.partials.cancel-order-modal')

</div>

@push('scripts')
<script>
    function erpOrdersManager() {
        return {
            searchQuery: '{{ addslashes($search ?? "") }}',
            statusFilter: '{{ addslashes($statusFilter ?? "all") }}',
            mobileFilterOpen: false,
            drawerOpen: false,
            selectedOrder: null,
            activeActionMenuId: null,
            _searchTimer: null,

            toggleActionMenu(orderId) {
                this.activeActionMenuId = (this.activeActionMenuId === orderId ? null : orderId);
            },
            closeActionMenu() {
                this.activeActionMenuId = null;
            },

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

            // Expand/Collapse Product Details (Requirement 1)
            expandedOrders: [],
            toggleExpand(orderId) {
                const idx = this.expandedOrders.indexOf(orderId);
                if (idx > -1) {
                    this.expandedOrders.splice(idx, 1);
                } else {
                    this.expandedOrders.push(orderId);
                }
            },
            isExpanded(orderId) {
                return this.expandedOrders.includes(orderId);
            },

            // DO Modal State (Revision 2)
            modalDoOpen: false,
            submittingDo: false,
            doForm: {
                order_id: null,
                order_number: '',
                customer_name: '',
                delivery_date: '',
                recipient_name: '',
                recipient_phone: '',
                vendor_name: '',
                driver_name: '',
                vehicle_number: '',
                notes: ''
            },

            // Vendor Allocation Modal State (Revised: Multi-Product Section, Header PO Date Only)
            modalAllocateOpen: false,
            submittingAllocate: false,
            allocateForm: {
                order_id: null,
                order_number: '',
                customer_name: '',
                order_revenue: 0,
                po_date: '',
                existing_allocations: [],
                products: []
            },
            calculatedVendorCost: 0,
            calculatedGrossProfit: 0,
            calculatedMarginPercent: 0,

            openAllocateModal(order) {
                if (!order) return;
                this.allocateForm.order_id = order.id;
                this.allocateForm.order_number = order.order_number;
                this.allocateForm.customer_name = order.contact ? order.contact.name : '-';
                this.allocateForm.order_revenue = Number(order.subtotal || order.total_amount || 0);

                // Tanggal PO default: today
                const today = new Date().toISOString().split('T')[0];
                this.allocateForm.po_date = today;

                const existingPOs = order.vendor_orders || [];
                this.allocateForm.existing_allocations = existingPOs;

                const defaultProcType = (order.order_type === 'rental_order') ? 'sub_rental' : 'purchase_unit';
                const defaultStartDate = order.rental_start_date ? String(order.rental_start_date).split('T')[0] : today;
                const defaultDuration = order.rental_duration || 1;
                const defaultDurationUnit = order.rental_duration_unit || 'month';

                const rawItems = order.items || [];
                this.allocateForm.products = rawItems.map((it, idx) => {
                    let matchedVendorId = it.vendor_id || '';
                    const descLower = (it.description || (it.item ? it.item.name : '')).toLowerCase();
                    const isBeli = (it.transaction_type === 'sales') || descLower.includes('beli') || descLower.includes('jual') || descLower.includes('purchase');
                    let matchedProcType = isBeli ? 'purchase_unit' : ((it.transaction_type === 'rental' || defaultProcType === 'sub_rental') ? 'sub_rental' : 'purchase_unit');
                    let matchedStartDate = defaultStartDate;
                    let matchedDuration = it.duration || defaultDuration;
                    let matchedDurationUnit = it.duration_unit || defaultDurationUnit;
                    let matchedCost = Number(it.cost_price || 0);
                    let matchedPpn = false;
                    let matchedShipping = 0;
                    let matchedPaymentTerm = 'Net 30 Days';
                    let matchedNotes = it.notes || '';

                    // Match with existing vendor orders if any
                    if (existingPOs.length > 0) {
                        for (const po of existingPOs) {
                            if (po.items && po.items.length > 0) {
                                const foundPoi = po.items.find(poi => 
                                    (poi.order_item_id && poi.order_item_id == it.id) || 
                                    (poi.description === (it.description || (it.item ? it.item.name : ''))) || 
                                    (poi.item_id && poi.item_id === it.item_id)
                                );
                                if (foundPoi) {
                                    matchedVendorId = po.contact_id || foundPoi.vendor_id || '';
                                    matchedCost = Number(foundPoi.unit_price || foundPoi.cost_price || 0);
                                    matchedProcType = po.procurement_type || matchedProcType;
                                    matchedStartDate = po.rental_start_date ? String(po.rental_start_date).split('T')[0] : matchedStartDate;
                                    matchedDuration = po.rental_duration || matchedDuration;
                                    matchedDurationUnit = po.rental_duration_unit || matchedDurationUnit;
                                    matchedPpn = Number(po.tax_percent || 0) > 0;
                                    matchedShipping = Number(po.shipping_fee || 0);
                                    matchedPaymentTerm = po.payment_term || 'Net 30 Days';
                                    matchedNotes = foundPoi.notes || po.notes || '';
                                    break;
                                }
                            } else if (existingPOs.length === 1 && rawItems.length === 1) {
                                matchedVendorId = po.contact_id || '';
                                matchedCost = Number(po.subtotal || 0) / (Number(it.quantity || 1) * (matchedProcType === 'sub_rental' ? matchedDuration : 1));
                                matchedProcType = po.procurement_type || matchedProcType;
                                matchedStartDate = po.rental_start_date ? String(po.rental_start_date).split('T')[0] : matchedStartDate;
                                matchedDuration = po.rental_duration || matchedDuration;
                                matchedDurationUnit = po.rental_duration_unit || matchedDurationUnit;
                                matchedPpn = Number(po.tax_percent || 0) > 0;
                                matchedShipping = Number(po.shipping_fee || 0);
                                matchedPaymentTerm = po.payment_term || 'Net 30 Days';
                                matchedNotes = po.notes || '';
                            }
                        }
                    }

                    return {
                        order_item_id: it.id,
                        asset_id: it.asset_id || '',
                        item_name: it.description || (it.item ? it.item.name : ('Produk ' + (idx + 1))),
                        quantity: Number(it.quantity || 1),
                        unit: it.unit || 'Unit',
                        line_total: Number(it.line_total || 0),
                        procurement_type: matchedProcType,
                        rental_start_date: matchedStartDate,
                        duration: matchedDuration,
                        duration_unit: matchedDurationUnit,
                        vendor_id: matchedVendorId,
                        unit_cost: matchedCost,
                        include_ppn: matchedPpn,
                        shipping_fee: matchedShipping,
                        payment_term: matchedPaymentTerm,
                        notes: matchedNotes
                    };
                });

                this.recalcAllocateMargin();
                this.modalAllocateOpen = true;
            },

            calcProductVendorCost(prod) {
                const qty = Number(prod.quantity || 0);
                const cost = Number(prod.unit_cost || 0);
                const dur = (prod.procurement_type === 'sub_rental') ? Math.max(1, Number(prod.duration || 1)) : 1;
                const subtotal = qty * cost * dur;
                const ppn = prod.include_ppn ? (subtotal * 0.11) : 0;
                const shipping = Number(prod.shipping_fee || 0);
                return {
                    subtotal: subtotal,
                    ppn: ppn,
                    shipping: shipping,
                    total: subtotal + ppn + shipping
                };
            },

            recalcAllocateMargin() {
                let totalVendorCost = 0;
                let totalLogistics = 0;

                (this.allocateForm.products || []).forEach(prod => {
                    if (prod.vendor_id || prod.unit_cost > 0) {
                        const costInfo = this.calcProductVendorCost(prod);
                        totalVendorCost += (costInfo.subtotal + costInfo.ppn);
                        totalLogistics += costInfo.shipping;
                    }
                });

                this.calculatedVendorCost = totalVendorCost + totalLogistics;
                const revenue = Number(this.allocateForm.order_revenue || 0);
                this.calculatedGrossProfit = revenue - this.calculatedVendorCost;
                this.calculatedMarginPercent = (revenue > 0) ? Math.round((this.calculatedGrossProfit / revenue) * 100) : 0;
            },

            async submitAllocateVendor() {
                if (!this.allocateForm.po_date) {
                    alert('Mohon isi Tanggal PO!');
                    return;
                }

                // Check at least one product has a vendor or an internal fleet asset assigned
                const hasAllocation = (this.allocateForm.products || []).some(p => p.vendor_id || p.asset_id);
                if (!hasAllocation) {
                    alert('Mohon pilih minimal satu Vendor Mitra atau Armada Internal untuk produk yang dialokasikan!');
                    return;
                }

                // Validate sewa products with vendor
                for (let i = 0; i < this.allocateForm.products.length; i++) {
                    const prod = this.allocateForm.products[i];
                    if (prod.vendor_id) {
                        if (prod.procurement_type === 'sub_rental' && !prod.rental_start_date) {
                            alert(`Mohon lengkapi Tanggal Mulai Sewa untuk produk "${prod.item_name}"!`);
                            return;
                        }
                    }
                }

                this.submittingAllocate = true;
                try {
                    const res = await fetch('/erp/procurement/allocate', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': CSRF_TOKEN
                        },
                        body: JSON.stringify({
                            order_id: this.allocateForm.order_id,
                            po_date: this.allocateForm.po_date,
                            products: this.allocateForm.products
                        })
                    });
                    const result = await res.json();
                    if (result.success) {
                        this.modalAllocateOpen = false;
                        alert(result.message);
                        window.location.reload();
                    } else {
                        alert('Gagal: ' + (result.message || 'Terjadi kesalahan'));
                    }
                } catch (e) {
                    alert('Gagal menghubungi server.');
                } finally {
                    this.submittingAllocate = false;
                }
            },

            matchesRow(haystack, status, computedQuotationStatus = '') {
                const isCancelled = (status === 'cancelled' || computedQuotationStatus === 'cancel');
                const hasSearch = !!(this.searchQuery && this.searchQuery.trim() !== '');

                // Secara default, data status batal/cancel tidak tampil di tabel
                // KECUALI filter statusnya diarahkan ke cancel/cancelled ATAU ketika user melakukan search
                if (isCancelled) {
                    const isFilteringCancel = (this.statusFilter === 'cancel' || this.statusFilter === 'cancelled');
                    if (!isFilteringCancel && !hasSearch) {
                        return false;
                    }
                }

                const matchesSearch = !hasSearch || haystack.includes(this.searchQuery.toLowerCase());
                let matchesStatus = (this.statusFilter === 'all');
                if (!matchesStatus) {
                    if (computedQuotationStatus) {
                        matchesStatus = (computedQuotationStatus === this.statusFilter);
                    } else {
                        if (this.statusFilter === 'in_progress') {
                            matchesStatus = (status !== 'completed' && status !== 'cancelled');
                        } else if (this.statusFilter === 'completed') {
                            matchesStatus = (status === 'completed');
                        } else if (this.statusFilter === 'cancelled') {
                            matchesStatus = (status === 'cancelled');
                        } else {
                            matchesStatus = (status === this.statusFilter);
                        }
                    }
                }
                return matchesSearch && matchesStatus;
            },

            async completeTransaction(orderId, orderNumber) {
                const confirmed = await window.confirmDialog({
                    title: 'Konfirmasi Selesaikan Transaksi',
                    message: `Apakah Anda yakin transaksi ${orderNumber} telah selesai?\n\nStatus transaksi akan diperbarui menjadi Selesai.`,
                    confirmText: 'Ya, Tandai Selesai',
                    cancelText: 'Batal',
                    type: 'success'
                });
                if (!confirmed) {
                    return;
                }
                try {
                    const res = await fetch('/erp/orders/' + orderId + '/complete', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': CSRF_TOKEN
                        }
                    });
                    const result = await res.json();
                    if (result.success) {
                        window.showToast(result.message, 'success');
                        setTimeout(() => window.location.reload(), 800);
                    } else {
                        window.showToast('Gagal: ' + (result.message || 'Terjadi kesalahan'), 'error');
                    }
                } catch (e) {
                    window.showToast('Gagal menghubungi server.', 'error');
                }
            },

            async openDrawer(orderId) {
                this.drawerOpen = true;
                try {
                    const res = await fetch('/erp/orders/' + orderId + '/drawer');
                    const data = await res.json();
                    if (data.success) {
                        this.selectedOrder = data.order;
                    }
                } catch (e) {
                    console.error('Failed to load drawer:', e);
                }
            },

            // REVISION 2: Open DO Modal with separated vendor and optional fields
            openDoModal(order) {
                if (!order) return;
                this.doForm.order_id = order.id;
                this.doForm.order_number = order.order_number;
                this.doForm.customer_name = order.contact ? order.contact.name : '-';
                this.doForm.delivery_date = new Date().toISOString().split('T')[0];
                this.doForm.recipient_name = '';
                this.doForm.recipient_phone = '';
                this.doForm.vendor_name = '';
                this.doForm.driver_name = '';
                this.doForm.vehicle_number = '';
                this.doForm.notes = 'Unit siap operasi, kondisi baik dan lengkap sesuai kontrak.';
                this.modalDoOpen = true;
            },

            onSelectVendor(event) {
                const sel = event.target;
                const opt = sel.options[sel.selectedIndex];
                if (sel.value && sel.value !== '__custom__') {
                    this.doForm.vendor_name = sel.value;
                    const driver = opt.getAttribute('data-driver') || '';
                    if (driver && !this.doForm.driver_name) {
                        this.doForm.driver_name = driver;
                    }
                } else if (sel.value === '__custom__') {
                    this.doForm.vendor_name = '';
                }
            },

            async submitGenerateDo() {
                if (!this.doForm.recipient_name) {
                    alert('Mohon isi Nama PIC Penerima!');
                    return;
                }
                if (!this.doForm.delivery_date) {
                    alert('Mohon pilih Tanggal Pengiriman!');
                    return;
                }

                this.submittingDo = true;
                try {
                    const res = await fetch('/erp/orders/' + this.doForm.order_id + '/generate-do', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': CSRF_TOKEN
                        },
                        body: JSON.stringify({
                            delivery_date: this.doForm.delivery_date,
                            recipient_name: this.doForm.recipient_name,
                            recipient_phone: this.doForm.recipient_phone,
                            vendor_name: this.doForm.vendor_name,
                            driver_name: this.doForm.driver_name,
                            vehicle_number: this.doForm.vehicle_number,
                            notes: this.doForm.notes
                        })
                    });
                    const result = await res.json();
                    if (result.success) {
                        this.modalDoOpen = false;
                        alert(result.message);
                        if (result.pdf_url) {
                            window.open(result.pdf_url, '_blank');
                        }
                        window.location.reload();
                    } else {
                        alert('Gagal: ' + (result.message || 'Terjadi kesalahan'));
                    }
                } catch (e) {
                    alert('Gagal menghubungi server.');
                } finally {
                    this.submittingDo = false;
                }
            },

            // Convert quotation to transaction (single unified button)
            async generateTransaction(orderId, orderNumber) {
                const confirmed = await window.confirmDialog({
                    title: 'Konversi Penawaran ke Transaksi',
                    message: `Konversi Penawaran Harga ${orderNumber || ''} menjadi Transaksi?\n\nSetelah berhasil, sistem akan otomatis membuka tab Transaksi.`,
                    confirmText: 'Ya, Generate Transaksi',
                    cancelText: 'Batal',
                    type: 'info'
                });
                if (!confirmed) {
                    return;
                }

                try {
                    const res = await fetch('/erp/orders/' + orderId + '/convert', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': CSRF_TOKEN
                        },
                        body: JSON.stringify({})
                    });
                    const result = await res.json();
                    if (result.success) {
                        window.showToast(result.message, 'success');
                        setTimeout(() => {
                            window.location.href = result.redirect_url || '/erp/orders?type=transaksi';
                        }, 800);
                    } else {
                        window.showToast('Gagal: ' + (result.message || 'Terjadi kesalahan'), 'error');
                    }
                } catch (e) {
                    window.showToast('Gagal menghubungi server.', 'error');
                }
            },

            // Alias for backward compatibility
            generateOrder(orderId, quotationType, orderNumber) {
                return this.generateTransaction(orderId, orderNumber);
            },

            // ==================== CANCEL TRANSACTION FEATURE ====================
            modalCancelOpen: false,
            submittingCancel: false,
            cancelForm: {
                order_id: null,
                order_number: '',
                reason: ''
            },

            openCancelModal(order) {
                if (!order) return;
                this.cancelForm.order_id = order.id;
                this.cancelForm.order_number = order.order_number || '';
                this.cancelForm.reason = '';
                this.modalCancelOpen = true;
            },

            async submitCancelOrder() {
                if (!this.cancelForm.reason || !this.cancelForm.reason.trim()) {
                    alert('Mohon isi alasan pembatalan transaksi!');
                    return;
                }

                this.submittingCancel = true;
                try {
                    const res = await fetch('/erp/orders/' + this.cancelForm.order_id + '/cancel', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': CSRF_TOKEN
                        },
                        body: JSON.stringify({
                            cancel_reason: this.cancelForm.reason.trim()
                        })
                    });
                    const result = await res.json();
                    if (result.success) {
                        this.modalCancelOpen = false;
                        alert(result.message);
                        window.location.reload();
                    } else {
                        alert('Gagal: ' + (result.message || 'Terjadi kesalahan'));
                    }
                } catch (e) {
                    alert('Gagal menghubungi server.');
                } finally {
                    this.submittingCancel = false;
                }
            },

            // ==================== INVOICE GENERATION (REVISION 3 & 4) ====================
            modalGenerateInvOpen: false,
            submittingInvoice: false,
            taxInvoiceFile: null,
            taxInvoiceFileName: '',
            calculatedInvoicePpn: 0,
            calculatedInvoicePph: 0,
            calculatedInvoiceGrandTotal: 0,
            invoiceForm: {
                order_id: null,
                order_number: '',
                customer_name: '',
                invoice_number: '',
                issue_date: '',
                due_date: '',
                subtotal: 0,
                shipping_delivery_fee: 0,
                shipping_return_fee: 0,
                shipping_fee: 0,
                include_ppn: false,
                include_pph: false,
                items: [],
                notes: ''
            },

            openGenerateInvoiceModal(order) {
                if (!order) return;
                this.invoiceForm.order_id = order.id;
                this.invoiceForm.order_number = order.order_number;
                this.invoiceForm.customer_name = order.contact ? order.contact.name : '-';
                this.invoiceForm.items = order.items || [];

                let sub = Number(order.subtotal || 0);
                if (!sub && this.invoiceForm.items.length > 0) {
                    sub = this.invoiceForm.items.reduce((acc, it) => acc + (Number(it.quantity || 1) * Number(it.unit_price || 0)), 0);
                }
                this.invoiceForm.subtotal = sub;
                this.invoiceForm.shipping_delivery_fee = Number(order.shipping_delivery_fee || 0);
                this.invoiceForm.shipping_return_fee = Number(order.shipping_return_fee || 0);
                if (!this.invoiceForm.shipping_delivery_fee && !this.invoiceForm.shipping_return_fee && Number(order.shipping_fee || 0) > 0) {
                    this.invoiceForm.shipping_delivery_fee = Number(order.shipping_fee || 0);
                }
                this.invoiceForm.shipping_fee = this.invoiceForm.shipping_delivery_fee + this.invoiceForm.shipping_return_fee;

                const now = new Date();
                const romanMonths = ['I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'];
                const romanMonth = romanMonths[now.getMonth()] || 'X';
                const y = now.getFullYear();
                this.invoiceForm.invoice_number = String(order.id).padStart(3, '0') + '/INV/KLI/' + romanMonth + '/' + y;
                this.invoiceForm.issue_date = now.toISOString().split('T')[0];

                const due = new Date();
                due.setDate(due.getDate() + 30);
                this.invoiceForm.due_date = due.toISOString().split('T')[0];

                this.invoiceForm.include_ppn = (order.tax_amount && Number(order.tax_amount) > 0) || (order.tax_percent && Number(order.tax_percent) > 0);
                this.invoiceForm.include_pph = (order.pph_amount && Number(order.pph_amount) > 0) || (order.pph_percent && Number(order.pph_percent) > 0);
                this.invoiceForm.notes = 'Invoice tagihan atas pesanan ' + order.order_number;
                this.taxInvoiceFile = null;
                this.taxInvoiceFileName = '';

                this.recalcInvoiceFormTotals();
                this.modalGenerateInvOpen = true;
            },

            recalcInvoiceFormTotals() {
                const sub = Number(this.invoiceForm.subtotal || 0);
                const delFee = Number(this.invoiceForm.shipping_delivery_fee || 0);
                const retFee = Number(this.invoiceForm.shipping_return_fee || 0);
                this.invoiceForm.shipping_fee = delFee + retFee;
                const ship = this.invoiceForm.shipping_fee;
                this.calculatedInvoicePpn = this.invoiceForm.include_ppn ? Math.round(sub * 0.11) : 0;
                this.calculatedInvoicePph = this.invoiceForm.include_pph ? Math.round(sub * 0.02) : 0;
                this.calculatedInvoiceGrandTotal = sub + ship + this.calculatedInvoicePpn - this.calculatedInvoicePph;
            },

            onTaxInvoiceFileSelected(event) {
                const file = event.target.files[0];
                if (file) {
                    this.taxInvoiceFile = file;
                    this.taxInvoiceFileName = file.name;
                }
            },

            async submitGenerateInvoice() {
                if (!this.invoiceForm.issue_date || !this.invoiceForm.due_date) {
                    alert('Mohon lengkapi Tanggal Terbit dan Tanggal Jatuh Tempo!');
                    return;
                }

                this.submittingInvoice = true;
                try {
                    const fd = new FormData();
                    fd.append('invoice_number', this.invoiceForm.invoice_number);
                    fd.append('issue_date', this.invoiceForm.issue_date);
                    fd.append('due_date', this.invoiceForm.due_date);
                    fd.append('shipping_delivery_fee', this.invoiceForm.shipping_delivery_fee || 0);
                    fd.append('shipping_return_fee', this.invoiceForm.shipping_return_fee || 0);
                    fd.append('shipping_fee', (Number(this.invoiceForm.shipping_delivery_fee || 0) + Number(this.invoiceForm.shipping_return_fee || 0)));
                    fd.append('include_ppn', this.invoiceForm.include_ppn ? '1' : '0');
                    fd.append('tax_percent', this.invoiceForm.include_ppn ? '11' : '0');
                    fd.append('include_pph', this.invoiceForm.include_pph ? '1' : '0');
                    fd.append('pph_percent', this.invoiceForm.include_pph ? '2' : '0');
                    fd.append('notes', this.invoiceForm.notes || '');
                    if (this.taxInvoiceFile) {
                        fd.append('tax_invoice_document', this.taxInvoiceFile);
                    }

                    const res = await fetch('/erp/orders/' + this.invoiceForm.order_id + '/generate-invoice', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': CSRF_TOKEN
                        },
                        body: fd
                    });
                    const result = await res.json();
                    if (result.success) {
                        this.modalGenerateInvOpen = false;
                        alert(result.message);
                        if (result.pdf_url) {
                            window.open(result.pdf_url, '_blank');
                        }
                        window.location.reload();
                    } else {
                        alert('Gagal: ' + (result.message || 'Terjadi kesalahan'));
                    }
                } catch (e) {
                    alert('Gagal menghubungi server.');
                } finally {
                    this.submittingInvoice = false;
                }
            }
        };
    }
</script>
@endpush
@endsection
