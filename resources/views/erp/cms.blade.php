@extends('layouts.erp')

@section('title', 'Konten CMS Website - Keylock Indonesia')

@section('breadcrumbs')
    <a href="{{ route('erp.index') }}" class="hover:text-slate-800">Home</a>
    <span>›</span>
    <span class="text-slate-400">Konten CMS Website</span>
    <span>›</span>
    <span class="text-slate-800 font-semibold" x-text="getTabTitle(activeTab)">Kategori Produk</span>
@endsection

@section('content')
<div x-data="cmsManager('{{ $tab }}')">

    <!-- ========================================================================= -->
    <!-- 1. PAGE HEADER                                                            -->
    <!-- ========================================================================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center space-x-2.5">
                <div class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center font-black">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </div>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight" x-text="lang === 'zh' ? '网站内容与门户管理 (CMS)' : 'Konten & Portal CMS Website'">
                    Konten & Portal CMS Website
                </h2>
            </div>
            <p class="text-xs text-slate-500 mt-1" x-text="lang === 'zh' ? '管理 Keylock 公共门户网站的商品分类、产品目录、案例相册、合作伙伴及客户评价。' : 'Pusat pengelolaan konten website publik Keylock: Kategori, Produk, Galeri Dokumentasi, Mitra Kerjasama, Testimoni, Profil, dan Kontak.'">
                Pusat pengelolaan konten website publik Keylock: Kategori, Produk, Galeri Dokumentasi, Mitra Kerjasama, Testimoni, Profil, dan Kontak.
            </p>
        </div>

        <!-- Dynamic Action Button based on Active Tab -->
        <div class="flex items-center space-x-3">
            <template x-if="activeTab === 'categories'">
                <button type="button" @click="openCategoryModal()" 
                        class="bg-amber-500 hover:bg-amber-600 text-slate-950 font-black px-5 py-2.5 rounded-full shadow-md hover:shadow-lg text-xs transition flex items-center space-x-2 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    <span x-text="lang === 'zh' ? '新建分类' : 'Tambah Kategori Baru'">Tambah Kategori Baru</span>
                </button>
            </template>
            <template x-if="activeTab === 'products'">
                <button type="button" @click="openProductModal()" 
                        class="bg-blue-600 hover:bg-blue-700 text-white font-black px-5 py-2.5 rounded-full shadow-md hover:shadow-lg text-xs transition flex items-center space-x-2 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    <span x-text="lang === 'zh' ? '新建商品' : 'Tambah Produk Baru'">Tambah Produk Baru</span>
                </button>
            </template>
            <template x-if="activeTab === 'galleries'">
                <button type="button" @click="openGalleryModal()" 
                        class="bg-emerald-600 hover:bg-emerald-700 text-white font-black px-5 py-2.5 rounded-full shadow-md hover:shadow-lg text-xs transition flex items-center space-x-2 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    <span x-text="lang === 'zh' ? '上传照片' : 'Unggah Foto Galeri'">Unggah Foto Galeri</span>
                </button>
            </template>
            <template x-if="activeTab === 'partners'">
                <button type="button" @click="openPartnerModal()" 
                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-black px-5 py-2.5 rounded-full shadow-md hover:shadow-lg text-xs transition flex items-center space-x-2 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    <span x-text="lang === 'zh' ? '添加合作伙伴' : 'Tambah Mitra Baru'">Tambah Mitra Baru</span>
                </button>
            </template>
            <template x-if="activeTab === 'testimonials'">
                <button type="button" @click="openTestimonialModal()" 
                        class="bg-purple-600 hover:bg-purple-700 text-white font-black px-5 py-2.5 rounded-full shadow-md hover:shadow-lg text-xs transition flex items-center space-x-2 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    <span x-text="lang === 'zh' ? '新建评价' : 'Tambah Testimoni Baru'">Tambah Testimoni Baru</span>
                </button>
            </template>
            <a href="https://keylockindonesia.com" target="_blank" 
               class="bg-white hover:bg-slate-50 text-slate-700 border border-slate-200/90 font-bold px-4 py-2.5 rounded-full shadow-2xs text-xs transition flex items-center space-x-1.5">
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                <span x-text="lang === 'zh' ? '浏览网站' : 'Lihat Website'">Lihat Website</span>
            </a>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 2. PILL TABS BAR                                                          -->
    <!-- ========================================================================= -->
    <div class="flex items-center gap-2 overflow-x-auto pb-2 mb-6 scrollbar-none">
        
        <!-- Tab 1: Categories -->
        <button type="button" @click="switchTab('categories')" 
                :class="activeTab === 'categories' ? 'bg-white text-slate-900 shadow-sm border border-slate-200/90 font-black' : 'bg-slate-100/80 text-slate-600 hover:bg-white hover:text-slate-900 border border-transparent font-bold'"
                class="px-4 py-2 rounded-2xl text-xs transition flex items-center space-x-2 shrink-0">
            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
            <span x-text="lang === 'zh' ? '产品分类' : 'Kategori Produk'">Kategori Produk</span>
            <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-900 text-[10px] font-black font-mono">
                {{ $stats['total_categories'] }}
            </span>
        </button>

        <!-- Tab 2: Products -->
        <button type="button" @click="switchTab('products')" 
                :class="activeTab === 'products' ? 'bg-white text-slate-900 shadow-sm border border-slate-200/90 font-black' : 'bg-slate-100/80 text-slate-600 hover:bg-white hover:text-slate-900 border border-transparent font-bold'"
                class="px-4 py-2 rounded-2xl text-xs transition flex items-center space-x-2 shrink-0">
            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
            <span x-text="lang === 'zh' ? '商品目录' : 'Katalog Produk'">Katalog Produk</span>
            <span class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-900 text-[10px] font-black font-mono">
                {{ $stats['total_products'] }}
            </span>
        </button>

        <!-- Tab 3: Gallery -->
        <button type="button" @click="switchTab('galleries')" 
                :class="activeTab === 'galleries' ? 'bg-white text-slate-900 shadow-sm border border-slate-200/90 font-black' : 'bg-slate-100/80 text-slate-600 hover:bg-white hover:text-slate-900 border border-transparent font-bold'"
                class="px-4 py-2 rounded-2xl text-xs transition flex items-center space-x-2 shrink-0">
            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            <span x-text="lang === 'zh' ? '图片相册' : 'Galeri Dokumentasi'">Galeri Dokumentasi</span>
            <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-900 text-[10px] font-black font-mono">
                {{ $stats['total_galleries'] }}
            </span>
        </button>

        <!-- Tab 4: Partners -->
        <button type="button" @click="switchTab('partners')" 
                :class="activeTab === 'partners' ? 'bg-white text-slate-900 shadow-sm border border-slate-200/90 font-black' : 'bg-slate-100/80 text-slate-600 hover:bg-white hover:text-slate-900 border border-transparent font-bold'"
                class="px-4 py-2 rounded-2xl text-xs transition flex items-center space-x-2 shrink-0">
            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            <span x-text="lang === 'zh' ? '合作伙伴' : 'Mitra Kerjasama'">Mitra Kerjasama</span>
            <span class="px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-900 text-[10px] font-black font-mono">
                {{ $stats['total_partners'] }}
            </span>
        </button>

        <!-- Tab 5: Testimonials -->
        <button type="button" @click="switchTab('testimonials')" 
                :class="activeTab === 'testimonials' ? 'bg-white text-slate-900 shadow-sm border border-slate-200/90 font-black' : 'bg-slate-100/80 text-slate-600 hover:bg-white hover:text-slate-900 border border-transparent font-bold'"
                class="px-4 py-2 rounded-2xl text-xs transition flex items-center space-x-2 shrink-0">
            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
            <span x-text="lang === 'zh' ? '客户评价' : 'Testimoni Klien'">Testimoni Klien</span>
            <span class="px-2 py-0.5 rounded-full bg-purple-100 text-purple-900 text-[10px] font-black font-mono">
                {{ $stats['total_testimonials'] }}
            </span>
        </button>

        <!-- Tab 6: About Page -->
        <button type="button" @click="switchTab('about')" 
                :class="activeTab === 'about' ? 'bg-white text-slate-900 shadow-sm border border-slate-200/90 font-black' : 'bg-slate-100/80 text-slate-600 hover:bg-white hover:text-slate-900 border border-transparent font-bold'"
                class="px-4 py-2 rounded-2xl text-xs transition flex items-center space-x-2 shrink-0">
            <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            <span x-text="lang === 'zh' ? '关于我们' : 'Tentang Perusahaan'">Tentang Perusahaan</span>
        </button>

        <!-- Tab 7: Contact & QR -->
        <button type="button" @click="switchTab('contact')" 
                :class="activeTab === 'contact' ? 'bg-white text-slate-900 shadow-sm border border-slate-200/90 font-black' : 'bg-slate-100/80 text-slate-600 hover:bg-white hover:text-slate-900 border border-transparent font-bold'"
                class="px-4 py-2 rounded-2xl text-xs transition flex items-center space-x-2 shrink-0">
            <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
            <span x-text="lang === 'zh' ? '联系方式与微信' : 'Kontak & QR WeChat'">Kontak & QR WeChat</span>
        </button>

    </div>

    <!-- ========================================================================= -->
    <!-- TAB 1: KATEGORI PRODUK (CATEGORIES) - EXACT MATCH FOR IMAGE 2             -->
    <!-- ========================================================================= -->
    <div x-show="activeTab === 'categories'" class="space-y-6">
        
        <!-- Filter & Search Bar -->
        <div class="bg-white p-4 rounded-3xl border border-slate-200/90 shadow-2xs flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="relative w-full md:w-80">
                <input type="text" x-model="searchCategory" placeholder="Cari nama atau slug kategori..." 
                       class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-4 py-2 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <div class="flex items-center space-x-2 text-xs text-slate-500 font-semibold self-end md:self-auto">
                <span x-text="lang === 'zh' ? '总分类数:' : 'Total Kategori:'">Total Kategori:</span>
                <span class="font-black text-slate-900 font-mono">{{ $categories->count() }}</span>
            </div>
        </div>

        <!-- Categories Table (Matching Image 2: Gambar, Nama, Slug, Actions) -->
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-2xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-black uppercase text-slate-500 tracking-wider">
                            <th class="py-3.5 px-6 w-24" x-text="lang === 'zh' ? '图片' : 'Gambar'">Gambar</th>
                            <th class="py-3.5 px-6" x-text="lang === 'zh' ? '分类名称' : 'Nama Kategori'">Nama Kategori</th>
                            <th class="py-3.5 px-6 font-mono" x-text="lang === 'zh' ? 'URL 别名 (Slug)' : 'Slug URL'">Slug</th>
                            <th class="py-3.5 px-6 text-center" x-text="lang === 'zh' ? '关联产品' : 'Total Produk'">Total Produk</th>
                            <th class="py-3.5 px-6 text-right w-44" x-text="lang === 'zh' ? '操作' : 'Aksi'">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                        @forelse($categories as $cat)
                            @php
                                $imgSrc = $cat->image ? asset('storage/' . $cat->image) : null;
                                $catNameId = is_array($cat->name) ? ($cat->name['id'] ?? '') : $cat->name;
                                $catNameEn = is_array($cat->name) ? ($cat->name['en'] ?? '') : '';
                                $catNameZh = is_array($cat->name) ? ($cat->name['zh-Hans'] ?? '') : '';
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition" 
                                x-show="matchesSearchCategory('{{ strtolower($catNameId . ' ' . $catNameEn . ' ' . $cat->slug) }}')">
                                <!-- Image Thumbnail -->
                                <td class="py-3 px-6">
                                    <div class="w-14 h-14 rounded-2xl bg-slate-100 border border-slate-200/80 overflow-hidden flex items-center justify-center shrink-0 shadow-2xs">
                                        @if($imgSrc)
                                            <img src="{{ $imgSrc }}" alt="{{ $catNameId }}" class="w-full h-full object-cover">
                                        @else
                                            <svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        @endif
                                    </div>
                                </td>

                                <!-- Category Name -->
                                <td class="py-3 px-6">
                                    <div class="font-black text-slate-900 text-sm">
                                        {{ $catNameId }}
                                    </div>
                                    @if($catNameEn || $catNameZh)
                                        <div class="text-[11px] text-slate-400 mt-0.5 space-x-2">
                                            @if($catNameEn) <span>🇬🇧 {{ $catNameEn }}</span> @endif
                                            @if($catNameZh) <span>🇨🇳 {{ $catNameZh }}</span> @endif
                                        </div>
                                    @endif
                                </td>

                                <!-- Slug -->
                                <td class="py-3 px-6">
                                    <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 font-mono text-[11px] font-bold border border-slate-200/70">
                                        {{ $cat->slug }}
                                    </span>
                                </td>

                                <!-- Total Products & Galleries -->
                                <td class="py-3 px-6 text-center">
                                    <div class="inline-flex items-center space-x-1.5">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-blue-50 text-blue-700 border border-blue-200">
                                            {{ $cat->products_count }} Produk
                                        </span>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            {{ $cat->galleries_count }} Galeri
                                        </span>
                                    </div>
                                </td>

                                <!-- Actions -->
                                <td class="py-3 px-6 text-right">
                                    <div class="flex items-center justify-end space-x-2">
                                        <button type="button" 
                                                @click="editCategory({{ json_encode([
                                                    'id' => $cat->id,
                                                    'name_id' => $catNameId,
                                                    'name_en' => $catNameEn,
                                                    'name_zh' => $catNameZh,
                                                    'slug' => $cat->slug,
                                                    'image_url' => $imgSrc,
                                                ]) }})"
                                                class="px-3 py-1.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-800 text-xs font-black transition border border-amber-200/80 active:scale-95 flex items-center space-x-1">
                                            <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                            <span x-text="lang === 'zh' ? '编辑' : 'Edit'">Edit</span>
                                        </button>
                                        <form method="POST" action="{{ route('erp.cms.categories.delete', $cat->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini? Produk terkait mungkin akan terdampak.');" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-black transition border border-rose-200/80 active:scale-95 flex items-center space-x-1">
                                                <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                <span x-text="lang === 'zh' ? '删除' : 'Hapus'">Hapus</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-slate-400 text-xs">
                                    Belum ada data kategori. Klik tombol <strong>+ Tambah Kategori Baru</strong> di atas untuk membuat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 2: KATALOG PRODUK (PRODUCTS)                                          -->
    <!-- ========================================================================= -->
    <div x-show="activeTab === 'products'" class="space-y-6">
        
        <!-- Filter & Search Bar -->
        <div class="bg-white p-4 rounded-3xl border border-slate-200/90 shadow-2xs flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                <div class="relative w-full sm:w-72">
                    <input type="text" x-model="searchProduct" placeholder="Cari nama atau slug produk..." 
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-4 py-2 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <select x-model="filterProductCategory" 
                        class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                    <option value="all">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->slug }}">{{ $cat->display_name }} ({{ $cat->slug }})</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center space-x-2 text-xs text-slate-500 font-semibold self-end md:self-auto">
                <span x-text="lang === 'zh' ? '总商品数:' : 'Total Produk:'">Total Produk:</span>
                <span class="font-black text-slate-900 font-mono">{{ $products->count() }}</span>
            </div>
        </div>

        <!-- Products Table -->
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-2xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-black uppercase text-slate-500 tracking-wider">
                            <th class="py-3.5 px-6 w-24" x-text="lang === 'zh' ? '商品图片' : 'Foto Produk'">Foto Produk</th>
                            <th class="py-3.5 px-6" x-text="lang === 'zh' ? '商品名称与规格' : 'Nama Produk'">Nama Produk</th>
                            <th class="py-3.5 px-6" x-text="lang === 'zh' ? '所属分类' : 'Kategori'">Kategori</th>
                            <th class="py-3.5 px-6 font-mono" x-text="lang === 'zh' ? 'Slug URL' : 'Slug'">Slug</th>
                            <th class="py-3.5 px-6 text-center" x-text="lang === 'zh' ? '推荐状态' : 'Trending'">Trending</th>
                            <th class="py-3.5 px-6 text-right w-44" x-text="lang === 'zh' ? '操作' : 'Aksi'">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                        @forelse($products as $prod)
                            @php
                                $imgSrc = $prod->image ? asset('storage/' . $prod->image) : null;
                                $pTitleId = is_array($prod->title) ? ($prod->title['id'] ?? '') : $prod->title;
                                $pTitleEn = is_array($prod->title) ? ($prod->title['en'] ?? '') : '';
                                $pTitleZh = is_array($prod->title) ? ($prod->title['zh-Hans'] ?? '') : '';
                                $pDescId = is_array($prod->desc) ? ($prod->desc['id'] ?? '') : ($prod->desc ?? '');
                                $pDescEn = is_array($prod->desc) ? ($prod->desc['en'] ?? '') : '';
                                $pDescZh = is_array($prod->desc) ? ($prod->desc['zh-Hans'] ?? '') : '';
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition"
                                x-show="matchesSearchProduct('{{ strtolower($pTitleId . ' ' . $pTitleEn . ' ' . $prod->slug) }}', '{{ $prod->category_slug }}')">
                                <td class="py-3 px-6">
                                    <div class="w-14 h-14 rounded-2xl bg-slate-100 border border-slate-200/80 overflow-hidden flex items-center justify-center shrink-0 shadow-2xs">
                                        @if($imgSrc)
                                            <img src="{{ $imgSrc }}" alt="{{ $pTitleId }}" class="w-full h-full object-cover">
                                        @else
                                            <svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3 px-6 max-w-sm">
                                    <div class="font-black text-slate-900 text-sm leading-snug">
                                        {{ $pTitleId }}
                                    </div>
                                    @if($pTitleEn || $pTitleZh)
                                        <div class="text-[11px] text-slate-400 mt-0.5 space-x-2 truncate">
                                            @if($pTitleEn) <span>🇬🇧 {{ $pTitleEn }}</span> @endif
                                            @if($pTitleZh) <span>🇨🇳 {{ $pTitleZh }}</span> @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3 px-6">
                                    <span class="px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 text-[11px] font-black border border-blue-200/80">
                                        {{ $prod->category?->display_name ?? $prod->category_slug }}
                                    </span>
                                </td>
                                <td class="py-3 px-6">
                                    <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600 font-mono text-[11px] font-bold">
                                        {{ $prod->slug }}
                                    </span>
                                </td>
                                <td class="py-3 px-6 text-center">
                                    @if($prod->trending)
                                        <span class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 text-[10px] font-black border border-amber-200 inline-flex items-center space-x-1">
                                            <span>⭐</span>
                                            <span>Trending</span>
                                        </span>
                                    @else
                                        <span class="text-slate-300 text-xs">-</span>
                                    @endif
                                </td>
                                <td class="py-3 px-6 text-right">
                                    <div class="flex items-center justify-end space-x-2">
                                        <button type="button" 
                                                @click="editProduct({{ json_encode([
                                                    'id' => $prod->id,
                                                    'title_id' => $pTitleId,
                                                    'title_en' => $pTitleEn,
                                                    'title_zh' => $pTitleZh,
                                                    'slug' => $prod->slug,
                                                    'category_slug' => $prod->category_slug,
                                                    'trending' => (bool)$prod->trending,
                                                    'desc_id' => $pDescId,
                                                    'desc_en' => $pDescEn,
                                                    'desc_zh' => $pDescZh,
                                                    'image_url' => $imgSrc,
                                                ]) }})"
                                                class="px-3 py-1.5 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-800 text-xs font-black transition border border-blue-200/80 active:scale-95 flex items-center space-x-1">
                                            <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                            <span x-text="lang === 'zh' ? '编辑' : 'Edit'">Edit</span>
                                        </button>
                                        <form method="POST" action="{{ route('erp.cms.products.delete', $prod->id) }}" onsubmit="return confirm('Hapus produk ini dari katalog?');" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-black transition border border-rose-200/80 active:scale-95 flex items-center space-x-1">
                                                <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                <span x-text="lang === 'zh' ? '删除' : 'Hapus'">Hapus</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-400 text-xs">
                                    Belum ada produk di katalog. Klik tombol <strong>+ Tambah Produk Baru</strong> di atas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 3: GALERI DOKUMENTASI (GALLERIES)                                     -->
    <!-- ========================================================================= -->
    <div x-show="activeTab === 'galleries'" class="space-y-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
            @forelse($galleries as $gal)
                @php
                    $imgSrc = $gal->image_url ? asset('storage/' . $gal->image_url) : null;
                    $gTitleId = is_array($gal->title) ? ($gal->title['id'] ?? '') : $gal->title;
                    $gTitleEn = is_array($gal->title) ? ($gal->title['en'] ?? '') : '';
                    $gTitleZh = is_array($gal->title) ? ($gal->title['zh-Hans'] ?? '') : '';
                    $gDescId = is_array($gal->description) ? ($gal->description['id'] ?? '') : ($gal->description ?? '');
                @endphp
                <div class="bg-white rounded-3xl border border-slate-200/90 shadow-2xs overflow-hidden flex flex-col justify-between group hover:shadow-lg transition">
                    <div>
                        <!-- Photo Frame -->
                        <div class="h-48 w-full bg-slate-100 relative overflow-hidden">
                            @if($imgSrc)
                                <img src="{{ $imgSrc }}" alt="{{ $gTitleId }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-300">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                            @endif
                            <div class="absolute top-3 left-3">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-white/90 backdrop-blur-xs text-slate-800 shadow-2xs border border-white">
                                    {{ $gal->category?->display_name ?? 'Galeri' }}
                                </span>
                            </div>
                            <div class="absolute top-3 right-3">
                                <button type="button" @click="toggleGalleryStatus({{ $gal->id }})" 
                                        class="px-2 py-0.5 rounded-full text-[10px] font-black shadow-2xs transition"
                                        :class="{{ $gal->status ? 'true' : 'false' }} ? 'bg-emerald-500 text-white' : 'bg-slate-400 text-white'">
                                    {{ $gal->status ? 'Tayang' : 'Draft' }}
                                </button>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="p-4">
                            <h4 class="font-black text-slate-900 text-sm leading-snug line-clamp-1">
                                {{ $gTitleId ?: 'Dokumentasi Proyek' }}
                            </h4>
                            @if($gDescId)
                                <div class="text-xs text-slate-500 mt-1.5 line-clamp-2">
                                    {!! strip_tags($gDescId) !!}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Card Actions -->
                    <div class="px-4 pb-4 pt-2 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-[10px] font-mono text-slate-400">{{ $gal->created_at?->format('d M Y') }}</span>
                        <div class="flex items-center space-x-1.5">
                            <button type="button" 
                                    @click="editGallery({{ json_encode([
                                        'id' => $gal->id,
                                        'category_id' => $gal->category_id,
                                        'title_id' => $gTitleId,
                                        'title_en' => $gTitleEn,
                                        'title_zh' => $gTitleZh,
                                        'description_id' => $gDescId,
                                        'image_url' => $imgSrc,
                                        'status' => (bool)$gal->status,
                                    ]) }})"
                                    class="p-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition" title="Edit">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </button>
                            <form method="POST" action="{{ route('erp.cms.galleries.delete', $gal->id) }}" onsubmit="return confirm('Hapus foto ini?');" class="inline">
                                @csrf
                                <button type="submit" class="p-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 transition" title="Hapus">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white p-12 rounded-3xl border border-slate-200/90 text-center text-slate-400 text-xs">
                    Belum ada foto dokumentasi di galeri. Klik tombol <strong>+ Unggah Foto Galeri</strong>.
                </div>
            @endforelse
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 4: MITRA KERJASAMA (PARTNERS)                                         -->
    <!-- ========================================================================= -->
    <div x-show="activeTab === 'partners'" class="space-y-6">
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @forelse($partners as $partner)
                @php
                    $imgSrc = $partner->image_url ? asset('storage/' . $partner->image_url) : null;
                @endphp
                <div class="bg-white p-4 rounded-3xl border border-slate-200/90 shadow-2xs flex flex-col items-center justify-between group hover:shadow-md transition text-center">
                    <div class="w-full h-24 bg-slate-50 rounded-2xl border border-slate-100 flex items-center justify-center p-2 mb-3">
                        @if($imgSrc)
                            <img src="{{ $imgSrc }}" alt="{{ $partner->name }}" class="max-h-full max-w-full object-contain">
                        @else
                            <span class="text-xs font-bold text-slate-400">Logo</span>
                        @endif
                    </div>
                    <h5 class="text-xs font-black text-slate-800 line-clamp-2 mb-3">
                        {{ $partner->name }}
                    </h5>
                    <div class="flex items-center space-x-1.5 w-full justify-center pt-2 border-t border-slate-100">
                        <button type="button" 
                                @click="editPartner({{ json_encode([
                                    'id' => $partner->id,
                                    'name' => $partner->name,
                                    'image_url' => $imgSrc,
                                ]) }})"
                                class="p-1 rounded-lg hover:bg-slate-100 text-slate-600 transition" title="Edit">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                        </button>
                        <form method="POST" action="{{ route('erp.cms.partners.delete', $partner->id) }}" onsubmit="return confirm('Hapus mitra ini?');" class="inline">
                            @csrf
                            <button type="submit" class="p-1 rounded-lg hover:bg-rose-50 text-rose-600 transition" title="Hapus">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white p-12 rounded-3xl border border-slate-200/90 text-center text-slate-400 text-xs">
                    Belum ada mitra kerjasama yang didaftarkan. Klik tombol <strong>+ Tambah Mitra Baru</strong>.
                </div>
            @endforelse
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 5: TESTIMONI KLIEN (TESTIMONIALS)                                     -->
    <!-- ========================================================================= -->
    <div x-show="activeTab === 'testimonials'" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @forelse($testimonials as $testi)
                @php
                    $msgId = is_array($testi->message) ? ($testi->message['id'] ?? '') : $testi->message;
                    $msgEn = is_array($testi->message) ? ($testi->message['en'] ?? '') : '';
                    $msgZh = is_array($testi->message) ? ($testi->message['zh-Hans'] ?? '') : '';
                @endphp
                <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-2xs flex flex-col justify-between">
                    <div>
                        <!-- Rating Stars -->
                        <div class="flex items-center space-x-1 mb-3">
                            @for($i = 1; $i <= 5; $i++)
                                <span class="text-sm {{ $i <= ($testi->rating ?: 5) ? 'text-amber-400' : 'text-slate-200' }}">★</span>
                            @endfor
                            <span class="text-[11px] font-black text-slate-400 ml-1.5 font-mono">({{ $testi->rating ?: 5 }}/5)</span>
                        </div>

                        <!-- Quote Message -->
                        <p class="text-xs text-slate-600 leading-relaxed italic mb-4 line-clamp-4">
                            "{{ $msgId }}"
                        </p>
                    </div>

                    <!-- Client PIC & Role -->
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                        <div>
                            <h5 class="text-xs font-black text-slate-900">{{ $testi->name }}</h5>
                            <span class="text-[10px] text-slate-400 block">{{ $testi->role ?: 'Klien Industri' }}</span>
                        </div>
                        <div class="flex items-center space-x-1.5">
                            <button type="button" 
                                    @click="editTestimonial({{ json_encode([
                                        'id' => $testi->id,
                                        'name' => $testi->name,
                                        'role' => $testi->role,
                                        'rating' => $testi->rating ?: 5,
                                        'message_id' => $msgId,
                                        'message_en' => $msgEn,
                                        'message_zh' => $msgZh,
                                    ]) }})"
                                    class="p-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition" title="Edit">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </button>
                            <form method="POST" action="{{ route('erp.cms.testimonials.delete', $testi->id) }}" onsubmit="return confirm('Hapus testimoni ini?');" class="inline">
                                @csrf
                                <button type="submit" class="p-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 transition" title="Hapus">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white p-12 rounded-3xl border border-slate-200/90 text-center text-slate-400 text-xs">
                    Belum ada ulasan testimoni. Klik tombol <strong>+ Tambah Testimoni Baru</strong>.
                </div>
            @endforelse
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 6: TENTANG PERUSAHAAN (ABOUT PAGE)                                    -->
    <!-- ========================================================================= -->
    <div x-show="activeTab === 'about'" class="space-y-6">
        @php
            $aboutData = $aboutpage->data['content']['id'] ?? [];
        @endphp
        <form method="POST" action="{{ route('erp.cms.about.update') }}" class="space-y-6">
            @csrf

            <!-- Section 1: Overview & Judul -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200/90 shadow-2xs space-y-4">
                <h4 class="text-sm font-black text-slate-900 flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-teal-500"></span>
                    <span>1. Profil & Sekilas Perusahaan (Header Halaman About)</span>
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Badge Atas</label>
                        <input type="text" name="badge_id" value="{{ $aboutData['badge'] ?? 'SEKILAS PERUSAHAAN' }}" required
                               class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-teal-500/20">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Judul Utama Halaman</label>
                        <input type="text" name="title_id" value="{{ $aboutData['title'] ?? 'Solusi Logistik & Material Handling Terpercaya' }}" required
                               class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-teal-500/20">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Deskripsi Paragraf 1</label>
                        <textarea name="description_1_id" rows="3" required
                                  class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-teal-500/20">{{ $aboutData['description_1'] ?? '' }}</textarea>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Deskripsi Paragraf 2</label>
                        <textarea name="description_2_id" rows="3" required
                                  class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-teal-500/20">{{ $aboutData['description_2'] ?? '' }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Section 2: Counter Statistik -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200/90 shadow-2xs space-y-4">
                <h4 class="text-sm font-black text-slate-900 flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                    <span>2. Pencapaian Angka Statistik (Counters)</span>
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/70">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Statistik 1 (Unit Alat)</label>
                        <input type="text" name="stats_units_value" value="{{ $aboutData['stats_units_value'] ?? '500+' }}" 
                               class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs font-black mt-1">
                        <input type="text" name="stats_units_label" value="{{ $aboutData['stats_units_label'] ?? 'TOTAL UNIT ALAT' }}" 
                               class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-[11px] font-semibold mt-2 text-slate-500">
                    </div>
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/70">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Statistik 2 (Proyek)</label>
                        <input type="text" name="stats_cities_value" value="{{ $aboutData['stats_cities_value'] ?? '1000+' }}" 
                               class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs font-black mt-1">
                        <input type="text" name="stats_cities_label" value="{{ $aboutData['stats_cities_label'] ?? 'PROYEK SELESAI' }}" 
                               class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-[11px] font-semibold mt-2 text-slate-500">
                    </div>
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/70">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Statistik 3 (Klien)</label>
                        <input type="text" name="stats_clients_value" value="{{ $aboutData['stats_clients_value'] ?? '500+' }}" 
                               class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs font-black mt-1">
                        <input type="text" name="stats_clients_label" value="{{ $aboutData['stats_clients_label'] ?? 'KLIEN MITRA' }}" 
                               class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-[11px] font-semibold mt-2 text-slate-500">
                    </div>
                </div>
            </div>

            <!-- Section 3: Visi & Misi -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200/90 shadow-2xs space-y-4">
                <h4 class="text-sm font-black text-slate-900 flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                    <span>3. Visi & Misi Perusahaan</span>
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Judul Visi</label>
                        <input type="text" name="vision_title" value="{{ $aboutData['vision_title'] ?? 'Visi Kami' }}" required
                               class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs font-semibold mb-3">
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Uraian Visi</label>
                        <textarea name="vision_text" rows="4" required
                                  class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs font-semibold">{{ $aboutData['vision_text'] ?? '' }}</textarea>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Judul Misi</label>
                        <input type="text" name="mission_title" value="{{ $aboutData['mission_title'] ?? 'Misi Kami' }}" required
                               class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs font-semibold mb-3">
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Daftar Poin Misi (1, 2, 3)</label>
                        <div class="space-y-2">
                            <input type="text" name="mission_item_1" value="{{ $aboutData['mission_list'][0]['text'] ?? '' }}" placeholder="Poin Misi 1"
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold">
                            <input type="text" name="mission_item_2" value="{{ $aboutData['mission_list'][1]['text'] ?? '' }}" placeholder="Poin Misi 2"
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold">
                            <input type="text" name="mission_item_3" value="{{ $aboutData['mission_list'][2]['text'] ?? '' }}" placeholder="Poin Misi 3"
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold">
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex justify-end">
                    <button type="submit" 
                            class="bg-teal-600 hover:bg-teal-700 text-white font-black px-6 py-2.5 rounded-full shadow-md hover:shadow-lg text-xs transition active:scale-95">
                        Simpan Perubahan Profil Perusahaan
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 7: KONTAK & QR WECHAT (CONTACT & QR)                                  -->
    <!-- ========================================================================= -->
    <div x-show="activeTab === 'contact'" class="space-y-6">
        <form method="POST" action="{{ route('erp.cms.contact.update') }}" enctype="multipart/form-data" 
              class="max-w-2xl bg-white p-6 rounded-3xl border border-slate-200/90 shadow-2xs space-y-6">
            @csrf
            
            <div class="border-b border-slate-100 pb-4">
                <h4 class="text-sm font-black text-slate-900">Pengaturan Kontak Publik & WeChat Official</h4>
                <p class="text-xs text-slate-500 mt-0.5">Nomor WhatsApp dan QR WeChat yang tampil di footer & kontak website publik.</p>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Nomor WhatsApp Utama (Contact 1)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-xs text-slate-400 font-bold">+</span>
                        <input type="text" name="contact1" value="{{ $cmsContact->contact1 ?? '6289699392924' }}" required
                               class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-7 pr-4 py-2.5 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-rose-500/20">
                    </div>
                    <span class="text-[10px] text-slate-400 mt-1 block">Format: 628xxxxxxxx (otomatis tersambung ke tautan wa.me)</span>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Nomor WhatsApp Cadangan / Sales (Contact 2)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-xs text-slate-400 font-bold">+</span>
                        <input type="text" name="contact2" value="{{ $cmsContact->contact2 ?? '6285173013525' }}"
                               class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-7 pr-4 py-2.5 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-rose-500/20">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">QR Code WeChat Resmi</label>
                    <div class="flex items-center space-x-4 mt-2">
                        <div class="w-28 h-28 rounded-2xl bg-slate-50 border-2 border-dashed border-slate-200 flex items-center justify-center overflow-hidden shrink-0">
                            @if(!empty($cmsContact->wechat_qr))
                                <img src="{{ asset('storage/' . $cmsContact->wechat_qr) }}" alt="WeChat QR" class="w-full h-full object-contain">
                            @else
                                <span class="text-[10px] font-bold text-slate-400 text-center px-2">Belum ada QR</span>
                            @endif
                        </div>
                        <div class="space-y-2">
                            <input type="file" name="wechat_qr" accept="image/*"
                                   class="text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                            <span class="text-[10px] text-slate-400 block">Pilih file gambar QR WeChat (PNG, JPG, JPEG)</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end">
                <button type="submit" 
                        class="bg-rose-600 hover:bg-rose-700 text-white font-black px-6 py-2.5 rounded-full shadow-md hover:shadow-lg text-xs transition active:scale-95">
                    Simpan Perubahan Kontak & QR
                </button>
            </div>
        </form>
    </div>

    <!-- ========================================================================= -->
    <!-- MODALS                                                                    -->
    <!-- ========================================================================= -->

    <!-- 1. CATEGORY MODAL -->
    <div x-show="modalCategoryOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" x-cloak>
        <div @click.away="modalCategoryOpen = false" class="bg-white w-full max-w-lg rounded-3xl shadow-2xl border border-slate-200 overflow-hidden animate-scaleIn">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="text-sm font-black text-slate-900" x-text="isEditCategory ? 'Edit Kategori Produk' : 'Tambah Kategori Baru'"></h3>
                <button type="button" @click="modalCategoryOpen = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg">&times;</button>
            </div>
            <form :action="categoryFormAction" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Nama Kategori (Indonesia 🇮🇩) <span class="text-rose-500">*</span></label>
                    <input type="text" name="name_id" x-model="categoryForm.name_id" required placeholder="Contoh: Forklift & Material Handling"
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-amber-500/20">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Nama (English 🇬🇧)</label>
                        <input type="text" name="name_en" x-model="categoryForm.name_en" placeholder="Contoh: Forklift"
                               class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Nama (Mandarin 🇨🇳)</label>
                        <input type="text" name="name_zh" x-model="categoryForm.name_zh" placeholder="Contoh: 叉车"
                               class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold">
                    </div>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Slug URL <span class="text-slate-400 font-normal">(Opsional, otomatis dari nama)</span></label>
                    <input type="text" name="slug" x-model="categoryForm.slug" placeholder="forklift"
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs font-mono font-bold text-slate-800">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Gambar / Thumbnail Kategori</label>
                    <div class="flex items-center space-x-3 mt-1.5">
                        <template x-if="categoryForm.image_url">
                            <img :src="categoryForm.image_url" class="w-12 h-12 rounded-xl object-cover border border-slate-200 shrink-0">
                        </template>
                        <input type="file" name="image" accept="image/*"
                               class="text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                    </div>
                </div>
                <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-2">
                    <button type="button" @click="modalCategoryOpen = false" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl text-xs font-black bg-amber-500 hover:bg-amber-600 text-slate-950 shadow-sm" x-text="isEditCategory ? 'Simpan Perubahan' : 'Tambah Kategori'"></button>
                </div>
            </form>
        </div>
    </div>

    <!-- 2. PRODUCT MODAL (Multilingual Tabs matching ERP Light Theme) -->
    <div x-show="modalProductOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" x-cloak>
        <div @click.away="modalProductOpen = false" class="bg-white w-full max-w-3xl rounded-3xl shadow-2xl border border-slate-200 overflow-hidden animate-scaleIn max-h-[90vh] flex flex-col">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50 shrink-0">
                <div class="flex items-center space-x-2.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                    <h3 class="text-sm font-black text-slate-900 tracking-wide" x-text="isEditProduct ? 'Edit Data Produk' : 'Tambah Produk Baru ke Katalog'"></h3>
                </div>
                <button type="button" @click="modalProductOpen = false" class="text-slate-400 hover:text-slate-600 font-bold text-xl leading-none transition">&times;</button>
            </div>
            
            <form :action="productFormAction" method="POST" enctype="multipart/form-data" class="p-6 space-y-5 overflow-y-auto">
                @csrf
                
                <!-- CARD 1: Multilingual Tabs (English, Indonesia, Chinese) -->
                <div class="bg-slate-50/70 border border-slate-200/80 rounded-2xl p-5 space-y-4">
                    <!-- Language Switcher Tabs -->
                    <div class="flex items-center justify-between border-b border-slate-200/80 pb-3">
                        <div class="flex items-center space-x-1.5 p-1 bg-slate-200/60 rounded-xl">
                            <button type="button" @click="productLangTab = 'en'"
                                    :class="productLangTab === 'en' ? 'bg-white text-slate-900 font-black shadow-xs' : 'text-slate-600 hover:text-slate-900 font-bold'"
                                    class="px-3.5 py-1.5 rounded-lg text-xs transition">
                                English
                            </button>
                            <button type="button" @click="productLangTab = 'id'"
                                    :class="productLangTab === 'id' ? 'bg-white text-slate-900 font-black shadow-xs' : 'text-slate-600 hover:text-slate-900 font-bold'"
                                    class="px-3.5 py-1.5 rounded-lg text-xs transition">
                                Indonesia
                            </button>
                            <button type="button" @click="productLangTab = 'zh'"
                                    :class="productLangTab === 'zh' ? 'bg-white text-slate-900 font-black shadow-xs' : 'text-slate-600 hover:text-slate-900 font-bold'"
                                    class="px-3.5 py-1.5 rounded-lg text-xs transition">
                                Chinese
                            </button>
                        </div>
                        <span class="text-[11px] font-bold text-amber-700 bg-amber-50 border border-amber-200/80 px-2.5 py-1 rounded-lg">
                            <span x-text="productLangTab === 'en' ? 'English Content' : (productLangTab === 'id' ? 'Konten Indonesia' : '中文内容')"></span>
                        </span>
                    </div>

                    <!-- English Content Tab -->
                    <div x-show="productLangTab === 'en'" class="space-y-4">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 mb-1">Title (EN) <span class="text-rose-500">*</span></label>
                            <input type="text" name="title_en" x-model="productForm.title_en" @input="onTitleInput('en')" placeholder="Enter English product title"
                                   class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 mb-1">Description (EN)</label>
                            <div class="border border-slate-200 rounded-xl overflow-hidden bg-white focus-within:ring-2 focus-within:ring-amber-500/20 focus-within:border-amber-500 transition shadow-2xs">
                                <!-- Toolbar -->
                                <div class="flex items-center flex-wrap gap-1 px-3 py-2 border-b border-slate-100 bg-slate-50/80 text-slate-600">
                                    <button type="button" @click="insertFormat('b')" title="Bold" class="w-6 h-6 flex items-center justify-center rounded text-slate-600 hover:text-slate-900 hover:bg-slate-200/80 font-bold text-xs transition">B</button>
                                    <button type="button" @click="insertFormat('i')" title="Italic" class="w-6 h-6 flex items-center justify-center rounded text-slate-600 hover:text-slate-900 hover:bg-slate-200/80 italic font-serif text-xs transition">I</button>
                                    <button type="button" @click="insertFormat('u')" title="Underline" class="w-6 h-6 flex items-center justify-center rounded text-slate-600 hover:text-slate-900 hover:bg-slate-200/80 underline text-xs transition">U</button>
                                    <button type="button" @click="insertFormat('s')" title="Strikethrough" class="w-6 h-6 flex items-center justify-center rounded text-slate-600 hover:text-slate-900 hover:bg-slate-200/80 line-through text-xs transition">S</button>
                                    <div class="h-3 w-px bg-slate-200 mx-0.5"></div>
                                    <button type="button" @click="insertFormat('link')" title="Insert Link" class="w-6 h-6 flex items-center justify-center rounded text-slate-600 hover:text-slate-900 hover:bg-slate-200/80 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                    </button>
                                    <div class="h-3 w-px bg-slate-200 mx-0.5"></div>
                                    <button type="button" @click="insertFormat('h2')" title="Heading 2" class="w-6 h-6 flex items-center justify-center rounded text-slate-600 hover:text-slate-900 hover:bg-slate-200/80 font-bold text-[11px] transition">H₂</button>
                                    <button type="button" @click="insertFormat('h3')" title="Heading 3" class="w-6 h-6 flex items-center justify-center rounded text-slate-600 hover:text-slate-900 hover:bg-slate-200/80 font-bold text-[11px] transition">H₃</button>
                                    <div class="h-3 w-px bg-slate-200 mx-0.5"></div>
                                    <button type="button" @click="insertFormat('quote')" title="Quote" class="w-6 h-6 flex items-center justify-center rounded text-slate-600 hover:text-slate-900 hover:bg-slate-200/80 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                    </button>
                                    <button type="button" @click="insertFormat('code')" title="Code" class="w-6 h-6 flex items-center justify-center rounded text-slate-600 hover:text-slate-900 hover:bg-slate-200/80 font-mono font-bold text-[10px] transition">&lt;/&gt;</button>
                                    <div class="h-3 w-px bg-slate-200 mx-0.5"></div>
                                    <button type="button" @click="insertFormat('ul')" title="Bullet List" class="w-6 h-6 flex items-center justify-center rounded text-slate-600 hover:text-slate-900 hover:bg-slate-200/80 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16M2 6h.01M2 12h.01M2 18h.01"></path></svg>
                                    </button>
                                    <button type="button" @click="insertFormat('ol')" title="Numbered List" class="w-6 h-6 flex items-center justify-center rounded text-slate-600 hover:text-slate-900 hover:bg-slate-200/80 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 6h11M9 12h11M9 18h11M4 6v4m0-4L3 7m1 7v4m-1 0h2"></path></svg>
                                    </button>
                                    <button type="button" @click="insertFormat('img')" title="Image Link" class="w-6 h-6 flex items-center justify-center rounded text-slate-600 hover:text-slate-900 hover:bg-slate-200/80 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </button>
                                    <div class="h-3 w-px bg-slate-200 mx-0.5"></div>
                                    <button type="button" @click="insertFormat('undo')" title="Undo" class="w-6 h-6 flex items-center justify-center rounded text-slate-600 hover:text-slate-900 hover:bg-slate-200/80 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a5 5 0 015 5v2m-15-7l4-4m-4 4l4 4"></path></svg>
                                    </button>
                                    <button type="button" @click="insertFormat('redo')" title="Redo" class="w-6 h-6 flex items-center justify-center rounded text-slate-600 hover:text-slate-900 hover:bg-slate-200/80 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 10H11a5 5 0 00-5 5v2m15-7l-4-4m4 4l-4 4"></path></svg>
                                    </button>
                                </div>
                                <textarea name="desc_en" x-ref="desc_en" x-model="productForm.desc_en" rows="5"
                                          placeholder="Product description, technical specifications, and key features..."
                                          class="w-full bg-white p-3.5 text-xs font-normal text-slate-800 placeholder-slate-400 focus:outline-none resize-y min-h-[130px]"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Indonesia Content Tab -->
                    <div x-show="productLangTab === 'id'" class="space-y-4">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 mb-1">Title (ID) <span class="text-rose-500">*</span></label>
                            <input type="text" name="title_id" x-model="productForm.title_id" @input="onTitleInput('id')" placeholder="Masukkan nama produk bahasa Indonesia"
                                   class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 mb-1">Description (ID)</label>
                            <div class="border border-slate-200 rounded-xl overflow-hidden bg-white focus-within:ring-2 focus-within:ring-amber-500/20 focus-within:border-amber-500 transition shadow-2xs">
                                <div class="flex items-center flex-wrap gap-1 px-3 py-2 border-b border-slate-100 bg-slate-50/80 text-slate-600">
                                    <button type="button" @click="insertFormat('b')" title="Bold" class="w-6 h-6 flex items-center justify-center rounded text-slate-600 hover:text-slate-900 hover:bg-slate-200/80 font-bold text-xs transition">B</button>
                                    <button type="button" @click="insertFormat('i')" title="Italic" class="w-6 h-6 flex items-center justify-center rounded text-slate-600 hover:text-slate-900 hover:bg-slate-200/80 italic font-serif text-xs transition">I</button>
                                    <button type="button" @click="insertFormat('u')" title="Underline" class="w-6 h-6 flex items-center justify-center rounded text-slate-600 hover:text-slate-900 hover:bg-slate-200/80 underline text-xs transition">U</button>
                                    <button type="button" @click="insertFormat('s')" title="Strikethrough" class="w-6 h-6 flex items-center justify-center rounded text-slate-600 hover:text-slate-900 hover:bg-slate-200/80 line-through text-xs transition">S</button>
                                    <div class="h-3 w-px bg-slate-200 mx-0.5"></div>
                                    <button type="button" @click="insertFormat('link')" title="Insert Link" class="w-6 h-6 flex items-center justify-center rounded text-slate-600 hover:text-slate-900 hover:bg-slate-200/80 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                    </button>
                                    <div class="h-3 w-px bg-slate-200 mx-0.5"></div>
                                    <button type="button" @click="insertFormat('h2')" title="Heading 2" class="w-6 h-6 flex items-center justify-center rounded text-slate-600 hover:text-slate-900 hover:bg-slate-200/80 font-bold text-[11px] transition">H₂</button>
                                    <button type="button" @click="insertFormat('h3')" title="Heading 3" class="w-6 h-6 flex items-center justify-center rounded text-slate-600 hover:text-slate-900 hover:bg-slate-200/80 font-bold text-[11px] transition">H₃</button>
                                    <div class="h-3 w-px bg-slate-200 mx-0.5"></div>
                                    <button type="button" @click="insertFormat('quote')" title="Quote" class="w-6 h-6 flex items-center justify-center rounded text-slate-600 hover:text-slate-900 hover:bg-slate-200/80 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                    </button>
                                    <button type="button" @click="insertFormat('code')" title="Code" class="w-6 h-6 flex items-center justify-center rounded text-slate-600 hover:text-slate-900 hover:bg-slate-200/80 font-mono font-bold text-[10px] transition">&lt;/&gt;</button>
                                    <div class="h-3 w-px bg-slate-200 mx-0.5"></div>
                                    <button type="button" @click="insertFormat('ul')" title="Bullet List" class="w-6 h-6 flex items-center justify-center rounded text-slate-600 hover:text-slate-900 hover:bg-slate-200/80 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16M2 6h.01M2 12h.01M2 18h.01"></path></svg>
                                    </button>
                                    <button type="button" @click="insertFormat('ol')" title="Numbered List" class="w-6 h-6 flex items-center justify-center rounded text-slate-600 hover:text-slate-900 hover:bg-slate-200/80 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 6h11M9 12h11M9 18h11M4 6v4m0-4L3 7m1 7v4m-1 0h2"></path></svg>
                                    </button>
                                    <button type="button" @click="insertFormat('img')" title="Image Link" class="w-6 h-6 flex items-center justify-center rounded text-slate-600 hover:text-slate-900 hover:bg-slate-200/80 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </button>
                                    <div class="h-3 w-px bg-slate-200 mx-0.5"></div>
                                    <button type="button" @click="insertFormat('undo')" title="Undo" class="w-6 h-6 flex items-center justify-center rounded text-slate-600 hover:text-slate-900 hover:bg-slate-200/80 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a5 5 0 015 5v2m-15-7l4-4m-4 4l4 4"></path></svg>
                                    </button>
                                    <button type="button" @click="insertFormat('redo')" title="Redo" class="w-6 h-6 flex items-center justify-center rounded text-slate-600 hover:text-slate-900 hover:bg-slate-200/80 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 10H11a5 5 0 00-5 5v2m15-7l-4-4m4 4l-4 4"></path></svg>
                                    </button>
                                </div>
                                <textarea name="desc_id" x-ref="desc_id" x-model="productForm.desc_id" rows="5"
                                          placeholder="Deskripsi produk, spesifikasi teknis, dan keunggulan..."
                                          class="w-full bg-white p-3.5 text-xs font-normal text-slate-800 placeholder-slate-400 focus:outline-none resize-y min-h-[130px]"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Chinese Content Tab -->
                    <div x-show="productLangTab === 'zh'" class="space-y-4">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 mb-1">Title (ZH) <span class="text-rose-500">*</span></label>
                            <input type="text" name="title_zh" x-model="productForm.title_zh" @input="onTitleInput('zh')" placeholder="输入中文产品标题"
                                   class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 mb-1">Description (ZH)</label>
                            <div class="border border-slate-200 rounded-xl overflow-hidden bg-white focus-within:ring-2 focus-within:ring-amber-500/20 focus-within:border-amber-500 transition shadow-2xs">
                                <div class="flex items-center flex-wrap gap-1 px-3 py-2 border-b border-slate-100 bg-slate-50/80 text-slate-600">
                                    <button type="button" @click="insertFormat('b')" title="Bold" class="w-6 h-6 flex items-center justify-center rounded text-slate-600 hover:text-slate-900 hover:bg-slate-200/80 font-bold text-xs transition">B</button>
                                    <button type="button" @click="insertFormat('i')" title="Italic" class="w-6 h-6 flex items-center justify-center rounded text-slate-600 hover:text-slate-900 hover:bg-slate-200/80 italic font-serif text-xs transition">I</button>
                                    <button type="button" @click="insertFormat('u')" title="Underline" class="w-6 h-6 flex items-center justify-center rounded text-slate-600 hover:text-slate-900 hover:bg-slate-200/80 underline text-xs transition">U</button>
                                    <button type="button" @click="insertFormat('s')" title="Strikethrough" class="w-6 h-6 flex items-center justify-center rounded text-slate-600 hover:text-slate-900 hover:bg-slate-200/80 line-through text-xs transition">S</button>
                                    <div class="h-3 w-px bg-slate-200 mx-0.5"></div>
                                    <button type="button" @click="insertFormat('link')" title="Insert Link" class="w-6 h-6 flex items-center justify-center rounded text-slate-600 hover:text-slate-900 hover:bg-slate-200/80 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                    </button>
                                    <div class="h-3 w-px bg-slate-200 mx-0.5"></div>
                                    <button type="button" @click="insertFormat('h2')" title="Heading 2" class="w-6 h-6 flex items-center justify-center rounded text-slate-600 hover:text-slate-900 hover:bg-slate-200/80 font-bold text-[11px] transition">H₂</button>
                                    <button type="button" @click="insertFormat('h3')" title="Heading 3" class="w-6 h-6 flex items-center justify-center rounded text-slate-600 hover:text-slate-900 hover:bg-slate-200/80 font-bold text-[11px] transition">H₃</button>
                                    <div class="h-3 w-px bg-slate-200 mx-0.5"></div>
                                    <button type="button" @click="insertFormat('quote')" title="Quote" class="w-6 h-6 flex items-center justify-center rounded text-slate-600 hover:text-slate-900 hover:bg-slate-200/80 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                    </button>
                                    <button type="button" @click="insertFormat('code')" title="Code" class="w-6 h-6 flex items-center justify-center rounded text-slate-600 hover:text-slate-900 hover:bg-slate-200/80 font-mono font-bold text-[10px] transition">&lt;/&gt;</button>
                                    <div class="h-3 w-px bg-slate-200 mx-0.5"></div>
                                    <button type="button" @click="insertFormat('ul')" title="Bullet List" class="w-6 h-6 flex items-center justify-center rounded text-slate-600 hover:text-slate-900 hover:bg-slate-200/80 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16M2 6h.01M2 12h.01M2 18h.01"></path></svg>
                                    </button>
                                    <button type="button" @click="insertFormat('ol')" title="Numbered List" class="w-6 h-6 flex items-center justify-center rounded text-slate-600 hover:text-slate-900 hover:bg-slate-200/80 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 6h11M9 12h11M9 18h11M4 6v4m0-4L3 7m1 7v4m-1 0h2"></path></svg>
                                    </button>
                                    <button type="button" @click="insertFormat('img')" title="Image Link" class="w-6 h-6 flex items-center justify-center rounded text-slate-600 hover:text-slate-900 hover:bg-slate-200/80 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </button>
                                    <div class="h-3 w-px bg-slate-200 mx-0.5"></div>
                                    <button type="button" @click="insertFormat('undo')" title="Undo" class="w-6 h-6 flex items-center justify-center rounded text-slate-600 hover:text-slate-900 hover:bg-slate-200/80 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a5 5 0 015 5v2m-15-7l4-4m-4 4l4 4"></path></svg>
                                    </button>
                                    <button type="button" @click="insertFormat('redo')" title="Redo" class="w-6 h-6 flex items-center justify-center rounded text-slate-600 hover:text-slate-900 hover:bg-slate-200/80 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 10H11a5 5 0 00-5 5v2m15-7l-4-4m4 4l-4 4"></path></svg>
                                    </button>
                                </div>
                                <textarea name="desc_zh" x-ref="desc_zh" x-model="productForm.desc_zh" rows="5"
                                          placeholder="中文产品规格与详细说明..."
                                          class="w-full bg-white p-3.5 text-xs font-normal text-slate-800 placeholder-slate-400 focus:outline-none resize-y min-h-[130px]"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD 2: Pengaturan Produk -->
                <div>
                    <h4 class="text-xs font-black text-slate-900 mb-2 uppercase tracking-wider">Pengaturan Produk</h4>
                    <div class="bg-slate-50/70 border border-slate-200/80 rounded-2xl p-5 space-y-4">
                        <!-- Slug Field with Gold Lock Badge -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 mb-1">Slug <span class="text-rose-500">*</span></label>
                            <div class="relative flex items-center">
                                <input type="text" name="slug" x-model="productForm.slug" :readonly="slugLocked" required
                                       placeholder="pmsm-mp-fx-series"
                                       class="w-full bg-white border border-slate-200 rounded-xl pl-3.5 pr-12 py-2.5 text-xs font-mono font-bold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition"
                                       :class="{'cursor-not-allowed bg-slate-100 text-slate-500': slugLocked}">
                                <button type="button" @click="slugLocked = !slugLocked" 
                                        :title="slugLocked ? 'Kunci aktif: Slug otomatis terisi dari Judul. Klik untuk edit manual.' : 'Kunci terbuka: Mode edit manual aktif. Klik untuk mengunci kembali.'"
                                        class="absolute right-2 w-7 h-7 rounded-lg bg-amber-400 hover:bg-amber-300 text-slate-950 flex items-center justify-center transition shadow-xs">
                                    <template x-if="slugLocked">
                                        <!-- Locked Keyhole Icon -->
                                        <svg class="w-3.5 h-3.5 text-slate-950" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                        </svg>
                                    </template>
                                    <template x-if="!slugLocked">
                                        <!-- Unlocked Keyhole Icon -->
                                        <svg class="w-3.5 h-3.5 text-slate-950" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10 2a5 5 0 00-5 5v2a2 2 0 00-2 2v5a2 2 0 002 2h10a2 2 0 002-2v-5a2 2 0 00-2-2H7V7a3 3 0 015.905-.75 1 1 0 001.937-.5A5.002 5.002 0 0010 2z" />
                                        </svg>
                                    </template>
                                </button>
                            </div>
                        </div>

                        <!-- 2 Columns: Kategori & Image -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Left: Kategori + Trending -->
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Kategori <span class="text-rose-500">*</span></label>
                                    <div class="relative">
                                        <select name="category_slug" x-model="productForm.category_slug" required
                                                class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition appearance-none cursor-pointer">
                                            <option value="" disabled :selected="!productForm.category_slug">Select an option</option>
                                            @foreach($categories as $cat)
                                                <option value="{{ $cat->slug }}">{{ $cat->display_name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="pt-1">
                                    <label class="inline-flex items-center space-x-2.5 cursor-pointer select-none">
                                        <input type="checkbox" name="trending" value="1" x-model="productForm.trending" 
                                               class="w-4 h-4 rounded text-amber-500 focus:ring-amber-400 border-slate-300">
                                        <span class="text-xs font-bold text-slate-700">Tampilkan sebagai Produk Trending ⭐</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Right: Image Drag & Drop -->
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-1">Image</label>
                                <div @click="$refs.productImageFile.click()"
                                     @dragover.prevent="dragOver = true"
                                     @dragleave.prevent="dragOver = false"
                                     @drop.prevent="handleImageDrop($event)"
                                     :class="dragOver ? 'border-amber-500 bg-amber-50' : 'border-slate-300 hover:border-amber-500 bg-white'"
                                     class="border border-dashed rounded-xl p-3 min-h-[82px] flex items-center justify-center cursor-pointer transition relative group">
                                    <input type="file" name="image" x-ref="productImageFile" @change="handleImageSelect($event)" accept="image/*" class="hidden">
                                    
                                    <template x-if="productImagePreview">
                                        <div class="flex items-center space-x-3 w-full px-1">
                                            <img :src="productImagePreview" class="w-10 h-10 rounded-xl object-cover border border-slate-200 shrink-0">
                                            <div class="truncate text-left text-xs flex-1">
                                                <span class="text-slate-800 font-bold block truncate" x-text="productImageName || 'Gambar Terpilih'"></span>
                                                <span class="text-amber-600 text-[11px] font-bold group-hover:underline">Klik / drop untuk ganti</span>
                                            </div>
                                        </div>
                                    </template>
                                    
                                    <template x-if="!productImagePreview">
                                        <div class="text-center py-2">
                                            <span class="text-xs text-slate-500 font-medium">Drag & Drop your files or <strong class="text-amber-600 font-bold underline group-hover:text-amber-700">Browse</strong></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Buttons -->
                <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-2 shrink-0">
                    <button type="button" @click="modalProductOpen = false" 
                            class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-5 py-2 rounded-xl text-xs font-black bg-amber-500 hover:bg-amber-600 text-slate-950 shadow-sm transition active:scale-95" 
                            x-text="isEditProduct ? 'Simpan Perubahan' : 'Tambah Produk'">
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 3. PARTNER MODAL -->
    <div x-show="modalPartnerOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" x-cloak>
        <div @click.away="modalPartnerOpen = false" class="bg-white w-full max-w-md rounded-3xl shadow-2xl border border-slate-200 overflow-hidden animate-scaleIn">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="text-sm font-black text-slate-900" x-text="isEditPartner ? 'Edit Mitra Kerjasama' : 'Tambah Mitra Baru'"></h3>
                <button type="button" @click="modalPartnerOpen = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg">&times;</button>
            </div>
            <form :action="partnerFormAction" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Nama Perusahaan Mitra <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" x-model="partnerForm.name" required placeholder="Contoh: PT. Haier Electrical Appliances Indonesia"
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Logo Mitra <span x-show="!isEditPartner" class="text-rose-500">*</span></label>
                    <div class="flex items-center space-x-3 mt-1.5">
                        <template x-if="partnerForm.image_url">
                            <img :src="partnerForm.image_url" class="w-12 h-12 rounded-xl object-contain border border-slate-200 shrink-0 bg-slate-50 p-1">
                        </template>
                        <input type="file" name="image" accept="image/*" :required="!isEditPartner"
                               class="text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                    </div>
                </div>
                <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-2">
                    <button type="button" @click="modalPartnerOpen = false" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl text-xs font-black bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm" x-text="isEditPartner ? 'Simpan Perubahan' : 'Tambah Mitra'"></button>
                </div>
            </form>
        </div>
    </div>

    <!-- 4. GALLERY MODAL -->
    <div x-show="modalGalleryOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" x-cloak>
        <div @click.away="modalGalleryOpen = false" class="bg-white w-full max-w-lg rounded-3xl shadow-2xl border border-slate-200 overflow-hidden animate-scaleIn">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="text-sm font-black text-slate-900" x-text="isEditGallery ? 'Edit Foto Galeri' : 'Unggah Foto Galeri Baru'"></h3>
                <button type="button" @click="modalGalleryOpen = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg">&times;</button>
            </div>
            <form :action="galleryFormAction" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Kategori Galeri <span class="text-rose-500">*</span></label>
                    <select name="category_id" x-model="galleryForm.category_id" required
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->display_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Judul Foto / Proyek <span class="text-rose-500">*</span></label>
                    <input type="text" name="title_id" x-model="galleryForm.title_id" required placeholder="Contoh: Pengiriman Armada Forklift Cikarang"
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Deskripsi Singkat</label>
                    <textarea name="description_id" x-model="galleryForm.description_id" rows="2" placeholder="Catatan dokumentasi..."
                              class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs font-semibold"></textarea>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Foto Dokumentasi <span x-show="!isEditGallery" class="text-rose-500">*</span></label>
                    <div class="flex items-center space-x-3 mt-1.5">
                        <template x-if="galleryForm.image_url">
                            <img :src="galleryForm.image_url" class="w-12 h-12 rounded-xl object-cover border border-slate-200 shrink-0">
                        </template>
                        <input type="file" name="image" accept="image/*" :required="!isEditGallery"
                               class="text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                    </div>
                </div>
                <div>
                    <label class="flex items-center space-x-2 cursor-pointer select-none">
                        <input type="checkbox" name="status" value="1" x-model="galleryForm.status" class="rounded text-emerald-600 focus:ring-emerald-500 w-4 h-4">
                        <span class="text-xs font-black text-slate-800">Status Tayang di Website Publik</span>
                    </label>
                </div>
                <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-2">
                    <button type="button" @click="modalGalleryOpen = false" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl text-xs font-black bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm" x-text="isEditGallery ? 'Simpan Perubahan' : 'Unggah Foto'"></button>
                </div>
            </form>
        </div>
    </div>

    <!-- 5. TESTIMONIAL MODAL -->
    <div x-show="modalTestimonialOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" x-cloak>
        <div @click.away="modalTestimonialOpen = false" class="bg-white w-full max-w-lg rounded-3xl shadow-2xl border border-slate-200 overflow-hidden animate-scaleIn">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="text-sm font-black text-slate-900" x-text="isEditTestimonial ? 'Edit Testimoni' : 'Tambah Testimoni Baru'"></h3>
                <button type="button" @click="modalTestimonialOpen = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg">&times;</button>
            </div>
            <form :action="testimonialFormAction" method="POST" class="p-6 space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Nama Reviewer <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" x-model="testimonialForm.name" required placeholder="Budi Santoso"
                               class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-purple-500/20">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Jabatan / Perusahaan</label>
                        <input type="text" name="role" x-model="testimonialForm.role" placeholder="Plant Manager, PT XYZ"
                               class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs font-semibold">
                    </div>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Rating Bintang (1 - 5) <span class="text-rose-500">*</span></label>
                    <select name="rating" x-model="testimonialForm.rating" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold">
                        <option value="5">⭐⭐⭐⭐⭐ (5 Bintang - Sangat Puas)</option>
                        <option value="4">⭐⭐⭐⭐ (4 Bintang - Puas)</option>
                        <option value="3">⭐⭐⭐ (3 Bintang - Cukup)</option>
                        <option value="2">⭐⭐ (2 Bintang - Kurang)</option>
                        <option value="1">⭐ (1 Bintang - Tidak Puas)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Pesan Ulasan / Testimoni <span class="text-rose-500">*</span></label>
                    <textarea name="message_id" x-model="testimonialForm.message_id" rows="4" required placeholder="Tulis ulasan klien di sini..."
                              class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs font-semibold"></textarea>
                </div>
                <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-2">
                    <button type="button" @click="modalTestimonialOpen = false" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl text-xs font-black bg-purple-600 hover:bg-purple-700 text-white shadow-sm" x-text="isEditTestimonial ? 'Simpan Perubahan' : 'Tambah Testimoni'"></button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
function cmsManager(initialTab) {
    return {
        activeTab: initialTab || 'categories',
        
        // Category Search & Modal
        searchCategory: '',
        modalCategoryOpen: false,
        isEditCategory: false,
        categoryFormAction: '{{ route("erp.cms.categories.store") }}',
        categoryForm: {
            id: null,
            name_id: '',
            name_en: '',
            name_zh: '',
            slug: '',
            image_url: null,
        },

        // Product Search & Modal
        searchProduct: '',
        filterProductCategory: 'all',
        modalProductOpen: false,
        isEditProduct: false,
        productFormAction: '{{ route("erp.cms.products.store") }}',
        productLangTab: 'en',
        slugLocked: true,
        dragOver: false,
        productImagePreview: null,
        productImageName: '',
        productForm: {
            id: null,
            title_id: '',
            title_en: '',
            title_zh: '',
            slug: '',
            category_slug: '{{ $categories->first()?->slug ?? "forklift" }}',
            trending: false,
            desc_id: '',
            desc_en: '',
            desc_zh: '',
            image_url: null,
        },

        // Partner Modal
        modalPartnerOpen: false,
        isEditPartner: false,
        partnerFormAction: '{{ route("erp.cms.partners.store") }}',
        partnerForm: {
            id: null,
            name: '',
            image_url: null,
        },

        // Gallery Modal
        modalGalleryOpen: false,
        isEditGallery: false,
        galleryFormAction: '{{ route("erp.cms.galleries.store") }}',
        galleryForm: {
            id: null,
            category_id: '{{ $categories->first()?->id ?? 1 }}',
            title_id: '',
            title_en: '',
            title_zh: '',
            description_id: '',
            image_url: null,
            status: true,
        },

        // Testimonial Modal
        modalTestimonialOpen: false,
        isEditTestimonial: false,
        testimonialFormAction: '{{ route("erp.cms.testimonials.store") }}',
        testimonialForm: {
            id: null,
            name: '',
            role: '',
            rating: 5,
            message_id: '',
            message_en: '',
            message_zh: '',
        },

        switchTab(tabKey) {
            this.activeTab = tabKey;
            const url = new URL(window.location);
            url.searchParams.set('tab', tabKey);
            window.history.replaceState({}, '', url);
        },

        getTabTitle(tab) {
            const map = {
                'categories': 'Kategori Produk',
                'products': 'Katalog Produk',
                'galleries': 'Galeri Dokumentasi',
                'partners': 'Mitra Kerjasama',
                'testimonials': 'Testimoni Klien',
                'about': 'Tentang Perusahaan',
                'contact': 'Kontak & QR WeChat'
            };
            return map[tab] || 'Konten CMS';
        },

        matchesSearchCategory(str) {
            if (!this.searchCategory) return true;
            return str.includes(this.searchCategory.toLowerCase());
        },

        matchesSearchProduct(str, catSlug) {
            if (this.filterProductCategory !== 'all' && catSlug !== this.filterProductCategory) return false;
            if (!this.searchProduct) return true;
            return str.includes(this.searchProduct.toLowerCase());
        },

        // CATEGORY MODAL HANDLERS
        openCategoryModal() {
            this.isEditCategory = false;
            this.categoryFormAction = '{{ route("erp.cms.categories.store") }}';
            this.categoryForm = {
                id: null,
                name_id: '',
                name_en: '',
                name_zh: '',
                slug: '',
                image_url: null,
            };
            this.modalCategoryOpen = true;
        },

        editCategory(cat) {
            this.isEditCategory = true;
            this.categoryFormAction = `/erp/cms/categories/${cat.id}/update`;
            this.categoryForm = { ...cat };
            this.modalCategoryOpen = true;
        },

        // PRODUCT MODAL HANDLERS
        openProductModal() {
            this.isEditProduct = false;
            this.productFormAction = '{{ route("erp.cms.products.store") }}';
            this.productForm = {
                id: null,
                title_id: '',
                title_en: '',
                title_zh: '',
                slug: '',
                category_slug: '{{ $categories->first()?->slug ?? "forklift" }}',
                trending: false,
                desc_id: '',
                desc_en: '',
                desc_zh: '',
                image_url: null,
            };
            this.productLangTab = 'en';
            this.slugLocked = true;
            this.productImagePreview = null;
            this.productImageName = '';
            this.modalProductOpen = true;
        },

        editProduct(prod) {
            this.isEditProduct = true;
            this.productFormAction = `/erp/cms/products/${prod.id}/update`;
            this.productForm = { ...prod };
            this.productLangTab = 'en';
            this.slugLocked = true;
            this.productImagePreview = prod.image_url || null;
            this.productImageName = prod.image_url ? 'Gambar saat ini' : '';
            this.modalProductOpen = true;
        },

        onTitleInput(lang) {
            if (!this.slugLocked) return;
            const title = this.productForm['title_' + lang] || this.productForm.title_en || this.productForm.title_id || '';
            this.productForm.slug = this.slugify(title);
        },

        slugify(text) {
            if (!text) return '';
            return text.toString().toLowerCase()
                .trim()
                .replace(/\s+/g, '-')
                .replace(/[^\w\-]+/g, '')
                .replace(/\-\-+/g, '-');
        },

        insertFormat(tag) {
            const currentLang = this.productLangTab;
            const refKey = 'desc_' + currentLang;
            const textarea = this.$refs[refKey];
            if (!textarea) return;

            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            const text = textarea.value;
            const selected = text.substring(start, end);

            let replacement = '';

            switch(tag) {
                case 'b':
                    replacement = `<strong>${selected || 'bold text'}</strong>`;
                    break;
                case 'i':
                    replacement = `<em>${selected || 'italic text'}</em>`;
                    break;
                case 'u':
                    replacement = `<u>${selected || 'underlined text'}</u>`;
                    break;
                case 's':
                    replacement = `<s>${selected || 'strikethrough text'}</s>`;
                    break;
                case 'link':
                    const url = prompt('Masukkan URL Link:', 'https://');
                    if (url) {
                        replacement = `<a href="${url}" target="_blank" rel="noopener">${selected || url}</a>`;
                    } else {
                        return;
                    }
                    break;
                case 'h2':
                    replacement = `<h2>${selected || 'Heading 2'}</h2>`;
                    break;
                case 'h3':
                    replacement = `<h3>${selected || 'Heading 3'}</h3>`;
                    break;
                case 'quote':
                    replacement = `<blockquote>${selected || 'Quote text'}</blockquote>`;
                    break;
                case 'code':
                    replacement = `<code>${selected || 'code'}</code>`;
                    break;
                case 'ul':
                    replacement = `<ul>\n  <li>${selected || 'List item 1'}</li>\n  <li>List item 2</li>\n</ul>`;
                    break;
                case 'ol':
                    replacement = `<ol>\n  <li>${selected || 'Step 1'}</li>\n  <li>Step 2</li>\n</ol>`;
                    break;
                case 'img':
                    const imgUrl = prompt('Masukkan URL Gambar:', 'https://');
                    if (imgUrl) {
                        replacement = `<img src="${imgUrl}" alt="${selected || 'Product image'}" class="my-3 rounded-lg max-w-full">`;
                    } else {
                        return;
                    }
                    break;
                case 'undo':
                    document.execCommand('undo');
                    return;
                case 'redo':
                    document.execCommand('redo');
                    return;
            }

            textarea.value = text.substring(0, start) + replacement + text.substring(end);
            this.productForm['desc_' + currentLang] = textarea.value;

            this.$nextTick(() => {
                textarea.focus();
                textarea.setSelectionRange(start + replacement.length, start + replacement.length);
            });
        },

        handleImageDrop(e) {
            this.dragOver = false;
            if (e.dataTransfer.files && e.dataTransfer.files[0]) {
                const file = e.dataTransfer.files[0];
                this.$refs.productImageFile.files = e.dataTransfer.files;
                this.productImageName = file.name;
                this.productImagePreview = URL.createObjectURL(file);
            }
        },

        handleImageSelect(e) {
            if (e.target.files && e.target.files[0]) {
                const file = e.target.files[0];
                this.productImageName = file.name;
                this.productImagePreview = URL.createObjectURL(file);
            }
        },

        // PARTNER MODAL HANDLERS
        openPartnerModal() {
            this.isEditPartner = false;
            this.partnerFormAction = '{{ route("erp.cms.partners.store") }}';
            this.partnerForm = {
                id: null,
                name: '',
                image_url: null,
            };
            this.modalPartnerOpen = true;
        },

        editPartner(part) {
            this.isEditPartner = true;
            this.partnerFormAction = `/erp/cms/partners/${part.id}/update`;
            this.partnerForm = { ...part };
            this.modalPartnerOpen = true;
        },

        // GALLERY MODAL HANDLERS
        openGalleryModal() {
            this.isEditGallery = false;
            this.galleryFormAction = '{{ route("erp.cms.galleries.store") }}';
            this.galleryForm = {
                id: null,
                category_id: '{{ $categories->first()?->id ?? 1 }}',
                title_id: '',
                title_en: '',
                title_zh: '',
                description_id: '',
                image_url: null,
                status: true,
            };
            this.modalGalleryOpen = true;
        },

        editGallery(gal) {
            this.isEditGallery = true;
            this.galleryFormAction = `/erp/cms/galleries/${gal.id}/update`;
            this.galleryForm = { ...gal };
            this.modalGalleryOpen = true;
        },

        async toggleGalleryStatus(galleryId) {
            try {
                const res = await fetch(`/erp/cms/galleries/${galleryId}/toggle-status`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                const result = await res.json();
                if (result.success) {
                    window.location.reload();
                }
            } catch(e) {
                alert('Gagal mengubah status');
            }
        },

        // TESTIMONIAL MODAL HANDLERS
        openTestimonialModal() {
            this.isEditTestimonial = false;
            this.testimonialFormAction = '{{ route("erp.cms.testimonials.store") }}';
            this.testimonialForm = {
                id: null,
                name: '',
                role: '',
                rating: 5,
                message_id: '',
                message_en: '',
                message_zh: '',
            };
            this.modalTestimonialOpen = true;
        },

        editTestimonial(testi) {
            this.isEditTestimonial = true;
            this.testimonialFormAction = `/erp/cms/testimonials/${testi.id}/update`;
            this.testimonialForm = { ...testi };
            this.modalTestimonialOpen = true;
        }
    };
}
</script>
@endsection
