<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan {{ ucfirst($tab) }} - PT KEY LOCK INDONESIA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        body {
            background-color: #f8fafc;
            color: #0f172a;
            margin: 0;
            padding: 24px;
            font-size: 11px;
            line-height: 1.4;
        }
        .page {
            background: white;
            max-width: 960px;
            margin: 0 auto;
            padding: 36px 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 16px;
            margin-bottom: 20px;
        }
        .logo-box {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .logo-box img {
            width: 44px;
            height: 44px;
            object-fit: contain;
        }
        .company-name {
            font-size: 16px;
            font-weight: 900;
            letter-spacing: -0.02em;
            color: #0f172a;
        }
        .company-meta {
            font-size: 10px;
            color: #64748b;
            margin-top: 2px;
            max-width: 380px;
        }
        .report-title-box {
            text-align: right;
        }
        .report-badge {
            display: inline-block;
            background: #fef08a;
            color: #854d0e;
            font-size: 9px;
            font-weight: 800;
            padding: 2px 8px;
            border-radius: 9999px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 4px;
        }
        .report-title {
            font-size: 15px;
            font-weight: 900;
            color: #0f172a;
            margin: 0;
        }
        .report-period {
            font-size: 10px;
            color: #64748b;
            margin-top: 4px;
        }
        .kpi-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 24px;
        }
        .kpi-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 12px;
            border-radius: 8px;
        }
        .kpi-label {
            font-size: 9px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .kpi-val {
            font-size: 14px;
            font-weight: 900;
            color: #0f172a;
            margin-top: 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10px;
        }
        th {
            background: #f1f5f9;
            color: #334155;
            font-weight: 800;
            text-align: left;
            padding: 8px 10px;
            border-bottom: 1px solid #cbd5e1;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            font-size: 9px;
        }
        td {
            padding: 7px 10px;
            border-bottom: 1px solid #f1f5f9;
            color: #1e293b;
        }
        tr:nth-child(even) {
            background: #fafafa;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-mono { font-family: monospace; }
        .font-bold { font-weight: 700; }
        .font-black { font-weight: 900; }
        .status-badge {
            display: inline-block;
            font-size: 8px;
            font-weight: 800;
            padding: 2px 6px;
            border-radius: 4px;
            text-transform: uppercase;
        }
        .bg-emerald { background: #dcfce7; color: #166534; }
        .bg-blue { background: #dbeafe; color: #1e40af; }
        .bg-amber { background: #fef3c7; color: #92400e; }
        .bg-slate { background: #f1f5f9; color: #475569; }
        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
            margin-top: 36px;
            padding-top: 16px;
            border-top: 1px dashed #cbd5e1;
            text-align: center;
        }
        .sig-space {
            height: 50px;
        }
        .print-btn-bar {
            text-align: center;
            margin-bottom: 20px;
        }
        .print-btn {
            background: #0f172a;
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 9999px;
            font-weight: 800;
            font-size: 12px;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .print-btn:hover { background: #334155; }
        @media print {
            body { background: white; padding: 0; }
            .page { box-shadow: none; padding: 0; }
            .print-btn-bar { display: none; }
        }
    </style>
</head>
<body>

    <div class="print-btn-bar">
        <button class="print-btn" onclick="window.print()">Cetak / Simpan ke PDF</button>
    </div>

    <div class="page">
        <!-- Header -->
        <div class="header">
            <div class="logo-box">
                <img src="/images/keylock-logo.svg" alt="Keylock Logo">
                <div>
                    <div class="company-name">{{ $company['name'] }}</div>
                    <div class="company-meta">{{ $company['address'] }}</div>
                    <div class="company-meta">Telp: {{ $company['phone'] }} | Email: {{ $company['email'] }}</div>
                </div>
            </div>
            <div class="report-title-box">
                <span class="report-badge">Official Report</span>
                <h1 class="report-title">
                    @if($tab === 'sales')
                        Laporan Penjualan & Sales CRM
                    @elseif($tab === 'operational')
                        Laporan Operasional & Unit Armada
                    @elseif($tab === 'owner')
                        Laporan Finansial & Profitabilitas Owner
                    @elseif($tab === 'incentive')
                        Laporan Insentif Sales & Komisi Penjualan
                    @endif
                </h1>
                <div class="report-period">
                    Periode: {{ date('d/m/Y', strtotime($startDate)) }} - {{ date('d/m/Y', strtotime($endDate)) }}
                </div>
            </div>
        </div>

        <!-- Sales Content -->
        @if($tab === 'sales')
            <div class="kpi-row">
                <div class="kpi-card">
                    <div class="kpi-label">Total Penawaran</div>
                    <div class="kpi-val">{{ count($data['quotations'] ?? []) }}</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-label">Deal Diterima (Won)</div>
                    <div class="kpi-val" style="color: #16a34a;">{{ $data['accepted_count'] ?? 0 }}</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-label">Total Nilai Penawaran</div>
                    <div class="kpi-val">Rp {{ number_format($data['total_amount'] ?? 0, 0, ',', '.') }}</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-label">Tanggal Cetak</div>
                    <div class="kpi-val">{{ date('d/m/Y H:i') }}</div>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th style="width: 24px;" class="text-center">No</th>
                        <th>No Penawaran</th>
                        <th>Tanggal</th>
                        <th>Nama Pelanggan</th>
                        <th>Sales Person</th>
                        <th class="text-center">Durasi</th>
                        <th class="text-right">Nilai Penawaran (IDR)</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @php $sumQ = 0; @endphp
                    @forelse($data['quotations'] as $i => $q)
                        @php $sumQ += (float) $q->total_amount; @endphp
                        <tr>
                            <td class="text-center">{{ $i + 1 }}</td>
                            <td class="font-bold font-mono">{{ $q->order_number }}</td>
                            <td>{{ $q->order_date?->format('d/m/Y') ?? '-' }}</td>
                            <td class="font-bold">{{ $q->contact?->name ?? 'Pelanggan Umum' }}</td>
                            <td>{{ $q->creator?->name ?? '-' }}</td>
                            <td class="text-center">{{ $q->rental_duration ?? 1 }} {{ $q->rental_duration_unit ?? 'Bulan' }}</td>
                            <td class="text-right font-bold font-mono">Rp {{ number_format((float) $q->total_amount, 0, ',', '.') }}</td>
                            <td class="text-center">
                                <span class="status-badge {{ $q->status === 'accepted' ? 'bg-emerald' : ($q->status === 'sent' ? 'bg-blue' : 'bg-slate') }}">
                                    {{ strtoupper($q->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center" style="padding: 24px; color: #94a3b8;">Tidak ada data penawaran.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr style="background: #f8fafc; font-weight: 800;">
                        <td colspan="6" class="text-right">TOTAL KESELURUHAN (IDR):</td>
                        <td class="text-right font-mono" style="font-size: 11px;">Rp {{ number_format($sumQ, 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        @endif

        <!-- Operational Content -->
        @if($tab === 'operational')
            <div class="kpi-row">
                <div class="kpi-card">
                    <div class="kpi-label">Total Armada Unit</div>
                    <div class="kpi-val">{{ count($data['assets'] ?? []) }}</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-label">Unit Disewa</div>
                    <div class="kpi-val" style="color: #16a34a;">{{ $data['assets']->where('status', 'rented')->count() }}</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-label">Unit Standby</div>
                    <div class="kpi-val" style="color: #2563eb;">{{ $data['assets']->whereIn('status', ['available', 'standby'])->count() }}</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-label">DO Terkirim</div>
                    <div class="kpi-val">{{ count($data['deliveries'] ?? []) }}</div>
                </div>
            </div>

            <h3 style="font-size: 11px; font-weight: 800; text-transform: uppercase; margin-bottom: 8px;">Daftar Status Unit Armada</h3>
            <table>
                <thead>
                    <tr>
                        <th style="width: 24px;" class="text-center">No</th>
                        <th>Kode Unit</th>
                        <th>Nama & Model</th>
                        <th>Kategori</th>
                        <th>Serial Number</th>
                        <th class="text-center">Hour Meter</th>
                        <th>Lokasi Terkini</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['assets'] as $ai => $asset)
                        <tr>
                            <td class="text-center">{{ $ai + 1 }}</td>
                            <td class="font-bold font-mono">{{ $asset->asset_code }}</td>
                            <td class="font-bold">{{ $asset->item?->name ?? 'Unit Forklift' }}</td>
                            <td>{{ $asset->item?->category ?? '-' }}</td>
                            <td class="font-mono">{{ $asset->serial_number ?? '-' }}</td>
                            <td class="text-center font-mono">{{ $asset->hour_meter ?? 0 }} HM</td>
                            <td>{{ $asset->current_location ?? 'Pool Karawang' }}</td>
                            <td class="text-center">
                                <span class="status-badge {{ $asset->status === 'rented' ? 'bg-emerald' : ($asset->status === 'available' ? 'bg-blue' : 'bg-amber') }}">
                                    {{ strtoupper($asset->status) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <!-- Owner Content -->
        @if($tab === 'owner')
            <div class="kpi-row">
                <div class="kpi-card">
                    <div class="kpi-label">Total Omset Rental</div>
                    <div class="kpi-val">Rp {{ number_format($data['total_revenue'] ?? 0, 0, ',', '.') }}</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-label">Total HPP Vendor Subkon</div>
                    <div class="kpi-val" style="color: #e11d48;">Rp {{ number_format($data['total_vendor_cost'] ?? 0, 0, ',', '.') }}</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-label">Estimasi Gross Profit</div>
                    <div class="kpi-val" style="color: #16a34a;">Rp {{ number_format($data['gross_profit'] ?? 0, 0, ',', '.') }}</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-label">Gross Margin %</div>
                    <div class="kpi-val">{{ ($data['total_revenue'] ?? 0) > 0 ? round((($data['gross_profit'] ?? 0) / $data['total_revenue']) * 100, 1) : 0 }}%</div>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th style="width: 24px;" class="text-center">No</th>
                        <th>No Kontrak</th>
                        <th>Pelanggan</th>
                        <th class="text-right">Omset Rental</th>
                        <th class="text-right">HPP Vendor</th>
                        <th class="text-right">Biaya Kirim</th>
                        <th class="text-right">Gross Profit</th>
                        <th class="text-center">Margin %</th>
                    </tr>
                </thead>
                <tbody>
                    @php 
                        $sRev = 0; $sVend = 0; $sLog = 0; $sProf = 0;
                    @endphp
                    @forelse($data['orders'] as $oi => $order)
                        @php
                            $rev = (float) $order->total_amount;
                            $vend = (float) $order->vendor_cost;
                            $log = (float) ($order->logistics_cost ?: $order->shipping_fee);
                            $prof = $rev - $vend - $log;
                            $mrg = $rev > 0 ? round(($prof / $rev) * 100, 1) : 0;
                            $sRev += $rev; $sVend += $vend; $sLog += $log; $sProf += $prof;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $oi + 1 }}</td>
                            <td class="font-bold font-mono">{{ $order->order_number }}</td>
                            <td class="font-bold">{{ $order->contact?->name ?? 'Pelanggan Umum' }}</td>
                            <td class="text-right font-mono font-bold">Rp {{ number_format($rev, 0, ',', '.') }}</td>
                            <td class="text-right font-mono" style="color: #e11d48;">Rp {{ number_format($vend, 0, ',', '.') }}</td>
                            <td class="text-right font-mono">Rp {{ number_format($log, 0, ',', '.') }}</td>
                            <td class="text-right font-mono font-bold" style="color: #16a34a;">Rp {{ number_format($prof, 0, ',', '.') }}</td>
                            <td class="text-center font-bold">{{ $mrg }}%</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center" style="padding: 24px; color: #94a3b8;">Belum ada kontrak rental.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr style="background: #f8fafc; font-weight: 800;">
                        <td colspan="3" class="text-right">TOTAL RINGKASAN (IDR):</td>
                        <td class="text-right font-mono">Rp {{ number_format($sRev, 0, ',', '.') }}</td>
                        <td class="text-right font-mono" style="color: #e11d48;">Rp {{ number_format($sVend, 0, ',', '.') }}</td>
                        <td class="text-right font-mono">Rp {{ number_format($sLog, 0, ',', '.') }}</td>
                        <td class="text-right font-mono" style="color: #16a34a;">Rp {{ number_format($sProf, 0, ',', '.') }}</td>
                        <td class="text-center">{{ $sRev > 0 ? round(($sProf / $sRev) * 100, 1) : 0 }}%</td>
                    </tr>
                </tfoot>
            </table>
        @endif

        <!-- Sales Incentive Content -->
        @if($tab === 'incentive')
            <div class="kpi-row">
                <div class="kpi-card">
                    <div class="kpi-label">Total Insentif Komisi</div>
                    <div class="kpi-val" style="color: #e11d48;">Rp {{ number_format($data['total_incentive'] ?? 0, 0, ',', '.') }}</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-label">Total Omset Transaksi</div>
                    <div class="kpi-val">Rp {{ number_format($data['total_revenue'] ?? 0, 0, ',', '.') }}</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-label">Insentif Sudah Cair</div>
                    <div class="kpi-val" style="color: #16a34a;">Rp {{ number_format($data['paid_incentive'] ?? 0, 0, ',', '.') }}</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-label">Insentif Menunggu Pelunasan</div>
                    <div class="kpi-val" style="color: #d97706;">Rp {{ number_format($data['pending_incentive'] ?? 0, 0, ',', '.') }}</div>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th style="width: 25px;" class="text-center">No</th>
                        <th>No Order / Tanggal</th>
                        <th>Pelanggan & CRM Lead</th>
                        <th>Sales PIC (Hak Insentif)</th>
                        <th>Pembuat Transaksi</th>
                        <th class="text-right">Omset (IDR)</th>
                        <th class="text-center">Rate</th>
                        <th class="text-right">Insentif (IDR)</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $sumRev = 0;
                        $sumInc = 0;
                    @endphp
                    @forelse($data['orders'] as $idx => $ord)
                        @php
                            $salesUser = ($ord->contact && $ord->contact->sales && $ord->contact->sales->is_active)
                                ? $ord->contact->sales
                                : ($ord->creator ?? \App\Models\User::find($ord->created_by));

                            $salesName = $salesUser?->name ?? 'Sales Belum Ditentukan';
                            $salesId = $salesUser?->id ?? 0;
                            $ratePercent = ($ord->order_type === 'sales_order' || ($ord->order_type === 'quotation' && $ord->quotation_type === 'sales')) ? 1.5 : 2.0;
                            $revenue = (float) $ord->total_amount;
                            $incentiveNominal = round(($revenue * $ratePercent) / 100, 2);
                            $isPaid = ($ord->invoice_status === 'paid') || ($ord->invoices->where('status', 'paid')->count() > 0);

                            $isPicAttributed = ($ord->contact && $ord->contact->sales_id && $ord->contact->sales_id == $salesId);
                            $createdByAdmin = ($ord->creator && $ord->creator->isAdmin() && $ord->creator->id != $salesId);

                            $sumRev += $revenue;
                            $sumInc += $incentiveNominal;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $idx + 1 }}</td>
                            <td>
                                <div class="font-bold font-mono">{{ $ord->order_number }}</div>
                                <div style="font-size: 8px; color: #64748b;">{{ date('d/m/Y', strtotime($ord->order_date)) }}</div>
                            </td>
                            <td>
                                <div class="font-bold">{{ $ord->contact?->name ?? 'Pelanggan Umum' }}</div>
                                @if($ord->contact?->lead_code)
                                    <div style="font-size: 8px; color: #64748b; font-family: monospace;">Lead: {{ $ord->contact->lead_code }}</div>
                                @endif
                            </td>
                            <td>
                                <div class="font-bold" style="color: #0f172a;">{{ $salesName }}</div>
                                @if($isPicAttributed)
                                    <span class="status-badge bg-blue" style="font-size: 7px;">Lead PIC Sales</span>
                                @endif
                            </td>
                            <td>
                                <div style="font-size: 9px; color: #475569;">{{ $ord->creator?->name ?? 'Admin' }}</div>
                                @if($createdByAdmin)
                                    <span class="status-badge bg-slate" style="font-size: 7px;">Via Admin</span>
                                @endif
                            </td>
                            <td class="text-right font-mono font-bold">Rp {{ number_format($revenue, 0, ',', '.') }}</td>
                            <td class="text-center font-mono">{{ $ratePercent }}%</td>
                            <td class="text-right font-mono font-bold" style="color: #e11d48;">Rp {{ number_format($incentiveNominal, 0, ',', '.') }}</td>
                            <td class="text-center">
                                @if($isPaid)
                                    <span class="status-badge bg-emerald">Cair (Lunas)</span>
                                @else
                                    <span class="status-badge bg-amber">Menunggu</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center" style="padding: 24px; color: #94a3b8;">Belum ada data transaksi insentif pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr style="background: #f8fafc; font-weight: 800;">
                        <td colspan="5" class="text-right">TOTAL INSENTIF & REVENUE:</td>
                        <td class="text-right font-mono font-black">Rp {{ number_format($sumRev, 0, ',', '.') }}</td>
                        <td></td>
                        <td class="text-right font-mono font-black" style="color: #e11d48;">Rp {{ number_format($sumInc, 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        @endif

        <!-- Signatures -->
        <div class="signatures">
            <div>
                <div style="font-weight: 800; color: #64748b;">Dibuat Oleh:</div>
                <div class="sig-space"></div>
                <div style="font-weight: 900; text-decoration: underline;">{{ auth()->user()->name ?? 'Staff Operasional' }}</div>
                <div style="font-size: 9px; color: #64748b;">Staff / Administrator</div>
            </div>
            <div>
                <div style="font-weight: 800; color: #64748b;">Diperiksa Oleh:</div>
                <div class="sig-space"></div>
                <div style="font-weight: 900; text-decoration: underline;">Head of Operations</div>
                <div style="font-size: 9px; color: #64748b;">Manajer Operasional</div>
            </div>
            <div>
                <div style="font-weight: 800; color: #64748b;">Disetujui Oleh:</div>
                <div class="sig-space"></div>
                <div style="font-weight: 900; text-decoration: underline;">QI SHUJUN 齐书骏</div>
                <div style="font-size: 9px; color: #64748b;">Presiden Direktur</div>
            </div>
        </div>
    </div>

</body>
</html>
