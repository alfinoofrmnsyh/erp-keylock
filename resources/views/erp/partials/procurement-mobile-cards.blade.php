{{-- ========================================================================= --}}
{{-- MOBILE CARD UI COMPONENT: PROCUREMENT & VENDOR (PO / SUB-RENT)            --}}
{{-- Rendered exclusively on mobile screens (< md)                             --}}
{{-- Consistent with Penawaran & Order Mobile UX Design Pattern                --}}
{{-- ========================================================================= --}}

@forelse($purchaseOrders as $index => $po)
    @php
        $itemNamesStr = strtolower($po->items->pluck('description')->join(' '));
        $vendorBill = $po->invoices ? $po->invoices->where('invoice_type', 'vendor_bill')->first() : null;
        $hasBill = ($vendorBill !== null) || in_array($po->invoice_status, ['invoiced', 'partial', 'paid']);
        $items = $po->items ?? collect();
        $firstItem = $items->first();
        $itemCount = $items->count();
        $isCancelled = ($po->status === 'cancelled');

        // Helper label type
        $typeLabels = [
            'sub_rental'    => ['label' => 'SUB-RENT', 'bg' => 'bg-amber-500'],
            'purchase_unit' => ['label' => 'BELI',     'bg' => 'bg-purple-600'],
            'service'       => ['label' => 'JASA',     'bg' => 'bg-blue-600'],
            'operational'   => ['label' => 'OPEX',     'bg' => 'bg-orange-500'],
        ];
        $typeConfig = $typeLabels[$po->procurement_type] ?? ['label' => 'PO', 'bg' => 'bg-slate-700'];
    @endphp

    <div class="bg-white rounded-2xl border border-[#E5EAF2] p-3.5 shadow-[0_2px_8px_rgba(0,0,0,0.03)] space-y-3 relative transition hover:border-slate-300 {{ $isCancelled ? 'bg-slate-50/70 border-dashed' : '' }}"
         x-show="matchesFilter('{{ strtolower($po->order_number) }}', '{{ strtolower($po->contact->name ?? '') }}', '{{ strtolower($po->parent->order_number ?? '') }}', '{{ addslashes($itemNamesStr) }}', '{{ $po->status }}', '{{ strtolower($vendorBill->vendor_ref_no ?? '') }}', '{{ strtolower($vendorBill->invoice_number ?? '') }}')">

        <!-- 1. BARIS IDENTITAS PO, TIPE & STATUS PILL -->
        <div class="flex items-center justify-between gap-2">
            <!-- Left: Type Badge + PO Number + Date -->
            <div class="flex items-center space-x-1.5 min-w-0">
                <span class="{{ $typeConfig['bg'] }} text-white text-[9px] font-black px-2 py-0.5 rounded-full shrink-0 tracking-wider">
                    {{ $typeConfig['label'] }}
                </span>
                <span class="font-mono font-black text-sm text-slate-900 truncate">
                    {{ $po->order_number }}
                </span>
                @if($po->order_date)
                    <span class="text-[10px] text-slate-400 shrink-0 font-medium">
                        {{ $po->order_date->format('d/m/y') }}
                    </span>
                @endif
            </div>

            <!-- Right: Status PO Pill -->
            <div class="shrink-0">
                @if($po->status === 'completed')
                    <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        <span x-text="lang === 'zh' ? '已完成' : 'Selesai'">Selesai</span>
                    </span>
                @elseif($po->status === 'cancelled')
                    <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200"
                          @if($po->cancel_reason) title="Alasan: {{ $po->cancel_reason }}" @endif>
                        <svg class="w-3 h-3 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                        <span x-text="lang === 'zh' ? '已取消' : 'Batal'">Batal</span>
                    </span>
                @elseif($po->status === 'approved')
                    <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                        <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span x-text="lang === 'zh' ? '执行中' : 'Disetujui / Aktif'">Disetujui / Aktif</span>
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                        {{ ucfirst($po->status) }}
                    </span>
                @endif
            </div>
        </div>

        <!-- 2. VENDOR & PROYEK PELANGGAN (SO/RO) -->
        <div class="bg-slate-50/70 p-2.5 rounded-xl border border-slate-100 space-y-1.5">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-1.5 min-w-0">
                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    <span class="font-black text-xs text-slate-900 truncate">
                        {{ $po->contact->name ?? 'Vendor Mitra' }}
                    </span>
                </div>
                @if($po->contact?->phone)
                    <span class="text-[10px] font-mono text-slate-500">
                        {{ $po->contact->phone }}
                    </span>
                @endif
            </div>

            <!-- Proyek Customer SO/RO -->
            @if($po->parent)
                <div class="flex items-center space-x-1.5 text-[11px] pt-1 border-t border-slate-200/60">
                    <span class="text-slate-500">Proyek SO/RO:</span>
                    <a href="{{ route('erp.orders.index', ['search' => $po->parent->order_number]) }}" 
                       class="font-mono font-bold text-blue-700 bg-blue-50 hover:bg-blue-100 px-1.5 py-0.5 rounded border border-blue-200 transition">
                        #{{ $po->parent->order_number }}
                    </a>
                </div>
            @else
                <div class="text-[11px] text-slate-400 italic">
                    Pengadaan Operasional / Non Order Khusus
                </div>
            @endif
        </div>

        <!-- 3. DETAIL BARANG / UNIT YANG DIADAKAN -->
        @if($itemCount > 0)
            <div class="text-xs space-y-1">
                <div class="flex items-center justify-between text-slate-500 text-[11px]">
                    <span class="font-bold uppercase tracking-wider text-[10px] text-slate-400">Rincian Barang / Jasa ({{ $itemCount }} Item)</span>
                    @if($firstItem && $firstItem->quantity)
                        <span class="font-mono font-semibold">{{ (float)$firstItem->quantity }} {{ $firstItem->unit ?: 'Unit' }}</span>
                    @endif
                </div>
                @if($firstItem)
                    <div class="font-semibold text-slate-800 text-xs truncate">
                        {{ $firstItem->description }}
                    </div>
                @endif
            </div>
        @endif

        <!-- 4. GRID METRIK KEUANGAN: NILAI TOTAL HPP & STATUS BILL -->
        <div class="grid grid-cols-2 gap-2 text-xs">
            <!-- Nilai Total HPP -->
            <div class="bg-white p-2.5 rounded-xl border border-[#E5EAF2]">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Nilai Total HPP</span>
                <span class="font-mono font-black text-slate-900 text-sm mt-0.5 block">
                    Rp {{ number_format($po->total_amount, 0, ',', '.') }}
                </span>
            </div>

            <!-- Status Tagihan Vendor (Bill) -->
            <div class="bg-white p-2.5 rounded-xl border border-[#E5EAF2]">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Status Bill</span>
                <div class="mt-0.5">
                    @if($hasBill && $vendorBill)
                        <div class="flex items-center space-x-1">
                            @if($vendorBill->payment_status === 'paid')
                                <span class="inline-flex items-center space-x-1 px-1.5 py-0.5 rounded text-[10px] font-black bg-emerald-100 text-emerald-800">
                                    <span>Lunas</span>
                                </span>
                            @elseif($vendorBill->payment_status === 'partial')
                                <span class="inline-flex items-center space-x-1 px-1.5 py-0.5 rounded text-[10px] font-black bg-amber-100 text-amber-800">
                                    <span>Sebagian</span>
                                </span>
                            @else
                                <span class="inline-flex items-center space-x-1 px-1.5 py-0.5 rounded text-[10px] font-black bg-rose-100 text-rose-800">
                                    <span>Belum Bayar</span>
                                </span>
                            @endif
                            <span class="font-mono text-[10px] text-purple-700 font-bold truncate">
                                {{ $vendorBill->invoice_number }}
                            </span>
                        </div>
                    @else
                        <span class="text-[11px] text-slate-400 font-medium">Belum Ada Bill</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- 5. BARIS TOMBOL AKSI -->
        <div class="flex items-center justify-between gap-2 pt-2 border-t border-[#E5EAF2]">
            <div class="flex items-center space-x-1.5 flex-1">
                <!-- Detail PO Drawer Button -->
                <button type="button" 
                        @click="openDetailsDrawer({{ json_encode($po) }})" 
                        class="flex-1 py-2 px-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs flex items-center justify-center space-x-1 transition shadow-2xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    <span>Detail</span>
                </button>

                <!-- Cetak PDF PO -->
                <a href="{{ route('erp.procurement.pdf', $po->id) }}" 
                   target="_blank" 
                   class="py-2 px-3 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 font-bold text-xs flex items-center justify-center space-x-1 transition shadow-2xs"
                   title="Cetak Dokumen PO PDF">
                    <svg class="w-3.5 h-3.5 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    <span>PDF</span>
                </a>
            </div>

            <!-- Generate Bill Button (Jika belum ada Bill dan belum Cancel) -->
            @if(!$hasBill && $po->status !== 'cancelled')
                <button type="button" 
                        @click="openBillModal({{ json_encode($po) }})" 
                        class="py-2 px-3 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-black text-xs flex items-center space-x-1 shadow-xs transition active:scale-95 shrink-0"
                        title="Buat Tagihan Vendor (Bill)">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z"></path></svg>
                    <span>Tagihan</span>
                </button>
            @endif

            <!-- Batalkan PO (jika belum cancel) -->
            @if($po->status !== 'cancelled')
                <button type="button" 
                        @click="openCancelPoModal({{ json_encode($po) }})" 
                        class="p-2 rounded-xl bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 transition shadow-2xs shrink-0"
                        title="Batalkan PO">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            @endif
        </div>
    </div>
@empty
    <div class="bg-white rounded-2xl border border-slate-200 p-8 text-center space-y-3">
        <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 mx-auto flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
        </div>
        <h4 class="font-black text-slate-800 text-sm">Tidak ada data pengadaan yang cocok</h4>
        <p class="text-xs text-slate-500">Coba ubah filter atau kata kunci pencarian Anda.</p>
    </div>
@endforelse
