{{-- resources/views/erp/partials/dashboard-mobile.blade.php --}}
{{-- Rendered exclusively on screens below the md breakpoint (block md:hidden) --}}
@php
    $user = auth()->user();
    $userName = $user->name ?? 'Admin Keylock';
    $userRole = strtoupper($user->role ?? 'Admin');
@endphp

<div class="space-y-4">

    <!-- ========================================================================= -->
    <!-- 1. SEARCH BAR (RINGKAS DENGAN IKON PENCARIAN & AKSI NAVIGASI KE ORDERS)   -->
    <!-- ========================================================================= -->
    <div class="bg-white rounded-2xl px-3.5 py-2.5 shadow-[0_2px_8px_rgba(0,0,0,0.03)] border border-[#E3EAF3] flex items-center space-x-2.5 transition focus-within:border-blue-500 focus-within:ring-2 focus-within:ring-blue-100">
        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
        <input type="text" 
               @keydown.enter="if ($event.target.value.trim()) { window.location.href = '{{ route('erp.orders.index') }}?per_page=10&search=' + encodeURIComponent($event.target.value.trim()); }"
               :placeholder="lang === 'zh' ? '搜索设备、报价、订单、客户...' : 'Cari armada, penawaran, order, pelanggan...'"
               placeholder="Cari armada, penawaran, order, pelanggan..." 
               class="w-full bg-transparent text-xs text-[#17243B] placeholder-slate-400 focus:outline-none"
               aria-label="Pencarian Cepat Mobile">
    </div>

    <!-- ========================================================================= -->
    <!-- 2. BANNER UTAMA (BRANDING KEYLOCK: NAVY-TO-BLUE, TEKS SAPAAN & KONTEKS)   -->
    <!-- ========================================================================= -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-[#142A57] via-[#1A3A75] to-[#2458E8] p-5 text-white shadow-md border border-blue-900/30">
        <!-- Subtle background glow -->
        <div class="absolute -right-8 -top-8 w-36 h-36 bg-blue-400/20 rounded-full blur-2xl pointer-events-none" aria-hidden="true"></div>
        <div class="absolute -left-8 -bottom-8 w-32 h-32 bg-indigo-500/15 rounded-full blur-xl pointer-events-none" aria-hidden="true"></div>

        <div class="relative z-10 flex flex-col justify-between min-h-[140px]">
            <div>
                <!-- Brand Pill -->
                <div class="inline-flex items-center space-x-1.5 px-2.5 py-0.5 rounded-full bg-white/15 backdrop-blur-md border border-white/20 text-[10px] font-bold tracking-wider uppercase text-white">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-300 animate-pulse"></span>
                    <span>KEYLOCK INDONESIA</span>
                </div>

                <!-- Headline / Sapaan -->
                <h2 class="text-lg font-black tracking-tight text-white mt-2 leading-snug">
                    Accurate, Efficient, and Innovative.
                </h2>

                <!-- Subtitle Konteks Bisnis -->
                <p class="text-[11px] text-blue-100/90 mt-1 leading-relaxed max-w-[280px]">
                    <span x-text="lang === 'zh' ? '高效管理叉车与重机租赁、跟踪交付与开票结算。' : 'Kelola operasional rental armada, lacak pengiriman, dan kendalikan invoicing secara efisien.'">
                        Kelola operasional rental armada, lacak pengiriman, dan kendalikan invoicing secara efisien.
                    </span>
                </p>
            </div>

            <!-- Contextual Quick Metric Strip -->
            <div class="mt-3 pt-2.5 border-t border-white/15 flex items-center justify-between text-[11px]">
                <div class="flex items-center space-x-1.5 text-blue-100">
                    <span class="font-bold text-white">{{ $userName }}</span>
                    <span class="text-blue-300">|</span>
                    <span class="text-[10px] font-semibold text-blue-200 uppercase tracking-wider">{{ $userRole }}</span>
                </div>
                <a href="{{ route('erp.orders.index', ['per_page' => 10]) }}" class="inline-flex items-center space-x-1 text-white font-bold hover:underline text-[11px]">
                    <span x-text="lang === 'zh' ? '进入订单' : 'Buka Order'">Buka Order</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 3. STATUS OPERASIONAL & TAGIHAN (GRID 2 KOLOM TANPA EMOJI)               -->
    <!-- ========================================================================= -->
    <div class="space-y-2.5">
        <div class="flex items-center justify-between px-1">
            <h3 class="text-[11px] font-black uppercase tracking-wider text-slate-500" x-text="lang === 'zh' ? '业务运营与账单状态' : 'STATUS OPERASIONAL & TAGIHAN'">
                STATUS OPERASIONAL & TAGIHAN
            </h3>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <!-- Card 1: Penawaran Aktif (Pending Follow-up) -->
            <a href="{{ route('erp.orders.index', ['type' => 'quotation', 'per_page' => 10]) }}" 
               class="bg-white rounded-2xl border border-[#E3EAF3] p-3.5 shadow-[0_2px_8px_rgba(0,0,0,0.03)] hover:border-slate-300 transition flex flex-col justify-between space-y-2">
                <div class="flex items-center justify-between">
                    <span class="px-2 py-0.5 rounded-lg text-[10px] font-extrabold bg-amber-50 text-amber-700 border border-amber-200/60" x-text="lang === 'zh' ? '待跟进报价' : 'Penawaran Aktif'">
                        Penawaran Aktif
                    </span>
                    <div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
                <div>
                    <span class="text-2xl font-black text-[#17243B] tracking-tight block">
                        {{ $activeQuotationsCount ?? 0 }}
                    </span>
                    <span class="text-[10px] text-slate-500 font-medium leading-tight block mt-0.5" x-text="lang === 'zh' ? '需跟进报价单' : 'Quotation butuh tindak lanjut'">
                        Quotation butuh tindak lanjut
                    </span>
                </div>
            </a>

            <!-- Card 2: Rental Berjalan (Active Rental Contracts) -->
            <a href="{{ route('erp.orders.index', ['type' => 'transaksi', 'per_page' => 10]) }}" 
               class="bg-white rounded-2xl border border-[#E3EAF3] p-3.5 shadow-[0_2px_8px_rgba(0,0,0,0.03)] hover:border-slate-300 transition flex flex-col justify-between space-y-2">
                <div class="flex items-center justify-between">
                    <span class="px-2 py-0.5 rounded-lg text-[10px] font-extrabold bg-blue-50 text-blue-700 border border-blue-200/60" x-text="lang === 'zh' ? '在租运行' : 'Rental Berjalan'">
                        Rental Berjalan
                    </span>
                    <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                </div>
                <div>
                    <span class="text-2xl font-black text-[#17243B] tracking-tight block">
                        {{ $transactionStatuses['rental_contract'] ?? ($displayRented ?? 0) }}
                    </span>
                    <span class="text-[10px] text-slate-500 font-medium leading-tight block mt-0.5" x-text="lang === 'zh' ? '设备在租作业中' : 'Unit sewa aktif beroperasi'">
                        Unit sewa aktif beroperasi
                    </span>
                </div>
            </a>

            <!-- Card 3: Pengiriman DO (In Delivery / Scheduled) -->
            <a href="{{ route('erp.orders.index', ['type' => 'transaksi', 'per_page' => 10]) }}" 
               class="bg-white rounded-2xl border border-[#E3EAF3] p-3.5 shadow-[0_2px_8px_rgba(0,0,0,0.03)] hover:border-slate-300 transition flex flex-col justify-between space-y-2">
                <div class="flex items-center justify-between">
                    <span class="px-2 py-0.5 rounded-lg text-[10px] font-extrabold bg-indigo-50 text-indigo-700 border border-indigo-200/60" x-text="lang === 'zh' ? '交付与运输' : 'Pengiriman DO'">
                        Pengiriman DO
                    </span>
                    <div class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                        </svg>
                    </div>
                </div>
                <div>
                    <span class="text-2xl font-black text-[#17243B] tracking-tight block">
                        {{ $transactionStatuses['in_delivery'] ?? 5 }}
                    </span>
                    <span class="text-[10px] text-slate-500 font-medium leading-tight block mt-0.5" x-text="lang === 'zh' ? '在途与出货单据' : 'Surat jalan & pengiriman'">
                        Surat jalan & pengiriman
                    </span>
                </div>
            </a>

            <!-- Card 4: Invoice Unpaid (Tagihan Belum Lunas) -->
            <a href="{{ route('erp.finance.index') }}" 
               class="bg-white rounded-2xl border border-[#E3EAF3] p-3.5 shadow-[0_2px_8px_rgba(0,0,0,0.03)] hover:border-slate-300 transition flex flex-col justify-between space-y-2">
                <div class="flex items-center justify-between">
                    <span class="px-2 py-0.5 rounded-lg text-[10px] font-extrabold bg-rose-50 text-rose-700 border border-rose-200/60" x-text="lang === 'zh' ? '待付账单' : 'Invoice Unpaid'">
                        Invoice Unpaid
                    </span>
                    <div class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                </div>
                <div>
                    <span class="text-2xl font-black text-[#17243B] tracking-tight block">
                        {{ $transactionStatuses['unpaid_invoice'] ?? 4 }}
                    </span>
                    <span class="text-[10px] text-slate-500 font-medium leading-tight block mt-0.5" x-text="lang === 'zh' ? '尚未完成付款发票' : 'Invoice belum dibayar'">
                        Invoice belum dibayar
                    </span>
                </div>
            </a>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 4. RINGKASAN FINANSIAL (CARD HORIZONTAL RINGKAS SESUAI REFERENSI)          -->
    <!-- ========================================================================= -->
    <div class="bg-white rounded-2xl border border-[#E3EAF3] p-3.5 shadow-[0_2px_8px_rgba(0,0,0,0.03)] flex items-center justify-between">
        <div class="flex items-center space-x-3 min-w-0">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-[#2458E8] flex items-center justify-center shrink-0 border border-blue-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
            </div>
            <div class="min-w-0">
                <span class="text-[10.5px] font-semibold text-slate-500 block truncate" x-text="lang === 'zh' ? '待结算客户账单' : 'Invoice Belum Lunas'">
                    Invoice Belum Lunas
                </span>
                <div class="flex items-baseline space-x-1 mt-0.5">
                    <span class="text-base font-black text-[#17243B] leading-none">{{ $transactionStatuses['unpaid_invoice'] ?? 0 }}</span>
                    <span class="text-xs font-bold text-slate-500">Invoice</span>
                    <span class="text-[11px] text-slate-400 font-medium truncate">
                        (Rp {{ number_format($totalReceivables ?? 0, 0, ',', '.') }})
                    </span>
                </div>
            </div>
        </div>
        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-purple-50 text-purple-700 border border-purple-200/60 shrink-0" x-text="lang === 'zh' ? '部分/待结' : 'Tempo / Partial'">
            Tempo / Partial
        </span>
    </div>

    <!-- ========================================================================= -->
    <!-- 5. PERFORMA & UTILISASI ARMADA (MULTI-SEGMENT PROGRESS BAR)               -->
    <!-- ========================================================================= -->
    <div class="bg-gradient-to-br from-white via-white to-slate-50/50 rounded-2xl border border-[#E3EAF3] p-4 shadow-[0_2px_8px_rgba(0,0,0,0.03)] space-y-3">
        <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-[#17243B]" x-text="lang === 'zh' ? '车队利用率与状态' : 'Utilisasi & Status Armada'">
                Utilisasi & Status Armada
            </span>
            <span class="px-2 py-0.5 rounded-lg bg-slate-100 text-slate-600 text-[10px] font-extrabold" x-text="lang === 'zh' ? '本月' : 'Bulan Ini'">
                Bulan Ini
            </span>
        </div>

        @php
            $totalAssets = max(1, $displayTotalAssets ?? 30);
            $rentedPct = min(100, round((($displayRented ?? 18) / $totalAssets) * 100));
            $availablePct = min(100 - $rentedPct, round((($displayAvailable ?? 7) / $totalAssets) * 100));
            $maintPct = max(0, 100 - $rentedPct - $availablePct);
        @endphp

        <!-- Multi-segment progress bar -->
        <div class="h-2.5 w-full bg-slate-100 rounded-full flex overflow-hidden">
            <div style="width: {{ $rentedPct }}%" class="bg-emerald-500 transition-all duration-500" title="Disewa"></div>
            <div style="width: {{ $availablePct }}%" class="bg-amber-400 transition-all duration-500" title="Tersedia"></div>
            <div style="width: {{ $maintPct }}%" class="bg-[#2458E8] transition-all duration-500" title="Servis"></div>
        </div>

        <!-- Legend -->
        <div class="flex items-center justify-between text-[10px] font-semibold text-slate-600 pt-0.5">
            <div class="flex items-center space-x-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span x-text="lang === 'zh' ? '在租: {{ $displayRented ?? 18 }}' : 'Disewa: {{ $displayRented ?? 18 }}'">
                    Disewa: {{ $displayRented ?? 18 }}
                </span>
            </div>
            <div class="flex items-center space-x-1.5">
                <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                <span x-text="lang === 'zh' ? '空闲: {{ $displayAvailable ?? 7 }}' : 'Tersedia: {{ $displayAvailable ?? 7 }}'">
                    Tersedia: {{ $displayAvailable ?? 7 }}
                </span>
            </div>
            <div class="flex items-center space-x-1.5">
                <span class="w-2 h-2 rounded-full bg-[#2458E8]"></span>
                <span x-text="lang === 'zh' ? 'Servis: {{ $displayMaintenance ?? 3 }}' : 'Servis: {{ $displayMaintenance ?? 3 }}'">
                    Servis: {{ $displayMaintenance ?? 3 }}
                </span>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 6. PENGINGAT & AKTIVITAS OPERASIONAL (REMINDER CONTAINER TANPA EMOJI)      -->
    <!-- ========================================================================= -->
    <div class="space-y-2">
        <div class="flex items-center justify-between px-1">
            <div class="flex items-center space-x-1.5">
                <h4 class="text-xs font-bold text-[#17243B]" x-text="lang === 'zh' ? '运营待办与提醒' : 'Pengingat Operasional'">
                    Pengingat Operasional
                </h4>
                <span class="px-1.5 py-0.5 rounded-full text-[9px] font-extrabold uppercase bg-rose-50 text-rose-600 border border-rose-200">
                    Urgent
                </span>
            </div>
            <a href="{{ route('erp.orders.index', ['per_page' => 10]) }}" class="text-[11px] font-bold text-blue-600 hover:text-blue-800" x-text="lang === 'zh' ? '查看全部 >' : 'Lihat Semua >'">
                Lihat Semua >
            </a>
        </div>

        @if(!empty($systemAlerts) && count($systemAlerts) > 0)
            <div class="space-y-2">
                @foreach(array_slice($systemAlerts, 0, 2) as $alert)
                    <div class="bg-white rounded-2xl border border-[#E3EAF3] p-3 shadow-[0_2px_8px_rgba(0,0,0,0.03)] flex items-center justify-between space-x-3">
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-[#17243B] truncate">{{ $alert['title'] }}</p>
                            <p class="text-[10px] text-slate-500 truncate mt-0.5">{{ $alert['desc'] }}</p>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold shrink-0 {{ $alert['color'] === 'rose' ? 'bg-rose-50 text-rose-600 border border-rose-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                            {{ $alert['badge'] }}
                        </span>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-2xl border border-[#E3EAF3] p-4 text-center shadow-[0_2px_8px_rgba(0,0,0,0.03)]">
                <p class="text-xs text-slate-500" x-text="lang === 'zh' ? '未来 2 天内无紧急待办。' : 'Tidak ada jadwal pick-up atau pengingat untuk 2 hari ke depan.'">
                    Tidak ada jadwal pick-up atau pengingat untuk 2 hari ke depan.
                </p>
            </div>
        @endif
    </div>

</div>
