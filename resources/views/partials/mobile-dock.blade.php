@php
    $user = auth()->user();
    $role = strtolower($user->role ?? '');
    $isAdmin = $user ? $user->isAdmin() : false;
    $isSales = $user ? $user->isSales() : false;
    $isFinance = $user ? $user->isFinance() : false;
    $isOps = $user ? $user->isOperational() : false;

    // Active Route Checkers
    $isHomeActive = request()->is('erp') && !request()->is('erp/*');
    $isCrmActive = request()->is('erp/crm*');
    $isOrdersActive = request()->is('erp/orders*');
    $isFleetActive = request()->is('erp/fleet*');
    $isFinanceActive = request()->is('erp/finance*');
    $isProcurementActive = request()->is('erp/procurement*');
@endphp

<!-- ========================================================================= -->
<!-- MOBILE BOTTOM NAVIGATION DOCK (VISIBLE ONLY ON SCREENS BELOW MD)           -->
<!-- ========================================================================= -->
<nav class="fixed bottom-0 inset-x-0 z-40 block md:hidden bg-slate-900/95 backdrop-blur-md border-t border-slate-800 text-white shadow-[0_-8px_30px_rgba(0,0,0,0.3)]"
     style="padding-bottom: max(env(safe-area-inset-bottom), 0.75rem); padding-top: 0.5rem;"
     role="navigation"
     aria-label="Navigasi Bawah Mobile">

    <div class="max-w-md mx-auto px-3 flex items-center justify-around">

        <!-- 1. BERANDA (ALL ROLES) -->
        <a href="{{ route('erp.index') }}" 
           class="flex flex-col items-center justify-center py-1 px-2 min-w-[56px] min-h-[48px] rounded-xl transition {{ $isHomeActive ? 'text-amber-400 font-extrabold' : 'text-slate-400 hover:text-white font-medium' }}"
           aria-current="{{ $isHomeActive ? 'page' : 'false' }}">
            <svg class="w-5 h-5 {{ $isHomeActive ? 'text-amber-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $isHomeActive ? '2.5' : '2' }}" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span class="text-[10px] mt-1 leading-none" x-text="lang === 'zh' ? '首页' : 'Beranda'">Beranda</span>
        </a>

        <!-- 2. ROLE PRIMARY MODULE -->
        @if($isSales && !$isAdmin)
            <!-- Sales: CRM -->
            <a href="{{ route('erp.crm.index') }}" 
               class="flex flex-col items-center justify-center py-1 px-2 min-w-[56px] min-h-[48px] rounded-xl transition {{ $isCrmActive ? 'text-amber-400 font-extrabold' : 'text-slate-400 hover:text-white font-medium' }}"
               aria-current="{{ $isCrmActive ? 'page' : 'false' }}">
                <svg class="w-5 h-5 {{ $isCrmActive ? 'text-amber-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $isCrmActive ? '2.5' : '2' }}" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <span class="text-[10px] mt-1 leading-none" x-text="lang === 'zh' ? 'CRM' : 'CRM'">CRM</span>
            </a>
        @elseif($isOps && !$isAdmin)
            <!-- Operasional: Armada -->
            <a href="{{ route('erp.fleet.index') }}" 
               class="flex flex-col items-center justify-center py-1 px-2 min-w-[56px] min-h-[48px] rounded-xl transition {{ $isFleetActive ? 'text-amber-400 font-extrabold' : 'text-slate-400 hover:text-white font-medium' }}"
               aria-current="{{ $isFleetActive ? 'page' : 'false' }}">
                <svg class="w-5 h-5 {{ $isFleetActive ? 'text-amber-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $isFleetActive ? '2.5' : '2' }}" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0zM13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1"></path>
                </svg>
                <span class="text-[10px] mt-1 leading-none" x-text="lang === 'zh' ? '车队' : 'Armada'">Armada</span>
            </a>
        @elseif($isFinance && !$isAdmin)
            <!-- Finance: Invoices -->
            <a href="{{ route('erp.finance.index') }}" 
               class="flex flex-col items-center justify-center py-1 px-2 min-w-[56px] min-h-[48px] rounded-xl transition {{ $isFinanceActive ? 'text-amber-400 font-extrabold' : 'text-slate-400 hover:text-white font-medium' }}"
               aria-current="{{ $isFinanceActive ? 'page' : 'false' }}">
                <svg class="w-5 h-5 {{ $isFinanceActive ? 'text-amber-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $isFinanceActive ? '2.5' : '2' }}" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"></path>
                </svg>
                <span class="text-[10px] mt-1 leading-none" x-text="lang === 'zh' ? '发票' : 'Invoice'">Invoice</span>
            </a>
        @else
            <!-- Admin / Owner: Transaksi Hub -->
            <a href="{{ route('erp.orders.index') }}" 
               class="flex flex-col items-center justify-center py-1 px-2 min-w-[56px] min-h-[48px] rounded-xl transition {{ $isOrdersActive ? 'text-amber-400 font-extrabold' : 'text-slate-400 hover:text-white font-medium' }}"
               aria-current="{{ $isOrdersActive ? 'page' : 'false' }}">
                <svg class="w-5 h-5 {{ $isOrdersActive ? 'text-amber-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $isOrdersActive ? '2.5' : '2' }}" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span class="text-[10px] mt-1 leading-none" x-text="lang === 'zh' ? '交易' : 'Transaksi'">Transaksi</span>
            </a>
        @endif

        <!-- 3. CENTER QUICK ACTION BUTTON (+) -->
        <div class="relative flex items-center justify-center -mt-6">
            <button type="button" 
                    @click="toggleMobileQuickActions()"
                    class="w-12 h-12 rounded-full bg-gradient-to-tr from-blue-600 via-blue-500 to-indigo-600 text-white font-black flex items-center justify-center shadow-lg shadow-blue-500/30 border-4 border-slate-900 active:scale-95 transition-transform"
                    aria-label="Aksi Cepat Transaksi"
                    :aria-expanded="mobileQuickActionsOpen">
                <svg class="w-6 h-6 transition-transform duration-200" 
                     :class="mobileQuickActionsOpen ? 'rotate-45' : ''" 
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path>
                </svg>
            </button>
        </div>

        <!-- 4. ROLE SECONDARY CORE MODULE -->
        @if($isSales && !$isAdmin)
            <!-- Sales: Penawaran & Order -->
            <a href="{{ route('erp.orders.index') }}" 
               class="flex flex-col items-center justify-center py-1 px-2 min-w-[56px] min-h-[48px] rounded-xl transition {{ $isOrdersActive ? 'text-amber-400 font-extrabold' : 'text-slate-400 hover:text-white font-medium' }}"
               aria-current="{{ $isOrdersActive ? 'page' : 'false' }}">
                <svg class="w-5 h-5 {{ $isOrdersActive ? 'text-amber-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $isOrdersActive ? '2.5' : '2' }}" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span class="text-[10px] mt-1 leading-none" x-text="lang === 'zh' ? '报价' : 'Penawaran'">Penawaran</span>
            </a>
        @elseif($isOps && !$isAdmin)
            <!-- Operasional: Orders & Pengiriman DO -->
            <a href="{{ route('erp.orders.index') }}" 
               class="flex flex-col items-center justify-center py-1 px-2 min-w-[56px] min-h-[48px] rounded-xl transition {{ $isOrdersActive ? 'text-amber-400 font-extrabold' : 'text-slate-400 hover:text-white font-medium' }}"
               aria-current="{{ $isOrdersActive ? 'page' : 'false' }}">
                <svg class="w-5 h-5 {{ $isOrdersActive ? 'text-amber-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $isOrdersActive ? '2.5' : '2' }}" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <span class="text-[10px] mt-1 leading-none" x-text="lang === 'zh' ? '订单' : 'Order'">Order</span>
            </a>
        @elseif($isFinance && !$isAdmin)
            <!-- Finance: Orders / Transaksi Induk -->
            <a href="{{ route('erp.orders.index') }}" 
               class="flex flex-col items-center justify-center py-1 px-2 min-w-[56px] min-h-[48px] rounded-xl transition {{ $isOrdersActive ? 'text-amber-400 font-extrabold' : 'text-slate-400 hover:text-white font-medium' }}"
               aria-current="{{ $isOrdersActive ? 'page' : 'false' }}">
                <svg class="w-5 h-5 {{ $isOrdersActive ? 'text-amber-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $isOrdersActive ? '2.5' : '2' }}" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-[10px] mt-1 leading-none" x-text="lang === 'zh' ? '交易' : 'Transaksi'">Transaksi</span>
            </a>
        @else
            <!-- Admin / Owner: Armada & Unit -->
            <a href="{{ route('erp.fleet.index') }}" 
               class="flex flex-col items-center justify-center py-1 px-2 min-w-[56px] min-h-[48px] rounded-xl transition {{ $isFleetActive ? 'text-amber-400 font-extrabold' : 'text-slate-400 hover:text-white font-medium' }}"
               aria-current="{{ $isFleetActive ? 'page' : 'false' }}">
                <svg class="w-5 h-5 {{ $isFleetActive ? 'text-amber-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $isFleetActive ? '2.5' : '2' }}" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0zM13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1"></path>
                </svg>
                <span class="text-[10px] mt-1 leading-none" x-text="lang === 'zh' ? '车队' : 'Armada'">Armada</span>
            </a>
        @endif

        <!-- 5. MENU LAINNYA (...) -->
        <button type="button" 
                @click="toggleMobileMoreMenu()"
                class="flex flex-col items-center justify-center py-1 px-2 min-w-[56px] min-h-[48px] rounded-xl transition text-slate-400 hover:text-white font-medium"
                :class="mobileMoreMenuOpen ? 'text-amber-400' : ''"
                aria-label="Menu Lainnya"
                :aria-expanded="mobileMoreMenuOpen">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"></path>
            </svg>
            <span class="text-[10px] mt-1 leading-none" x-text="lang === 'zh' ? '更多' : 'Lainnya'">Lainnya</span>
        </button>

    </div>
