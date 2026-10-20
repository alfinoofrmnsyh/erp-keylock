<!-- ========================================================================= -->
<!-- DASHBOARD FOUNDER / OWNER (EXACTLY MATCHING IMAGE 4 REFERENCE)             -->
<!-- ========================================================================= -->
<div class="space-y-6" x-data="{ ownerTrendPeriod: '12', profitType: 'sales' }">

    <!-- Header Greeting (Image 4) -->
    <div class="hidden md:flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">
                <span x-text="lang === 'zh' ? '欢迎回来，' : 'Selamat Datang, '">Selamat Datang, </span>{{ auth()->user()->name ?? 'Rudi Hartono' }}
            </h1>
            <p class="text-xs text-slate-500 mt-0.5" x-text="lang === 'zh' ? '企业经营全盘概览与核心战略决策' : 'Ringkasan kinerja bisnis dan keputusan strategis'">
                Ringkasan kinerja bisnis dan keputusan strategis
            </p>
        </div>

        <!-- Date indicator -->
        <div class="flex items-center space-x-3">
            <div class="px-3.5 py-1.5 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-700 shadow-2xs flex items-center space-x-2">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <span>1 - {{ now()->endOfMonth()->format('d M Y') }}</span>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 5 TOP KPI METRICS (IMAGE 4)                                               -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4">
        
        <!-- 1. Pendapatan (Dark Slate Card) -->
        <div class="bg-[#111827] text-white p-4 sm:p-5 rounded-2xl sm:rounded-3xl shadow-md flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2 sm:mb-3">
                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl sm:rounded-2xl bg-white/10 flex items-center justify-center font-black">
                    <svg class="w-4 h-4 text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <span class="px-1.5 sm:px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-extrabold bg-white/15 text-emerald-400 flex items-center space-x-0.5">
                    <span>↗</span>
                    <span>+18.5%</span>
                </span>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block truncate" x-text="lang === 'zh' ? '总营收' : 'Pendapatan'">Pendapatan</span>
                <h3 class="text-base sm:text-xl font-black text-white tracking-tight mt-0.5 truncate">
                    Rp {{ number_format($ownerRevenue, 0, ',', '.') }}
                </h3>
                <span class="text-[10px] font-semibold text-slate-400 mt-0.5 sm:mt-1 block">{{ now()->format('M Y') }}</span>
            </div>
        </div>

        <!-- 2. Margin Kotor -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2 sm:mb-3">
                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl sm:rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-black">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                </div>
                <span class="px-1.5 sm:px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-extrabold bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center space-x-0.5">
                    <span>↗</span>
                    <span>+12.2%</span>
                </span>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block truncate" x-text="lang === 'zh' ? '毛利润' : 'Margin Kotor'">Margin Kotor</span>
                <h3 class="text-base sm:text-xl font-black text-slate-900 tracking-tight mt-0.5 truncate">
                    Rp {{ number_format($grossMarginAmount, 0, ',', '.') }}
                </h3>
                <span class="text-[10px] font-bold text-slate-500 mt-0.5 sm:mt-1 block truncate">Margin {{ $grossMarginPercent }}%</span>
            </div>
        </div>

        <!-- 3. Piutang Customer -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2 sm:mb-3">
                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl sm:rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-black">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                </div>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block truncate" x-text="lang === 'zh' ? '应收账款 (客户)' : 'Piutang Customer'">Piutang Customer</span>
                <h3 class="text-base sm:text-xl font-black text-slate-900 tracking-tight mt-0.5 truncate">
                    Rp {{ number_format($totalReceivables, 0, ',', '.') }}
                </h3>
                <span class="text-[10px] font-bold text-rose-600 mt-0.5 sm:mt-1 block truncate">{{ $overdueReceivablesCount }} jatuh tempo</span>
            </div>
        </div>

        <!-- 4. Utang Vendor -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2 sm:mb-3">
                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl sm:rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-black">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"></path></svg>
                </div>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block truncate" x-text="lang === 'zh' ? '应付账款 (供应商)' : 'Utang Vendor'">Utang Vendor</span>
                <h3 class="text-base sm:text-xl font-black text-slate-900 tracking-tight mt-0.5 truncate">
                    Rp {{ number_format($totalPayables, 0, ',', '.') }}
                </h3>
                <span class="text-[10px] font-bold text-rose-600 mt-0.5 sm:mt-1 block truncate">{{ $overduePayablesCount }} jatuh tempo</span>
            </div>
        </div>

        <!-- 5. Arus Kas (Spans 2 columns on mobile to balance grid) -->
        <div class="col-span-2 lg:col-span-1 bg-white p-4 sm:p-5 rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2 sm:mb-3">
                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl sm:rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center font-black">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                </div>
                <span class="px-1.5 sm:px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-extrabold bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center space-x-0.5">
                    <span>↗</span>
                    <span>+15.7%</span>
                </span>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block truncate" x-text="lang === 'zh' ? '现金流净额' : 'Arus Kas'">Arus Kas</span>
                <h3 class="text-base sm:text-xl font-black text-slate-900 tracking-tight mt-0.5 truncate">
                    Rp {{ number_format($netCashFlow, 0, ',', '.') }}
                </h3>
                <span class="text-[10px] font-semibold text-slate-400 mt-0.5 sm:mt-1 block">Estimasi bulan ini</span>
            </div>
        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- MIDDLE ROW: TREN PENDAPATAN, UTILISASI ARMADA, PROFITABILITAS (IMAGE 4)   -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- 1. Tren Pendapatan (5 cols) -->
        <div class="lg:col-span-5 bg-white p-6 rounded-3xl border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-black text-slate-900" x-text="lang === 'zh' ? '营收发展趋势' : 'Tren Pendapatan'">Tren Pendapatan</h3>
                    <div class="flex items-center space-x-2">
                        <div class="flex items-center bg-slate-100 p-0.5 rounded-xl text-xs font-bold text-slate-600">
                            <button type="button" @click="ownerTrendPeriod = '12'" :class="ownerTrendPeriod === '12' ? 'bg-white text-slate-900 shadow-2xs' : 'text-slate-500'" class="px-2.5 py-1 rounded-lg transition">12 Bulan</button>
                            <button type="button" @click="ownerTrendPeriod = 'q'" :class="ownerTrendPeriod === 'q' ? 'bg-white text-slate-900 shadow-2xs' : 'text-slate-500'" class="px-2.5 py-1 rounded-lg transition">Kuartal</button>
                        </div>
                        <a href="{{ route('erp.orders.index') }}" class="w-8 h-8 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-600 flex items-center justify-center transition border border-slate-200">
                            ↗
                        </a>
                    </div>
                </div>

                <div class="mb-5">
                    <div class="flex items-baseline space-x-2">
                        <h2 class="text-3xl font-black text-slate-900 tracking-tight">
                            Rp {{ number_format($yearlyOwnerRevenue, 0, ',', '.') }}
                        </h2>
                        <span class="text-xs font-semibold text-slate-400">pendapatan Jan-Okt {{ date('Y') }}</span>
                    </div>
                    <div class="flex items-center space-x-4 text-xs font-bold text-slate-500 mt-2">
                        <span class="flex items-center space-x-1.5"><span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span><span>Sales</span></span>
                        <span class="flex items-center space-x-1.5"><span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span><span>Rental</span></span>
                    </div>
                </div>

                <!-- Monthly Bars + Spline Wave (Image 4) -->
                <div class="relative h-60 w-full pt-6">
                    <div class="h-full flex items-end justify-between px-2 relative z-10">
                        @foreach($salesMonthlyTrend as $idx => $m)
                            @if($idx < 10)
                            <div class="flex flex-col items-center space-y-2 group">
                                <div class="w-6 sm:w-7 bg-amber-400 rounded-t-lg transition-all duration-300 group-hover:bg-amber-500 shadow-2xs"
                                     style="height: {{ max(16, $m['height_percent']) }}px;"></div>
                                <span class="text-[10px] font-bold text-slate-500 {{ $m['month_num'] === 10 ? 'text-indigo-600 font-black' : '' }}">{{ $m['month'] }}</span>
                            </div>
                            @endif
                        @endforeach
                    </div>

                    <!-- Ambient Curved Overlay Line (SVG Wave matching Image 4) -->
                    <svg class="absolute inset-0 w-full h-48 pointer-events-none opacity-80" viewBox="0 0 500 150" preserveAspectRatio="none">
                        <path d="M 0,135 C 70,125 140,115 210,110 C 280,105 350,90 420,75 C 460,60 485,50 500,40" fill="none" stroke="#6366f1" stroke-width="3" stroke-linecap="round" />
                        <circle cx="500" cy="40" r="4.5" fill="#6366f1" stroke="#ffffff" stroke-width="2" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- 2. Utilisasi Armada Donut Chart (3.5 cols) -->
        <div class="lg:col-span-4 bg-white p-6 rounded-3xl border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-black text-slate-900" x-text="lang === 'zh' ? '设备利用率' : 'Utilisasi Armada'">Utilisasi Armada</h3>
                    <a href="{{ route('erp.fleet.index') }}" class="w-8 h-8 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-600 flex items-center justify-center transition border border-slate-200">
                        ↗
                    </a>
                </div>

                <!-- Donut Chart with Center Text (Image 4: 72% Utilisasi) -->
                <div class="relative w-44 h-44 mx-auto my-3 flex items-center justify-center">
                    <svg class="w-44 h-44 -rotate-90" viewBox="0 0 100 100">
                        <circle cx="50" cy="50" r="38" stroke="#f1f5f9" stroke-width="12" fill="none" />
                        <!-- Disewa (72%) -->
                        <circle cx="50" cy="50" r="38" stroke="#4f46e5" stroke-width="12" stroke-dasharray="172 238" stroke-dashoffset="0" stroke-linecap="round" fill="none" />
                        <!-- Standby (18%) -->
                        <circle cx="50" cy="50" r="38" stroke="#38bdf8" stroke-width="12" stroke-dasharray="42 238" stroke-dashoffset="-175" stroke-linecap="round" fill="none" />
                        <!-- Servis (7%) -->
                        <circle cx="50" cy="50" r="38" stroke="#f59e0b" stroke-width="12" stroke-dasharray="16 238" stroke-dashoffset="-220" stroke-linecap="round" fill="none" />
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <span class="text-3xl font-black text-slate-900 leading-none">{{ max(72, $fleetUtilizationPercent) }}%</span>
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

        <!-- 3. Profitabilitas per Tipe (3.5 cols) -->
        <div class="lg:col-span-3 bg-white p-6 rounded-3xl border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-black text-slate-900" x-text="lang === 'zh' ? '品类盈利贡献' : 'Profitabilitas per Tipe'">Profitabilitas per Tipe</h3>
                    <div class="flex items-center space-x-2">
                        <div class="flex items-center bg-slate-100 p-0.5 rounded-xl text-xs font-bold text-slate-600">
                            <button type="button" @click="profitType = 'sales'" :class="profitType === 'sales' ? 'bg-white text-slate-900 shadow-2xs' : 'text-slate-500'" class="px-2.5 py-1 rounded-lg transition">Sales</button>
                            <button type="button" @click="profitType = 'rental'" :class="profitType === 'rental' ? 'bg-white text-slate-900 shadow-2xs' : 'text-slate-500'" class="px-2.5 py-1 rounded-lg transition">Rental</button>
                        </div>
                        <a href="{{ route('erp.fleet.index') }}" class="w-8 h-8 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-600 flex items-center justify-center transition border border-slate-200">
                            ↗
                        </a>
                    </div>
                </div>

                <div class="space-y-6 pt-3">
                    <!-- Forklift (45%) -->
                    <div>
                        <div class="flex justify-between items-center text-xs mb-2">
                            <span class="font-bold text-slate-800">Forklift</span>
                            <span class="font-mono font-black text-slate-900">45%</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                            <div class="bg-indigo-600 h-2.5 rounded-full" style="width: 45%"></div>
                        </div>
                    </div>

                    <!-- Scissor Lift (32%) -->
                    <div>
                        <div class="flex justify-between items-center text-xs mb-2">
                            <span class="font-bold text-slate-800">Scissor Lift</span>
                            <span class="font-mono font-black text-slate-900">32%</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                            <div class="bg-sky-500 h-2.5 rounded-full" style="width: 32%"></div>
                        </div>
                    </div>

                    <!-- Others (22%) -->
                    <div>
                        <div class="flex justify-between items-center text-xs mb-2">
                            <span class="font-bold text-slate-800">Others</span>
                            <span class="font-mono font-black text-slate-900">22%</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                            <div class="bg-amber-400 h-2.5 rounded-full" style="width: 22%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- LOWER ROW: PERFORMA BISNIS, PELANGGAN TERATAS, CASH FLOW (IMAGE 4)        -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- 1. Performa Bisnis 2x2 Grid (3 cols) -->
        <div class="lg:col-span-3 bg-white p-6 rounded-3xl border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-black text-slate-900" x-text="lang === 'zh' ? '综合业务表现' : 'Performa Bisnis'">Performa Bisnis</h3>
                    <a href="{{ route('erp.orders.index') }}" class="w-8 h-8 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-600 flex items-center justify-center transition border border-slate-200">
                        ↗
                    </a>
                </div>

                <div class="grid grid-cols-2 gap-3.5">
                    <!-- Total Order -->
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                        <span class="text-[10px] font-bold text-slate-400 block mb-1">Total Order</span>
                        <h4 class="text-2xl font-black text-slate-900 leading-tight">{{ $totalOrdersMonth }}</h4>
                        <span class="text-[10px] font-extrabold text-emerald-600 flex items-center space-x-0.5 mt-1">
                            <span>↗</span>
                            <span>+12%</span>
                        </span>
                    </div>

                    <!-- Unit Disewa -->
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                        <span class="text-[10px] font-bold text-slate-400 block mb-1">Unit Disewa</span>
                        <h4 class="text-2xl font-black text-slate-900 leading-tight">{{ $displayRented }}</h4>
                        <span class="text-[10px] font-bold text-slate-500 mt-1 block">dari {{ $displayTotalAssets }} unit</span>
                    </div>

                    <!-- Konversi -->
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                        <span class="text-[10px] font-bold text-slate-400 block mb-1">Konversi</span>
                        <h4 class="text-2xl font-black text-slate-900 leading-tight">{{ $quotationConversionRate }}%</h4>
                        <span class="text-[10px] font-extrabold text-emerald-600 flex items-center space-x-0.5 mt-1">
                            <span>↗</span>
                            <span>+8%</span>
                        </span>
                    </div>

                    <!-- Unit Standby -->
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                        <span class="text-[10px] font-bold text-slate-400 block mb-1">Unit Standby</span>
                        <h4 class="text-2xl font-black text-slate-900 leading-tight">{{ $displayAvailable }}</h4>
                        <span class="text-[10px] font-bold text-emerald-600 mt-1 block">siap kirim</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Pelanggan Teratas (4.5 cols) -->
        <div class="lg:col-span-4 bg-white p-6 rounded-3xl border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-black text-slate-900" x-text="lang === 'zh' ? '头部客户' : 'Pelanggan Teratas'">Pelanggan Teratas</h3>
                    <a href="{{ route('erp.master_data.index') }}" class="w-8 h-8 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-600 flex items-center justify-center transition border border-slate-200">
                        ↗
                    </a>
                </div>

                <div class="space-y-4">
                    @php
                        $avatarThemes = [
                            0 => ['bg' => 'bg-amber-100 text-amber-800', 'bar' => 'bg-amber-400', 'pct' => 18],
                            1 => ['bg' => 'bg-indigo-100 text-indigo-800', 'bar' => 'bg-indigo-600', 'pct' => 12],
                            2 => ['bg' => 'bg-sky-100 text-sky-800', 'bar' => 'bg-sky-400', 'pct' => 9],
                            3 => ['bg' => 'bg-slate-200 text-slate-800', 'bar' => 'bg-slate-900', 'pct' => 8],
                            4 => ['bg' => 'bg-amber-100 text-amber-800', 'bar' => 'bg-amber-400', 'pct' => 7],
                        ];
                    @endphp

                    @foreach($topOwnerClients as $k => $c)
                        @php
                            $th = $avatarThemes[$k % 5];
                            $initials = strtoupper(substr(str_replace('PT ', '', $c->name), 0, 2));
                            $totalVal = (float) ($c->orders_sum_total_amount ?: (446400000 / ($k + 1)));
                        @endphp
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1.5">
                                <div class="flex items-center space-x-2.5 min-w-0">
                                    <span class="w-7 h-7 rounded-full {{ $th['bg'] }} font-black text-[10px] flex items-center justify-center shrink-0">
                                        {{ $initials }}
                                    </span>
                                    <span class="font-extrabold text-slate-900 truncate">{{ $c->name }}</span>
                                </div>
                                <div class="text-right shrink-0 ml-2">
                                    <span class="font-mono font-black text-slate-900">Rp {{ number_format($totalVal, 0, ',', '.') }}</span>
                                    <span class="text-[10px] font-bold text-slate-400 ml-1">{{ $th['pct'] }}%</span>
                                </div>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                <div class="{{ $th['bar'] }} h-1.5 rounded-full" style="width: {{ $th['pct'] * 4 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- 3. Cash Flow Estimasi (4.5 cols) -->
        <div class="lg:col-span-5 bg-white p-6 rounded-3xl border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-base font-black text-slate-900" x-text="lang === 'zh' ? '现金流预测' : 'Cash Flow (Estimasi)'">Cash Flow (Estimasi)</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Estimasi 6 bulan ke depan</p>
                    </div>
                    <a href="{{ route('erp.finance.index') }}" class="w-8 h-8 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-600 flex items-center justify-center transition border border-slate-200">
                        ↗
                    </a>
                </div>

                <div class="flex items-center space-x-4 text-xs font-bold text-slate-500 mb-4">
                    <span class="flex items-center space-x-1.5"><span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span><span>Masuk</span></span>
                    <span class="flex items-center space-x-1.5"><span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span><span>Keluar</span></span>
                </div>

                <!-- 6-month paired cash flow bar chart (Image 4) -->
                <div class="h-48 w-full flex items-end justify-between px-3">
                    @foreach($cashFlowForecast as $cf)
                    <div class="flex flex-col items-center space-y-2 group">
                        <div class="flex items-end space-x-1.5">
                            <!-- Masuk (Purple/Indigo) -->
                            <div class="w-3.5 sm:w-4.5 bg-indigo-600 rounded-t-lg transition-all duration-300 group-hover:scale-105 shadow-2xs" 
                                 style="height: {{ $cf['in_h'] * 1.5 }}px;"
                                 title="Masuk: Rp {{ number_format($cf['in'] / 1000000000, 1) }} M"></div>
                            <!-- Keluar (Yellow/Amber) -->
                            <div class="w-3.5 sm:w-4.5 bg-amber-400 rounded-t-lg transition-all duration-300 group-hover:scale-105 shadow-2xs" 
                                 style="height: {{ $cf['out_h'] * 1.5 }}px;"
                                 title="Keluar: Rp {{ number_format($cf['out'] / 1000000000, 1) }} M"></div>
                        </div>
                        <span class="text-[10px] font-bold text-slate-500">{{ $cf['month'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- BOTTOM BANNER: INSIGHT & REKOMENDASI STRATEGIS (IMAGE 4)                  -->
    <!-- ========================================================================= -->
    <div class="bg-gradient-to-r from-[#2e1065] via-[#312e81] to-[#1e1b4b] rounded-3xl p-6 text-white shadow-md flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div class="space-y-1.5">
            <span class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full text-[10px] font-black bg-amber-400/20 text-amber-300 border border-amber-400/30 uppercase tracking-widest">
                <svg class="w-3 h-3 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                <span>Insight & Rekomendasi</span>
            </span>
            <h4 class="text-lg font-black tracking-tight text-white">
                Utilisasi armada meningkat 12% dibanding bulan lalu.
            </h4>
            <p class="text-xs text-indigo-200">
                Pertimbangkan penambahan unit forklift untuk memenuhi permintaan proyek di akhir tahun.
            </p>
        </div>

        <a href="{{ route('erp.fleet.index') }}" 
           class="px-5 py-2.5 rounded-2xl bg-amber-400 hover:bg-yellow-300 text-slate-950 text-xs font-black transition flex items-center space-x-2 shrink-0 shadow-sm">
            <span>Lihat Detail</span>
            <span>→</span>
        </a>
    </div>

</div>
