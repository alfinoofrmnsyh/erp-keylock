{{-- ========================================================================= --}}
{{-- MOBILE CARD UI COMPONENT: FLEET & ASSETS MANAGEMENT                       --}}
{{-- Rendered exclusively on mobile screens (< md)                             --}}
{{-- Consistent with Penawaran & Order Mobile UX Design Pattern                --}}
{{-- ========================================================================= --}}

@forelse($armada as $unit)
    @php
        $activeItem = $unit->activeOrderItem;
        $activeOrder = $activeItem ? $activeItem->order : null;
        $customer = $activeOrder ? $activeOrder->contact : null;
        $latestService = $unit->latestService;
        
        // Calculate Akhir Sewa and Remaining Days
        $endDate = $activeOrder ? $activeOrder->effective_end_date : null;
        $remainingDays = null;
        $isOverdueRental = false;
        if ($endDate) {
            $endCarbon = \Carbon\Carbon::parse($endDate)->startOfDay();
            $nowCarbon = \Carbon\Carbon::today();
            $diff = $nowCarbon->diffInDays($endCarbon, false);
            $remainingDays = $diff;
            if ($diff < 0) {
                $isOverdueRental = true;
            }
        }

        // Check Service Overdue
        $isServiceOverdue = false;
        $isServiceDueSoon = false;
        if ($unit->next_service_due) {
            $dueCarbon = \Carbon\Carbon::parse($unit->next_service_due)->startOfDay();
            $nowCarbon = \Carbon\Carbon::today();
            $dueDiff = $nowCarbon->diffInDays($dueCarbon, false);
            if ($dueDiff < 0) {
                $isServiceOverdue = true;
            } elseif ($dueDiff <= 14) {
                $isServiceDueSoon = true;
            }
        }

        $searchHaystack = strtolower(implode(' ', [
            $unit->asset_code ?? '',
            $unit->serial_number ?? '',
            $unit->license_plate ?? '',
            $unit->item->name ?? '',
            $unit->item->brand ?? '',
            $unit->current_location ?? '',
            $customer->name ?? '',
            $activeOrder->order_number ?? ''
        ]));
    @endphp

    <div class="bg-white rounded-2xl border border-[#E5EAF2] p-3.5 shadow-[0_2px_8px_rgba(0,0,0,0.03)] space-y-3 relative transition hover:border-slate-300"
         x-show="matchesRow('{{ addslashes($searchHaystack) }}', '{{ $unit->status }}', '{{ $unit->ownership }}')">

        <!-- 1. BARIS HEADER CARD: KODE UNIT, OWNERSHIP & STATUS PILL -->
        <div class="flex items-center justify-between gap-2">
            <!-- Left: Ownership Badge + Asset Code -->
            <div class="flex items-center space-x-1.5 min-w-0">
                @if($unit->ownership === 'company')
                    <span class="bg-[#142A57] text-white text-[9px] font-black px-2 py-0.5 rounded-full shrink-0 tracking-wider">
                        OWN
                    </span>
                @else
                    <span class="bg-amber-500 text-white text-[9px] font-black px-2 py-0.5 rounded-full shrink-0 tracking-wider">
                        EXT
                    </span>
                @endif
                <span class="font-mono font-black text-sm text-slate-900 truncate">
                    {{ $unit->asset_code }}
                </span>
                @if($unit->license_plate)
                    <span class="text-[11px] font-mono text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded border border-slate-200 shrink-0">
                        {{ $unit->license_plate }}
                    </span>
                @endif
            </div>

            <!-- Right: Status Pill -->
            <div class="shrink-0">
                @if($unit->status === 'available')
                    <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        <span x-text="lang === 'zh' ? '空闲可用' : 'Siap Operasi'">Siap Operasi</span>
                    </span>
                @elseif($unit->status === 'rented')
                    <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                        <span x-text="lang === 'zh' ? '在租中' : 'Sedang Order'">Sedang Order</span>
                    </span>
                @elseif($unit->status === 'maintenance')
                    <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                        <span x-text="lang === 'zh' ? '维保中' : 'Servis'">Servis</span>
                    </span>
                @elseif($unit->status === 'broken')
                    <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                        <span x-text="lang === 'zh' ? '故障' : 'Rusak'">Rusak</span>
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                        {{ ucfirst($unit->status) }}
                    </span>
                @endif
            </div>
        </div>

        <!-- 2. NAMA ALAT & MODEL -->
        <div class="bg-slate-50/70 p-2.5 rounded-xl border border-slate-100">
            <h4 class="font-black text-xs text-slate-900 leading-tight">
                {{ $unit->item->name ?? 'Unit Alat Berat' }}
            </h4>
            <div class="flex items-center space-x-2 text-[11px] text-slate-500 mt-1">
                @if($unit->item?->brand)
                    <span class="font-semibold text-slate-700">{{ $unit->item->brand }}</span>
                @endif
                @if($unit->year)
                    <span>• Thn {{ $unit->year }}</span>
                @endif
                @if($unit->serial_number)
                    <span>• SN: <span class="font-mono font-medium">{{ $unit->serial_number }}</span></span>
                @endif
            </div>
        </div>

        <!-- 3. GRID INFO SPESIFIKASI: HM & LOKASI -->
        <div class="grid grid-cols-2 gap-2 text-xs">
            <div class="bg-white p-2 rounded-xl border border-[#E5EAF2]">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Hour Meter (HM)</span>
                <span class="font-mono font-black text-slate-900 text-xs mt-0.5 block">
                    {{ number_format($unit->hour_meter ?? 0, 1) }} Jam
                </span>
            </div>
            <div class="bg-white p-2 rounded-xl border border-[#E5EAF2]">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Lokasi Unit</span>
                <span class="font-semibold text-slate-800 text-xs mt-0.5 truncate block" title="{{ $unit->current_location ?: 'Pool Pusat Keylock' }}">
                    {{ $unit->current_location ?: 'Pool Pusat Keylock' }}
                </span>
            </div>
        </div>

        <!-- 4. INFO SEWA PELANGGAN (JIKA SEDANG ORDER) -->
        @if($unit->status === 'rented' && $activeOrder)
            <div class="bg-blue-50/60 p-2.5 rounded-xl border border-blue-200/70 space-y-1.5 text-xs">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-1 min-w-0">
                        <svg class="w-3.5 h-3.5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        <span class="font-bold text-slate-900 truncate max-w-[170px]" title="{{ $customer->name ?? 'Pelanggan' }}">
                            {{ $customer->name ?? 'Pelanggan' }}
                        </span>
                    </div>
                    <a href="{{ route('erp.orders.index', ['search' => $activeOrder->order_number]) }}" 
                       class="font-mono font-bold text-[10px] text-blue-700 bg-blue-100 hover:bg-blue-200 px-2 py-0.5 rounded transition">
                        #{{ $activeOrder->order_number }}
                    </a>
                </div>

                <div class="grid grid-cols-2 gap-2 pt-1 border-t border-blue-200/50 text-[11px]">
                    <div>
                        <span class="text-slate-400 text-[10px] block">Mulai Sewa:</span>
                        <span class="font-semibold text-slate-700">{{ $activeOrder->rental_start_date ? $activeOrder->rental_start_date->format('d M Y') : '-' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 text-[10px] block">Akhir Sewa:</span>
                        <span class="font-black {{ $isOverdueRental ? 'text-rose-600' : 'text-slate-900' }}">
                            {{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d M Y') : '-' }}
                        </span>
                    </div>
                </div>

                @if($isOverdueRental)
                    <div class="pt-1 text-[10px] font-bold text-rose-600 flex items-center space-x-1">
                        <svg class="w-3 h-3 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <span>Sewa Lewat Jatuh Tempo (Overdue)</span>
                    </div>
                @endif
            </div>
        @endif

        <!-- 5. STATUS SERVIS UNIT -->
        @if($isServiceOverdue || $isServiceDueSoon || $unit->next_service_due)
            <div class="flex items-center justify-between text-[11px] px-2.5 py-1.5 rounded-xl {{ $isServiceOverdue ? 'bg-rose-50 border border-rose-200 text-rose-700' : ($isServiceDueSoon ? 'bg-amber-50 border border-amber-200 text-amber-800' : 'bg-slate-50 border border-slate-200 text-slate-600') }}">
                <span class="font-semibold">Jadwal Servis:</span>
                <span class="font-bold">
                    {{ \Carbon\Carbon::parse($unit->next_service_due)->format('d M Y') }}
                    @if($isServiceOverdue)
                        (Overdue)
                    @elseif($isServiceDueSoon)
                        (Segera)
                    @endif
                </span>
            </div>
        @endif

        <!-- 6. BARIS AKSI BUTTONS -->
        <div class="flex items-center justify-between gap-2 pt-2 border-t border-[#E5EAF2]">
            <div class="flex items-center space-x-1.5 flex-1">
                <!-- Detail Button -->
                <button type="button" 
                        @click="openDetailsDrawer({{ $unit->id }})" 
                        class="flex-1 py-2 px-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs flex items-center justify-center space-x-1 transition shadow-2xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    <span>Detail</span>
                </button>

                <!-- Catat Servis Button -->
                <button type="button" 
                        @click="openServiceModal({{ json_encode($unit) }})" 
                        class="py-2 px-3 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 font-bold text-xs flex items-center justify-center space-x-1 transition shadow-2xs">
                    <svg class="w-3.5 h-3.5 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span>Servis</span>
                </button>

                <!-- Edit Button -->
                <button type="button" 
                        @click="openEditModal({{ json_encode($unit) }})" 
                        class="p-2 rounded-xl bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 transition shadow-2xs" 
                        title="Edit Data Unit">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                </button>
            </div>

            <!-- Unit Kembali Button (Jika Status Rented) -->
            @if($unit->status === 'rented')
                <button type="button" 
                        @click="openReturnModal({{ json_encode($unit) }})" 
                        class="py-2 px-3 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-black text-xs flex items-center space-x-1 shadow-xs transition active:scale-95 shrink-0"
                        title="Unit Kembali dari Sewa">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                    <span>Kembali</span>
                </button>
            @endif
        </div>
    </div>
@empty
    <div class="bg-white rounded-2xl border border-slate-200 p-8 text-center space-y-3">
        <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 mx-auto flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
        </div>
        <h4 class="font-black text-slate-800 text-sm">Tidak ada data armada yang cocok</h4>
        <p class="text-xs text-slate-500">Coba ubah filter status atau kata kunci pencarian Anda.</p>
    </div>
@endforelse
