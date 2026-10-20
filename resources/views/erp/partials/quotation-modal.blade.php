<div x-show="modalQuotationOpen" 
     x-cloak
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/50 drawer-overlay"
     @click.self="modalQuotationOpen = false" style="display: none;">
    
    <div class="w-full max-w-5xl xl:max-w-6xl bg-white rounded-3xl border border-slate-200 shadow-2xl overflow-hidden flex flex-col max-h-[92vh]">
        <!-- Modal Header (Tabs removed as per user requirement) -->
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-2xl bg-yellow-400 text-slate-950 flex items-center justify-center font-black shadow-sm shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    </div>
                    <div>
                        <div class="flex items-center space-x-2">
                            <h3 class="text-lg font-black text-slate-900" x-text="quotationMode === 'edit' ? (lang === 'zh' ? '编辑价格报价单' : 'Edit Penawaran Harga') : (lang === 'zh' ? '创建价格报价单' : 'Buat Penawaran Harga')"></h3>
                            <!-- Auto-determined Quotation Type Badge -->
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-black uppercase tracking-wider border shadow-2xs"
                                  :class="{
                                      'bg-amber-50 text-amber-700 border-amber-200': quotationForm.quotation_type === 'rental',
                                      'bg-emerald-50 text-emerald-700 border-emerald-200': quotationForm.quotation_type === 'sales',
                                      'bg-purple-50 text-purple-700 border-purple-200': quotationForm.quotation_type === 'both'
                                  }"
                                  x-text="quotationForm.quotation_type === 'both' ? (lang === 'zh' ? '类型: 两者 (租赁与销售)' : 'Tipe: Keduanya') : (quotationForm.quotation_type === 'sales' ? (lang === 'zh' ? '类型: 销售 (Pembelian)' : 'Tipe: Pembelian') : (lang === 'zh' ? '类型: 租赁 (Sewa)' : 'Tipe: Sewa / Rental'))">
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5" x-text="lang === 'zh' ? '在产品行中设置每项交易类型（购买或租赁）。增值税11%自动计算。' : 'Tentukan tipe transaksi (Pembelian atau Sewa/Rental) per unit produk. PPN 11% terhitung otomatis.'"></p>
                    </div>
                </div>
                <button @click="modalQuotationOpen = false" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-200/60 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </div>

        <!-- Modal Form Body -->
        <div class="p-6 overflow-y-auto space-y-4 text-xs">
            <!-- Top Header Inputs (Requirement 2.b: No durasi sewa & satuan durasi here) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '客户 / 目标公司 *' : 'Pelanggan / Prospek *'"></label>
                    <div class="flex items-center space-x-1.5">
                        <select x-model="quotationForm.contact_id" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-slate-900 font-medium focus:border-amber-400 focus:bg-white outline-none">
                            <template x-for="cust in customerList" :key="cust.id">
                                <option :value="cust.id" :selected="cust.id == quotationForm.contact_id" x-text="cust.name"></option>
                            </template>
                        </select>
                        <button type="button" 
                                @click="openQuickCustomerModal()" 
                                class="w-9 h-9 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 inline-flex items-center justify-center transition shadow-2xs active:scale-95 shrink-0" 
                                :title="lang === 'zh' ? '快速新增客户' : 'Tambah Customer Baru'"
                                title="Tambah Customer Baru">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '文件日期 *' : 'Tanggal Dokumen *'"></label>
                    <input type="date" x-model="quotationForm.order_date" @change="updateValidUntil()" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-slate-900 font-medium focus:border-amber-400 focus:bg-white outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '有效期' : 'Masa Berlaku'"></label>
                    <input type="date" x-model="quotationForm.valid_until" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-slate-900 font-medium focus:border-amber-400 focus:bg-white outline-none">
                </div>
            </div>

            <!-- Row: No PO Customer (Opsional) & Alamat Site -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">
                        <span x-text="lang === 'zh' ? '客户采购单号 (No. PO)' : 'No. PO Customer'">No. PO Customer</span>
                    </label>
                    <input type="text" x-model="quotationForm.customer_po_number" placeholder="Contoh: PO-KIP/2026/X/089" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-slate-900 font-medium focus:border-amber-400 focus:bg-white outline-none">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '工地地址' : 'Alamat Site'"></label>
                    <input type="text" x-model="quotationForm.shipping_address" placeholder="Contoh: Kawasan Industri GIIC Cikarang Blok AB No. 12" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-slate-900 font-medium focus:border-amber-400 focus:bg-white outline-none">
                </div>
            </div>

            <!-- Unit Line Items -->
            <div class="border-t border-slate-200 pt-4">
                <div class="flex items-center justify-between mb-2.5">
                    <span class="font-bold text-slate-800 text-xs" x-text="lang === 'zh' ? '产品清单与交易明细' : 'Daftar Unit & Produk Transaksi'">Daftar Unit & Produk Transaksi</span>
                    <button type="button" @click="addItemRow()" class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-extrabold text-xs shadow-xs transition active:scale-95">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                        <span x-text="lang === 'zh' ? '添加产品行' : 'Tambah Baris Unit'">Tambah Baris Unit</span>
                    </button>
                </div>

                <div class="space-y-3">
                    <template x-for="(row, idx) in quotationForm.items" :key="idx">
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 space-y-3 relative">
                            <!-- Row 1: Tipe Transaksi, Nama Produk & Spesifikasi (Ketik Langsung) -->
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-start">
                                <!-- Tipe Transaksi (Sebelum Nama Produk) -->
                                <div class="md:col-span-3">
                                    <label class="text-[10px] text-slate-700 font-bold block mb-1" x-text="lang === 'zh' ? '交易类型 *' : 'Tipe Transaksi *'"></label>
                                    <select x-model="row.transaction_type" 
                                            @change="onItemTransactionTypeChange(idx)" 
                                            class="w-full bg-white border border-slate-300 rounded-xl px-2.5 py-2 text-slate-900 text-xs font-bold outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 shadow-xs">
                                        <option value="rental">Sewa / Rental</option>
                                        <option value="sales">Pembelian</option>
                                    </select>
                                </div>

                                <!-- Nama Produk (Ketik) -->
                                <div class="md:col-span-4">
                                    <label class="text-[10px] text-slate-700 font-bold block mb-1" x-text="lang === 'zh' ? '产品名称 / 编号 *' : 'Nama Produk / Model Unit *'"></label>
                                    <input type="text" 
                                           x-model="row.name" 
                                           required
                                           :placeholder="lang === 'zh' ? '例如: Forklift Diesel 3.5 Ton Toyota' : 'Contoh: Forklift Diesel 3.5 Ton Toyota'"
                                           class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-slate-900 text-xs font-bold outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 shadow-xs">
                                </div>

                                <!-- Spesifikasi (Ketik) -->
                                <div class="md:col-span-4">
                                    <label class="text-[10px] text-slate-700 font-bold block mb-1" x-text="lang === 'zh' ? '规格配置与参数 (Spesifikasi)' : 'Spesifikasi Unit (Ketik)'"></label>
                                    <input type="text" 
                                           x-model="row.specification" 
                                           :placeholder="lang === 'zh' ? '例如: 提升高度 3M, 实心橡胶轮胎, 货叉 1220mm' : 'Contoh: Mast 3M, Ban Solid Rubber, Fork 1220mm, dsb'"
                                           class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-slate-900 text-xs outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 shadow-xs">
                                </div>

                                <!-- Remove row button -->
                                <div class="md:col-span-1 flex flex-col justify-end items-end md:items-center">
                                    <label class="text-[10px] text-transparent block mb-1 select-none hidden md:block">Aksi</label>
                                    <button type="button" @click="removeItemRow(idx)" class="w-9 h-9 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 inline-flex items-center justify-center transition shadow-2xs active:scale-95" title="Hapus Baris">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Row 2: Qty, Durasi, Satuan Durasi, Harga Satuan, Foto -->
                            <div class="grid grid-cols-2 md:grid-cols-12 gap-3 items-center pt-2.5 border-t border-slate-200/70">
                                <!-- Qty -->
                                <div class="col-span-1 md:col-span-2">
                                    <label class="text-[10px] text-slate-500 font-bold block mb-1" x-text="lang === 'zh' ? '数量' : 'Qty'"></label>
                                    <input type="number" min="1" x-model.number="row.quantity" class="w-full bg-white border border-slate-300 rounded-xl px-3 py-1.5 text-slate-900 text-xs text-center font-bold outline-none focus:border-amber-400">
                                </div>

                                <!-- If Rental: Durasi & Satuan Durasi -->
                                <template x-if="row.transaction_type === 'rental'">
                                    <div class="col-span-1 md:col-span-2">
                                        <label class="text-[10px] text-slate-500 font-bold block mb-1" x-text="lang === 'zh' ? '期限 (Durasi)' : 'Durasi'"></label>
                                        <input type="number" min="1" x-model.number="row.duration" class="w-full bg-white border border-slate-300 rounded-xl px-3 py-1.5 text-slate-900 text-xs text-center font-bold outline-none focus:border-amber-400">
                                    </div>
                                </template>

                                <template x-if="row.transaction_type === 'rental'">
                                    <div class="col-span-2 md:col-span-2">
                                        <label class="text-[10px] text-slate-500 font-bold block mb-1" x-text="lang === 'zh' ? '时间单位' : 'Satuan Durasi'"></label>
                                        <select x-model="row.duration_unit" class="w-full bg-white border border-slate-300 rounded-xl px-2.5 py-1.5 text-slate-900 text-xs font-semibold outline-none focus:border-amber-400">
                                            <option value="Bulan">Bulan (月)</option>
                                            <option value="Minggu">Minggu (周)</option>
                                            <option value="Hari">Hari (天)</option>
                                            <option value="Jam">Jam (小时)</option>
                                        </select>
                                    </div>
                                </template>

                                <!-- Unit Price -->
                                <div :class="row.transaction_type === 'rental' ? 'col-span-2 md:col-span-3' : 'col-span-2 md:col-span-6'">
                                    <label class="text-[10px] text-slate-500 font-bold block mb-1" x-text="row.transaction_type === 'rental' ? (lang === 'zh' ? '租赁费率 (Rp)' : 'Tarif Sewa (Rp)') : (lang === 'zh' ? '购买/销售价格 (Rp)' : 'Harga Pembelian (Rp)')"></label>
                                    <input type="text"
                                           inputmode="numeric"
                                           data-rupiah
                                           :value="formatNumberWithDots(row.unit_price)"
                                           @input="row.unit_price = parseNumberFromDots($event.target.value)"
                                           placeholder="0"
                                           class="w-full bg-white border border-slate-300 rounded-xl px-3 py-1.5 text-slate-900 text-xs font-mono font-bold outline-none focus:border-amber-400">
                                </div>

                                <!-- Foto / Unit Upload -->
                                <div :class="row.transaction_type === 'rental' ? 'col-span-2 md:col-span-3' : 'col-span-2 md:col-span-4'">
                                    <label class="text-[10px] text-slate-500 font-bold block mb-1 truncate" x-text="lang === 'zh' ? '设备照片' : 'Foto Unit (Opsional)'"></label>
                                    <div class="flex items-center space-x-2">
                                        <label class="cursor-pointer h-9 px-3.5 rounded-xl border border-slate-300 hover:border-amber-400 bg-white hover:bg-amber-50 text-slate-700 transition inline-flex items-center space-x-1.5 text-xs font-bold shrink-0 shadow-2xs active:scale-95" title="Upload Foto Unit">
                                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                            <span x-text="lang === 'zh' ? '上传照片' : 'Upload Foto'">Upload Foto</span>
                                            <input type="file" accept="image/*" @change="uploadRowImage(idx, $event)" class="hidden">
                                        </label>
                                        <template x-if="row.image_preview">
                                            <img :src="row.image_preview" alt="Preview" class="w-8 h-8 rounded-lg object-cover border border-slate-300 shrink-0 shadow-xs">
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Tax Configuration & Live Calculation (Generous Spacing & Open Padding) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mt-6 pt-2">
                <!-- Customer Tax & Shipping Options -->
                <div class="lg:col-span-7 bg-slate-50/90 p-6 sm:p-7 rounded-3xl border border-slate-200/90 shadow-2xs space-y-4">
                    <div class="flex items-center justify-between pb-3.5 border-b border-slate-200/80 mb-2">
                        <h5 class="font-extrabold text-slate-900 text-xs sm:text-sm flex items-center space-x-2.5">
                            <span class="w-7 h-7 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 shadow-2xs">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z"></path></svg>
                            </span>
                            <span x-text="lang === 'zh' ? '运费与税务配置' : 'Pengaturan Ongkir & Pajak'">Pengaturan Ongkir & Pajak</span>
                        </h5>
                        <span class="text-[10px] font-bold text-slate-500 bg-white px-2.5 py-1 rounded-full border border-slate-200/70 shadow-2xs">Opsi Tambahan</span>
                    </div>

                    <!-- Biaya Pengiriman Checkbox & Conditional Inputs -->
                    <div class="p-4 sm:p-4.5 rounded-2xl bg-white border border-slate-200/90 hover:border-amber-400/80 shadow-2xs transition select-none">
                        <label class="flex items-center justify-between cursor-pointer">
                            <div class="flex items-center space-x-3">
                                <input type="checkbox" x-model="quotationForm.has_shipping" @change="if(!quotationForm.has_shipping){ quotationForm.shipping_delivery_fee = 0; quotationForm.shipping_return_fee = 0; }" class="w-4 h-4 text-amber-500 rounded border-slate-300 focus:ring-amber-400">
                                <div>
                                    <p class="font-extrabold text-slate-800 text-xs sm:text-sm" x-text="lang === 'zh' ? '运费 / 物流配送费 (Biaya Pengiriman)' : 'Biaya Pengiriman'"></p>
                                    <p class="text-[11px] text-slate-500 mt-0.5" x-text="lang === 'zh' ? '勾选添加设备发车及回收往返运费' : 'Centang untuk menambahkan biaya kirim berangkat & pengembalian'"></p>
                                </div>
                            </div>
                            <span class="text-xs font-mono font-bold" :class="quotationForm.has_shipping ? 'text-blue-700 bg-blue-50 px-2.5 py-1 rounded-lg border border-blue-200' : 'text-slate-400'" x-text="quotationForm.has_shipping ? ('Rp ' + formatNumberWithDots(calculateShippingFee || 0)) : 'Tidak Ada'"></span>
                        </label>

                        <!-- Shipping Breakdown Inputs -->
                        <div x-show="quotationForm.has_shipping" x-cloak class="mt-4 pt-4 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-700 mb-1.5" x-text="lang === 'zh' ? '发车运费 (Berangkat)' : 'Biaya Kirim Berangkat (Rp)'">Biaya Kirim Berangkat (Rp)</label>
                                <input type="text"
                                       inputmode="numeric"
                                       data-rupiah
                                       :value="formatNumberWithDots(quotationForm.shipping_delivery_fee)"
                                       @input="quotationForm.shipping_delivery_fee = parseNumberFromDots($event.target.value)"
                                       placeholder="0"
                                       class="w-full bg-slate-50 border border-slate-200 focus:bg-white focus:border-amber-400 rounded-xl px-3.5 py-2 text-xs font-mono font-bold text-slate-900 outline-none">
                                <span class="text-[9px] text-slate-400 mt-1 block" x-text="lang === 'zh' ? '设备调遣/出库运费' : 'Kirim unit ke lokasi klien'"></span>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-700 mb-1.5" x-text="lang === 'zh' ? '回程运费 (Pengembalian)' : 'Biaya Kirim Pengembalian (Rp)'">Biaya Kirim Pengembalian (Rp)</label>
                                <input type="text"
                                       inputmode="numeric"
                                       data-rupiah
                                       :value="formatNumberWithDots(quotationForm.shipping_return_fee)"
                                       @input="quotationForm.shipping_return_fee = parseNumberFromDots($event.target.value)"
                                       placeholder="0"
                                       class="w-full bg-slate-50 border border-slate-200 focus:bg-white focus:border-amber-400 rounded-xl px-3.5 py-2 text-xs font-mono font-bold text-slate-900 outline-none">
                                <span class="text-[9px] text-slate-400 mt-1 block" x-text="lang === 'zh' ? '租期结束设备撤场运费' : 'Penarikan unit kembali'"></span>
                            </div>
                        </div>
                    </div>

                    <!-- PPN Checkbox -->
                    <label class="flex items-center justify-between p-4 sm:p-4.5 rounded-2xl bg-white border border-slate-200/90 hover:border-amber-400/80 cursor-pointer transition select-none shadow-2xs">
                        <div class="flex items-center space-x-3">
                            <input type="checkbox" x-model="quotationForm.include_ppn" class="w-4 h-4 text-amber-500 rounded border-slate-300 focus:ring-amber-400">
                            <div>
                                <p class="font-extrabold text-slate-800 text-xs sm:text-sm">PPN 11% / 增值税</p>
                                <p class="text-[11px] text-slate-500 mt-0.5" x-text="lang === 'zh' ? '对总金额征收11%增值税' : 'Dikenakan PPN 11% pada penawaran'"></p>
                            </div>
                        </div>
                        <span class="text-xs font-mono font-bold" :class="quotationForm.include_ppn ? 'text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200' : 'text-slate-400'" x-text="quotationForm.include_ppn ? '+11%' : 'Non-PPN'"></span>
                    </label>

                    <!-- PPh 23 Checkbox -->
                    <label class="flex items-center justify-between p-4 sm:p-4.5 rounded-2xl bg-white border border-slate-200/90 hover:border-amber-400/80 cursor-pointer transition select-none shadow-2xs">
                        <div class="flex items-center space-x-3">
                            <input type="checkbox" x-model="quotationForm.include_pph23" class="w-4 h-4 text-amber-500 rounded border-slate-300 focus:ring-amber-400">
                            <div>
                                <p class="font-extrabold text-slate-800 text-xs sm:text-sm">PPh 23 (2%) / 预扣所得税</p>
                                <p class="text-[11px] text-slate-500 mt-0.5" x-text="lang === 'zh' ? '客户预扣2%所得税' : 'Dipotong oleh Customer (PPh Pasal 23 Sewa/Jasa)'"></p>
                            </div>
                        </div>
                        <span class="text-xs font-mono font-bold" :class="quotationForm.include_pph23 ? 'text-rose-600 bg-rose-50 px-2.5 py-1 rounded-lg border border-rose-200' : 'text-slate-400'" x-text="quotationForm.include_pph23 ? '-2%' : 'Tidak'"></span>
                    </label>
                </div>

                <!-- Financial Calculation Summary Box -->
                <div class="lg:col-span-5 bg-gradient-to-br from-slate-50 via-white to-amber-50/20 p-6 sm:p-7 rounded-3xl border border-slate-200/90 shadow-2xs flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-3.5 border-b border-slate-200/80 mb-4">
                            <h5 class="font-extrabold text-slate-900 text-xs sm:text-sm flex items-center space-x-2">
                                <span class="w-7 h-7 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 shadow-2xs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                </span>
                                <span x-text="lang === 'zh' ? '金额明细汇总' : 'Ringkasan Nilai Penawaran'">Ringkasan Nilai Penawaran</span>
                            </h5>
                            <span class="text-[10px] font-black font-mono px-2.5 py-1 rounded-md bg-amber-100 text-amber-800 border border-amber-200/80">IDR</span>
                        </div>

                        <div class="space-y-3.5 pt-1">
                            <div class="flex justify-between items-center text-xs text-slate-600">
                                <span x-text="lang === 'zh' ? '小计 (Subtotal):' : 'Subtotal:'">Subtotal:</span>
                                <span class="font-extrabold text-slate-900 font-mono text-sm" x-text="formatRupiah(calculateSubtotal)"></span>
                            </div>
                            <template x-if="quotationForm.has_shipping && calculateShippingFee > 0">
                                <div class="flex justify-between items-center text-xs text-slate-600">
                                    <span x-text="lang === 'zh' ? '运费 (Biaya Kirim):' : 'Biaya Pengiriman:'">Biaya Pengiriman:</span>
                                    <span class="font-extrabold text-blue-700 font-mono text-sm" x-text="'+ ' + formatRupiah(calculateShippingFee)"></span>
                                </div>
                            </template>
                            <template x-if="quotationForm.include_ppn">
                                <div class="flex justify-between items-center text-xs text-slate-600">
                                    <span x-text="lang === 'zh' ? '增值税 (PPN 11%):' : 'PPN 11%:'">PPN 11%:</span>
                                    <span class="font-extrabold text-emerald-700 font-mono text-sm" x-text="'+ ' + formatRupiah(calculatePpn)"></span>
                                </div>
                            </template>
                            <template x-if="!quotationForm.include_ppn">
                                <div class="flex justify-between items-center text-slate-400 text-xs">
                                    <span>PPN 11%:</span>
                                    <span class="font-mono">Non-PPN (Rp 0)</span>
                                </div>
                            </template>
                            <template x-if="quotationForm.include_pph23">
                                <div class="flex justify-between items-center text-xs text-slate-600">
                                    <span x-text="lang === 'zh' ? '预扣所得税 (PPh 23 2%):' : 'Potongan PPh 23 (2%):'">Potongan PPh 23 (2%):</span>
                                    <span class="font-extrabold text-rose-600 font-mono text-sm" x-text="'- ' + formatRupiah(calculatePph23)"></span>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div>
                        <div class="mt-6 pt-4 border-t-2 border-slate-200/90 bg-white p-4.5 rounded-2xl border border-slate-200 shadow-2xs flex items-center justify-between">
                            <div>
                                <span class="text-[11px] font-black uppercase tracking-wider text-slate-500 block" x-text="lang === 'zh' ? '总报价金额 (Total):' : 'Total Penawaran:'">Total Penawaran:</span>
                                <span class="text-[10px] text-slate-400 font-medium">Termasuk pajak & pengiriman</span>
                            </div>
                            <span class="text-xl sm:text-2xl font-black font-mono text-amber-700 tracking-tight" x-text="formatRupiah(calculateTotal)"></span>
                        </div>

                        <p class="text-[11px] text-slate-400 mt-4 leading-relaxed flex items-center space-x-1.5">
                            <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>* Dokumen otomatis dibuat dengan kop surat resmi PT Keylock Indonesia Perkasa.</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="p-5 border-t border-slate-200 bg-slate-50 flex items-center justify-between shrink-0">
            <div class="flex items-center space-x-2 text-xs text-slate-500 font-medium">
                <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                <span>Keylock Indonesia • Penawaran Harga Generator</span>
            </div>
            <div class="flex items-center space-x-3">
                <button type="button" 
                        @click="modalQuotationOpen = false" 
                        class="px-5 py-2.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-100 text-slate-700 font-bold transition shadow-xs active:scale-95" 
                        x-text="lang === 'zh' ? '取消' : 'Batal'">
                    Batal
                </button>
                <button type="button" 
                        @click="submitFastQuotation()" 
                        class="px-6 py-2.5 rounded-xl bg-yellow-400 hover:bg-yellow-300 active:scale-95 text-slate-950 font-black transition shadow-md flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    <span x-text="quotationMode === 'edit' ? (lang === 'zh' ? '保存更改' : 'Simpan Perubahan') : (lang === 'zh' ? '保存并创建文档' : 'Simpan & Buat Dokumen')"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- QUICK CUSTOMER CREATOR MODAL -->
