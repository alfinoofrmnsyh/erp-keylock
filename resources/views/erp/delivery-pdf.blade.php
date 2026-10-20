<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Jalan / 送货单 - {{ $delivery->delivery_number }}</title>
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
        <a href="{{ route('erp.orders.index') }}" class="inline-flex items-center space-x-2 text-xs font-bold text-slate-700 hover:text-slate-900 bg-white px-4 py-2 rounded-xl shadow-xs border border-slate-200 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Kembali ke Penawaran & Transaksi / 返回</span>
        </a>

        <div class="flex items-center space-x-3">
            <button onclick="window.print()" class="inline-flex items-center space-x-2 bg-yellow-400 hover:bg-yellow-300 text-slate-950 font-black text-xs px-5 py-2 rounded-xl shadow-md transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span>Cetak Surat Jalan / 打印送货单</span>
            </button>
        </div>
    </div>

    <!-- Official A4 Document Container -->
    <div class="page-container max-w-5xl mx-auto bg-white p-8 sm:p-10 border border-slate-300 shadow-lg">

        <!-- ================================================================= -->
        <!-- HEADER LETTERHEAD (LOGO & COMPANY DETAILS)                        -->
        <!-- ================================================================= -->
        <div class="flex items-start justify-between mb-4 border-b pb-4 border-black">
            <div class="flex items-start space-x-4">
                <div class="w-16 h-16 shrink-0 pt-0.5">
                    <img src="{{ asset('images/keylock-logo.svg') }}" alt="Keylock Logo" class="w-16 h-16 object-contain">
                </div>
                <div>
                    <h1 class="text-base font-black tracking-tight text-black uppercase font-mono">{{ $company['name'] }}</h1>
                    <p class="text-[11px] text-black max-w-lg mt-0.5 leading-tight">
                        {{ $company['address'] }}
                    </p>
                    <p class="text-[10px] text-slate-700 mt-0.5 font-bold">
                        NPWP: {{ $company['npwp'] }} &nbsp;|&nbsp; Telp: {{ $company['phone'] }} &nbsp;|&nbsp; Email: {{ $company['email'] }}
                    </p>
                </div>
            </div>

            <div class="text-right">
                <div class="inline-block border-2 border-black px-3 py-1 font-mono font-black text-sm uppercase bg-yellow-300/40">
                    SURAT JALAN / 送货单
                </div>
                <p class="text-xs font-mono font-bold text-slate-800 mt-2">DO No: {{ $delivery->delivery_number }}</p>
                <p class="text-[11px] text-slate-600">Tanggal / 日期: {{ $delivery->delivery_date ? $delivery->delivery_date->format('d/m/Y') : date('d/m/Y') }}</p>
            </div>
        </div>

        <!-- ================================================================= -->
        <!-- METADATA & RECIPIENT GRID                                         -->
        <!-- ================================================================= -->
        <div class="grid grid-cols-2 gap-6 text-[11px] text-black mb-5">
            <!-- Left: Customer, PIC & Destination -->
            <div class="border border-black p-3 rounded-xs space-y-1 bg-slate-50/50">
                <div class="font-bold uppercase tracking-wider text-[10px] border-b border-black pb-1 mb-1 text-slate-700">
                    DATA PENERIMA & PENGIRIMAN / 收货信息
                </div>
                <p><strong>No. DO / 送货单号:</strong> <span class="font-mono font-bold">{{ $delivery->delivery_number }}</span></p>
                <p><strong>Tanggal Pengiriman / 日期:</strong> {{ $delivery->delivery_date ? $delivery->delivery_date->format('d/m/Y') : date('d/m/Y') }}</p>
                <p><strong>Nama Pelanggan / 客户:</strong> {{ $delivery->contact->name ?? ($delivery->order?->contact?->name ?? '-') }}</p>
                <p><strong>Nama PIC / 责任人:</strong> {{ $delivery->recipient_name ?? ($delivery->contact?->pic_name ?? '-') }}</p>
                <p><strong>No. Telp PIC / 电话:</strong> {{ $delivery->recipient_phone ?: ($delivery->contact?->phone ?: '-') }}</p>
                <p><strong>Alamat Tujuan / 地址:</strong> {{ $delivery->destination_address ?? ($delivery->order?->shipping_address ?? '-') }}</p>
            </div>

            <!-- Right: Order Ref, Armada, Driver & Remarks -->
            <div class="border border-black p-3 rounded-xs space-y-1 bg-slate-50/50">
                <div class="font-bold uppercase tracking-wider text-[10px] border-b border-black pb-1 mb-1 text-slate-700">
                    INFORMASI ARMADA, DRIVER & REMAKS / 运输及备注
                </div>
                <p><strong>No. Order Referensi:</strong> {{ $delivery->order ? $delivery->order->order_number : '-' }}</p>
                <p><strong>Tipe Transaksi:</strong> 
                    @if($delivery->order && $delivery->order->order_type === 'rental_order')
                        <span class="font-bold text-blue-800">Rental / Sewa Unit Alat Berat (RO)</span>
                    @elseif($delivery->order && $delivery->order->order_type === 'sales_order')
                        <span class="font-bold text-purple-800">Penjualan Unit Alat Berat (SO)</span>
                    @else
                        <span>Pengiriman Reguler</span>
                    @endif
                </p>
                <p><strong>Driver (Vendor) / 司机:</strong> {{ $delivery->driver_name ?: '-' }}</p>
                <p><strong>No. Plat / 车牌号:</strong> <span class="font-mono font-bold">{{ $delivery->vehicle_number ?: '-' }}</span></p>
                <p><strong>Remaks / 备注:</strong> {{ $delivery->notes ?: '-' }}</p>
            </div>
        </div>

        <p class="text-[11px] font-bold mb-2">
            Mohon diterima dengan baik sejumlah unit alat berat / perlengkapan berikut ini:
        </p>

        <!-- ================================================================= -->
        <!-- DELIVERY ITEMS TABLE                                              -->
        <!-- ================================================================= -->
        <table class="commercial-table w-full mb-5 text-[11px]">
            <thead>
                <tr class="bg-slate-100 text-center font-bold">
                    <th class="py-2 px-2 w-10">NO<br>序号</th>
                    <th class="py-2 px-3 text-left">NAMA BARANG / DESKRIPSI ALAT BERAT<br>产品名称及规格</th>
                    <th class="py-2 px-2 w-20">JUMLAH<br>数量</th>
                    <th class="py-2 px-2 w-20">SATUAN<br>单位</th>
                    <th class="py-2 px-4 text-left">REMAKS<br>备注</th>
                </tr>
            </thead>
            <tbody>
                @forelse($delivery->items as $index => $item)
                    @php
                        $orderItem = $item->orderItem;
                        $itemName = $orderItem?->item?->name ?? $orderItem?->description ?? 'Unit Alat Berat';
                        $unit = $orderItem?->unit ?? $orderItem?->item?->unit ?? 'Unit';
                    @endphp
                    <tr>
                        <td class="py-3 px-2 text-center font-bold">{{ $index + 1 }}</td>
                        <td class="py-3 px-3">
                            <div class="font-extrabold text-black text-xs">{{ $itemName }}</div>
                            @if($orderItem && $orderItem->duration && $delivery->order?->order_type === 'rental_order')
                                <div class="text-[10px] text-slate-600">Durasi Sewa: {{ $orderItem->duration }} {{ $orderItem->duration_unit ?? 'Bulan' }}</div>
                            @endif
                        </td>
                        <td class="py-3 px-2 text-center font-black font-mono text-sm">
                            {{ number_format($item->quantity, 0) }}
                        </td>
                        <td class="py-3 px-2 text-center font-bold">
                            {{ $unit }}
                        </td>
                        <td class="py-3 px-4">
                            <div class="font-medium text-slate-800">{{ $item->inspection_notes ?? $delivery->notes ?? 'Kondisi Baik / Siap Operasi' }}</div>
                        </td>
                    </tr>
                @empty
                    @if($delivery->order && $delivery->order->items)
                        @foreach($delivery->order->items as $index => $orderItem)
                            <tr>
                                <td class="py-3 px-2 text-center font-bold">{{ $index + 1 }}</td>
                                <td class="py-3 px-3">
                                    <div class="font-extrabold text-black text-xs">{{ $orderItem->item?->name ?? $orderItem->description }}</div>
                                </td>
                                <td class="py-3 px-2 text-center font-black font-mono text-sm">
                                    {{ number_format($orderItem->quantity, 0) }}
                                </td>
                                <td class="py-3 px-2 text-center font-bold">
                                    {{ $orderItem->unit ?? 'Unit' }}
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-medium text-slate-800">Kondisi Baik / Siap Operasi</div>
                                    <div class="text-[10px] text-slate-500 mt-0.5">Unit telah melalui pra-inspeksi operasional.</div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5" class="py-4 text-center text-slate-500 italic">Tidak ada rincian unit pengiriman.</td>
                        </tr>
                    @endif
                @endforelse
            </tbody>
        </table>

        <!-- ================================================================= -->
        <!-- INSTRUCTIONS & SPECIAL NOTES                                      -->
        <!-- ================================================================= -->
        <div class="border border-black p-3 rounded-xs mb-6 text-[10px] space-y-1">
            <p class="font-bold uppercase tracking-wider text-slate-800">Catatan & Syarat Serah Terima / 交付须知 :</p>
            <p>1. Surat Jalan ini merupakan bukti resmi penyerahan fisik barang/unit dari pihak <strong>{{ $company['name'] }}</strong> kepada Klien.</p>
            <p>2. Barang/unit telah diperiksa dan diserahkan dalam keadaan lengkap, berfungsi normal, serta memenuhi standar keselamatan kerja.</p>
            <p>3. Pihak penerima wajib menandatangani dan membubuhkan stempel resmi perusahaan pada kolom tanda terima di bawah ini.</p>
            <p>4. <strong>Catatan Khusus Pengiriman:</strong> {{ $delivery->notes ?? 'Tidak ada catatan tambahan.' }}</p>
        </div>

        <!-- ================================================================= -->
        <!-- 3 SIGNATURE BLOCKS                                                -->
        <!-- ================================================================= -->
        <div class="grid grid-cols-3 gap-4 text-center text-[11px] text-black">
            <!-- Box 1: Gudang / Logistik Keylock -->
            <div class="border border-black p-3 flex flex-col justify-between h-40">
                <div>
                    <p class="font-bold">Pengirim / Bagian Logistik</p>
                    <p class="text-[10px] text-slate-600">发货部门 (Logistics Keylock)</p>
                </div>
                <div>
                    <div class="w-24 h-12 border-b border-black mx-auto mb-1 flex items-end justify-center">
                        <span class="text-[10px] font-mono text-slate-400">Ttd / Stamp</span>
                    </div>
                    <p class="font-bold underline">( Staff Logistik & Gudang )</p>
                    <p class="text-[10px] text-slate-500">Tanggal: {{ date('d/m/Y') }}</p>
                </div>
            </div>

            <!-- Box 2: Driver Pengangkut -->
            <div class="border border-black p-3 flex flex-col justify-between h-40">
                <div>
                    <p class="font-bold">Supir Pengangkut</p>
                    <p class="text-[10px] text-slate-600">承运司机 (Transporter Driver)</p>
                </div>
                <div>
                    <div class="w-24 h-12 border-b border-black mx-auto mb-1 flex items-end justify-center">
                        <span class="text-[10px] font-mono text-slate-400">Ttd Driver</span>
                    </div>
                    <p class="font-bold underline">( {{ $delivery->driver_name ?: 'Driver Pengantar' }} )</p>
                    <p class="text-[10px] text-slate-500">No. Pol: {{ $delivery->vehicle_number ?: '-' }}</p>
                </div>
            </div>

            <!-- Box 3: Penerima / Customer -->
            <div class="border border-black p-3 flex flex-col justify-between h-40 bg-slate-50/50">
                <div>
                    <p class="font-bold">Penerima Barang / Klien</p>
                    <p class="text-[10px] text-slate-600">客户收货人 (Customer Recipient)</p>
                </div>
                <div>
                    <div class="w-28 h-12 border-b border-black mx-auto mb-1 flex items-end justify-center">
                        <span class="text-[10px] font-mono text-slate-400">Ttd & Cap Perusahaan</span>
                    </div>
                    <p class="font-bold underline">( {{ $delivery->recipient_name ?? 'Nama Terang & Stempel' }} )</p>
                    <p class="text-[10px] text-slate-500">Tanggal Terima: ___ / ___ / 2026</p>
                </div>
            </div>
        </div>

    </div>

</body>
</html>
