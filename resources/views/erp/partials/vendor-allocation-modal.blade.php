<!-- MODAL ALOKASI VENDOR / SUB-RENT (REVISED: CLEAN, SPACIOUS, PREMIUM BALANCED LAYOUT) -->
<div x-show="modalAllocateOpen" 
     x-cloak
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-5 bg-slate-900/60 backdrop-blur-xs overflow-y-auto"
     @click.self="modalAllocateOpen = false" 
     style="display: none;">

    <div class="bg-white w-full max-w-5xl rounded-3xl shadow-2xl border border-slate-200 overflow-hidden my-auto animate-in zoom-in-95 duration-150 flex flex-col max-h-[92vh]">
        
        <!-- 1. MODAL HEADER (FIXED TOP) -->
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between shrink-0">
            <div class="flex items-center space-x-3.5">
                <div class="w-10 h-10 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center font-black shrink-0 shadow-2xs">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm sm:text-base font-black text-slate-900 flex items-center space-x-2.5">
                        <span x-text="lang === 'zh' ? '创建供应商采购订单 / 转租 (Buat PO)' : 'Buat PO Vendor / Sub-Rent Unit'">Buat PO Vendor / Sub-Rent Unit</span>
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-mono font-bold bg-amber-100 text-amber-900 border border-amber-200/80" x-text="allocateForm.order_number || ''"></span>
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5" x-text="lang === 'zh' ? '按交易产品明细分别分配外部供应商，自动生成采购订单并计算真实毛利。' : 'Kaitkan vendor per produk transaksi untuk penerbitan PO dan perhitungan laba kotor riil.'">
                        Kaitkan vendor per produk transaksi untuk penerbitan PO dan perhitungan laba kotor riil.
                    </p>
                </div>
            </div>
            <button type="button" @click="modalAllocateOpen = false" class="text-slate-400 hover:text-slate-600 p-2 rounded-xl hover:bg-slate-200/60 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- 2. SCROLLABLE FORM BODY -->
        <form id="vendorAllocateForm" @submit.prevent="submitAllocateVendor()" class="flex-1 overflow-y-auto p-5 sm:p-6 space-y-5 text-xs">
            
            <!-- ORDER INFO STRIP: CLEAN 4-ITEM BALANCED GRID -->
            <div class="bg-slate-50/90 p-3.5 sm:p-4 rounded-2xl border border-slate-200/90 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 shadow-2xs">
                <!-- 1. Nomor Pesanan -->
                <div class="bg-white p-2.5 px-3 rounded-xl border border-slate-200 flex items-center space-x-2.5">
                    <div class="w-8 h-8 rounded-lg bg-slate-900 text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-2xs">
                        #
                    </div>
                    <div class="min-w-0 flex-1">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block" x-text="lang === 'zh' ? '订单编号' : 'No. Transaksi'">No. Transaksi</span>
                        <span class="font-mono font-black text-slate-900 text-xs truncate block" x-text="allocateForm.order_number || '-'"></span>
                    </div>
                </div>

                <!-- 2. Klien / Customer -->
                <div class="bg-white p-2.5 px-3 rounded-xl border border-slate-200 flex items-center space-x-2.5">
                    <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xs shrink-0 shadow-2xs">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block" x-text="lang === 'zh' ? '客户名称' : 'Pelanggan / Klien'">Pelanggan / Klien</span>
                        <span class="font-extrabold text-slate-900 text-xs truncate block" :title="allocateForm.customer_name" x-text="allocateForm.customer_name || '-'"></span>
                    </div>
                </div>

                <!-- 3. Omset / Nilai Transaksi -->
                <div class="bg-white p-2.5 px-3 rounded-xl border border-slate-200 flex items-center space-x-2.5">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center font-black text-[11px] shrink-0 shadow-2xs">
                        Rp
                    </div>
                    <div class="min-w-0 flex-1">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block" x-text="lang === 'zh' ? '客户合同总额' : 'Nilai Omset Klien'">Nilai Omset Klien</span>
                        <span class="font-mono font-black text-emerald-600 text-xs truncate block" x-text="'Rp ' + Number(allocateForm.order_revenue || 0).toLocaleString('id-ID')"></span>
                    </div>
                </div>

                <!-- 4. Tanggal PO Vendor -->
                <div class="bg-amber-50/90 p-2.5 px-3 rounded-xl border border-amber-300 flex items-center space-x-2.5">
                    <div class="w-8 h-8 rounded-lg bg-amber-400 text-slate-950 flex items-center justify-center font-bold text-xs shrink-0 shadow-2xs">
                        <svg class="w-4 h-4 text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <label class="text-[10px] font-black uppercase tracking-wider text-amber-900 block" x-text="lang === 'zh' ? '采购订单日期 *' : 'Tanggal Terbit PO *'">Tanggal Terbit PO *</label>
                        <input type="date" 
                               x-model="allocateForm.po_date" 
                               required 
                               class="w-full bg-white border border-amber-300 rounded-lg px-2 py-0.5 text-slate-900 font-bold text-xs focus:ring-2 focus:ring-amber-400 focus:outline-none shadow-2xs">
                    </div>
                </div>
            </div>

            <!-- SECTION HEADER PER-PRODUCT -->
            <div class="space-y-3.5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pt-1">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-2.5 h-4.5 bg-amber-500 rounded-full"></div>
                        <h4 class="font-black text-slate-900 text-xs sm:text-sm uppercase tracking-wider flex items-center space-x-2" x-text="lang === 'zh' ? '产品采购明细清单' : 'Daftar Produk PO Vendor'">
                            <span>Daftar Produk PO Vendor</span>
                        </h4>
                        <span class="px-2.5 py-0.5 rounded-full bg-slate-200 text-slate-700 font-mono font-bold text-[10px]" x-text="(allocateForm.products?.length || 0) + ' Produk'"></span>
                    </div>
                    <div class="flex items-center space-x-1.5 text-[11px] text-slate-500 bg-slate-100 px-3 py-1 rounded-full border border-slate-200/80 self-start sm:self-auto">
                        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span x-text="lang === 'zh' ? '未指定供应商的产品将作为公司自有设备处理' : 'Kosongkan vendor jika unit dipenuhi dari internal Keylock.'">
                            Kosongkan vendor jika unit dipenuhi dari internal Keylock.
                        </span>
                    </div>
                </div>

                <!-- Products Loop Cards -->
                <div class="space-y-4">
                    <template x-for="(prod, idx) in allocateForm.products" :key="idx">
                        <div class="rounded-2xl border transition-all p-4 sm:p-5 space-y-4 shadow-xs"
                             :class="prod.vendor_id ? 'bg-amber-50/30 border-amber-300 ring-1 ring-amber-300/40' : 'bg-white border-slate-200/90'">
                            
                            <!-- Product Title & Details Banner -->
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3.5 border-b border-slate-200/80 gap-2.5">
                                <div class="flex items-center space-x-3">
                                    <span class="w-7 h-7 rounded-xl bg-slate-900 text-white font-mono font-black text-xs flex items-center justify-center shrink-0 shadow-xs" x-text="idx + 1"></span>
                                    <div>
                                        <h4 class="font-black text-slate-900 text-sm sm:text-base tracking-tight" x-text="prod.item_name"></h4>
                                        <div class="flex flex-wrap items-center gap-2 text-xs text-slate-500 mt-0.5">
                                            <span class="px-2 py-0.5 rounded-md bg-slate-100 border border-slate-200 font-bold text-slate-700 text-[11px]" x-text="'Qty: ' + prod.quantity + ' ' + (prod.unit || 'Unit')"></span>
                                            <span class="px-2 py-0.5 rounded-md bg-blue-50 border border-blue-200 text-blue-900 font-semibold text-[11px]">
                                                Nilai Klien: <strong class="font-mono font-bold" x-text="'Rp ' + Number(prod.line_total || 0).toLocaleString('id-ID')"></strong>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Status Badge on Right -->
                                <div class="text-right shrink-0">
                                    <template x-if="prod.vendor_id">
                                        <div class="inline-flex items-center space-x-2 px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-900 border border-emerald-300 text-xs font-mono font-bold shadow-2xs">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                            <span>HPP Vendor: <strong class="font-black text-emerald-950" x-text="'Rp ' + calcProductVendorCost(prod).total.toLocaleString('id-ID')"></strong></span>
                                        </div>
                                    </template>
                                    <template x-if="!prod.vendor_id">
                                        <div class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-xl bg-slate-50 border border-dashed border-slate-300 text-slate-500 text-xs font-semibold">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                            <span x-text="lang === 'zh' ? '自有设备 (无采购单)' : 'Unit Internal Sendiri (Tanpa PO)'">Unit Internal Sendiri (Tanpa PO)</span>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Product Input Fields Form Grid -->
                            <div class="space-y-3.5">
                                
                                <!-- ROW 1: Tipe Pengadaan & Mulai Sewa / Durasi (3 Balanced Columns) -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                    <!-- 1. Tipe Pengadaan -->
                                    <div>
                                        <label class="block font-bold text-slate-700 mb-1.5 text-xs" x-text="lang === 'zh' ? '采购类型 *' : 'Tipe Pengadaan *'">Tipe Pengadaan *</label>
                                        <select x-model="prod.procurement_type" @change="recalcAllocateMargin()" class="w-full h-10 bg-white border border-slate-300 rounded-xl px-3 text-slate-900 font-bold text-xs focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20 focus:outline-none shadow-2xs">
                                            <option value="sub_rental">Sewa (Sub-Rent Unit)</option>
                                            <option value="purchase_unit">Pembelian (Beli Unit / Barang)</option>
                                            <option value="service">Jasa (Servis / Operator / Layanan)</option>
                                        </select>
                                    </div>

                                    <!-- 2. Mulai Sewa (jika Sewa) -->
                                    <template x-if="prod.procurement_type === 'sub_rental'">
                                        <div>
                                            <label class="block font-bold text-slate-700 mb-1.5 text-xs flex items-center justify-between">
                                                <span>Mulai Sewa (Rental Start)</span>
                                                <span class="text-rose-500 font-bold" x-show="prod.vendor_id">*</span>
                                            </label>
                                            <input type="date" 
                                                   x-model="prod.rental_start_date" 
                                                   :required="prod.vendor_id && prod.procurement_type === 'sub_rental'" 
                                                   class="w-full h-10 bg-white border border-slate-300 focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20 rounded-xl px-3 text-slate-900 font-bold text-xs focus:outline-none shadow-2xs">
                                        </div>
                                    </template>

                                    <!-- 3. Durasi Sewa (jika Sewa) -->
                                    <template x-if="prod.procurement_type === 'sub_rental'">
                                        <div>
                                            <label class="block font-bold text-slate-700 mb-1.5 text-xs" x-text="lang === 'zh' ? '租赁时长' : 'Durasi Sewa'">Durasi Sewa</label>
                                            <div class="flex items-center space-x-2">
                                                <input type="number" 
                                                       min="1" 
                                                       x-model.number="prod.duration" 
                                                       @input="recalcAllocateMargin()" 
                                                       class="w-20 h-10 bg-white border border-slate-300 rounded-xl px-2.5 text-center font-bold text-slate-900 text-xs focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20 focus:outline-none shadow-2xs [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                                                <select x-model="prod.duration_unit" class="flex-1 h-10 bg-white border border-slate-300 rounded-xl px-3 text-xs font-bold text-slate-800 focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20 focus:outline-none shadow-2xs">
                                                    <option value="Bulan">Bulan</option>
                                                    <option value="Hari">Hari</option>
                                                    <option value="Minggu">Minggu</option>
                                                    <option value="Tahun">Tahun</option>
                                                </select>
                                            </div>
                                        </div>
                                    </template>

                                    <!-- Clean Badge jika Pembelian atau Jasa -->
                                    <template x-if="prod.procurement_type === 'purchase_unit' || prod.procurement_type === 'service'">
                                        <div class="md:col-span-2">
                                            <label class="block font-bold text-slate-700 mb-1.5 text-xs" x-text="lang === 'zh' ? '采购模式说明' : 'Keterangan Model Transaksi'">Keterangan Model Transaksi</label>
                                            <div class="h-10 bg-blue-50/80 border border-blue-200/90 rounded-xl px-3.5 flex items-center space-x-2 text-blue-900 text-xs font-semibold shadow-2xs">
                                                <span class="w-2 h-2 rounded-full bg-blue-500 shrink-0"></span>
                                                <span class="font-bold" x-text="prod.procurement_type === 'purchase_unit' ? 'Pengadaan Beli Putus / Unit' : 'Pengadaan Jasa / Layanan Vendor'">Pengadaan Beli Putus / Unit</span>
                                                <span class="text-blue-300 hidden sm:inline">•</span>
                                                <span class="text-[11px] text-blue-700 font-normal hidden sm:inline">Dihitung 1x tanpa durasi sewa bulanan</span>
                                            </div>
                                        </div>
                                    </template>
                                </div>

                                <!-- ROW 2: Sumber Unit & Alokasi (Armada Internal vs Vendor Mitra) -->
                                <div class="p-3.5 bg-slate-50/90 rounded-2xl border border-slate-200/90 space-y-3">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                                        <span class="font-black text-slate-800 text-xs uppercase tracking-wider flex items-center space-x-1.5">
                                            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1"></path></svg>
                                            <span x-text="lang === 'zh' ? '设备来源分配 (自有设备或供应商)' : 'Sumber Unit & Alokasi'">Sumber Unit & Alokasi</span>
                                        </span>
                                        <span class="text-[11px] text-slate-500">
                                            Pilih armada internal jika unit milik sendiri, atau pilih vendor mitra untuk pengadaan luar.
                                        </span>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <!-- 1. Armada Internal Keylock -->
                                        <div class="p-2.5 rounded-xl border transition" :class="prod.asset_id ? 'bg-amber-50/70 border-amber-300 ring-1 ring-amber-300' : 'bg-white border-slate-200'">
                                            <label class="block font-bold text-slate-700 mb-1 text-xs flex items-center justify-between">
                                                <span class="flex items-center space-x-1.5">
                                                    <span class="w-2 h-2 rounded-full" :class="prod.asset_id ? 'bg-amber-500' : 'bg-slate-300'"></span>
                                                    <span x-text="lang === 'zh' ? '自有车队/设备 (Armada Internal)' : 'Armada Internal Keylock (Unit Sendiri)'">Armada Internal Keylock (Unit Sendiri)</span>
                                                </span>
                                                <template x-if="prod.asset_id">
                                                    <span class="text-[10px] text-amber-800 font-bold bg-amber-100 px-2 py-0.5 rounded-full border border-amber-200">Sedang Dialokasikan</span>
                                                </template>
                                            </label>
                                            <select x-model="prod.asset_id" @change="if(prod.asset_id) { prod.vendor_id = ''; prod.unit_cost = 0; } recalcAllocateMargin()" class="w-full h-10 bg-white border border-slate-300 rounded-xl px-3 text-slate-900 font-semibold text-xs focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20 focus:outline-none shadow-2xs">
                                                <option value="">-- Tidak Menggunakan Armada Internal --</option>
                                                @if(isset($availableAssets) && count($availableAssets) > 0)
                                                    @foreach($availableAssets as $ast)
                                                        <option value="{{ $ast->id }}">
                                                            [{{ $ast->asset_code }}] {{ $ast->item->name ?? 'Unit' }} - {{ $ast->license_plate ?: ($ast->serial_number ?: 'Tanpa Plat') }} ({{ $ast->status === 'available' ? 'Tersedia' : ($ast->status === 'rented' ? 'Sedang Disewa' : ucfirst($ast->status)) }})
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            <p class="text-[10px] text-amber-700 mt-1 font-semibold" x-show="prod.asset_id">
                                                Status unit ini akan otomatis menjadi <strong>"Sedang dalam order"</strong>.
                                            </p>
                                        </div>

                                        <!-- 2. Vendor Mitra Eksternal -->
                                        <div class="p-2.5 rounded-xl border transition" :class="prod.vendor_id ? 'bg-blue-50/70 border-blue-300 ring-1 ring-blue-300' : 'bg-white border-slate-200'">
                                            <label class="block font-bold text-slate-700 mb-1 text-xs flex items-center justify-between">
                                                <span class="flex items-center space-x-1.5">
                                                    <span class="w-2 h-2 rounded-full" :class="prod.vendor_id ? 'bg-blue-500' : 'bg-slate-300'"></span>
                                                    <span x-text="lang === 'zh' ? '外部供应商 (Vendor Mitra)' : 'Vendor Mitra Eksternal (Sub-Rent / Beli)'">Vendor Mitra Eksternal (Sub-Rent / Beli)</span>
                                                </span>
                                                <template x-if="prod.vendor_id">
                                                    <span class="text-[10px] text-blue-800 font-bold bg-blue-100 px-2 py-0.5 rounded-full border border-blue-200">Terbit PO</span>
                                                </template>
                                            </label>
                                            <select x-model="prod.vendor_id" @change="if(prod.vendor_id) { prod.asset_id = ''; } recalcAllocateMargin()" class="w-full h-10 bg-white border border-slate-300 rounded-xl px-3 text-slate-900 font-semibold text-xs focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20 focus:outline-none shadow-2xs">
                                                <option value="">-- Kosongkan jika Pakai Armada Internal --</option>
                                                @foreach($unitVendors ?? $vendors as $ven)
                                                    @php
                                                        $catLower = strtolower($ven->category ?? '');
                                                        $nameLower = strtolower($ven->name ?? '');
                                                        $isPengiriman = str_contains($catLower, 'pengiriman') || str_contains($catLower, 'ekspedisi') || str_contains($catLower, 'logistik') || str_contains($nameLower, 'ekspedisi') || str_contains($nameLower, 'lalamove');
                                                    @endphp
                                                    @if(!$isPengiriman)
                                                        <option value="{{ $ven->id }}">{{ $ven->name }} ({{ $ven->category ?: 'Mitra Unit' }})</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                            <p class="text-[10px] text-blue-700 mt-1 font-semibold" x-show="prod.vendor_id">
                                                PO pengadaan vendor otomatis diterbitkan.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- ROW 3: Biaya HPP Vendor, Towing & TOP (3 Balanced Columns) -->
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">

                                    <!-- 5. Harga Vendor (Rp) with Integrated PPN 11% Toggle -->
                                    <div>
                                        <div class="flex items-center justify-between mb-1.5">
                                            <label class="font-bold text-slate-700 text-xs truncate">
                                                <span x-text="prod.procurement_type === 'sub_rental' ? 'Harga Sewa Vendor' : 'Harga Beli Vendor'">Harga Sewa Vendor</span>
                                                <span class="text-rose-500 font-bold" x-show="prod.vendor_id">*</span>
                                            </label>
                                            <!-- PPN 11% Integrated Checkbox Badge -->
                                            <label class="inline-flex items-center space-x-1.5 cursor-pointer bg-white px-2 py-0.5 rounded-md border border-slate-200 hover:border-amber-400 transition select-none shadow-2xs">
                                                <input type="checkbox" 
                                                       x-model="prod.include_ppn" 
                                                       @change="recalcAllocateMargin()" 
                                                       class="rounded text-amber-600 focus:ring-amber-500 w-3.5 h-3.5">
                                                <span class="text-[10px] font-bold" :class="prod.include_ppn ? 'text-amber-800' : 'text-slate-500'">+ PPN 11%</span>
                                            </label>
                                        </div>
                                        <div class="relative">
                                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-xs select-none">Rp</span>
                                            <input type="text"
                                                   inputmode="numeric"
                                                   data-rupiah
                                                   :value="formatNumberWithDots(prod.unit_cost)"
                                                   @input="prod.unit_cost = parseNumberFromDots($event.target.value); recalcAllocateMargin()"
                                                   placeholder="0"
                                                   class="w-full h-10 bg-white border border-slate-300 rounded-xl pl-9 pr-3 text-right font-mono font-bold text-slate-900 text-xs focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20 focus:outline-none shadow-2xs">
                                        </div>
                                    </div>

                                    <!-- 6. Biaya Kirim / Towing -->
                                    <div>
                                        <label class="block font-bold text-slate-700 mb-1.5 text-xs" x-text="lang === 'zh' ? '运费 / 拖运费 (Rp)' : 'Biaya Kirim / Towing (Rp)'">Biaya Kirim / Towing (Rp)</label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-xs select-none">Rp</span>
                                            <input type="text"
                                                   inputmode="numeric"
                                                   data-rupiah
                                                   :value="formatNumberWithDots(prod.shipping_fee)"
                                                   @input="prod.shipping_fee = parseNumberFromDots($event.target.value); recalcAllocateMargin()"
                                                   placeholder="0"
                                                   class="w-full h-10 bg-white border border-slate-300 rounded-xl pl-9 pr-3 text-right font-mono font-bold text-slate-900 text-xs focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20 focus:outline-none shadow-2xs">
                                        </div>
                                    </div>

                                    <!-- 7. TOP (Term of Payment) -->
                                    <div>
                                        <label class="block font-bold text-slate-700 mb-1.5 text-xs" x-text="lang === 'zh' ? '付款账期 (TOP)' : 'TOP (Term of Payment)'">TOP (Term of Payment)</label>
                                        <select x-model="prod.payment_term" class="w-full h-10 bg-white border border-slate-300 rounded-xl px-3 text-slate-900 font-semibold text-xs focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20 focus:outline-none shadow-2xs">
                                            <option value="Net 30 Days">Net 30 Days (Standard)</option>
                                            <option value="Net 14 Days">Net 14 Days</option>
                                            <option value="Net 7 Days">Net 7 Days</option>
                                            <option value="Cash / COD">Cash / COD</option>
                                            <option value="DP 50% Pelunasan">DP 50% Pelunasan</option>
                                            <option value="Net 60 Days">Net 60 Days</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- ROW 3: Remarks / Catatan Vendor (Full Width) -->
                                <div>
                                    <label class="block font-bold text-slate-700 mb-1.5 text-xs flex items-center justify-between">
                                        <span x-text="lang === 'zh' ? '备注说明 (Remaks)' : 'Remaks / Catatan Vendor'">Remaks / Catatan Vendor</span>
                                        <span class="text-[10px] text-slate-400 font-normal">Opsional: nomor seri sasis unit, attachment, garansi, dsb.</span>
                                    </label>
                                    <input type="text" 
                                           x-model="prod.notes" 
                                           placeholder="Contoh: Unit nomor sasis FD30-..., include garansi service dan teknisi..." 
                                           class="w-full h-10 bg-white border border-slate-300 rounded-xl px-3.5 text-slate-900 text-xs focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20 focus:outline-none shadow-2xs">
                                </div>
                            </div>

                            <!-- Live Product Cost Breakdown Footnote (Only if vendor is filled) -->
                            <template x-if="prod.vendor_id && prod.unit_cost > 0">
                                <div class="flex flex-wrap items-center justify-between text-xs bg-amber-50/90 border border-amber-200/90 rounded-xl px-4 py-2.5 text-slate-700 font-mono gap-2 shadow-2xs">
                                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1">
                                        <span class="text-slate-600">HPP Unit: <strong class="text-slate-900">Rp <span x-text="calcProductVendorCost(prod).subtotal.toLocaleString('id-ID')"></span></strong></span>
                                        <template x-if="prod.include_ppn">
                                            <span class="text-amber-800 font-semibold">+ PPN 11%: <strong class="font-bold">Rp <span x-text="calcProductVendorCost(prod).ppn.toLocaleString('id-ID')"></span></strong></span>
                                        </template>
                                        <template x-if="prod.shipping_fee > 0">
                                            <span class="text-blue-800 font-semibold">+ Kirim: <strong class="font-bold">Rp <span x-text="Number(prod.shipping_fee || 0).toLocaleString('id-ID')"></span></strong></span>
                                        </template>
                                    </div>
                                    <div class="font-sans font-bold text-slate-900 flex items-center space-x-1.5">
                                        <span class="text-slate-500 font-medium text-[11px] uppercase tracking-wide">Total Biaya Vendor:</span>
                                        <span class="font-mono text-amber-900 font-black text-xs sm:text-sm">Rp <span x-text="calcProductVendorCost(prod).total.toLocaleString('id-ID')"></span></span>
                                    </div>
                                </div>
                            </template>

                        </div>
                    </template>
                </div>
            </div>

            <!-- EXISTING ALLOCATIONS PREVIEW (IF ANY ALREADY SAVED) -->
            <div x-show="allocateForm.existing_allocations && allocateForm.existing_allocations.length > 0" class="border border-slate-200/90 rounded-2xl p-4 bg-slate-50 space-y-2 shadow-2xs">
                <div class="flex items-center justify-between text-xs font-bold text-slate-800">
                    <span x-text="lang === 'zh' ? '此订单当前已关联的供应商采购单 (PO/SR)' : 'Dokumen PO Vendor Terkait'">Dokumen PO Vendor Terkait</span>
                    <span class="text-[11px] font-mono text-slate-500 font-bold px-2 py-0.5 rounded-full bg-white border border-slate-200" x-text="(allocateForm.existing_allocations.length) + ' Dokumen PO'"></span>
                </div>
                <div class="space-y-1.5 max-h-28 overflow-y-auto pr-1">
                    <template x-for="(po, pidx) in allocateForm.existing_allocations" :key="pidx">
                        <div class="p-2.5 px-3 rounded-xl bg-white border border-slate-200 flex items-center justify-between text-xs hover:border-slate-300 transition">
                            <div class="flex items-center space-x-2">
                                <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                                <div>
                                    <span class="font-bold text-slate-900 block text-xs" x-text="po.contact ? po.contact.name : 'Vendor Mitra'"></span>
                                    <span class="text-[10px] text-slate-500 font-mono" x-text="(po.procurement_type === 'sub_rental' ? 'Sub-Rent' : 'Beli') + ' • ' + po.order_number + ' • ' + (po.order_date ? po.order_date.split('T')[0] : '-')"></span>
                                </div>
                            </div>
                            <div class="text-right font-mono">
                                <span class="text-rose-600 font-black text-xs" x-text="'Rp ' + Number(po.total_amount || 0).toLocaleString('id-ID')"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

        </form>

        <!-- 3. FIXED MODAL FOOTER (EXECUTIVE FINANCIAL SUMMARY & ACTION BUTTONS) -->
        <div class="p-4 px-6 border-t border-slate-200 bg-white flex flex-col sm:flex-row items-center justify-between gap-4 shrink-0 shadow-sm">
            <!-- Left: Live Summary Strip -->
            <div class="flex items-center space-x-5 text-xs w-full sm:w-auto justify-between sm:justify-start">
                <div>
                    <span class="text-[10px] text-slate-400 uppercase tracking-wider block font-bold">Total Biaya Vendor (HPP)</span>
                    <span class="font-mono font-black text-rose-600 text-sm sm:text-base" x-text="'Rp ' + calculatedVendorCost.toLocaleString('id-ID')"></span>
                </div>
                <div class="h-8 w-px bg-slate-200"></div>
                <div>
                    <span class="text-[10px] text-slate-400 uppercase tracking-wider block font-bold">Proyeksi Laba Kotor</span>
                    <div class="flex items-center space-x-2">
                        <span class="font-mono font-black text-sm sm:text-base" :class="calculatedGrossProfit >= 0 ? 'text-emerald-600' : 'text-rose-600'" x-text="'Rp ' + calculatedGrossProfit.toLocaleString('id-ID')"></span>
                        <span class="px-2 py-0.5 rounded-full font-mono text-[10px] font-black" :class="calculatedGrossProfit >= 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800'" x-text="calculatedMarginPercent + '%'"></span>
                    </div>
                </div>
            </div>

            <!-- Right: Action Buttons -->
            <div class="flex items-center space-x-3 w-full sm:w-auto justify-end">
                <button type="button" 
                        @click="modalAllocateOpen = false" 
                        class="flex-1 sm:flex-initial px-5 py-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-700 hover:bg-slate-100 transition shadow-2xs" 
                        x-text="lang === 'zh' ? '取消' : 'Batal'">
                    Batal
                </button>
                <button type="submit" 
                        form="vendorAllocateForm"
                        :disabled="submittingAllocate" 
                        class="flex-1 sm:flex-initial px-6 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 active:scale-95 text-white text-xs font-black shadow-md transition disabled:opacity-50 flex items-center justify-center space-x-2">
                    <svg x-show="submittingAllocate" class="w-4 h-4 animate-spin text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                    <span x-text="submittingAllocate ? 'Menyimpan & Terbitkan...' : (lang === 'zh' ? '确认并生成采购订单 (Buat PO)' : 'Simpan & Terbitkan PO')">Simpan & Terbitkan PO</span>
                </button>
            </div>
        </div>

    </div>
</div>
