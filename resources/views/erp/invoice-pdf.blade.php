<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $invoice->invoice_number }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+SC:wght@400;500;700;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', 'Noto Sans SC', Arial, sans-serif;
            background-color: #F8FAFC;
            color: #000000;
            font-size: 11px;
            line-height: 1.35;
        }
        @media print {
            body {
                background-color: #FFFFFF !important;
                color: #000000 !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .page-container {
                padding: 10mm 12mm !important;
                margin: 0 auto !important;
                max-width: 100% !important;
                box-shadow: none !important;
                border: none !important;
                min-height: 100vh;
            }
            .page-break {
                page-break-before: always !important;
                break-before: page !important;
            }
            @page {
                size: A4;
                margin: 0;
            }
        }
        .page-break {
            page-break-before: always;
            break-before: page;
        }
        table.commercial-table {
            border: 1px solid #000000;
            border-collapse: collapse;
            width: 100%;
        }
        table.commercial-table th, table.commercial-table td {
            border: 1px solid #000000;
        }
    </style>
</head>
<body class="py-6 antialiased text-black">

    <!-- Top Action Toolbar (Hidden during print) -->
    <div class="no-print max-w-4xl mx-auto mb-4 px-4 flex items-center justify-between">
        <a href="{{ route('erp.finance.index', ['tab' => 'invoices']) }}" class="inline-flex items-center space-x-2 text-xs font-bold text-slate-700 hover:text-slate-900 bg-white px-4 py-2 rounded-xl shadow-xs border border-slate-200 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Kembali ke Finance / Invoicing</span>
        </a>

        <div class="flex items-center space-x-3">
            <button onclick="window.print()" class="inline-flex items-center space-x-2 bg-yellow-400 hover:bg-yellow-300 text-slate-950 font-black text-xs px-5 py-2 rounded-xl shadow-md transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span>Cetak / Simpan PDF</span>
            </button>
        </div>
    </div>

    @php
        $bulanIndo = [
            1 => 'JANUARI', 2 => 'FEBRUARI', 3 => 'MARET', 4 => 'APRIL', 5 => 'MEI', 6 => 'JUNI',
            7 => 'JULI', 8 => 'AGUSTUS', 9 => 'SEPTEMBER', 10 => 'OKTOBER', 11 => 'NOVEMBER', 12 => 'DESEMBER'
        ];
        $tanggalTerbitFormatted = $invoice->issue_date 
            ? $invoice->issue_date->format('d') . ' ' . ($bulanIndo[(int)$invoice->issue_date->format('n')] ?? '') . ' ' . $invoice->issue_date->format('Y')
            : date('d F Y');

        $rentalPeriod = '';
        if ($invoice->order && $invoice->order->rental_start_date) {
            $startDate = \Carbon\Carbon::parse($invoice->order->rental_start_date)->format('d/m/Y');
            $endDate = $invoice->order->rental_end_date ? \Carbon\Carbon::parse($invoice->order->rental_end_date)->format('d/m/Y') : '';
            $rentalPeriod = $endDate ? "{$startDate}–{$endDate}" : $startDate;
        }

        $items = collect();
        if ($invoice->orders && $invoice->orders->count() > 0) {
            foreach ($invoice->orders as $linkedOrd) {
                foreach ($linkedOrd->items as $itm) {
                    $prefix = ($invoice->orders->count() > 1) ? "[{$linkedOrd->order_number}] " : '';
                    $itm->display_description = $prefix . ($itm->description ?: ($itm->item->name ?? 'Produk / Jasa'));
                    $items->push($itm);
                }
            }
        } elseif ($invoice->order && $invoice->order->items && $invoice->order->items->count() > 0) {
            foreach ($invoice->order->items as $itm) {
                $itm->display_description = $itm->description ?: ($itm->item->name ?? 'Produk / Jasa');
                $items->push($itm);
            }
        }

        if ($items->isEmpty()) {
            $items = collect([
                (object)[
                    'description' => $invoice->notes ?: 'Layanan Rental & Jasa Alat Berat',
                    'display_description' => $invoice->notes ?: 'Layanan Rental & Jasa Alat Berat',
                    'quantity' => 1,
                    'unit_price' => $invoice->subtotal,
                    'line_total' => $invoice->subtotal
                ]
            ]);
        }
    @endphp

    <!-- ========================================================================= -->
    <!-- HALAMAN 1: LEMBAR INVOICE                                              -->
    <!-- ========================================================================= -->
    <div class="page-container max-w-4xl mx-auto bg-white p-10 border border-slate-300 shadow-md">

        <!-- 1. KOP SURAT RESMI PERUSAHAAN (LETTERHEAD) -->
        <div class="flex items-center justify-between pb-3.5 border-b-2 border-black">
            <!-- Logo Perusahaan -->
            <div class="w-32 shrink-0">
                <img src="{{ asset('images/keylock-logo.png') }}" alt="Keylock Logo" class="w-24 object-contain">
            </div>

            <!-- Identitas Lengkap Perusahaan (Kanan) -->
            <div class="text-right text-[11px] leading-tight text-black max-w-lg">
                <h2 class="font-black text-sm uppercase tracking-wider text-black">{{ $company['name'] ?? 'PT KEY LOCK INDONESIA' }}</h2>
                <p class="mt-1 text-black">
                    {{ $company['address'] ?? 'Jalan Arteri KIIC Rolling Hills Ruko Pacific Plaza No. 91, Desa Margakaya, Kecamatan Telukjambe Barat, Kabupaten Karawang 41361.' }}
                </p>
                <p class="mt-0.5 text-black">
                    Telp/WA: <strong>{{ $company['phone'] ?? '+62 811 2345 6789' }}</strong> &nbsp;|&nbsp; Email: <strong>{{ $company['email'] ?? 'keylockindo@outlook.com' }}</strong>
                </p>
                <p class="mt-0.5 font-bold text-black">
                    NPWP: {{ $company['npwp'] ?? '1000 0000 0573 6973' }}
                </p>
            </div>
        </div>

        <!-- 2. JUDUL INVOICE & KOTAK INFORMASI BILATERAL -->
        <div class="grid grid-cols-2 gap-6 mt-5 text-[11px]">
            <!-- Kiri: Kepada Yth (Customer / Bill To) -->
            <div class="space-y-1">
                <h1 class="text-xl font-black tracking-tight text-black uppercase mb-2">INVOICE</h1>
                <p class="font-bold text-slate-700 uppercase tracking-wider text-[10px]">TAGIHAN KEPADA (BILL TO):</p>
                <p class="font-black text-xs uppercase text-black">{{ $invoice->contact->name ?? '-' }}</p>
                <p class="text-black leading-snug">
                    {{ $invoice->contact->billing_address ?? $invoice->contact->shipping_address ?? $invoice->contact->address ?? '-' }}
                </p>
                @if($invoice->contact && $invoice->contact->phone)
                    <p class="text-black">Telp/PIC: {{ $invoice->contact->pic_name ? $invoice->contact->pic_name . ' (' . $invoice->contact->phone . ')' : $invoice->contact->phone }}</p>
                @endif
                <p class="font-bold text-black">NPWP: {{ $invoice->contact->npwp ?: '-' }}</p>
            </div>

            <!-- Kanan: Nomor Invoice & Detail Pembayaran -->
            <div class="flex flex-col justify-start items-end text-right">
                <div class="bg-slate-50 border border-black/30 rounded-lg p-3 w-full max-w-xs space-y-1.5 font-mono text-xs">
                    <div class="flex justify-between items-center border-b border-slate-200 pb-1">
                        <span class="font-sans font-bold text-slate-700 text-[10px] uppercase">No. Invoice:</span>
                        <span class="font-black text-black">{{ $invoice->invoice_number }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="font-sans font-medium text-slate-600 text-[10px] uppercase">Tanggal Terbit:</span>
                        <span class="font-bold text-black">{{ $tanggalTerbitFormatted }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="font-sans font-medium text-slate-600 text-[10px] uppercase">Jatuh Tempo:</span>
                        <span class="font-bold text-black">{{ $invoice->due_date ? $invoice->due_date->format('d') . ' ' . ($bulanIndo[(int)$invoice->due_date->format('n')] ?? '') . ' ' . $invoice->due_date->format('Y') : '-' }}</span>
                    </div>
                    @if($invoice->vendor_ref_no || ($invoice->order && $invoice->order->customer_po_number))
                        <div class="flex justify-between items-center">
                            <span class="font-sans font-medium text-slate-600 text-[10px] uppercase">No. PO Klien:</span>
                            <span class="font-bold text-black">{{ $invoice->vendor_ref_no ?: $invoice->order->customer_po_number }}</span>
                        </div>
                    @endif
                    @if($invoice->order)
                        <div class="flex justify-between items-center border-t border-slate-200 pt-1">
                            <span class="font-sans font-medium text-slate-600 text-[10px] uppercase">No. Transaksi:</span>
                            <span class="font-bold text-slate-800">#{{ $invoice->order->order_number }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- 4. Table Barang & Perhitungan Pajak -->
        <div class="mt-5">
            <table class="commercial-table text-[11px]">
                <thead>
                    <tr class="text-center font-bold">
                        <th class="py-1.5 px-2 w-10 text-center">No.</th>
                        <th class="py-1.5 px-3 text-center">Deskripsi Produk</th>
                        <th class="py-1.5 px-2 w-12 text-center">Qty</th>
                        <th class="py-1.5 px-3 w-36 text-center leading-tight">
                            <div>单价 (Rp) Harga</div>
                            <div>satuan (Rp)</div>
                        </th>
                        <th class="py-1.5 px-3 w-36 text-center leading-tight">
                            <div>金额(Rp) Jumlah</div>
                            <div>(Rp)</div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $idx => $line)
                        <tr>
                            <td class="text-center py-2 px-1 align-middle">{{ $idx + 1 }}</td>
                            <td class="text-center py-2 px-3 align-middle font-bold text-black uppercase leading-tight">
                                {{ $line->display_description ?? $line->description }}
                                @if(!empty($rentalPeriod))
                                    <span class="block text-[10px] font-normal tracking-wide mt-0.5">{{ $rentalPeriod }}</span>
                                @endif
                            </td>
                            <td class="text-center py-2 px-1 align-middle font-bold">{{ (int)$line->quantity }}</td>
                            <td class="text-center py-2 px-3 align-middle font-mono font-medium">Rp{{ number_format($line->unit_price, 0, ',', '.') }}</td>
                            <td class="text-center py-2 px-3 align-middle font-mono font-bold">Rp{{ number_format($line->line_total, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach

                    @if(($invoice->shipping_delivery_fee ?? 0) > 0 || ($invoice->shipping_return_fee ?? 0) > 0)
                        <!-- Baris Biaya Kirim Berangkat -->
                        <tr>
                            <td colspan="3" class="text-center py-1 px-3 font-bold text-[10px] uppercase">
                                BIAYA KIRIM BERANGKAT
                            </td>
                            <td colspan="2" class="text-center py-1 px-3 font-mono font-medium">
                                Rp{{ number_format($invoice->shipping_delivery_fee ?? 0, 0, ',', '.') }}
                            </td>
                        </tr>
                        <!-- Baris Biaya Kirim Pengembalian -->
                        <tr>
                            <td colspan="3" class="text-center py-1 px-3 font-bold text-[10px] uppercase">
                                BIAYA KIRIM PENGEMBALIAN
                            </td>
                            <td colspan="2" class="text-center py-1 px-3 font-mono font-medium">
                                Rp{{ number_format($invoice->shipping_return_fee ?? 0, 0, ',', '.') }}
                            </td>
                        </tr>
                    @else
                        <!-- Baris Biaya Kirim Pulang Pergi -->
                        <tr>
                            <td colspan="3" class="text-center py-1 px-3 font-bold text-[10px] uppercase">
                                BIAYA KIRIM PULANG PERGI
                            </td>
                            <td colspan="2" class="text-center py-1 px-3 font-mono font-medium">
                                Rp{{ number_format($invoice->shipping_fee ?? 0, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endif

                    <!-- Baris PPN -->
                    <tr>
                        <td colspan="3" class="text-center py-1 px-3 font-bold text-[10px] uppercase">
                            PPN {{ (int)($invoice->tax_percent > 0 ? $invoice->tax_percent : 11) }}%
                        </td>
                        <td colspan="2" class="text-center py-1 px-3 font-mono font-medium">
                            Rp{{ number_format($invoice->tax_amount ?? 0, 0, ',', '.') }}
                        </td>
                    </tr>

                    <!-- Baris PPh 23 (Jika Ada, Tampil Merah Italic) -->
                    @if(($invoice->pph_amount ?? 0) > 0)
                        <tr>
                            <td colspan="3" class="text-center py-1 px-3 font-bold text-[10px] uppercase">
                                PPH 23
                            </td>
                            <td colspan="2" class="text-center py-1 px-3 font-mono font-bold italic text-rose-600">
                                Rp{{ number_format($invoice->pph_amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endif

                    <!-- Baris GRAND TOTAL -->
                    <tr class="font-black text-[11px]">
                        <td colspan="3" class="text-center py-1.5 px-3 uppercase tracking-wider">
                            GRAND TOTAL
                        </td>
                        <td colspan="2" class="text-center py-1.5 px-3 font-mono font-black text-[12px]">
                            Rp{{ number_format($invoice->total_amount ?? 0, 0, ',', '.') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- 5. Bank Accounts Info -->
        <div class="mt-4 text-[10px] leading-snug space-y-0.5">
            <p class="font-bold text-black">BCA : 7285865115</p>
            <p class="text-black">BCA KCP Grand Wisata</p>
            <p class="font-bold text-black mt-1">BNI : 6099609978</p>
            <p class="text-black">BNI Biro Kalsifikasi Indonesia</p>
            <p class="text-black pt-1">Mohon konfirmasi pembayaran melalui email: <span class="font-medium text-black">keylockindo@outlook.com</span></p>
            <p class="text-black">请通过电子邮件确认付款: <span class="font-medium text-black">keylockindo@outlook.com</span></p>
        </div>

        <!-- 6. Catatan (Bilingual ID / ZH) -->
        <div class="mt-5 text-[9.5px] leading-relaxed text-black">
            <p class="font-bold">Catatan:</p>
            <p class="mt-0.5">- Ketentuan pembayaran: Dalam kondisi normal, penyewa wajib melakukan pembayaran 100% . Metode pembayaran lainnya dapat dibicarakan lebih lanjut dan harus disepakati serta dikonfirmasi secara tertulis</p>
            <p class="text-slate-800">付款条款: 通常情况下，租户需支付100%的租金。其他付款方式可另行商议，但须以书面形式达成一致并确认。</p>
            
            <p class="mt-1">- Harga diatas sudah termasuk PPN {{ (int)($invoice->tax_percent > 0 ? $invoice->tax_percent : 12) }}%.</p>
            <p class="text-slate-800">上面的价格已经包含{{ (int)($invoice->tax_percent > 0 ? $invoice->tax_percent : 12) }}%的增值税。</p>
            
            <p class="mt-1">- Barang yang telah diterima tidak dapat dikembalikan.</p>
            <p class="text-slate-800">已收到的商品不能退回。</p>
            
            <p class="mt-1">- Invoice ini sah tanpa tanda tangan basah.</p>
            <p class="text-slate-800">这张发票不需要手写签名也有效。</p>
        </div>

        <!-- 7. Tanda Tangan & Cap Resmi Keylock -->
        <div class="mt-6 flex justify-start">
            <div class="text-left">
                <p class="text-[11px] font-normal text-black">Dikeluarkan oleh,</p>
                <p class="text-[11px] font-extrabold text-black">PT Key Lock Indonesia</p>
                
                <!-- Blue Signature Stamp Graphic -->
                <div class="my-1.5 -ml-1">
                    <img src="{{ asset('images/keylock-blue-stamp.svg') }}" alt="Cap dan Tanda Tangan Resmi" class="h-16 object-contain">
                </div>

                <p class="text-[11px] font-bold uppercase tracking-wider text-black mt-1">QI SHUJUN</p>
            </div>
        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- HALAMAN 2: DOKUMEN FAKTUR PAJAK (LAMPIRAN RESMI)                          -->
    <!-- ========================================================================= -->
    @if(!empty($invoice->tax_invoice_document))
        <div class="page-break"></div>

        <div class="page-container max-w-4xl mx-auto bg-white p-10 border border-slate-300 shadow-md mt-6 print:mt-0 print:border-none print:shadow-none">
            
            <!-- Header Halaman 2 -->
            <div class="border-b-2 border-black pb-3 mb-6 flex items-start justify-between">
                <div>
                    <h2 class="text-sm font-black uppercase text-black tracking-wider">LAMPIRAN DOKUMEN FAKTUR PAJAK</h2>
                    <p class="text-[10px] text-slate-600 mt-0.5">Lampiran Resmi Faktur Pajak Pertambahan Nilai (PPN) / e-Faktur Pajak</p>
                </div>
                <div class="text-right text-[11px] font-mono leading-tight">
                    <p><span class="text-slate-500 font-sans">No. Invoice: </span><strong>{{ $invoice->invoice_number }}</strong></p>
                    <p><span class="text-slate-500 font-sans">Klien: </span><strong>{{ $invoice->contact->name ?? '-' }}</strong></p>
                </div>
            </div>

            @php
                $ext = strtolower(pathinfo($invoice->tax_invoice_document, PATHINFO_EXTENSION));
                $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
            @endphp

            @if($isImage)
                <!-- Tampilan Gambar Faktur Pajak -->
                <div class="flex justify-center items-center py-4">
                    <img src="{{ asset('storage/' . $invoice->tax_invoice_document) }}" alt="Dokumen Faktur Pajak" class="max-w-full max-h-[82vh] object-contain border border-slate-300 rounded shadow-xs">
                </div>
            @else
                <!-- Tampilan Dokumen PDF Faktur Pajak -->
                <div class="w-full h-[82vh] border border-slate-300 rounded overflow-hidden">
                    <iframe src="{{ asset('storage/' . $invoice->tax_invoice_document) }}" class="w-full h-full" frameborder="0"></iframe>
                </div>
            @endif

            <div class="mt-4 pt-2 border-t border-slate-200 flex items-center justify-between text-[10px] text-slate-500">
                <span>Dokumen Lampiran Resmi PT KEY LOCK INDONESIA</span>
                <span>Halaman 2 dari 2</span>
            </div>

        </div>
    @endif

</body>
</html>
