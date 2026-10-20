@extends('layouts.erp')

@section('title', 'Menu & Hak Akses Management - Keylock Indonesia')

@section('breadcrumbs')
    <a href="{{ route('erp.index') }}" class="hover:text-slate-800">Home</a>
    <span>›</span>
    <span class="text-slate-800 font-semibold">General System</span>
    <span>›</span>
    <span class="text-slate-800 font-semibold" x-text="lang === 'zh' ? '菜单与权限管理' : 'Menu Management'">Menu Management</span>
@endsection

@section('header_title')
    General System • Menu & Hak Akses
@endsection

@section('content')
<div x-data="erpMenuManager('{{ $tab }}', {{ $selectedRoleId }}, @js($menus), @js($rootMenus), @js($roles), @js($roleMenuIds), @js($allAccesses))">

    <!-- ========================================================================= -->
    <!-- 1. PAGE HEADER                                                            -->
    <!-- ========================================================================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-black text-slate-900 tracking-tight" x-text="lang === 'zh' ? '菜单与角色权限管理' : 'Menu & Hak Akses Management'">
                Menu & Hak Akses Management
            </h2>
            <p class="text-xs text-slate-500 mt-1" x-text="lang === 'zh' ? '集中管理系统的层级菜单项结构、页面路由以及各个角色用户的菜单访问授权。' : 'Pusat pengelolaan struktur hierarki menu sistem, rute halaman, dan konfigurasi hak akses per role.'">
                Pusat pengelolaan struktur hierarki menu sistem, rute halaman, dan konfigurasi hak akses per role.
            </p>
        </div>

        <!-- Top Right Action Buttons -->
        <div class="flex items-center space-x-3 self-start md:self-auto">
            <!-- Button for Menu Tab -->
            <button type="button" 
                    x-show="activeTab === 'menu'"
                    @click="openCreateMenuModal()" 
                    class="bg-blue-600 hover:bg-blue-700 text-white font-extrabold px-5 py-2.5 rounded-full shadow-md hover:shadow-lg text-xs transition flex items-center space-x-2 active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                <span x-text="lang === 'zh' ? '添加新菜单' : 'Tambah Menu Baru'">Tambah Menu Baru</span>
            </button>

            <!-- Button for Access Tab -->
            <button type="button" 
                    x-show="activeTab === 'access'"
                    @click="saveAccessSync()" 
                    :disabled="isSavingAccess"
                    class="bg-yellow-400 hover:bg-yellow-300 text-slate-950 font-black px-5 py-2.5 rounded-full shadow-md hover:shadow-lg text-xs transition flex items-center space-x-2 active:scale-95 disabled:opacity-60">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                <span x-text="isSavingAccess ? (lang === 'zh' ? '正在保存...' : 'Menyimpan...') : (lang === 'zh' ? '保存权限配置' : 'Simpan Hak Akses')">Simpan Hak Akses</span>
            </button>
        </div>
    </div>

    <!-- Alert Success / Info -->
    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs font-bold flex items-center justify-between">
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- 2. PILL TABS BAR: TAB MENU & TAB HAK AKSES                                -->
    <!-- ========================================================================= -->
    <div class="flex flex-wrap items-center gap-3 mb-6">
        <!-- Tab 1: Menu -->
        <button type="button" 
                @click="switchTab('menu')"
                :class="activeTab === 'menu' ? 'bg-white text-slate-900 shadow-sm border border-slate-200/90 font-black' : 'bg-slate-100/80 text-slate-600 hover:bg-white hover:text-slate-900 border border-transparent font-bold'"
                class="px-4 py-2 rounded-2xl text-xs transition flex items-center space-x-2.5">
            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
            </svg>
            <span x-text="lang === 'zh' ? '菜单结构 (appmenu)' : 'Tab Menu'">Tab Menu</span>
            <span class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 text-[10px] font-black">
                {{ $menus->count() }}
            </span>
        </button>

        <!-- Tab 2: Hak Akses -->
        <button type="button" 
                @click="switchTab('access')"
                :class="activeTab === 'access' ? 'bg-white text-slate-900 shadow-sm border border-slate-200/90 font-black' : 'bg-slate-100/80 text-slate-600 hover:bg-white hover:text-slate-900 border border-transparent font-bold'"
                class="px-4 py-2 rounded-2xl text-xs transition flex items-center space-x-2.5">
            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
            <span x-text="lang === 'zh' ? '角色权限 (appmenuaccess)' : 'Tab Hak Akses'">Tab Hak Akses</span>
            <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[10px] font-black">
                {{ $allAccesses->count() }}
            </span>
        </button>
    </div>

    <!-- ========================================================================= -->
    <!-- 3. STATS OVERVIEW CARDS                                                   -->
    <!-- ========================================================================= -->
    <!-- Menu Stats -->
    <div x-show="activeTab === 'menu'" class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-[11px] font-bold text-slate-400 block mb-1" x-text="lang === 'zh' ? '菜单总数' : 'Total Menu Sistem'">Total Menu Sistem</span>
            <p class="text-2xl font-black text-slate-900">{{ $menus->count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-[11px] font-bold text-slate-400 block mb-1" x-text="lang === 'zh' ? '根菜单 / 分组' : 'Root / Header Kategori'">Root / Header Kategori</span>
            <p class="text-2xl font-black text-blue-600">{{ $rootMenus->count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-[11px] font-bold text-slate-400 block mb-1" x-text="lang === 'zh' ? '子菜单项' : 'Sub-Menu Item'">Sub-Menu Item</span>
            <p class="text-2xl font-black text-emerald-600">{{ $menus->where('menu_parent', '>', 0)->count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-[11px] font-bold text-slate-400 block mb-1" x-text="lang === 'zh' ? 'ERP 活跃显示' : 'Tampil di ERP'">Tampil di ERP</span>
            <p class="text-2xl font-black text-amber-500">{{ $menus->where('erp', 'Y')->count() }}</p>
        </div>
    </div>

    <!-- Access Stats -->
    <div x-show="activeTab === 'access'" class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-[11px] font-bold text-slate-400 block mb-1" x-text="lang === 'zh' ? '当前角色' : 'Role Terpilih'">Role Terpilih</span>
            <p class="text-lg font-black text-slate-900 truncate" x-text="getSelectedRoleLabel()">
                {{ $roles->firstWhere('id', $selectedRoleId)->label ?? 'Role' }}
            </p>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-[11px] font-bold text-slate-400 block mb-1" x-text="lang === 'zh' ? '可访问菜单数' : 'Menu Yang Diakses Role Ini'">Menu Diakses Role Ini</span>
            <p class="text-2xl font-black text-amber-600" x-text="currentRoleMenuIds.length">
                {{ count($roleMenuIds) }}
            </p>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-[11px] font-bold text-slate-400 block mb-1" x-text="lang === 'zh' ? '系统角色总数' : 'Total Role Sistem'">Total Role Sistem</span>
            <p class="text-2xl font-black text-blue-600">{{ $roles->count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-[11px] font-bold text-slate-400 block mb-1" x-text="lang === 'zh' ? '权限配置总条数' : 'Total Konfigurasi Akses'">Total Konfigurasi Akses</span>
            <p class="text-2xl font-black text-slate-700">{{ $allAccesses->count() }}</p>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 4. TAB 1: MENU LIST (CRUD MENU)                                          -->
    <!-- ========================================================================= -->
    <div x-show="activeTab === 'menu'" class="space-y-6">

        <!-- Search and Filter Bar -->
        <div class="bg-white p-4 rounded-3xl border border-slate-200/80 shadow-xs flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex-1 w-full md:w-auto relative">
                <input type="text" 
                       x-model="searchMenu"
                       :placeholder="lang === 'zh' ? '搜索菜单名称或路径 URL...' : 'Cari nama menu atau URL...'"
                       class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:border-yellow-400 focus:bg-white transition">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>

            <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                <!-- Filter Parent -->
                <select x-model="filterParent" class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold text-slate-700 outline-none focus:border-yellow-400">
                    <option value="all" x-text="lang === 'zh' ? '所有层级' : 'Semua Level'">Semua Level</option>
                    <option value="root" x-text="lang === 'zh' ? '仅根菜单 (Root / Header)' : 'Root / Header Kategori'">Root / Header Kategori</option>
                    <option value="sub" x-text="lang === 'zh' ? '仅子菜单' : 'Sub Menu'">Sub Menu</option>
                </select>

                <!-- Filter ERP Platform -->
                <select x-model="filterPlatform" class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold text-slate-700 outline-none focus:border-yellow-400">
                    <option value="all" x-text="lang === 'zh' ? '所有平台' : 'Semua Platform'">Semua Platform</option>
                    <option value="erp_y">ERP: Y</option>
                    <option value="erp_n">ERP: N</option>
                </select>
            </div>
        </div>

        <!-- Menu Table -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-black text-slate-900" x-text="lang === 'zh' ? '系统菜单列表' : 'Daftar Menu Sistem (appmenu)'">
                        Daftar Menu Sistem (appmenu)
                    </h3>
                    <p class="text-xs text-slate-500" x-text="lang === 'zh' ? '支持添加、编辑与删除菜单项。包含 URL 路由、排列次序与平台可见性。' : 'Dapat menambah, mengedit, dan menghapus menu. Mengatur URL, urutan, dan platform.'">
                        Dapat menambah, mengedit, dan menghapus menu. Mengatur URL, urutan, dan platform.
                    </p>
                </div>
                <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-800 text-xs font-bold font-mono" x-text="`${filteredMenus.length} items`">
                    {{ $menus->count() }} items
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-600 text-[11px] uppercase tracking-wider font-bold border-b border-slate-200">
                        <tr>
                            <th class="py-3.5 px-4 w-16 text-center">ID</th>
                            <th class="py-3.5 px-4" x-text="lang === 'zh' ? '上级菜单' : 'Parent Menu'">Parent Menu</th>
                            <th class="py-3.5 px-6" x-text="lang === 'zh' ? '菜单名称' : 'Nama Menu'">Nama Menu</th>
                            <th class="py-3.5 px-6" x-text="lang === 'zh' ? 'URL / 页面路径' : 'URL / Halaman'">URL / Halaman</th>
                            <th class="py-3.5 px-4 text-center" x-text="lang === 'zh' ? '排序' : 'Urutan'">Urutan</th>
                            <th class="py-3.5 px-4" x-text="lang === 'zh' ? '图标代码' : 'Icon'">Icon</th>
                            <th class="py-3.5 px-3 text-center">ERP</th>
                            <th class="py-3.5 px-3 text-center">Mobile</th>
                            <th class="py-3.5 px-4 text-center" x-text="lang === 'zh' ? '角色授权数' : 'Hak Akses'">Hak Akses</th>
                            <th class="py-3.5 px-4 text-center w-28" x-text="lang === 'zh' ? '操作' : 'Aksi'">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-800 font-medium">
                        <template x-for="menu in filteredMenus" :key="menu.menu_id">
                            <tr class="hover:bg-amber-50/30 transition" :class="menu.menu_parent == 0 ? 'bg-slate-50/50 font-bold' : ''">
                                <!-- ID -->
                                <td class="py-3.5 px-4 text-center font-mono text-slate-400 font-bold" x-text="menu.menu_id"></td>

                                <!-- Parent -->
                                <td class="py-3.5 px-4">
                                    <template x-if="menu.menu_parent == 0">
                                        <span class="px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 text-[10px] font-black border border-blue-200">
                                            0 • ROOT
                                        </span>
                                    </template>
                                    <template x-if="menu.menu_parent > 0">
                                        <span class="text-slate-500 font-medium text-[11px]" x-text="getParentName(menu.menu_parent)"></span>
                                    </template>
                                </td>

                                <!-- Nama Menu -->
                                <td class="py-3.5 px-6">
                                    <div class="flex items-center space-x-2" :class="menu.menu_parent > 0 ? 'pl-6' : ''">
                                        <template x-if="menu.menu_parent > 0">
                                            <span class="text-slate-300 font-bold">↳</span>
                                        </template>
                                        <span class="font-extrabold text-slate-900" x-text="menu.menu_name"></span>
                                    </div>
                                </td>

                                <!-- URL / Page -->
                                <td class="py-3.5 px-6 font-mono text-[11px]">
                                    <template x-if="menu.menu_page">
                                        <a :href="menu.menu_page" class="text-blue-600 hover:underline font-semibold" x-text="menu.menu_page"></a>
                                    </template>
                                    <template x-if="!menu.menu_page">
                                        <span class="text-slate-300 italic">[Header / None]</span>
                                    </template>
                                </td>

                                <!-- Urutan -->
                                <td class="py-3.5 px-4 text-center font-bold text-slate-700" x-text="menu.menu_order"></td>

                                <!-- Icon -->
                                <td class="py-3.5 px-4 font-mono text-slate-500 text-[11px]" x-text="menu.menu_icon || '-'"></td>

                                <!-- ERP Platform -->
                                <td class="py-3.5 px-3 text-center">
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-black"
                                          :class="menu.erp === 'Y' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-400'"
                                          x-text="menu.erp">
                                    </span>
                                </td>

                                <!-- Mobile Platform -->
                                <td class="py-3.5 px-3 text-center">
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-black"
                                          :class="menu.mobile === 'Y' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-400'"
                                          x-text="menu.mobile">
                                    </span>
                                </td>

                                <!-- Hak Akses Count -->
                                <td class="py-3.5 px-4 text-center">
                                    <span class="px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-800 font-bold text-[10px] border border-amber-200"
                                          x-text="`${(menu.accesses || []).length} role`">
                                    </span>
                                </td>

                                <!-- Aksi (Icon Only) -->
                                <td class="py-3.5 px-4 text-center">
                                    <div class="flex items-center justify-center space-x-1.5">
                                        <!-- Edit Button (Icon Only) -->
                                        <button type="button" 
                                                @click="openEditMenuModal(menu)"
                                                title="Edit Menu"
                                                class="w-7 h-7 flex items-center justify-center rounded-lg bg-slate-100 hover:bg-yellow-400 hover:text-slate-950 text-slate-600 transition active:scale-95 shadow-xs">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        </button>

                                        <!-- Delete Button (Icon Only) -->
                                        <button type="button" 
                                                @click="deleteMenu(menu)"
                                                title="Hapus Menu"
                                                class="w-7 h-7 flex items-center justify-center rounded-lg bg-rose-50 hover:bg-rose-600 hover:text-white text-rose-600 transition active:scale-95 shadow-xs">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>

                        <template x-if="filteredMenus.length === 0">
                            <tr>
                                <td colspan="10" class="py-12 text-center text-slate-400">
                                    <p class="font-bold text-sm" x-text="lang === 'zh' ? '未找到相关菜单项' : 'Tidak ada menu yang sesuai dengan filter pencarian.'">
                                        Tidak ada menu yang sesuai dengan filter pencarian.
                                    </p>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- 5. TAB 2: HAK AKSES PER ROLE (CRUD & MATRIX AKSES)                       -->
    <!-- ========================================================================= -->
    <div x-show="activeTab === 'access'" class="space-y-6">

        <!-- Role Selector Card -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <label class="block text-xs font-black uppercase tracking-wider text-slate-500 mb-2" x-text="lang === 'zh' ? '选择配置权限的目标角色' : 'Pilih Role Pengguna Yang Akan Diatur'">
                        Pilih Role Pengguna Yang Akan Diatur
                    </label>
                    <div class="flex flex-wrap items-center gap-2">
                        <template x-for="r in roles" :key="r.id">
                            <button type="button" 
                                    @click="selectRole(r.id)"
                                    :class="selectedRoleId === r.id ? 'bg-amber-400 text-slate-950 font-black shadow-md border-amber-400' : 'bg-slate-100 text-slate-700 hover:bg-slate-200 border-transparent font-bold'"
                                    class="px-4 py-2 rounded-xl text-xs transition border flex items-center space-x-2">
                                <span x-text="r.label"></span>
                                <template x-if="r.is_system">
                                    <span class="px-1.5 py-0.2 rounded text-[9px] bg-slate-900 text-white font-extrabold">CORE</span>
                                </template>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Quick Check/Uncheck Tools -->
                <div class="flex items-center space-x-2 pt-2 md:pt-0">
                    <button type="button" 
                            @click="checkAllAccess()" 
                            class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                        <span x-text="lang === 'zh' ? '全选' : 'Centang Semua'">Centang Semua</span>
                    </button>
                    <button type="button" 
                            @click="uncheckAllAccess()" 
                            class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                        <span x-text="lang === 'zh' ? '全不选' : 'Hapus Semua Centang'">Hapus Semua Centang</span>
                    </button>
                    <button type="button" 
                            @click="saveAccessSync()" 
                            :disabled="isSavingAccess"
                            class="px-4 py-2 rounded-xl bg-amber-400 hover:bg-yellow-300 text-slate-950 text-xs font-black transition shadow-xs disabled:opacity-50">
                        <span x-text="isSavingAccess ? '...' : (lang === 'zh' ? '保存更改' : 'Simpan Perubahan')">Simpan Perubahan</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Permission Matrix List Grouped by Root -->
        <div class="space-y-4">
            <template x-for="root in rootMenus" :key="root.menu_id">
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
                    <!-- Root Header -->
                    <div class="p-4 bg-slate-50/80 border-b border-slate-200/80 flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" 
                                       :checked="isMenuChecked(root.menu_id)" 
                                       @change="toggleMenuAccess(root.menu_id)"
                                       class="w-4 h-4 text-amber-500 rounded border-slate-300 focus:ring-amber-400 cursor-pointer">
                                <span class="font-black text-sm text-slate-900 uppercase tracking-wide" x-text="root.menu_name"></span>
                            </label>
                            <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-200/70 text-slate-700" x-text="`ID: ${root.menu_id}`"></span>
                        </div>

                        <!-- Action for Root Access -->
                        <template x-if="isMenuChecked(root.menu_id)">
                            <button type="button" 
                                    @click="deleteAccessRecord(selectedRoleId, root.menu_id)"
                                    title="Hapus Hak Akses Root"
                                    class="w-6 h-6 flex items-center justify-center rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white transition text-xs">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </template>
                    </div>

                    <!-- Submenu Items -->
                    <div class="divide-y divide-slate-100">
                        <template x-for="sub in getSubMenus(root.menu_id)" :key="sub.menu_id">
                            <div class="p-3.5 px-6 flex items-center justify-between hover:bg-amber-50/20 transition">
                                <div class="flex items-center space-x-3">
                                    <input type="checkbox" 
                                           :checked="isMenuChecked(sub.menu_id)" 
                                           @change="toggleMenuAccess(sub.menu_id)"
                                           class="w-4 h-4 text-amber-500 rounded border-slate-300 focus:ring-amber-400 cursor-pointer">
                                    
                                    <div>
                                        <div class="flex items-center space-x-2">
                                            <span class="font-extrabold text-xs text-slate-900" x-text="sub.menu_name"></span>
                                            <span class="text-[10px] font-mono text-slate-400" x-text="`(${sub.menu_page || 'No Route'})`"></span>
                                        </div>
                                        <span class="text-[10px] text-slate-400 font-mono" x-text="`Icon: ${sub.menu_icon || 'none'} • Order: ${sub.menu_order}`"></span>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-3">
                                    <template x-if="sub.erp === 'Y'">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            ERP
                                        </span>
                                    </template>
                                    <template x-if="sub.mobile === 'Y'">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-blue-50 text-blue-700 border border-blue-200">
                                            Mobile
                                        </span>
                                    </template>

                                    <!-- Delete / Revoke Access (Icon Only) -->
                                    <template x-if="isMenuChecked(sub.menu_id)">
                                        <button type="button" 
                                                @click="deleteAccessRecord(selectedRoleId, sub.menu_id)"
                                                title="Cabut / Hapus Hak Akses"
                                                class="w-7 h-7 flex items-center justify-center rounded-lg bg-rose-50 hover:bg-rose-600 hover:text-white text-rose-600 transition active:scale-95 shadow-xs">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <template x-if="getSubMenus(root.menu_id).length === 0">
                            <div class="p-4 px-6 text-xs text-slate-400 italic">
                                <span x-text="lang === 'zh' ? '该分组下无子菜单' : 'Tidak ada sub-menu di bawah kategori ini.'"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </div>

        <!-- Raw Access Audit Table -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden mt-8">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-black text-slate-900" x-text="lang === 'zh' ? '全部已配置权限条目 (appmenuaccess 数据表)' : 'Daftar Seluruh Konfigurasi Hak Akses (appmenuaccess)'">
                        Daftar Seluruh Konfigurasi Hak Akses (appmenuaccess)
                    </h3>
                    <p class="text-xs text-slate-500" x-text="lang === 'zh' ? '可以在此一览所有角色分配到的菜单权限，并支持直接删除。' : 'Rekapitulasi seluruh entri hak akses grup pengguna. Dapat dihapus secara individual.'">
                        Rekapitulasi seluruh entri hak akses grup pengguna. Dapat dihapus secara individual.
                    </p>
                </div>
                <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-800 text-xs font-bold font-mono">
                    {{ $allAccesses->count() }} records
                </span>
            </div>

            <div class="overflow-x-auto max-h-96">
                <table class="w-full text-left text-xs font-mono">
                    <thead class="bg-slate-50 text-slate-600 text-[11px] uppercase tracking-wider font-bold border-b border-slate-200 sticky top-0 bg-slate-50 z-10">
                        <tr>
                            <th class="py-3 px-4">usergrpid</th>
                            <th class="py-3 px-6" x-text="lang === 'zh' ? '角色名称' : 'Role Pengguna'">Role Pengguna</th>
                            <th class="py-3 px-4">menu_id</th>
                            <th class="py-3 px-6" x-text="lang === 'zh' ? '目标菜单' : 'Nama Menu'">Nama Menu</th>
                            <th class="py-3 px-6" x-text="lang === 'zh' ? '页面 URL' : 'URL Halaman'">URL Halaman</th>
                            <th class="py-3 px-4 text-center" x-text="lang === 'zh' ? '操作' : 'Aksi'">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-800 font-medium">
                        @foreach($allAccesses as $acc)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-2.5 px-4 font-bold text-amber-700">{{ $acc->usergrpid }}</td>
                                <td class="py-2.5 px-6 font-sans font-extrabold text-slate-900">
                                    {{ $acc->role->label ?? 'Role #' . $acc->usergrpid }}
                                </td>
                                <td class="py-2.5 px-4 font-bold text-slate-900">{{ $acc->menu_id }}</td>
                                <td class="py-2.5 px-6 font-sans font-semibold text-slate-800">
                                    {{ $acc->menu->menu_name ?? '[Menu #' . $acc->menu_id . ']' }}
                                </td>
                                <td class="py-2.5 px-6 text-blue-600 text-[11px]">
                                    {{ $acc->menu->menu_page ?? '-' }}
                                </td>
                                <td class="py-2.5 px-4 text-center">
                                    <!-- Delete Access (Icon Only) -->
                                    <button type="button" 
                                            @click="deleteAccessRecord({{ $acc->usergrpid }}, {{ $acc->menu_id }})"
                                            title="Hapus Hak Akses Ini"
                                            class="w-7 h-7 inline-flex items-center justify-center rounded-lg bg-rose-50 hover:bg-rose-600 hover:text-white text-rose-600 transition active:scale-95 shadow-xs">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- 6. MODAL TAMBAH & EDIT MENU                                               -->
    <!-- ========================================================================= -->
    <div x-show="menuModalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        
        <div @click.away="menuModalOpen = false" 
             class="bg-white w-full max-w-xl rounded-3xl shadow-2xl border border-slate-200 overflow-hidden animate-in fade-in zoom-in-95 duration-150">
            
            <!-- Modal Header -->
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center font-black">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-900" 
                            x-text="menuModalMode === 'create' ? (lang === 'zh' ? '添加新系统菜单' : 'Tambah Menu Sistem Baru') : (lang === 'zh' ? '编辑系统菜单' : 'Edit Menu Sistem')">
                        </h3>
                        <p class="text-[11px] text-slate-500" x-text="lang === 'zh' ? '配置层级关系、页面 URL 与平台显示' : 'Konfigurasi hirarki, URL halaman, dan platform'">
                        </p>
                    </div>
                </div>
                <button type="button" @click="menuModalOpen = false" class="text-slate-400 hover:text-slate-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Modal Body Form -->
            <form @submit.prevent="submitMenuForm" class="p-6 space-y-4 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '菜单名称 (menu_name) *' : 'Nama Menu (menu_name) *'">Nama Menu *</label>
                    <input type="text" 
                           x-model="menuForm.menu_name" 
                           required 
                           placeholder="Contoh: Laporan Penjualan" 
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-900 font-semibold outline-none focus:border-yellow-400 focus:bg-white transition">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '上级菜单 (menu_parent)' : 'Parent Menu (menu_parent)'">Parent Menu</label>
                        <select x-model="menuForm.menu_parent" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-900 font-semibold outline-none focus:border-yellow-400 focus:bg-white transition">
                            <option value="0">0 - Root / Header Kategori</option>
                            <template x-for="r in rootMenus" :key="r.menu_id">
                                <option :value="r.menu_id" x-text="`${r.menu_id} - ${r.menu_name}`"></option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '排序权重 (menu_order) *' : 'Urutan (menu_order) *'">Urutan *</label>
                        <input type="number" 
                               x-model="menuForm.menu_order" 
                               required 
                               class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-900 font-semibold outline-none focus:border-yellow-400 focus:bg-white transition">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? 'URL 路由 (menu_page)' : 'URL / Halaman (menu_page)'">URL / Halaman</label>
                        <input type="text" 
                               x-model="menuForm.menu_page" 
                               placeholder="Contoh: /erp/finance" 
                               class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-900 font-semibold outline-none focus:border-yellow-400 focus:bg-white transition">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '图标样式 (menu_icon)' : 'Icon Class (menu_icon)'">Icon Class</label>
                        <input type="text" 
                               x-model="menuForm.menu_icon" 
                               placeholder="pe-7s-link, pe-7s-graph, etc" 
                               class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-900 font-semibold outline-none focus:border-yellow-400 focus:bg-white transition">
                    </div>
                </div>

                <!-- Platform switches -->
                <div class="grid grid-cols-3 gap-3 pt-2">
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                        <label class="block font-bold text-slate-700 mb-1">Tampil ERP</label>
                        <select x-model="menuForm.erp" class="w-full bg-white border border-slate-200 rounded-lg px-2 py-1.5 text-xs font-bold text-slate-900 outline-none">
                            <option value="Y">Y (Ya)</option>
                            <option value="N">N (Tidak)</option>
                        </select>
                    </div>

                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                        <label class="block font-bold text-slate-700 mb-1">Project</label>
                        <select x-model="menuForm.project" class="w-full bg-white border border-slate-200 rounded-lg px-2 py-1.5 text-xs font-bold text-slate-900 outline-none">
                            <option value="N">N (Tidak)</option>
                            <option value="Y">Y (Ya)</option>
                        </select>
                    </div>

                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                        <label class="block font-bold text-slate-700 mb-1">Mobile</label>
                        <select x-model="menuForm.mobile" class="w-full bg-white border border-slate-200 rounded-lg px-2 py-1.5 text-xs font-bold text-slate-900 outline-none">
                            <option value="N">N (Tidak)</option>
                            <option value="Y">Y (Ya)</option>
                        </select>
                    </div>
                </div>

                <!-- Initial role access assignment (for create mode only) -->
                <template x-if="menuModalMode === 'create'">
                    <div class="pt-2">
                        <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '分配初始访问权限至角色' : 'Berikan Akses Awal ke Role'">Berikan Akses Awal ke Role</label>
                        <select x-model="menuForm.usergrpid" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-900 font-semibold outline-none focus:border-yellow-400 focus:bg-white transition">
                            <option value="1">Semua Administrator (usergrpid: 1)</option>
                            <template x-for="r in roles" :key="r.id">
                                <option :value="r.id" x-text="`${r.label} (ID: ${r.id})`"></option>
                            </template>
                        </select>
                    </div>
                </template>

                <div class="pt-4 flex items-center justify-end space-x-3 border-t border-slate-100">
                    <button type="button" @click="menuModalOpen = false" class="px-4 py-2 text-slate-600 font-bold hover:bg-slate-100 rounded-xl transition">
                        Batal
                    </button>
                    <button type="submit" 
                            :disabled="isSubmittingMenu"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-extrabold px-6 py-2 rounded-xl shadow-md transition disabled:opacity-50">
                        <span x-text="isSubmittingMenu ? 'Menyimpan...' : (menuModalMode === 'create' ? 'Tambah Menu' : 'Simpan Perubahan')"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
    function erpMenuManager(initialTab, initialRoleId, initialMenus, initialRootMenus, initialRoles, initialRoleMenuIds, initialAllAccesses) {
        return {
            activeTab: initialTab || 'menu',
            selectedRoleId: initialRoleId || 1,
            menus: initialMenus || [],
            rootMenus: initialRootMenus || [],
            roles: initialRoles || [],
            currentRoleMenuIds: initialRoleMenuIds || [],
            allAccesses: initialAllAccesses || [],

            searchMenu: '',
            filterParent: 'all',
            filterPlatform: 'all',

            // Modal state
            menuModalOpen: false,
            menuModalMode: 'create',
            isSubmittingMenu: false,
            isSavingAccess: false,

            menuForm: {
                id: null,
                menu_name: '',
                menu_parent: 0,
                menu_page: '',
                menu_order: 1,
                menu_icon: 'pe-7s-link',
                erp: 'Y',
                project: 'N',
                mobile: 'N',
                usergrpid: 1
            },

            switchTab(tab) {
                this.activeTab = tab;
                const url = new URL(window.location);
                url.searchParams.set('tab', tab);
                window.history.replaceState({}, '', url);
            },

            selectRole(roleId) {
                this.selectedRoleId = roleId;
                const url = new URL(window.location);
                url.searchParams.set('tab', 'access');
                url.searchParams.set('role_id', roleId);
                window.location.href = url.toString();
            },

            getSelectedRoleLabel() {
                const found = this.roles.find(r => r.id === this.selectedRoleId);
                return found ? found.label : `Role #${this.selectedRoleId}`;
            },

            getParentName(parentId) {
                const found = this.menus.find(m => m.menu_id == parentId);
                return found ? found.menu_name : `ID: ${parentId}`;
            },

            getSubMenus(rootId) {
                return this.menus.filter(m => m.menu_parent == rootId);
            },

            isMenuChecked(menuId) {
                return this.currentRoleMenuIds.includes(Number(menuId));
            },

            checkAllAccess() {
                this.currentRoleMenuIds = this.menus.map(m => Number(m.menu_id));
            },

            uncheckAllAccess() {
                this.currentRoleMenuIds = [];
            },

            get filteredMenus() {
                return this.menus.filter(m => {
                    // Search
                    if (this.searchMenu) {
                        const q = this.searchMenu.toLowerCase();
                        const matchName = m.menu_name && m.menu_name.toLowerCase().includes(q);
                        const matchPage = m.menu_page && m.menu_page.toLowerCase().includes(q);
                        if (!matchName && !matchPage) return false;
                    }
                    // Filter Parent
                    if (this.filterParent === 'root' && m.menu_parent != 0) return false;
                    if (this.filterParent === 'sub' && m.menu_parent == 0) return false;
                    // Filter Platform
                    if (this.filterPlatform === 'erp_y' && m.erp !== 'Y') return false;
                    if (this.filterPlatform === 'erp_n' && m.erp === 'Y') return false;

                    return true;
                });
            },

            openCreateMenuModal() {
                this.menuModalMode = 'create';
                this.menuForm = {
                    id: null,
                    menu_name: '',
                    menu_parent: 0,
                    menu_page: '',
                    menu_order: this.menus.length + 1,
                    menu_icon: 'pe-7s-link',
                    erp: 'Y',
                    project: 'N',
                    mobile: 'N',
                    usergrpid: this.selectedRoleId || 1
                };
                this.menuModalOpen = true;
            },

            openEditMenuModal(menu) {
                this.menuModalMode = 'edit';
                this.menuForm = {
                    id: menu.menu_id,
                    menu_name: menu.menu_name,
                    menu_parent: menu.menu_parent,
                    menu_page: menu.menu_page || '',
                    menu_order: menu.menu_order,
                    menu_icon: menu.menu_icon || 'pe-7s-link',
                    erp: menu.erp,
                    project: menu.project,
                    mobile: menu.mobile,
                    usergrpid: 1
                };
                this.menuModalOpen = true;
            },

            async submitMenuForm() {
                this.isSubmittingMenu = true;
                try {
                    const url = this.menuModalMode === 'edit'
                        ? `/erp/system-menus/${this.menuForm.id}/update`
                        : '{{ route("erp.menus.store") }}';

                    const res = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(this.menuForm)
                    });
                    const result = await res.json();
                    if (result.success) {
                        alert(result.message);
                        window.location.reload();
                    } else {
                        alert('Gagal menyimpan menu: ' + (result.message || 'Periksa kembali data yang dimasukkan.'));
                    }
                } catch(e) {
                    alert('Terjadi kesalahan pada server.');
                } finally {
                    this.isSubmittingMenu = false;
                }
            },

            async deleteMenu(menu) {
                if (!confirm(`Apakah Anda yakin ingin menghapus menu '${menu.menu_name}'?`)) {
                    return;
                }

                try {
                    const res = await fetch(`/erp/system-menus/${menu.menu_id}/delete`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                    const result = await res.json();
                    if (result.success) {
                        alert(result.message);
                        window.location.reload();
                    } else {
                        alert(result.message || 'Gagal menghapus menu.');
                    }
                } catch(e) {
                    alert('Terjadi kesalahan pada server saat menghapus menu.');
                }
            },

            async toggleMenuAccess(menuId) {
                menuId = Number(menuId);
                const hasAccess = this.currentRoleMenuIds.includes(menuId);
                if (hasAccess) {
                    this.currentRoleMenuIds = this.currentRoleMenuIds.filter(id => id !== menuId);
                } else {
                    this.currentRoleMenuIds.push(menuId);
                }

                try {
                    const res = await fetch('{{ route("erp.menus.access.toggle") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            usergrpid: this.selectedRoleId,
                            menu_id: menuId
                        })
                    });
                    const result = await res.json();
                    if (!result.success) {
                        alert(result.message || 'Gagal mengubah status hak akses.');
                    }
                } catch(e) {
                    console.error('Error toggling access', e);
                }
            },

            async deleteAccessRecord(usergrpid, menuId) {
                if (!confirm('Apakah Anda yakin ingin menghapus / mencabut hak akses ini?')) {
                    return;
                }

                try {
                    const res = await fetch('{{ route("erp.menus.access.delete") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            usergrpid: usergrpid,
                            menu_id: menuId
                        })
                    });
                    const result = await res.json();
                    if (result.success) {
                        alert(result.message);
                        window.location.reload();
                    } else {
                        alert(result.message || 'Gagal menghapus hak akses.');
                    }
                } catch(e) {
                    alert('Terjadi kesalahan pada server.');
                }
            },

            async saveAccessSync() {
                this.isSavingAccess = true;
                try {
                    const res = await fetch('{{ route("erp.menus.access.sync") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            usergrpid: this.selectedRoleId,
                            menu_ids: this.currentRoleMenuIds
                        })
                    });
                    const result = await res.json();
                    if (result.success) {
                        alert(result.message);
                        window.location.reload();
                    } else {
                        alert(result.message || 'Gagal menyimpan hak akses.');
                    }
                } catch(e) {
                    alert('Terjadi kesalahan pada server.');
                } finally {
                    this.isSavingAccess = false;
                }
            }
        };
    }
</script>
@endpush
@endsection
