<!-- MODAL CANCEL TRANSAKSI (BATALKAN TRANSAKSI DENGAN ALASAN) -->
<div x-show="modalCancelOpen" 
     x-cloak
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto"
     @click.self="modalCancelOpen = false" style="display: none;">

    <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl border border-slate-200 overflow-hidden my-8 animate-in zoom-in-95 duration-150 flex flex-col">
        
        <!-- Modal Header -->
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-rose-50/50 shrink-0">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-2xl bg-rose-100 text-rose-700 flex items-center justify-center font-black shrink-0">
                    <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-black text-slate-900 flex items-center space-x-1.5">
                        <span x-text="lang === 'zh' ? '取消交易订单' : 'Batalkan Transaksi'">Batalkan Transaksi</span>
                        <span class="font-mono text-rose-600 font-black" x-text="cancelForm.order_number ? '(' + cancelForm.order_number + ')' : ''"></span>
                    </h3>
                    <p class="text-[11px] text-slate-500 mt-0.5" x-text="lang === 'zh' ? '输入取消原因并将订单状态更改为已取消。' : 'Masukkan alasan pembatalan untuk mengubah status menjadi Batal.'"></p>
                </div>
            </div>
            <button type="button" @click="modalCancelOpen = false" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-200/60 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Form Body -->
        <form @submit.prevent="submitCancelOrder()" class="p-6 space-y-4 text-xs">
            
            <!-- Warning Notice Banner -->
            <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200/80 flex items-start space-x-3 text-rose-900">
                <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <div class="space-y-0.5">
                    <p class="font-bold text-xs" x-text="lang === 'zh' ? '注意：此操作不可撤销' : 'Perhatian: Tindakan Ini Akan Membatalkan Transaksi'"></p>
                    <p class="text-[11px] text-rose-700 leading-relaxed" x-text="lang === 'zh' ? '该交易订单状态将变为“已取消”。请填写明确的取消原因以供审计查阅。' : 'Status transaksi akan diubah menjadi Cancelled (Batal). Alasan pembatalan akan disimpan dalam histori pesanan.'"></p>
                </div>
            </div>

            <!-- Field: Alasan Cancel (Required) -->
            <div>
                <label class="block font-bold text-slate-800 mb-1.5">
                    <span x-text="lang === 'zh' ? '取消原因 (必填)' : 'Alasan Pembatalan Transaksi'">Alasan Pembatalan Transaksi</span>
                    <span class="text-rose-500">*</span>
                </label>
                <textarea x-model="cancelForm.reason" 
                          rows="4" 
                          required
                          placeholder="Masukkan alasan pembatalan (misal: Klien membatalkan proyek, unit tidak tersedia, perubahan spesifikasi, dsb)..." 
                          class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-3.5 text-slate-900 font-medium outline-none focus:border-rose-400 focus:bg-white text-xs resize-none transition shadow-2xs"></textarea>
                <p class="text-[10px] text-slate-400 mt-1" x-text="lang === 'zh' ? '请如实填写取消原因，用于公司运营和财务归档。' : 'Alasan ini akan tercatat permanen pada sistem dan dapat dilihat oleh staf berwenang.'"></p>
            </div>

            <!-- Footer Action Buttons -->
            <div class="flex items-center justify-end space-x-3 pt-3 border-t border-slate-100">
                <button type="button" 
                        @click="modalCancelOpen = false" 
                        class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-100 transition" 
                        x-text="lang === 'zh' ? '取消关闭' : 'Tutup'">
                    Tutup
                </button>
                <button type="submit" 
                        :disabled="submittingCancel || !cancelForm.reason || !cancelForm.reason.trim()" 
                        class="px-6 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-black shadow-sm transition active:scale-95 disabled:opacity-50 flex items-center space-x-2">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                    <span x-text="submittingCancel ? (lang === 'zh' ? '正在取消...' : 'Memproses...') : (lang === 'zh' ? '确认取消交易' : 'Konfirmasi Batalkan Transaksi')">Konfirmasi Batalkan Transaksi</span>
                </button>
            </div>
        </form>
    </div>
</div>
