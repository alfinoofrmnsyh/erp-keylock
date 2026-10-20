<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title', 'Keylock Indonesia - ERP & CMS')</title>
    
    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#0F172A">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Keylock ERP">
    <link rel="manifest" href="/manifest.webmanifest">
    <link rel="apple-touch-icon" href="/images/icons/icon-192x192.png">
    <link rel="icon" type="image/svg+xml" href="/images/keylock-logo.svg">

    <!-- Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            grey: '#707276',
                            yellow: '#F8E738',
                            gold: '#FACC15',
                            dark: '#0F172A',
                            subtle: '#F8FAFC',
                            card: '#FFFFFF',
                            border: '#E2E8F0',
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        display: ['"Space Grotesk"', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #F8FAFC;
            color: #0F172A;
            -webkit-tap-highlight-color: transparent;
        }

        /* Custom soft scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #F1F5F9;
        }
        ::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            border-radius: 999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94A3B8;
        }

        .light-card-shadow {
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px -1px rgba(0, 0, 0, 0.05);
        }
        .light-card-hover {
            transition: all 0.2s ease-in-out;
        }
        .light-card-hover:hover {
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.06), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
            transform: translateY(-1px);
        }
        .drawer-overlay {
            backdrop-filter: blur(4px);
        }
    </style>
</head>
<body class="min-h-screen bg-[#F4F6F8] text-slate-900 antialiased selection:bg-yellow-300 selection:text-black"
      x-data="erpAppMaster()"
      @open-quotation-modal.window="onOpenQuotationFromEvent($event.detail)"
      @app-toast.window="notify($event.detail.message, $event.detail.type, $event.detail.title)"
      @app-confirm.window="openConfirmModal($event.detail)">

    <div class="min-h-screen flex bg-[#F4F6F8]">

        <!-- ========================================================================= -->
        <!-- DYNAMIC LEFT SIDEBAR (DRIVEN BY APPMENU & APPMENUACCESS TABLES)           -->
        <!-- ========================================================================= -->
        <aside class="hidden md:flex w-64 bg-white border-r border-slate-200/90 flex-col justify-between shrink-0 h-screen sticky top-0 z-30 overflow-y-auto">
            <div>
                <!-- Official Brand Header (Image 2: Grey K with Yellow Dot) -->
                <div class="px-6 py-5 flex items-center justify-between border-b border-slate-100 sticky top-0 bg-white z-10">
                    <a href="{{ route('erp.index') }}" class="flex items-center space-x-3 group">
                        <div class="w-9 h-9 flex items-center justify-center shrink-0">
                            <img src="/images/keylock-logo.svg" alt="Keylock Logo" class="w-9 h-9 object-contain">
                        </div>
                        <div>
                            <span class="font-black text-sm tracking-tight text-slate-900 block leading-tight">ERP KEYLOCK ID</span>
                        </div>
                    </a>
                </div>

                <!-- Dynamic Navigation Links from Database -->
                <div class="px-4 py-4 space-y-5">
                    @forelse($sidebarMenus as $group)
                        <div>
                            @php
                                $groupZh = [
                                    'OPERASIONAL ERP' => '业务运营 ERP',
                                    'KONTEN CMS WEBSITE' => 'CMS 网站内容',
                                    'General System' => '系统通用管理',
                                ];
                                $groupZhName = $groupZh[$group->menu_name] ?? $group->menu_name;
                            @endphp
                            <p class="px-3 text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1.5 flex items-center justify-between">
                                <span x-text="lang === 'zh' ? '{{ $groupZhName }}' : '{{ $group->menu_name }}'">{{ $group->menu_name }}</span>
                                
                            </p>
                            <nav class="space-y-1">
                                @foreach($group->children as $item)
                                    @php
                                        $url = $item->menu_page ?: '#';
                                        $cleanPage = trim($url, '/');
                                        if ($cleanPage === 'erp') {
                                            $isActive = request()->is('erp') && !request()->is('erp/*');
                                        } elseif ($cleanPage === 'erp/cms') {
                                            $isActive = request()->is('erp/cms*') || request()->is('erp/categories*') || request()->is('erp/products*') || request()->is('erp/galleries*') || request()->is('erp/partners*') || request()->is('erp/testimonials*') || request()->is('erp/manage-about-page*') || request()->is('erp/cms-contact*');
                                        } elseif ($cleanPage === 'erp/reports') {
                                            $isActive = request()->is('erp/reports*');
                                        } else {
                                            $isActive = !empty($cleanPage) && (request()->is($cleanPage) || request()->is($cleanPage . '/*'));
                                        }
                                        $menuZh = [
                                            'Dashboard' => '控制面板',
                                            'Master Data' => '主数据管理',
                                            'Penawaran & Order' => '报价与订单',
                                            'Penawaran & Transaksi' => '报价与订单',
                                            'Tabel Penawaran & Order' => '报价与订单',
                                            'Tabel Transaksi & Penawaran' => '报价与订单',
                                            'Tabel Transaksi & Penawaran Harga' => '报价与订单',
                                            'Katalog Unit & Armada' => '设备与车队目录',
                                            'Pelanggan & CRM' => '客户与 CRM',
                                            'CRM' => '客户关系管理 (CRM)',
                                            'Content Management' => '网站内容管理 (CMS)',
                                            'Konten Management' => '网站内容管理 (CMS)',
                                            'Laporan & Rekapitulasi' => '业务报表与下载',
                                            'Laporan Sistem' => '业务报表与下载',
                                            'Kategori Produk' => '产品分类',
                                            'Katalog Produk' => '产品目录',
                                            'Galeri Dokumentasi' => '图片库',
                                            'Mitra Kerjasama' => '合作伙伴',
                                            'Testimoni Klien' => '客户评价',
                                            'Tentang Perusahaan' => '公司简介',
                                            'Kontak & QR WeChat' => '联系方式与微信二维码',
                                            'User Management' => '用户与权限管理',
                                            'Menu Management' => '菜单管理',
                                            'Procurement & Vendor' => '采购与供应商',
                                            'Pengadaan & Vendor' => '采购与供应商',
                                            'Finance & Invoicing' => '财务与发票管理',
                                            'Keuangan & Penagihan' => '财务与发票管理',
                                            'Armada & Unit' => '车队与设备管理',
                                            'Fleet & Armada' => '车队与设备管理',
                                            'Config & Dropdown' => '系统配置与下拉选项',
                                            'Master Config' => '系统配置与下拉选项',
                                        ];
                                        $zhItemName = $menuZh[$item->menu_name] ?? $item->menu_name;
                                    @endphp
                                    <a href="{{ $url }}" 
                                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs transition {{ $isActive ? 'bg-amber-50 text-slate-900 font-extrabold shadow-xs border border-amber-200/60' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-medium' }}">
                                        <div class="flex items-center space-x-3 min-w-0">
                                             <!-- Dynamic Icon Rendering -->
                                            <span class="shrink-0 {{ $isActive ? 'text-amber-500' : 'text-slate-400' }}">
                                                @if(str_contains($item->menu_icon, 'config') || str_contains($item->menu_icon, 'tool') || str_contains(strtolower($item->menu_name), 'config'))
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                @elseif(str_contains($item->menu_icon, 'browser') || str_contains(strtolower($item->menu_name), 'content') || str_contains(strtolower($item->menu_name), 'konten') || str_contains(strtolower($item->menu_name), 'cms'))
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                                @elseif(str_contains($item->menu_icon, 'home'))
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                                                @elseif(str_contains($item->menu_icon, 'car') || str_contains($item->menu_icon, 'truck') || str_contains(strtolower($item->menu_name), 'armada') || str_contains(strtolower($item->menu_name), 'fleet'))
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path></svg>
                                                @elseif(str_contains($item->menu_icon, 'shopping') || str_contains($item->menu_icon, 'bag') || str_contains(strtolower($item->menu_name), 'procurement'))
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                                @elseif(str_contains($item->menu_icon, 'banknote') || str_contains($item->menu_icon, 'finance') || str_contains(strtolower($item->menu_name), 'finance'))
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                @elseif(str_contains($item->menu_icon, 'server') || str_contains($item->menu_icon, 'database') || str_contains(strtolower($item->menu_name), 'master'))
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path></svg>
                                                @elseif(str_contains($item->menu_icon, 'cart') || str_contains($item->menu_icon, 'order'))
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                @elseif(str_contains($item->menu_icon, 'box') || str_contains($item->menu_icon, 'product'))
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                                @elseif(str_contains($item->menu_icon, 'user') || str_contains($item->menu_icon, 'contact') || str_contains(strtolower($item->menu_name), 'crm'))
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                                @elseif(str_contains($item->menu_icon, 'network') || str_contains($item->menu_icon, 'category'))
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                                                @elseif(str_contains($item->menu_icon, 'photo') || str_contains($item->menu_icon, 'gallery'))
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                @elseif(str_contains($item->menu_icon, 'id') || str_contains($item->menu_icon, 'partner'))
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                                                @elseif(str_contains($item->menu_icon, 'comment') || str_contains($item->menu_icon, 'testimonial'))
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                                @elseif(str_contains($item->menu_icon, 'info'))
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                @elseif(str_contains($item->menu_icon, 'bars') || str_contains($item->menu_icon, 'menu'))
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
                                                @elseif(str_contains($item->menu_icon, 'note') || str_contains($item->menu_icon, 'report') || str_contains(strtolower($item->menu_name), 'laporan') || str_contains(strtolower($item->menu_name), 'rekapitulasi'))
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                @else
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                                @endif
                                            </span>
                                            <span class="truncate" x-text="lang === 'zh' ? '{{ $zhItemName }}' : '{{ $item->menu_name }}'">{{ $item->menu_name }}</span>
                                        </div>

                                        <!-- Badge for Orders / Quotations -->
                                        @if(str_contains($item->menu_page, 'orders') && isset($activeQuotationsCount) && $activeQuotationsCount > 0)
                                            <span class="bg-amber-100 text-amber-800 text-[10px] font-bold px-2 py-0.5 rounded-full border border-amber-200 shrink-0">
                                                {{ $activeQuotationsCount }}
                                            </span>
                                        @endif
                                    </a>
                                @endforeach

                                <!-- Quick Quotation Button after Operasional Group -->
                                @if(str_contains(strtoupper($group->menu_name), 'OPERASIONAL'))
                                    <button type="button" @click="modalQuotationOpen = true" 
                                            class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:bg-amber-50 hover:text-slate-900 transition text-left group">
                                        <div class="flex items-center space-x-3 min-w-0">
                                            <span class="shrink-0 text-amber-500 group-hover:text-amber-600 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </span>
                                            <span class="truncate" x-text="lang === 'zh' ? '创建快速报价单' : 'Buat Penawaran Harga'">Buat Penawaran Harga</span>
                                        </div>
                                        <span class="w-4 h-4 rounded-md bg-amber-400/20 text-amber-700 font-black text-[10px] flex items-center justify-center shrink-0">
                                            +
                                        </span>
                                    </button>
                                @endif
                            </nav>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 px-3">Tidak ada menu aktif.</p>
                    @endforelse
                </div>
            </div>

            <!-- Sidebar Footer -->
            <div class="p-4 border-t border-slate-100 space-y-2 sticky bottom-0 bg-white">

                <!-- User Info & Logout -->
                <div class="pt-2 flex items-center justify-between">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-8 h-8 rounded-full bg-slate-900 text-yellow-400 flex items-center justify-center font-bold text-xs">
                            {{ strtoupper(substr(auth()->user()->name ?? 'AD', 0, 2)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-slate-900 truncate">{{ auth()->user()->name ?? 'Admin Keylock' }}</p>
                            <p class="text-[10px] text-slate-500 uppercase font-semibold">{{ auth()->user()->role ?? 'Admin' }}</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" title="Keluar / Logout" class="p-1.5 text-slate-500 hover:text-rose-600 hover:bg-slate-100 rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- ========================================================================= -->
        <!-- MAIN CONTENT CONTAINER                                                    -->
        <!-- ========================================================================= -->
        <main class="flex-1 overflow-y-auto min-h-screen">
            <!-- Top Header Navigation Bar (Sticky Light Header) -->
            <header class="bg-white border-b border-slate-200/90 px-4 md:px-8 py-2.5 md:py-3.5 flex items-center justify-between sticky top-0 z-20">
                <!-- Mobile Top Bar View (< md) -->
                <div class="flex md:hidden items-center justify-between w-full">
                    <!-- Brand Logo, Title & Logged-in User (Matching Reference) -->
                    <a href="{{ route('erp.index') }}" class="flex items-center space-x-2.5 min-w-0 pr-2">
                        <div class="w-9 h-9 flex items-center justify-center shrink-0">
                            <img src="/images/keylock-logo.svg" alt="Keylock Logo" class="w-9 h-9 object-contain">
                        </div>
                        <div class="min-w-0">
                            <span class="font-black text-xs sm:text-sm tracking-tight text-slate-900 block leading-tight truncate">ERP Keylock Indonesia</span>
                            <span class="text-[11px] font-medium text-slate-500 block truncate -mt-0.5">{{ auth()->user()->email ?? auth()->user()->name ?? 'user@keylock.id' }}</span>
                        </div>
                    </a>

                    <!-- Mobile Right Section: Language Pill, Circular Notification & Avatar -->
                    <div class="flex items-center space-x-1.5 shrink-0">
                        <!-- Bilingual Pill -->
                        <div class="flex items-center bg-slate-100 p-0.5 rounded-lg border border-slate-200/80">
                            <button type="button" @click="setLang('id')"
                                    :class="lang === 'id' ? 'bg-white text-slate-900 font-extrabold shadow-xs' : 'text-slate-500 font-medium'"
                                    class="px-1.5 py-0.5 rounded text-[10px] transition">
                                ID
                            </button>
                            <button type="button" @click="setLang('zh')"
                                    :class="lang === 'zh' ? 'bg-white text-slate-900 font-extrabold shadow-xs' : 'text-slate-500 font-medium'"
                                    class="px-1.5 py-0.5 rounded text-[10px] transition">
                                中文
                            </button>
                        </div>

                        <!-- Notification Bell (Circular Background Matching Reference) -->
                        <button type="button" class="w-8 h-8 rounded-full bg-slate-100/90 hover:bg-slate-200 text-slate-600 flex items-center justify-center relative transition shadow-2xs" aria-label="Notifikasi">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                            <span class="w-2 h-2 rounded-full bg-rose-500 absolute top-1.5 right-1.5 ring-1.5 ring-white"></span>
                        </button>

                        <!-- User Avatar Button (Toggles Mobile Sheet) -->
                        <button type="button" 
                                @click="toggleMobileMoreMenu()"
                                class="w-7 h-7 rounded-full bg-slate-900 text-yellow-400 flex items-center justify-center font-extrabold text-[11px] ring-2 ring-slate-200"
                                aria-label="Menu Pengguna">
                            {{ strtoupper(substr(auth()->user()->name ?? 'AD', 0, 2)) }}
                        </button>
                    </div>
                </div>

                <!-- Desktop Top Bar View (>= md) -->
                <div class="hidden md:flex items-center justify-between w-full">
                    <div>
                        <!-- Breadcrumbs Slot -->
                        <div class="flex items-center space-x-1.5 text-xs text-slate-500">
                            @section('breadcrumbs')
                                <a href="{{ route('erp.index') }}" class="hover:text-slate-800">Home</a>
                                <span>›</span>
                                <span class="text-slate-800 font-semibold" x-text="lang === 'zh' ? '报价与订单' : 'Penawaran & Order'">Penawaran & Order</span>
                            @show
                        </div>
                        @hasSection('header_title')
                            <h1 class="text-xl font-extrabold text-slate-900 tracking-tight mt-0.5">
                                @yield('header_title')
                            </h1>
                        @endif
                    </div>

                    <!-- Header Right Section: Language Switcher, Search & User Pill -->
                    <div class="flex items-center space-x-3.5">
                        <!-- BILINGUAL LANGUAGE SWITCHER (ID / 中文) -->
                        <div class="flex items-center bg-slate-100 p-0.5 rounded-xl border border-slate-200/80">
                            <button type="button" @click="setLang('id')"
                                    :class="lang === 'id' ? 'bg-white text-slate-900 font-extrabold shadow-xs' : 'text-slate-500 hover:text-slate-900 font-medium'"
                                    class="px-3 py-1 rounded-lg text-xs transition">
                                ID
                            </button>
                            <button type="button" @click="setLang('zh')"
                                    :class="lang === 'zh' ? 'bg-white text-slate-900 font-extrabold shadow-xs' : 'text-slate-500 hover:text-slate-900 font-medium'"
                                    class="px-3 py-1 rounded-lg text-xs transition">
                                中文
                            </button>
                        </div>

                        <!-- Quick Global Search -->
                        <div class="relative hidden lg:block w-64">
                            <input type="text" 
                                   :placeholder="lang === 'zh' ? '搜索单据、客户、设备...' : 'Cari dokumen, pelanggan, armada...'"
                                   placeholder="Cari dokumen, pelanggan, armada..." 
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-8 pr-3 py-1.5 text-xs text-slate-900 placeholder:text-slate-400 focus:outline-none focus:bg-white focus:border-yellow-400 transition">
                            <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>

                        <!-- Notification Bell -->
                        <button type="button" class="p-2 rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-100 relative transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                            <span class="w-2 h-2 rounded-full bg-yellow-500 absolute top-2 right-2 ring-2 ring-white"></span>
                        </button>

                        <!-- User Pill Badge -->
                        <div class="flex items-center space-x-2.5 pl-2 border-l border-slate-200">
                            <div class="w-8 h-8 rounded-full bg-slate-900 text-yellow-400 flex items-center justify-center font-extrabold text-xs">
                                {{ strtoupper(substr(auth()->user()->name ?? 'AD', 0, 2)) }}
                            </div>
                            <span class="text-xs font-bold text-slate-900 hidden sm:inline">{{ auth()->user()->name ?? 'Admin Keylock' }}</span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Body Content -->
            <div class="p-4 sm:p-6 md:p-8 space-y-6 pb-28 md:pb-8">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- GLOBAL TOAST NOTIFICATION CONTAINER (Requirement 4) -->
    <div class="fixed top-5 right-5 z-[9999] flex flex-col space-y-3 pointer-events-none max-w-sm w-full px-4 sm:px-0">
        <template x-for="t in toasts" :key="t.id">
            <div x-show="t.show"
                 x-cloak
                 x-transition:enter="transform ease-out duration-300 transition"
                 x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-4 scale-95"
                 x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 :class="{
                     'border-emerald-300 shadow-emerald-500/10': t.type === 'success',
                     'border-rose-300 shadow-rose-500/10': t.type === 'error',
                     'border-amber-300 shadow-amber-500/10': t.type === 'warning',
                     'border-blue-300 shadow-blue-500/10': t.type === 'info'
                 }"
                 class="pointer-events-auto w-full bg-white/95 backdrop-blur-md p-4 rounded-2xl border shadow-xl flex items-start space-x-3 transition">
                
                <!-- Icon by Type -->
                <div class="shrink-0 mt-0.5">
                    <template x-if="t.type === 'success'">
                        <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                    </template>
                    <template x-if="t.type === 'error'">
                        <div class="w-8 h-8 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </div>
                    </template>
                    <template x-if="t.type === 'warning'">
                        <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                    </template>
                    <template x-if="t.type === 'info'">
                        <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </template>
                </div>

                <!-- Content -->
                <div class="flex-1 min-w-0 pr-1">
                    <h5 class="text-xs font-black text-slate-900" x-text="t.title"></h5>
                    <p class="text-xs text-slate-600 mt-0.5 leading-relaxed break-words" x-text="t.message"></p>
                </div>

                <!-- Dismiss Button -->
                <button type="button" @click="dismissToast(t.id)" class="shrink-0 text-slate-400 hover:text-slate-600 p-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </template>
    </div>

    <!-- GLOBAL CONFIRMATION MODAL TOAST (Requirement: Modern Confirmation Dialog) -->
    <div x-show="confirmModal.open"
         x-cloak
         class="fixed inset-0 z-[10000] flex items-center justify-center p-4 sm:p-6"
         x-transition:enter="transition ease-out duration-250"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @keydown.escape.window="if(confirmModal.open) handleConfirmChoice(false)">
         
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm transition-opacity"
             @click="handleConfirmChoice(false)"></div>

        <!-- Dialog Box -->
        <div class="relative bg-white rounded-3xl max-w-lg w-full p-6 sm:p-7 shadow-2xl border border-slate-200/90 z-10 overflow-hidden transform transition-all"
             x-transition:enter="transition ease-out duration-250"
             x-transition:enter-start="opacity-0 scale-95 translate-y-3"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-3"
             @click.stop>
            
            <div class="flex items-start space-x-4">
                <!-- Icon Badge based on type -->
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 shadow-xs"
                     :class="{
                         'bg-emerald-50 text-emerald-600 border border-emerald-200/80': confirmModal.type === 'success',
                         'bg-amber-50 text-amber-600 border border-amber-200/80': confirmModal.type === 'warning',
                         'bg-rose-50 text-rose-600 border border-rose-200/80': confirmModal.type === 'danger' || confirmModal.type === 'error',
                         'bg-blue-50 text-blue-600 border border-blue-200/80': confirmModal.type === 'info'
                     }">
                    <template x-if="confirmModal.type === 'success'">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    </template>
                    <template x-if="confirmModal.type === 'warning'">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </template>
                    <template x-if="confirmModal.type === 'danger' || confirmModal.type === 'error'">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </template>
                    <template x-if="confirmModal.type === 'info'">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </template>
                </div>

                <!-- Text Content -->
                <div class="flex-1 min-w-0">
                    <h3 class="text-base font-black text-slate-900 leading-snug tracking-tight" x-text="confirmModal.title"></h3>
                    <p class="text-xs text-slate-600 mt-2 leading-relaxed whitespace-pre-line font-medium" x-text="confirmModal.message"></p>
                </div>
            </div>

            <!-- Footer Action Buttons -->
            <div class="mt-6 flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                <button type="button" 
                        @click="handleConfirmChoice(false)"
                        class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition active:scale-95 shadow-2xs"
                        x-text="confirmModal.cancelText">
                    Batal
                </button>
                <button type="button" 
                        @click="handleConfirmChoice(true)"
                        class="px-6 py-2.5 rounded-xl text-xs font-black shadow-md transition active:scale-95 flex items-center space-x-2"
                        :class="{
                            'bg-emerald-600 hover:bg-emerald-700 text-white': confirmModal.type === 'success',
                            'bg-amber-500 hover:bg-amber-600 text-slate-950': confirmModal.type === 'warning',
                            'bg-rose-600 hover:bg-rose-700 text-white': confirmModal.type === 'danger' || confirmModal.type === 'error',
                            'bg-blue-600 hover:bg-blue-700 text-white': confirmModal.type === 'info'
                        }">
                    <span x-text="confirmModal.confirmText"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- Reusable Fast Quotation Creator Modal -->
    @include('erp.partials.quotation-modal')

    <!-- Reusable Role-Based Mobile Bottom Navigation Dock & Sheets -->
    @include('partials.mobile-dock')

    <!-- Global Application Alpine Master Script -->
    <script>
        const KEYLOCK_CATALOG = @json($catalogItems ?? []);
        const KEYLOCK_CUSTOMERS = @json($customers ?? []);
        const CSRF_TOKEN = '{{ csrf_token() }}';

        // Global Toast Dispatcher
        window.showToast = function(message, type = 'success', title = null) {
            window.dispatchEvent(new CustomEvent('app-toast', {
                detail: { message, type, title }
            }));
        };
        window.toast = window.showToast;

        // Global Confirmation Dialog Dispatcher (Modal Toast)
        window.confirmDialog = function(options) {
            let detail = {};
            if (typeof options === 'string') {
                detail = { message: options, title: 'Konfirmasi Tindakan', type: 'warning' };
            } else {
                detail = options || {};
            }
            return new Promise((resolve) => {
                detail.resolve = resolve;
                window.dispatchEvent(new CustomEvent('app-confirm', { detail }));
            });
        };
        window.confirmModal = window.confirmDialog;

        // Global Alert Interceptor to replace native browser alert() with Toast Alert everywhere
        window.alert = function(msg) {
            if (!msg && msg !== 0) return;
            const text = String(msg);
            const lower = text.toLowerCase();
            let type = 'info';
            if (lower.includes('berhasil') || lower.includes('sukses') || lower.includes('success')) {
                type = 'success';
            } else if (lower.includes('gagal') || lower.includes('error') || lower.includes('kesalahan') || lower.includes('mohon') || lower.includes('wajib') || lower.includes('tidak')) {
                type = 'error';
            } else if (lower.includes('perhatian') || lower.includes('warning') || lower.includes('peringatan') || lower.includes('pemberitahuan')) {
                type = 'warning';
            }
            window.showToast(text, type);
        };

        // =========================================================================
        // GLOBAL RUPIAH FORMATTER & REAL-TIME INPUT MASK SYSTEM (PHASE 3)
        // =========================================================================
        window.formatRupiah = function(val) {
            if (val === undefined || val === null || val === '') return '';
            const strVal = String(val).replace(/,\d{1,2}$/, '');
            let clean = strVal.replace(/\D/g, '');
            if (!clean) return '';
            if (clean.length > 1) {
                clean = clean.replace(/^0+/, '');
                if (clean === '') clean = '0';
            }
            return clean.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        };

        window.parseRupiah = function(val) {
            if (val === undefined || val === null || val === '') return 0;
            const strVal = String(val).replace(/,\d{1,2}$/, '');
            const clean = strVal.replace(/\D/g, '');
            return clean ? parseInt(clean, 10) : 0;
        };

        // Aliases for backwards compatibility
        window.formatNumberWithDots = window.formatRupiah;
        window.parseNumberFromDots = window.parseRupiah;

        /**
         * Real-time Rupiah Input Masker with Cursor Position & Backspace Preservation.
         * Works with dynamic inputs, modals, drawers, and form submissions.
         */
        window.maskRupiah = function(input, callback) {
            if (!input) return;

            const rawVal = input.value;
            const selStart = input.selectionStart !== null ? input.selectionStart : rawVal.length;

            // Count how many numeric digits precede the cursor in raw input
            const textBeforeCursor = rawVal.slice(0, selStart);
            const digitsBeforeCursor = (textBeforeCursor.replace(/\D/g, '')).length;

            // Clean input digits
            let clean = String(rawVal).replace(/,\d{1,2}$/, '').replace(/\D/g, '');
            if (clean.length > 1) {
                clean = clean.replace(/^0+/, '');
                if (clean === '') clean = '0';
            }

            const formatted = clean !== '' ? clean.replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '';
            input.value = formatted;
            input._maskHandled = true;

            // Sync hidden companion input if specified by data attribute
            const hiddenTarget = input.dataset.targetName || input.dataset.hiddenInput;
            if (hiddenTarget) {
                const scope = input.form || input.closest('[x-data]') || input.parentElement;
                const hiddenEl = scope ? scope.querySelector(`input[name="${hiddenTarget}"][type="hidden"]`) : null;
                if (hiddenEl) {
                    hiddenEl.value = clean ? parseInt(clean, 10) : 0;
                }
            }

            // Reposition cursor: find index where we've seen digitsBeforeCursor digits
            let newCursorPos = 0;
            let digitsCounted = 0;

            for (let i = 0; i < formatted.length; i++) {
                if (/\d/.test(formatted[i])) {
                    digitsCounted++;
                }
                if (digitsCounted === digitsBeforeCursor) {
                    newCursorPos = i + 1;
                    break;
                }
            }

            if (digitsBeforeCursor === 0) {
                newCursorPos = 0;
            } else if (digitsCounted < digitsBeforeCursor) {
                newCursorPos = formatted.length;
            }

            try {
                input.setSelectionRange(newCursorPos, newCursorPos);
            } catch (e) {
                // Ignore elements that don't support selectionRange
            }

            const numVal = clean ? parseInt(clean, 10) : 0;
            if (typeof callback === 'function') {
                callback(numVal, formatted);
            }
            return numVal;
        };

        /**
         * Keydown handler to smoothly backspace/delete over thousand separator dots
         */
        window.handleRupiahKeydown = function(e) {
            const input = e.target;
            if (!input) return;

            // Backspace over dot: delete preceding digit
            if (e.key === 'Backspace' && input.selectionStart === input.selectionEnd && input.selectionStart > 0) {
                const pos = input.selectionStart;
                if (input.value[pos - 1] === '.') {
                    e.preventDefault();
                    const val = input.value;
                    const newVal = val.slice(0, pos - 2) + val.slice(pos);
                    input.value = newVal;
                    input.setSelectionRange(pos - 2, pos - 2);
                    input.dispatchEvent(new Event('input', { bubbles: true }));
                }
            } else if (e.key === 'Delete' && input.selectionStart === input.selectionEnd && input.selectionStart < input.value.length) {
                const pos = input.selectionStart;
                if (input.value[pos] === '.') {
                    e.preventDefault();
                    const val = input.value;
                    const newVal = val.slice(0, pos) + val.slice(pos + 2);
                    input.value = newVal;
                    input.setSelectionRange(pos, pos);
                    input.dispatchEvent(new Event('input', { bubbles: true }));
                }
            }
        };

        /**
         * Prevent invalid non-digit keystrokes (allows navigation, select-all, copy/paste)
         */
        window.handleRupiahKeypress = function(e) {
            if (e.ctrlKey || e.metaKey || e.altKey) return;
            if (e.key && e.key.length === 1 && !/\d/.test(e.key)) {
                e.preventDefault();
            }
        };

        // Global Event Delegation for all [data-rupiah] and .rupiah-input elements
        document.addEventListener('keydown', function(e) {
            if (e.target && (e.target.matches('[data-rupiah]') || e.target.classList.contains('rupiah-input'))) {
                window.handleRupiahKeydown(e);
            }
        });

        document.addEventListener('keypress', function(e) {
            if (e.target && (e.target.matches('[data-rupiah]') || e.target.classList.contains('rupiah-input'))) {
                window.handleRupiahKeypress(e);
            }
        });

        document.addEventListener('input', function(e) {
            if (e.target && (e.target.matches('[data-rupiah]') || e.target.classList.contains('rupiah-input'))) {
                if (e.target._maskHandled) {
                    e.target._maskHandled = false;
                    return;
                }
                window.maskRupiah(e.target);
            }
        });

        document.addEventListener('paste', function(e) {
            if (e.target && (e.target.matches('[data-rupiah]') || e.target.classList.contains('rupiah-input'))) {
                setTimeout(function() {
                    window.maskRupiah(e.target);
                    e.target.dispatchEvent(new Event('input', { bubbles: true }));
                }, 0);
            }
        });

        function erpAppMaster() {
            const defaultItemId = KEYLOCK_CATALOG.length > 0 ? KEYLOCK_CATALOG[0].id : 1;
            const defaultPrice = KEYLOCK_CATALOG.length > 0 ? (KEYLOCK_CATALOG[0].rental_rate_monthly || 35000000) : 35000000;
            const defaultItemImage = KEYLOCK_CATALOG.length > 0 ? (KEYLOCK_CATALOG[0].image || null) : null;
            const defaultContactId = KEYLOCK_CUSTOMERS.length > 0 ? KEYLOCK_CUSTOMERS[0].id : 1;

            return {
                lang: localStorage.getItem('keylock_lang') || 'id',
                mobileQuickActionsOpen: false,
                mobileMoreMenuOpen: false,
                toggleMobileQuickActions() {
                    this.mobileQuickActionsOpen = !this.mobileQuickActionsOpen;
                    if (this.mobileQuickActionsOpen) this.mobileMoreMenuOpen = false;
                },
                toggleMobileMoreMenu() {
                    this.mobileMoreMenuOpen = !this.mobileMoreMenuOpen;
                    if (this.mobileMoreMenuOpen) this.mobileQuickActionsOpen = false;
                },
                closeMobileSheets() {
                    this.mobileQuickActionsOpen = false;
                    this.mobileMoreMenuOpen = false;
                },
                formatRupiah: window.formatRupiah,
                parseRupiah: window.parseRupiah,
                maskRupiah: window.maskRupiah,
                formatNumberWithDots: window.formatNumberWithDots,
                parseNumberFromDots: window.parseNumberFromDots,
                modalQuotationOpen: false,
                quotationMode: 'create', // 'create' or 'edit'
                editingQuotationId: null,
                customerList: KEYLOCK_CUSTOMERS,

                // Confirmation Modal State (Modal Toast)
                confirmModal: {
                    open: false,
                    title: 'Konfirmasi',
                    message: '',
                    confirmText: 'Ya, Lanjutkan',
                    cancelText: 'Batal',
                    type: 'warning',
                    resolve: null
                },
                openConfirmModal(detail) {
                    this.confirmModal.title = detail.title || (this.lang === 'zh' ? '确认操作' : 'Konfirmasi Tindakan');
                    this.confirmModal.message = detail.message || '';
                    this.confirmModal.confirmText = detail.confirmText || (this.lang === 'zh' ? '确定' : 'Ya, Lanjutkan');
                    this.confirmModal.cancelText = detail.cancelText || (this.lang === 'zh' ? '取消' : 'Batal');
                    this.confirmModal.type = detail.type || 'warning';
                    this.confirmModal.resolve = detail.resolve || null;
                    this.confirmModal.open = true;
                },
                handleConfirmChoice(choice) {
                    this.confirmModal.open = false;
                    if (typeof this.confirmModal.resolve === 'function') {
                        this.confirmModal.resolve(choice);
                        this.confirmModal.resolve = null;
                    }
                },

                // Toast Alert System State (Requirement 4)
                toasts: [],
                notify(message, type = 'success', title = null) {
                    if (!message) return;
                    const id = Date.now() + Math.random().toString(36).substring(2, 7);
                    let defaultTitle = 'Notifikasi';
                    if (type === 'success') defaultTitle = (this.lang === 'zh' ? '操作成功' : 'Berhasil');
                    else if (type === 'error' || type === 'danger') {
                        type = 'error';
                        defaultTitle = (this.lang === 'zh' ? '注意 / 失败' : 'Perhatian');
                    } else if (type === 'warning') defaultTitle = (this.lang === 'zh' ? '系统提示' : 'Peringatan');
                    else if (type === 'info') defaultTitle = (this.lang === 'zh' ? '信息' : 'Informasi');

                    const toast = {
                        id,
                        show: true,
                        message,
                        type,
                        title: title || defaultTitle
                    };
                    this.toasts.push(toast);

                    setTimeout(() => {
                        this.dismissToast(id);
                    }, 4000);
                },
                dismissToast(id) {
                    const t = this.toasts.find(item => item.id === id);
                    if (t) {
                        t.show = false;
                        setTimeout(() => {
                            this.toasts = this.toasts.filter(item => item.id !== id);
                        }, 300);
                    }
                },

                // Quick Customer Creator State
                quickCustomerModalOpen: false,
                submittingQuickCustomer: false,
                quickCustomerForm: {
                    name: '',
                    pic_name: '',
                    phone: '',
                    email: '',
                    billing_address: ''
                },

                openQuickCustomerModal() {
                    this.quickCustomerForm = {
                        name: '',
                        pic_name: '',
                        phone: '',
                        email: '',
                        billing_address: ''
                    };
                    this.quickCustomerModalOpen = true;
                },

                async submitQuickCustomer() {
                    if (!this.quickCustomerForm.name || !this.quickCustomerForm.name.trim()) {
                        this.notify('Mohon isi Nama Perusahaan / Customer!', 'warning');
                        return;
                    }
                    this.submittingQuickCustomer = true;
                    try {
                        const res = await fetch('{{ route('erp.master_data.contact.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': CSRF_TOKEN
                            },
                            body: JSON.stringify({
                                type: 'customer',
                                name: this.quickCustomerForm.name.trim(),
                                pic_name: this.quickCustomerForm.pic_name ? this.quickCustomerForm.pic_name.trim() : null,
                                phone: this.quickCustomerForm.phone ? this.quickCustomerForm.phone.trim() : null,
                                email: this.quickCustomerForm.email ? this.quickCustomerForm.email.trim() : null,
                                billing_address: this.quickCustomerForm.billing_address ? this.quickCustomerForm.billing_address.trim() : null
                            })
                        });
                        const result = await res.json();
                        if (result.success && result.data) {
                            const newCust = result.data;
                            this.customerList.unshift(newCust);
                            this.quotationForm.contact_id = newCust.id;
                            this.quickCustomerModalOpen = false;
                            this.notify('Customer baru (' + newCust.name + ') berhasil ditambahkan dan langsung dipilih!', 'success');
                        } else {
                            this.notify('Gagal menambahkan customer: ' + (result.message || 'Periksa kembali data'), 'error');
                        }
                    } catch (e) {
                        this.notify('Gagal menghubungi server.', 'error');
                    } finally {
                        this.submittingQuickCustomer = false;
                    }
                },

                init() {
                    const params = new URLSearchParams(window.location.search);
                    if (params.get('open_quotation') === '1') {
                        const custId = params.get('customer_id');
                        const custName = params.get('customer_name');
                        const prod = params.get('product');
                        this.openNewQuotationModal({
                            customer_id: custId,
                            customer_name: custName,
                            product_name: prod
                        });
                        try {
                            const cleanUrl = window.location.pathname;
                            window.history.replaceState({}, document.title, cleanUrl);
                        } catch(e) {}
                    }
                },

                onOpenQuotationFromEvent(detail) {
                    if (!detail) {
                        this.openNewQuotationModal();
                        return;
                    }
                    if (detail.order) {
                        this.editQuotation(detail.order);
                        return;
                    }
                    this.openNewQuotationModal(detail);
                },

                setLang(l) {
                    this.lang = l;
                    localStorage.setItem('keylock_lang', l);
                    window.dispatchEvent(new CustomEvent('lang-changed', { detail: l }));
                },

                updateValidUntil() {
                    if (this.quotationForm.order_date) {
                        const d = new Date(this.quotationForm.order_date);
                        d.setDate(d.getDate() + 3);
                        this.quotationForm.valid_until = d.toISOString().split('T')[0];
                    }
                },

                quotationForm: {
                    quotation_type: 'rental',
                    contact_id: defaultContactId,
                    customer_po_number: '',
                    order_date: '{{ date('Y-m-d') }}',
                    valid_until: '{{ date('Y-m-d', strtotime('+3 days')) }}',
                    shipping_address: '',
                    has_shipping: false,
                    shipping_delivery_fee: 0,
                    shipping_return_fee: 0,
                    include_ppn: true,
                    include_pph23: false,
                    items: [
                        {
                            transaction_type: 'rental',
                            item_id: null,
                            name: '',
                            specification: '',
                            quantity: 1,
                            unit_price: defaultPrice,
                            duration: 1,
                            duration_unit: 'Bulan',
                            image: defaultItemImage,
                            image_preview: defaultItemImage,
                            discount: 0
                        }
                    ]
                },

                openNewQuotationModal(prefill = {}) {
                    this.quotationMode = 'create';
                    this.editingQuotationId = null;

                    let cId = defaultContactId;
                    if (prefill.customer_id) {
                        cId = prefill.customer_id;
                        // Ensure this customer is present in this.customerList so dropdown displays and selects it
                        const existingIdx = this.customerList.findIndex(c => c.id == cId);
                        if (existingIdx === -1) {
                            this.customerList.unshift({
                                id: cId,
                                code: prefill.customer_code || ('CUST-' + String(cId).padStart(3, '0')),
                                name: prefill.customer_name || 'Customer CRM',
                                pic_name: prefill.pic_name || '',
                                phone: prefill.phone || '',
                                shipping_address: prefill.shipping_address || ''
                            });
                        }
                    } else if (prefill.customer_name) {
                        const foundCust = this.customerList.find(c => c.name && c.name.toLowerCase() === prefill.customer_name.toLowerCase());
                        if (foundCust) cId = foundCust.id;
                    }

                    let initialItems = [];
                    if (prefill.items && Array.isArray(prefill.items) && prefill.items.length > 0) {
                        initialItems = prefill.items.map(it => ({
                            transaction_type: it.deal_type === 'sales' ? 'sales' : 'rental',
                            item_id: it.item_id || null,
                            name: it.name || it.description || '',
                            specification: it.specification || '',
                            quantity: Number(it.quantity || 1),
                            unit_price: Number(it.unit_price || defaultPrice),
                            duration: Number(it.duration || 1),
                            duration_unit: it.duration_unit || 'Bulan',
                            image: it.image || defaultItemImage,
                            image_preview: it.image_preview || it.image || defaultItemImage,
                            discount: 0
                        }));
                    } else if (prefill.product_name) {
                        initialItems = [{
                            transaction_type: prefill.deal_type === 'sales' ? 'sales' : 'rental',
                            item_id: null,
                            name: prefill.product_name,
                            specification: prefill.specification || '',
                            quantity: Number(prefill.quantity || 1),
                            unit_price: defaultPrice,
                            duration: Number(prefill.duration || 1),
                            duration_unit: 'Bulan',
                            image: defaultItemImage,
                            image_preview: defaultItemImage,
                            discount: 0
                        }];
                    } else {
                        initialItems = [{
                            transaction_type: 'rental',
                            item_id: null,
                            name: '',
                            specification: '',
                            quantity: 1,
                            unit_price: defaultPrice,
                            duration: 1,
                            duration_unit: 'Bulan',
                            image: defaultItemImage,
                            image_preview: defaultItemImage,
                            discount: 0
                        }];
                    }

                    this.quotationForm = {
                        quotation_type: (prefill.deal_type === 'sales' ? 'sales' : 'rental'),
                        contact_id: cId,
                        customer_po_number: prefill.customer_po_number || '',
                        order_date: '{{ date('Y-m-d') }}',
                        valid_until: '{{ date('Y-m-d', strtotime('+3 days')) }}',
                        shipping_address: prefill.shipping_address || '',
                        has_shipping: false,
                        shipping_delivery_fee: 0,
                        shipping_return_fee: 0,
                        include_ppn: true,
                        include_pph23: false,
                        items: initialItems
                    };

                    this.updateQuotationType();
                    this.modalQuotationOpen = true;
                    this.$nextTick(() => {
                        this.quotationForm.contact_id = cId;
                    });
                },

                editQuotation(order) {
                    if (!order) return;
                    this.quotationMode = 'edit';
                    this.editingQuotationId = order.id;

                    let items = [];
                    if (order.items && order.items.length > 0) {
                        items = order.items.map(it => ({
                            transaction_type: it.transaction_type || (order.quotation_type === 'sales' ? 'sales' : 'rental'),
                            item_id: it.item_id || null,
                            name: it.description || (it.item ? it.item.name : ''),
                            specification: it.specification || '',
                            quantity: Number(it.quantity || 1),
                            unit_price: Number(it.unit_price || 0),
                            duration: Number(it.rental_duration || 1),
                            duration_unit: it.rental_duration_unit || 'Bulan',
                            image: it.image || null,
                            image_preview: it.image || null,
                            discount: Number(it.discount_amount || 0)
                        }));
                    } else {
                        items = [{
                            transaction_type: 'rental',
                            item_id: null,
                            name: '',
                            specification: '',
                            quantity: 1,
                            unit_price: defaultPrice,
                            duration: 1,
                            duration_unit: 'Bulan',
                            image: defaultItemImage,
                            image_preview: defaultItemImage,
                            discount: 0
                        }];
                    }

                    let orderDate = '{{ date('Y-m-d') }}';
                    if (order.order_date) {
                        orderDate = (typeof order.order_date === 'string') ? order.order_date.substring(0, 10) : new Date(order.order_date).toISOString().substring(0, 10);
                    }

                    let validUntil = '';
                    if (order.valid_until) {
                        validUntil = (typeof order.valid_until === 'string') ? order.valid_until.substring(0, 10) : new Date(order.valid_until).toISOString().substring(0, 10);
                    }

                    const hasShipping = Boolean((Number(order.shipping_delivery_fee || 0) > 0) || (Number(order.shipping_return_fee || 0) > 0) || (Number(order.shipping_fee || 0) > 0));
                    let delFee = Number(order.shipping_delivery_fee || 0);
                    let retFee = Number(order.shipping_return_fee || 0);
                    if (hasShipping && delFee === 0 && retFee === 0 && Number(order.shipping_fee || 0) > 0) {
                        delFee = Number(order.shipping_fee || 0);
                    }

                    this.quotationForm = {
                        quotation_type: order.quotation_type || 'rental',
                        contact_id: order.contact_id || defaultContactId,
                        customer_po_number: order.customer_po_number || '',
                        order_date: orderDate,
                        valid_until: validUntil,
                        shipping_address: order.shipping_address || '',
                        has_shipping: hasShipping,
                        shipping_delivery_fee: delFee,
                        shipping_return_fee: retFee,
                        include_ppn: Boolean(Number(order.tax_amount || 0) > 0 || Number(order.tax_percent || 0) > 0),
                        include_pph23: Boolean(Number(order.pph_amount || 0) > 0 || Number(order.pph_percent || 0) > 0),
                        items: items
                    };

                    this.updateQuotationType();
                    this.modalQuotationOpen = true;
                },

                onItemTransactionTypeChange(index) {
                    if (this.quotationForm.items[index].transaction_type === 'sales') {
                        this.quotationForm.items[index].duration = 1;
                    }
                    this.updateQuotationType();
                },

                updateQuotationType() {
                    const items = this.quotationForm.items || [];
                    const hasRental = items.some(function(it) { return it.transaction_type === 'rental'; });
                    const hasSales = items.some(function(it) { return it.transaction_type === 'sales'; });
                    if (hasRental && hasSales) {
                        this.quotationForm.quotation_type = 'both';
                    } else if (hasSales) {
                        this.quotationForm.quotation_type = 'sales';
                    } else {
                        this.quotationForm.quotation_type = 'rental';
                    }
                },

                setQuotationType(type) {
                    this.quotationForm.quotation_type = type;
                    this.quotationForm.items.forEach(function(item) {
                        item.transaction_type = type;
                        if (type === 'sales') {
                            item.duration = 1;
                        }
                    });
                },

                addItemRow() {
                    const defaultType = (this.quotationForm.quotation_type === 'sales') ? 'sales' : 'rental';
                    this.quotationForm.items.push({
                        transaction_type: defaultType,
                        item_id: null,
                        name: '',
                        specification: '',
                        quantity: 1,
                        unit_price: 0,
                        duration: 1,
                        duration_unit: 'Bulan',
                        image: null,
                        image_preview: null,
                        discount: 0
                    });
                    this.updateQuotationType();
                },

                removeItemRow(index) {
                    if (this.quotationForm.items.length > 1) {
                        this.quotationForm.items.splice(index, 1);
                        this.updateQuotationType();
                    }
                },

                uploadRowImage(index, event) {
                    const file = event.target.files[0];
                    if (!file) return;
                    const reader = new FileReader();
                    const self = this;
                    reader.onload = function(e) {
                        self.quotationForm.items[index].image = e.target.result;
                        self.quotationForm.items[index].image_preview = e.target.result;
                    };
                    reader.readAsDataURL(file);
                },

                onItemSelectChange(index, event) {
                    const itemId = Number(event.target.value);
                    const found = KEYLOCK_CATALOG.find(function(it) { return it.id === itemId; });
                    if (found) {
                        const isSales = (this.quotationForm.items[index].transaction_type === 'sales');
                        if (isSales) {
                            this.quotationForm.items[index].unit_price = Number(found.sale_price || found.rental_rate_monthly || 0);
                        } else {
                            this.quotationForm.items[index].unit_price = Number(found.rental_rate_monthly || found.sale_price || 0);
                        }
                        if (found.image) {
                            this.quotationForm.items[index].image = found.image;
                            this.quotationForm.items[index].image_preview = found.image;
                        }
                    }
                },

                get calculateSubtotal() {
                    return this.quotationForm.items.reduce(function(sum, item) {
                        const isRental = (item.transaction_type === 'rental');
                        const dur = isRental ? Number(item.duration || 1) : 1;
                        const line = (Number(item.quantity || 1) * Number(item.unit_price || 0) * dur) - Number(item.discount || 0);
                        return sum + (line > 0 ? line : 0);
                    }, 0);
                },

                get calculatePpn() {
                    return this.quotationForm.include_ppn ? Math.round(this.calculateSubtotal * 0.11) : 0;
                },

                get calculatePph23() {
                    return this.quotationForm.include_pph23 ? Math.round(this.calculateSubtotal * 0.02) : 0;
                },

                get calculateShippingFee() {
                    if (!this.quotationForm.has_shipping) return 0;
                    return (Number(this.quotationForm.shipping_delivery_fee) || 0) + (Number(this.quotationForm.shipping_return_fee) || 0);
                },

                get calculateTotal() {
                    return this.calculateSubtotal + this.calculateShippingFee + this.calculatePpn - this.calculatePph23;
                },

                formatRupiah(num) {
                    return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
                },

                async submitFastQuotation() {
                    const isEdit = (this.quotationMode === 'edit' && this.editingQuotationId);
                    const url = isEdit ? ('/erp/quotations/' + this.editingQuotationId + '/update') : '{{ route('erp.quotations.store') }}';

                    try {
                        const res = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': CSRF_TOKEN
                            },
                            body: JSON.stringify(this.quotationForm)
                        });
                        const result = await res.json();
                        if (result.success) {
                            const orderId = result.order ? result.order.id : this.editingQuotationId;
                            this.modalQuotationOpen = false;
                            
                            const shouldPrint = await window.confirmDialog({
                                title: this.lang === 'zh' ? '报价单创建成功！' : 'Quotation Berhasil Dibuat!',
                                message: (result.message || 'Quotation berhasil disimpan.') + (this.lang === 'zh' ? "\n\n数据已保存至订单与报价单列表 (/erp/orders)！\n\n您是否要立即打印或预览 PDF 文档？" : "\n\nData tersimpan di menu Penawaran & Transaksi (/erp/orders)!\n\nApakah Anda ingin langsung CETAK DOKUMEN PDF sekarang?"),
                                type: 'success',
                                confirmText: this.lang === 'zh' ? '立即打印 PDF' : 'Cetak Dokumen PDF',
                                cancelText: this.lang === 'zh' ? '稍后再说' : 'Nanti Saja'
                            });

                            if (orderId && shouldPrint) {
                                window.open('/erp/quotations/' + orderId + '/pdf', '_blank');
                            }
                            window.location.href = '/erp/orders?type=quotation';
                        } else {
                            this.notify('Gagal menyimpan penawaran: ' + (result.message || 'Periksa kembali input Anda'), 'error');
                        }
                    } catch (e) {
                        this.notify('Terjadi kesalahan pada server.', 'error');
                    }
                }
            };
        }

        window.setTablePerPage = function(val) {
            const url = new URL(window.location.href);
            url.searchParams.set('per_page', val);
            url.searchParams.set('page', '1');
            window.location.href = url.toString();
        };
    </script>

    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                window.showToast(@json(session('success')), 'success');
            });
        </script>
    @endif
    @if(session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                window.showToast(@json(session('error')), 'error');
            });
        </script>
    @endif
    @if(session('warning'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                window.showToast(@json(session('warning')), 'warning');
            });
        </script>
    @endif

    @stack('scripts')

    <!-- PWA Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator && (window.location.protocol === 'https:' || window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1')) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js', { scope: '/' })
                    .then(function(reg) {
                        // Registration successful
                    })
                    .catch(function(err) {
                        console.debug('[PWA] ServiceWorker registration note:', err.message);
                    });
            });
        }
    </script>
</body>
</html>
