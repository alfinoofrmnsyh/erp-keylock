@extends('layouts.erp')

@section('title', 'Laporan & Rekapitulasi - Keylock Indonesia')

@section('breadcrumbs')
    <a href="{{ route('erp.index') }}" class="hover:text-slate-800">Home</a>
    <span>›</span>
    <span class="text-slate-400">Operasional ERP</span>
    <span>›</span>
    <span class="text-slate-800 font-semibold" x-text="lang === 'zh' ? '业务报表与下载' : 'Laporan & Rekapitulasi'">Laporan & Rekapitulasi</span>
@endsection

@section('content')
<div x-data="{
    activeTab: '{{ $tab }}',
    filterStartDate: '{{ $startDate }}',
    filterEndDate: '{{ $endDate }}',
    filterSalesId: '{{ request('sales_id', '') }}',
    isDownloading: false,

    switchTab(newTab) {
        let url = `{{ route('erp.reports.index') }}?tab=${newTab}&start_date=${this.filterStartDate}&end_date=${this.filterEndDate}`;
        if (newTab === 'incentive' && this.filterSalesId) {
            url += `&sales_id=${this.filterSalesId}`;
        }
        window.location.href = url;
    },

    applyFilter() {
        let url = `{{ route('erp.reports.index') }}?tab=${this.activeTab}&start_date=${this.filterStartDate}&end_date=${this.filterEndDate}`;
        if (this.activeTab === 'incentive' && this.filterSalesId) {
            url += `&sales_id=${this.filterSalesId}`;
        }
        window.location.href = url;
    },

    resetFilter() {
        this.filterStartDate = '{{ now()->startOfMonth()->toDateString() }}';
        this.filterEndDate = '{{ now()->endOfMonth()->toDateString() }}';
        this.filterSalesId = '';
        this.applyFilter();
    },

    exportCsv() {
        let url = `{{ route('erp.reports.export') }}?tab=${this.activeTab}&start_date=${this.filterStartDate}&end_date=${this.filterEndDate}`;
        if (this.activeTab === 'incentive' && this.filterSalesId) {
            url += `&sales_id=${this.filterSalesId}`;
        }
        window.location.href = url;
    },

    openPrintWindow() {
        let url = `{{ route('erp.reports.print') }}?tab=${this.activeTab}&start_date=${this.filterStartDate}&end_date=${this.filterEndDate}`;
        if (this.activeTab === 'incentive' && this.filterSalesId) {
            url += `&sales_id=${this.filterSalesId}`;
        }
        window.open(url, '_blank');
    }
}" class="space-y-6">

    <!-- ========================================================================= -->
    <!-- 1. PAGE HEADER                                                            -->
    <!-- ========================================================================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2.5">
                <div class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center font-black">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight" x-text="lang === 'zh' ? '业务报表与数据中心' : 'Pusat Laporan & Rekapitulasi'">
                    Pusat Laporan & Rekapitulasi
                </h2>
            </div>
            <p class="text-xs text-slate-500 mt-1" x-text="lang === 'zh' ? '根据角色权限查看、下载并打印销售表现、车队运营与所有者财务报表。' : 'Unduh dan cetak rekapitulasi data penjualan, armada operasional, dan finansial owner sesuai hak akses role.'">
                Unduh dan cetak rekapitulasi data penjualan, armada operasional, dan finansial owner sesuai hak akses role.
            </p>
        </div>

        <!-- Export Action Buttons -->
        <div class="flex flex-wrap items-center gap-2.5">
            <template x-if="activeTab !== 'access'">
                <div class="flex items-center space-x-2">
                    <!-- Download CSV / Excel -->
                    <button type="button" 
                            @click="exportCsv()" 
                            class="bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold px-4 py-2.5 rounded-full shadow-md hover:shadow-lg text-xs transition flex items-center space-x-2 active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span x-text="lang === 'zh' ? '下载 Excel / CSV' : 'Unduh CSV / Excel'">Unduh CSV / Excel</span>
                    </button>

                    <!-- Print / PDF -->
                    <button type="button" 
                            @click="openPrintWindow()" 
                            class="bg-slate-900 hover:bg-slate-800 text-white font-extrabold px-4 py-2.5 rounded-full shadow-md hover:shadow-lg text-xs transition flex items-center space-x-2 active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        <span x-text="lang === 'zh' ? '打印报表 / PDF' : 'Cetak PDF'">Cetak PDF</span>
                    </button>
                </div>
            </template>
        </div>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs font-bold flex items-center justify-between">
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- 2. PILL TABS BAR: SALES, OPERATIONAL, OWNER, INCENTIVE, ACCESS CONFIG     -->
    <!-- ========================================================================= -->
    <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
        
        <!-- Tab 1: Sales -->
        @if(in_array('sales', $allowedTabs))
            <button type="button" 
                    @click="switchTab('sales')" 
                    :class="activeTab === 'sales' ? 'bg-white text-slate-900 shadow-sm border border-slate-200/90 font-black' : 'bg-slate-100/80 text-slate-600 hover:bg-white hover:text-slate-900 border border-transparent font-bold'"
                    class="px-4 py-2.5 rounded-2xl text-xs transition flex items-center space-x-2 shrink-0">
                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                <span x-text="lang === 'zh' ? '销售与 CRM 报表' : 'Laporan Sales & CRM'">Laporan Sales & CRM</span>
            </button>
        @endif

        <!-- Tab 2: Operational -->
        @if(in_array('operational', $allowedTabs))
            <button type="button" 
                    @click="switchTab('operational')" 
                    :class="activeTab === 'operational' ? 'bg-white text-slate-900 shadow-sm border border-slate-200/90 font-black' : 'bg-slate-100/80 text-slate-600 hover:bg-white hover:text-slate-900 border border-transparent font-bold'"
                    class="px-4 py-2.5 rounded-2xl text-xs transition flex items-center space-x-2 shrink-0">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0zM13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1"></path></svg>
                <span x-text="lang === 'zh' ? '车队与运营报表' : 'Laporan Operasional & Unit'">Laporan Operasional & Unit</span>
            </button>
        @endif

        <!-- Tab 3: Owner -->
        @if(in_array('owner', $allowedTabs))
            <button type="button" 
                    @click="switchTab('owner')" 
                    :class="activeTab === 'owner' ? 'bg-white text-slate-900 shadow-sm border border-slate-200/90 font-black' : 'bg-slate-100/80 text-slate-600 hover:bg-white hover:text-slate-900 border border-transparent font-bold'"
                    class="px-4 py-2.5 rounded-2xl text-xs transition flex items-center space-x-2 shrink-0">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span x-text="lang === 'zh' ? '所有者财务综合' : 'Laporan Finansial Owner'">Laporan Finansial Owner</span>
            </button>
        @endif

        <!-- Tab 4: Sales Incentive (Owner & Admin) -->
        @if(in_array('incentive', $allowedTabs))
            <button type="button" 
                    @click="switchTab('incentive')" 
                    :class="activeTab === 'incentive' ? 'bg-white text-slate-900 shadow-sm border border-slate-200/90 font-black' : 'bg-slate-100/80 text-slate-600 hover:bg-white hover:text-slate-900 border border-transparent font-bold'"
                    class="px-4 py-2.5 rounded-2xl text-xs transition flex items-center space-x-2 shrink-0">
                <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                <span x-text="lang === 'zh' ? '销售提成与激励' : 'Laporan Insentif Sales'">Laporan Insentif Sales</span>
                <span class="px-1.5 py-0.2 rounded text-[9px] bg-rose-100 text-rose-800 font-extrabold">OWNER</span>
            </button>
        @endif

        <!-- Tab 5: Hak Akses Config (Admin only) -->
        @if(in_array('access', $allowedTabs))
            <button type="button" 
                    @click="switchTab('access')" 
                    :class="activeTab === 'access' ? 'bg-white text-slate-900 shadow-sm border border-slate-200/90 font-black' : 'bg-slate-100/80 text-slate-600 hover:bg-white hover:text-slate-900 border border-transparent font-bold'"
                    class="px-4 py-2.5 rounded-2xl text-xs transition flex items-center space-x-2 shrink-0">
                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                <span x-text="lang === 'zh' ? '报表权限配置' : 'Konfigurasi Hak Akses'">Konfigurasi Hak Akses</span>
                <span class="px-1.5 py-0.2 rounded text-[9px] bg-purple-100 text-purple-800 font-extrabold">ADMIN</span>
            </button>
        @endif

    </div>

    <!-- ========================================================================= -->
    <!-- 3. DATE RANGE FILTER BAR (FOR REPORT TABS)                                -->
    <!-- ========================================================================= -->
    @if($tab !== 'access')
        <div class="bg-white p-4 rounded-3xl border border-slate-200/80 shadow-xs flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center space-x-2 text-xs font-bold text-slate-700">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <span x-text="lang === 'zh' ? '报表统计区间:' : 'Periode Laporan:'">Periode Laporan:</span>
            </div>

            <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                @if($tab === 'incentive' && !empty($reportData['all_sales_users']))
                    <div class="flex items-center space-x-2">
                        <span class="text-xs text-slate-400" x-text="lang === 'zh' ? '业务员' : 'Sales Person'">Sales:</span>
                        <select x-model="filterSalesId" 
                                class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 text-xs font-semibold text-slate-800 outline-none focus:border-amber-400">
                            <option value="">Semua Sales Person</option>
                            @foreach($reportData['all_sales_users'] as $su)
                                <option value="{{ $su->id }}" {{ (string)$su->id === (string)request('sales_id') ? 'selected' : '' }}>{{ $su->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="flex items-center space-x-2">
                    <span class="text-xs text-slate-400" x-text="lang === 'zh' ? '从' : 'Dari'">Dari</span>
                    <input type="date" 
                           x-model="filterStartDate" 
                           class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 text-xs font-semibold text-slate-800 outline-none focus:border-amber-400">
                </div>
                <div class="flex items-center space-x-2">
                    <span class="text-xs text-slate-400" x-text="lang === 'zh' ? '至' : 'Sampai'">Sampai</span>
                    <input type="date" 
                           x-model="filterEndDate" 
                           class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 text-xs font-semibold text-slate-800 outline-none focus:border-amber-400">
                </div>

                <button type="button" 
                        @click="applyFilter()" 
                        class="px-4 py-1.5 rounded-xl bg-amber-400 hover:bg-yellow-300 text-slate-950 text-xs font-black transition active:scale-95 shadow-xs">
                    <span x-text="lang === 'zh' ? '筛选' : 'Terapkan'">Terapkan</span>
                </button>
                <button type="button" 
                        @click="resetFilter()" 
                        class="px-3.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold transition">
                    <span x-text="lang === 'zh' ? '重置' : 'Reset'">Reset</span>
                </button>
            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- 4. TAB 1 CONTENT: SALES & CRM REPORT                                      -->
    <!-- ========================================================================= -->
    @if($tab === 'sales')
        <div class="space-y-6">
            <!-- 4 KPI Metrics -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs">
                    <span class="text-[11px] font-bold text-slate-400 block mb-1" x-text="lang === 'zh' ? '报价单总数' : 'Total Penawaran'">Total Penawaran</span>
                    <p class="text-2xl font-black text-slate-900">{{ $reportData['total_quotations_count'] }}</p>
                    <span class="text-[11px] text-slate-500 font-semibold mt-1 block">
                        Rp {{ number_format($reportData['total_quotations_amount'], 0, ',', '.') }}
                    </span>
                </div>

                <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs">
                    <span class="text-[11px] font-bold text-slate-400 block mb-1" x-text="lang === 'zh' ? '成功成交单数 (Won)' : 'Deal Diterima (Won)'">Deal Diterima (Won)</span>
                    <p class="text-2xl font-black text-emerald-600">{{ $reportData['accepted_count'] }}</p>
                    <span class="text-[11px] text-emerald-700 font-semibold mt-1 block">
                        Rp {{ number_format($reportData['accepted_amount'], 0, ',', '.') }}
                    </span>
                </div>

                <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs">
                    <span class="text-[11px] font-bold text-slate-400 block mb-1" x-text="lang === 'zh' ? '报价成交转化率' : 'Closing Rate Penjualan'">Closing Rate Penjualan</span>
                    <p class="text-2xl font-black text-amber-500">{{ $reportData['closing_rate'] }}%</p>
                    <span class="text-[11px] text-slate-400 font-semibold mt-1 block" x-text="lang === 'zh' ? '成交数 / 报价总数' : 'Deal / Total Quotation'">Deal / Total Quotation</span>
                </div>

                <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs">
                    <span class="text-[11px] font-bold text-slate-400 block mb-1" x-text="lang === 'zh' ? '已确认订单营收' : 'Realisasi Revenue Order'">Realisasi Revenue Order</span>
                    <p class="text-2xl font-black text-blue-600">Rp {{ number_format($reportData['confirmed_revenue'], 0, ',', '.') }}</p>
                    <span class="text-[11px] text-slate-500 font-semibold mt-1 block">
                        {{ $reportData['confirmed_orders']->count() }} Kontrak Rental
                    </span>
                </div>
            </div>

            <!-- Quotations Table (Desktop & Mobile) -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-black text-slate-900" x-text="lang === 'zh' ? '报价与销售清单明细' : 'Rincian Penawaran & Transaksi Sales'">
                            Rincian Penawaran & Transaksi Sales
                        </h3>
                        <p class="text-xs text-slate-500" x-text="lang === 'zh' ? '包含报价单编号、客户名称、租期与总金额。' : 'Daftar penawaran harga periode terpilih beserta status konversinya.'">
                            Daftar penawaran harga periode terpilih beserta status konversinya.
                        </p>
                    </div>
                    <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-800 text-xs font-bold font-mono">
                        {{ $reportData['quotations']->count() }} items
                    </span>
                </div>

                <!-- Desktop Table -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-600 text-[11px] uppercase tracking-wider font-bold border-b border-slate-200">
                            <tr>
                                <th class="py-3 px-4 w-12 text-center">No</th>
                                <th class="py-3 px-4">No Penawaran</th>
                                <th class="py-3 px-4">Tanggal</th>
                                <th class="py-3 px-5">Pelanggan</th>
                                <th class="py-3 px-4">Sales Person</th>
                                <th class="py-3 px-3 text-center">Masa Sewa</th>
                                <th class="py-3 px-4 text-right">Total Nilai (IDR)</th>
                                <th class="py-3 px-4 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-800 font-medium">
                            @forelse($reportData['quotations'] as $idx => $q)
                                <tr class="hover:bg-amber-50/30 transition">
                                    <td class="py-3.5 px-4 text-center text-slate-400 font-mono">{{ $idx + 1 }}</td>
                                    <td class="py-3.5 px-4 font-mono font-bold text-blue-600">
                                        <a href="{{ route('erp.orders.index', ['search' => $q->order_number]) }}" class="hover:underline">
                                            {{ $q->order_number }}
                                        </a>
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-500">{{ $q->order_date?->format('d/m/Y') ?? '-' }}</td>
                                    <td class="py-3.5 px-5 font-bold text-slate-900">{{ $q->contact?->name ?? 'Pelanggan Umum' }}</td>
                                    <td class="py-3.5 px-4 text-slate-600">{{ $q->creator?->name ?? '-' }}</td>
                                    <td class="py-3.5 px-3 text-center">
                                        <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[10px] font-bold">
                                            {{ $q->rental_duration ?? 1 }} {{ $q->rental_duration_unit ?? 'Bulan' }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-bold font-mono text-slate-900">
                                        Rp {{ number_format((float) $q->total_amount, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        @if($q->status === 'accepted')
                                            <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-black uppercase">WON / ACCEPTED</span>
                                        @elseif($q->status === 'sent')
                                            <span class="px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-800 text-[10px] font-black uppercase">SENT</span>
                                        @elseif($q->status === 'rejected')
                                            <span class="px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-800 text-[10px] font-black uppercase">REJECTED</span>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 text-[10px] font-black uppercase">{{ $q->status }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-12 text-center text-slate-400">
                                        <p class="font-bold text-sm" x-text="lang === 'zh' ? '该时间段内无报价记录' : 'Belum ada data penawaran harga pada periode ini.'">
                                            Belum ada data penawaran harga pada periode ini.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards -->
                <div class="block md:hidden divide-y divide-slate-100 p-3 space-y-3">
                    @forelse($reportData['quotations'] as $q)
                        <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-200/70 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="font-mono text-xs font-black text-blue-600">{{ $q->order_number }}</span>
                                @if($q->status === 'accepted')
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[9px] font-black uppercase">WON</span>
                                @elseif($q->status === 'sent')
                                    <span class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 text-[9px] font-black uppercase">SENT</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full bg-slate-200 text-slate-700 text-[9px] font-black uppercase">{{ $q->status }}</span>
                                @endif
                            </div>
                            <p class="text-xs font-bold text-slate-900 leading-snug">{{ $q->contact?->name ?? 'Pelanggan Umum' }}</p>
                            <div class="flex items-center justify-between text-[11px] text-slate-500 pt-1 border-t border-slate-200/60">
                                <span>Sales: {{ $q->creator?->name ?? '-' }}</span>
                                <span class="font-mono font-black text-slate-900">Rp {{ number_format((float) $q->total_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-slate-400 py-6 text-xs">Belum ada data penawaran pada periode ini.</p>
                    @endforelse
                </div>
            </div>

            <!-- Sales Person Performance Table -->
            @if(!empty($reportData['sales_reps']))
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
                    <div class="p-5 border-b border-slate-100">
                        <h3 class="text-sm font-black text-slate-900" x-text="lang === 'zh' ? '业务员个人销售业绩' : 'Performa Individu Sales Person'">
                            Performa Individu Sales Person
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50 text-slate-600 text-[11px] uppercase tracking-wider font-bold border-b border-slate-200">
                                <tr>
                                    <th class="py-3 px-5">Nama Sales Person</th>
                                    <th class="py-3 px-4 text-center">Jumlah Penawaran</th>
                                    <th class="py-3 px-5 text-right">Total Nilai Penawaran</th>
                                    <th class="py-3 px-4 text-center">Deal Won</th>
                                    <th class="py-3 px-5 text-right">Total Revenue Won</th>
                                    <th class="py-3 px-4 text-center">Konversi %</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-800 font-medium">
                                @foreach($reportData['sales_reps'] as $rep)
                                    @php
                                        $repRate = $rep['quotations_count'] > 0 ? round(($rep['won_count'] / $rep['quotations_count']) * 100, 1) : 0;
                                    @endphp
                                    <tr class="hover:bg-amber-50/30 transition">
                                        <td class="py-3.5 px-5 font-bold text-slate-900 flex items-center space-x-2">
                                            <div class="w-6 h-6 rounded-full bg-slate-800 text-yellow-400 text-[10px] font-extrabold flex items-center justify-center">
                                                {{ strtoupper(substr($rep['name'], 0, 2)) }}
                                            </div>
                                            <span>{{ $rep['name'] }}</span>
                                        </td>
                                        <td class="py-3.5 px-4 text-center font-bold">{{ $rep['quotations_count'] }}</td>
                                        <td class="py-3.5 px-5 text-right font-mono">Rp {{ number_format($rep['quotations_total'], 0, ',', '.') }}</td>
                                        <td class="py-3.5 px-4 text-center font-bold text-emerald-600">{{ $rep['won_count'] }}</td>
                                        <td class="py-3.5 px-5 text-right font-mono font-bold text-emerald-700">Rp {{ number_format($rep['won_total'], 0, ',', '.') }}</td>
                                        <td class="py-3.5 px-4 text-center font-black">
                                            <span class="px-2.5 py-0.5 rounded-full {{ $repRate >= 50 ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                                {{ $repRate }}%
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- 5. TAB 2 CONTENT: OPERATIONAL & FLEET REPORT                              -->
    <!-- ========================================================================= -->
    @if($tab === 'operational')
        <div class="space-y-6">
            <!-- 4 KPI Metrics -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs">
                    <span class="text-[11px] font-bold text-slate-400 block mb-1" x-text="lang === 'zh' ? '总设备数' : 'Total Unit Armada'">Total Unit Armada</span>
                    <p class="text-2xl font-black text-slate-900">{{ $reportData['total_units'] }}</p>
                    <span class="text-[11px] text-slate-500 font-semibold mt-1 block">Aset Alat Berat / Forklift</span>
                </div>

                <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs">
                    <span class="text-[11px] font-bold text-slate-400 block mb-1" x-text="lang === 'zh' ? '设备出租率' : 'Tingkat Utilisasi Sewa'">Tingkat Utilisasi Sewa</span>
                    <p class="text-2xl font-black text-emerald-600">{{ $reportData['utilization_rate'] }}%</p>
                    <span class="text-[11px] text-emerald-700 font-semibold mt-1 block">
                        {{ $reportData['rented_units'] }} Unit Aktif Disewa
                    </span>
                </div>

                <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs">
                    <span class="text-[11px] font-bold text-slate-400 block mb-1" x-text="lang === 'zh' ? '待命可用设备' : 'Unit Standby / Tersedia'">Unit Standby / Tersedia</span>
                    <p class="text-2xl font-black text-blue-600">{{ $reportData['available_units'] }}</p>
                    <span class="text-[11px] text-slate-400 font-semibold mt-1 block">Siap disewa / di pool</span>
                </div>

                <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs">
                    <span class="text-[11px] font-bold text-slate-400 block mb-1" x-text="lang === 'zh' ? '出货单 / DO 派发' : 'Surat Jalan (DO) Terkirim'">Surat Jalan (DO) Terkirim</span>
                    <p class="text-2xl font-black text-amber-500">{{ $reportData['deliveries']->count() }}</p>
                    <span class="text-[11px] text-slate-500 font-semibold mt-1 block">
                        {{ $reportData['maintenance_units'] }} Unit Sedang Servis
                    </span>
                </div>
            </div>

            <!-- Fleet Inventory Table -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-black text-slate-900" x-text="lang === 'zh' ? '车队设备库存与使用状况' : 'Status Ketersediaan & Lokasi Unit Armada'">
                            Status Ketersediaan & Lokasi Unit Armada
                        </h3>
                        <p class="text-xs text-slate-500" x-text="lang === 'zh' ? '设备编号、工作小时数 (HM)、状态及所在项目现场。' : 'Daftar armada beserta status sewa, jam kerja (HM), dan lokasi terkini.'">
                            Daftar armada beserta status sewa, jam kerja (HM), dan lokasi terkini.
                        </p>
                    </div>
                    <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-800 text-xs font-bold font-mono">
                        {{ $reportData['assets']->count() }} units
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-600 text-[11px] uppercase tracking-wider font-bold border-b border-slate-200">
                            <tr>
                                <th class="py-3 px-4 w-12 text-center">No</th>
                                <th class="py-3 px-4">Kode Unit</th>
                                <th class="py-3 px-5">Nama & Model</th>
                                <th class="py-3 px-4">Kategori</th>
                                <th class="py-3 px-4">Serial Number</th>
                                <th class="py-3 px-4 text-center">Hour Meter</th>
                                <th class="py-3 px-4">Lokasi Saat Ini</th>
                                <th class="py-3 px-4 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-800 font-medium">
                            @foreach($reportData['assets'] as $idx => $asset)
                                <tr class="hover:bg-amber-50/30 transition">
                                    <td class="py-3.5 px-4 text-center text-slate-400 font-mono">{{ $idx + 1 }}</td>
                                    <td class="py-3.5 px-4 font-mono font-bold text-slate-900">{{ $asset->asset_code }}</td>
                                    <td class="py-3.5 px-5 font-bold text-slate-900">{{ $asset->item?->name ?? 'Unit Forklift' }}</td>
                                    <td class="py-3.5 px-4 text-slate-600">{{ $asset->item?->category ?? '-' }}</td>
                                    <td class="py-3.5 px-4 font-mono text-slate-500 text-[11px]">{{ $asset->serial_number ?? '-' }}</td>
                                    <td class="py-3.5 px-4 text-center font-mono font-bold">{{ $asset->hour_meter ?? 0 }} HM</td>
                                    <td class="py-3.5 px-4 text-slate-600 text-[11px]">{{ $asset->current_location ?? 'Pool Karawang' }}</td>
                                    <td class="py-3.5 px-4 text-center">
                                        @if($asset->status === 'rented')
                                            <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-black uppercase">DISEWA</span>
                                        @elseif(in_array($asset->status, ['available', 'standby']))
                                            <span class="px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-800 text-[10px] font-black uppercase">STANDBY</span>
                                        @elseif(in_array($asset->status, ['maintenance', 'service']))
                                            <span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[10px] font-black uppercase">SERVIS</span>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 text-[10px] font-black uppercase">{{ $asset->status }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Delivery Orders Log -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-black text-slate-900" x-text="lang === 'zh' ? '派工与发运单 (DO) 明细' : 'Log Pengiriman Surat Jalan (DO)'">
                            Log Pengiriman Surat Jalan (DO)
                        </h3>
                    </div>
                    <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-800 text-xs font-bold font-mono">
                        {{ $reportData['deliveries']->count() }} DO
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-600 text-[11px] uppercase tracking-wider font-bold border-b border-slate-200">
                            <tr>
                                <th class="py-3 px-4 w-12 text-center">No</th>
                                <th class="py-3 px-4">No Surat Jalan</th>
                                <th class="py-3 px-4">Tanggal Kirim</th>
                                <th class="py-3 px-5">Pelanggan</th>
                                <th class="py-3 px-4">No Order</th>
                                <th class="py-3 px-4">Driver / Sopir</th>
                                <th class="py-3 px-4">No Polisi</th>
                                <th class="py-3 px-4 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-800 font-medium">
                            @forelse($reportData['deliveries'] as $dIdx => $d)
                                <tr class="hover:bg-amber-50/30 transition">
                                    <td class="py-3.5 px-4 text-center text-slate-400 font-mono">{{ $dIdx + 1 }}</td>
                                    <td class="py-3.5 px-4 font-mono font-bold text-blue-600">{{ $d->delivery_number }}</td>
                                    <td class="py-3.5 px-4 text-slate-500">{{ $d->delivery_date?->format('d/m/Y') ?? '-' }}</td>
                                    <td class="py-3.5 px-5 font-bold text-slate-900">{{ $d->order?->contact?->name ?? '-' }}</td>
                                    <td class="py-3.5 px-4 font-mono text-slate-600">{{ $d->order?->order_number ?? '-' }}</td>
                                    <td class="py-3.5 px-4 text-slate-700">{{ $d->driver_name ?? '-' }}</td>
                                    <td class="py-3.5 px-4 font-mono text-slate-600">{{ $d->vehicle_number ?? '-' }}</td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-800 text-[10px] font-black uppercase">
                                            {{ $d->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-10 text-center text-slate-400 font-bold">
                                        Tidak ada pengiriman Surat Jalan pada periode ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- 6. TAB 3 CONTENT: OWNER FINANCIAL & PROFITABILITY REPORT                  -->
    <!-- ========================================================================= -->
    @if($tab === 'owner')
        <div class="space-y-6">
            <!-- 4 Financial Summary Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs">
                    <span class="text-[11px] font-bold text-slate-400 block mb-1" x-text="lang === 'zh' ? '总租赁营业额' : 'Total Omset Rental'">Total Omset Rental</span>
                    <p class="text-2xl font-black text-slate-900">Rp {{ number_format($reportData['total_revenue'], 0, ',', '.') }}</p>
                    <span class="text-[11px] text-slate-500 font-semibold mt-1 block">
                        {{ $reportData['orders']->count() }} Kontrak Terbit
                    </span>
                </div>

                <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs">
                    <span class="text-[11px] font-bold text-slate-400 block mb-1" x-text="lang === 'zh' ? '供应商成本 (HPP)' : 'Total HPP Vendor Subkon'">Total HPP Vendor Subkon</span>
                    <p class="text-2xl font-black text-rose-600">Rp {{ number_format($reportData['total_vendor_cost'], 0, ',', '.') }}</p>
                    <span class="text-[11px] text-slate-500 font-semibold mt-1 block">
                        Biaya Logistik: Rp {{ number_format($reportData['total_logistics_cost'], 0, ',', '.') }}
                    </span>
                </div>

                <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs">
                    <span class="text-[11px] font-bold text-slate-400 block mb-1" x-text="lang === 'zh' ? '毛利润 (Gross Profit)' : 'Estimasi Gross Profit'">Estimasi Gross Profit</span>
                    <p class="text-2xl font-black text-emerald-600">Rp {{ number_format($reportData['gross_profit'], 0, ',', '.') }}</p>
                    <span class="text-[11px] text-emerald-700 font-semibold mt-1 block">
                        Gross Margin: {{ $reportData['gross_margin_percent'] }}%
                    </span>
                </div>

                <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs">
                    <span class="text-[11px] font-bold text-slate-400 block mb-1" x-text="lang === 'zh' ? '实际现金流入 / 流出' : 'Realisasi Kas Masuk / Keluar'">Realisasi Kas Masuk / Keluar</span>
                    <p class="text-lg font-black text-blue-600 leading-tight">
                        +Rp {{ number_format($reportData['cash_in'], 0, ',', '.') }}
                    </p>
                    <span class="text-[11px] text-rose-600 font-semibold mt-0.5 block">
                        -Rp {{ number_format($reportData['cash_out'], 0, ',', '.') }} (Vendor)
                    </span>
                </div>
            </div>

            <!-- AR Aging Summary & AP Liabilities -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- AR Aging -->
                <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div>
                            <h4 class="text-xs font-black text-slate-900 uppercase tracking-wider" x-text="lang === 'zh' ? '应收账款账龄分析 (AR Aging)' : 'Analisis Umur Piutang Pelanggan (AR Aging)'">
                                Analisis Umur Piutang Pelanggan (AR Aging)
                            </h4>
                            <p class="text-[11px] text-slate-400">Total piutang invoice pelanggan yang belum lunas</p>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3 rounded-2xl bg-emerald-50/70 border border-emerald-100">
                            <span class="text-xs font-bold text-emerald-900">Belum Jatuh Tempo (Current)</span>
                            <span class="font-mono font-black text-emerald-800 text-xs">Rp {{ number_format($reportData['ar_aging_current'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 rounded-2xl bg-amber-50/70 border border-amber-100">
                            <span class="text-xs font-bold text-amber-900">Lewat Jatuh Tempo 1 - 30 Hari</span>
                            <span class="font-mono font-black text-amber-800 text-xs">Rp {{ number_format($reportData['ar_aging_30'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 rounded-2xl bg-orange-50/70 border border-orange-100">
                            <span class="text-xs font-bold text-orange-900">Lewat Jatuh Tempo 31 - 60 Hari</span>
                            <span class="font-mono font-black text-orange-800 text-xs">Rp {{ number_format($reportData['ar_aging_60'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 rounded-2xl bg-rose-50/70 border border-rose-100">
                            <span class="text-xs font-bold text-rose-900">Overdue > 60 Hari (Kritis)</span>
                            <span class="font-mono font-black text-rose-800 text-xs">Rp {{ number_format($reportData['ar_aging_over60'], 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- AP Liabilities -->
                <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div>
                            <h4 class="text-xs font-black text-slate-900 uppercase tracking-wider" x-text="lang === 'zh' ? '应付账款与供应商负债 (AP)' : 'Kewajiban Utang Vendor Subkon (AP)'">
                                Kewajiban Utang Vendor Subkon (AP)
                            </h4>
                            <p class="text-[11px] text-slate-400">Total tagihan vendor pengadaan unit yang belum dibayarkan</p>
                        </div>
                    </div>
                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200/80 text-center space-y-2">
                        <span class="text-xs font-bold text-slate-500 uppercase">Total Sisa Utang Vendor</span>
                        <p class="text-3xl font-black text-rose-600 font-mono">
                            Rp {{ number_format($reportData['total_ap_outstanding'], 0, ',', '.') }}
                        </p>
                        <p class="text-xs text-slate-400">Pastikan pelunasan vendor terjaga sesuai jatuh tempo kontrak.</p>
                    </div>
                </div>
            </div>

            <!-- Orders Profitability Table -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-100">
                    <h3 class="text-sm font-black text-slate-900" x-text="lang === 'zh' ? '每笔租赁合同利润明细' : 'Rincian Margin & Profit Tiap Kontrak Rental'">
                        Rincian Margin & Profit Tiap Kontrak Rental
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-600 text-[11px] uppercase tracking-wider font-bold border-b border-slate-200">
                            <tr>
                                <th class="py-3 px-4 w-12 text-center">No</th>
                                <th class="py-3 px-4">No Kontrak</th>
                                <th class="py-3 px-5">Pelanggan</th>
                                <th class="py-3 px-4 text-right">Nilai Kontrak</th>
                                <th class="py-3 px-4 text-right">HPP Vendor</th>
                                <th class="py-3 px-4 text-right">Biaya Kirim</th>
                                <th class="py-3 px-4 text-right">Gross Profit</th>
                                <th class="py-3 px-4 text-center">Margin %</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-800 font-medium">
                            @forelse($reportData['orders'] as $idx => $order)
                                @php
                                    $revenue = (float) $order->total_amount;
                                    $vendor = (float) $order->vendor_cost;
                                    $logistics = (float) ($order->logistics_cost ?: $order->shipping_fee);
                                    $profit = $revenue - $vendor - $logistics;
                                    $margin = $revenue > 0 ? round(($profit / $revenue) * 100, 1) : 0;
                                @endphp
                                <tr class="hover:bg-amber-50/30 transition">
                                    <td class="py-3.5 px-4 text-center text-slate-400 font-mono">{{ $idx + 1 }}</td>
                                    <td class="py-3.5 px-4 font-mono font-bold text-slate-900">{{ $order->order_number }}</td>
                                    <td class="py-3.5 px-5 font-bold text-slate-900">{{ $order->contact?->name ?? 'Pelanggan Umum' }}</td>
                                    <td class="py-3.5 px-4 text-right font-mono font-bold">Rp {{ number_format($revenue, 0, ',', '.') }}</td>
                                    <td class="py-3.5 px-4 text-right font-mono text-rose-600">Rp {{ number_format($vendor, 0, ',', '.') }}</td>
                                    <td class="py-3.5 px-4 text-right font-mono text-slate-600">Rp {{ number_format($logistics, 0, ',', '.') }}</td>
                                    <td class="py-3.5 px-4 text-right font-mono font-bold text-emerald-600">Rp {{ number_format($profit, 0, ',', '.') }}</td>
                                    <td class="py-3.5 px-4 text-center font-black">
                                        <span class="px-2.5 py-0.5 rounded-full {{ $margin >= 25 ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                            {{ $margin }}%
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-12 text-center text-slate-400 font-bold">
                                        Belum ada order rental pada periode ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- 6. TAB 4 CONTENT: SALES INCENTIVE AUDIT REPORT (OWNER & ADMIN)            -->
    <!-- ========================================================================= -->
    @if($tab === 'incentive')
        <div class="space-y-6">
            <!-- 4 Incentive Metric Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs">
                    <span class="text-[11px] font-bold text-slate-400 block mb-1" x-text="lang === 'zh' ? '总销售提成' : 'Total Insentif Komisi'">Total Insentif Komisi</span>
                    <p class="text-2xl font-black text-rose-600">Rp {{ number_format($reportData['total_incentive'], 0, ',', '.') }}</p>
                    <span class="text-[11px] text-slate-500 font-semibold mt-1 block">
                        {{ $reportData['total_deals'] }} Transaksi Won (Closing)
                    </span>
                </div>

                <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs">
                    <span class="text-[11px] font-bold text-slate-400 block mb-1" x-text="lang === 'zh' ? '成交订单总营业额' : 'Total Omset Closing'">Total Omset Closing</span>
                    <p class="text-2xl font-black text-slate-900">Rp {{ number_format($reportData['total_revenue'], 0, ',', '.') }}</p>
                    <span class="text-[11px] text-slate-500 font-semibold mt-1 block" x-text="lang === 'zh' ? '租赁与整机销售' : 'Rental & Penjualan Unit'">
                        Rental & Penjualan Unit
                    </span>
                </div>

                <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs">
                    <span class="text-[11px] font-bold text-slate-400 block mb-1" x-text="lang === 'zh' ? '提成发放状态' : 'Status Pencairan Komisi'">Status Pencairan Komisi</span>
                    <p class="text-lg font-black text-emerald-600 leading-tight">
                        +Rp {{ number_format($reportData['total_paid_incentive'], 0, ',', '.') }}
                    </p>
                    <span class="text-[11px] text-amber-600 font-semibold mt-0.5 block">
                        Menunggu: Rp {{ number_format($reportData['total_pending_incentive'], 0, ',', '.') }}
                    </span>
                </div>

                <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs">
                    <span class="text-[11px] font-bold text-slate-400 block mb-1" x-text="lang === 'zh' ? '提成冠军业务员' : 'Top Kontributor Sales'">Top Kontributor Sales</span>
                    <p class="text-xl font-black text-slate-900 truncate" title="{{ $reportData['top_sales_rep'] }}">
                        {{ $reportData['top_sales_rep'] }}
                    </p>
                    <span class="text-[11px] text-slate-400 font-semibold mt-1 block" x-text="lang === 'zh' ? '本期提成业绩第一' : 'Perolehan Komisi Tertinggi'">
                        Perolehan Komisi Tertinggi
                    </span>
                </div>
            </div>

            <!-- Permanent Sales Lead Attribution Policy Banner -->
            <div class="p-5 rounded-3xl bg-gradient-to-r from-slate-900 via-slate-800 to-indigo-950 text-white shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div class="flex items-start space-x-3.5">
                    <div class="w-10 h-10 rounded-2xl bg-amber-400 text-slate-950 flex items-center justify-center font-black shrink-0 shadow-md">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.3" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <div>
                        <div class="flex items-center space-x-2">
                            <h4 class="text-sm font-black text-white" x-text="lang === 'zh' ? '永久业绩与客户线索归属机制 (Owner Audit)' : 'Kebijakan Atribusi Komisi & Leads Permanen (Owner Audit)'">
                                Kebijakan Atribusi Komisi & Leads Permanen (Owner Audit)
                            </h4>
                            <span class="px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 text-[10px] font-black border border-emerald-500/30">AKTIF</span>
                        </div>
                        <p class="text-xs text-slate-300 mt-1 leading-relaxed" x-text="lang === 'zh' ? '当客户由特定业务员开拓（拥有 CRM 线索号），后续即使由管理员 (Admin) 代开报价单或合同，该客户产生的所有业务提成仍将自动归属于该业务员（在账号处于活跃状态下）。' : 'Pelanggan yang diperoleh oleh Sales PIC tertentu (tercatat nomor Leads CRM), seluruh transaksi berulang otomatis dialokasikan komisi ke Sales PIC tersebut selamanya selama akun masih aktif, meski penawaran dibuat oleh Admin.'">
                            Pelanggan yang diperoleh oleh Sales PIC tertentu (tercatat nomor Leads CRM), seluruh transaksi berulang otomatis dialokasikan komisi ke Sales PIC tersebut selamanya selama akun masih aktif, meski penawaran dibuat oleh Admin.
                        </p>
                    </div>
                </div>

                @if(!empty($reportData['selected_sales_id']))
                    <button type="button" 
                            @click="filterSalesId = ''; applyFilter()" 
                            class="inline-flex items-center space-x-1.5 px-4 py-2 rounded-2xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold transition shrink-0 border border-white/20">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                        <span>Hapus Filter Sales</span>
                    </button>
                @endif
            </div>

            <!-- Rekapitulasi Komisi Per Sales Person Table -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-3 bg-slate-50/50">
                    <div>
                        <h4 class="text-xs font-black text-slate-900 uppercase tracking-wider" x-text="lang === 'zh' ? '业务员提成汇总' : 'Rekapitulasi Komisi Per Sales Person'">
                            Rekapitulasi Komisi Per Sales Person
                        </h4>
                        <p class="text-[11px] text-slate-400" x-text="lang === 'zh' ? '按销售人员归属统计的总成交额、应计提成及回款到账情况' : 'Akumulasi total omset won, estimasi insentif, serta status pencairan per tenaga penjual'">
                            Akumulasi total omset won, estimasi insentif, serta status pencairan per tenaga penjual
                        </p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50 text-slate-500 font-extrabold uppercase text-[10px] tracking-wider border-b border-slate-200">
                                <th class="py-3 px-4 text-center w-12">No</th>
                                <th class="py-3 px-5">Sales Person</th>
                                <th class="py-3 px-4 text-center">Status Akun</th>
                                <th class="py-3 px-4 text-center">Deals Won</th>
                                <th class="py-3 px-4 text-right">Total Omset</th>
                                <th class="py-3 px-4 text-right">Estimasi Insentif</th>
                                <th class="py-3 px-4 text-right">Sudah Cair</th>
                                <th class="py-3 px-4 text-right">Menunggu Pelunasan</th>
                                <th class="py-3 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-800 font-medium">
                            @forelse($reportData['sales_reps'] as $sIdx => $rep)
                                <tr class="hover:bg-amber-50/30 transition {{ (string)$rep['sales_id'] === (string)$reportData['selected_sales_id'] ? 'bg-amber-50/70 font-semibold' : '' }}">
                                    <td class="py-3.5 px-4 text-center text-slate-400 font-mono">{{ $sIdx + 1 }}</td>
                                    <td class="py-3.5 px-5">
                                        <div class="flex items-center space-x-2.5">
                                            <div class="w-7 h-7 rounded-full bg-slate-900 text-white flex items-center justify-center font-black text-xs shrink-0">
                                                {{ strtoupper(substr($rep['name'], 0, 1)) }}
                                            </div>
                                            <span class="font-bold text-slate-900">{{ $rep['name'] }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        @if($rep['is_active'])
                                            <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-black">Aktif</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 text-[10px] font-bold">Non-aktif</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-black">
                                        <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-800 font-mono">
                                            {{ $rep['deals_count'] }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-mono font-bold text-slate-900">
                                        Rp {{ number_format($rep['total_revenue'], 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-mono font-black text-rose-600">
                                        Rp {{ number_format($rep['total_incentive'], 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-mono font-bold text-emerald-600">
                                        Rp {{ number_format($rep['paid_incentive'], 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-mono font-bold text-amber-600">
                                        Rp {{ number_format($rep['pending_incentive'], 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <button type="button" 
                                                @click="filterSalesId = '{{ $rep['sales_id'] }}'; applyFilter()" 
                                                class="px-2.5 py-1 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-bold transition">
                                            Filter Transaksi
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="py-12 text-center text-slate-400 font-bold">
                                        Belum ada data transaksi insentif pada periode yang dipilih.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if(!empty($reportData['sales_reps']))
                            <tfoot>
                                <tr class="bg-slate-50 font-black text-slate-900 border-t border-slate-200">
                                    <td colspan="4" class="py-3.5 px-5 text-right uppercase text-[10px] tracking-wider text-slate-500">
                                        TOTAL KESELURUHAN:
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-mono">
                                        Rp {{ number_format($reportData['total_revenue'], 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-mono text-rose-600">
                                        Rp {{ number_format($reportData['total_incentive'], 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-mono text-emerald-600">
                                        Rp {{ number_format($reportData['total_paid_incentive'], 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-mono text-amber-600">
                                        Rp {{ number_format($reportData['total_pending_incentive'], 0, ',', '.') }}
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>

            <!-- Rincian Transaksi & Hak Insentif Audit Table -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-3 bg-slate-50/50">
                    <div>
                        <h4 class="text-xs font-black text-slate-900 uppercase tracking-wider" x-text="lang === 'zh' ? '成交明细与提成审计' : 'Rincian Transaksi & Audit Hak Insentif'">
                            Rincian Transaksi & Audit Hak Insentif
                        </h4>
                        <p class="text-[11px] text-slate-400" x-text="lang === 'zh' ? '详尽核对每一笔成交订单、客户线索来源、提成归属业务员及发票回款状态' : 'Daftar audit seluruh order closing, asal kode leads, penerima hak insentif, dan status tagihan invoice'">
                            Daftar audit seluruh order closing, asal kode leads, penerima hak insentif, dan status tagihan invoice
                        </p>
                    </div>
                    <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-black">
                        {{ count($reportData['breakdown']) }} Data Ditemukan
                    </span>
                </div>

                <!-- Desktop Table -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50 text-slate-500 font-extrabold uppercase text-[10px] tracking-wider border-b border-slate-200">
                                <th class="py-3 px-4 text-center w-12">No</th>
                                <th class="py-3 px-4">No Transaksi / Tanggal</th>
                                <th class="py-3 px-5">Pelanggan & Leads CRM</th>
                                <th class="py-3 px-4">Sales PIC (Hak Insentif)</th>
                                <th class="py-3 px-4">Dibuat Oleh</th>
                                <th class="py-3 px-4 text-right">Nilai Omset</th>
                                <th class="py-3 px-3 text-center">Rate</th>
                                <th class="py-3 px-4 text-right">Nominal Insentif</th>
                                <th class="py-3 px-4 text-center">Status Tagihan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-800 font-medium">
                            @forelse($reportData['breakdown'] as $bIdx => $bItem)
                                <tr class="hover:bg-amber-50/30 transition">
                                    <td class="py-3.5 px-4 text-center text-slate-400 font-mono">{{ $bIdx + 1 }}</td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-mono font-bold text-slate-900">{{ $bItem['order']->order_number }}</div>
                                        <div class="text-[11px] text-slate-400">{{ date('d M Y', strtotime($bItem['order']->order_date)) }}</div>
                                    </td>
                                    <td class="py-3.5 px-5">
                                        <div class="font-bold text-slate-900">{{ $bItem['customer_name'] }}</div>
                                        @if($bItem['lead_code'] !== '-')
                                            <div class="flex items-center space-x-1 mt-0.5">
                                                <span class="text-[10px] font-mono text-indigo-700 bg-indigo-50 border border-indigo-100 px-1.5 py-0.2 rounded font-bold">
                                                    Lead: {{ $bItem['lead_code'] }}
                                                </span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-slate-900">{{ $bItem['sales_name'] }}</div>
                                        @if($bItem['is_pic_attributed'])
                                            <span class="text-[10px] font-bold text-blue-700 bg-blue-50 px-1.5 py-0.2 rounded inline-block mt-0.5">
                                                PIC Leads Customer
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="text-xs text-slate-700 font-semibold">{{ $bItem['created_by_name'] }}</div>
                                        @if($bItem['created_by_admin'])
                                            <span class="text-[10px] font-extrabold text-amber-800 bg-amber-100 px-1.5 py-0.2 rounded inline-block mt-0.5" title="Dibuat oleh Admin, hak komisi tetap ke Sales PIC">
                                                Via Admin (Hak Tetap)
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-mono font-bold text-slate-900">
                                        Rp {{ number_format($bItem['revenue'], 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-3 text-center font-mono font-bold text-slate-600">
                                        {{ $bItem['rate_percent'] }}%
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-mono font-black text-rose-600">
                                        Rp {{ number_format($bItem['incentive_nominal'], 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        @if($bItem['is_paid'])
                                            <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-black inline-flex items-center space-x-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                <span>Lunas (Cair)</span>
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 text-[10px] font-bold inline-flex items-center space-x-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                <span>Menunggu Tagihan</span>
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="py-12 text-center text-slate-400 font-bold">
                                        Tidak ada transaksi insentif yang cocok dengan kriteria filter.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card Layout -->
                <div class="md:hidden divide-y divide-slate-100 p-4 space-y-3">
                    @forelse($reportData['breakdown'] as $mbItem)
                        <div class="p-4 rounded-2xl bg-slate-50/70 border border-slate-200/80 space-y-2.5">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <div class="font-mono font-bold text-xs text-slate-900">{{ $mbItem['order']->order_number }}</div>
                                    <div class="text-[11px] text-slate-500 font-bold">{{ $mbItem['customer_name'] }}</div>
                                </div>
                                @if($mbItem['is_paid'])
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-black">Lunas (Cair)</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[10px] font-bold">Menunggu</span>
                                @endif
                            </div>

                            <div class="text-[11px] space-y-1 text-slate-600 border-t border-slate-200/60 pt-2">
                                <div class="flex justify-between">
                                    <span class="text-slate-400">Sales PIC:</span>
                                    <span class="font-bold text-slate-800">{{ $mbItem['sales_name'] }}</span>
                                </div>
                                @if($mbItem['created_by_admin'])
                                    <div class="flex justify-between text-[10px] text-amber-800">
                                        <span>Dibuat:</span>
                                        <span class="font-semibold">Via Admin (Hak Tetap ke Sales)</span>
                                    </div>
                                @endif
                                <div class="flex justify-between">
                                    <span class="text-slate-400">Omset Transaksi:</span>
                                    <span class="font-mono font-bold text-slate-900">Rp {{ number_format($mbItem['revenue'], 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-400">Komisi ({{ $mbItem['rate_percent'] }}%):</span>
                                    <span class="font-mono font-black text-rose-600">Rp {{ number_format($mbItem['incentive_nominal'], 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-slate-400 text-xs font-bold">
                            Tidak ada transaksi insentif pada periode ini.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- 7. TAB 5 CONTENT: ROLE-BASED ACCESS CONFIGURATION (ADMIN ONLY)            -->
    <!-- ========================================================================= -->
    @if($tab === 'access' && auth()->user()?->isAdmin())
        <div class="space-y-6">
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
                
                <!-- Notice Banner: Synchronized with Menu Management -->
                <div class="mb-6 p-4 rounded-2xl bg-indigo-50/70 border border-indigo-200/70 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 rounded-xl bg-indigo-600 text-white flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                        </div>
                        <div>
                            <h5 class="text-xs font-black text-slate-900" x-text="lang === 'zh' ? '与系统菜单权限同步管理' : 'Tersinkronisasi Penuh Dengan Menu Management'">
                                Tersinkronisasi Penuh Dengan Menu Management
                            </h5>
                            <p class="text-[11px] text-slate-600 mt-0.5" x-text="lang === 'zh' ? '选项卡权限与 Menu Management > Hak Akses Role (TAB LAPORAN & REKAPITULASI) 保持双向实时同步。' : 'Pengaturan tab laporan di sini terhubung langsung dengan Menu Management di grup TAB LAPORAN & REKAPITULASI seperti halnya Konten Website CMS.'">
                                Pengaturan tab laporan di sini terhubung langsung dengan Menu Management di grup TAB LAPORAN & REKAPITULASI seperti halnya Konten Website CMS.
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('erp.system-menus.index', ['tab' => 'access']) }}" 
                       class="inline-flex items-center space-x-1.5 px-4 py-2 rounded-xl bg-white border border-indigo-200 text-indigo-700 hover:bg-indigo-50 text-xs font-bold transition shadow-2xs shrink-0">
                        <span x-text="lang === 'zh' ? '打开菜单管理' : 'Buka Menu Management'">Buka Menu Management</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                    </a>
                </div>

                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-100 pb-5 mb-6">
                    <div>
                        <h3 class="text-base font-black text-slate-900" x-text="lang === 'zh' ? '报表选项卡访问权限配置' : 'Konfigurasi Hak Akses Tab Laporan'">
                            Konfigurasi Hak Akses Tab Laporan
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5" x-text="lang === 'zh' ? '勾选或取消勾选各个角色可访问的报表选项卡。仅拥有权限的角色可在系统中查看和导出对应报表。' : 'Tentukan role mana saja yang diizinkan membuka dan mendownload masing-masing tab laporan. Konfigurasi tersimpan secara dinamis.'">
                            Tentukan role mana saja yang diizinkan membuka dan mendownload masing-masing tab laporan. Konfigurasi tersimpan secara dinamis.
                        </p>
                    </div>
                </div>

                <form action="{{ route('reports.access_config') }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
                        
                        <!-- Card 1: Tab Sales Permissions -->
                        <div class="p-5 rounded-2xl bg-amber-50/40 border border-amber-200/80 space-y-4">
                            <div class="flex items-center space-x-2.5">
                                <div class="w-8 h-8 rounded-xl bg-amber-400 text-slate-950 flex items-center justify-center font-black">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                </div>
                                <div>
                                    <h4 class="text-xs font-black text-slate-900 uppercase">Tab Laporan Sales</h4>
                                    <p class="text-[10px] text-slate-500">Penawaran, Leads, Closing Rate</p>
                                </div>
                            </div>
                            <div class="space-y-2 pt-2 border-t border-amber-200/60">
                                @foreach($roles as $r)
                                    @php
                                        $isChecked = in_array($r->id, $permissions['sales'] ?? []);
                                        $isAdminRole = ($r->id === 1);
                                    @endphp
                                    <label class="flex items-center space-x-3 p-2 rounded-xl hover:bg-white transition cursor-pointer">
                                        <input type="checkbox" 
                                               name="sales_roles[]" 
                                               value="{{ $r->id }}" 
                                               {{ $isChecked ? 'checked' : '' }}
                                               {{ $isAdminRole ? 'checked disabled' : '' }}
                                               class="w-4 h-4 rounded text-amber-500 focus:ring-amber-400 border-slate-300">
                                        <div class="flex-1">
                                            <span class="text-xs font-bold text-slate-800">{{ $r->label }}</span>
                                            @if($isAdminRole)
                                                <span class="ml-1 text-[9px] bg-slate-900 text-white font-extrabold px-1.5 py-0.2 rounded">WAJIB</span>
                                            @endif
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Card 2: Tab Operational Permissions -->
                        <div class="p-5 rounded-2xl bg-blue-50/40 border border-blue-200/80 space-y-4">
                            <div class="flex items-center space-x-2.5">
                                <div class="w-8 h-8 rounded-xl bg-blue-600 text-white flex items-center justify-center font-black">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0zM13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1"></path></svg>
                                </div>
                                <div>
                                    <h4 class="text-xs font-black text-slate-900 uppercase">Tab Laporan Operasional</h4>
                                    <p class="text-[10px] text-slate-500">Armada, DO, Hour Meter, Servis</p>
                                </div>
                            </div>
                            <div class="space-y-2 pt-2 border-t border-blue-200/60">
                                @foreach($roles as $r)
                                    @php
                                        $isChecked = in_array($r->id, $permissions['operational'] ?? []);
                                        $isAdminRole = ($r->id === 1);
                                    @endphp
                                    <label class="flex items-center space-x-3 p-2 rounded-xl hover:bg-white transition cursor-pointer">
                                        <input type="checkbox" 
                                               name="operational_roles[]" 
                                               value="{{ $r->id }}" 
                                               {{ $isChecked ? 'checked' : '' }}
                                               {{ $isAdminRole ? 'checked disabled' : '' }}
                                               class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500 border-slate-300">
                                        <div class="flex-1">
                                            <span class="text-xs font-bold text-slate-800">{{ $r->label }}</span>
                                            @if($isAdminRole)
                                                <span class="ml-1 text-[9px] bg-slate-900 text-white font-extrabold px-1.5 py-0.2 rounded">WAJIB</span>
                                            @endif
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Card 3: Tab Owner Permissions -->
                        <div class="p-5 rounded-2xl bg-emerald-50/40 border border-emerald-200/80 space-y-4">
                            <div class="flex items-center space-x-2.5">
                                <div class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-black">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <div>
                                    <h4 class="text-xs font-black text-slate-900 uppercase">Tab Laporan Owner</h4>
                                    <p class="text-[10px] text-slate-500">Gross Margin, AR Aging, AP Utang</p>
                                </div>
                            </div>
                            <div class="space-y-2 pt-2 border-t border-emerald-200/60">
                                @foreach($roles as $r)
                                    @php
                                        $isChecked = in_array($r->id, $permissions['owner'] ?? []);
                                        $isAdminRole = ($r->id === 1);
                                    @endphp
                                    <label class="flex items-center space-x-3 p-2 rounded-xl hover:bg-white transition cursor-pointer">
                                        <input type="checkbox" 
                                               name="owner_roles[]" 
                                               value="{{ $r->id }}" 
                                               {{ $isChecked ? 'checked' : '' }}
                                               {{ $isAdminRole ? 'checked disabled' : '' }}
                                               class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300">
                                        <div class="flex-1">
                                            <span class="text-xs font-bold text-slate-800">{{ $r->label }}</span>
                                            @if($isAdminRole)
                                                <span class="ml-1 text-[9px] bg-slate-900 text-white font-extrabold px-1.5 py-0.2 rounded">WAJIB</span>
                                            @endif
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Card 4: Tab Incentive Permissions (Owner & Admin) -->
                        <div class="p-5 rounded-2xl bg-rose-50/40 border border-rose-200/80 space-y-4">
                            <div class="flex items-center space-x-2.5">
                                <div class="w-8 h-8 rounded-xl bg-rose-600 text-white flex items-center justify-center font-black">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                </div>
                                <div>
                                    <h4 class="text-xs font-black text-slate-900 uppercase">Tab Insentif Sales</h4>
                                    <p class="text-[10px] text-slate-500">Audit Komisi & Leads PIC (Owner)</p>
                                </div>
                            </div>
                            <div class="space-y-2 pt-2 border-t border-rose-200/60">
                                @foreach($roles as $r)
                                    @php
                                        $isChecked = in_array($r->id, $permissions['incentive'] ?? []);
                                        $isAdminRole = ($r->id === 1);
                                    @endphp
                                    <label class="flex items-center space-x-3 p-2 rounded-xl hover:bg-white transition cursor-pointer">
                                        <input type="checkbox" 
                                               name="incentive_roles[]" 
                                               value="{{ $r->id }}" 
                                               {{ $isChecked ? 'checked' : '' }}
                                               {{ $isAdminRole ? 'checked disabled' : '' }}
                                               class="w-4 h-4 rounded text-rose-600 focus:ring-rose-500 border-slate-300">
                                        <div class="flex-1">
                                            <span class="text-xs font-bold text-slate-800">{{ $r->label }}</span>
                                            @if($isAdminRole)
                                                <span class="ml-1 text-[9px] bg-slate-900 text-white font-extrabold px-1.5 py-0.2 rounded">WAJIB</span>
                                            @endif
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                    </div>

                    <!-- Footer Action Buttons -->
                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                        <button type="submit" 
                                class="bg-amber-400 hover:bg-yellow-300 text-slate-950 font-black px-6 py-2.5 rounded-full shadow-md hover:shadow-lg text-xs transition active:scale-95 flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            <span x-text="lang === 'zh' ? '保存权限配置' : 'Simpan Konfigurasi Hak Akses'">Simpan Konfigurasi Hak Akses</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>
@endsection
