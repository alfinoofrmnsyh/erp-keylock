@extends('layouts.erp')

@section('title', 'ERP KEYLOCK ID - Mobile Portal')

@section('content')
<!-- Covera UI Theme Container: Soft Ice/Sky Tinted Background (#EEF5F9) -->
<div class="min-h-screen bg-[#EEF5F9] text-slate-800 pb-28 max-w-md mx-auto relative shadow-2xl border-x border-slate-200/60 font-sans" x-data="coveraMobileErp()">

    <!-- ========================================================================= -->
    <!-- 1. STATUS & TOP BAR (COVERA SCREEN 1 STYLE)                              -->
    <!-- ========================================================================= -->
    <header class="bg-white/80 backdrop-blur-md sticky top-0 z-30 px-5 pt-3 pb-3 border-b border-slate-150/70 flex items-center justify-between">
        <!-- Left: Circular Logo Button -->
        <a href="{{ route('mobile') }}" class="w-10 h-10 rounded-full bg-white shadow-xs border border-slate-200/80 flex items-center justify-center p-2 hover:bg-slate-50 transition shrink-0" title="ERP KEYLOCK ID">
            <img src="/images/keylock-logo.svg" alt="Keylock Logo" class="w-6 h-6 object-contain">
        </a>

        <!-- Center: Clean Brand Title (Strictly ERP KEYLOCK ID - Requirement 5) -->
        <div class="text-center">
            <h1 class="text-base font-extrabold text-slate-900 tracking-tight leading-none">ERP KEYLOCK ID</h1>
            <span class="text-[10px] font-semibold text-slate-400">Operasional & Penawaran</span>
        </div>

        <!-- Right: Language Selector Pill (Strictly ID / 中文 - Requirement 6) -->
        <div class="flex items-center bg-slate-100/90 p-0.5 rounded-full border border-slate-200/80">
            <button type="button" @click="setLang('id')"
                    :class="lang === 'id' ? 'bg-white text-slate-900 font-extrabold shadow-xs' : 'text-slate-500 font-medium'"
                    class="px-2.5 py-1 rounded-full text-[11px] transition">
                ID
            </button>
            <button type="button" @click="setLang('zh')"
                    :class="lang === 'zh' ? 'bg-white text-slate-900 font-extrabold shadow-xs' : 'text-slate-500 font-medium'"
                    class="px-2.5 py-1 rounded-full text-[11px] transition">
                中文
            </button>
        </div>
    </header>

    <!-- ========================================================================= -->
    <!-- 2. PILL SEARCH BAR (COVERA PILL INPUT WITH FILTER SLIDERS)                 -->
    <!-- ========================================================================= -->
    <div class="px-5 pt-4 pb-2">
        <div class="bg-white rounded-full px-4 py-2.5 shadow-xs border border-slate-200/70 flex items-center justify-between space-x-2">
            <div class="flex items-center space-x-2.5 flex-1">
                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input type="text" x-model="searchQuery"
                       :placeholder="lang === 'zh' ? '搜索设备、客户或单据...' : 'Cari armada, pelanggan, atau penawaran...'"
                       class="w-full bg-transparent text-xs text-slate-800 placeholder-slate-400 focus:outline-none">
            </div>
            <!-- Sliders / Filter icon -->
            <button @click="showFilterMenu = !showFilterMenu" class="p-1 text-slate-400 hover:text-slate-700 transition" title="Filter Transaksi">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                </svg>
            </button>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 3. COVERA SCREEN 2 BANNER: KEYLOCK MANAGEMENT CENTER (CYAN-TO-BLUE)       -->
    <!-- ========================================================================= -->
    <div class="px-5 py-2">
        <div class="bg-gradient-to-r from-[#17B8F0] via-[#0EA5E9] to-[#0284C7] rounded-[24px] p-5 text-white shadow-md relative overflow-hidden">
            <!-- Decorative circle blur -->
            <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-white/15 rounded-full blur-xl pointer-events-none"></div>

            <div class="flex items-center justify-between mb-3">
                <div class="w-8 h-8 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center p-1.5 border border-white/30">
                    <img src="/images/keylock-logo.svg" alt="Keylock" class="w-full h-full object-contain filter brightness-200">
                </div>
                <span class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full bg-white/20 backdrop-blur-md text-[11px] font-semibold border border-white/25">
                    <span class="w-2 h-2 rounded-full bg-emerald-300 animate-pulse"></span>
                    <span x-text="lang === 'zh' ? '系统已联网就绪' : 'We hope all is good!'"></span>
                </span>
            </div>

            <h2 class="text-lg font-black tracking-tight" x-text="lang === 'zh' ? 'Keylock 业务控制中心' : 'Keylock Center'"></h2>
            <p class="text-xs text-sky-100 mt-0.5 leading-relaxed" x-text="lang === 'zh' ? '高效制作专业报价单，快速跟进叉车与重机租赁订单' : 'Pembuatan penawaran cepat & manajemen operasional armada terpadu'"></p>

            <!-- Mini Pipeline Strip inside Banner -->
            <div class="mt-4 pt-3 border-t border-white/20 flex items-center justify-between">
                <div>
                    <span class="text-[10px] text-sky-200 uppercase font-semibold block" x-text="lang === 'zh' ? '进行中商机总额' : 'Total Pipeline'"></span>
                    <span class="text-sm font-black tracking-tight text-white">Rp {{ number_format($totalPipelineAmount, 0, ',', '.') }}</span>
                </div>
                <div class="text-right">
                    <span class="text-[10px] text-sky-200 uppercase font-semibold block" x-text="lang === 'zh' ? '在租设备' : 'Unit Disewa'"></span>
                    <span class="text-sm font-black text-white">{{ $rentedAssetsCount }} / {{ $availableAssets->count() + $rentedAssetsCount }} Unit</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 4. SECTION: AKSES CEPAT TRANSAKSI (COVERA: "Login to access" 3 TILES)     -->
    <!-- ========================================================================= -->
    <div class="px-5 pt-3 pb-1">
        <h3 class="text-sm font-bold text-slate-900 mb-3" x-text="lang === 'zh' ? '快速业务通道' : 'Akses Cepat Transaksi'"></h3>
        
        <div class="grid grid-cols-3 gap-3">
            <!-- Tile 1: Buat QTN (Soft Lavender/Indigo Gradient) -->
            <button @click="modalQuotationOpen = true" class="bg-white rounded-[22px] p-3.5 flex flex-col items-center justify-center shadow-xs border border-slate-150/80 active:scale-95 transition group hover:border-indigo-300">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-100 via-indigo-50 to-indigo-100 text-indigo-600 flex items-center justify-center shadow-inner group-hover:scale-105 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <span class="text-[11px] font-bold text-slate-800 mt-2 text-center" x-text="lang === 'zh' ? '创建报价' : 'Buat QTN'"></span>
            </button>

            <!-- Tile 2: Order Sewa (Soft Coral/Rose Gradient) -->
            <button @click="activeTab = 'rental_order'" class="bg-white rounded-[22px] p-3.5 flex flex-col items-center justify-center shadow-xs border border-slate-150/80 active:scale-95 transition group hover:border-rose-300">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-rose-100 via-rose-50 to-rose-100 text-rose-500 flex items-center justify-center shadow-inner group-hover:scale-105 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                    </svg>
                </div>
                <span class="text-[11px] font-bold text-slate-800 mt-2 text-center" x-text="lang === 'zh' ? '租赁订单' : 'Order Sewa'"></span>
            </button>

            <!-- Tile 3: Order Jual (Soft Purple/Violet Gradient) -->
            <button @click="activeTab = 'sales_order'" class="bg-white rounded-[22px] p-3.5 flex flex-col items-center justify-center shadow-xs border border-slate-150/80 active:scale-95 transition group hover:border-purple-300">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-purple-100 via-purple-50 to-purple-100 text-purple-600 flex items-center justify-center shadow-inner group-hover:scale-105 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
                <span class="text-[11px] font-bold text-slate-800 mt-2 text-center" x-text="lang === 'zh' ? '销售订单' : 'Order Jual'"></span>
            </button>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 5. HERO DARK PILL CARD (COVERA: "Renew Your Plan" WITH '+' BUTTON)        -->
    <!-- ========================================================================= -->
    <div class="px-5 py-2.5">
        <div @click="modalQuotationOpen = true" class="bg-[#0F1F2C] text-white rounded-[24px] p-4 flex items-center justify-between shadow-md border border-slate-800 cursor-pointer active:scale-[0.99] transition group">
            <div class="flex items-center space-x-3.5">
                <!-- Cyan Icon Badge -->
                <div class="w-11 h-11 rounded-2xl bg-sky-500/20 text-sky-400 flex items-center justify-center shrink-0 border border-sky-400/20 group-hover:bg-sky-500/30 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-white leading-tight" x-text="lang === 'zh' ? '快速生成报价与合同' : 'Buat Penawaran Cepat'"></h4>
                    <p class="text-[11px] text-slate-400 mt-0.5" x-text="lang === 'zh' ? '30天有效期 · 自动计税 · PDF即开' : '30 hari berlaku · Hitung PPN · Cetak PDF'"></p>
                </div>
            </div>

            <!-- Dark Pill Circular Button '+' -->
            <div class="w-9 h-9 rounded-full bg-slate-800 text-white flex items-center justify-center border border-slate-700 shadow-sm shrink-0 group-hover:bg-slate-700 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 6. SECTION: LAYANAN & ARMADA (COVERA: "Buy or ...", "View All >")        -->
    <!-- ========================================================================= -->
    <div class="px-5 pt-3 pb-1">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-bold text-slate-900" x-text="lang === 'zh' ? '设备目录与服务' : 'Layanan & Armada'"></h3>
            <button @click="alert('Katalog Armada Keylock: Total ' + {{ count($availableAssets) }} + ' unit siap sewa.')" class="text-xs font-semibold text-sky-600 hover:text-sky-700 transition">
                <span x-text="lang === 'zh' ? '查看全部 >' : 'Lihat Semua >'"></span>
            </button>
        </div>

        <!-- 4 Grid Rounded Cards -->
        <div class="grid grid-cols-2 gap-3">
            <!-- 1. Forklift -->
            <div class="bg-white rounded-[22px] p-3.5 border border-slate-150/80 shadow-xs flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </div>
                <div class="overflow-hidden">
                    <h5 class="text-xs font-bold text-slate-900 truncate">Sewa Forklift</h5>
                    <p class="text-[10px] text-slate-400 truncate">Diesel & Elektrik</p>
                </div>
            </div>

            <!-- 2. Alat Berat Boom Lift -->
            <div class="bg-white rounded-[22px] p-3.5 border border-slate-150/80 shadow-xs flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 11l7-7 7 7M5 19l7-7 7 7"></path></svg>
                </div>
                <div class="overflow-hidden">
                    <h5 class="text-xs font-bold text-slate-900 truncate">Alat Angkat</h5>
                    <p class="text-[10px] text-slate-400 truncate">Scissor & Boom Lift</p>
                </div>
            </div>

            <!-- 3. Towing Logistik -->
            <div class="bg-white rounded-[22px] p-3.5 border border-slate-150/80 shadow-xs flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0zM13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1"></path></svg>
                </div>
                <div class="overflow-hidden">
                    <h5 class="text-xs font-bold text-slate-900 truncate">Towing Logistik</h5>
                    <p class="text-[10px] text-slate-400 truncate">Kirim Seluruh RI</p>
                </div>
            </div>

            <!-- 4. Kontrak Maintenance -->
            <div class="bg-white rounded-[22px] p-3.5 border border-slate-150/80 shadow-xs flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </div>
                <div class="overflow-hidden">
                    <h5 class="text-xs font-bold text-slate-900 truncate">Maintenance</h5>
                    <p class="text-[10px] text-slate-400 truncate">Servis Rutin Berkala</p>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 7. TRANSAKSI INDUK DOKUMEN & FILTER PILLS                                 -->
    <!-- ========================================================================= -->
    <div class="px-5 pt-4 pb-1">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-bold text-slate-900" x-text="lang === 'zh' ? '单据记录' : 'Transaksi Dokumen'"></h3>
            <span class="text-xs font-semibold text-slate-500" x-text="lang === 'zh' ? '共 {{ count($orders) }} 笔单据' : 'Total: {{ count($orders) }} Dokumen'"></span>
        </div>

        <!-- Filter Horizontal Tabs -->
        <div class="flex items-center space-x-1.5 overflow-x-auto pb-1 no-scrollbar">
            <button @click="activeTab = 'semua'"
                    :class="activeTab === 'semua' ? 'bg-sky-500 text-white font-bold shadow-xs' : 'bg-white text-slate-600 border border-slate-200/80'"
                    class="px-3.5 py-1.5 rounded-full text-xs whitespace-nowrap transition">
                <span x-text="lang === 'zh' ? '全部' : 'Semua'"></span> ({{ count($orders) }})
            </button>
            <button @click="activeTab = 'quotation'"
                    :class="activeTab === 'quotation' ? 'bg-sky-500 text-white font-bold shadow-xs' : 'bg-white text-slate-600 border border-slate-200/80'"
                    class="px-3.5 py-1.5 rounded-full text-xs whitespace-nowrap transition">
                <span x-text="lang === 'zh' ? '报价' : 'Penawaran'"></span> ({{ $orders->where('order_type', 'quotation')->count() }})
            </button>
            <button @click="activeTab = 'rental_order'"
                    :class="activeTab === 'rental_order' ? 'bg-sky-500 text-white font-bold shadow-xs' : 'bg-white text-slate-600 border border-slate-200/80'"
                    class="px-3.5 py-1.5 rounded-full text-xs whitespace-nowrap transition">
                <span x-text="lang === 'zh' ? '租赁' : 'Sewa'"></span> ({{ $orders->where('order_type', 'rental_order')->count() }})
            </button>
            <button @click="activeTab = 'sales_order'"
                    :class="activeTab === 'sales_order' ? 'bg-sky-500 text-white font-bold shadow-xs' : 'bg-white text-slate-600 border border-slate-200/80'"
                    class="px-3.5 py-1.5 rounded-full text-xs whitespace-nowrap transition">
                <span x-text="lang === 'zh' ? '销售' : 'Jual'"></span> ({{ $orders->where('order_type', 'sales_order')->count() }})
            </button>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 8. COVERA TRANSACTIONS LIST FEED (TAP TO DRAWER & DIRECT PDF PRINT)       -->
    <!-- ========================================================================= -->
    <div class="px-5 py-2 space-y-3">
        @forelse($orders as $order)
            <div x-show="matchesTab('{{ $order->order_type }}') && matchesSearch('{{ strtolower($order->order_number . ' ' . ($order->contact->name ?? '')) }}')"
                 @click="openOrderDrawer({{ $order->id }})"
                 class="bg-white rounded-[22px] p-4 border border-slate-150/80 shadow-xs active:scale-[0.99] transition cursor-pointer hover:border-sky-300">
                
                <div class="flex items-start justify-between">
                    <div class="flex items-start space-x-3">
                        <!-- Type Pill Badge -->
                        @if($order->order_type === 'quotation')
                            <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 font-extrabold text-[11px] flex items-center justify-center shrink-0 border border-indigo-100">
                                QTN
                            </div>
                        @elseif($order->order_type === 'rental_order')
                            <div class="w-10 h-10 rounded-2xl bg-rose-50 text-rose-500 font-extrabold text-[11px] flex items-center justify-center shrink-0 border border-rose-100">
                                RO
                            </div>
                        @else
                            <div class="w-10 h-10 rounded-2xl bg-purple-50 text-purple-600 font-extrabold text-[11px] flex items-center justify-center shrink-0 border border-purple-100">
                                SO
                            </div>
                        @endif

                        <div class="overflow-hidden">
                            <h4 class="text-xs font-bold text-slate-900 truncate max-w-[190px]">
                                {{ $order->contact->name ?? 'Pelanggan Umum' }}
                            </h4>
                            <div class="flex items-center space-x-1.5 mt-0.5">
                                <span class="font-mono text-[10px] text-slate-500 font-semibold">{{ $order->order_number }}</span>
                                <span class="text-slate-300">•</span>
                                <span class="text-[10px] text-slate-400">{{ date('d M Y', strtotime($order->order_date)) }}</span>
                            </div>
                            
                            @if($order->items->count() > 0)
                                <p class="text-[10px] text-slate-500 truncate max-w-[200px] mt-1">
                                    {{ $order->items->first()->item->name ?? $order->items->first()->description }}
                                    @if($order->items->count() > 1)
                                        <span class="text-slate-400 font-semibold">(+{{ $order->items->count() - 1 }} item)</span>
                                    @endif
                                </p>
                            @endif
                        </div>
                    </div>

                    <!-- Right Column: Amount, Status & Quick Print -->
                    <div class="text-right shrink-0">
                        <div class="text-xs font-black text-slate-900">
                            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                        </div>
                        <div class="mt-1 flex items-center justify-end space-x-1.5">
                            @if($order->status === 'draft')
                                <span class="text-[9px] font-extrabold uppercase px-2 py-0.5 rounded-full bg-slate-100 text-slate-600">Draft</span>
                            @elseif($order->status === 'sent')
                                <span class="text-[9px] font-extrabold uppercase px-2 py-0.5 rounded-full bg-blue-50 text-blue-700">Terkirim</span>
                            @elseif($order->status === 'approved')
                                <span class="text-[9px] font-extrabold uppercase px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700">Disetujui</span>
                            @elseif($order->status === 'in_progress')
                                <span class="text-[9px] font-extrabold uppercase px-2 py-0.5 rounded-full bg-amber-50 text-amber-700">Berjalan</span>
                            @else
                                <span class="text-[9px] font-extrabold uppercase px-2 py-0.5 rounded-full bg-slate-100 text-slate-700">{{ $order->status }}</span>
                            @endif

                            <!-- Direct Print PDF Button -->
                            <a href="{{ route('erp.quotations.pdf', $order->id) }}" target="_blank" @click.stop title="Cetak PDF Kop Resmi"
                               class="p-1 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-[22px] p-8 text-center border border-slate-150/80">
                <svg class="w-10 h-10 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <p class="text-xs font-bold text-slate-700">Belum Ada Transaksi</p>
                <p class="text-[11px] text-slate-400 mt-1">Buat penawaran baru dengan tombol (+) di bawah.</p>
            </div>
        @endforelse
    </div>

    <!-- ========================================================================= -->
    <!-- 9. BOTTOM NAVIGATION DOCK (COVERA DOCK STYLE)                             -->
    <!-- ========================================================================= -->
    <div class="fixed bottom-0 left-0 right-0 max-w-md mx-auto bg-white/95 backdrop-blur-md border-t border-slate-150/80 z-30 px-6 py-2.5 flex items-center justify-between shadow-[0_-5px_25px_rgba(0,0,0,0.05)] rounded-t-[28px]">
        <!-- 1. Beranda (Active) -->
        <a href="{{ route('mobile') }}" class="flex flex-col items-center text-slate-900 group">
            <svg class="w-5 h-5 text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            <span class="text-[10px] font-bold mt-1" x-text="lang === 'zh' ? '首页' : 'Home'"></span>
        </a>

        <!-- 2. Penawaran -->
        <button @click="activeTab = 'quotation'" class="flex flex-col items-center text-slate-400 hover:text-slate-900 group transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <span class="text-[10px] font-medium mt-1" x-text="lang === 'zh' ? '报价' : 'Penawaran'"></span>
        </button>

        <!-- 3. Buat Penawaran Floating Center Button -->
        <div class="-mt-8">
            <button @click="modalQuotationOpen = true" class="w-13 h-13 rounded-full bg-gradient-to-r from-sky-400 to-blue-600 text-white flex items-center justify-center shadow-lg border-4 border-white active:scale-95 transition" title="Buat Penawaran Cepat">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
            </button>
        </div>

        <!-- 4. Armada -->
        <button @click="alert('Katalog Armada: Total {{ count($availableAssets) }} unit siap sewa.')" class="flex flex-col items-center text-slate-400 hover:text-slate-900 group transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            <span class="text-[10px] font-medium mt-1" x-text="lang === 'zh' ? '设备' : 'Armada'"></span>
        </button>

        <!-- 5. Portal Web & CMS -->
        <a href="{{ route('erp.index') }}" class="flex flex-col items-center text-slate-400 hover:text-slate-900 group transition" title="Buka Portal Desktop ERP">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            <span class="text-[10px] font-medium mt-1" x-text="lang === 'zh' ? '桌面端' : 'Desktop'"></span>
        </a>
    </div>

    <!-- ========================================================================= -->
    <!-- 10. MODAL BUAT PENAWARAN (COVERA SCREEN 3 FORM STYLE)                     -->
    <!-- ========================================================================= -->
    <div x-show="modalQuotationOpen"
         style="display: none;"
         class="fixed inset-0 z-50 overflow-y-auto"
         role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs transition-opacity" @click="modalQuotationOpen = false"></div>

        <div class="min-h-full flex items-center justify-center p-4">
            <div class="bg-white rounded-[28px] max-w-sm w-full overflow-hidden shadow-2xl border border-slate-150 relative z-10">
                
                <!-- Covera Screen 3 Header -->
                <div class="px-6 py-4 bg-slate-50/70 border-b border-slate-100 flex items-center justify-between">
                    <button type="button" @click="modalQuotationOpen = false" class="w-8 h-8 rounded-full bg-white border border-slate-200/80 flex items-center justify-center text-slate-600 hover:bg-slate-100 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                    </button>
                    <h3 class="text-sm font-extrabold text-slate-900" x-text="lang === 'zh' ? '创建价格报价单' : 'Form Penawaran Harga'"></h3>
                    <div class="w-8 h-8 flex items-center justify-center text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>

                <!-- Form Content (Covera Pill Form) -->
                <form action="{{ route('erp.quotations.store') }}" method="POST" class="p-6 space-y-3.5 text-xs">
                    @csrf

                    <!-- Tabs Rental vs Penjualan (Requirement 2.a) -->
                    <div class="flex items-center space-x-2 bg-slate-100 p-1 rounded-2xl mb-2">
                        <button type="button" @click="setMobileQuotationType('rental')"
                                :class="mobileQuotationType === 'rental' ? 'bg-white text-slate-900 font-extrabold shadow-xs' : 'text-slate-500 font-semibold'"
                                class="flex-1 py-1.5 rounded-xl text-center text-[11px] transition">
                            Rental (Sewa)
                        </button>
                        <button type="button" @click="setMobileQuotationType('sales')"
                                :class="mobileQuotationType === 'sales' ? 'bg-white text-slate-900 font-extrabold shadow-xs' : 'text-slate-500 font-semibold'"
                                class="flex-1 py-1.5 rounded-xl text-center text-[11px] transition">
                            Penjualan (Sales)
                        </button>
                    </div>
                    <input type="hidden" name="quotation_type" :value="mobileQuotationType">

                    <!-- Customer Dropdown -->
                    <div>
                        <label class="block font-bold text-slate-700 text-[11px] mb-1.5" x-text="lang === 'zh' ? '客户 / 公司名称 *' : 'Pelanggan / Perusahaan *'"></label>
                        <select name="contact_id" required class="w-full bg-[#F4F8FA] border border-[#E2EAF0] rounded-2xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-sky-400 focus:bg-white transition">
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}">{{ $c->name }} (PIC: {{ $c->pic_name ?? 'Umum' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date & Auto 30-Day Validity -->
                    <div class="grid grid-cols-2 gap-2.5">
                        <div>
                            <label class="block font-bold text-slate-700 text-[11px] mb-1.5" x-text="lang === 'zh' ? '报价日期 *' : 'Tanggal *'"></label>
                            <input type="date" name="order_date" value="{{ date('Y-m-d') }}" required class="w-full bg-[#F4F8FA] border border-[#E2EAF0] rounded-2xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-sky-400 focus:bg-white transition">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 text-[11px] mb-1.5" x-text="lang === 'zh' ? '有效期 (自动30天)' : 'Masa Berlaku'"></label>
                            <input type="date" name="valid_until" value="{{ date('Y-m-d', strtotime('+30 days')) }}" required class="w-full bg-[#F4F8FA] border border-[#E2EAF0] rounded-2xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-sky-400 focus:bg-white transition">
                        </div>
                    </div>

                    <!-- Unit Selector -->
                    <div>
                        <label class="block font-bold text-slate-700 text-[11px] mb-1.5" x-text="lang === 'zh' ? '选择设备型号 *' : 'Pilih Unit Armada *'"></label>
                        <select name="items[0][item_id]" x-model="quickItemId" @change="onItemChange()" required class="w-full bg-[#F4F8FA] border border-[#E2EAF0] rounded-2xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-sky-400 focus:bg-white transition">
                            @foreach($catalogItems as $item)
                                <option value="{{ $item->id }}" data-price="{{ $item->rental_rate_month ?? $item->rental_rate_monthly }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Quantity & Rate + Duration if Rental -->
                    <div class="grid grid-cols-2 gap-2.5">
                        <div>
                            <label class="block font-bold text-slate-700 text-[11px] mb-1.5" x-text="lang === 'zh' ? '数量 (台) *' : 'Jumlah Unit *'"></label>
                            <input type="number" name="items[0][quantity]" x-model.number="quickQty" min="1" value="1" required class="w-full bg-[#F4F8FA] border border-[#E2EAF0] rounded-2xl px-3 py-2 text-xs text-center font-bold text-slate-900 focus:outline-none focus:border-sky-400 focus:bg-white transition">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 text-[11px] mb-1.5" x-text="mobileQuotationType === 'rental' ? 'Tarif Sewa (Rp) *' : 'Harga Jual (Rp) *'"></label>
                            <input type="hidden" name="items[0][unit_price]" :value="quickPrice">
                            <input type="text"
                                   inputmode="numeric"
                                   data-rupiah
                                   :value="formatNumberWithDots(quickPrice)"
                                   @input="quickPrice = parseNumberFromDots($event.target.value)"
                                   placeholder="0"
                                   required
                                   class="w-full bg-[#F4F8FA] border border-[#E2EAF0] rounded-2xl px-3 py-2 text-xs font-mono font-bold text-slate-900 focus:outline-none focus:border-sky-400 focus:bg-white transition">
                        </div>
                    </div>

                    <!-- Duration if Rental (Requirement 2.c) -->
                    <template x-if="mobileQuotationType === 'rental'">
                        <div class="grid grid-cols-2 gap-2.5">
                            <div>
                                <label class="block font-bold text-slate-700 text-[11px] mb-1.5">Durasi Rental *</label>
                                <input type="number" name="items[0][duration]" x-model.number="quickDuration" min="1" value="1" class="w-full bg-[#F4F8FA] border border-[#E2EAF0] rounded-2xl px-3 py-2 text-xs text-center font-bold text-slate-900 focus:outline-none focus:border-sky-400 focus:bg-white transition">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-700 text-[11px] mb-1.5">Satuan Durasi *</label>
                                <select name="items[0][duration_unit]" x-model="quickDurationUnit" class="w-full bg-[#F4F8FA] border border-[#E2EAF0] rounded-2xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-sky-400 focus:bg-white transition">
                                    <option value="Bulan">Bulan</option>
                                    <option value="Minggu">Minggu</option>
                                    <option value="Hari">Hari</option>
                                    <option value="Jam">Jam</option>
                                </select>
                            </div>
                        </div>
                    </template>
                    <input type="hidden" name="rental_duration" :value="quickDuration">
                    <input type="hidden" name="rental_duration_unit" value="month">

                    <!-- Estimated Subtotal & PPN 11% Box -->
                    <div class="p-3.5 bg-sky-50/80 rounded-2xl border border-sky-200/80 flex items-center justify-between">
                        <span class="text-[11px] font-bold text-slate-700" x-text="lang === 'zh' ? '预估总价 (含11%增值税):' : 'Total Estimasi (+PPN 11%):'"></span>
                        <span class="text-sm font-black text-sky-700" x-text="'Rp ' + Number(calculateTotal()).toLocaleString('id-ID')"></span>
                    </div>

                    <!-- Action Buttons (Covera Bottom Pill Button) -->
                    <div class="pt-2">
                        <button type="submit" class="w-full bg-[#38B6FF] hover:bg-[#20A6F0] text-white font-extrabold py-3.5 rounded-full shadow-md text-xs transition flex items-center justify-center space-x-2 active:scale-[0.99]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            <span x-text="lang === 'zh' ? '保存并开具报价单' : 'Simpan & Terbitkan Penawaran'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 11. SLIDE-UP DETAIL DRAWER (BOTTOM SHEET WITH PDF PRINT)                  -->
    <!-- ========================================================================= -->
    <div x-show="drawerOpen"
         style="display: none;"
         class="fixed inset-0 z-50 overflow-hidden"
         role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs transition-opacity"
             @click="drawerOpen = false"></div>

        <div class="fixed inset-x-0 bottom-0 max-w-md mx-auto max-h-[90vh] bg-white rounded-t-[32px] shadow-2xl flex flex-col overflow-hidden"
             x-show="drawerOpen"
             x-transition:enter="transform transition ease-out duration-300"
             x-transition:enter-start="translate-y-full"
             x-transition:enter-end="translate-y-0"
             x-transition:leave="transform transition ease-in duration-200"
             x-transition:leave-start="translate-y-0"
             x-transition:leave-end="translate-y-full">

            <!-- Drag Handle Bar -->
            <div class="pt-3 pb-1 flex justify-center">
                <div class="w-12 h-1.5 rounded-full bg-slate-300"></div>
            </div>

            <!-- Drawer Header -->
            <div class="px-6 py-3.5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold text-sky-600 uppercase tracking-wider block" x-text="selectedOrder ? selectedOrder.order_type.toUpperCase() : ''"></span>
                    <h3 class="text-base font-extrabold text-slate-900" x-text="selectedOrder ? selectedOrder.order_number : ''"></h3>
                </div>
                <button @click="drawerOpen = false" class="p-2 text-slate-400 hover:text-slate-600 rounded-full hover:bg-slate-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Drawer Scrollable Content -->
            <div class="p-6 overflow-y-auto space-y-4 text-xs" x-show="selectedOrder">
                <!-- Customer Details Card -->
                <div class="bg-slate-50 p-4 rounded-[22px] border border-slate-150">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Pelanggan / PIC</span>
                    <h4 class="text-sm font-extrabold text-slate-900" x-text="selectedOrder ? (selectedOrder.contact ? selectedOrder.contact.name : '-') : ''"></h4>
                    <p class="text-slate-600 mt-1" x-text="selectedOrder ? (selectedOrder.contact ? selectedOrder.contact.pic_name + ' (' + (selectedOrder.contact.phone || '-') + ')' : '-') : ''"></p>
                </div>

                <!-- Items Breakdown -->
                <div>
                    <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-2">Rincian Unit & Tarif</h4>
                    <div class="space-y-2">
                        <template x-for="item in (selectedOrder ? selectedOrder.items : [])" :key="item.id">
                            <div class="p-3 bg-white rounded-xl border border-slate-200 flex items-center justify-between">
                                <div>
                                    <h5 class="font-bold text-slate-900 text-xs" x-text="item.item ? item.item.name : item.description"></h5>
                                    <p class="text-[11px] text-slate-500 mt-0.5">
                                        <span x-text="item.quantity + ' ' + (item.unit || 'Unit')"></span> &bull; 
                                        <span x-text="item.duration + ' Bulan'"></span>
                                    </p>
                                </div>
                                <div class="text-right">
                                    <span class="font-bold text-slate-900" x-text="'Rp ' + Number(item.line_total).toLocaleString('id-ID')"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Financial Total Box -->
                <div class="bg-sky-50/80 p-4 rounded-[22px] border border-sky-200 space-y-1.5">
                    <div class="flex justify-between text-slate-600">
                        <span>Subtotal:</span>
                        <span class="font-bold text-slate-900" x-text="selectedOrder ? 'Rp ' + Number(selectedOrder.subtotal).toLocaleString('id-ID') : ''"></span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>PPN (11%):</span>
                        <span class="font-bold text-slate-900" x-text="selectedOrder ? 'Rp ' + Number(selectedOrder.tax_amount).toLocaleString('id-ID') : ''"></span>
                    </div>
                    <div class="pt-2 border-t border-sky-200 flex justify-between text-sm font-extrabold text-slate-900">
                        <span>Total Nilai:</span>
                        <span class="text-sky-700" x-text="selectedOrder ? 'Rp ' + Number(selectedOrder.total_amount).toLocaleString('id-ID') : ''"></span>
                    </div>
                </div>
            </div>

            <!-- Drawer Bottom Actions: Direct PDF Print & Convert -->
            <div class="p-5 border-t border-slate-150 bg-slate-50 flex items-center space-x-2.5" x-show="selectedOrder">
                <!-- Convert to Rental Order Button (if Quotation) -->
                <template x-if="selectedOrder && selectedOrder.order_type === 'quotation'">
                    <button @click="convertCurrentOrder('rental_order')"
                            :disabled="isConverting"
                            class="flex-1 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold py-3.5 px-3 rounded-full text-xs flex items-center justify-center space-x-1 shadow-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span x-text="isConverting ? 'Memproses...' : 'Setujui & Jadikan Sewa (RO)'"></span>
                    </button>
                </template>

                <!-- PDF Print Button (Full Kop Keylock) -->
                <a :href="'/erp/quotations/' + (selectedOrder ? selectedOrder.id : 1) + '/pdf'"
                   target="_blank"
                   class="px-5 py-3.5 bg-sky-500 hover:bg-sky-400 text-white font-extrabold rounded-full shadow-sm flex items-center justify-center space-x-1.5 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    <span>Cetak PDF</span>
                </a>
            </div>
        </div>
    </div>