<div x-show="quickCustomerModalOpen"
     x-cloak
     style="display: none;"
     class="fixed inset-0 z-[70] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
     @click.self="quickCustomerModalOpen = false">
    <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl border border-slate-200 overflow-hidden animate-in zoom-in-95 duration-150">
        
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
            <div class="flex items-center space-x-2.5">
                <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-black">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                </div>
                <div>
                    <h4 class="text-sm font-black text-slate-900" x-text="lang === 'zh' ? '快速新增客户' : 'Tambah Customer Baru'">Tambah Customer Baru</h4>
                    <p class="text-[10px] text-slate-500" x-text="lang === 'zh' ? '直接存入客户主数据' : 'Langsung tersimpan ke Master Data Customer'"></p>
                </div>
            </div>
            <button type="button" @click="quickCustomerModalOpen = false" class="text-slate-400 hover:text-slate-600 p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form @submit.prevent="submitQuickCustomer()" class="p-6 space-y-3.5 text-xs">
            <div>
                <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '客户 / 公司名称 *' : 'Nama Customer / Perusahaan *'">Nama Customer / Perusahaan *</label>
                <input type="text" x-model="quickCustomerForm.name" required placeholder="Contoh: PT. Sumber Makmur Abadi" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-slate-900 font-semibold focus:border-amber-400 focus:bg-white outline-none">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '主要联系人 (PIC)' : 'Nama PIC'">Nama PIC</label>
                    <input type="text" x-model="quickCustomerForm.pic_name" placeholder="Bpk. Hendra" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-slate-900 font-semibold focus:border-amber-400 focus:bg-white outline-none">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '电话 / WhatsApp' : 'Telepon / WA'">Telepon / WA</label>
                    <input type="text" x-model="quickCustomerForm.phone" placeholder="0812xxxxxxxx" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-slate-900 font-semibold focus:border-amber-400 focus:bg-white outline-none">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '电子邮箱 (Email)' : 'Email'">Email</label>
                <input type="email" x-model="quickCustomerForm.email" placeholder="purchasing@company.com" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-slate-900 font-semibold focus:border-amber-400 focus:bg-white outline-none">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '发票 / 公司地址' : 'Alamat Penagihan / Kantor'">Alamat Penagihan / Kantor</label>
                <textarea x-model="quickCustomerForm.billing_address" rows="2" placeholder="Alamat lengkap perusahaan..." class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-slate-900 font-semibold focus:border-amber-400 focus:bg-white outline-none"></textarea>
            </div>

            <div class="pt-3 flex items-center justify-end space-x-2.5 border-t border-slate-100">
                <button type="button" @click="quickCustomerModalOpen = false" class="px-5 py-2.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-100 text-slate-700 font-bold transition shadow-xs active:scale-95" x-text="lang === 'zh' ? '取消' : 'Batal'">
                    Batal
                </button>
                <button type="submit" 
                        :disabled="submittingQuickCustomer"
                        class="bg-yellow-400 hover:bg-yellow-300 active:scale-95 text-slate-950 font-black px-6 py-2.5 rounded-xl shadow-md transition disabled:opacity-50 flex items-center space-x-2">
                    <span x-text="submittingQuickCustomer ? (lang === 'zh' ? '正在保存...' : 'Menyimpan...') : (lang === 'zh' ? '保存并使用' : 'Simpan & Gunakan')"></span>
                </button>
            </div>
        </form>
    </div>
</div>
