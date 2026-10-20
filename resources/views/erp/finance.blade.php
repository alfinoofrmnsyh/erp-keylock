@extends('layouts.erp')

@section('title', 'Finance & Invoicing - Keylock Indonesia')

@section('breadcrumbs')
    <a href="{{ route('erp.index') }}" class="hover:text-slate-800">Home</a>
    <span>›</span>
    <span class="text-slate-800 font-semibold" x-text="lang === 'zh' ? '财务与发票管理' : 'Finance & Invoicing'">Finance & Invoicing</span>
@endsection

@section('content')
<div x-data="erpFinanceManager()">

    <!-- ========================================================================= -->
    <!-- MOBILE TOP BAR & QUICK ACTIONS (VISIBLE ON SCREENS < MD)                   -->
    <!-- ========================================================================= -->
    <div class="block md:hidden space-y-3 mb-4">
        <!-- 1. Searchbox + Filter Button -->
        <div class="flex items-center space-x-2">
            <div class="relative flex-1">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input type="text" 
                       x-model="searchQuery" 
                       @input="onSearchInput()"
                       @keydown.enter.prevent="submitSearch()"
                       :placeholder="lang === 'zh' ? '搜索发票、客户、订单...' : 'Cari nomor invoice, pelanggan, order...'"
                       placeholder="Cari nomor invoice, pelanggan, order..." 
                       class="w-full bg-white border border-[#E5EAF2] rounded-2xl pl-10 pr-3.5 py-2.5 text-xs text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-blue-500 shadow-2xs">
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
                <option value="unpaid" x-text="lang === 'zh' ? '待付款 (Belum Bayar)' : 'Belum Dibayar'">Belum Dibayar</option>
                <option value="partial" x-text="lang === 'zh' ? '部分付款 (Partial)' : 'Sebagian (Partial)'">Sebagian (Partial)</option>
                <option value="paid" x-text="lang === 'zh' ? '已结清 (Lunas)' : 'Lunas (Paid)'">Lunas (Paid)</option>
                <option value="overdue" x-text="lang === 'zh' ? '逾期未付 (Overdue)' : 'Lewat Tempo (Overdue)'">Lewat Tempo (Overdue)</option>
            </select>
        </div>

        <!-- Mobile Navigation Tabs (Customer Invoices vs Vendor Bills) -->
        <div class="grid grid-cols-2 gap-2 py-0.5">
            <a href="{{ route('erp.finance.index', ['tab' => 'invoices']) }}" 
               class="py-2.5 px-3 rounded-xl text-xs text-center transition flex items-center justify-center space-x-1.5 {{ $tab === 'invoices' ? 'bg-[#142A57] text-white font-bold shadow-xs' : 'bg-white text-slate-700 hover:text-slate-900 border border-[#E5EAF2] font-semibold shadow-2xs' }}">
                <svg class="w-3.5 h-3.5 {{ $tab === 'invoices' ? 'text-yellow-300' : 'text-blue-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span x-text="lang === 'zh' ? '客户发票' : 'Tagihan Piutang (AR)'">Tagihan Piutang (AR)</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] font-black {{ $tab === 'invoices' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-800' }}">
                    {{ $totalCustomerInvoicesCount }}
                </span>
            </a>
            <a href="{{ route('erp.finance.index', ['tab' => 'bills']) }}" 
               class="py-2.5 px-3 rounded-xl text-xs text-center transition flex items-center justify-center space-x-1.5 {{ $tab === 'bills' ? 'bg-[#142A57] text-white font-bold shadow-xs' : 'bg-white text-slate-700 hover:text-slate-900 border border-[#E5EAF2] font-semibold shadow-2xs' }}">
                <svg class="w-3.5 h-3.5 {{ $tab === 'bills' ? 'text-yellow-300' : 'text-purple-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                <span x-text="lang === 'zh' ? '供应商账单' : 'Utang Vendor (AP)'">Utang Vendor (AP)</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] font-black {{ $tab === 'bills' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-800' }}">
                    {{ $totalVendorBillsCount }}
                </span>
            </a>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- DESKTOP HEADER & CONTROLS (VISIBLE ON SCREENS >= MD)                      -->
    <!-- ========================================================================= -->
    <div class="hidden md:block">
        <!-- 1. PAGE HEADER                                                            -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-2xl bg-amber-400 text-slate-950 flex items-center justify-center font-black shadow-xs">
                        <svg class="w-5 h-5 text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-slate-900 tracking-tight" x-text="lang === 'zh' ? '财务、发票与利润中心' : 'Finance & Invoicing Hub'">
                            Finance & Invoicing Hub
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5" x-text="lang === 'zh' ? '全面监控客户收款 (AR)、供应商应付账款 (AP)、现金流变动及交易毛利率。' : 'Pantau penagihan pelanggan (AR), utang vendor (AP), arus kas riil, dan laporan laba kotor per transaksi.'">
                            Pantau penagihan pelanggan (AR), utang vendor (AP), arus kas riil, dan laporan laba kotor per transaksi.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Top Right Action Buttons -->
            <div class="flex items-center space-x-2.5">
                @if($tab === 'invoices' || $tab === 'profit')
                    <button type="button" 
                            @click="openInvoiceModal()" 
                            class="bg-blue-600 hover:bg-blue-700 text-white font-extrabold px-4 py-2.5 rounded-full shadow-md text-xs transition flex items-center space-x-1.5 active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                        <span x-text="lang === 'zh' ? '开具客户发票' : 'Buat Invoice Pelanggan'">Buat Invoice Pelanggan</span>
                    </button>
                @endif

                @if($tab === 'bills')
                    <button type="button" 
                            @click="openBillModal()" 
                            class="bg-purple-600 hover:bg-purple-700 text-white font-extrabold px-4 py-2.5 rounded-full shadow-md text-xs transition flex items-center space-x-1.5 active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span x-text="lang === 'zh' ? '录入供应商账单' : 'Catat Tagihan Vendor'">Catat Tagihan Vendor</span>
                    </button>
                @endif
            </div>
        </div>

        <!-- 2. NAVIGATION TABS (DESKTOP) -->
        <div class="flex flex-wrap items-center gap-2 mb-5 border-b border-slate-200/80 pb-3">
            <a href="{{ route('erp.finance.index', ['tab' => 'invoices']) }}" 
               class="px-4 py-2 rounded-2xl text-xs font-bold transition flex items-center space-x-2 {{ $tab === 'invoices' ? 'bg-slate-900 text-white shadow-sm' : 'bg-white text-slate-600 hover:text-slate-900 border border-slate-200/90' }}">
                <svg class="w-4 h-4 {{ $tab === 'invoices' ? 'text-yellow-400' : 'text-blue-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span x-text="lang === 'zh' ? '客户发票 / 应收 (AR)' : 'Tagihan Pelanggan / Piutang (AR)'">Tagihan Pelanggan / Piutang (AR)</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $tab === 'invoices' ? 'bg-slate-800 text-yellow-300' : 'bg-slate-100 text-slate-700' }}">
                    {{ $totalCustomerInvoicesCount }}
                </span>
            </a>
            <a href="{{ route('erp.finance.index', ['tab' => 'bills']) }}" 
               class="px-4 py-2 rounded-2xl text-xs font-bold transition flex items-center space-x-2 {{ $tab === 'bills' ? 'bg-slate-900 text-white shadow-sm' : 'bg-white text-slate-600 hover:text-slate-900 border border-slate-200/90' }}">
                <svg class="w-4 h-4 {{ $tab === 'bills' ? 'text-yellow-400' : 'text-purple-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                <span x-text="lang === 'zh' ? '供应商账单 / 应付 (AP)' : 'Tagihan Vendor / Utang (AP)'">Tagihan Vendor / Utang (AP)</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $tab === 'bills' ? 'bg-slate-800 text-yellow-300' : 'bg-slate-100 text-slate-700' }}">
                    {{ $totalVendorBillsCount }}
                </span>
            </a>
            <a href="{{ route('erp.finance.index', ['tab' => 'cashflow']) }}" 
               class="px-4 py-2 rounded-2xl text-xs font-bold transition flex items-center space-x-2 {{ $tab === 'cashflow' ? 'bg-slate-900 text-white shadow-sm' : 'bg-white text-slate-600 hover:text-slate-900 border border-slate-200/90' }}">
                <svg class="w-4 h-4 {{ $tab === 'cashflow' ? 'text-yellow-400' : 'text-emerald-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                <span x-text="lang === 'zh' ? '现金流与收付款明细' : 'Riwayat Pembayaran (Kas In & Out)'">Riwayat Pembayaran (Kas In & Out)</span>
            </a>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 1: CUSTOMER INVOICES (TAGIHAN PELANGGAN / PIUTANG AR)            -->
    <!-- ========================================================================= -->
    @if($tab === 'invoices')
        <!-- Operational Admin KPI Cards for Customer Invoices / AR (Matching Procurement Style) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Card 1: Transaksi Belum Invoiced -->
            <div class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1" x-text="lang === 'zh' ? '待开发票交易' : 'Transaksi Belum Invoiced'">Transaksi Belum Invoiced</p>
                    <h3 class="text-2xl font-black text-slate-900">{{ $unbilledOrdersCount }}</h3>
                    <p class="text-[11px] text-slate-500 mt-0.5" x-text="lang === 'zh' ? '已转交易未开发票' : 'Rp {{ number_format($unbilledOrdersAmount, 0, ',', '.') }} Belum Ditagih'">Rp {{ number_format($unbilledOrdersAmount, 0, ',', '.') }} Belum Ditagih</p>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
            </div>

            <!-- Card 2: Total Piutang Belum Terbayar (Unpaid Invoices) -->
            <div class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1" x-text="lang === 'zh' ? '待收发票款项' : 'Invoice Belum Lunas'">Invoice Belum Lunas</p>
                    <h3 class="text-xl font-black text-slate-900 font-mono">Rp {{ number_format($unpaidInvoicesAmount, 0, ',', '.') }}</h3>
                    <p class="text-[11px] text-amber-600 font-semibold mt-0.5" x-text="'{{ $unpaidInvoicesCount }}' + (lang === 'zh' ? ' 笔待收款发票' : ' Invoice Menunggu Bayar')">{{ $unpaidInvoicesCount }} Invoice Menunggu Bayar</p>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>

            <!-- Card 3: Invoice Overdue (Lewat Jatuh Tempo) -->
            <div class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1" x-text="lang === 'zh' ? '逾期未付发票' : 'Invoice Overdue (Lewat Tempo)'">Invoice Overdue (Lewat Tempo)</p>
                    <h3 class="text-xl font-black text-rose-600 font-mono">Rp {{ number_format($overdueInvoicesAmount, 0, ',', '.') }}</h3>
                    <p class="text-[11px] text-rose-500 font-semibold mt-0.5" x-text="'{{ $overdueInvoicesCount }}' + (lang === 'zh' ? ' 笔逾期发票' : ' Invoice Lewat Tempo')">{{ $overdueInvoicesCount }} Invoice Lewat Tempo</p>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>

            <!-- Card 4: Invoice Paid (Lunas) -->
            <div class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1" x-text="lang === 'zh' ? '已结清发票' : 'Invoice Lunas (Paid)'">Invoice Lunas (Paid)</p>
                    <h3 class="text-xl font-black text-slate-900 font-mono">Rp {{ number_format($paidInvoicesAmount, 0, ',', '.') }}</h3>
                    <p class="text-[11px] text-emerald-600 font-semibold mt-0.5" x-text="'{{ $paidInvoicesCount }}' + (lang === 'zh' ? ' 笔已付清发票' : ' Invoice Telah Selesai')">{{ $paidInvoicesCount }} Invoice Telah Selesai</p>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>
        <!-- Search & Filter Bar for Invoices -->
        <div class="bg-white rounded-3xl p-3 border border-slate-200/80 shadow-xs mb-6 flex flex-col md:flex-row items-center justify-between gap-3">
            <div class="relative w-full md:w-96 flex items-center">
                <svg class="w-4 h-4 text-slate-400 absolute left-4 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input type="text" 
                       x-model="searchQuery" 
                       @input="onSearchInput()"
                       @keydown.enter.prevent="submitSearch()"
                       :placeholder="lang === 'zh' ? '搜索发票、客户、订单...' : 'Cari nomor invoice, pelanggan, order...'"
                       placeholder="Cari nomor invoice, pelanggan, order..." 
                       class="w-full bg-transparent pl-11 pr-4 py-2 text-xs text-slate-900 placeholder:text-slate-400 focus:outline-none">
            </div>

            <div class="flex items-center space-x-2.5 self-end md:self-auto w-full md:w-auto justify-end">
                <select x-model="statusFilter" @change="submitSearch()" class="bg-slate-50 border border-slate-200 rounded-2xl px-4 py-2 text-xs font-semibold text-slate-700 outline-none focus:border-blue-400">
                    <option value="all" x-text="lang === 'zh' ? '所有状态' : 'Semua Status'">Semua Status</option>
                    <option value="unpaid" x-text="lang === 'zh' ? '待付款 (Belum Bayar)' : 'Belum Dibayar'">Belum Dibayar</option>
                    <option value="partial" x-text="lang === 'zh' ? '部分付款 (Partial)' : 'Sebagian (Partial)'">Sebagian (Partial)</option>
                    <option value="paid" x-text="lang === 'zh' ? '已结清 (Lunas)' : 'Lunas (Paid)'">Lunas (Paid)</option>
                    <option value="overdue" x-text="lang === 'zh' ? '逾期未付 (Overdue)' : 'Lewat Tempo (Overdue)'">Lewat Tempo (Overdue)</option>
                </select>

                <button type="button" @click="clearSearch()" class="w-9 h-9 rounded-2xl bg-slate-50 hover:bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-500 hover:text-slate-800 transition shadow-2xs" title="Reset / Refresh Data">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                </button>
            </div>
        </div>

        <!-- DESKTOP TABLE VIEW (VISIBLE ON SCREENS >= MD) -->
        <div class="hidden md:block bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden mb-8">
            <div class="overflow-x-auto min-h-[360px]">
                <table class="w-full text-left border-collapse text-xs whitespace-nowrap">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200/90 text-slate-400 font-black uppercase text-[10px] tracking-wider">
                            <th class="py-3.5 px-4 w-12 text-center">#</th>
                            <th class="py-3.5 px-4 w-44 font-black text-slate-700" x-text="lang === 'zh' ? '发票编号' : 'No. Invoice'">No. Invoice</th>
                            <th class="py-3.5 px-4 min-w-[200px]" x-text="lang === 'zh' ? '客户名称' : 'Pelanggan'">Pelanggan</th>
                            <th class="py-3.5 px-4 w-36" x-text="lang === 'zh' ? '关联合同 (SO/RO)' : 'Order Terkait'">Order Terkait</th>
                            <th class="py-3.5 px-4 w-36" x-text="lang === 'zh' ? '开票与到期日' : 'Tgl Terbit & Tempo'">Tgl Terbit & Tempo</th>
                            <th class="py-3.5 px-4 w-36 text-right" x-text="lang === 'zh' ? '发票总额' : 'Total Tagihan'">Total Tagihan</th>
                            <th class="py-3.5 px-4 w-32 text-right" x-text="lang === 'zh' ? '已付金额' : 'Sudah Dibayar'">Sudah Dibayar</th>
                            <th class="py-3.5 px-4 w-32 text-right" x-text="lang === 'zh' ? '未结余额 (Piutang)' : 'Sisa Piutang'">Sisa Piutang</th>
                            <th class="py-3.5 px-4 w-28 text-center" x-text="lang === 'zh' ? '状态' : 'Status'">Status</th>
                            <th class="py-3.5 px-4 w-28 text-center sticky right-0 bg-slate-50 z-10 shadow-[-6px_0_8px_-4px_rgba(0,0,0,0.06)] border-l border-slate-200" x-text="lang === 'zh' ? '操作' : 'Aksi'">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($customerInvoices as $index => $inv)
                            <tr class="hover:bg-slate-50/80 transition group"
                                x-show="matchesInvoiceRow('{{ strtolower($inv->invoice_number . ' ' . ($inv->contact->name ?? '') . ' ' . ($inv->order->order_number ?? '') . ' ' . $inv->orders->pluck('order_number')->implode(' ')) }}', '{{ $inv->status }}', {{ ($inv->due_date && $inv->due_date->isPast() && $inv->balance_due > 0) ? 'true' : 'false' }})">
                                <td class="py-4 px-4 font-mono text-slate-400 text-[11px]">{{ ($customerInvoices->firstItem() ?? 1) + $index }}</td>
                                <td class="py-4 px-4 font-mono text-xs">
                                    <div class="inline-flex items-center space-x-1.5 font-bold text-slate-900 bg-slate-50 hover:bg-slate-100 px-2 py-0.5 rounded-lg border border-slate-200 transition shadow-2xs">
                                        <button type="button" @click="openDetailsDrawer(@js($inv))" class="font-mono font-black text-slate-900 hover:text-blue-600 transition" title="Buka Detail Tagihan">
                                            {{ $inv->invoice_number }}
                                        </button>
                                        <a href="{{ route('erp.finance.invoices.pdf', $inv->id) }}" 
                                           target="_blank" 
                                           class="p-0.5 rounded text-amber-700 hover:text-amber-950 hover:bg-amber-100 transition shrink-0" 
                                           title="Cetak PDF Invoice ({{ $inv->invoice_number }})">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                        </a>
                                    </div>
                                </td>
                                <td class="py-4 px-4 font-bold text-slate-800">
                                    {{ $inv->contact->name ?? '-' }}
                                </td>
                                <td class="py-4 px-4">
                                    @if($inv->orders && $inv->orders->count() > 0)
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($inv->orders as $ord)
                                                <a href="{{ route('erp.orders.index', ['type' => 'transaksi', 'search' => $ord->order_number]) }}" 
                                                   class="px-2 py-0.5 rounded-md font-mono font-bold text-[10px] hover:underline transition {{ $ord->order_type === 'rental_order' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}"
                                                   title="Buka transaksi ini di Tab Transaksi">
                                                    {{ $ord->order_number }}
                                                </a>
                                            @endforeach
                                        </div>
                                    @elseif($inv->order)
                                        <a href="{{ route('erp.orders.index', ['type' => 'transaksi', 'search' => $inv->order->order_number]) }}" 
                                           class="px-2 py-0.5 rounded-md font-mono font-bold text-[10px] hover:underline transition {{ $inv->order->order_type === 'rental_order' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}"
                                           title="Buka transaksi ini di Tab Transaksi">
                                            {{ $inv->order->order_number }}
                                        </a>
                                    @else
                                        <span class="text-slate-400 text-xs">—</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-[11px] text-slate-600">
                                    <div>{{ $inv->issue_date ? $inv->issue_date->format('d/m/Y') : '-' }}</div>
                                    <div class="text-[10px] {{ $inv->due_date && $inv->due_date->isPast() && $inv->balance_due > 0 ? 'text-rose-600 font-bold' : 'text-slate-400' }}">
                                        Due: {{ $inv->due_date ? $inv->due_date->format('d/m/Y') : '-' }}
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-right font-mono font-black text-slate-900 text-xs">
                                    Rp {{ number_format($inv->total_amount, 0, ',', '.') }}
                                </td>
                                <td class="py-4 px-4 text-right font-mono font-bold text-emerald-600 text-xs">
                                    Rp {{ number_format($inv->paid_amount, 0, ',', '.') }}
                                </td>
                                <td class="py-4 px-4 text-right font-mono font-black {{ $inv->balance_due > 0 ? 'text-rose-600' : 'text-slate-400' }} text-xs">
                                    Rp {{ number_format($inv->balance_due, 0, ',', '.') }}
                                </td>
                                <td class="py-4 px-4 text-center">
                                    @if($inv->status === 'paid')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800" x-text="lang === 'zh' ? '已付清' : 'Lunas'">Lunas</span>
                                    @elseif($inv->status === 'partial')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-blue-100 text-blue-800" x-text="lang === 'zh' ? '部分付款' : 'Sebagian'">Sebagian</span>
                                    @elseif($inv->status === 'overdue')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-rose-100 text-rose-800" x-text="lang === 'zh' ? '已逾期' : 'Jatuh Tempo'">Jatuh Tempo</span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-amber-100 text-amber-800" x-text="lang === 'zh' ? '待付款' : 'Belum Lunas'">Belum Lunas</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-center sticky right-0 bg-white group-hover:bg-slate-50 transition z-10 shadow-[-6px_0_8px_-4px_rgba(0,0,0,0.06)] border-l border-slate-100">
                                    <div class="flex items-center justify-center space-x-1.5">
                                        <!-- Lihat Rincian Invoice -->
                                        <div class="relative group/tip inline-flex">
                                            <button type="button" 
                                                    @click="openDetailsDrawer(@js($inv))" 
                                                    class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 flex items-center justify-center transition active:scale-95 shadow-xs"
                                                    :title="lang === 'zh' ? '查看发票详情' : 'Lihat Rincian Invoice'">
                                                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            </button>
                                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 hidden group-hover/tip:flex flex-col items-center z-30 pointer-events-none">
                                                <div class="bg-slate-900 text-white text-[10px] font-bold px-2 py-1 rounded-lg shadow-lg whitespace-nowrap">
                                                    <span x-text="lang === 'zh' ? '查看发票详情' : 'Lihat Rincian Invoice'">Lihat Rincian Invoice</span>
                                                </div>
                                                <div class="w-1.5 h-1.5 bg-slate-900 rotate-45 -mt-1"></div>
                                            </div>
                                        </div>

                                        <!-- PDF Print -->
                                        <div class="relative group/tip inline-flex">
                                            <a href="{{ route('erp.finance.invoices.pdf', $inv->id) }}" 
                                               target="_blank" 
                                               class="w-8 h-8 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-300 flex items-center justify-center transition active:scale-95 shadow-xs"
                                               :title="lang === 'zh' ? '打印发票 (PDF)' : 'Cetak PDF Invoice'">
                                                <svg class="w-4 h-4 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                            </a>
                                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 hidden group-hover/tip:flex flex-col items-center z-30 pointer-events-none">
                                                <div class="bg-slate-900 text-white text-[10px] font-bold px-2 py-1 rounded-lg shadow-lg whitespace-nowrap">
                                                    <span x-text="lang === 'zh' ? '打印发票 (PDF)' : 'Cetak PDF Invoice'">Cetak PDF Invoice</span>
                                                </div>
                                                <div class="w-1.5 h-1.5 bg-slate-900 rotate-45 -mt-1"></div>
                                            </div>
                                        </div>

                                        <!-- Catat Pembayaran Masuk (Cash In / Pembayaran) -->
                                        @if($inv->balance_due > 0)
                                            <div class="relative group/tip inline-flex">
                                                <button type="button" 
                                                        @click="openPaymentModal(@js($inv), 'in')" 
                                                        class="w-8 h-8 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white flex items-center justify-center transition active:scale-95 shadow-xs"
                                                        :title="lang === 'zh' ? '收款 / 录入付款' : 'Pembayaran'">
                                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <rect x="2" y="6" width="20" height="12" rx="2" stroke-width="2"></rect>
                                                        <circle cx="12" cy="12" r="2.5" stroke-width="2"></circle>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 12h.01M18 12h.01"></path>
                                                    </svg>
                                                </button>
                                                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 hidden group-hover/tip:flex flex-col items-center z-30 pointer-events-none">
                                                    <div class="bg-slate-900 text-white text-[10px] font-bold px-2 py-1 rounded-lg shadow-lg whitespace-nowrap">
                                                        <span x-text="lang === 'zh' ? '收款 / 录入付款' : 'Pembayaran'">Pembayaran</span>
                                                    </div>
                                                    <div class="w-1.5 h-1.5 bg-slate-900 rotate-45 -mt-1"></div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-12 text-slate-400">
                                    <p class="font-bold text-slate-600" x-text="lang === 'zh' ? '暂无客户发票。' : 'Belum ada invoice tagihan pelanggan.'">Belum ada invoice tagihan pelanggan.</p>
                                    <button type="button" @click="openInvoiceModal()" class="mt-2 text-blue-600 font-bold hover:underline" x-text="lang === 'zh' ? '+ 开具首张发票' : '+ Buat Invoice Pertama'">+ Buat Invoice Pertama</button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Bar for Customer Invoices -->
            {{ $customerInvoices->links('erp.pagination') }}
        </div>

        <!-- MOBILE CARD LIST VIEW (VISIBLE ON SCREENS < MD) -->
        <div class="block md:hidden space-y-3 mb-8">
            @include('erp.partials.invoice-mobile-cards')
            <div class="pt-2">
                {{ $customerInvoices->links('erp.pagination') }}
            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- TAB 2: VENDOR BILLS (TAGIHAN VENDOR / UTANG)                              -->
    <!-- ========================================================================= -->
    @if($tab === 'bills')
        <!-- Operational Admin KPI Cards for Vendor Bills / AP (Matching Procurement Style) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Card 1: PO Belum Ditagihkan -->
            <div class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1" x-text="lang === 'zh' ? '待入账供应商 PO' : 'PO Belum Ditagihkan'">PO Belum Ditagihkan</p>
                    <h3 class="text-2xl font-black text-slate-900">{{ $unbilledPosCount }}</h3>
                    <p class="text-[11px] text-slate-500 mt-0.5" x-text="lang === 'zh' ? 'PO 已下达但未开账单' : 'Rp {{ number_format($unbilledPosAmount, 0, ',', '.') }} Belum Ada Bill'">Rp {{ number_format($unbilledPosAmount, 0, ',', '.') }} Belum Ada Bill</p>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                </div>
            </div>

            <!-- Card 2: Tagihan Vendor Belum Lunas -->
            <div class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1" x-text="lang === 'zh' ? '待付供应商账单' : 'Tagihan Vendor Belum Lunas'">Tagihan Vendor Belum Lunas</p>
                    <h3 class="text-xl font-black text-slate-900 font-mono">Rp {{ number_format($unpaidBillsAmount, 0, ',', '.') }}</h3>
                    <p class="text-[11px] text-amber-600 font-semibold mt-0.5" x-text="'{{ $unpaidBillsCount }}' + (lang === 'zh' ? ' 笔待付款账单' : ' Tagihan Vendor')">{{ $unpaidBillsCount }} Tagihan Vendor</p>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>

            <!-- Card 3: Tagihan Vendor Overdue (Lewat Tempo) -->
            <div class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1" x-text="lang === 'zh' ? '逾期应付账单' : 'Tagihan Vendor Overdue'">Tagihan Vendor Overdue</p>
                    <h3 class="text-xl font-black text-rose-600 font-mono">Rp {{ number_format($overdueBillsAmount, 0, ',', '.') }}</h3>
                    <p class="text-[11px] text-rose-500 font-semibold mt-0.5" x-text="'{{ $overdueBillsCount }}' + (lang === 'zh' ? ' 笔逾期账单' : ' Tagihan Lewat Tempo')">{{ $overdueBillsCount }} Tagihan Lewat Tempo</p>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>

            <!-- Card 4: Tagihan Vendor Lunas (Paid) -->
            <div class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1" x-text="lang === 'zh' ? '已付清账单' : 'Tagihan Vendor Lunas'">Tagihan Vendor Lunas</p>
                    <h3 class="text-xl font-black text-slate-900 font-mono">Rp {{ number_format($paidBillsAmount, 0, ',', '.') }}</h3>
                    <p class="text-[11px] text-emerald-600 font-semibold mt-0.5" x-text="'{{ $paidBillsCount }}' + (lang === 'zh' ? ' 笔已付清账单' : ' Tagihan Telah Selesai')">{{ $paidBillsCount }} Tagihan Telah Selesai</p>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>

        <!-- Search & Filter Bar for Bills -->
        <div class="bg-white rounded-3xl p-3 border border-slate-200/80 shadow-xs mb-6 flex flex-col md:flex-row items-center justify-between gap-3">
            <div class="relative w-full md:w-96">
                <svg class="w-4 h-4 text-slate-400 absolute left-4 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input type="text" 
                       x-model="searchQuery" 
                       :placeholder="lang === 'zh' ? '搜索账单号、供应商、原发票号、PO...' : 'Cari nomor bill, vendor, ref/kwitansi, PO...'"
                       placeholder="Cari nomor bill, vendor, ref/kwitansi, PO..." 
                       class="w-full bg-transparent pl-11 pr-4 py-2 text-xs text-slate-900 placeholder:text-slate-400 focus:outline-none">
            </div>

            <div class="flex items-center space-x-2.5 self-end md:self-auto w-full md:w-auto justify-end">
                <select x-model="statusFilter" class="bg-slate-50 border border-slate-200 rounded-2xl px-4 py-2 text-xs font-semibold text-slate-700 outline-none focus:border-blue-400">
                    <option value="all" x-text="lang === 'zh' ? '所有状态' : 'Semua Status'">Semua Status</option>
                    <option value="unpaid" x-text="lang === 'zh' ? '待付款 (Belum Bayar)' : 'Belum Dibayar'">Belum Dibayar</option>
                    <option value="partial" x-text="lang === 'zh' ? '部分付款 (Partial)' : 'Sebagian (Partial)'">Sebagian (Partial)</option>
                    <option value="paid" x-text="lang === 'zh' ? '已结清 (Lunas)' : 'Lunas (Paid)'">Lunas (Paid)</option>
                    <option value="overdue" x-text="lang === 'zh' ? '逾期应付 (Overdue)' : 'Lewat Tempo (Overdue)'">Lewat Tempo (Overdue)</option>
                </select>
            </div>
        </div>

        <!-- DESKTOP TABLE VIEW (VISIBLE ON SCREENS >= MD) -->
        <div class="hidden md:block bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden mb-8">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200/90 text-slate-400 font-black uppercase text-[10px] tracking-wider">
                            <th class="py-3.5 px-4">#</th>
                            <th class="py-3.5 px-4" x-text="lang === 'zh' ? '账单编号' : 'No. Bill'">No. Bill</th>
                            <th class="py-3.5 px-4" x-text="lang === 'zh' ? '供应商 (Vendor)' : 'Vendor'">Vendor</th>
                            <th class="py-3.5 px-4" x-text="lang === 'zh' ? '供应商原发票号' : 'No. Kwitansi/Ref Vendor'">No. Kwitansi/Ref Vendor</th>
                            <th class="py-3.5 px-4" x-text="lang === 'zh' ? '关联合同 (PO / 客户SO)' : 'PO / Proyek Terkait'">PO / Proyek Terkait</th>
                            <th class="py-3.5 px-4 text-right" x-text="lang === 'zh' ? '账单总额' : 'Total Tagihan'">Total Tagihan</th>
                            <th class="py-3.5 px-4 text-right" x-text="lang === 'zh' ? '已付金额' : 'Sudah Dibayar'">Sudah Dibayar</th>
                            <th class="py-3.5 px-4 text-right" x-text="lang === 'zh' ? '待付款 (Utang)' : 'Sisa Utang'">Sisa Utang</th>
                            <th class="py-3.5 px-4 text-center" x-text="lang === 'zh' ? '状态' : 'Status'">Status</th>
                            <th class="py-3.5 px-6 text-center sticky right-0 bg-slate-50 z-10 shadow-[-6px_0_8px_-4px_rgba(0,0,0,0.06)]" x-text="lang === 'zh' ? '操作' : 'Aksi'">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($vendorBills as $index => $bill)
                            <tr class="hover:bg-slate-50/80 transition group"
                                x-show="matchesBillRow('{{ strtolower($bill->invoice_number . ' ' . ($bill->vendor_ref_no ?? '') . ' ' . ($bill->contact->name ?? '') . ' ' . ($bill->order->order_number ?? '') . ' ' . ($bill->customerOrder->order_number ?? '')) }}', '{{ $bill->status }}', {{ ($bill->due_date && $bill->due_date->isPast() && $bill->balance_due > 0) ? 'true' : 'false' }})">
                                <td class="py-4 px-4 font-mono text-slate-400 text-[11px]">{{ ($vendorBills->firstItem() ?? 1) + $index }}</td>
                                <td class="py-4 px-4">
                                    <button type="button" @click="openDetailsDrawer(@js($bill))" class="font-mono font-black text-slate-900 hover:text-purple-600 transition">
                                        {{ $bill->invoice_number }}
                                    </button>
                                </td>
                                <td class="py-4 px-4 font-bold text-slate-800">
                                    {{ $bill->contact->name ?? '-' }}
                                </td>
                                <td class="py-4 px-4 font-mono text-slate-600 text-xs">
                                    {{ $bill->vendor_ref_no ?: '-' }}
                                </td>
                                <td class="py-4 px-4">
                                    @if($bill->order)
                                        <span class="px-2 py-0.5 rounded-md font-mono font-bold text-[10px] bg-amber-100 text-amber-800">
                                            {{ $bill->order->order_number }}
                                        </span>
                                    @elseif($bill->customerOrder)
                                        <span class="px-2 py-0.5 rounded-md font-mono font-bold text-[10px] bg-blue-100 text-blue-800">
                                            {{ $bill->customerOrder->order_number }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 text-xs">—</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-right font-mono font-black text-slate-900 text-xs">
                                    Rp {{ number_format($bill->total_amount, 0, ',', '.') }}
                                </td>
                                <td class="py-4 px-4 text-right font-mono font-bold text-emerald-600 text-xs">
                                    Rp {{ number_format($bill->paid_amount, 0, ',', '.') }}
                                </td>
                                <td class="py-4 px-4 text-right font-mono font-black {{ $bill->balance_due > 0 ? 'text-rose-600' : 'text-slate-400' }} text-xs">
                                    Rp {{ number_format($bill->balance_due, 0, ',', '.') }}
                                </td>
                                <td class="py-4 px-4 text-center">
                                    @if($bill->status === 'paid')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800" x-text="lang === 'zh' ? '已付清' : 'Lunas'">Lunas</span>
                                    @elseif($bill->status === 'partial')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-blue-100 text-blue-800" x-text="lang === 'zh' ? '部分支付' : 'Sebagian'">Sebagian</span>
                                    @elseif($bill->status === 'overdue')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-rose-100 text-rose-800" x-text="lang === 'zh' ? '已逾期' : 'Jatuh Tempo'">Jatuh Tempo</span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-amber-100 text-amber-800" x-text="lang === 'zh' ? '待付款' : 'Belum Dibayar'">Belum Dibayar</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-center sticky right-0 bg-white group-hover:bg-slate-50 transition z-10 shadow-[-6px_0_8px_-4px_rgba(0,0,0,0.06)] border-l border-slate-100">
                                    <div class="flex items-center justify-center space-x-1.5">
                                        <!-- Lihat Rincian Tagihan Vendor -->
                                        <div class="relative group/tip inline-flex">
                                            <button type="button" 
                                                    @click="openDetailsDrawer(@js($bill))" 
                                                    class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 flex items-center justify-center transition active:scale-95 shadow-xs"
                                                    :title="lang === 'zh' ? '查看账单详情' : 'Lihat Rincian Tagihan'">
                                                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            </button>
                                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 hidden group-hover/tip:flex flex-col items-center z-30 pointer-events-none">
                                                <div class="bg-slate-900 text-white text-[10px] font-bold px-2 py-1 rounded-lg shadow-lg whitespace-nowrap">
                                                    <span x-text="lang === 'zh' ? '查看账单详情' : 'Lihat Rincian Tagihan'">Lihat Rincian Tagihan</span>
                                                </div>
                                                <div class="w-1.5 h-1.5 bg-slate-900 rotate-45 -mt-1"></div>
                                            </div>
                                        </div>

                                        <!-- Catat Pembayaran Keluar (Cash Out / Pembayaran) -->
                                        @if($bill->balance_due > 0)
                                            <div class="relative group/tip inline-flex">
                                                <button type="button" 
                                                        @click="openPaymentModal(@js($bill), 'out')" 
                                                        class="w-8 h-8 rounded-xl bg-purple-600 hover:bg-purple-700 text-white flex items-center justify-center transition active:scale-95 shadow-xs"
                                                        :title="lang === 'zh' ? '付款给供应商' : 'Pembayaran'">
                                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <rect x="2" y="6" width="20" height="12" rx="2" stroke-width="2"></rect>
                                                        <circle cx="12" cy="12" r="2.5" stroke-width="2"></circle>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 12h.01M18 12h.01"></path>
                                                    </svg>
                                                </button>
                                                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 hidden group-hover/tip:flex flex-col items-center z-30 pointer-events-none">
                                                    <div class="bg-slate-900 text-white text-[10px] font-bold px-2 py-1 rounded-lg shadow-lg whitespace-nowrap">
                                                        <span x-text="lang === 'zh' ? '付款给供应商' : 'Pembayaran'">Pembayaran</span>
                                                    </div>
                                                    <div class="w-1.5 h-1.5 bg-slate-900 rotate-45 -mt-1"></div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-12 text-slate-400">
                                    <p class="font-bold text-slate-600" x-text="lang === 'zh' ? '暂无供应商账单。' : 'Belum ada tagihan vendor.'">Belum ada tagihan vendor.</p>
                                    <button type="button" @click="openBillModal()" class="mt-2 text-purple-600 font-bold hover:underline" x-text="lang === 'zh' ? '+ 录入首张账单' : '+ Catat Tagihan Pertama'">+ Catat Tagihan Pertama</button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Bar for Vendor Bills -->
            {{ $vendorBills->links('erp.pagination') }}
        </div>

        <!-- MOBILE CARD LIST VIEW (VISIBLE ON SCREENS < MD) -->
        <div class="block md:hidden space-y-3 mb-8">
            @include('erp.partials.invoice-mobile-cards')
            <div class="pt-2">
                {{ $vendorBills->links('erp.pagination') }}
            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- TAB 3: CASHFLOW RIWAYAT PEMBAYARAN (CASH IN & OUT)                        -->
    <!-- ========================================================================= -->
    @if($tab === 'cashflow')
        <!-- Search & Filter Bar for Cashflow -->
        <div class="bg-white rounded-3xl p-3 border border-slate-200/80 shadow-xs mb-6 flex flex-col md:flex-row items-center justify-between gap-3">
            <div class="relative w-full md:w-96">
                <svg class="w-4 h-4 text-slate-400 absolute left-4 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input type="text" 
                       x-model="searchQuery" 
                       :placeholder="lang === 'zh' ? '搜索付款编号、银行、凭证号、对方名称...' : 'Cari no. pembayaran, bank, no. ref, pihak terkait...'"
                       placeholder="Cari no. pembayaran, bank, no. ref, pihak terkait..." 
                       class="w-full bg-transparent pl-11 pr-4 py-2 text-xs text-slate-900 placeholder:text-slate-400 focus:outline-none">
            </div>

            <div class="flex items-center space-x-2.5 self-end md:self-auto w-full md:w-auto justify-end">
                <select x-model="statusFilter" class="bg-slate-50 border border-slate-200 rounded-2xl px-4 py-2 text-xs font-semibold text-slate-700 outline-none focus:border-blue-400">
                    <option value="all" x-text="lang === 'zh' ? '所有类型' : 'Semua Arus Kas'">Semua Arus Kas</option>
                    <option value="in" x-text="lang === 'zh' ? '↓ 仅限收款 (Cash In)' : '↓ Kas Masuk (Customer)'">↓ Kas Masuk (Customer)</option>
                    <option value="out" x-text="lang === 'zh' ? '↑ 仅限付款 (Cash Out)' : '↑ Kas Keluar (Vendor)'">↑ Kas Keluar (Vendor)</option>
                </select>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden mb-8">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200/90 text-slate-400 font-black uppercase text-[10px] tracking-wider">
                            <th class="py-3.5 px-4">#</th>
                            <th class="py-3.5 px-4" x-text="lang === 'zh' ? '收付款单号' : 'No. Pembayaran'">No. Pembayaran</th>
                            <th class="py-3.5 px-4" x-text="lang === 'zh' ? '交易类型' : 'Arah Arus Kas'">Arah Arus Kas</th>
                            <th class="py-3.5 px-4" x-text="lang === 'zh' ? '客户 / 供应商' : 'Pihak Terkait'">Pihak Terkait</th>
                            <th class="py-3.5 px-4" x-text="lang === 'zh' ? '发票/账单号' : 'No. Invoice / Bill'">No. Invoice / Bill</th>
                            <th class="py-3.5 px-4" x-text="lang === 'zh' ? '支付方式与银行' : 'Metode & Rekening'">Metode & Rekening</th>
                            <th class="py-3.5 px-4" x-text="lang === 'zh' ? '参考编号 / 凭证' : 'No. Ref Transfer'">No. Ref Transfer</th>
                            <th class="py-3.5 px-4 text-right" x-text="lang === 'zh' ? '交易金额' : 'Nominal'">Nominal</th>
                            <th class="py-3.5 px-4 text-center" x-text="lang === 'zh' ? '凭证附件' : 'Bukti Transfer'">Bukti Transfer</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($payments as $index => $pay)
                            <tr class="hover:bg-slate-50/80 transition group"
                                x-show="matchesCashflowRow('{{ strtolower($pay->payment_number . ' ' . ($pay->reference_number ?? '') . ' ' . ($pay->bank_account ?? '') . ' ' . ($pay->invoice->contact->name ?? '') . ' ' . ($pay->invoice->invoice_number ?? '')) }}', '{{ $pay->payment_type }}')">
                                <td class="py-4 px-4 font-mono text-slate-400 text-[11px]">{{ ($payments->firstItem() ?? 1) + $index }}</td>
                                <td class="py-4 px-4 font-mono font-bold text-slate-900">
                                    {{ $pay->payment_number }}
                                    <div class="text-[10px] text-slate-400 font-normal">
                                        {{ $pay->payment_date ? $pay->payment_date->format('d/m/Y') : '-' }}
                                    </div>
                                </td>
                                <td class="py-4 px-4">
                                    @if($pay->payment_type === 'in')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 inline-flex items-center space-x-1">
                                            <span>↓</span>
                                            <span x-text="lang === 'zh' ? '收款 (Cash In)' : 'Kas Masuk'">Kas Masuk</span>
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-rose-100 text-rose-800 inline-flex items-center space-x-1">
                                            <span>↑</span>
                                            <span x-text="lang === 'zh' ? '付款 (Cash Out)' : 'Kas Keluar'">Kas Keluar</span>
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-4 font-bold text-slate-800">
                                    {{ $pay->invoice->contact->name ?? '-' }}
                                </td>
                                <td class="py-4 px-4 font-mono text-slate-700">
                                    {{ $pay->invoice->invoice_number ?? '-' }}
                                </td>
                                <td class="py-4 px-4 text-slate-700">
                                    <div class="font-bold uppercase text-[11px]">{{ str_replace('_', ' ', $pay->payment_method) }}</div>
                                    <div class="text-[10px] text-slate-500">{{ $pay->bank_account ?: 'BCA' }}</div>
                                </td>
                                <td class="py-4 px-4 font-mono text-slate-600 text-xs">
                                    {{ $pay->reference_number ?: '-' }}
                                </td>
                                <td class="py-4 px-4 text-right font-mono font-black text-xs {{ $pay->payment_type === 'in' ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $pay->payment_type === 'in' ? '+' : '-' }} Rp {{ number_format($pay->amount, 0, ',', '.') }}
                                </td>
                                <td class="py-4 px-4 text-center">
                                    @if($pay->proof_image)
                                        <a href="/storage/{{ $pay->proof_image }}" target="_blank" class="px-2 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 text-[10px] font-bold">
                                            Lihat Foto
                                        </a>
                                    @else
                                        <span class="text-slate-300 text-xs">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-12 text-slate-400" x-text="lang === 'zh' ? '暂无收付款变动记录。' : 'Belum ada catatan mutasi kas masuk / keluar.'">
                                    Belum ada catatan mutasi kas masuk / keluar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Bar for Cashflow -->
            {{ $payments->links('erp.pagination') }}
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- 4. MODAL BUAT INVOICE PELANGGAN (MULTI-TRANSAKSI CHECKLIST)              -->
    <!-- ========================================================================= -->
    <div x-show="modalInvoiceOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto"
         @click.self="modalInvoiceOpen = false" style="display: none;">

        <div class="bg-white rounded-3xl max-w-2xl w-full shadow-2xl border border-slate-200 overflow-hidden my-8 animate-in zoom-in-95 duration-150 flex flex-col max-h-[92vh]">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50 shrink-0">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-2xl bg-blue-100 text-blue-700 flex items-center justify-center font-black shrink-0">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-900" x-text="lang === 'zh' ? '开具客户发票 (Customer Invoice)' : 'Buat Invoice Pelanggan'">
                            Buat Invoice Pelanggan
                        </h3>
                        <p class="text-[11px] text-slate-500 mt-0.5" x-text="lang === 'zh' ? '支持勾选多笔未结订单合并开票' : 'Pilih pelanggan dan ceklis 1 atau lebih transaksi aktif untuk digabung ke dalam 1 invoice.'">
                            Pilih pelanggan dan ceklis 1 atau lebih transaksi aktif untuk digabung ke dalam 1 invoice.
                        </p>
                    </div>
                </div>
                <button type="button" @click="modalInvoiceOpen = false" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-200/60 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form action="{{ route('erp.finance.invoices.store') }}" method="POST" class="p-6 space-y-4 overflow-y-auto">
                @csrf
                <input type="hidden" name="order_id" x-model="invoiceForm.order_id">

                <!-- 1. Pilih Pelanggan -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '客户 (Pelanggan) *' : 'Pilih Pelanggan *'">Pilih Pelanggan *</label>
                    <select name="contact_id" 
                            x-model="invoiceForm.contact_id" 
                            @change="onCustomerChanged()" 
                            required 
                            class="w-full bg-slate-50 border border-slate-200 focus:bg-white focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400/20 rounded-xl px-3 py-2 text-xs font-semibold text-slate-800 transition outline-none">
                        <option value="">-- Pilih Pelanggan --</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- 2. Checklist Transaksi Pelanggan (Bisa pilih > 1 transaksi) -->
                <div class="bg-slate-50/90 rounded-2xl p-3.5 border border-slate-200 space-y-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-1.5">
                            <label class="text-xs font-black text-slate-800" x-text="lang === 'zh' ? '选择交易订单 (可多选)' : 'Ceklis Transaksi (Bisa Lebih Dari 1)'">
                                Ceklis Transaksi (Bisa Lebih Dari 1)
                            </label>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800" 
                                  x-show="invoiceForm.contact_id" 
                                  x-text="getFilteredOrders().length + ' Tersedia'"></span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800" 
                                  x-show="invoiceForm.selectedOrderIds.length > 0" 
                                  x-text="invoiceForm.selectedOrderIds.length + ' Dipilih'"></span>
                        </div>
                        <template x-if="getFilteredOrders().length > 0">
                            <button type="button" 
                                    @click="toggleSelectAllOrders()" 
                                    class="text-[11px] font-bold text-blue-600 hover:text-blue-800 hover:underline transition"
                                    x-text="isAllOrdersSelected() ? 'Batal Pilih Semua' : 'Pilih Semua (' + getFilteredOrders().length + ')'">
                                Pilih Semua
                            </button>
                        </template>
                    </div>

                    <!-- Prompt Jika Belum Pilih Customer -->
                    <template x-if="!invoiceForm.contact_id">
                        <div class="py-4 px-3 text-center rounded-xl bg-white border border-dashed border-slate-200 text-slate-400 text-xs">
                            <svg class="w-6 h-6 mx-auto mb-1 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            <p class="font-semibold text-slate-500">Silakan pilih pelanggan di atas</p>
                            <p class="text-[10px] text-slate-400 mt-0.5">Daftar transaksi yang belum terbit invoice akan otomatis muncul untuk diceklis.</p>
                        </div>
                    </template>

                    <!-- Info Jika Customer Tidak Punya Transaksi Unbilled -->
                    <template x-if="invoiceForm.contact_id && getFilteredOrders().length === 0">
                        <div class="py-4 px-3 text-center rounded-xl bg-white border border-dashed border-slate-200 text-slate-400 text-xs">
                            <p class="font-bold text-slate-600">Tidak ada transaksi aktif yang belum di-invoice untuk pelanggan ini.</p>
                            <p class="text-[10px] text-slate-400 mt-0.5">Anda tetap dapat membuat invoice manual dengan mengisi nominal di bawah.</p>
                        </div>
                    </template>

                    <!-- Checklist Transaksi -->
                    <template x-if="invoiceForm.contact_id && getFilteredOrders().length > 0">
                        <div class="space-y-1.5 max-h-52 overflow-y-auto pr-1">
                            <template x-for="ord in getFilteredOrders()" :key="ord.id">
                                <label class="flex items-start space-x-3 p-2.5 rounded-xl border transition cursor-pointer"
                                       :class="isOrderChecked(ord.id) ? 'bg-blue-50/90 border-blue-300 ring-1 ring-blue-300/60 shadow-xs' : 'bg-white border-slate-200 hover:bg-slate-100/70'">
                                    <input type="checkbox" 
                                           name="order_ids[]" 
                                           :value="ord.id" 
                                           :checked="isOrderChecked(ord.id)"
                                           @change="toggleOrderSelection(ord)" 
                                           class="mt-1 rounded text-blue-600 focus:ring-blue-500 w-4 h-4 cursor-pointer">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-1.5">
                                                <span class="font-mono font-black text-slate-900 text-xs" x-text="ord.order_number"></span>
                                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase"
                                                      :class="ord.order_type === 'sales_order' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800'"
                                                      x-text="ord.order_type === 'sales_order' ? 'Penjualan' : 'Rental'"></span>
                                            </div>
                                            <span class="font-mono font-black text-slate-900 text-xs" x-text="'Rp ' + Number(ord.total_amount).toLocaleString('id-ID')"></span>
                                        </div>
                                        <div class="flex items-center justify-between text-[11px] text-slate-500 mt-1">
                                            <span x-text="ord.order_date ? 'Tgl: ' + formatDate(ord.order_date) : ''"></span>
                                            <span class="text-[10px] text-slate-400 font-mono">
                                                Sub: Rp <span x-text="Number(ord.subtotal).toLocaleString('id-ID')"></span> 
                                                <template x-if="Number(ord.tax_amount) > 0">
                                                    <span> | PPN: Rp <span x-text="Number(ord.tax_amount).toLocaleString('id-ID')"></span></span>
                                                </template>
                                            </span>
                                        </div>
                                    </div>
                                </label>
                            </template>
                        </div>
                    </template>
                </div>

                <!-- 3. Tanggal Terbit & Jatuh Tempo -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '开票日期 *' : 'Tanggal Terbit *'">Tanggal Terbit *</label>
                        <input type="date" name="issue_date" x-model="invoiceForm.issue_date" required class="w-full bg-slate-50 border border-slate-200 focus:bg-white focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400/20 rounded-xl px-3 py-2 text-xs font-semibold text-slate-800 transition outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '付款截止日 *' : 'Jatuh Tempo *'">Jatuh Tempo *</label>
                        <input type="date" name="due_date" x-model="invoiceForm.due_date" required class="w-full bg-slate-50 border border-slate-200 focus:bg-white focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400/20 rounded-xl px-3 py-2 text-xs font-semibold text-slate-800 transition outline-none">
                    </div>
                </div>

                <!-- 4. Subtotal, PPN, Total -->
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Subtotal (Rp) *</label>
                        <input type="hidden" name="subtotal" :value="invoiceForm.subtotal">
                        <input type="text" inputmode="numeric" data-rupiah :value="formatNumberWithDots(invoiceForm.subtotal)" @input="invoiceForm.subtotal = parseNumberFromDots($event.target.value); recalcInvoiceTotal()" required class="w-full bg-slate-50 border border-slate-200 focus:bg-white focus:border-yellow-400 rounded-xl px-3 py-2 text-xs font-mono font-bold text-slate-800 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">PPN (Rp)</label>
                        <input type="hidden" name="tax_amount" :value="invoiceForm.tax_amount">
                        <input type="text" inputmode="numeric" data-rupiah :value="formatNumberWithDots(invoiceForm.tax_amount)" @input="invoiceForm.tax_amount = parseNumberFromDots($event.target.value); recalcInvoiceTotal()" class="w-full bg-slate-50 border border-slate-200 focus:bg-white focus:border-yellow-400 rounded-xl px-3 py-2 text-xs font-mono font-bold text-slate-800 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Total (Rp) *</label>
                        <input type="hidden" name="total_amount" :value="invoiceForm.total_amount">
                        <input type="text" readonly :value="'Rp ' + formatNumberWithDots(invoiceForm.total_amount)" class="w-full bg-emerald-50 border border-emerald-300 rounded-xl px-3 py-2 text-xs font-mono font-black text-emerald-800 outline-none cursor-not-allowed">
                    </div>
                </div>

                <!-- 5. Catatan -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '备注' : 'Catatan Tagihan'">Catatan Tagihan</label>
                    <textarea name="notes" x-model="invoiceForm.notes" rows="2" placeholder="Catatan pembayaran / rekening..." class="w-full bg-slate-50 border border-slate-200 focus:bg-white focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400/20 rounded-xl p-3 text-xs text-slate-800 transition outline-none"></textarea>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-3 border-t border-slate-100">
                    <button type="button" @click="modalInvoiceOpen = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-100 transition" x-text="lang === 'zh' ? '取消' : 'Batal'">Batal</button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-black shadow-sm transition active:scale-95" x-text="lang === 'zh' ? '确认并开具发票' : 'Terbitkan Invoice'">Terbitkan Invoice</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 5. MODAL CATAT TAGIHAN VENDOR (VENDOR BILL)                               -->
    <!-- ========================================================================= -->
    <div x-show="modalBillCreateOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto"
         @click.self="modalBillCreateOpen = false" style="display: none;">

        <div class="bg-white rounded-3xl max-w-xl w-full shadow-2xl border border-slate-200 overflow-hidden my-8 animate-in zoom-in-95 duration-150 flex flex-col">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50 shrink-0">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-2xl bg-purple-100 text-purple-700 flex items-center justify-center font-black shrink-0">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-900" x-text="lang === 'zh' ? '录入供应商账单 (Vendor Bill)' : 'Catat Tagihan Vendor Baru'">
                            Catat Tagihan Vendor Baru
                        </h3>
                        <p class="text-[11px] text-slate-500 mt-0.5" x-text="lang === 'zh' ? '记录应付账款 (AP) 及其对应的采购订单' : 'Catat utang usaha (AP) atas pengadaan unit atau suku cadang vendor.'"></p>
                    </div>
                </div>
                <button type="button" @click="modalBillCreateOpen = false" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-200/60 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form action="{{ route('erp.finance.bills.store') }}" method="POST" class="p-6 space-y-4">
                @csrf

                <!-- Pilih PO Belum Ditagihkan -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '选择未出账单的 PO (可选)' : 'Pilih dari PO Vendor (Autofill)'">Pilih dari PO Vendor (Autofill)</label>
                    <select @change="onPoSelected($event)" class="w-full bg-slate-50 border border-slate-200 focus:bg-white focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400/20 rounded-xl px-3 py-2 text-xs font-semibold text-slate-800 transition outline-none">
                        <option value="">-- Buat Tagihan Vendor Bebas --</option>
                        @foreach($unbilledPos as $upo)
                            <option value="{{ $upo->id }}" 
                                    data-contact="{{ $upo->contact_id }}"
                                    data-subtotal="{{ $upo->subtotal }}"
                                    data-tax="{{ $upo->tax_amount }}"
                                    data-total="{{ $upo->total_amount }}">
                                [{{ $upo->order_number }}] {{ $upo->contact->name ?? '-' }} (Rp {{ number_format($upo->total_amount, 0, ',', '.') }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <input type="hidden" name="order_id" x-model="billForm.order_id">

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '供应商 *' : 'Vendor Mitra *'">Vendor Mitra *</label>
                        <select name="contact_id" x-model="billForm.contact_id" required class="w-full bg-slate-50 border border-slate-200 focus:bg-white focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400/20 rounded-xl px-3 py-2 text-xs font-semibold text-slate-800 transition outline-none">
                            <option value="">-- Pilih Vendor --</option>
                            @foreach($vendors as $v)
                                <option value="{{ $v->id }}">{{ $v->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '供应商发票号 (Vendor Ref)' : 'No. Kwitansi / Surat Tagihan Vendor'">No. Kwitansi / Surat Tagihan Vendor</label>
                        <input type="text" name="vendor_ref_no" placeholder="Contoh: INV/VD-909" class="w-full bg-slate-50 border border-slate-200 focus:bg-white focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400/20 rounded-xl px-3 py-2 text-xs font-semibold text-slate-800 transition outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '账单日期 *' : 'Tanggal Tagihan *'">Tanggal Tagihan *</label>
                        <input type="date" name="issue_date" value="{{ date('Y-m-d') }}" required class="w-full bg-slate-50 border border-slate-200 focus:bg-white focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400/20 rounded-xl px-3 py-2 text-xs font-semibold text-slate-800 transition outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '到期日 *' : 'Jatuh Tempo *'">Jatuh Tempo *</label>
                        <input type="date" name="due_date" value="{{ date('Y-m-d', strtotime('+30 days')) }}" required class="w-full bg-slate-50 border border-slate-200 focus:bg-white focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400/20 rounded-xl px-3 py-2 text-xs font-semibold text-slate-800 transition outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Subtotal (Rp) *</label>
                        <input type="hidden" name="subtotal" :value="billForm.subtotal">
                        <input type="text" inputmode="numeric" data-rupiah :value="formatNumberWithDots(billForm.subtotal)" @input="billForm.subtotal = parseNumberFromDots($event.target.value); recalcBillTotal()" required class="w-full bg-slate-50 border border-slate-200 focus:bg-white focus:border-yellow-400 rounded-xl px-3 py-2 text-xs font-mono font-bold text-slate-800 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">PPN (Rp)</label>
                        <input type="hidden" name="tax_amount" :value="billForm.tax_amount">
                        <input type="text" inputmode="numeric" data-rupiah :value="formatNumberWithDots(billForm.tax_amount)" @input="billForm.tax_amount = parseNumberFromDots($event.target.value); recalcBillTotal()" class="w-full bg-slate-50 border border-slate-200 focus:bg-white focus:border-yellow-400 rounded-xl px-3 py-2 text-xs font-mono font-bold text-slate-800 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Total (Rp) *</label>
                        <input type="hidden" name="total_amount" :value="billForm.total_amount">
                        <input type="text" readonly :value="'Rp ' + formatNumberWithDots(billForm.total_amount)" class="w-full bg-purple-50 border border-purple-300 rounded-xl px-3 py-2 text-xs font-mono font-black text-purple-900 outline-none cursor-not-allowed">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '备注' : 'Catatan Tagihan'">Catatan Tagihan</label>
                    <textarea name="notes" rows="2" placeholder="Catatan tagihan vendor..." class="w-full bg-slate-50 border border-slate-200 focus:bg-white focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400/20 rounded-xl p-3 text-xs text-slate-800 transition outline-none"></textarea>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-3 border-t border-slate-100">
                    <button type="button" @click="modalBillCreateOpen = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-100 transition" x-text="lang === 'zh' ? '取消' : 'Batal'">Batal</button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-black shadow-sm transition active:scale-95" x-text="lang === 'zh' ? '保存供应商账单' : 'Simpan Tagihan Vendor'">Simpan Tagihan Vendor</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 6. MODAL UNIVERSAL CATAT PEMBAYARAN (CASH IN / CASH OUT)                  -->
    <!-- ========================================================================= -->
    <div x-show="modalPaymentOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto"
         @click.self="modalPaymentOpen = false" style="display: none;">

        <div class="bg-white rounded-3xl max-w-lg w-full shadow-2xl border border-slate-200 overflow-hidden my-8 animate-in zoom-in-95 duration-150 flex flex-col">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50 shrink-0">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-2xl flex items-center justify-center font-black shrink-0"
                         :class="paymentDirection === 'in' ? 'bg-emerald-100 text-emerald-700' : 'bg-purple-100 text-purple-700'">
                        <svg x-show="paymentDirection === 'in'" class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path></svg>
                        <svg x-show="paymentDirection !== 'in'" class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-900" x-text="paymentDirection === 'in' ? (lang === 'zh' ? '记录客户收款 (Cash In)' : 'Catat Pembayaran Masuk Pelanggan') : (lang === 'zh' ? '记录供应商付款 (Cash Out)' : 'Catat Pelunasan Utang Vendor')">
                            Catat Pembayaran
                        </h3>
                        <p class="text-[11px] text-slate-500 mt-0.5" x-text="paymentDirection === 'in' ? (lang === 'zh' ? '将收款核销至客户发票并更新实收金额' : 'Catat mutasi kas/bank masuk dan kurangi piutang pelanggan.') : (lang === 'zh' ? '核销供应商账单并减少应付余额' : 'Catat mutasi kas/bank keluar untuk pelunasan tagihan vendor.')"></p>
                    </div>
                </div>
                <button type="button" @click="modalPaymentOpen = false" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-200/60 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form action="{{ route('erp.finance.payments.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf

                <input type="hidden" name="invoice_id" x-model="selectedInvoiceForPay?.id">

                <!-- Info Box -->
                <div class="p-3.5 rounded-2xl border text-xs"
                     :class="paymentDirection === 'in' ? 'bg-emerald-50 border-emerald-200 text-emerald-950' : 'bg-purple-50 border-purple-200 text-purple-950'">
                    <div class="flex items-center justify-between font-bold">
                        <span x-text="selectedInvoiceForPay?.invoice_number"></span>
                        <span x-text="selectedInvoiceForPay?.contact?.name"></span>
                    </div>
                    <div class="flex items-center justify-between font-mono font-black text-sm mt-1 pt-1 border-t border-slate-200/60">
                        <span x-text="lang === 'zh' ? '剩余待付/待收' : 'Sisa Tagihan'">Sisa Tagihan</span>
                        <span x-text="'Rp ' + Number(selectedInvoiceForPay?.balance_due || 0).toLocaleString('id-ID')"></span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '支付/收款日期 *' : 'Tanggal Bayar *'">Tanggal Bayar *</label>
                        <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required class="w-full bg-slate-50 border border-slate-200 focus:bg-white focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400/20 rounded-xl px-3 py-2 text-xs font-semibold text-slate-800 transition outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '金额 (Rp) *' : 'Nominal Bayar (Rp) *'">Nominal Bayar (Rp) *</label>
                        <input type="hidden" name="amount" :value="payAmount">
                        <input type="text" inputmode="numeric" data-rupiah :value="formatNumberWithDots(payAmount)" @input="payAmount = parseNumberFromDots($event.target.value)" placeholder="0" required class="w-full bg-white border border-slate-300 focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400/20 rounded-xl px-3 py-2 text-xs font-mono font-black text-slate-900 transition outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '支付渠道 *' : 'Metode Pembayaran *'">Metode Pembayaran *</label>
                        <select name="payment_method" required class="w-full bg-slate-50 border border-slate-200 focus:bg-white focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400/20 rounded-xl px-3 py-2 text-xs font-semibold text-slate-800 transition outline-none">
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cash">Cash / Tunai</option>
                            <option value="giro">Giro / Bilyet</option>
                            <option value="qris">QRIS</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '结算银行账户' : 'Rekening Bank'">Rekening Bank</label>
                        <input type="text" name="bank_account" value="BCA - PT KEY LOCK INDONESIA" class="w-full bg-slate-50 border border-slate-200 focus:bg-white focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400/20 rounded-xl px-3 py-2 text-xs font-semibold text-slate-800 transition outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '转账凭证号' : 'No. Referensi Transfer'">No. Referensi Transfer</label>
                        <input type="text" name="reference_number" placeholder="Contoh: REF987123" class="w-full bg-slate-50 border border-slate-200 focus:bg-white focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400/20 rounded-xl px-3 py-2 text-xs font-semibold text-slate-800 transition outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '凭证照片' : 'Upload Bukti Transfer'">Upload Bukti Transfer</label>
                        <input type="file" name="proof_image" accept="image/*" class="w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '备注' : 'Catatan Pembayaran'">Catatan Pembayaran</label>
                    <textarea name="notes" rows="2" placeholder="Catatan bukti transfer / kasir..." class="w-full bg-slate-50 border border-slate-200 focus:bg-white focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400/20 rounded-xl p-3 text-xs text-slate-800 transition outline-none"></textarea>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-3 border-t border-slate-100">
                    <button type="button" @click="modalPaymentOpen = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-100 transition" x-text="lang === 'zh' ? '取消' : 'Batal'">Batal</button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-black shadow-sm transition active:scale-95"
                            x-text="paymentDirection === 'in' ? (lang === 'zh' ? '确认收款入账' : 'Konfirmasi Kas Masuk') : (lang === 'zh' ? '确认支付出账' : 'Konfirmasi Kas Keluar')">
                        Konfirmasi Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 7. SLIDE-UP DETAIL DRAWER FOR INVOICE / BILL                              -->
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
                <div>
                    <h3 class="text-base font-extrabold" x-text="activeDoc?.invoice_number || 'Detail Dokumen'"></h3>
                    <p class="text-xs text-slate-400" x-text="'Pihak: ' + (activeDoc?.contact?.name || '-')"></p>
                </div>
                <button @click="drawerOpen = false" class="text-slate-400 hover:text-white p-1 rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="p-6 overflow-y-auto space-y-5 text-xs flex-1">
                <div class="grid grid-cols-2 gap-3 bg-slate-50 p-4 rounded-2xl border border-slate-200">
                    <div>
                        <span class="text-slate-400 font-bold block mb-0.5">Tipe Dokumen</span>
                        <span class="font-bold text-slate-900 uppercase" x-text="activeDoc?.invoice_type"></span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-bold block mb-0.5">Status</span>
                        <span class="font-bold text-slate-900 capitalize" x-text="activeDoc?.status"></span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-bold block mb-0.5">Tanggal Terbit</span>
                        <span class="font-medium text-slate-800" x-text="activeDoc?.issue_date"></span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-bold block mb-0.5">Jatuh Tempo</span>
                        <span class="font-medium text-slate-800" x-text="activeDoc?.due_date"></span>
                    </div>
                    <template x-if="activeDoc?.orders && activeDoc.orders.length > 0">
                        <div class="col-span-2 pt-2 border-t border-slate-200/60">
                            <span class="text-slate-400 font-bold block mb-1">Transaksi Terkait:</span>
                            <div class="flex flex-wrap gap-1.5">
                                <template x-for="ord in activeDoc.orders" :key="ord.id">
                                    <span class="px-2 py-0.5 rounded-md font-mono font-bold text-[10px] bg-blue-100 text-blue-800" x-text="ord.order_number"></span>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Financial Balance -->
                <div class="bg-slate-900 text-white p-4 rounded-2xl space-y-2">
                    <div class="flex items-center justify-between text-xs text-slate-400">
                        <span>Total Nilai Tagihan</span>
                        <span class="font-mono" x-text="'Rp ' + Number(activeDoc?.total_amount || 0).toLocaleString('id-ID')"></span>
                    </div>
                    <div class="flex items-center justify-between text-xs text-emerald-400 font-bold">
                        <span>Sudah Dibayar</span>
                        <span class="font-mono" x-text="'Rp ' + Number(activeDoc?.paid_amount || 0).toLocaleString('id-ID')"></span>
                    </div>
                    <div class="flex items-center justify-between text-sm font-black pt-2 border-t border-slate-800 text-rose-400">
                        <span>Sisa Saldo Belum Lunas</span>
                        <span class="font-mono" x-text="'Rp ' + Number(activeDoc?.balance_due || 0).toLocaleString('id-ID')"></span>
                    </div>
                </div>

                <!-- Riwayat Pembayaran Terdaftar -->
                <div>
                    <h4 class="font-black text-slate-900 uppercase text-[10px] tracking-wider mb-2">Riwayat Pembayaran Masuk / Keluar</h4>
                    <div class="space-y-2">
                        <template x-for="pay in activeDoc?.payments || []" :key="pay.id">
                            <div class="bg-slate-50 p-3 rounded-xl border border-slate-200 flex items-center justify-between">
                                <div>
                                    <span class="font-bold text-slate-900 block font-mono" x-text="pay.payment_number"></span>
                                    <span class="text-[10px] text-slate-500" x-text="pay.payment_date + ' via ' + pay.payment_method"></span>
                                </div>
                                <span class="font-mono font-black text-emerald-600" x-text="'Rp ' + Number(pay.amount).toLocaleString('id-ID')"></span>
                            </div>
                        </template>
                        <template x-if="!activeDoc?.payments || activeDoc.payments.length === 0">
                            <p class="text-slate-400 italic">Belum ada riwayat pembayaran tercatat.</p>
                        </template>
                    </div>
                </div>
            </div>

            <div class="p-4 border-t border-slate-100 bg-slate-50 flex items-center justify-end">
                <button @click="drawerOpen = false" class="px-5 py-2 rounded-full border border-slate-200 text-xs font-bold text-slate-600 hover:bg-white transition" x-text="lang === 'zh' ? '关闭' : 'Tutup'">
                    Tutup
                </button>
            </div>
        </div>
    </div>

</div>

<!-- Alpine Logic for Finance Hub -->
<script>
function erpFinanceManager() {
    return {
        tab: '{{ $tab }}',
        searchQuery: '{{ $search ?? '' }}',
        statusFilter: '{{ $statusFilter ?? 'all' }}',
        modalInvoiceOpen: false,
        modalBillCreateOpen: false,
        modalPaymentOpen: false,
        drawerOpen: false,
        activeDoc: null,
        selectedInvoiceForPay: null,
        paymentDirection: 'in',
        payAmount: 0,

        formatNumberWithDots(val) {
            return typeof window.formatNumberWithDots === 'function' ? window.formatNumberWithDots(val) : (val ? Number(String(val).replace(/\D/g, '')).toLocaleString('id-ID') : '');
        },
        parseNumberFromDots(val) {
            return typeof window.parseNumberFromDots === 'function' ? window.parseNumberFromDots(val) : (val ? parseInt(String(val).replace(/\D/g, '') || 0, 10) : 0);
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

        matchesInvoiceRow(haystack, status, isOverdue) {
            const hasSearch = !!(this.searchQuery && this.searchQuery.trim() !== '');
            const matchesSearch = !hasSearch || haystack.includes(this.searchQuery.toLowerCase());
            
            let matchesStatus = (this.statusFilter === 'all');
            if (!matchesStatus) {
                if (this.statusFilter === 'overdue') {
                    matchesStatus = isOverdue;
                } else {
                    matchesStatus = (status === this.statusFilter);
                }
            }
            return matchesSearch && matchesStatus;
        },

        matchesBillRow(haystack, status, isOverdue) {
            const hasSearch = !!(this.searchQuery && this.searchQuery.trim() !== '');
            const matchesSearch = !hasSearch || haystack.includes(this.searchQuery.toLowerCase());
            
            let matchesStatus = (this.statusFilter === 'all');
            if (!matchesStatus) {
                if (this.statusFilter === 'overdue') {
                    matchesStatus = isOverdue;
                } else {
                    matchesStatus = (status === this.statusFilter);
                }
            }
            return matchesSearch && matchesStatus;
        },

        matchesCashflowRow(haystack, paymentType) {
            const hasSearch = !!(this.searchQuery && this.searchQuery.trim() !== '');
            const matchesSearch = !hasSearch || haystack.includes(this.searchQuery.toLowerCase());
            
            let matchesType = (this.statusFilter === 'all');
            if (!matchesType) {
                matchesType = (paymentType === this.statusFilter);
            }
            return matchesSearch && matchesType;
        },

        allUnbilledOrders: @js($unbilledOrders),

        invoiceForm: {
            order_id: '',
            contact_id: '',
            selectedOrderIds: [],
            issue_date: '{{ date('Y-m-d') }}',
            due_date: '{{ date('Y-m-d', strtotime('+30 days')) }}',
            subtotal: 0,
            tax_amount: 0,
            total_amount: 0,
            notes: ''
        },

        billForm: {
            order_id: '',
            contact_id: '',
            subtotal: 0,
            tax_amount: 0,
            total_amount: 0
        },

        openInvoiceModal() {
            this.invoiceForm = {
                order_id: '',
                contact_id: '',
                selectedOrderIds: [],
                issue_date: '{{ date('Y-m-d') }}',
                due_date: '{{ date('Y-m-d', strtotime('+30 days')) }}',
                subtotal: 0,
                tax_amount: 0,
                total_amount: 0,
                notes: ''
            };
            this.modalInvoiceOpen = true;
        },

        openBillModal() {
            this.billForm = { order_id: '', contact_id: '', subtotal: 0, tax_amount: 0, total_amount: 0 };
            this.modalBillCreateOpen = true;
        },

        openPaymentModal(doc, direction) {
            this.selectedInvoiceForPay = doc;
            this.paymentDirection = direction;
            this.payAmount = Number(doc.balance_due || doc.total_amount || 0);
            this.modalPaymentOpen = true;
        },

        openDetailsDrawer(doc) {
            this.activeDoc = doc;
            this.drawerOpen = true;
        },

        getFilteredOrders() {
            if (!this.invoiceForm.contact_id) return [];
            return this.allUnbilledOrders.filter(o => String(o.contact_id) === String(this.invoiceForm.contact_id));
        },

        onCustomerChanged() {
            this.invoiceForm.selectedOrderIds = [];
            this.invoiceForm.order_id = '';
            this.invoiceForm.subtotal = 0;
            this.invoiceForm.tax_amount = 0;
            this.invoiceForm.total_amount = 0;
            this.invoiceForm.notes = '';
        },

        toggleOrderSelection(ord) {
            const idNum = Number(ord.id);
            const idx = this.invoiceForm.selectedOrderIds.indexOf(idNum);
            if (idx > -1) {
                this.invoiceForm.selectedOrderIds.splice(idx, 1);
            } else {
                this.invoiceForm.selectedOrderIds.push(idNum);
            }
            this.recalcFromSelectedOrders();
        },

        isOrderChecked(ordId) {
            return this.invoiceForm.selectedOrderIds.includes(Number(ordId));
        },

        isAllOrdersSelected() {
            const filtered = this.getFilteredOrders();
            if (filtered.length === 0) return false;
            return filtered.every(o => this.invoiceForm.selectedOrderIds.includes(Number(o.id)));
        },

        toggleSelectAllOrders() {
            const filtered = this.getFilteredOrders();
            if (this.isAllOrdersSelected()) {
                this.invoiceForm.selectedOrderIds = [];
            } else {
                this.invoiceForm.selectedOrderIds = filtered.map(o => Number(o.id));
            }
            this.recalcFromSelectedOrders();
        },

        recalcFromSelectedOrders() {
            const selected = this.allUnbilledOrders.filter(o => this.invoiceForm.selectedOrderIds.includes(Number(o.id)));
            if (selected.length === 0) {
                this.invoiceForm.order_id = '';
                this.invoiceForm.subtotal = 0;
                this.invoiceForm.tax_amount = 0;
                this.invoiceForm.total_amount = 0;
                return;
            }
            this.invoiceForm.order_id = selected[0].id;
            let subtotal = 0;
            let tax = 0;
            let total = 0;
            let orderNumbers = [];

            selected.forEach(o => {
                subtotal += Number(o.subtotal || 0);
                tax += Number(o.tax_amount || 0);
                total += Number(o.total_amount || 0);
                orderNumbers.push(o.order_number);
            });

            this.invoiceForm.subtotal = subtotal;
            this.invoiceForm.tax_amount = tax;
            this.invoiceForm.total_amount = total;
            this.invoiceForm.notes = 'Invoice tagihan atas pesanan: ' + orderNumbers.join(', ');
        },

        formatDate(dateStr) {
            if (!dateStr) return '-';
            const parts = dateStr.split('T')[0].split('-');
            if (parts.length === 3) {
                return parts[2] + '/' + parts[1] + '/' + parts[0];
            }
            return dateStr;
        },

        onPoSelected(event) {
            const opt = event.target.selectedOptions[0];
            if (!opt || !opt.value) {
                this.billForm.order_id = '';
                return;
            }
            this.billForm.order_id = opt.value;
            this.billForm.contact_id = opt.dataset.contact;
            this.billForm.subtotal = Number(opt.dataset.subtotal || 0);
            this.billForm.tax_amount = Number(opt.dataset.tax || 0);
            this.billForm.total_amount = Number(opt.dataset.total || 0);
        },

        recalcInvoiceTotal() {
            this.invoiceForm.total_amount = Number(this.invoiceForm.subtotal || 0) + Number(this.invoiceForm.tax_amount || 0);
        },

        recalcBillTotal() {
            this.billForm.total_amount = Number(this.billForm.subtotal || 0) + Number(this.billForm.tax_amount || 0);
        }
    }
}
</script>
@endsection