</div>

<!-- Alpine.js Controller for Covera Mobile UI -->
<script>
    function coveraMobileErp() {
        return {
            lang: localStorage.getItem('keylock_lang') || 'id',
            activeTab: 'semua',
            searchQuery: '',
            showFilterMenu: false,
            drawerOpen: false,
            modalQuotationOpen: false,
            selectedOrder: null,
            isConverting: false,
            quickItemId: '{{ $catalogItems->first()->id ?? 1 }}',
            quickQty: 1,
            quickPrice: {{ $catalogItems->first()->rental_rate_month ?? 16740000 }},
            mobileQuotationType: 'rental',
            quickDuration: 1,
            quickDurationUnit: 'Bulan',
            formatNumberWithDots: window.formatNumberWithDots,
            parseNumberFromDots: window.parseNumberFromDots,
            maskRupiah: window.maskRupiah,

            setMobileQuotationType(type) {
                this.mobileQuotationType = type;
                this.onItemChange();
            },

            setLang(l) {
                this.lang = l;
                localStorage.setItem('keylock_lang', l);
            },

            matchesTab(type) {
                if (this.activeTab === 'semua') return true;
                return this.activeTab === type;
            },

            matchesSearch(text) {
                if (!this.searchQuery) return true;
                return text.includes(this.searchQuery.toLowerCase());
            },

            filteredOrders() {
                const q = this.searchQuery.toLowerCase();
                const tab = this.activeTab;
                // Client-side quick count helper
                return [1]; // non-empty placeholder
            },

            onItemChange() {
                const select = document.querySelector('select[name="items[0][item_id]"]');
                if (select && select.selectedOptions.length) {
                    const price = select.selectedOptions[0].getAttribute('data-price');
                    if (price) {
                        this.quickPrice = parseFloat(price);
                    }
                }
            },

            calculateTotal() {
                const dur = (this.mobileQuotationType === 'rental') ? (this.quickDuration || 1) : 1;
                const sub = (this.quickQty * this.quickPrice * dur);
                return Math.round(sub * 1.11);
            },

            openOrderDrawer(orderId) {
                fetch('/erp/orders/' + orderId + '/drawer', {
                    headers: { 'Accept': 'application/json' }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        this.selectedOrder = data.order;
                        this.drawerOpen = true;
                    }
                })
                .catch(err => console.error('Gagal mengambil data drawer:', err));
            },

            convertCurrentOrder(targetType) {
                if (!this.selectedOrder) return;
                this.isConverting = true;

                fetch('/erp/orders/' + this.selectedOrder.id + '/convert', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ target_type: targetType })
                })
                .then(r => r.json())
                .then(data => {
                    this.isConverting = false;
                    if (data.success) {
                        alert(data.message);
                        window.location.reload();
                    } else {
                        alert(data.message || 'Gagal konversi dokumen');
                    }
                })
                .catch(err => {
                    this.isConverting = false;
                    alert('Terjadi kesalahan jaringan.');
                });
            }
        };
    }
</script>
@endsection
