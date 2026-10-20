<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penawaran Harga / 报价单 - {{ $order->order_number }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+SC:wght@400;500;700;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', 'Noto Sans SC', sans-serif;
            background-color: #F1F5F9;
            color: #000000;
            font-size: 11px;
            line-height: 1.35;
        }
        @media print {
            body {
                background-color: #FFFFFF;
                color: #000000;
                padding: 0 !important;
                margin: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .page-container {
                padding: 0 !important;
                margin: 0 !important;
                max-width: 100% !important;
                box-shadow: none !important;
                border: none !important;
            }
            @page {
                size: A4;
                margin: 10mm 12mm 10mm 12mm;
            }
        }
        table.commercial-table, table.commercial-table th, table.commercial-table td {
            border: 1px solid #000000;
            border-collapse: collapse;
        }
    </style>
</head>
<body class="py-6 antialiased">

    <!-- Top Action Toolbar (Hidden during print) -->
    <div class="no-print max-w-5xl mx-auto mb-4 px-4 flex items-center justify-between">
        <a href="{{ route('erp.index') }}" class="inline-flex items-center space-x-2 text-xs font-bold text-slate-700 hover:text-slate-900 bg-white px-4 py-2 rounded-xl shadow-xs border border-slate-200 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Kembali ke Mini-ERP / 返回</span>
        </a>

        <div class="flex items-center space-x-3">
            <button onclick="window.print()" class="inline-flex items-center space-x-2 bg-yellow-400 hover:bg-yellow-300 text-slate-950 font-black text-xs px-5 py-2 rounded-xl shadow-md transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span>Cetak / Simpan PDF (打印/保存)</span>
            </button>
        </div>
    </div>

    <!-- Official A4 Document Container (Replicating PT Trafindo PDF exactly) -->
    <div class="page-container max-w-5xl mx-auto bg-white p-8 sm:p-10 border border-slate-300 shadow-lg">

        <!-- ================================================================= -->
        <!-- HEADER LETTERHEAD (LOGO IMAGE 2 & COMPANY KARAWANG DETAILS)       -->
        <!-- ================================================================= -->
        <div class="flex items-start justify-between mb-4">
            <div class="flex items-start space-x-4">
                <!-- Official Keylock Logo (Image 2) -->
                <div class="w-16 h-16 shrink-0 pt-0.5">
                    <img src="{{ asset('images/keylock-logo.svg') }}" alt="Keylock Logo" class="w-16 h-16 object-contain">
                </div>
                <div>
                    <h1 class="text-base font-black tracking-tight text-black uppercase font-mono">{{ $company['name'] }}</h1>
                    <p class="text-[11px] text-black max-w-md mt-0.5 leading-tight">
                        {{ $company['address'] }}
                    </p>
                </div>
            </div>

            <div class="text-right">
                <span class="text-xs font-mono font-bold text-slate-500">No: {{ $order->order_number }}</span>
                <p class="text-[10px] text-slate-400">Date: {{ date('d/m/Y', strtotime($order->order_date)) }}</p>
                @if(!empty($order->customer_po_number))
                    <p class="text-[10px] font-mono font-bold text-slate-700">PO: {{ $order->customer_po_number }}</p>
                @endif
            </div>
        </div>

        <!-- Company NPWP Line -->
        <div class="text-[11px] font-bold text-black mb-3 pb-1">
            <p>{{ $company['name'] }}</p>
            <p>NPWP: {{ $company['npwp'] }}</p>
        </div>

        <!-- Customer Box -->
        <div class="text-[11px] text-black mb-4 space-y-0.5">
            <p><strong class="font-bold">客户名称 Nama Pelanggan :</strong> {{ $order->contact->name ?? 'PT. Trafoindo Prima Perkasa' }}</p>
            <p class="pl-4">{{ $order->shipping_address ?? $order->contact->shipping_address ?? 'Jl. Raya Siliwangi RT. 001 / 001, Kel. Gembor, Kec. Periuk, Tangerang, Banten, 15133, Indonesia' }}</p>
            <p class="pl-4"><strong>NPWP :</strong> {{ $order->contact->npwp ?? '-' }}</p>
            @if(!empty($order->customer_po_number))
                <p class="pl-4"><strong>No. PO Pelanggan / 采购单号 :</strong> {{ $order->customer_po_number }}</p>
            @endif
        </div>

        <!-- ================================================================= -->
        <!-- ITEM TABLE (EXACT 8 COLUMNS AS IN THE SAMPLE PDF)                 -->
        <!-- ================================================================= -->
        <table class="w-full commercial-table text-[10px] mb-0">
            <thead class="bg-slate-50 font-bold text-center">
                <tr>
                    <th class="py-1.5 px-1 w-8">No.</th>
                    <th class="py-1.5 px-2 w-36 text-left">产品名称<br>Nama Produk</th>
                    <th class="py-1.5 px-3 text-left">规格配置<br>Spesifikasi</th>
                    <th class="py-1.5 px-1 w-20 text-center">图片<br>Foto / Unit</th>
                    <th class="py-1.5 px-1 w-12 text-center">数量<br>Qty</th>
                    @if($order->quotation_type !== 'sales')
                    <th class="py-1.5 px-2 w-20 text-center">租赁期限 /<br>Durasi Rental</th>
                    @endif
                    <th class="py-1.5 px-2 w-28 text-right">单价(Rp)<br>Harga satuan (Rp)</th>
                    <th class="py-1.5 px-2 w-28 text-right">金额(Rp)<br>Jumlah (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($order->items as $idx => $line)
                <tr>
                    <td class="text-center py-2 px-1 align-top">{{ $idx + 1 }}</td>
                    <td class="font-bold py-2 px-2 align-top text-black text-left">
                        {{ $line->description ?? ($line->item->name ?? '-') }}
                        @if(($line->transaction_type ?? '') === 'sales')
                            <span class="inline-block mt-0.5 px-1.5 py-0.5 rounded text-[8px] bg-emerald-50 text-emerald-800 border border-emerald-300 font-bold uppercase tracking-wider">Beli / 购买</span>
                        @elseif(($line->transaction_type ?? '') === 'rental')
                            <span class="inline-block mt-0.5 px-1.5 py-0.5 rounded text-[8px] bg-amber-50 text-amber-800 border border-amber-300 font-bold uppercase tracking-wider">Sewa / 租赁</span>
                        @endif
                    </td>
                    <td class="py-2 px-3 align-top leading-relaxed text-black text-left">
                        @if(!empty($line->specification))
                            {!! nl2br(e($line->specification)) !!}
                        @elseif($line->item && !empty($line->item->description))
                            {!! nl2br(e($line->item->description)) !!}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-center py-2 px-1 align-top">
                        <!-- Unit Thumbnail Picture -->
                        <div class="w-16 h-14 mx-auto bg-slate-50 border border-slate-300 rounded flex items-center justify-center overflow-hidden">
                            @if(!empty($line->image))
                                <img src="{{ asset($line->image) }}" alt="Unit Preview" class="w-full h-full object-cover">
                            @elseif($line->item && !empty($line->item->image))
                                <img src="{{ asset($line->item->image) }}" alt="Unit Preview" class="w-full h-full object-cover">
                            @else
                                <img src="{{ asset('images/keylock-logo.svg') }}" alt="Unit Preview" class="w-10 h-10 object-contain opacity-60">
                            @endif
                        </div>
                    </td>
                    <td class="text-center py-2 px-1 align-top font-bold">{{ $line->quantity }}</td>
                    @if($order->quotation_type !== 'sales')
                    <td class="text-center py-2 px-2 align-top font-semibold">
                        @if(($line->transaction_type ?? 'rental') === 'sales')
                            <span class="text-slate-400 font-normal">-</span>
                        @else
                            {{ $line->duration }} {{ $line->duration_unit ?? 'Bulan' }}
                        @endif
                    </td>
                    @endif
                    <td class="text-right py-2 px-2 align-top font-mono">
                        Rp {{ number_format($line->unit_price, 0, ',', '.') }}
                    </td>
                    <td class="text-right py-2 px-2 align-top font-mono font-bold">
                        Rp {{ number_format($line->line_total, 0, ',', '.') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td class="text-center py-2 px-1 align-top">1</td>
                    <td class="font-bold py-2 px-2 align-top text-black text-left">Forklift EP Electric ELF353</td>
                    <td class="py-2 px-3 align-top leading-relaxed text-black text-left">
                        电动叉车，载重3.5吨（提升高度3米），配备80V/280Ah锂电池，实心橡胶轮胎。/ Electric Forklift Capacity 3.5 Ton (Lift Height 3 Meter), Battery Lithium-Ion 80V/280Ah, Tyre Solid Rubber<br><br>
                        <strong>包含 / Include :</strong><br>
                        制造商保修（1年官方制造商保修）及电池充电器 / Manufacturer Warranty (Garansi 1 Tahun Resmi Pabrikan) & Charger Battery
                    </td>
                    <td class="text-center py-2 px-1 align-top">
                        <div class="w-16 h-14 mx-auto bg-slate-50 border border-slate-300 rounded flex items-center justify-center">
                            <img src="{{ asset('images/keylock-logo.svg') }}" class="w-10 h-10 object-contain opacity-70">
                        </div>
                    </td>
                    <td class="text-center py-2 px-1 align-top font-bold">1 Unit</td>
                    @if($order->quotation_type !== 'sales')
                    <td class="text-center py-2 px-2 align-top font-semibold">1 Bulan</td>
                    @endif
                    <td class="text-right py-2 px-2 align-top font-mono">Rp {{ number_format($order->subtotal > 0 ? $order->subtotal : $order->total_amount, 0, ',', '.') }}</td>
                    <td class="text-right py-2 px-2 align-top font-mono font-bold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- ================================================================= -->
        <!-- BOTTOM SECTION: BILINGUAL TERMS (LEFT) & TOTALS/BANK (RIGHT)       -->
        <!-- ================================================================= -->
        <table class="w-full commercial-table text-[9.5px]">
            <tbody>
                <tr>
                    <!-- LEFT COLUMN: 8 OFFICIAL TERMS & CONDITIONS (BILINGUAL) -->
                    <td class="w-[62%] p-3 align-top leading-tight space-y-2 border-r border-black">
                        @php
                            $validityDateFormatted = $order->valid_until ? \Carbon\Carbon::parse($order->valid_until)->format('d/m/Y') : null;
                            $validityDays = 30;
                            if ($order->order_date && $order->valid_until) {
                                $startDate = \Carbon\Carbon::parse($order->order_date)->startOfDay();
                                $endDate = \Carbon\Carbon::parse($order->valid_until)->startOfDay();
                                $validityDays = max(1, $startDate->diffInDays($endDate));
                            }
                        @endphp
                        <div>
                            <p class="font-bold text-black">1、价格有效期：本报价自报价日期起有效期为 {{ $validityDays }} 天 @if($validityDateFormatted)（至 {{ $validityDateFormatted }} 止）@endif，逾期需重新确认价格</p>
                            <p class="text-black"><strong>Masa Berlaku Harga:</strong> Penawaran harga ini berlaku selama {{ $validityDays }} hari sejak tanggal penawaran @if($validityDateFormatted)(hingga tanggal {{ $validityDateFormatted }})@endif. Apabila melewati masa tersebut, harga perlu dikonfirmasi ulang.</p>
                        </div>

                        <div>
                            <p class="font-bold text-black">2、付款条件：在正常情况下，买方必须先支付50%的款项。余款在收到货物后3天内支付。其他付款方式可以进一步商讨，必须达成一致并书面确认。</p>
                            <p class="text-black"><strong>Syarat Pembayaran:</strong> Dalam kondisi normal, pembeli wajib melakukan pembayaran 50% terlebih dahulu. Untuk Pelunasan dibayarkan 3 hari setelah barang diterima. Metode pembayaran lainnya dapat dibicarakan lebih lanjut dan harus disepakati serta dikonfirmasi secara tertulis.</p>
                        </div>

                        <div>
                            <p class="font-bold text-black">3、发货时间：库存状态为有货的产品将在全额付款后1 - 3个工作日内发货。如果发生缺货或其他特殊情况，卖家将立即通知买家并协调寻找解决方案。</p>
                            <p class="text-black"><strong>Waktu Pengiriman:</strong> Produk yang berstatus stok tersedia akan dikirimkan dalam waktu 1 - 3 hari kerja setelah pembayaran uang muka. Apabila terjadi kekurangan stok atau kondisi khusus lainnya, penjual akan segera memberitahukan pembeli dan berkoordinasi untuk mencari solusi.</p>
                        </div>

                        <div>
                            <p class="font-bold text-black">4、运输/运输条款：除非双方另有约定，价格已包含运费，自货物交付给承运人起，货物损坏或丢失的风险转移给买方</p>
                            <p class="text-black"><strong>Ketentuan Pengiriman / Transportasi:</strong> Kecuali disepakati lain oleh kedua belah pihak, harga sudah termasuk biaya pengiriman, dan risiko kerusakan atau kehilangan barang beralih kepada pembeli sejak barang diserahkan kepada pihak pengangkut.</p>
                        </div>

                        <div>
                            <p class="font-bold text-black">5、质量要求 : 产品须符合双方确认的技术规格、样品或书面描述中的质量要求，如果报价规格与实际需求不符或存在差异，买方应在下单前与相关人员沟通协商，并及时修改。</p>
                            <p class="text-black"><strong>Persyaratan Kualitas:</strong> Produk harus memenuhi spesifikasi teknis, contoh (sample), atau deskripsi tertulis yang telah disepakati oleh kedua belah pihak. Apabila spesifikasi dalam penawaran tidak sesuai atau terdapat perbedaan dengan kebutuhan aktual, pembeli wajib berkoordinasi dan melakukan klarifikasi dengan pihak terkait sebelum melakukan pemesanan, serta melakukan penyesuaian.</p>
                        </div>

                        <div>
                            <p class="font-bold text-black">6、不可抗力：如因不可抗力事件（包括但不限于自然灾害、战争、政府政策变化等）导致交付延迟或无法履行合同，双方同意友好协商解决方案，且不承担因不可抗力造成的责任。</p>
                            <p class="text-black"><strong>Keadaan Kahar (Force Majeure):</strong> Apabila terjadi keadaan kahar (termasuk namun tidak terbatas pada bencana alam, perang, perubahan kebijakan pemerintah, dan kejadian lain di luar kendali para pihak) yang mengakibatkan keterlambatan pengiriman atau ketidakmampuan pelaksanaan kontrak, maka kedua belah pihak sepakat untuk menyelesaikannya melalui musyawarah, dan tidak bertanggung jawab atas kerugian yang timbul akibat keadaan kahar tersebut.</p>
                        </div>

                        <div>
                            <p class="font-bold text-black">7、税务说明：本报价已包含增值税（PPN）。但是，如果有其他税费或关税，则由买方承担。具体税务规定将在合同中进一步规定。</p>
                            <p class="text-black"><strong>Keterangan Pajak:</strong> Penawaran harga ini sudah termasuk Pajak Pertambahan Nilai (PPN). Namun, apabila terdapat pajak lain atau bea masuk, maka menjadi tanggungan pembeli. Ketentuan pajak secara rinci akan diatur lebih lanjut dalam kontrak.</p>
                        </div>

                        <div>
                            <p class="font-bold text-black">8、其他条款：不包括产品安装。尚未规定或与本报价条款相抵触的事项，将参照双方已签署的最终合同。</p>
                            <p class="text-black"><strong>Ketentuan Lain-lain:</strong> Tidak termasuk instalasi produk. Segala hal yang belum diatur atau bertentangan dengan ketentuan dalam penawaran ini, akan mengacu pada kontrak final yang telah disepakati bersama.</p>
                        </div>
                    </td>

                    <!-- RIGHT COLUMN: FINANCIAL TOTALS + PAYMENT METHOD + OFFICIAL STAMP -->
                    <td class="w-[38%] align-top p-0">
                        <!-- Subtotal, PPN, Total -->
                        <table class="w-full text-[10px] commercial-table mb-2">
                            <tbody>
                                <tr>
                                    <td class="p-1.5 font-bold bg-slate-50">小计 SUBTOTAL</td>
                                    <td class="p-1.5 text-right font-mono font-bold">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</td>
                                </tr>
                                @if($order->tax_amount > 0)
                                <tr>
                                    <td class="p-1.5 font-bold bg-slate-50">税金 PPN {{ $order->tax_percent > 0 ? (int)$order->tax_percent.'%' : '11%' }}</td>
                                    <td class="p-1.5 text-right font-mono font-bold">Rp {{ number_format($order->tax_amount, 0, ',', '.') }}</td>
                                </tr>
                                @else
                                <tr>
                                    <td class="p-1.5 font-bold bg-slate-50 text-slate-500">税金 PPN (Non-PPN)</td>
                                    <td class="p-1.5 text-right font-mono text-slate-500">Rp 0</td>
                                </tr>
                                @endif
                                @if(!empty($order->pph_amount) && $order->pph_amount > 0)
                                <tr>
                                    <td class="p-1.5 font-bold bg-slate-50 text-rose-700">预扣所得税 PPh 23 ({{ (int)($order->pph_percent ?? 2) }}%)</td>
                                    <td class="p-1.5 text-right font-mono font-bold text-rose-700">- Rp {{ number_format($order->pph_amount, 0, ',', '.') }}</td>
                                </tr>
                                @endif
                                @if(($order->shipping_delivery_fee ?? 0) > 0 || ($order->shipping_return_fee ?? 0) > 0)
                                <tr>
                                    <td class="p-1.5 font-bold bg-slate-50">发车运费 BIAYA BERANGKAT</td>
                                    <td class="p-1.5 text-right font-mono">Rp {{ number_format($order->shipping_delivery_fee ?? 0, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td class="p-1.5 font-bold bg-slate-50">回程运费 BIAYA PENGEMBALIAN</td>
                                    <td class="p-1.5 text-right font-mono">Rp {{ number_format($order->shipping_return_fee ?? 0, 0, ',', '.') }}</td>
                                </tr>
                                @else
                                <tr>
                                    <td class="p-1.5 font-bold bg-slate-50">运费 BIAYA KIRIM</td>
                                    <td class="p-1.5 text-right font-mono">Rp {{ number_format($order->shipping_fee ?? 0, 0, ',', '.') }}</td>
                                </tr>
                                @endif
                                <tr class="bg-yellow-50 font-black">
                                    <td class="p-2 font-bold text-black">总额 TOTAL</td>
                                    <td class="p-2 text-right font-mono text-xs text-black">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- Payment Method Box -->
                        <div class="p-2.5 text-[9.5px] border-b border-black leading-tight bg-slate-50/50">
                            <p class="font-bold text-black">支付方式</p>
                            <p class="text-slate-600">Metode Pembayaran:</p>
                            <p class="text-black mt-1">请通过银行转账支付<br>Silakan lakukan pembayaran ke:</p>
                            <p class="font-bold text-black mt-1">收款人: KEY LOCK INDONESIA</p>
                            <div class="mt-1 font-mono text-black space-y-0.5">
                                <p><strong>BCA: 7260555116</strong><br><span class="text-[8.5px] text-slate-500 font-sans">BCA KCP Grand Wisata</span></p>
                                <p><strong>BRI: 609900978</strong><br><span class="text-[8.5px] text-slate-500 font-sans">BRI Biro Klasifikasi Indonesia</span></p>
                            </div>
                            <p class="text-[9px] text-slate-500 mt-1">Mohon konfirmasi pembayaran melalui email:<br><span class="font-mono text-black font-semibold">{{ $company['email'] }}</span></p>
                        </div>

                        <!-- Official Signature & Stamp Box -->
                        <div class="p-3 text-center">
                            <p class="font-bold text-black text-[10px] tracking-tight">PT KEY LOCK INDONESIA</p>
                            
                            <!-- Stamp & Logo Area -->
                            <div class="my-2 relative flex items-center justify-center">
                                <img src="{{ asset('images/keylock-logo.svg') }}" class="w-12 h-12 object-contain opacity-40">
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <span class="text-[9px] font-black uppercase text-amber-700/60 border border-amber-600/60 px-2 py-0.5 rounded rotate-[-12deg]">
                                        KEY LOCK INDONESIA
                                    </span>
                                </div>
                            </div>

                            <p class="font-bold text-black text-[10px] underline underline-offset-2">QI SHUJUN 齐书骏</p>
                            <p class="text-[9px] text-slate-700">Presiden Direktur 董事长</p>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Page Footer -->
        <div class="mt-4 pt-2 border-t border-slate-300 flex items-center justify-between text-[9px] text-slate-500">
            <span>PT KEY LOCK INDONESIA • Mini-ERP System</span>
            <span>第1页，共1页</span>
        </div>

    </div>

</body>
</html>
