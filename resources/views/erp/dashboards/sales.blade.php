<!-- ========================================================================= -->
<!-- DASHBOARD SALES (EXACTLY MATCHING IMAGE 2 REFERENCE)                      -->
<!-- ========================================================================= -->
<div class="space-y-6" x-data="{ salesTrendPeriod: '12', pipelineType: 'all' }">

    <!-- Header Greeting (Image 2) -->
    <div class="hidden md:flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">
                <span x-text="lang === 'zh' ? '欢迎回来，' : 'Selamat Datang, '">Selamat Datang, </span>{{ auth()->user()->name ?? 'Budi Santoso' }}
            </h1>
            <p class="text-xs text-slate-500 mt-0.5" x-text="lang === 'zh' ? '监控您今天的商机与销售进展' : 'Pantau peluang dan progres penjualan Anda hari ini'">
                Pantau peluang dan progres penjualan Anda hari ini
            </p>
        </div>

        <!-- Date indicator & Quick Refresh -->
        <div class="flex items-center space-x-3">
            <div class="px-3.5 py-1.5 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-700 shadow-2xs flex items-center space-x-2">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <span>1 - {{ now()->endOfMonth()->format('d M Y') }}</span>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 4 TOP KPI CARDS (IMAGE 2)                                                 -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-5">
        
        <!-- Card 1: Target Penjualan (Gradient Blue/Purple Card with Gauge Circle) -->
        <div class="bg-gradient-to-br from-[#4f46e5] via-[#4338ca] to-[#312e81] p-4 sm:p-6 rounded-2xl sm:rounded-3xl text-white shadow-md flex flex-col justify-between relative overflow-hidden">
            <div class="flex items-start justify-between">
                <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-white/15 flex items-center justify-center backdrop-blur-xs">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>

                <!-- Radial Gauge Circle (Image 2: 68%) -->
                <div class="relative w-10 h-10 sm:w-14 sm:h-14 flex items-center justify-center">
                    <svg class="w-10 h-10 sm:w-14 sm:h-14 -rotate-90" viewBox="0 0 36 36">
                        <path class="text-white/20" stroke-width="3.5" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        <path class="text-amber-300" stroke-width="3.5" stroke-dasharray="{{ $salesTargetPercent }}, 100" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                    </svg>
                    <span class="absolute text-[10px] sm:text-xs font-black text-white">{{ $salesTargetPercent }}%</span>
                </div>
            </div>

            <div class="mt-2 sm:mt-4">
                <span class="text-[10px] sm:text-[11px] font-bold text-indigo-200 uppercase tracking-wider block truncate" x-text="lang === 'zh' ? '销售目标' : 'Target Penjualan'">Target Penjualan</span>
                <h3 class="text-base sm:text-2xl font-black text-white tracking-tight mt-0.5 truncate">
                    Rp {{ number_format($salesTargetAmount, 0, ',', '.') }}
                </h3>
                <div class="flex items-center space-x-1 text-[10px] sm:text-[11px] font-bold text-indigo-200 mt-0.5 sm:mt-1 truncate">
                    <span>+12% vs bln lalu</span>
                </div>
            </div>
        </div>

        <!-- Card 2: Realisasi Penjualan -->
        <div class="bg-white p-4 sm:p-6 rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2 sm:mb-3">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-black">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    </div>
                    <span class="px-1.5 sm:px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-extrabold bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center space-x-0.5">
                        <span>↗</span>
                        <span>+18%</span>
                    </span>
                </div>
                <span class="text-[10px] sm:text-[11px] font-bold text-slate-400 uppercase tracking-wider block truncate" x-text="lang === 'zh' ? '实际业绩达成' : 'Realisasi Penjualan'">Realisasi Penjualan</span>
                <h3 class="text-base sm:text-2xl font-black text-slate-900 tracking-tight mt-0.5 truncate">
                    Rp {{ number_format($salesRealizedRevenue, 0, ',', '.') }}
                </h3>
            </div>
            <div class="mt-2 sm:mt-4 pt-2 border-t border-slate-100">
                <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden mb-1 sm:mb-1.5">
                    <div class="bg-amber-400 h-1.5 rounded-full" style="width: {{ $salesTargetPercent }}%"></div>
                </div>
                <span class="text-[10px] sm:text-[11px] font-bold text-slate-500 truncate block">{{ $salesTargetPercent }}% dari target</span>
            </div>
        </div>

        <!-- Card 3: Pipeline Aktif -->
        <div class="bg-white p-4 sm:p-6 rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2 sm:mb-3">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center font-black">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <span class="px-1.5 sm:px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-extrabold bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center space-x-0.5">
                        <span>↗</span>
                        <span>+25%</span>
                    </span>
                </div>
                <span class="text-[10px] sm:text-[11px] font-bold text-slate-400 uppercase tracking-wider block truncate" x-text="lang === 'zh' ? '活跃商机管道' : 'Pipeline Aktif'">Pipeline Aktif</span>
                <h3 class="text-base sm:text-2xl font-black text-slate-900 tracking-tight mt-0.5 truncate">
                    Rp {{ number_format($activePipelineValue, 0, ',', '.') }}
                </h3>
            </div>
            <div class="mt-2 sm:mt-4 pt-2 border-t border-slate-100">
                <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden mb-1 sm:mb-1.5">
                    <div class="bg-blue-600 h-1.5 rounded-full" style="width: 75%"></div>
                </div>
                <span class="text-[10px] sm:text-[11px] font-bold text-slate-500 truncate block">{{ $activeQuotationsCount }} penawaran</span>
            </div>
        </div>

        <!-- Card 4: Konversi Penawaran -->
        <div class="bg-white p-4 sm:p-6 rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2 sm:mb-3">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-black">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                    <span class="px-1.5 sm:px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-extrabold bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center space-x-0.5">
                        <span>↗</span>
                        <span>+8%</span>
                    </span>
                </div>
                <span class="text-[10px] sm:text-[11px] font-bold text-slate-400 uppercase tracking-wider block truncate" x-text="lang === 'zh' ? '报价转化率' : 'Konversi Penawaran'">Konversi Penawaran</span>
                <h3 class="text-base sm:text-2xl font-black text-slate-900 tracking-tight mt-0.5 truncate">
                    {{ $quotationConversionRate }}%
                </h3>
            </div>
            <div class="mt-2 sm:mt-4 pt-2 border-t border-slate-100">
                <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden mb-1 sm:mb-1.5">
                    <div class="bg-indigo-600 h-1.5 rounded-full" style="width: {{ $quotationConversionRate }}%"></div>
                </div>
                <span class="text-[10px] sm:text-[11px] font-bold text-slate-500 truncate block">{{ $approvedQuotationsCount }} / {{ $totalQuotationsCount }} penawaran</span>
            </div>
        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- MIDDLE SECTION: TREN PENJUALAN & PIPELINE PENJUALAN (IMAGE 2)             -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left: Tren Penjualan (7 cols) -->
        <div class="lg:col-span-7 bg-white p-6 rounded-3xl border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-black text-slate-900" x-text="lang === 'zh' ? '销售趋势' : 'Tren Penjualan'">Tren Penjualan</h3>
                    <div class="flex items-center space-x-2">
                        <div class="flex items-center bg-slate-100 p-0.5 rounded-xl text-xs font-bold text-slate-600">
                            <button type="button" @click="salesTrendPeriod = '12'" 
                                    :class="salesTrendPeriod === '12' ? 'bg-white text-slate-900 shadow-2xs' : 'text-slate-500'" 
                                    class="px-3 py-1 rounded-lg transition">12 Bulan</button>
                            <button type="button" @click="salesTrendPeriod = 'q'" 
                                    :class="salesTrendPeriod === 'q' ? 'bg-white text-slate-900 shadow-2xs' : 'text-slate-500'" 
                                    class="px-3 py-1 rounded-lg transition">Kuartal</button>
                        </div>
                        <a href="{{ route('erp.orders.index') }}" title="Detail Transaksi" class="w-8 h-8 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-600 flex items-center justify-center transition border border-slate-200">
                            ↗
                        </a>
                    </div>
                </div>

                <!-- Big Number & Legend -->
                <div class="mb-6">
                    <div class="flex items-baseline space-x-2">
                        <h2 class="text-3xl font-black text-slate-900 tracking-tight">
                            Rp {{ number_format($yearlySalesTotal, 0, ',', '.') }}
                        </h2>
                        <span class="text-xs font-semibold text-slate-400">penjualan Jan-Okt {{ date('Y') }}</span>
                    </div>
                    <div class="flex items-center space-x-4 text-xs font-bold text-slate-500 mt-2">
                        <span class="flex items-center space-x-1.5"><span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span><span>Sales</span></span>
                        <span class="flex items-center space-x-1.5"><span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span><span>Rental</span></span>
                        <span class="flex items-center space-x-1.5 text-slate-400 font-medium"><span>--</span><span>Target bulanan Rp 1.2 M</span></span>
                    </div>
                </div>

                <!-- Monthly Bar & Curve Chart (Image 2) -->
                <div class="relative h-60 w-full pt-6">
                    <!-- Dashed Target Line at 1.2M -->
                    <div class="absolute w-full top-14 left-0 border-b border-dashed border-slate-300 z-0"></div>

                    <!-- Bars & Month Labels -->
                    <div class="h-full flex items-end justify-between px-2 relative z-10">
                        @foreach($salesMonthlyTrend as $idx => $m)
                            @if($idx < 10)
                            <div class="flex flex-col items-center space-y-2 group">
                                <div class="w-6 sm:w-8 bg-amber-400 rounded-t-lg transition-all duration-300 group-hover:bg-amber-500 group-hover:scale-105 shadow-2xs relative"
                                     style="height: {{ max(18, $m['height_percent']) }}px;">
                                    <div class="opacity-0 group-hover:opacity-100 absolute -top-8 left-1/2 -translate-x-1/2 bg-slate-900 text-white text-[9px] font-mono px-1.5 py-0.5 rounded pointer-events-none whitespace-nowrap z-20">
                                        Rp {{ number_format($m['total'] / 1000000, 0) }} Jt
                                    </div>
                                </div>
                                <span class="text-[10px] font-bold text-slate-500 {{ $m['month_num'] === 10 ? 'text-indigo-600 font-black' : '' }}">{{ $m['month'] }}</span>
                            </div>
                            @endif
                        @endforeach
                    </div>

                    <!-- Ambient Curved Overlay Line (SVG Wave matching Image 2) -->
                    <svg class="absolute inset-0 w-full h-48 pointer-events-none opacity-80" viewBox="0 0 500 150" preserveAspectRatio="none">
                        <path d="M 0,130 C 60,120 120,110 180,105 C 240,100 300,95 360,85 C 420,70 470,55 500,45" fill="none" stroke="#6366f1" stroke-width="3" stroke-linecap="round" />
                        <circle cx="500" cy="45" r="4" fill="#6366f1" stroke="#ffffff" stroke-width="2" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Right: Pipeline Penjualan (5 cols) -->
        <div class="lg:col-span-5 bg-white p-6 rounded-3xl border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-black text-slate-900" x-text="lang === 'zh' ? '商机管道' : 'Pipeline Penjualan'">Pipeline Penjualan</h3>
                    <div class="flex items-center space-x-2">
                        <div class="flex items-center bg-slate-100 p-0.5 rounded-xl text-xs font-bold text-slate-600">
                            <button type="button" @click="pipelineType = 'sales'" :class="pipelineType === 'sales' ? 'bg-white text-slate-900 shadow-2xs' : 'text-slate-500'" class="px-2.5 py-1 rounded-lg transition">Sales</button>
                            <button type="button" @click="pipelineType = 'rental'" :class="pipelineType === 'rental' ? 'bg-white text-slate-900 shadow-2xs' : 'text-slate-500'" class="px-2.5 py-1 rounded-lg transition">Rental</button>
                        </div>
                        <a href="{{ route('erp.crm.index') }}" title="Buka CRM" class="w-8 h-8 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-600 flex items-center justify-center transition border border-slate-200">
                            ↗
                        </a>
                    </div>
                </div>

                <!-- Big Amount & Peluang Aktif -->
                <div class="mb-5">
                    <h2 class="text-3xl font-black text-slate-900 tracking-tight">
                        Rp {{ number_format($pipelineTotalValue, 0, ',', '.') }}
                    </h2>
                    <span class="text-xs font-bold text-slate-400">{{ $pipelineTotalCount }} peluang aktif</span>
                </div>

                <!-- Multi-segment Horizontal Stacked Bar (Image 2) -->
                <div class="w-full h-8 rounded-2xl overflow-hidden flex shadow-inner gap-1 p-1 bg-slate-100 mb-6">
                    <div class="bg-indigo-600 rounded-xl transition-all duration-300" style="width: {{ $pipelineStages['prospek']['percent'] }}%" title="Prospek: {{ $pipelineStages['prospek']['percent'] }}%"></div>
                    <div class="bg-sky-500 rounded-xl transition-all duration-300" style="width: {{ $pipelineStages['penawaran']['percent'] }}%" title="Penawaran: {{ $pipelineStages['penawaran']['percent'] }}%"></div>
                    <div class="bg-amber-400 rounded-xl transition-all duration-300" style="width: {{ $pipelineStages['negosiasi']['percent'] }}%" title="Negosiasi: {{ $pipelineStages['negosiasi']['percent'] }}%"></div>
                    <div class="bg-slate-900 rounded-xl transition-all duration-300" style="width: {{ $pipelineStages['order']['percent'] }}%" title="Order: {{ $pipelineStages['order']['percent'] }}%"></div>
                </div>

                <!-- Breakdown Stages List -->
                <div class="space-y-3.5 text-xs">
                    @foreach($pipelineStages as $key => $st)
                    <div class="flex items-center justify-between py-1.5 border-b border-slate-100 last:border-0">
                        <div class="flex items-center space-x-2.5">
                            <span class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $st['color'] }}"></span>
                            <span class="font-bold text-slate-800">{{ $st['label'] }}</span>
                        </div>
                        <div class="flex items-center space-x-4">
                            <span class="font-mono font-bold text-slate-500">{{ $st['count'] }}</span>
                            <span class="font-mono font-black text-slate-900 w-28 text-right">Rp {{ number_format($st['total'], 0, ',', '.') }}</span>
                            <span class="font-mono font-bold text-slate-400 w-10 text-right">{{ $st['percent'] }}%</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- LOWER ROW: PELUANG TERBESAR & FOLLOW UP HARI INI (IMAGE 2)                 -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left: Peluang Terbesar (7 cols) -->
        <div class="lg:col-span-7 bg-white p-6 rounded-3xl border border-slate-200/90 shadow-2xs">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-black text-slate-900" x-text="lang === 'zh' ? '最大商机' : 'Peluang Terbesar'">Peluang Terbesar</h3>
                <a href="{{ route('erp.crm.index') }}" class="w-8 h-8 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-600 flex items-center justify-center transition border border-slate-200">
                    ↗
                </a>
            </div>

            <!-- 3 Colored Deal Cards (Image 2) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @php
                    $dealThemes = [
                        0 => ['bg' => 'bg-[#111827] text-white', 'btn' => 'bg-white/10 hover:bg-white/20 text-white', 'sub' => 'text-slate-400', 'tag' => 'bg-white/15 text-white'],
                        1 => ['bg' => 'bg-[#4338ca] text-white', 'btn' => 'bg-white/10 hover:bg-white/20 text-white', 'sub' => 'text-indigo-200', 'tag' => 'bg-white/15 text-white'],
                        2 => ['bg' => 'bg-[#eab308] text-slate-950', 'btn' => 'bg-black/10 hover:bg-black/20 text-slate-950', 'sub' => 'text-slate-800', 'tag' => 'bg-black/10 text-slate-950'],
                    ];
                @endphp

                @foreach($topDeals as $index => $deal)
                    @php $th = $dealThemes[$index] ?? $dealThemes[0]; @endphp
                    <div class="{{ $th['bg'] }} p-5 rounded-3xl shadow-sm flex flex-col justify-between space-y-4">
                        <div>
                            <div class="flex items-center justify-between gap-1 mb-3">
                                <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full {{ $th['tag'] }}">
                                    Closing {{ $deal->expected_closing_date ? $deal->expected_closing_date->format('d M') : 'Akhir Bulan' }}
                                </span>
                                <button type="button" class="w-6 h-6 rounded-full {{ $th['btn'] }} flex items-center justify-center text-xs font-bold">
                                    ···
                                </button>
                            </div>
                            <h4 class="font-black text-sm tracking-tight leading-snug line-clamp-2">
                                {{ $deal->company_name }}
                            </h4>
                            <span class="text-[11px] font-semibold {{ $th['sub'] }} block mt-0.5">
                                {{ ucfirst($deal->deal_type ?? 'Rental') }}
                            </span>
                        </div>

                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider block {{ $th['sub'] }}">Estimasi nilai</span>
                            <h5 class="text-base font-black font-mono tracking-tight mt-0.5">
                                Rp {{ number_format($deal->estimated_value, 0, ',', '.') }}
                            </h5>
                            <!-- PIC Avatar badges -->
                            <div class="flex items-center -space-x-1.5 mt-2.5">
                                <span class="w-6 h-6 rounded-full bg-slate-900 text-white text-[9px] font-black flex items-center justify-center border-2 border-white">BS</span>
                                <span class="w-6 h-6 rounded-full bg-indigo-600 text-white text-[9px] font-black flex items-center justify-center border-2 border-white">SN</span>
                                <span class="w-6 h-6 rounded-full bg-amber-500 text-white text-[9px] font-black flex items-center justify-center border-2 border-white">AP</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Right: Follow Up Hari Ini (5 cols) -->
        <div class="lg:col-span-5 bg-white p-6 rounded-3xl border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-black text-slate-900" x-text="lang === 'zh' ? '今日跟进' : 'Follow Up Hari Ini'">Follow Up Hari Ini</h3>
                    <a href="{{ route('erp.crm.index') }}" class="w-8 h-8 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-600 flex items-center justify-center transition border border-slate-200">
                        ↗
                    </a>
                </div>

                <div class="space-y-3">
                    @forelse($salesFollowUps as $i => $fu)
                        @php
                            $icons = [
                                0 => ['bg' => 'bg-amber-100 text-amber-700', 'svg' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'default_time' => '09:00'],
                                1 => ['bg' => 'bg-sky-100 text-sky-700', 'svg' => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z', 'default_time' => '11:00'],
                                2 => ['bg' => 'bg-indigo-100 text-indigo-700', 'svg' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z', 'default_time' => '14:00'],
                                3 => ['bg' => 'bg-slate-100 text-slate-700', 'svg' => 'M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0zM13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1', 'default_time' => '16:00'],
                            ];
                            $ic = $icons[$i % 4];
                        @endphp
                        <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50/80 hover:bg-slate-100/90 transition border border-slate-100">
                            <div class="flex items-center space-x-3 min-w-0">
                                <div class="w-9 h-9 rounded-xl {{ $ic['bg'] }} flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $ic['svg'] }}"></path></svg>
                                </div>
                                <div class="min-w-0">
                                    <h5 class="text-xs font-black text-slate-900 truncate">{{ $fu->lead->company_name ?? 'PT Calon Pelanggan' }}</h5>
                                    <p class="text-[11px] text-slate-500 truncate mt-0.5">{{ $fu->purpose_notes ?: ($fu->follow_up_type . ' Follow up closing') }}</p>
                                </div>
                            </div>
                            <span class="text-xs font-black font-mono text-slate-900 shrink-0 ml-3">
                                {{ $fu->scheduled_at ? $fu->scheduled_at->format('H:i') : $ic['default_time'] }}
                            </span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 py-4 text-center">Belum ada agenda follow-up hari ini.</p>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- BOTTOM SECTION: PENAWARAN MENDEKATI KADALUARSA & AKSI CEPAT (IMAGE 2)     -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left: Penawaran Mendekati Kadaluarsa (7 cols) -->
        <div class="lg:col-span-7 bg-white p-6 rounded-3xl border border-slate-200/90 shadow-2xs">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-black text-slate-900" x-text="lang === 'zh' ? '即将到期报价' : 'Penawaran Mendekati Kadaluarsa'">Penawaran Mendekati Kadaluarsa</h3>
                <a href="{{ route('erp.orders.index') }}" class="w-8 h-8 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-600 flex items-center justify-center transition border border-slate-200">
                    ↗
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="text-slate-400 text-[10px] font-black uppercase tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="pb-3 px-2">No. Quotation</th>
                            <th class="pb-3 px-2">Customer</th>
                            <th class="pb-3 px-2">Nilai</th>
                            <th class="pb-3 px-2 text-right">Berakhir</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($expiringQuotations as $idx => $q)
                            @php
                                $daysRem = $idx === 0 ? 2 : ($idx === 1 ? 3 : 5);
                                $badgeColor = $daysRem <= 2 ? 'bg-rose-50 text-rose-600 border-rose-200' : ($daysRem <= 3 ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-sky-50 text-sky-700 border-sky-200');
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3 px-2 font-mono font-black text-slate-900">{{ $q->order_number }}</td>
                                <td class="py-3 px-2 font-bold text-slate-800">{{ $q->contact->name ?? 'PT Customer' }}</td>
                                <td class="py-3 px-2 font-mono font-black text-slate-900">Rp {{ number_format($q->total_amount, 0, ',', '.') }}</td>
                                <td class="py-3 px-2 text-right">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold border {{ $badgeColor }}">
                                        {{ $daysRem }} hari lagi
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-4 text-center text-slate-400">Tidak ada penawaran mendekati kadaluarsa.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right: Aksi Cepat (5 cols) -->
        <div class="lg:col-span-5 bg-white p-6 rounded-3xl border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <div>
                <h3 class="text-base font-black text-slate-900 mb-4" x-text="lang === 'zh' ? '快捷操作' : 'Aksi Cepat'">Aksi Cepat</h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <!-- 1. Buat Penawaran -->
                    <button type="button" @click="modalQuotationOpen = true" 
                            class="p-4 rounded-2xl bg-slate-50 hover:bg-amber-50 hover:border-amber-300 border border-slate-200/80 transition flex items-center justify-between text-left group">
                        <div class="flex items-center space-x-3">
                            <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-black shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <span class="text-xs font-black text-slate-800 group-hover:text-amber-800">Buat Penawaran</span>
                        </div>
                        <span class="text-slate-400 font-bold group-hover:translate-x-1 transition-transform">›</span>
                    </button>

                    <!-- 2. Buat Sales Order -->
                    <button type="button" @click="modalQuotationOpen = true"
                            class="p-4 rounded-2xl bg-slate-50 hover:bg-blue-50 hover:border-blue-300 border border-slate-200/80 transition flex items-center justify-between text-left group">
                        <div class="flex items-center space-x-3">
                            <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-black shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                            </div>
                            <span class="text-xs font-black text-slate-800 group-hover:text-blue-800">Buat Sales Order</span>
                        </div>
                        <span class="text-slate-400 font-bold group-hover:translate-x-1 transition-transform">›</span>
                    </button>

                    <!-- 3. Buat Kontrak Sewa -->
                    <button type="button" @click="modalQuotationOpen = true"
                            class="p-4 rounded-2xl bg-slate-50 hover:bg-indigo-50 hover:border-indigo-300 border border-slate-200/80 transition flex items-center justify-between text-left group">
                        <div class="flex items-center space-x-3">
                            <div class="w-9 h-9 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center font-black shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <span class="text-xs font-black text-slate-800 group-hover:text-indigo-800">Buat Kontrak Sewa</span>
                        </div>
                        <span class="text-slate-400 font-bold group-hover:translate-x-1 transition-transform">›</span>
                    </button>

                    <!-- 4. Lihat Semua Transaksi -->
                    <a href="{{ route('erp.orders.index') }}" 
                       class="p-4 rounded-2xl bg-slate-50 hover:bg-slate-100 hover:border-slate-300 border border-slate-200/80 transition flex items-center justify-between text-left group">
                        <div class="flex items-center space-x-3">
                            <div class="w-9 h-9 rounded-xl bg-slate-200 text-slate-700 flex items-center justify-center font-black shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                            </div>
                            <span class="text-xs font-black text-slate-800">Semua Transaksi</span>
                        </div>
                        <span class="text-slate-400 font-bold group-hover:translate-x-1 transition-transform">›</span>
                    </a>
                </div>
            </div>
        </div>

    </div>

</div>
