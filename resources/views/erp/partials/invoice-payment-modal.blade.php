<!-- MODAL CATAT PEMBAYARAN INVOICE (UPDATE PAYMENT) - CRM STYLE -->
<div x-show="modalPaymentOpen" 
     x-cloak
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto"
     @click.self="modalPaymentOpen = false" style="display: none;">

    <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl border border-slate-200 overflow-hidden my-8 animate-in zoom-in-95 duration-150 flex flex-col max-h-[90vh]">
        
        <!-- Modal Header (CRM Style) -->
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50 shrink-0">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-black shrink-0">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-black text-slate-900" x-text="lang === 'zh' ? '录入收款记录 (Payment In)' : 'Catat / Update Pembayaran Masuk'">
                        Catat / Update Pembayaran Masuk
                    </h3>
                    <p class="text-[11px] text-slate-500 mt-0.5" x-text="lang === 'zh' ? '登记客户对于发票的付款金额与流水单号。' : 'Catat pelunasan atau cicilan pembayaran atas invoice tagihan pelanggan.'"></p>
                </div>
            </div>
            <button type="button" @click="modalPaymentOpen = false" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-200/60 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Form Body -->
        <form @submit.prevent="submitPayment()" class="p-6 space-y-4 overflow-y-auto text-xs">
            
            <!-- Summary Info Tagihan -->
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 space-y-1.5">
                <div class="flex items-center justify-between text-xs font-bold text-slate-600">
                    <span>
                        <span class="text-slate-400 font-normal">No. Invoice:</span> 
                        <span class="text-slate-900 font-mono font-black" x-text="paymentForm.invoice_number || '-'"></span>
                    </span>
                    <span class="truncate max-w-[200px]">
                        <span class="text-slate-400 font-normal">Klien:</span> 
                        <span class="text-slate-900 font-extrabold" x-text="paymentForm.customer_name || '-'"></span>
                    </span>
                </div>
                <div class="grid grid-cols-2 gap-2 text-xs pt-2 border-t border-slate-200 font-mono">
                    <div>
                        <span class="text-slate-400 text-[10px] block font-sans">Total Tagihan:</span>
                        <span class="text-slate-900 font-bold" x-text="'Rp ' + Number(paymentForm.total_amount || 0).toLocaleString('id-ID')"></span>
                    </div>
                    <div>
                        <span class="text-rose-500 text-[10px] block font-sans font-bold">Sisa Belum Bayar:</span>
                        <span class="text-rose-600 font-black text-sm" x-text="'Rp ' + Number(paymentForm.balance_due || 0).toLocaleString('id-ID')"></span>
                    </div>
                </div>
            </div>

            <!-- Field: Jumlah Pembayaran & Tanggal Bayar -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '收款金额 (Rp) *' : 'Jumlah Pembayaran (Rp) *'">
                        Jumlah Pembayaran (Rp) *
                    </label>
                    <input type="text" 
                           inputmode="numeric"
                           data-rupiah
                           :value="formatNumberWithDots(paymentForm.amount)" 
                           @input="paymentForm.amount = parseNumberFromDots($event.target.value)"
                           placeholder="0"
                           required 
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-900 font-mono font-black text-sm outline-none focus:border-yellow-400 focus:bg-white">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '付款日期 *' : 'Tanggal Bayar *'">
                        Tanggal Bayar *
                    </label>
                    <input type="date" 
                           x-model="paymentForm.payment_date" 
                           required 
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-900 font-semibold outline-none focus:border-yellow-400 focus:bg-white text-xs">
                </div>
            </div>

            <!-- Field: Metode & Rekening Bank -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '支付方式 *' : 'Metode Pembayaran *'">
                        Metode Pembayaran *
                    </label>
                    <select x-model="paymentForm.payment_method" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-900 font-semibold outline-none focus:border-yellow-400 focus:bg-white text-xs">
                        <option value="bank_transfer">Transfer Bank (BCA / Mandiri)</option>
                        <option value="cash">Kas Tunai (Cash)</option>
                        <option value="giro">Bilyet Giro / Cek</option>
                        <option value="qris">QRIS / E-Wallet</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '收款账户 (Bank)' : 'Rekening Bank Tujuan'">
                        Rekening Bank Tujuan
                    </label>
                    <select x-model="paymentForm.bank_account" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-900 font-semibold outline-none focus:border-yellow-400 focus:bg-white text-xs">
                        <option value="BCA - Keylock (873-098-1234)">BCA - PT Keylock Indonesia (873-098-1234)</option>
                        <option value="Mandiri - Keylock (133-00-29831-2)">Mandiri - PT Keylock Indonesia (133-00-29831-2)</option>
                        <option value="Kas Utama Operasional">Kas Utama Operasional (Tunai)</option>
                    </select>
                </div>
            </div>

            <!-- Field: No Referensi Transfer & Catatan -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '转账单号 / 参考号' : 'No. Referensi / Bukti Transfer'">
                        No. Referensi / Bukti Transfer
                    </label>
                    <input type="text" 
                           x-model="paymentForm.reference_number" 
                           placeholder="Contoh: TRF-BCA-981290" 
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-900 font-semibold outline-none focus:border-yellow-400 focus:bg-white text-xs">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '收款备注' : 'Catatan Pembayaran'">
                        Catatan Pembayaran
                    </label>
                    <input type="text" 
                           x-model="paymentForm.notes" 
                           placeholder="Catatan pelunasan invoice..." 
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-900 font-medium outline-none focus:border-yellow-400 focus:bg-white text-xs">
                </div>
            </div>

            <!-- Footer Action Buttons -->
            <div class="flex items-center justify-end space-x-3 pt-3 border-t border-slate-100">
                <button type="button" @click="modalPaymentOpen = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-100 transition" x-text="lang === 'zh' ? '取消' : 'Batal'">Batal</button>
                <button type="submit" :disabled="submittingPayment" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black shadow-sm transition active:scale-95 disabled:opacity-50 flex items-center space-x-2">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span x-text="submittingPayment ? (lang === 'zh' ? '正在保存...' : 'Menyimpan...') : (lang === 'zh' ? '确认并保存付款' : 'Simpan Pembayaran')">Simpan Pembayaran</span>
                </button>
            </div>
        </form>
    </div>
</div>
