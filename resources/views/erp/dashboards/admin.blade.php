<!-- ========================================================================= -->
<!-- DASHBOARD ADMINISTRATOR & ADMIN OPERASIONAL (MATCHING IMAGE 3 REFERENCE)  -->
<!-- ========================================================================= -->
<div class="space-y-6" x-data="{ bizGraphFilter: 'total' }">

    <!-- Header Greeting -->
    <div class="hidden md:flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">
                <span x-text="lang === 'zh' ? '欢迎回来，' : 'Selamat Datang, '">Selamat Datang, </span>{{ auth()->user()->name ?? 'Admin Keylock' }}
            </h1>
            <p class="text-xs text-slate-500 mt-0.5" x-text="lang === 'zh' ? '监控整个业务流程并在仪表板上管理系统' : 'Pantau seluruh proses bisnis dan kelola sistem pada dashboard'">
                Pantau seluruh proses bisnis dan kelola sistem pada dashboard
            </p>
        </div>

        <!-- Top Date Indicator -->
        <div class="flex items-center space-x-3">
            <div class="px-3.5 py-1.5 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-700 shadow-2xs flex items-center space-x-2">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <span>1 - {{ now()->endOfMonth()->format('d M Y') }}</span>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 4 TOP KPI CARDS (IMAGE 3)                                                 -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-5">
        
        <!-- Card 1: Total Pendapatan (Blue Gradient Card) -->
        <div class="bg-gradient-to-br from-[#2563eb] to-[#1d4ed8] p-4 sm:p-6 rounded-2xl sm:rounded-3xl text-white shadow-md flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2 sm:mb-3">
                <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-white/15 flex items-center justify-center backdrop-blur-xs font-black">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <span class="px-1.5 sm:px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-extrabold bg-white/20 text-white flex items-center space-x-0.5">
                    <span>↗</span>
                    <span>+12.4%</span>
                </span>
            </div>
            <div>
                <span class="text-[10px] sm:text-[11px] font-bold text-blue-100 uppercase tracking-wider block truncate" x-text="lang === 'zh' ? '总营收' : 'Total Pendapatan'">Total Pendapatan</span>
                <h3 class="text-xs sm:text-base font-black text-white tracking-tight mt-0.5 truncate">
                    Rp {{ number_format($totalRevenueMonth, 0, ',', '.') }}
                </h3>
                <span class="text-[10px] sm:text-[11px] font-semibold text-blue-200 mt-0.5 sm:mt-1 block">{{ now()->format('M Y') }}</span>
            </div>
        </div>

        <!-- Card 2: Total Order -->
        <div class="bg-white p-4 sm:p-6 rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2 sm:mb-3">
                <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-black">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                </div>
                <span class="px-1.5 sm:px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-extrabold bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center space-x-0.5">
                    <span>↗</span>
                    <span>+5.6%</span>
                </span>
            </div>
            <div>
                <span class="text-[10px] sm:text-[11px] font-bold text-slate-400 uppercase tracking-wider block truncate" x-text="lang === 'zh' ? '总订单量' : 'Total Order'">Total Order</span>
                <h3 class="text-base sm:text-2xl font-black text-slate-900 tracking-tight mt-0.5 truncate">
                    {{ $totalOrdersMonth }}
                </h3>
                <span class="text-[10px] sm:text-[11px] font-semibold text-slate-400 mt-0.5 sm:mt-1 block">{{ now()->format('M Y') }}</span>
            </div>
        </div>

        <!-- Card 3: Piutang Customer -->
        <div class="bg-white p-4 sm:p-6 rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2 sm:mb-3">
                <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-black">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                </div>
            </div>
            <div>
                <span class="text-[10px] sm:text-[11px] font-bold text-slate-400 uppercase tracking-wider block truncate" x-text="lang === 'zh' ? '应收账款 (客户)' : 'Piutang Customer'">Piutang Customer</span>
                <h3 class="text-base sm:text-2xl font-black text-slate-900 tracking-tight mt-0.5 truncate">
                    Rp {{ number_format($totalReceivables, 0, ',', '.') }}
                </h3>
                <span class="text-[10px] sm:text-[11px] font-bold text-rose-600 mt-0.5 sm:mt-1 block truncate">{{ $overdueReceivablesCount }} jatuh tempo</span>
            </div>
        </div>

        <!-- Card 4: Utang Vendor -->
        <div class="bg-white p-4 sm:p-6 rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2 sm:mb-3">
                <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-slate-100 text-slate-700 flex items-center justify-center font-black">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"></path></svg>
                </div>
            </div>
            <div>
                <span class="text-[10px] sm:text-[11px] font-bold text-slate-400 uppercase tracking-wider block truncate" x-text="lang === 'zh' ? '应付账款 (供应商)' : 'Utang Vendor'">Utang Vendor</span>
                <h3 class="text-base sm:text-2xl font-black text-slate-900 tracking-tight mt-0.5 truncate">
                    Rp {{ number_format($totalPayables, 0, ',', '.') }}
                </h3>
                <span class="text-[10px] sm:text-[11px] font-bold text-rose-600 mt-0.5 sm:mt-1 block truncate">{{ $overduePayablesCount }} jatuh tempo</span>
            </div>
        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- MIDDLE SECTION: GRAFIK BISNIS & UTILISASI ARMADA (IMAGE 3)               -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- 1. Grafik Bisnis (7 cols) -->
        <div class="lg:col-span-7 bg-white p-6 rounded-3xl border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-black text-slate-900" x-text="lang === 'zh' ? '业务营收与支出图表' : 'Grafik Bisnis'">Grafik Bisnis</h3>
                    <div class="flex items-center space-x-2">
                        <div class="flex items-center bg-slate-100 p-0.5 rounded-xl text-xs font-bold text-slate-600">
                            <button type="button" @click="bizGraphFilter = 'sales'" :class="bizGraphFilter === 'sales' ? 'bg-white text-slate-900 shadow-2xs' : 'text-slate-500'" class="px-2 py-1 rounded-lg transition">Sales</button>
                            <button type="button" @click="bizGraphFilter = 'rental'" :class="bizGraphFilter === 'rental' ? 'bg-white text-slate-900 shadow-2xs' : 'text-slate-500'" class="px-2 py-1 rounded-lg transition">Rental</button>
                            <button type="button" @click="bizGraphFilter = 'total'" :class="bizGraphFilter === 'total' ? 'bg-white text-slate-900 shadow-2xs' : 'text-slate-500'" class="px-2 py-1 rounded-lg transition">Total</button>
                        </div>
                        <a href="{{ route('erp.orders.index') }}" class="w-8 h-8 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-600 flex items-center justify-center transition border border-slate-200">
                            ↗
                        </a>
                    </div>
                </div>

                <div class="mb-5">
                    <div class="flex items-baseline space-x-2">
                        <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                            Rp {{ number_format($sixMonthTotalRev, 0, ',', '.') }}
                        </h2>
                        <span class="text-xs font-semibold text-slate-400">pendapatan 6 bulan terakhir</span>
                    </div>
                    <div class="flex items-center space-x-4 text-xs font-bold text-slate-500 mt-2">
                        <span class="flex items-center space-x-1.5"><span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span><span>Pendapatan</span></span>
                        <span class="flex items-center space-x-1.5"><span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span><span>Biaya</span></span>
                    </div>
                </div>

                <!-- 6-month paired bar chart (Image 3) -->
                <div class="h-60 w-full pt-6 flex items-end justify-between px-3">
                    @foreach($sixMonthData as $d)
                    <div class="flex flex-col items-center space-y-2 group">
                        <div class="flex items-end space-x-1.5">
                            <!-- Bar Pendapatan (Purple/Indigo) -->
                            <div class="w-3.5 sm:w-4.5 bg-indigo-600 rounded-t-lg transition-all duration-300 group-hover:scale-105 shadow-2xs" 
                                 style="height: {{ max(20, $d['pendapatan_height'] * 1.8) }}px;"
                                 title="Pendapatan: Rp {{ number_format($d['pendapatan'] / 1000000, 0) }} Jt"></div>
                            <!-- Bar Biaya (Yellow/Amber) -->
                            <div class="w-3.5 sm:w-4.5 bg-amber-400 rounded-t-lg transition-all duration-300 group-hover:scale-105 shadow-2xs" 
                                 style="height: {{ max(16, $d['biaya_height'] * 1.8) }}px;"
                                 title="Biaya: Rp {{ number_format($d['biaya'] / 1000000, 0) }} Jt"></div>
                        </div>
                        <span class="text-[10px] font-bold text-slate-500">{{ $d['month'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- 2. Utilisasi Armada Donut Chart (5 cols) -->
        <div class="lg:col-span-5 bg-white p-6 rounded-3xl border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-black text-slate-900" x-text="lang === 'zh' ? '设备利用率' : 'Utilisasi Armada'">Utilisasi Armada</h3>
                    <a href="{{ route('erp.fleet.index') }}" class="w-8 h-8 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-600 flex items-center justify-center transition border border-slate-200">
                        ↗
                    </a>
                </div>

                <!-- Donut Chart with Center Text (Image 3: 68% Utilisasi) -->
                <div class="relative w-44 h-44 mx-auto my-3 flex items-center justify-center">
                    <svg class="w-44 h-44 -rotate-90" viewBox="0 0 100 100">
                        <!-- Background track -->
                        <circle cx="50" cy="50" r="38" stroke="#f1f5f9" stroke-width="12" fill="none" />
                        <!-- Disewa (Indigo: 60%) -->
                        <circle cx="50" cy="50" r="38" stroke="#4f46e5" stroke-width="12" stroke-dasharray="143 238" stroke-dashoffset="0" stroke-linecap="round" fill="none" />
                        <!-- Standby (Sky blue: 23%) -->
                        <circle cx="50" cy="50" r="38" stroke="#38bdf8" stroke-width="12" stroke-dasharray="55 238" stroke-dashoffset="-147" stroke-linecap="round" fill="none" />
                        <!-- Servis (Yellow: 10%) -->
                        <circle cx="50" cy="50" r="38" stroke="#f59e0b" stroke-width="12" stroke-dasharray="24 238" stroke-dashoffset="-206" stroke-linecap="round" fill="none" />
                    </svg>
                    <!-- Center Percentage -->
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <span class="text-3xl font-black text-slate-900 leading-none">{{ $fleetUtilizationPercent }}%</span>
                        <span class="text-[10px] font-bold text-slate-400 mt-1 uppercase">Utilisasi</span>
                    </div>
                </div>

                <!-- Legend Breakdown -->
                <div class="space-y-2 text-xs mt-4">
                    <div class="flex items-center justify-between">
                        <span class="flex items-center space-x-2"><span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span><span class="font-bold text-slate-700">Disewa</span></span>
                        <span class="font-mono font-black text-slate-900">{{ $displayRented }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="flex items-center space-x-2"><span class="w-2.5 h-2.5 rounded-full bg-sky-400"></span><span class="font-bold text-slate-700">Standby</span></span>
                        <span class="font-mono font-black text-slate-900">{{ $displayAvailable }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="flex items-center space-x-2"><span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span><span class="font-bold text-slate-700">Servis</span></span>
                        <span class="font-mono font-black text-slate-900">{{ $displayMaintenance }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="flex items-center space-x-2"><span class="w-2.5 h-2.5 rounded-full bg-slate-900"></span><span class="font-bold text-slate-700">Vendor</span></span>
                        <span class="font-mono font-black text-slate-900">{{ $displayVendor }}</span>
                    </div>
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500 font-bold mt-4">
                <span>Total Unit</span>
                <span class="text-slate-900 font-mono font-black">{{ $displayTotalAssets }}</span>
            </div>
        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- BAGIAN BAWAH DASHBOARD (GRID 2 KOLOM SEIMBANG: MOBILE & DESKTOP)          -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-2 gap-3 sm:gap-6 items-start">

        <!-- KOLOM KIRI: Card Status Transaksi & Card Notifikasi Sistem -->
        <div class="space-y-3 sm:space-y-6">
            <!-- 1. Status Transaksi -->
            <div class="bg-white p-3.5 sm:p-6 rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-2xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2.5 sm:mb-4">
                        <h3 class="text-xs sm:text-base font-black text-slate-900 truncate" x-text="lang === 'zh' ? '单据业务状态' : 'Status Transaksi'">Status Transaksi</h3>
                        <a href="{{ route('erp.orders.index') }}" class="w-6 h-6 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-600 flex items-center justify-center transition border border-slate-200 text-xs sm:text-sm">
                            ↗
                        </a>
                    </div>

                    <div class="space-y-2 sm:space-y-3">
                        <!-- Sales Order -->
                        <div class="flex items-center justify-between p-2 sm:p-2.5 rounded-xl sm:rounded-2xl bg-slate-50 border border-slate-100 gap-1">
                            <div class="flex items-center space-x-1.5 sm:space-x-3 min-w-0">
                                <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center shrink-0">
                                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </div>
                                <span class="text-[10px] sm:text-xs font-bold text-slate-800 truncate">Sales Order</span>
                            </div>
                            <span class="font-mono font-black text-xs sm:text-sm text-slate-900 shrink-0">{{ $transactionStatuses['sales_order'] }}</span>
                        </div>

                        <!-- Rental Contract -->
                        <div class="flex items-center justify-between p-2 sm:p-2.5 rounded-xl sm:rounded-2xl bg-slate-50 border border-slate-100 gap-1">
                            <div class="flex items-center space-x-1.5 sm:space-x-3 min-w-0">
                                <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                </div>
                                <span class="text-[10px] sm:text-xs font-bold text-slate-800 truncate">Rental Contract</span>
                            </div>
                            <span class="font-mono font-black text-xs sm:text-sm text-slate-900 shrink-0">{{ $transactionStatuses['rental_contract'] }}</span>
                        </div>

                        <!-- Dalam Pengiriman -->
                        <div class="flex items-center justify-between p-2 sm:p-2.5 rounded-xl sm:rounded-2xl bg-slate-50 border border-slate-100 gap-1">
                            <div class="flex items-center space-x-1.5 sm:space-x-3 min-w-0">
                                <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center shrink-0">
                                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1"></path></svg>
                                </div>
                                <span class="text-[10px] sm:text-xs font-bold text-slate-800 truncate">Pengiriman</span>
                            </div>
                            <span class="font-mono font-black text-xs sm:text-sm text-slate-900 shrink-0">{{ $transactionStatuses['in_delivery'] }}</span>
                        </div>

                        <!-- Invoice Belum Lunas -->
                        <div class="flex items-center justify-between p-2 sm:p-2.5 rounded-xl sm:rounded-2xl bg-slate-50 border border-slate-100 gap-1">
                            <div class="flex items-center space-x-1.5 sm:space-x-3 min-w-0">
                                <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center shrink-0">
                                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                </div>
                                <span class="text-[10px] sm:text-xs font-bold text-slate-800 truncate">Inv Belum Lunas</span>
                            </div>
                            <span class="font-mono font-black text-xs sm:text-sm text-slate-900 shrink-0">{{ $transactionStatuses['unpaid_invoice'] }}</span>
                        </div>

                        <!-- Tagihan Vendor -->
                        <div class="flex items-center justify-between p-2 sm:p-2.5 rounded-xl sm:rounded-2xl bg-slate-50 border border-slate-100 gap-1">
                            <div class="flex items-center space-x-1.5 sm:space-x-3 min-w-0">
                                <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-slate-200 text-slate-700 flex items-center justify-center shrink-0">
                                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                </div>
                                <span class="text-[10px] sm:text-xs font-bold text-slate-800 truncate">Tagihan Vendor</span>
                            </div>
                            <span class="font-mono font-black text-xs sm:text-sm text-slate-900 shrink-0">{{ $transactionStatuses['vendor_bill'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Notifikasi Sistem -->
            <div class="bg-white p-3.5 sm:p-6 rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-2xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2.5 sm:mb-4">
                        <h3 class="text-xs sm:text-base font-black text-slate-900 truncate" x-text="lang === 'zh' ? '系统通知' : 'Notifikasi Sistem'">Notifikasi Sistem</h3>
                        <button type="button" class="w-6 h-6 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-600 flex items-center justify-center transition border border-slate-200 text-xs sm:text-sm">
                            ↗
                        </button>
                    </div>

                    <div class="space-y-2 sm:space-y-3">
                        @foreach($systemAlerts as $alert)
                        <div class="flex items-start space-x-2 sm:space-x-3 p-2 sm:p-3 rounded-xl sm:rounded-2xl bg-slate-50/80 border border-slate-100 hover:bg-slate-100/80 transition">
                            <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 mt-0.5">
                                @if($alert['type'] === 'service')
                                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg>
                                @elseif($alert['type'] === 'invoice')
                                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                @elseif($alert['type'] === 'po')
                                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                @else
                                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <h5 class="text-[10px] sm:text-xs font-black text-slate-900 leading-tight truncate">{{ $alert['title'] }}</h5>
                                <span class="text-[9px] sm:text-[10px] text-slate-500 block mt-0.5 truncate">{{ $alert['desc'] }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- KOLOM KANAN: Card Aktivitas Terbaru & Card Aksi Cepat -->
        <div class="space-y-3 sm:space-y-6">
            <!-- 3. Aktivitas Terbaru -->
            <div class="bg-white p-3.5 sm:p-6 rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-2xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2.5 sm:mb-4">
                        <h3 class="text-xs sm:text-base font-black text-slate-900 truncate" x-text="lang === 'zh' ? '最新动态' : 'Aktivitas Terbaru'">Aktivitas Terbaru</h3>
                        <a href="{{ route('erp.orders.index') }}" class="w-6 h-6 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-600 flex items-center justify-center transition border border-slate-200 text-xs sm:text-sm">
                            ↗
                        </a>
                    </div>

                    <!-- Compact Mobile Activities List (< sm) -->
                    <div class="space-y-2 block sm:hidden">
                        @foreach($recentActivities as $act)
                        <div class="p-2 rounded-xl bg-slate-50/80 border border-slate-100 space-y-1">
                            <div class="flex items-center justify-between gap-1">
                                <span class="font-mono font-black text-[10px] text-slate-900 truncate">{{ $act['dokumen'] }}</span>
                                <span class="px-1.5 py-0.5 rounded-full text-[8px] font-extrabold shrink-0 {{ $act['status_class'] }}">
                                    {{ $act['status'] }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-[9px] text-slate-500 gap-1">
                                <span class="font-medium text-slate-700 truncate max-w-[90px]">{{ $act['customer'] }}</span>
                                <span class="font-mono text-[8px] shrink-0">{{ $act['tgl'] }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Desktop Activities Table (>= sm) -->
                    <div class="overflow-x-auto hidden sm:block">
                        <table class="w-full text-left text-xs">
                            <thead class="text-slate-400 text-[10px] font-black uppercase tracking-wider border-b border-slate-100">
                                <tr>
                                    <th class="pb-3 px-2">Tgl</th>
                                    <th class="pb-3 px-2">Dokumen</th>
                                    <th class="pb-3 px-2">Customer</th>
                                    <th class="pb-3 px-2 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($recentActivities as $act)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-2.5 px-2 text-slate-500 font-mono text-[11px] whitespace-nowrap">{{ $act['tgl'] }}</td>
                                    <td class="py-2.5 px-2 font-mono font-black text-slate-900 whitespace-nowrap">{{ $act['dokumen'] }}</td>
                                    <td class="py-2.5 px-2 font-bold text-slate-800 truncate max-w-[120px]">{{ $act['customer'] }}</td>
                                    <td class="py-2.5 px-2 text-center">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold {{ $act['status_class'] }}">
                                            {{ $act['status'] }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 4. Aksi Cepat -->
            <div class="bg-white p-3.5 sm:p-6 rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-2xs flex flex-col justify-between">
                <div>
                    <h3 class="text-xs sm:text-base font-black text-slate-900 mb-2.5 sm:mb-4 truncate" x-text="lang === 'zh' ? '快捷操作' : 'Aksi Cepat'">Aksi Cepat</h3>
                    
                    <div class="space-y-2 sm:space-y-3">
                        <!-- Buat Penawaran -->
                        <button type="button" @click="modalQuotationOpen = true" 
                                class="w-full p-2 sm:p-3.5 rounded-xl sm:rounded-2xl bg-slate-50 hover:bg-amber-50 hover:border-amber-300 border border-slate-200/80 transition flex items-center justify-between text-left group gap-1">
                            <div class="flex items-center space-x-1.5 sm:space-x-3 min-w-0">
                                <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-black shrink-0">
                                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </div>
                                <span class="text-[10px] sm:text-xs font-black text-slate-800 group-hover:text-amber-800 truncate">Penawaran</span>
                            </div>
                            <span class="text-slate-400 font-bold group-hover:translate-x-0.5 transition-transform text-xs hidden sm:inline">›</span>
                        </button>

                        <!-- Buat Transaksi -->
                        <button type="button" @click="modalQuotationOpen = true" 
                                class="w-full p-2 sm:p-3.5 rounded-xl sm:rounded-2xl bg-slate-50 hover:bg-blue-50 hover:border-blue-300 border border-slate-200/80 transition flex items-center justify-between text-left group gap-1">
                            <div class="flex items-center space-x-1.5 sm:space-x-3 min-w-0">
                                <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-black shrink-0">
                                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                </div>
                                <span class="text-[10px] sm:text-xs font-black text-slate-800 group-hover:text-blue-800 truncate">Transaksi</span>
                            </div>
                            <span class="text-slate-400 font-bold group-hover:translate-x-0.5 transition-transform text-xs hidden sm:inline">›</span>
                        </button>

                        <!-- Input Pengeluaran -->
                        <a href="{{ route('erp.finance.index') }}" 
                           class="w-full p-2 sm:p-3.5 rounded-xl sm:rounded-2xl bg-slate-50 hover:bg-indigo-50 hover:border-indigo-300 border border-slate-200/80 transition flex items-center justify-between text-left group gap-1">
                            <div class="flex items-center space-x-1.5 sm:space-x-3 min-w-0">
                                <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center font-black shrink-0">
                                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                </div>
                                <span class="text-[10px] sm:text-xs font-black text-slate-800 group-hover:text-indigo-800 truncate">Pengeluaran</span>
                            </div>
                            <span class="text-slate-400 font-bold group-hover:translate-x-0.5 transition-transform text-xs hidden sm:inline">›</span>
                        </a>

                        <!-- Lihat Semua Laporan -->
                        <a href="{{ route('erp.orders.index') }}" 
                           class="w-full p-2 sm:p-3.5 rounded-xl sm:rounded-2xl bg-slate-50 hover:bg-slate-100 hover:border-slate-300 border border-slate-200/80 transition flex items-center justify-between text-left group gap-1">
                            <div class="flex items-center space-x-1.5 sm:space-x-3 min-w-0">
                                <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-slate-200 text-slate-700 flex items-center justify-center font-black shrink-0">
                                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                </div>
                                <span class="text-[10px] sm:text-xs font-black text-slate-800 truncate">Semua Laporan</span>
                            </div>
                            <span class="text-slate-400 font-bold group-hover:translate-x-0.5 transition-transform text-xs hidden sm:inline">›</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
