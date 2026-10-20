{{-- ========================================================================= --}}
{{-- MOBILE CARD UI COMPONENT: INVOICE & TAGIHAN PELANGGAN / UTANG VENDOR      --}}
{{-- Rendered exclusively on mobile screens (< md)                             --}}
{{-- Consistent with Penawaran & Order Mobile UX Design Pattern                --}}
{{-- ========================================================================= --}}

@if($tab === 'invoices')
    @forelse($customerInvoices as $index => $inv)
        @php
            $isOverdue = ($inv->due_date && $inv->due_date->isPast() && $inv->balance_due > 0);
            $orderNumbers = $inv->orders ? $inv->orders->pluck('order_number')->implode(' ') : ($inv->order->order_number ?? '');
            $searchHaystack = strtolower(implode(' ', [
                $inv->invoice_number,
                $inv->contact->name ?? '',
                $inv->contact->pic_name ?? '',
                $inv->contact->phone ?? '',
                $orderNumbers
            ]));

            // Role permissions
            $user = auth()->user();
            $isAdmin = $user ? $user->isAdmin() : false;
            $isFinance = $user ? $user->isFinance() : false;
            $canRecordPayment = $isAdmin || $isFinance;
        @endphp

        <div class="bg-white rounded-2xl border border-[#E5EAF2] p-3.5 shadow-[0_2px_8px_rgba(0,0,0,0.03)] space-y-3 relative transition hover:border-slate-300 {{ $isOverdue ? 'border-rose-200' : '' }}"
             x-show="matchesInvoiceRow('{{ addslashes($searchHaystack) }}', '{{ $inv->status }}', {{ $isOverdue ? 'true' : 'false' }})">

            <!-- 1. BARIS IDENTITAS INVOICE, TIPE & STATUS PILL -->
            <div class="flex items-center justify-between gap-2">
                <!-- Left: Type Badge + Doc Number + Date -->
                <div class="flex items-center space-x-1.5 min-w-0">
                    <span class="bg-blue-600 text-white text-[9px] font-black px-2 py-0.5 rounded-full shrink-0 tracking-wider">
                        INV
                    </span>
                    <button type="button" @click="openDetailsDrawer(@js($inv))" class="font-mono font-black text-sm text-slate-900 truncate hover:text-blue-600 transition text-left">
                        {{ $inv->invoice_number }}
                    </button>
                    @if($inv->issue_date)
                        <span class="text-[10px] text-slate-400 shrink-0 font-medium">
                            {{ $inv->issue_date->format('d/m/y') }}
                        </span>
                    @endif
                </div>

                <!-- Right: Status Pill -->
                <div class="shrink-0">
                    @if($inv->status === 'paid')
                        <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            <span x-text="lang === 'zh' ? '已付清' : 'Lunas'">Lunas</span>
                        </span>
                    @elseif($inv->status === 'partial')
                        <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                            <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span x-text="lang === 'zh' ? '部分付款' : 'Sebagian'">Sebagian</span>
                        </span>
                    @elseif($isOverdue)
                        <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                            <svg class="w-3 h-3 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span x-text="lang === 'zh' ? '逾期未付' : 'Lewat Tempo'">Lewat Tempo</span>
                        </span>
                    @else
                        <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                            <svg class="w-3 h-3 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"></circle><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2"></path></svg>
                            <span x-text="lang === 'zh' ? '待付款' : 'Belum Bayar'">Belum Bayar</span>
                        </span>
                    @endif
                </div>
            </div>

            <!-- 2. CUSTOMER & ORDER TERKAIT -->
            <div class="bg-slate-50/70 p-2.5 rounded-xl border border-slate-100 space-y-2">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-1.5 min-w-0">
                        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        <span class="font-black text-xs text-slate-900 truncate">
                            {{ $inv->contact->name ?? 'Pelanggan Keylock' }}
                        </span>
                    </div>
                    @if($inv->due_date)
                        <span class="text-[10px] {{ $isOverdue ? 'text-rose-600 font-bold' : 'text-slate-400' }} shrink-0">
                            Tempo: {{ $inv->due_date->format('d/m/y') }}
                        </span>
                    @endif
                </div>

                <!-- Order Terkait Badges -->
                <div class="flex flex-wrap items-center gap-1.5 pt-0.5">
                    <span class="text-[10px] font-bold text-slate-400" x-text="lang === 'zh' ? '关联合同:' : 'Order:'">Order:</span>
                    @if($inv->orders && $inv->orders->count() > 0)
                        @foreach($inv->orders as $ord)
                            <a href="{{ route('erp.orders.index', ['type' => 'transaksi', 'search' => $ord->order_number]) }}" 
                               class="px-2 py-0.5 rounded-md font-mono font-bold text-[10px] {{ $ord->order_type === 'rental_order' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }} hover:underline">
                                {{ $ord->order_number }}
                            </a>
                        @endforeach
                    @elseif($inv->order)
                        <a href="{{ route('erp.orders.index', ['type' => 'transaksi', 'search' => $inv->order->order_number]) }}" 
                           class="px-2 py-0.5 rounded-md font-mono font-bold text-[10px] {{ $inv->order->order_type === 'rental_order' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }} hover:underline">
                            {{ $inv->order->order_number }}
                        </a>
                    @else
                        <span class="text-[10px] text-slate-400 italic">Langsung (Non Order)</span>
                    @endif
                </div>
            </div>

            <!-- 3. FINANCIAL SUMMARY (3 KOLOM SEIMBANG) -->
            <div class="grid grid-cols-3 gap-2 bg-slate-50/90 p-2.5 rounded-xl text-center border border-slate-100">
                <div>
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block" x-text="lang === 'zh' ? '总额' : 'Total Tagihan'">Total Tagihan</span>
                    <span class="font-mono font-black text-xs text-slate-900 block mt-0.5">
                        Rp {{ number_format($inv->total_amount, 0, ',', '.') }}
                    </span>
                </div>
                <div class="border-x border-slate-200">
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block" x-text="lang === 'zh' ? '已收' : 'Sudah Dibayar'">Sudah Dibayar</span>
                    <span class="font-mono font-bold text-xs text-emerald-600 block mt-0.5">
                        Rp {{ number_format($inv->paid_amount, 0, ',', '.') }}
                    </span>
                </div>
                <div>
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block" x-text="lang === 'zh' ? '未结' : 'Sisa Piutang'">Sisa Piutang</span>
                    <span class="font-mono font-black text-xs {{ $inv->balance_due > 0 ? 'text-rose-600' : 'text-slate-400' }} block mt-0.5">
                        Rp {{ number_format($inv->balance_due, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <!-- 4. ACTION BUTTONS -->
            <div class="pt-1 flex items-center justify-between gap-2 border-t border-slate-100">
                <div class="flex items-center space-x-1.5">
                    <!-- Detail Drawer -->
                    <button type="button" 
                            @click="openDetailsDrawer(@js($inv))" 
                            class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition flex items-center space-x-1 active:scale-95 shadow-2xs">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        <span x-text="lang === 'zh' ? '详情' : 'Detail'">Detail</span>
                    </button>

                    <!-- Cetak PDF -->
                    <a href="{{ route('erp.finance.invoices.pdf', $inv->id) }}" 
                       target="_blank" 
                       class="px-2.5 py-1.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-800 text-xs font-bold border border-amber-200 transition flex items-center space-x-1 shadow-2xs active:scale-95" 
                       title="Cetak PDF">
                        <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        <span x-text="lang === 'zh' ? 'PDF' : 'PDF'">PDF</span>
                    </a>
                </div>

                <!-- Bayar / Catat Pembayaran -->
                @if($inv->balance_due > 0 && $canRecordPayment)
                    <button type="button" 
                            @click="openPaymentModal(@js($inv), 'in')" 
                            class="px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition flex items-center space-x-1.5 shadow-xs active:scale-95">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <rect x="2" y="6" width="20" height="12" rx="2" stroke-width="2"></rect>
                            <circle cx="12" cy="12" r="2.5" stroke-width="2"></circle>
                        </svg>
                        <span x-text="lang === 'zh' ? '录入收款' : 'Catat Bayar'">Catat Bayar</span>
                    </button>
                @endif
            </div>
        </div>
    @empty
        <div class="bg-white rounded-2xl border border-slate-200 p-8 text-center text-slate-400">
            <p class="font-bold text-slate-600" x-text="lang === 'zh' ? '暂无客户发票。' : 'Belum ada invoice tagihan pelanggan.'">Belum ada invoice tagihan pelanggan.</p>
            <button type="button" @click="openInvoiceModal()" class="mt-2 text-blue-600 font-bold hover:underline" x-text="lang === 'zh' ? '+ 开具首张发票' : '+ Buat Invoice Pertama'">+ Buat Invoice Pertama</button>
        </div>
    @endforelse

@elseif($tab === 'bills')
    @forelse($vendorBills as $index => $bill)
        @php
            $isOverdue = ($bill->due_date && $bill->due_date->isPast() && $bill->balance_due > 0);
            $searchHaystack = strtolower(implode(' ', [
                $bill->invoice_number,
                $bill->vendor_ref_no ?? '',
                $bill->contact->name ?? '',
                $bill->contact->pic_name ?? '',
                $bill->order?->order_number ?? ''
            ]));

            $user = auth()->user();
            $isAdmin = $user ? $user->isAdmin() : false;
            $isFinance = $user ? $user->isFinance() : false;
            $canRecordPayment = $isAdmin || $isFinance;
        @endphp

        <div class="bg-white rounded-2xl border border-[#E5EAF2] p-3.5 shadow-[0_2px_8px_rgba(0,0,0,0.03)] space-y-3 relative transition hover:border-slate-300 {{ $isOverdue ? 'border-rose-200' : '' }}"
             x-show="matchesBillRow('{{ addslashes($searchHaystack) }}', '{{ $bill->status }}', {{ $isOverdue ? 'true' : 'false' }})">

            <!-- 1. IDENTITAS BILL, TIPE & STATUS PILL -->
            <div class="flex items-center justify-between gap-2">
                <div class="flex items-center space-x-1.5 min-w-0">
                    <span class="bg-purple-600 text-white text-[9px] font-black px-2 py-0.5 rounded-full shrink-0 tracking-wider">
                        BILL
                    </span>
                    <button type="button" @click="openDetailsDrawer(@js($bill))" class="font-mono font-black text-sm text-slate-900 truncate hover:text-purple-600 transition text-left">
                        {{ $bill->invoice_number }}
                    </button>
                    @if($bill->issue_date)
                        <span class="text-[10px] text-slate-400 shrink-0 font-medium">
                            {{ $bill->issue_date->format('d/m/y') }}
                        </span>
                    @endif
                </div>

                <div class="shrink-0">
                    @if($bill->status === 'paid')
                        <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span x-text="lang === 'zh' ? '已结清' : 'Lunas'">Lunas</span>
                        </span>
                    @elseif($bill->status === 'partial')
                        <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                            <span x-text="lang === 'zh' ? '部分付款' : 'Sebagian'">Sebagian</span>
                        </span>
                    @elseif($isOverdue)
                        <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                            <span x-text="lang === 'zh' ? '逾期未付' : 'Lewat Tempo'">Lewat Tempo</span>
                        </span>
                    @else
                        <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                            <span x-text="lang === 'zh' ? '待付款' : 'Belum Bayar'">Belum Bayar</span>
                        </span>
                    @endif
                </div>
            </div>

            <!-- 2. VENDOR & PO TERKAIT -->
            <div class="bg-slate-50/70 p-2.5 rounded-xl border border-slate-100 space-y-1.5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-1.5 min-w-0">
                        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        <span class="font-black text-xs text-slate-900 truncate">
                            {{ $bill->contact->name ?? 'Vendor Mitra' }}
                        </span>
                    </div>
                    @if($bill->due_date)
                        <span class="text-[10px] {{ $isOverdue ? 'text-rose-600 font-bold' : 'text-slate-400' }} shrink-0">
                            Tempo: {{ $bill->due_date->format('d/m/y') }}
                        </span>
                    @endif
                </div>

                <div class="flex items-center justify-between text-[11px] pt-0.5">
                    <span class="text-slate-500 font-mono">PO: <span class="font-bold text-slate-800">{{ $bill->order?->order_number ?? '-' }}</span></span>
                    @if($bill->vendor_ref_no)
                        <span class="text-slate-500">Ref: <span class="font-mono font-bold text-slate-700">{{ $bill->vendor_ref_no }}</span></span>
                    @endif
                </div>
            </div>

            <!-- 3. FINANCIAL SUMMARY -->
            <div class="grid grid-cols-3 gap-2 bg-slate-50/90 p-2.5 rounded-xl text-center border border-slate-100">
                <div>
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block" x-text="lang === 'zh' ? '总额' : 'Total Tagihan'">Total Tagihan</span>
                    <span class="font-mono font-black text-xs text-slate-900 block mt-0.5">
                        Rp {{ number_format($bill->total_amount, 0, ',', '.') }}
                    </span>
                </div>
                <div class="border-x border-slate-200">
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block" x-text="lang === 'zh' ? '已付' : 'Sudah Dibayar'">Sudah Dibayar</span>
                    <span class="font-mono font-bold text-xs text-emerald-600 block mt-0.5">
                        Rp {{ number_format($bill->paid_amount, 0, ',', '.') }}
                    </span>
                </div>
                <div>
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block" x-text="lang === 'zh' ? '未结' : 'Sisa Utang'">Sisa Utang</span>
                    <span class="font-mono font-black text-xs {{ $bill->balance_due > 0 ? 'text-rose-600' : 'text-slate-400' }} block mt-0.5">
                        Rp {{ number_format($bill->balance_due, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <!-- 4. ACTION BUTTONS -->
            <div class="pt-1 flex items-center justify-between gap-2 border-t border-slate-100">
                <div class="flex items-center space-x-1.5">
                    <button type="button" 
                            @click="openDetailsDrawer(@js($bill))" 
                            class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition flex items-center space-x-1 shadow-2xs active:scale-95">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        <span x-text="lang === 'zh' ? '详情' : 'Detail'">Detail</span>
                    </button>
                </div>

                @if($bill->balance_due > 0 && $canRecordPayment)
                    <button type="button" 
                            @click="openPaymentModal(@js($bill), 'out')" 
                            class="px-3.5 py-1.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold transition flex items-center space-x-1.5 shadow-xs active:scale-95">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <rect x="2" y="6" width="20" height="12" rx="2" stroke-width="2"></rect>
                            <circle cx="12" cy="12" r="2.5" stroke-width="2"></circle>
                        </svg>
                        <span x-text="lang === 'zh' ? '支付账单' : 'Bayar Tagihan'">Bayar Tagihan</span>
                    </button>
                @endif
            </div>
        </div>
    @empty
        <div class="bg-white rounded-2xl border border-slate-200 p-8 text-center text-slate-400">
            <p class="font-bold text-slate-600" x-text="lang === 'zh' ? '暂无供应商账单。' : 'Belum ada tagihan vendor.'">Belum ada tagihan vendor.</p>
        </div>
    @endforelse
@endif
