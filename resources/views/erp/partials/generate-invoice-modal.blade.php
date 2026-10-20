<!-- MODAL GENERATE INVOICE (FAKTUR PENJUALAN) - CRM STYLE -->
<div x-show="modalGenerateInvOpen" 
     x-cloak
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto"
     @click.self="modalGenerateInvOpen = false" style="display: none;">

    <div class="bg-white w-full max-w-2xl rounded-3xl shadow-2xl border border-slate-200 overflow-hidden my-8 animate-in zoom-in-95 duration-150 flex flex-col max-h-[92vh]">
        
        <!-- Modal Header (CRM Style) -->
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50 shrink-0">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center font-black shrink-0">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-black text-slate-900" x-text="lang === 'zh' ? '开具销售发票 (Generate Invoice)' : 'Generate Invoice Pelanggan'">
                        Generate Invoice Pelanggan
                    </h3>
                    <p class="text-[11px] text-slate-500 mt-0.5" x-text="lang === 'zh' ? '核对商品、运费、税费及税务发票，一键生成正式客户账单。' : 'Periksa produk, biaya kirim, pajak PPN/PPh 23, dan lampiran faktur pajak.'"></p>
                </div>
            </div>
            <button type="button" @click="modalGenerateInvOpen = false" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-200/60 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Form Body -->
        <form @submit.prevent="submitGenerateInvoice()" class="p-6 space-y-4 overflow-y-auto text-xs">
            
            <!-- Ringkasan Transaksi / Pesanan -->
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 space-y-1.5">
                <div class="flex items-center justify-between text-xs font-bold text-slate-600">
                    <span>
                        <span class="text-slate-400 font-normal" x-text="lang === 'zh' ? '关联合同:' : 'Pesanan Induk:'">Pesanan Induk:</span> 
                        <span class="text-slate-900 font-mono font-black" x-text="invoiceForm.order_number || '-'"></span>
                    </span>
                    <span class="truncate max-w-[220px]">
                        <span class="text-slate-400 font-normal" x-text="lang === 'zh' ? '客户:' : 'Klien:'">Klien:</span> 
                        <span class="text-slate-900 font-extrabold" x-text="invoiceForm.customer_name || '-'"></span>
                    </span>
                </div>
            </div>

            <!-- Field 1: No Invoice (TERGENERATE SENDIRI & TIDAK BISA DIEDIT) -->
            <div>
                <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '发票编号 (系统自动生成，不可编辑) *' : 'No. Invoice (Tergenerate Otomatis & Terkunci) *'">
                    No. Invoice (Tergenerate Otomatis & Terkunci) *
                </label>
                <div class="relative">
                    <input type="text" 
                           x-model="invoiceForm.invoice_number" 
                           readonly 
                           class="w-full bg-slate-100 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 font-mono font-bold text-xs cursor-not-allowed select-none outline-none">
                    <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                </div>
                <span class="text-[10px] text-slate-400 mt-0.5 block" x-text="lang === 'zh' ? '系统已自动生成官方流水单号。' : 'Nomor invoice terbit otomatis mengikuti format resmi PT KEY LOCK INDONESIA.'"></span>
            </div>

            <!-- Field 2 & 3: Tanggal Terbit & Tanggal Jatuh Tempo -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '开票日期 (Tanggal Terbit) *' : 'Tanggal Terbit *'">
                        Tanggal Terbit *
                    </label>
                    <input type="date" 
                           x-model="invoiceForm.issue_date" 
                           required 
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-900 font-semibold outline-none focus:border-yellow-400 focus:bg-white text-xs">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '付款到期日 (Jatuh Tempo) *' : 'Tanggal Jatuh Tempo *'">
                        Tanggal Jatuh Tempo *
                    </label>
                    <input type="date" 
                           x-model="invoiceForm.due_date" 
                           required 
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-900 font-semibold outline-none focus:border-yellow-400 focus:bg-white text-xs">
                </div>
            </div>

            <!-- TABEL PRODUK APA YANG DIPESAN -->
            <div>
                <label class="block font-bold text-slate-700 mb-1.5" x-text="lang === 'zh' ? '订单产品及服务清单' : 'Daftar Produk / Layanan yang Dipesan'">
                    Daftar Produk / Layanan yang Dipesan
                </label>
                <div class="border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-100 text-slate-700 font-bold border-b border-slate-200">
                            <tr>
                                <th class="py-2 px-3 w-8 text-center">#</th>
                                <th class="py-2 px-3">Deskripsi Produk</th>
                                <th class="py-2 px-2 text-center w-12">Qty</th>
                                <th class="py-2 px-3 text-right w-28">Harga (Rp)</th>
                                <th class="py-2 px-3 text-right w-28">Total (Rp)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            <template x-for="(item, idx) in (invoiceForm.items || [])" :key="idx">
                                <tr>
                                    <td class="py-2 px-3 text-center text-slate-400 font-mono" x-text="idx + 1"></td>
                                    <td class="py-2 px-3 font-semibold text-slate-900" x-text="item.description || item.name || '-'"></td>
                                    <td class="py-2 px-2 text-center font-bold text-slate-800" x-text="item.quantity"></td>
                                    <td class="py-2 px-3 text-right font-mono text-slate-600" x-text="'Rp ' + Number(item.unit_price || 0).toLocaleString('id-ID')"></td>
                                    <td class="py-2 px-3 text-right font-mono font-bold text-slate-900" x-text="'Rp ' + Number(item.line_total || (item.quantity * item.unit_price) || 0).toLocaleString('id-ID')"></td>
                                </tr>
                            </template>
                            <template x-if="!invoiceForm.items || invoiceForm.items.length === 0">
                                <tr>
                                    <td colspan="5" class="py-3 px-3 text-center text-slate-400 italic">Memuat rincian produk...</td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- FIELD: BIAYA KIRIM BERANGKAT & BIAYA KIRIM PENGEMBALIAN -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <!-- Biaya Kirim Berangkat -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '发车运费 (Biaya Kirim Berangkat)' : 'Biaya Kirim Berangkat (Rp)'">
                        Biaya Kirim Berangkat (Rp)
                    </label>
                    <input type="text" 
                           inputmode="numeric"
                           data-rupiah
                           :value="formatNumberWithDots(invoiceForm.shipping_delivery_fee)" 
                           @input="invoiceForm.shipping_delivery_fee = parseNumberFromDots($event.target.value); recalcInvoiceFormTotals()" 
                           placeholder="0"
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-900 font-mono font-bold outline-none focus:border-yellow-400 focus:bg-white text-xs">
                    <span class="text-[10px] text-slate-400 mt-0.5 block" x-text="lang === 'zh' ? '设备调遣/出库运送至客户现场费用。' : 'Biaya mobilisasi / kirim unit ke lokasi klien.'"></span>
                </div>

                <!-- Biaya Kirim Pengembalian -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '返程运费 (Biaya Kirim Pengembalian)' : 'Biaya Kirim Pengembalian (Rp)'">
                        Biaya Kirim Pengembalian (Rp)
                    </label>
                    <input type="text" 
                           inputmode="numeric"
                           data-rupiah
                           :value="formatNumberWithDots(invoiceForm.shipping_return_fee)" 
                           @input="invoiceForm.shipping_return_fee = parseNumberFromDots($event.target.value); recalcInvoiceFormTotals()" 
                           placeholder="0"
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-900 font-mono font-bold outline-none focus:border-yellow-400 focus:bg-white text-xs">
                    <span class="text-[10px] text-slate-400 mt-0.5 block" x-text="lang === 'zh' ? '租期结束设备撤场回厂运费。' : 'Biaya demobilisasi / penarikan unit kembali.'"></span>
                </div>
            </div>

            <!-- KETERANGAN PPN & PPH 23 (DAPAT DICEKLIS JIKA DI PENAWARAN BELUM DICEKLIS) -->
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 space-y-3">
                <div class="text-xs font-bold text-slate-800 flex items-center justify-between">
                    <span x-text="lang === 'zh' ? '税务选项 (PPN 增值税 & PPh 23 租金预扣)' : 'Opsi Pajak (PPN & PPh Pasal 23)'">Opsi Pajak (PPN & PPh Pasal 23)</span>
                    <span class="text-[10px] text-slate-400 font-normal">Dapat dicentang sekarang jika di penawaran belum diceklis</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <!-- Checkbox PPN -->
                    <label class="flex items-start space-x-2.5 p-2.5 rounded-xl border bg-white cursor-pointer transition select-none"
                           :class="invoiceForm.include_ppn ? 'border-amber-400 bg-amber-50/20' : 'border-slate-200'">
                        <input type="checkbox" x-model="invoiceForm.include_ppn" @change="recalcInvoiceFormTotals()" class="mt-0.5 rounded text-amber-500 focus:ring-amber-400">
                        <div class="text-xs">
                            <span class="font-bold text-slate-900 block">PPN (11%)</span>
                            <span class="text-[10px] text-slate-500 block leading-tight">Pajak Pertambahan Nilai (+11% subtotal)</span>
                            <span class="font-mono font-bold text-emerald-600 text-[11px] mt-1 block" x-text="'+ Rp ' + Number(calculatedInvoicePpn).toLocaleString('id-ID')"></span>
                        </div>
                    </label>

                    <!-- Checkbox PPh 23 -->
                    <label class="flex items-start space-x-2.5 p-2.5 rounded-xl border bg-white cursor-pointer transition select-none"
                           :class="invoiceForm.include_pph ? 'border-rose-400 bg-rose-50/20' : 'border-slate-200'">
                        <input type="checkbox" x-model="invoiceForm.include_pph" @change="recalcInvoiceFormTotals()" class="mt-0.5 rounded text-rose-500 focus:ring-rose-400">
                        <div class="text-xs">
                            <span class="font-bold text-slate-900 block">PPh 23 (2%)</span>
                            <span class="text-[10px] text-slate-500 block leading-tight">Pemotongan pajak sewa alat (-2% subtotal)</span>
                            <span class="font-mono font-bold text-rose-600 text-[11px] mt-1 block" x-text="'- Rp ' + Number(calculatedInvoicePph).toLocaleString('id-ID')"></span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- LIVE FINANCIAL TOTALS PREVIEW (PERSIS FORMULA GAMBAR) -->
            <div class="bg-slate-900 text-white p-4 rounded-2xl space-y-2 text-xs shadow-md">
                <div class="flex items-center justify-between font-mono text-[11px] text-slate-400">
                    <span>Subtotal Produk:</span>
                    <span class="text-white font-bold" x-text="'Rp ' + Number(invoiceForm.subtotal || 0).toLocaleString('id-ID')"></span>
                </div>
                <div class="flex items-center justify-between font-mono text-[11px] text-slate-400">
                    <span x-text="lang === 'zh' ? '发车运费 (Berangkat):' : 'Biaya Kirim Berangkat:'">Biaya Kirim Berangkat:</span>
                    <span class="text-white font-bold" x-text="'Rp ' + Number(invoiceForm.shipping_delivery_fee || 0).toLocaleString('id-ID')"></span>
                </div>
                <div class="flex items-center justify-between font-mono text-[11px] text-slate-400">
                    <span x-text="lang === 'zh' ? '返程运费 (Pengembalian):' : 'Biaya Kirim Pengembalian:'">Biaya Kirim Pengembalian:</span>
                    <span class="text-white font-bold" x-text="'Rp ' + Number(invoiceForm.shipping_return_fee || 0).toLocaleString('id-ID')"></span>
                </div>
                <div class="flex items-center justify-between font-mono text-[11px] text-slate-400">
                    <span>PPN 11%:</span>
                    <span class="text-emerald-400 font-bold" x-text="'+ Rp ' + Number(calculatedInvoicePpn).toLocaleString('id-ID')"></span>
                </div>
                <div x-show="invoiceForm.include_pph" class="flex items-center justify-between font-mono text-[11px] text-rose-400">
                    <span>PPh 23 (2% Potongan):</span>
                    <span class="font-bold italic" x-text="'- Rp ' + Number(calculatedInvoicePph).toLocaleString('id-ID')"></span>
                </div>
                <div class="flex items-center justify-between pt-2 border-t border-slate-800 font-mono">
                    <span class="text-xs font-black uppercase text-amber-400">GRAND TOTAL INVOICE:</span>
                    <span class="text-base font-black text-amber-300" x-text="'Rp ' + Number(calculatedInvoiceGrandTotal).toLocaleString('id-ID')"></span>
                </div>
            </div>

            <!-- FIELD UPLOAD DOKUMEN FAKTUR PAJAK -->
            <div>
                <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '上传税务发票 (Dokumen Faktur Pajak e-Faktur)' : 'Upload Dokumen Faktur Pajak (e-Faktur DJP)'">
                    Upload Dokumen Faktur Pajak (e-Faktur DJP)
                </label>
                <div class="relative border-2 border-dashed border-slate-200 hover:border-yellow-400 rounded-2xl p-4 bg-slate-50 transition text-center cursor-pointer">
                    <input type="file" 
                           id="tax_doc_input" 
                           accept=".pdf,.jpg,.jpeg,.png,.webp"
                           @change="onTaxInvoiceFileSelected($event)"
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                    <div class="space-y-1">
                        <svg class="w-8 h-8 mx-auto text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                        <p class="text-xs font-bold text-slate-700" x-text="taxInvoiceFileName ? taxInvoiceFileName : (lang === 'zh' ? '点击或拖拽上传增值税电子发票 (PDF 或 图片)' : 'Pilih file PDF atau Foto e-Faktur Pajak')"></p>
                        <p class="text-[10px] text-slate-400" x-text="lang === 'zh' ? '支持 PDF, JPG, PNG 最大 10MB。此文件将作为发票第 2 页呈现。' : 'Format PDF, JPG, PNG (Maks 10MB). Dokumen ini akan tampil di Halaman 2 Invoice.'"></p>
                    </div>
                </div>
            </div>

            <!-- Field: Remaks / Catatan Invoice -->
            <div>
                <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '发票备注 / 条款 (Remaks)' : 'Remaks / Catatan Invoice'">
                    Remaks / Catatan Invoice
                </label>
                <textarea x-model="invoiceForm.notes" 
                          rows="2" 
                          placeholder="Catatan pembayaran, nomor rekening BCA/BNI, dll..." 
                          class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-slate-900 font-medium outline-none focus:border-yellow-400 focus:bg-white text-xs"></textarea>
            </div>

            <!-- Footer Action Buttons -->
            <div class="flex items-center justify-end space-x-3 pt-3 border-t border-slate-100">
                <button type="button" @click="modalGenerateInvOpen = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-100 transition" x-text="lang === 'zh' ? '取消' : 'Batal'">Batal</button>
                <button type="submit" :disabled="submittingInvoice" class="px-6 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-black shadow-sm transition active:scale-95 disabled:opacity-50 flex items-center space-x-2">
                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span x-text="submittingInvoice ? (lang === 'zh' ? '正在开票...' : 'Menerbitkan...') : (lang === 'zh' ? '确认并生成发票' : 'Terbitkan Invoice')">Terbitkan Invoice</span>
                </button>
            </div>
        </form>
    </div>
</div>
