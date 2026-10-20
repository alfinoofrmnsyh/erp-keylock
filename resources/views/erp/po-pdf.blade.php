<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order / 采购订单 - {{ $po->order_number }}</title>
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
        <a href="{{ route('erp.procurement.index') }}" class="inline-flex items-center space-x-2 text-xs font-bold text-slate-700 hover:text-slate-900 bg-white px-4 py-2 rounded-xl shadow-xs border border-slate-200 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Kembali ke Pengadaan / 返回</span>
        </a>

        <div class="flex items-center space-x-3">
            <button onclick="window.print()" class="inline-flex items-center space-x-2 bg-yellow-400 hover:bg-yellow-300 text-slate-950 font-black text-xs px-5 py-2 rounded-xl shadow-md transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span>Cetak / Simpan PDF (打印/保存)</span>
            </button>
        </div>
    </div>

    <!-- Official A4 Document Container -->
    <div class="page-container max-w-5xl mx-auto bg-white p-8 sm:p-10 border border-slate-300 shadow-lg">

        <!-- HEADER LETTERHEAD -->
        <div class="flex items-start justify-between mb-4">
            <div class="flex items-start space-x-4">
                <div class="w-16 h-16 shrink-0 pt-0.5">
                    <img src="{{ asset('images/keylock-logo.svg') }}" alt="Keylock Logo" class="w-16 h-16 object-contain">
                </div>
                <div>
                    <h1 class="text-base font-black tracking-tight text-black uppercase font-mono">{{ $company['name'] ?? 'PT KEYLOCK INDONESIA PERKASA' }}</h1>
                    <p class="text-[11px] text-black max-w-md mt-0.5 leading-tight">
                        {{ $company['address'] ?? 'Kawasan Industri GIIC Cikarang Pusat, Bekasi - Jawa Barat' }}
                    </p>
                    <p class="text-[10px] text-slate-600 mt-0.5">
                        Telp: {{ $company['phone'] ?? '+62 812-9000-0000' }} | Email: {{ $company['email'] ?? 'sales@keylock.co.id' }}
                    </p>
                </div>
            </div>

            <div class="text-right">
                <span class="inline-block px-3 py-1 bg-purple-100 text-purple-900 border border-purple-300 rounded font-black text-xs font-mono mb-1">PURCHASE ORDER</span>
                <p class="text-xs font-mono font-bold text-slate-700">No. PO: {{ $po->order_number }}</p>
                <p class="text-[10px] text-slate-500">Tanggal PO: {{ $po->order_date ? $po->order_date->format('d/m/Y') : date('d/m/Y') }}</p>
                @if($po->parent)
                    <p class="text-[10px] font-mono font-bold text-blue-700">Ref Transaksi: {{ $po->parent->order_number }}</p>
                @endif
            </div>
        </div>

        <div class="border-t-2 border-b border-black py-2 mb-4 grid grid-cols-2 gap-4 text-[10px]">
            <div>
                <p class="font-bold text-black uppercase text-[11px] mb-1">Kepada Vendor / 供应商信息:</p>
                <p class="font-bold text-black text-xs">{{ $po->contact->name ?? 'Vendor Mitra' }}</p>
                <p class="text-slate-700">{{ $po->contact->billing_address ?? $po->contact->shipping_address ?? '-' }}</p>
                <p class="text-slate-700">PIC: <strong>{{ $po->contact->pic_name ?: '-' }}</strong> | Telp: {{ $po->contact->phone ?: '-' }}</p>
                @if(!empty($po->contact->npwp))
                    <p class="text-slate-700">NPWP: {{ $po->contact->npwp }}</p>
                @endif
            </div>

            <div class="text-right">
                <p class="font-bold text-black uppercase text-[11px] mb-1">Ketentuan Pengadaan / 采购条款:</p>
                <p>Kategori: <strong>{{ strtoupper(str_replace('_', ' ', $po->procurement_type)) }}</strong></p>
                <p>Syarat Pembayaran: <strong>{{ $po->payment_term ?: 'Net 30 Days' }}</strong></p>
                @if($po->rental_start_date)
                    <p>Periode Sewa: <strong>{{ $po->rental_start_date->format('d/m/Y') }}</strong> s/d <strong>{{ $po->rental_end_date ? $po->rental_end_date->format('d/m/Y') : '-' }}</strong> ({{ $po->rental_duration }} {{ ucfirst($po->rental_duration_unit ?: 'Bulan') }})</p>
                @endif
                @if($po->parent && $po->parent->contact)
                    <p class="text-slate-600 mt-1">Alokasi Customer: <strong>{{ $po->parent->contact->name }}</strong></p>
                @endif
            </div>
        </div>

        <!-- ITEMS TABLE -->
        <table class="w-full commercial-table mb-4 text-[10.5px]">
            <thead>
                <tr class="bg-slate-100 font-bold text-center">
                    <th class="py-2 px-1 w-8">NO</th>
                    <th class="py-2 px-3 text-left">DESKRIPSI PRODUK / JASA<br><span class="text-[9px] font-normal text-slate-500">产品与服务描述</span></th>
                    <th class="py-2 px-2 text-left">SPESIFIKASI<br><span class="text-[9px] font-normal text-slate-500">规格型号</span></th>
                    <th class="py-2 px-2 w-16">QTY<br><span class="text-[9px] font-normal text-slate-500">数量</span></th>
                    @if($po->procurement_type === 'sub_rental')
                        <th class="py-2 px-2 w-20">DURASI<br><span class="text-[9px] font-normal text-slate-500">租期</span></th>
                    @endif
                    <th class="py-2 px-3 text-right w-28">HARGA SATUAN (RP)<br><span class="text-[9px] font-normal text-slate-500">单价</span></th>
                    <th class="py-2 px-3 text-right w-28">TOTAL (RP)<br><span class="text-[9px] font-normal text-slate-500">金额</span></th>
                </tr>
            </thead>
            <tbody>
                @forelse($po->items as $index => $item)
                    <tr>
                        <td class="text-center py-2 px-1 align-top font-bold">{{ $index + 1 }}</td>
                        <td class="py-2 px-3 align-top">
                            <span class="font-bold text-black">{{ $item->description ?: ($item->item?->name ?? 'Produk') }}</span>
                            @if($item->notes)
                                <div class="text-[9.5px] text-slate-600 italic mt-0.5">{{ $item->notes }}</div>
                            @endif
                        </td>
                        <td class="py-2 px-2 align-top text-slate-700 font-mono">{{ $item->specification ?: '-' }}</td>
                        <td class="text-center py-2 px-2 align-top font-bold">{{ (float)$item->quantity }} {{ $item->unit ?: 'Unit' }}</td>
                        @if($po->procurement_type === 'sub_rental')
                            <td class="text-center py-2 px-2 align-top font-semibold">{{ $item->duration ?: ($po->rental_duration ?: 1) }} {{ $item->duration_unit ?: ($po->rental_duration_unit ?: 'Bulan') }}</td>
                        @endif
                        <td class="text-right py-2 px-3 align-top font-mono">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                        <td class="text-right py-2 px-3 align-top font-mono font-bold">Rp {{ number_format($item->line_total, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $po->procurement_type === 'sub_rental' ? 7 : 6 }}" class="text-center py-4 text-slate-400 italic">Tidak ada rincian item pengadaan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- TOTALS & TERMS -->
        <table class="w-full commercial-table text-[10px]">
            <tbody>
                <tr>
                    <!-- LEFT COLUMN: NOTES & TERMS -->
                    <td class="w-[60%] p-3 align-top space-y-2 border-r border-black">
                        <p class="font-bold text-black text-xs">Catatan & Ketentuan Pembelian / 采购说明与条款:</p>
                        @if($po->notes)
                            <p class="text-slate-800 whitespace-pre-line">{{ $po->notes }}</p>
                        @else
                            <p class="text-slate-700 leading-relaxed">
                                1. Surat Pesanan ini merupakan bukti pemesanan resmi dari PT Keylock Indonesia Perkasa kepada Vendor.<br>
                                2. Vendor wajib menyediakan unit/barang sesuai dengan spesifikasi teknis dan tanggal yang telah disepakati.<br>
                                3. Penagihan (Invoice/Faktur Pajak) dilampirkan bersama Surat Jalan (DO) dan salinan PO resmi ini.<br>
                                4. Pembayaran akan diproses sesuai dengan syarat pembayaran yang telah tercantum.
                            </p>
                        @endif
                    </td>

                    <!-- RIGHT COLUMN: FINANCIAL BREAKDOWN -->
                    <td class="w-[40%] align-top p-0">
                        <table class="w-full text-[10px] commercial-table">
                            <tbody>
                                <tr>
                                    <td class="p-2 font-bold bg-slate-50">Subtotal Pengadaan:</td>
                                    <td class="p-2 text-right font-mono font-bold">Rp {{ number_format($po->subtotal ?: $po->items->sum('line_total'), 0, ',', '.') }}</td>
                                </tr>
                                @if(($po->shipping_fee ?? 0) > 0)
                                    <tr>
                                        <td class="p-2 font-bold bg-slate-50">Ongkos Kirim / Logistik:</td>
                                        <td class="p-2 text-right font-mono font-bold text-blue-700">+ Rp {{ number_format($po->shipping_fee, 0, ',', '.') }}</td>
                                    </tr>
                                @endif
                                @if(($po->tax_amount ?? 0) > 0)
                                    <tr>
                                        <td class="p-2 font-bold bg-slate-50">PPN ({{ (int)($po->tax_percent ?: 11) }}%):</td>
                                        <td class="p-2 text-right font-mono font-bold text-emerald-700">+ Rp {{ number_format($po->tax_amount, 0, ',', '.') }}</td>
                                    </tr>
                                @endif
                                <tr class="bg-yellow-50 font-black">
                                    <td class="p-2.5 font-bold text-black text-xs">TOTAL NILAI PO:</td>
                                    <td class="p-2.5 text-right font-mono text-sm text-black">Rp {{ number_format($po->total_amount, 0, ',', '.') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- SIGNATURE BLOCK -->
        <div class="mt-6 pt-4 border-t border-black grid grid-cols-3 gap-6 text-center text-[10.5px]">
            <div>
                <p class="font-bold text-black mb-16">Dibuat Oleh (Purchasing):</p>
                <div class="border-b border-black w-36 mx-auto"></div>
                <p class="mt-1 font-bold text-slate-700">Procurement Officer</p>
            </div>
            <div>
                <p class="font-bold text-black mb-16">Disetujui Oleh (Direksi):</p>
                <div class="border-b border-black w-36 mx-auto"></div>
                <p class="mt-1 font-bold text-slate-900">{{ $company['director'] ?? 'Direktur Utama' }}</p>
                <p class="text-[9px] text-slate-500">PT KEYLOCK INDONESIA PERKASA</p>
            </div>
            <div>
                <p class="font-bold text-black mb-16">Diterima & Dikonfirmasi Vendor:</p>
                <div class="border-b border-black w-36 mx-auto"></div>
                <p class="mt-1 font-bold text-slate-900">{{ $po->contact->name ?? 'Vendor Mitra' }}</p>
                <p class="text-[9px] text-slate-500">Tanda Tangan & Cap Perusahaan</p>
            </div>
        </div>

    </div>

</body>
</html>
