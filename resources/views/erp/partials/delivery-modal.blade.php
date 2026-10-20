<!-- MODAL GENERATE SURAT JALAN (DELIVERY ORDER) -->
<div x-show="modalDoOpen" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 drawer-overlay"
     @click.self="modalDoOpen = false" 
     style="display: none;">
    
    <div class="w-full max-w-2xl bg-white rounded-3xl border border-slate-200 shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
        <!-- Modal Header -->
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-2xl bg-emerald-500 text-white flex items-center justify-center font-black shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h2"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-black text-slate-900 flex items-center space-x-2">
                        <span>Terbitkan Surat Jalan (Delivery Order)</span>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-mono" x-text="doForm.order_number"></span>
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Klien: <strong class="text-slate-700" x-text="doForm.customer_name"></strong>
                    </p>
                </div>
            </div>
            <button @click="modalDoOpen = false" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-200/60 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Modal Form Body -->
        <form @submit.prevent="submitGenerateDo()" class="p-6 overflow-y-auto space-y-4 text-xs">

            <!-- Field 1: Tanggal Pengiriman (Required) -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">
                    Tanggal Pengiriman / 发货日期 <span class="text-rose-500">*</span>
                </label>
                <input type="date" 
                       x-model="doForm.delivery_date" 
                       required
                       class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2.5 text-slate-900 font-medium focus:border-emerald-500 focus:bg-white outline-none">
            </div>

            <!-- Field 2 & 3: Nama PIC (Required) & No Telp PIC (Opsional / Tidak Requirement) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">
                        Nama PIC Penerima / 收货责任人 <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           x-model="doForm.recipient_name" 
                           placeholder="Contoh: Bambang Sudiro" 
                           required
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2.5 text-slate-900 font-medium focus:border-emerald-500 focus:bg-white outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">
                        No. Telp PIC / 联系电话 <span class="text-slate-400 font-normal">(Opsional)</span>
                    </label>
                    <input type="text" 
                           x-model="doForm.recipient_phone" 
                           placeholder="Contoh: 0812-9876-5432" 
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2.5 text-slate-900 font-medium focus:border-emerald-500 focus:bg-white outline-none">
                </div>
            </div>

            <!-- Field 4: Vendor Pengiriman & Bisa Ketik Sendiri -->
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="font-bold text-slate-700">
                        Vendor Pengiriman / 承运公司
                    </label>
                    <span class="text-[10px] text-emerald-700 font-bold bg-emerald-50 px-2 py-0.5 rounded-md">
                        Bisa ketik baru otomatis tersimpan ke Master Vendor
                    </span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                    <!-- Dropdown Vendor dari Master Data -->
                    <select @change="onSelectVendor($event)" 
                            class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2.5 text-slate-900 font-medium focus:border-emerald-500 focus:bg-white outline-none">
                        <option value="">-- Pilih dari Master Vendor --</option>
                        @foreach($vendors ?? [] as $vendor)
                            @php
                                $cleanPic = !empty($vendor->pic_name) ? trim(preg_replace('/\s*\(.*?\)/', '', $vendor->pic_name)) : '';
                                $displayDriver = !empty($cleanPic) ? $cleanPic : '-';
                            @endphp
                            <option value="{{ $vendor->name }}" data-driver="{{ $displayDriver !== '-' ? $displayDriver : '' }}">
                                {{ $vendor->name }} ({{ $displayDriver }})
                            </option>
                        @endforeach
                        <option value="__custom__">+ Ketik Vendor Baru Lainnya</option>
                    </select>

                    <!-- Text input untuk Vendor (Bisa ketik sendiri atau hasil pilihan dropdown) -->
                    <input type="text" 
                           x-model="doForm.vendor_name" 
                           placeholder="Nama Vendor Pengiriman (ketik baru / dari master)" 
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2.5 text-slate-900 font-medium focus:border-emerald-500 focus:bg-white outline-none">
                </div>
                <p class="text-[10px] text-slate-400 mt-1">Jika mengetik vendor baru, sistem akan otomatis mendaftarkannya ke Master Data Vendor dengan kategori Vendor Pengiriman.</p>
            </div>

            <!-- Field 5 & 6: Nama Driver (Tidak Requirement) & No Plat (Tidak Requirement) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">
                        Nama Driver / 司机姓名 <span class="text-slate-400 font-normal">(Opsional)</span>
                    </label>
                    <input type="text" 
                           x-model="doForm.driver_name" 
                           placeholder="Contoh: Suhendra / Bambang" 
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2.5 text-slate-900 font-medium focus:border-emerald-500 focus:bg-white outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">
                        No. Plat Kendaraan / 车牌号 <span class="text-slate-400 font-normal">(Opsional)</span>
                    </label>
                    <input type="text" 
                           x-model="doForm.vehicle_number" 
                           placeholder="Contoh: B 9142 KLO (Tronton)" 
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2.5 text-slate-900 font-medium focus:border-emerald-500 focus:bg-white outline-none">
                </div>
            </div>

            <!-- Field 7: Remaks (Opsional) -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">
                    Remaks / Catatan Pengiriman (备注) <span class="text-slate-400 font-normal">(Opsional)</span>
                </label>
                <textarea x-model="doForm.notes" 
                          rows="3" 
                          placeholder="Tuliskan remaks serah terima, kondisi unit, nomor segel kontainer, dsb..."
                          class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-slate-900 font-medium focus:border-emerald-500 focus:bg-white outline-none"></textarea>
            </div>

            <!-- Modal Footer inside Form -->
            <div class="pt-4 border-t border-slate-200 flex items-center justify-between">
                <button type="button" 
                        @click="modalDoOpen = false" 
                        class="px-5 py-2.5 rounded-xl text-slate-600 hover:text-slate-800 font-semibold transition">
                    Batal
                </button>
                <button type="submit" 
                        :disabled="submittingDo"
                        class="bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 text-white font-black px-6 py-2.5 rounded-xl transition shadow-md flex items-center space-x-2 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    <span x-text="submittingDo ? 'Memproses...' : 'Terbitkan & Cetak DO'"></span>
                </button>
            </div>
        </form>
    </div>
</div>