</nav>

<!-- ========================================================================= -->
<!-- BOTTOM SHEET 1: QUICK ACTIONS MODAL (+)                                    -->
<!-- ========================================================================= -->
<div x-show="mobileQuickActionsOpen" 
     x-cloak
     style="display: none;"
     class="fixed inset-0 z-50 overflow-hidden" 
     role="dialog" 
     aria-modal="true" 
     aria-labelledby="quick-actions-title">
    
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs transition-opacity"
         @click="mobileQuickActionsOpen = false"
         x-transition:enter="ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"></div>

    <!-- Sheet Panel -->
    <div class="fixed inset-x-0 bottom-0 max-w-md mx-auto bg-white rounded-t-[28px] shadow-2xl overflow-hidden flex flex-col z-10"
         x-show="mobileQuickActionsOpen"
         x-transition:enter="transform transition ease-out duration-200"
         x-transition:enter-start="translate-y-full"
         x-transition:enter-end="translate-y-0"
         x-transition:leave="transform transition ease-in duration-150"
         x-transition:leave-start="translate-y-0"
         x-transition:leave-end="translate-y-full"
         style="padding-bottom: max(env(safe-area-inset-bottom), 1.25rem);">
        
        <!-- Drag Handle -->
        <div class="pt-3 pb-1 flex justify-center">
            <div class="w-10 h-1.5 rounded-full bg-slate-300"></div>
        </div>

        <!-- Header -->
        <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 id="quick-actions-title" class="text-sm font-extrabold text-slate-900" x-text="lang === 'zh' ? '快速业务通道' : 'Aksi Cepat Operasional'">Aksi Cepat Operasional</h3>
                <p class="text-[11px] text-slate-400 mt-0.5" x-text="lang === 'zh' ? '选择要执行的操作' : 'Pilih tindakan untuk memulai proses'">Pilih tindakan untuk memulai proses</p>
            </div>
            <button type="button" @click="mobileQuickActionsOpen = false" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-full hover:bg-slate-100">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Actions List -->
        <div class="p-4 space-y-2.5 max-h-[70vh] overflow-y-auto">
            
            <!-- Action 1: Buat Quotation (Sales, Ops, Admin) -->
            @if($isAdmin || $isSales || $isOps)
                <button type="button" 
                        @click="mobileQuickActionsOpen = false; modalQuotationOpen = true; quotationMode = 'create'"
                        class="w-full bg-slate-50 hover:bg-amber-50/60 p-3.5 rounded-2xl border border-slate-200/80 hover:border-amber-300 flex items-center space-x-3.5 transition group text-left">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 group-hover:scale-105 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-xs font-bold text-slate-900 leading-tight" x-text="lang === 'zh' ? '创建价格报价单 (Quotation)' : 'Buat Penawaran Harga (Quotation)'">Buat Penawaran Harga (Quotation)</h4>
                        <p class="text-[11px] text-slate-500 mt-0.5" x-text="lang === 'zh' ? '选择客户与设备，自动计算税费并生成 PDF' : 'Pilih unit sewa/jual & hitung PPN otomatis'">Pilih unit sewa/jual & hitung PPN otomatis</p>
                    </div>
                    <span class="text-xs text-slate-400 group-hover:text-amber-600 transition">›</span>
                </button>
            @endif

            <!-- Action 2: Tambah Lead CRM (Sales, Admin) -->
            @if($isAdmin || $isSales)
                <a href="{{ route('erp.crm.index') }}#tambah-lead"
                   @click="mobileQuickActionsOpen = false"
                   class="w-full bg-slate-50 hover:bg-sky-50/60 p-3.5 rounded-2xl border border-slate-200/80 hover:border-sky-300 flex items-center space-x-3.5 transition group text-left">
                    <div class="w-10 h-10 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center shrink-0 group-hover:scale-105 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-xs font-bold text-slate-900 leading-tight" x-text="lang === 'zh' ? '录入 CRM 新商机 (Lead)' : 'Input Lead Prospek Baru (CRM)'">Input Lead Prospek Baru (CRM)</h4>
                        <p class="text-[11px] text-slate-500 mt-0.5" x-text="lang === 'zh' ? '登记意向客户与跟进阶段' : 'Catat kebutuhan sewa calon pelanggan'">Catat kebutuhan sewa calon pelanggan</p>
                    </div>
                    <span class="text-xs text-slate-400 group-hover:text-sky-600 transition">›</span>
                </a>
            @endif

            <!-- Action 3: Servis Armada & Hour Meter (Ops, Admin) -->
            @if($isAdmin || $isOps)
                <a href="{{ route('erp.fleet.index') }}"
                   @click="mobileQuickActionsOpen = false"
                   class="w-full bg-slate-50 hover:bg-emerald-50/60 p-3.5 rounded-2xl border border-slate-200/80 hover:border-emerald-300 flex items-center space-x-3.5 transition group text-left">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 group-hover:scale-105 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-xs font-bold text-slate-900 leading-tight" x-text="lang === 'zh' ? '记录设备保养与工时 (Hour Meter)' : 'Catat Servis Armada & Hour Meter'">Catat Servis Armada & Hour Meter</h4>
                        <p class="text-[11px] text-slate-500 mt-0.5" x-text="lang === 'zh' ? '更新叉车工时表与定期保养记录' : 'Pembaruan jam operasi & pemeliharaan'">Pembaruan jam operasi & pemeliharaan</p>
                    </div>
                    <span class="text-xs text-slate-400 group-hover:text-emerald-600 transition">›</span>
                </a>
            @endif

            <!-- Action 4: Buat PO Pengadaan (Ops, Procurement, Admin) -->
            @if($isAdmin || $isOps)
                <a href="{{ route('erp.procurement.index') }}"
                   @click="mobileQuickActionsOpen = false"
                   class="w-full bg-slate-50 hover:bg-purple-50/60 p-3.5 rounded-2xl border border-slate-200/80 hover:border-purple-300 flex items-center space-x-3.5 transition group text-left">
                    <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center shrink-0 group-hover:scale-105 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-xs font-bold text-slate-900 leading-tight" x-text="lang === 'zh' ? '创建采购单 (PO Vendor)' : 'Buat PO Pengadaan / Vendor'">Buat PO Pengadaan / Vendor</h4>
                        <p class="text-[11px] text-slate-500 mt-0.5" x-text="lang === 'zh' ? '采购配件、外租重机或安排拖车物流' : 'Pengadaan unit, sparepart, atau towing'">Pengadaan unit, sparepart, atau towing</p>
                    </div>
                    <span class="text-xs text-slate-400 group-hover:text-purple-600 transition">›</span>
                </a>
            @endif

            <!-- Action 5: Catat Pembayaran Finance (Finance, Admin) -->
            @if($isAdmin || $isFinance)
                <a href="{{ route('erp.finance.index') }}"
                   @click="mobileQuickActionsOpen = false"
                   class="w-full bg-slate-50 hover:bg-rose-50/60 p-3.5 rounded-2xl border border-slate-200/80 hover:border-rose-300 flex items-center space-x-3.5 transition group text-left">
                    <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center shrink-0 group-hover:scale-105 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-xs font-bold text-slate-900 leading-tight" x-text="lang === 'zh' ? '记录客户付款 (Payment)' : 'Catat Pembayaran Masuk (Payment)'">Catat Pembayaran Masuk (Payment)</h4>
                        <p class="text-[11px] text-slate-500 mt-0.5" x-text="lang === 'zh' ? '确认银行转账并核销发票余额' : 'Konfirmasi pelunasan transfer bank'">Konfirmasi pelunasan transfer bank</p>
                    </div>
                    <span class="text-xs text-slate-400 group-hover:text-rose-600 transition">›</span>
                </a>
            @endif

        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- BOTTOM SHEET 2: MENU LAINNYA (...)                                        -->
