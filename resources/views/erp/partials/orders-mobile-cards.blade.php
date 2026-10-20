{{-- ========================================================================= --}}
{{-- MOBILE CARD UI COMPONENT: ORDERS & QUOTATION (PHASE 4D.1B REDESIGN)       --}}
{{-- Rendered exclusively on screens below the md breakpoint (block md:hidden) --}}
{{-- ========================================================================= --}}

@forelse($orders as $index => $order)
    @php
        $hasDeliveries = $order->deliveries && $order->deliveries->isNotEmpty();
        $firstDelivery = $hasDeliveries ? $order->deliveries->first() : null;

        // Customer Invoice Lookup
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

        // Quotation Computed Status
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

        // Client-side search haystack matching desktop table
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

        $items = $order->items ?? collect();
        $firstItem = $items->first();
        $itemCount = $items->count();

        // Role & Ownership Permissions
        $user = auth()->user();
        $isAdmin = $user ? $user->isAdmin() : false;
        $isSales = $user ? $user->isSales() : false;
        $isOps = $user ? $user->isOperational() : false;
        $isFinance = $user ? $user->isFinance() : false;

        $canManageQuotation = $isAdmin || ($isSales && (int)($order->created_by ?? 0) === (int)$user->id);
        $canCancelOrder = $isAdmin || ((int)($order->created_by ?? 0) === (int)$user->id);
        $canCreateDo = $isAdmin || $isOps;
        $canCreatePo = $isAdmin || $isOps;
        $canCreateInvoice = $isAdmin || $isFinance;

        // PDF URL helper for Primary Cetak button
        if ($order->order_type === 'quotation') {
            $printUrl = route('erp.quotations.pdf', $order->id);
        } elseif ($hasDeliveries) {
            $printUrl = route('erp.deliveries.pdf', $firstDelivery->id);
        } elseif ($order->parent) {
            $printUrl = route('erp.quotations.pdf', $order->parent->id);
        } else {
            $printUrl = route('erp.quotations.pdf', $order->id);
        }
    @endphp

    <div class="bg-white rounded-2xl border border-[#E5EAF2] p-3 sm:p-3.5 shadow-[0_2px_8px_rgba(0,0,0,0.03)] space-y-3 relative transition hover:border-slate-300 {{ $isCancelled ? 'bg-slate-50/70 border-dashed' : '' }}"
         x-show="matchesRow('{{ addslashes($searchHaystack) }}', '{{ $order->status }}', '{{ $order->order_type === 'quotation' ? $computedQuotationStatus : '' }}')">

        <!-- 1. BARIS IDENTITAS DOKUMEN & STATUS -->
        <div class="flex items-center justify-between gap-1">
            <!-- Left: Type Badge + Doc Number + Date -->
            <div class="flex items-center space-x-1 sm:space-x-1.5 min-w-0">
                <!-- Type Badge -->
                @if($order->order_type === 'quotation')
                    <span class="bg-[#2458E8] text-white text-[9px] font-black px-1.5 sm:px-2 py-0.5 rounded-full shrink-0 tracking-wider">
                        QTN
                    </span>
                @elseif($order->order_type === 'rental_order')
                    <span class="bg-emerald-600 text-white text-[9px] font-black px-1.5 sm:px-2 py-0.5 rounded-full shrink-0 tracking-wider">
                        RO
                    </span>
                @else
                    <span class="bg-amber-500 text-white text-[9px] font-black px-1.5 sm:px-2 py-0.5 rounded-full shrink-0 tracking-wider">
                        SO
                    </span>
                @endif

                <!-- Doc Number -->
                <span class="font-bold text-[10px] sm:text-[11px] text-[#17243B] font-mono select-text shrink-0">
                    {{ $order->order_number }}
                </span>

                <!-- Divider -->
                <span class="text-slate-300 font-light shrink-0">|</span>

                <!-- Date with Calendar Icon -->
                <span class="inline-flex items-center space-x-0.5 sm:space-x-1 text-[9.5px] sm:text-[10px] text-slate-500 font-medium shrink-0">
                    <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span>{{ $order->order_date ? $order->order_date->format('d M y') : '-' }}</span>
                </span>
            </div>

            <!-- Right: Status Badge & Action Menu Trigger (•••) -->
            <div class="flex items-center space-x-1 shrink-0">
                <!-- Status Badge -->
                @if($order->order_type === 'quotation')
                    @if($computedQuotationStatus === 'cancel')
                        <span class="px-1.5 sm:px-2 py-0.5 rounded-full text-[8.5px] sm:text-[9px] font-extrabold tracking-wider uppercase bg-rose-50 text-rose-600 border border-rose-200">
                            BATAL
                        </span>
                    @elseif($computedQuotationStatus === 'transaksi')
                        <span class="px-1.5 sm:px-2 py-0.5 rounded-full text-[8.5px] sm:text-[9px] font-extrabold tracking-wider uppercase bg-blue-50 text-blue-600 border border-blue-200">
                            TRANSAKSI
                        </span>
                    @elseif($computedQuotationStatus === 'tidak_aktif')
                        <span class="px-1.5 sm:px-2 py-0.5 rounded-full text-[8.5px] sm:text-[9px] font-extrabold tracking-wider uppercase bg-slate-100 text-slate-500 border border-slate-200">
                            TIDAK AKTIF
                        </span>
                    @else
                        <span class="px-1.5 sm:px-2 py-0.5 rounded-full text-[8.5px] sm:text-[9px] font-extrabold tracking-wider uppercase bg-amber-50 text-amber-700 border border-amber-200">
                            AKTIF
                        </span>
                    @endif
                @else
                    @if($order->status === 'completed')
                        <span class="px-1.5 sm:px-2 py-0.5 rounded-full text-[8.5px] sm:text-[9px] font-extrabold tracking-wider uppercase bg-emerald-50 text-emerald-600 border border-emerald-200">
                            SELESAI
                        </span>
                    @elseif($order->status === 'cancelled')
                        <span class="px-1.5 sm:px-2 py-0.5 rounded-full text-[8.5px] sm:text-[9px] font-extrabold tracking-wider uppercase bg-rose-50 text-rose-600 border border-rose-200">
                            BATAL
                        </span>
                    @else
                        <span class="px-1.5 sm:px-2 py-0.5 rounded-full text-[8.5px] sm:text-[9px] font-extrabold tracking-wider uppercase bg-blue-50 text-blue-600 border border-blue-200">
                            BERJALAN
                        </span>
                    @endif
                @endif

                <!-- Menu ••• Button (Top Right) -->
                <div class="relative inline-block text-left">
                    <button type="button" 
                            @click.stop="toggleActionMenu({{ $order->id }})" 
                            class="p-0.5 sm:p-1 text-slate-400 hover:text-slate-700 rounded-lg hover:bg-slate-100 transition"
                            aria-label="Menu Aksi"
                            :aria-expanded="activeActionMenuId === {{ $order->id }}">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <circle cx="5" cy="12" r="2" />
                            <circle cx="12" cy="12" r="2" />
                            <circle cx="19" cy="12" r="2" />
                        </svg>
                    </button>

                    <!-- Contextual Action Menu Dropdown -->
                    <div x-show="activeActionMenuId === {{ $order->id }}" 
                         @click.outside="if (activeActionMenuId === {{ $order->id }}) closeActionMenu()" 
                         x-cloak
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 top-full mt-1.5 w-52 bg-white rounded-2xl shadow-xl border border-slate-200 py-1.5 z-50 text-left">
                        
                        @if($order->order_type === 'quotation')
                            <!-- Edit Penawaran -->
                            @if($canManageQuotation && !$isConverted && $order->status !== 'cancelled')
                                <button type="button" 
                                        @click="closeActionMenu(); editQuotation(@js($order))" 
                                        class="w-full flex items-center space-x-2.5 px-3.5 py-2.5 text-xs font-semibold text-slate-700 hover:bg-blue-50 hover:text-blue-700 transition text-left">
                                    <span class="w-6 h-6 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </span>
                                    <span x-text="lang === 'zh' ? '编辑报价单' : 'Edit Penawaran'">Edit Penawaran</span>
                                </button>
                            @endif

                            <!-- Cancel Penawaran -->
                            @if($canCancelOrder && $order->status !== 'cancelled')
                                <div class="my-1 border-t border-slate-100"></div>
                                <button type="button" 
                                        @click="closeActionMenu(); openCancelModal(@js($order))" 
                                        class="w-full flex items-center space-x-2.5 px-3.5 py-2.5 text-xs font-semibold text-rose-600 hover:bg-rose-50 transition text-left">
                                    <span class="w-6 h-6 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </span>
                                    <span x-text="lang === 'zh' ? '取消报价单' : 'Batalkan Penawaran'">Batalkan Penawaran</span>
                                </button>
                            @endif
                        @else
                            <!-- Order Actions: Buat PO Vendor -->
                            @if($canCreatePo && !$isCancelled)
                                <button type="button" 
                                        @click="closeActionMenu(); openAllocateModal(@js($order))" 
                                        class="w-full flex items-center space-x-2.5 px-3.5 py-2.5 text-xs font-semibold text-slate-700 hover:bg-purple-50 hover:text-purple-800 transition text-left">
                                    <span class="w-6 h-6 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                    </span>
                                    <span x-text="lang === 'zh' ? '采购单 / PO' : 'Atur PO Vendor'">Atur PO Vendor</span>
                                </button>
                            @endif

                            <!-- Terbitkan DO Baru -->
                            @if($canCreateDo && !$isCancelled)
                                <button type="button" 
                                        @click="closeActionMenu(); openDoModal(@js($order))" 
                                        class="w-full flex items-center space-x-2.5 px-3.5 py-2.5 text-xs font-semibold text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 transition text-left">
                                    <span class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    </span>
                                    <span x-text="lang === 'zh' ? '开具新送货单' : 'Terbitkan DO Baru'">Terbitkan DO Baru</span>
                                </button>
                            @endif

                            <!-- Buat Invoice Tagihan -->
                            @if($canCreateInvoice && !$orderInvoice && !$isCancelled)
                                <button type="button" 
                                        @click="closeActionMenu(); openGenerateInvoiceModal(@js($order))" 
                                        class="w-full flex items-center space-x-2.5 px-3.5 py-2.5 text-xs font-semibold text-slate-700 hover:bg-amber-50 hover:text-amber-800 transition text-left">
                                    <span class="w-6 h-6 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"></path></svg>
                                    </span>
                                    <span x-text="lang === 'zh' ? '开具发票' : 'Buat Invoice Tagihan'">Buat Invoice Tagihan</span>
                                </button>
                            @endif

                            <!-- Cancel Transaksi -->
                            @if($canCancelOrder && $order->status !== 'cancelled')
                                <div class="my-1 border-t border-slate-100"></div>
                                <button type="button" 
                                        @click="closeActionMenu(); openCancelModal(@js($order))" 
                                        class="w-full flex items-center space-x-2.5 px-3.5 py-2.5 text-xs font-semibold text-rose-600 hover:bg-rose-50 transition text-left">
                                    <span class="w-6 h-6 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </span>
                                    <span x-text="lang === 'zh' ? '取消交易订单' : 'Batalkan Transaksi'">Batalkan Transaksi</span>
                                </button>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. INFORMASI PELANGGAN -->
        <div class="space-y-1">
            <div class="flex items-center space-x-2 min-w-0">
                <!-- Office Building Icon -->
                <svg class="w-4 h-4 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <h3 class="text-sm font-black text-[#17243B] truncate leading-tight" title="{{ $order->contact->name ?? '-' }}">
                    {{ $order->contact->name ?? 'Pelanggan Tidak Diketahui' }}
                </h3>
            </div>

            <!-- PIC & Phone -->
            @if($order->contact && ($order->contact->pic_name || $order->contact->phone))
                <div class="flex items-center space-x-2 text-xs text-slate-500 pl-0.5">
                    <!-- User Icon -->
                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span class="truncate">
                        {{ $order->contact->pic_name ?? '-' }} @if($order->contact->phone) ({{ $order->contact->phone }}) @endif
                    </span>
                    @if(!empty($order->customer_po_number))
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-blue-50 text-blue-700 border border-blue-200 text-[10px] font-mono font-bold shrink-0">
                            PO: {{ $order->customer_po_number }}
                        </span>
                    @endif
                </div>
            @endif
        </div>

        <!-- 3. RINGKASAN PRODUK & OPERASIONAL (CHIPS) -->
        <div class="flex flex-wrap items-center gap-1.5">
            <!-- Chip 1: Primary Product Name -->
            <div class="inline-flex items-center space-x-1.5 bg-slate-50 border border-slate-200/80 rounded-lg px-2.5 py-1 text-xs text-slate-700 max-w-[200px]">
                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                <span class="font-bold truncate text-[#17243B]">
                    {{ $firstItem ? ($firstItem->description ?: ($firstItem->item?->name ?? 'Produk')) : 'Unit Pesanan' }}
                </span>
            </div>

            <!-- Chip 2: Quantity & Duration -->
            <div class="inline-flex items-center space-x-1.5 bg-slate-50 border border-slate-200/80 rounded-lg px-2.5 py-1 text-xs text-slate-700 shrink-0">
                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span class="font-medium">
                    {{ $firstItem ? (float)$firstItem->quantity : 1 }} Unit &bull; {{ $order->order_type === 'rental_order' ? ($order->rental_duration ?: 1) . ' ' . (in_array(strtolower($order->rental_duration_unit ?? ''), ['month', 'bulan']) ? 'Bulan' : ucfirst($order->rental_duration_unit ?: 'Bulan')) : ($order->order_type === 'sales_order' ? 'Pembelian' : 'Unit') }}
                </span>
            </div>

            <!-- Chip 3: Validity / DO / Invoice -->
            @if($order->order_type === 'quotation')
                <div class="inline-flex items-center space-x-1.5 bg-slate-50 border border-slate-200/80 rounded-lg px-2.5 py-1 text-xs text-slate-700 shrink-0">
                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Berlaku s/d {{ $order->valid_until ? \Carbon\Carbon::parse($order->valid_until)->format('d M Y') : '-' }}</span>
                </div>
            @elseif($hasDeliveries)
                <a href="{{ route('erp.deliveries.pdf', $firstDelivery->id) }}" 
                   target="_blank" 
                   class="inline-flex items-center space-x-1.5 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 text-emerald-800 rounded-lg px-2.5 py-1 text-xs font-semibold transition shrink-0"
                   title="Lihat Surat Jalan PDF">
                    <svg class="w-3.5 h-3.5 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0zM13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h2"></path>
                    </svg>
                    <span>DO: {{ $firstDelivery->delivery_number }}</span>
                </a>
            @elseif($orderInvoice)
                <a href="{{ route('erp.invoices.pdf', $orderInvoice->id) }}" 
                   target="_blank" 
                   class="inline-flex items-center space-x-1.5 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 text-indigo-800 rounded-lg px-2.5 py-1 text-xs font-semibold transition shrink-0"
                   title="Lihat Invoice PDF">
                    <svg class="w-3.5 h-3.5 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"></path>
                    </svg>
                    <span>Invoice: {{ $orderInvoice->invoice_number }}</span>
                </a>
            @endif

            <!-- Expand More Units Button -->
            @if($itemCount > 1)
                <button type="button" 
                        @click="toggleExpand({{ $order->id }})" 
                        class="inline-flex items-center space-x-1 bg-blue-50 hover:bg-blue-100 border border-blue-200 text-blue-700 rounded-lg px-2 py-1 text-xs font-bold transition shrink-0">
                    <span>+ {{ $itemCount - 1 }} unit</span>
                    <svg class="w-3 h-3 transition-transform duration-200" :class="isExpanded({{ $order->id }}) ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            @endif
        </div>

        <!-- Expandable Product Details Accordion -->
        <div x-show="isExpanded({{ $order->id }})" 
             x-cloak 
             class="pt-2 border-t border-slate-200 space-y-2 mt-2">
            @foreach($items as $itIndex => $it)
                <div class="bg-white p-2.5 rounded-xl border border-slate-200/90 text-xs space-y-1">
                    <div class="flex items-start justify-between gap-2">
                        <span class="font-bold text-slate-900 leading-tight">
                            {{ $itIndex + 1 }}. {{ $it->description ?: ($it->item?->name ?? 'Produk') }}
                        </span>
                        <span class="font-bold font-mono text-slate-700 text-[11px] shrink-0">
                            {{ (float)$it->quantity }} {{ $it->unit ?: 'Unit' }}
                        </span>
                    </div>
                    @if($it->specification)
                        <p class="text-[10px] text-slate-500 font-mono">{{ $it->specification }}</p>
                    @endif
                    <div class="flex items-center justify-between text-[11px] pt-1 border-t border-slate-100 font-mono">
                        <span class="text-slate-500">
                            @if($order->order_type === 'rental_order' || $it->transaction_type === 'rental')
                                {{ $it->duration ?: ($order->rental_duration ?: 1) }} {{ $it->duration_unit ?: 'Bulan' }} &bull; Rp {{ number_format($it->unit_price, 0, ',', '.') }}
                            @else
                                @ Rp {{ number_format($it->unit_price, 0, ',', '.') }}
                            @endif
                        </span>
                        <span class="font-black text-slate-900">
                            Rp {{ number_format($it->line_total, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            @endforeach

            <!-- Subtotal in expander -->
            <div class="flex justify-between text-[11px] font-bold text-slate-600 px-1 pt-1">
                <span x-text="lang === 'zh' ? '小计金额' : 'Subtotal Produk:'">Subtotal Produk:</span>
                <span class="font-mono text-slate-900 font-extrabold">Rp {{ number_format($order->subtotal ?: $items->sum('line_total'), 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- 4. TOTAL NILAI BOX (SOFT BLUE BACKGROUND) -->
        <div class="bg-[#F3F6FD] rounded-xl p-3 border border-[#E3ECFB] flex items-center justify-between">
            <div>
                <span class="text-[10px] font-semibold text-blue-600 uppercase tracking-wider block">
                    Total Nilai
                </span>
                <span class="text-base font-black text-[#17243B] font-mono tracking-tight">
                    Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                </span>
            </div>

            <!-- Lihat Detail Button -->
            <button type="button" 
                    @click="openDrawer({{ $order->id }})" 
                    class="text-xs font-bold text-blue-600 hover:text-blue-700 bg-white px-2.5 py-1.5 rounded-lg border border-blue-200/80 shadow-2xs flex items-center space-x-1.5 min-h-[36px] active:scale-95 transition"
                    aria-label="Lihat Detail Transaksi">
                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span x-text="lang === 'zh' ? '查看详情' : 'Lihat Detail'">Lihat Detail</span>
                <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
        </div>

        <!-- 5. TOMBOL AKSI UTAMA (CETAK PDF + WORKFLOW PRIMARY + MORE MENU) -->
        <div class="pt-2 border-t border-slate-100 flex items-center space-x-2 {{ $isCancelled ? 'opacity-50 pointer-events-none' : '' }}">
            <!-- Button 1: Cetak PDF (Outline Style) -->
            <a href="{{ $printUrl }}" 
               target="_blank" 
               class="flex-1 min-h-[44px] py-2.5 px-3 rounded-xl bg-white hover:bg-slate-50 text-blue-600 border border-blue-200/90 font-extrabold text-xs flex items-center justify-center space-x-1.5 transition active:scale-95 shadow-2xs"
               aria-label="Cetak PDF">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                <span x-text="lang === 'zh' ? '打印 PDF' : 'Cetak PDF'">Cetak PDF</span>
            </a>

            <!-- Button 2: Primary Workflow Action (Navy Button #142A57) -->
            @if($order->order_type === 'quotation')
                @if(!$isConverted && $order->status !== 'cancelled' && $canManageQuotation)
                    <button type="button" 
                            @click="generateTransaction({{ $order->id }}, '{{ $order->order_number }}')" 
                            class="flex-1 min-h-[44px] py-2.5 px-3 rounded-xl bg-[#142A57] hover:bg-[#0d1b38] text-white font-extrabold text-xs flex items-center justify-center space-x-1.5 transition active:scale-95 shadow-xs"
                            aria-label="Generate Transaksi">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                        </svg>
                        <span x-text="lang === 'zh' ? '生成交易' : 'Jadikan Transaksi'">Jadikan Transaksi</span>
                    </button>
                @endif
            @else
                @if(!$hasDeliveries && !$isCancelled && $canCreateDo)
                    <button type="button" 
                            @click="openDoModal(@js($order))" 
                            class="flex-1 min-h-[44px] py-2.5 px-3 rounded-xl bg-[#142A57] hover:bg-[#0d1b38] text-white font-extrabold text-xs flex items-center justify-center space-x-1.5 transition active:scale-95 shadow-xs"
                            aria-label="Terbitkan Surat Jalan (DO)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0zM13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h2"></path>
                        </svg>
                        <span x-text="lang === 'zh' ? '开送货单' : 'Buat DO'">Buat DO</span>
                    </button>
                @elseif(!$orderInvoice && !$isCancelled && $canCreateInvoice)
                    <button type="button" 
                            @click="openGenerateInvoiceModal(@js($order))" 
                            class="flex-1 min-h-[44px] py-2.5 px-3 rounded-xl bg-[#142A57] hover:bg-[#0d1b38] text-white font-extrabold text-xs flex items-center justify-center space-x-1.5 transition active:scale-95 shadow-xs"
                            aria-label="Buat Faktur Invoice">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"></path>
                        </svg>
                        <span x-text="lang === 'zh' ? '开具发票' : 'Buat Invoice'">Buat Invoice</span>
                    </button>
                @elseif($order->status !== 'completed' && !$isCancelled && $canCreateDo)
                    <button type="button" 
                            @click="completeTransaction({{ $order->id }}, '{{ $order->order_number }}')" 
                            class="flex-1 min-h-[44px] py-2.5 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs flex items-center justify-center space-x-1.5 transition active:scale-95 shadow-xs"
                            aria-label="Selesaikan Transaksi">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span x-text="lang === 'zh' ? '完成' : 'Selesai'">Selesai</span>
                    </button>
                @endif
            @endif

            <!-- Button 3: Contextual Dropdown Menu (•••) -->
            <div class="relative inline-block text-left">
                <button type="button" 
                        @click.stop="toggleActionMenu({{ $order->id }})" 
                        class="w-11 h-11 rounded-xl bg-white hover:bg-slate-50 text-slate-600 border border-slate-200 flex items-center justify-center transition active:scale-95 shadow-2xs shrink-0"
                        :class="activeActionMenuId === {{ $order->id }} ? 'bg-slate-100 text-slate-900 ring-2 ring-blue-500' : ''"
                        aria-label="Aksi Tambahan"
                        :aria-expanded="activeActionMenuId === {{ $order->id }}">
                    <svg class="w-5 h-5 text-slate-600" fill="currentColor" viewBox="0 0 24 24">
                        <circle cx="5" cy="12" r="2" />
                        <circle cx="12" cy="12" r="2" />
                        <circle cx="19" cy="12" r="2" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
@empty
    <div class="bg-white rounded-2xl border border-[#E5EAF2] p-8 text-center shadow-xs">
        <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-3">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
        </div>
        <h4 class="text-xs font-extrabold text-[#17243B]" x-text="lang === 'zh' ? '未找到相关数据' : 'Tidak Ada Data Dokumen'">Tidak Ada Data Dokumen</h4>
        <p class="text-[11px] text-slate-400 mt-1" x-text="lang === 'zh' ? '没有找到符合当前筛选条件的记录。' : 'Tidak ditemukan transaksi yang cocok dengan filter atau pencarian Anda.'">
            Tidak ditemukan transaksi yang cocok dengan filter atau pencarian Anda.
        </p>
        <button type="button" 
                @click="modalQuotationOpen = true" 
                class="mt-4 px-4 py-2 bg-[#142A57] hover:bg-[#0d1b38] text-white font-bold rounded-xl shadow-xs transition inline-flex items-center space-x-1.5 text-xs">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
            <span x-text="lang === 'zh' ? '新建报价单' : 'Buat Penawaran Baru'">Buat Penawaran Baru</span>
        </button>
    </div>
@endforelse