<!-- ========================================================================= -->
<div x-show="mobileMoreMenuOpen" 
     x-cloak
     style="display: none;"
     class="fixed inset-0 z-50 overflow-hidden" 
     role="dialog" 
     aria-modal="true" 
     aria-labelledby="more-menu-title">
    
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs transition-opacity"
         @click="mobileMoreMenuOpen = false"
         x-transition:enter="ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"></div>

    <!-- Sheet Panel -->
    <div class="fixed inset-x-0 bottom-0 max-w-md mx-auto bg-white rounded-t-[28px] shadow-2xl overflow-hidden flex flex-col z-10"
         x-show="mobileMoreMenuOpen"
         x-transition:enter="transform transition ease-out duration-200"
         x-transition:enter-start="translate-y-full"
         x-transition:enter-end="translate-y-0"
         x-transition:leave="transform transition ease-in duration-150"
         x-transition:leave-start="translate-y-0"
         x-transition:leave-end="translate-y-full"
         style="padding-bottom: max(env(safe-area-inset-bottom), 1.25rem);">
        
        <!-- Drag Handle -->
        <div class="pt-3 pb-1 flex justify-center">
            <div class="w-10 h-1.5 rounded-full bg-slate-300"></div>
        </div>

        <!-- User Profile Card -->
        <div class="p-4 mx-4 mt-2 bg-slate-50 rounded-2xl border border-slate-200/80 flex items-center justify-between">
            <div class="flex items-center space-x-3 min-w-0">
                <div class="w-10 h-10 rounded-full bg-slate-900 text-amber-400 font-extrabold text-sm flex items-center justify-center shrink-0">
                    {{ strtoupper(substr($user->name ?? 'AD', 0, 2)) }}
                </div>
                <div class="min-w-0">
                    <h4 id="more-menu-title" class="text-xs font-extrabold text-slate-900 truncate">{{ $user->name ?? 'Pengguna' }}</h4>
                    <p class="text-[10px] text-slate-500 font-semibold uppercase tracking-wider">{{ $user->role ?? 'User' }} &bull; {{ $user->email ?? '' }}</p>
                </div>
            </div>

            <!-- Language Switcher in Sheet -->
            <div class="flex items-center bg-white p-0.5 rounded-xl border border-slate-200 shrink-0">
                <button type="button" @click="setLang('id')"
                        :class="lang === 'id' ? 'bg-slate-900 text-white font-bold' : 'text-slate-500'"
                        class="px-2.5 py-1 rounded-lg text-[11px] transition">
                    ID
                </button>
                <button type="button" @click="setLang('zh')"
                        :class="lang === 'zh' ? 'bg-slate-900 text-white font-bold' : 'text-slate-500'"
                        class="px-2.5 py-1 rounded-lg text-[11px] transition">
                    中文
                </button>
            </div>
        </div>

        <!-- Modules Grid List -->
        <div class="p-4 space-y-1.5 max-h-[60vh] overflow-y-auto">
            <p class="px-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1" x-text="lang === 'zh' ? '模块导航' : 'Modul Sistem Terkait'">Modul Sistem Terkait</p>

            @if($isAdmin || $isSales)
                <a href="{{ route('erp.crm.index') }}" 
                   @click="mobileMoreMenuOpen = false"
                   class="flex items-center justify-between p-3 rounded-xl hover:bg-slate-50 text-xs font-semibold text-slate-700 transition">
                    <div class="flex items-center space-x-3">
                        <span class="w-7 h-7 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </span>
                        <span x-text="lang === 'zh' ? '客户关系管理 (CRM)' : 'Pelanggan & CRM'">Pelanggan & CRM</span>
                    </div>
                    <span class="text-slate-300">›</span>
                </a>
            @endif

            @if($isAdmin || $isOps || $isSales)
                <a href="{{ route('erp.fleet.index') }}" 
                   @click="mobileMoreMenuOpen = false"
                   class="flex items-center justify-between p-3 rounded-xl hover:bg-slate-50 text-xs font-semibold text-slate-700 transition">
                    <div class="flex items-center space-x-3">
                        <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0zM13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1"></path></svg>
                        </span>
                        <span x-text="lang === 'zh' ? '车队与设备管理' : 'Katalog Armada & Unit'">Katalog Armada & Unit</span>
                    </div>
                    <span class="text-slate-300">›</span>
                </a>
            @endif

            @if($isAdmin || $isOps)
                <a href="{{ route('erp.procurement.index') }}" 
                   @click="mobileMoreMenuOpen = false"
                   class="flex items-center justify-between p-3 rounded-xl hover:bg-slate-50 text-xs font-semibold text-slate-700 transition">
                    <div class="flex items-center space-x-3">
                        <span class="w-7 h-7 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        </span>
                        <span x-text="lang === 'zh' ? '采购与供应商 (PO)' : 'Pengadaan & Vendor PO'">Pengadaan & Vendor PO</span>
                    </div>
                    <span class="text-slate-300">›</span>
                </a>
            @endif

            @if($isAdmin || $isFinance)
                <a href="{{ route('erp.finance.index') }}" 
                   @click="mobileMoreMenuOpen = false"
                   class="flex items-center justify-between p-3 rounded-xl hover:bg-slate-50 text-xs font-semibold text-slate-700 transition">
                    <div class="flex items-center space-x-3">
                        <span class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </span>
                        <span x-text="lang === 'zh' ? '财务与发票管理' : 'Keuangan & Invoicing'">Keuangan & Invoicing</span>
                    </div>
                    <span class="text-slate-300">›</span>
                </a>
            @endif

            <a href="{{ route('erp.master_data.index') }}" 
               @click="mobileMoreMenuOpen = false"
               class="flex items-center justify-between p-3 rounded-xl hover:bg-slate-50 text-xs font-semibold text-slate-700 transition">
                <div class="flex items-center space-x-3">
                    <span class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path></svg>
                    </span>
                    <span x-text="lang === 'zh' ? '主数据管理' : 'Master Data'">Master Data</span>
                </div>
                <span class="text-slate-300">›</span>
            </a>

            <a href="{{ route('erp.reports.index') }}" 
               @click="mobileMoreMenuOpen = false"
               class="flex items-center justify-between p-3 rounded-xl hover:bg-slate-50 text-xs font-semibold text-slate-700 transition">
                <div class="flex items-center space-x-3">
                    <span class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </span>
                    <span x-text="lang === 'zh' ? '业务报表与下载' : 'Laporan & Rekapitulasi'">Laporan & Rekapitulasi</span>
                </div>
                <span class="text-slate-300">›</span>
            </a>

            @if($isAdmin)
                <div class="pt-2 border-t border-slate-100">
                    <p class="px-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1" x-text="lang === 'zh' ? '管理员系统设置' : 'Administrasi Sistem'">Administrasi Sistem</p>
                    
                    <a href="{{ route('erp.users.index') }}" 
                       @click="mobileMoreMenuOpen = false"
                       class="flex items-center justify-between p-3 rounded-xl hover:bg-slate-50 text-xs font-semibold text-slate-700 transition">
                        <div class="flex items-center space-x-3">
                            <span class="w-7 h-7 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            </span>
                            <span x-text="lang === 'zh' ? '用户与权限' : 'User & Role Management'">User & Role Management</span>
                        </div>
                        <span class="text-slate-300">›</span>
                    </a>

                    <a href="{{ route('erp.menus.index') }}" 
                       @click="mobileMoreMenuOpen = false"
                       class="flex items-center justify-between p-3 rounded-xl hover:bg-slate-50 text-xs font-semibold text-slate-700 transition">
                        <div class="flex items-center space-x-3">
                            <span class="w-7 h-7 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
                            </span>
                            <span x-text="lang === 'zh' ? '菜单管理' : 'Menu Management'">Menu Management</span>
                        </div>
                        <span class="text-slate-300">›</span>
                    </a>

                    <a href="{{ route('erp.configs.index') }}" 
                       @click="mobileMoreMenuOpen = false"
                       class="flex items-center justify-between p-3 rounded-xl hover:bg-slate-50 text-xs font-semibold text-slate-700 transition">
                        <div class="flex items-center space-x-3">
                            <span class="w-7 h-7 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg>
                            </span>
                            <span x-text="lang === 'zh' ? '系统配置' : 'System Configs'">System Configs</span>
                        </div>
                        <span class="text-slate-300">›</span>
                    </a>

                    <a href="{{ route('erp.cms.index') }}" 
                       @click="mobileMoreMenuOpen = false"
                       class="flex items-center justify-between p-3 rounded-xl hover:bg-slate-50 text-xs font-semibold text-slate-700 transition">
                        <div class="flex items-center space-x-3">
                            <span class="w-7 h-7 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </span>
                            <span x-text="lang === 'zh' ? 'CMS 网站内容' : 'CMS Website'">CMS Website</span>
                        </div>
                        <span class="text-slate-300">›</span>
                    </a>
                </div>
            @endif

            <!-- Portal Mobile Link (Preserved during transition) -->
            <a href="{{ route('mobile') }}" 
               @click="mobileMoreMenuOpen = false"
               class="flex items-center justify-between p-3 rounded-xl hover:bg-slate-50 text-xs font-semibold text-slate-500 transition">
                <div class="flex items-center space-x-3">
                    <span class="w-7 h-7 rounded-lg bg-slate-100 text-slate-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    </span>
                    <span x-text="lang === 'zh' ? '独立移动门户 (/mobile)' : 'Portal Standalone (/mobile)'">Portal Standalone (/mobile)</span>
                </div>
                <span class="text-slate-300">↗</span>
            </a>

            <!-- SECURE LOGOUT (STRICT POST + CSRF) -->
            <div class="pt-3 border-t border-slate-100">
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button type="submit" 
                            class="w-full flex items-center justify-center space-x-2 py-3 px-4 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs transition border border-rose-200/60 active:scale-[0.99]">
                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        <span x-text="lang === 'zh' ? '退出登录' : 'Keluar / Logout'">Keluar / Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
