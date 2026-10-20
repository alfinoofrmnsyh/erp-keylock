@extends('layouts.erp')

@section('title', 'User & Role Management - Keylock Indonesia')

@section('breadcrumbs')
    <a href="{{ route('erp.index') }}" class="hover:text-slate-800">Home</a>
    <span>›</span>
    <span class="text-slate-800 font-semibold">General System</span>
    <span>›</span>
    <span class="text-slate-800 font-semibold" x-text="lang === 'zh' ? '用户与角色权限管理' : 'User & Role Management'">User Management</span>
@endsection

@section('content')
<div x-data="erpUserManager('{{ $tab }}', @js($users), @js($roles))">

    <!-- ========================================================================= -->
    <!-- 1. PAGE HEADER                                                            -->
    <!-- ========================================================================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-black text-slate-900 tracking-tight" x-text="lang === 'zh' ? '用户与角色管理中心' : 'User & Role Management'">
                User & Role Management
            </h2>
            <p class="text-xs text-slate-500 mt-1" x-text="lang === 'zh' ? '集中管理系统操作员账号、登录密码、角色权限及账号启停状态。' : 'Pusat pengelolaan akun operator sistem, password, hak akses role, dan status aktifasi user.'">
                Pusat pengelolaan akun operator sistem, password, hak akses role, dan status aktifasi user.
            </p>
        </div>

        <!-- Top Right Action Button -->
        <button type="button" 
                @click="openCreateModal()" 
                class="bg-blue-600 hover:bg-blue-700 text-white font-extrabold px-5 py-2.5 rounded-full shadow-md hover:shadow-lg text-xs transition flex items-center space-x-2 self-start md:self-auto active:scale-95">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
            <span x-text="getCreateButtonText()">Tambah Data</span>
        </button>
    </div>

    <!-- ========================================================================= -->
    <!-- 2. PILL TABS BAR: TAB USER & TAB ROLE                                     -->
    <!-- ========================================================================= -->
    <div class="flex flex-wrap items-center gap-3 mb-6">
        <!-- Tab 1: User -->
        <button type="button" 
                @click="switchTab('user')"
                :class="activeTab === 'user' ? 'bg-white text-slate-900 shadow-sm border border-slate-200/90 font-black' : 'bg-slate-100/80 text-slate-600 hover:bg-white hover:text-slate-900 border border-transparent font-bold'"
                class="px-4 py-2 rounded-2xl text-xs transition flex items-center space-x-2.5">
            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
            <span x-text="lang === 'zh' ? '用户账号 (Users)' : 'User Akun'">User Akun</span>
            <span class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 text-[10px] font-black">
                {{ $stats['total_users'] }}
            </span>
        </button>

        <!-- Tab 2: Role -->
        <button type="button" 
                @click="switchTab('role')"
                :class="activeTab === 'role' ? 'bg-white text-slate-900 shadow-sm border border-slate-200/90 font-black' : 'bg-slate-100/80 text-slate-600 hover:bg-white hover:text-slate-900 border border-transparent font-bold'"
                class="px-4 py-2 rounded-2xl text-xs transition flex items-center space-x-2.5">
            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
            </svg>
            <span x-text="lang === 'zh' ? '角色与分组 (Roles)' : 'Role & Hak Akses'">Role & Hak Akses</span>
            <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-black">
                {{ $stats['total_roles'] }}
            </span>
        </button>
    </div>

    <!-- ========================================================================= -->
    <!-- 3. STATS OVERVIEW CARDS                                                   -->
    <!-- ========================================================================= -->
    <!-- User Stats -->
    <div x-show="activeTab === 'user'" class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-[11px] font-bold text-slate-400 block mb-1" x-text="lang === 'zh' ? '用户总数' : 'Total User Terdaftar'">Total User Terdaftar</span>
            <p class="text-2xl font-black text-slate-900">{{ $stats['total_users'] }}</p>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-[11px] font-bold text-slate-400 block mb-1" x-text="lang === 'zh' ? '正常活跃用户' : 'User Aktif'">User Aktif</span>
            <p class="text-2xl font-black text-emerald-600">{{ $stats['active_users'] }}</p>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-[11px] font-bold text-slate-400 block mb-1" x-text="lang === 'zh' ? '已停用用户' : 'User Nonaktif'">User Nonaktif</span>
            <p class="text-2xl font-black text-slate-400">{{ $stats['inactive_users'] }}</p>
        </div>
    </div>

    <!-- Role Stats -->
    <div x-show="activeTab === 'role'" class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-[11px] font-bold text-slate-400 block mb-1" x-text="lang === 'zh' ? '角色总数' : 'Total Role Sistem'">Total Role Sistem</span>
            <p class="text-2xl font-black text-slate-900">{{ $stats['total_roles'] }}</p>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-[11px] font-bold text-slate-400 block mb-1" x-text="lang === 'zh' ? '系统核心角色 (不可删除)' : 'Role Inti Sistem (Protected)'">Role Inti Sistem</span>
            <p class="text-2xl font-black text-blue-600">{{ $stats['system_roles'] }}</p>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-[11px] font-bold text-slate-400 block mb-1" x-text="lang === 'zh' ? '自定义角色 (可增删改)' : 'Role Kustom (Dapat Dihapus)'">Role Kustom</span>
            <p class="text-2xl font-black text-emerald-600">{{ $stats['custom_roles'] }}</p>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 4. CONTROLS BAR: SEARCH & ROLE FILTER                                     -->
    <!-- ========================================================================= -->
    <div class="bg-white rounded-3xl p-3 border border-slate-200/80 shadow-xs mb-6 flex flex-col md:flex-row items-center justify-between gap-3">
        <!-- Search Input -->
        <div class="relative w-full md:w-96">
            <svg class="w-4 h-4 text-slate-400 absolute left-4 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input type="text" 
                   x-model="searchQuery" 
                   :placeholder="activeTab === 'user' ? (lang === 'zh' ? '搜索用户姓名、邮箱、角色...' : 'Cari nama user, email, role...') : (lang === 'zh' ? '搜索角色代码、名称、描述...' : 'Cari kode role, label, deskripsi...')"
                   class="w-full bg-transparent pl-11 pr-4 py-2 text-xs text-slate-900 placeholder:text-slate-400 focus:outline-none">
        </div>

        <!-- Filter Controls -->
        <div class="flex items-center space-x-2.5 self-end md:self-auto w-full md:w-auto justify-end">
            <!-- Filter by Role in User Tab -->
            <template x-if="activeTab === 'user'">
                <select x-model="roleFilter" class="bg-slate-50 border border-slate-200 rounded-2xl px-4 py-2 text-xs font-semibold text-slate-700 outline-none focus:border-blue-400">
                    <option value="all" x-text="lang === 'zh' ? '所有角色分组' : 'Semua Role'">Semua Role</option>
                    <template x-for="r in rolesList" :key="r.id">
                        <option :value="r.id" x-text="r.label"></option>
                    </template>
                </select>
            </template>

            <!-- Reset Button -->
            <button type="button" @click="resetFilters()" class="w-9 h-9 rounded-2xl bg-slate-50 hover:bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-500 hover:text-slate-800 transition" title="Reset Filter">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            </button>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 5. DATA TABLES                                                            -->
    <!-- ========================================================================= -->

    <!-- ========================== TAB 1: USERS ========================== -->
    <div x-show="activeTab === 'user'" class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs whitespace-nowrap">
                <thead>
                    <tr class="border-b border-slate-100 text-[11px] font-black uppercase tracking-wider text-slate-400 bg-slate-50/50">
                        <th class="py-4 px-4 w-12 text-center" x-text="lang === 'zh' ? '序号' : 'SL'">SL</th>
                        <th class="py-4 px-4" x-text="lang === 'zh' ? '用户姓名' : 'Nama Lengkap'">Nama Lengkap</th>
                        <th class="py-4 px-4" x-text="lang === 'zh' ? '登录邮箱' : 'Email (Username)'">Email (Username)</th>
                        <th class="py-4 px-4" x-text="lang === 'zh' ? '所属角色' : 'Role / Hak Akses'">Role / Hak Akses</th>
                        <th class="py-4 px-4" x-text="lang === 'zh' ? '创建时间' : 'Terdaftar'">Terdaftar</th>
                        <th class="py-4 px-4" x-text="lang === 'zh' ? '状态' : 'Status'">Status</th>
                        <th class="py-4 px-6 text-center sticky right-0 bg-slate-50 z-10 shadow-[-6px_0_8px_-4px_rgba(0,0,0,0.06)] border-l border-slate-200" x-text="lang === 'zh' ? '操作' : 'Aksi'">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    <template x-for="(user, idx) in filteredUsers" :key="user.id">
                        <tr class="hover:bg-slate-50/80 transition group">
                            <td class="py-4 px-4 text-center font-bold text-slate-400" x-text="String(idx + 1).padStart(2, '0')"></td>
                            <td class="py-4 px-4">
                                <div class="flex items-center space-x-2.5">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-700 font-extrabold flex items-center justify-center text-xs shrink-0">
                                        <span x-text="user.name ? user.name.charAt(0).toUpperCase() : 'U'"></span>
                                    </div>
                                    <span class="font-extrabold text-slate-900" x-text="user.name"></span>
                                </div>
                            </td>
                            <td class="py-4 px-4 font-mono text-slate-600" x-text="user.email"></td>
                            <td class="py-4 px-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border"
                                      :class="user.role === 'admin' ? 'bg-amber-50 text-amber-800 border-amber-200' : 'bg-blue-50 text-blue-800 border-blue-200'">
                                    <span x-text="user.role_model?.label || user.role || 'User'"></span>
                                </span>
                            </td>
                            <td class="py-4 px-4 text-slate-400 text-[11px]" x-text="formatDate(user.created_at)"></td>
                            <td class="py-4 px-4">
                                <span :class="user.is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-500 border-slate-200'" class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold border">
                                    <span :class="user.is_active ? 'bg-emerald-500' : 'bg-slate-400'" class="w-1.5 h-1.5 rounded-full"></span>
                                    <span x-text="user.is_active ? (lang === 'zh' ? '正常' : 'Aktif') : (lang === 'zh' ? '已停用' : 'Nonaktif')"></span>
                                </span>
                            </td>
                            <!-- Action: ICON ONLY (Edit & Menonaktifkan) -->
                            <td class="py-4 px-6 text-center sticky right-0 bg-white group-hover:bg-slate-50 transition z-10 shadow-[-6px_0_8px_-4px_rgba(0,0,0,0.06)] border-l border-slate-100">
                                <div class="flex items-center justify-center space-x-2">
                                    <!-- Edit Icon Button -->
                                    <button type="button" 
                                            @click="openEditUserModal(user)" 
                                            class="w-8 h-8 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-600 border border-blue-200 flex items-center justify-center transition shadow-xs" 
                                            :title="lang === 'zh' ? '编辑用户' : 'Edit User'">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                        </svg>
                                    </button>

                                    <!-- Toggle Active / Deactivate Icon Button -->
                                    <button type="button" 
                                            @click="toggleUserStatus(user)" 
                                            :class="user.is_active ? 'bg-rose-50 hover:bg-rose-100 text-rose-600 border-rose-200' : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-600 border-emerald-200'"
                                            class="w-8 h-8 rounded-xl border flex items-center justify-center transition shadow-xs" 
                                            :title="user.is_active ? (lang === 'zh' ? '停用账号' : 'Nonaktifkan User') : (lang === 'zh' ? '启用账号' : 'Aktifkan User')">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="filteredUsers.length === 0">
                        <td colspan="7" class="text-center py-10 text-slate-400" x-text="lang === 'zh' ? '暂无用户数据' : 'Belum ada data user.'">
                            Belum ada data user.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ========================== TAB 2: ROLES ========================== -->
    <div x-show="activeTab === 'role'" class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs whitespace-nowrap">
                <thead>
                    <tr class="border-b border-slate-100 text-[11px] font-black uppercase tracking-wider text-slate-400 bg-slate-50/50">
                        <th class="py-4 px-4 w-12 text-center" x-text="lang === 'zh' ? '序号' : 'SL'">SL</th>
                        <th class="py-4 px-4" x-text="lang === 'zh' ? '角色代码 (Slug)' : 'Kode Role'">Kode Role</th>
                        <th class="py-4 px-4" x-text="lang === 'zh' ? '角色名称 (Label)' : 'Nama Role (Label)'">Nama Role (Label)</th>
                        <th class="py-4 px-4" x-text="lang === 'zh' ? '权限描述' : 'Deskripsi Hak Akses'">Deskripsi Hak Akses</th>
                        <th class="py-4 px-4 text-center" x-text="lang === 'zh' ? '关联用户数' : 'Jumlah User'">Jumlah User</th>
                        <th class="py-4 px-4" x-text="lang === 'zh' ? '属性' : 'Tipe Role'">Tipe Role</th>
                        <th class="py-4 px-6 text-center sticky right-0 bg-slate-50 z-10 shadow-[-6px_0_8px_-4px_rgba(0,0,0,0.06)] border-l border-slate-200" x-text="lang === 'zh' ? '操作' : 'Aksi'">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    <template x-for="(role, idx) in filteredRoles" :key="role.id">
                        <tr class="hover:bg-slate-50/80 transition group">
                            <td class="py-4 px-4 text-center font-bold text-slate-400" x-text="String(idx + 1).padStart(2, '0')"></td>
                            <td class="py-4 px-4 font-mono font-bold text-slate-900" x-text="role.name"></td>
                            <td class="py-4 px-4">
                                <span class="font-extrabold text-slate-900" x-text="role.label"></span>
                            </td>
                            <td class="py-4 px-4 text-slate-500 max-w-xs truncate" :title="role.description" x-text="role.description || '-'"></td>
                            <td class="py-4 px-4 text-center font-bold font-mono">
                                <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-800 text-[11px]" x-text="role.users_count || 0"></span>
                            </td>
                            <td class="py-4 px-4">
                                <template x-if="role.is_system">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200" x-text="lang === 'zh' ? '系统核心' : 'System Role'"></span>
                                </template>
                                <template x-if="!role.is_system">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200" x-text="lang === 'zh' ? '自定义' : 'Custom'"></span>
                                </template>
                            </td>
                            <!-- Action: ICON ONLY (Edit & Delete for role) -->
                            <td class="py-4 px-6 text-center sticky right-0 bg-white group-hover:bg-slate-50 transition z-10 shadow-[-6px_0_8px_-4px_rgba(0,0,0,0.06)] border-l border-slate-100">
                                <div class="flex items-center justify-center space-x-2">
                                    <!-- Edit Icon Button -->
                                    <button type="button" 
                                            @click="openEditRoleModal(role)" 
                                            class="w-8 h-8 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-600 border border-blue-200 flex items-center justify-center transition shadow-xs" 
                                            :title="lang === 'zh' ? '编辑角色' : 'Edit Role'">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                        </svg>
                                    </button>

                                    <!-- Delete Icon Button (Available for non-system roles) -->
                                    <template x-if="!role.is_system">
                                        <button type="button" 
                                                @click="deleteRole(role)" 
                                                class="w-8 h-8 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 flex items-center justify-center transition shadow-xs" 
                                                :title="lang === 'zh' ? '删除角色' : 'Hapus Role'">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </template>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="filteredRoles.length === 0">
                        <td colspan="7" class="text-center py-10 text-slate-400" x-text="lang === 'zh' ? '暂无角色数据' : 'Belum ada data role.'">
                            Belum ada data role.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 6. MODAL USER (CREATE / EDIT)                                             -->
    <!-- ========================================================================= -->
    <div x-show="userModalOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 drawer-overlay"
         @click.self="userModalOpen = false" 
         style="display: none;">
        
        <div class="w-full max-w-lg bg-white rounded-3xl border border-slate-200 shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
            <div class="px-6 py-5 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-black text-slate-900" x-text="userModalMode === 'edit' ? (lang === 'zh' ? '编辑用户账号' : 'Edit Data User') : (lang === 'zh' ? '新增系统用户' : 'Tambah User Baru')"></h3>
                    <p class="text-xs text-slate-500 mt-0.5" x-text="lang === 'zh' ? '填写用户信息及分配所属角色权限。' : 'Lengkapi informasi user akun dan tetapkan role hak aksesnya.'"></p>
                </div>
                <button @click="userModalOpen = false" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-200/60 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form @submit.prevent="submitUserForm()" class="p-6 overflow-y-auto space-y-4 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '用户姓名 *' : 'Nama Lengkap *'"></label>
                    <input type="text" x-model="userForm.name" required placeholder="Contoh: Budi Santoso" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-slate-900 font-semibold focus:border-blue-500 focus:bg-white outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '登录邮箱 (Username) *' : 'Alamat Email (Username Login) *'"></label>
                    <input type="email" x-model="userForm.email" required placeholder="user@keylock.co.id" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-slate-900 font-semibold focus:border-blue-500 focus:bg-white outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '分配角色权限 *' : 'Pilih Role & Hak Akses *'"></label>
                    <select x-model.number="userForm.usergrpid" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-slate-900 font-semibold focus:border-blue-500 focus:bg-white outline-none">
                        <template x-for="r in rolesList" :key="r.id">
                            <option :value="r.id" x-text="r.label + ' (' + r.name + ')'"></option>
                        </template>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">
                        <span x-text="userModalMode === 'edit' ? (lang === 'zh' ? '新密码 (留空则保持原密码)' : 'Password Baru (Kosongkan jika tidak diubah)') : (lang === 'zh' ? '登录密码 * (至少6位)' : 'Password Login * (Minimal 6 karakter)')"></span>
                    </label>
                    <input type="password" 
                           x-model="userForm.password" 
                           :required="userModalMode === 'create'"
                           placeholder="••••••••" 
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-slate-900 font-semibold focus:border-blue-500 focus:bg-white outline-none font-mono">
                </div>

                <div class="pt-2">
                    <label class="flex items-center space-x-2.5 cursor-pointer select-none">
                        <input type="checkbox" x-model="userForm.is_active" class="w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                        <span class="font-bold text-slate-800" x-text="lang === 'zh' ? '账号处于活跃正常状态' : 'Status Akun Aktif (Dapat Login)'">Status Akun Aktif</span>
                    </label>
                </div>

                <div class="pt-3 border-t border-slate-200 flex items-center justify-end space-x-2">
                    <button type="button" @click="userModalOpen = false" class="px-4 py-2 rounded-xl text-slate-600 hover:text-slate-800 font-semibold" x-text="lang === 'zh' ? '取消' : 'Batal'">Batal</button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-extrabold px-5 py-2 rounded-xl shadow-xs transition" x-text="userModalMode === 'edit' ? (lang === 'zh' ? '更新用户' : 'Perbarui User') : (lang === 'zh' ? '保存用户' : 'Simpan User')">Simpan User</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 7. MODAL ROLE (CREATE / EDIT)                                             -->
    <!-- ========================================================================= -->
    <div x-show="roleModalOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 drawer-overlay"
         @click.self="roleModalOpen = false" 
         style="display: none;">
        
        <div class="w-full max-w-lg bg-white rounded-3xl border border-slate-200 shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
            <div class="px-6 py-5 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-black text-slate-900" x-text="roleModalMode === 'edit' ? (lang === 'zh' ? '编辑角色信息' : 'Edit Data Role') : (lang === 'zh' ? '新增角色分组' : 'Tambah Role Baru')"></h3>
                    <p class="text-xs text-slate-500 mt-0.5" x-text="lang === 'zh' ? '角色创建后可在菜单管理中配置对应的功能页面权限。' : 'Setelah dibuat, hak akses menu role dapat diatur di Menu Management.'"></p>
                </div>
                <button @click="roleModalOpen = false" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-200/60 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form @submit.prevent="submitRoleForm()" class="p-6 overflow-y-auto space-y-4 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '角色代码 (Slug) *' : 'Kode Role (Slug Unik) *'"></label>
                    <input type="text" 
                           x-model="roleForm.name" 
                           :disabled="roleModalMode === 'edit'"
                           required 
                           placeholder="Contoh: finance / auditor / inventory" 
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-slate-900 font-mono font-bold focus:border-emerald-500 focus:bg-white outline-none disabled:bg-slate-100 disabled:text-slate-500">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '角色名称 (Display Label) *' : 'Nama Role (Label Tampilan) *'"></label>
                    <input type="text" x-model="roleForm.label" required placeholder="Contoh: Finance & Accounting" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-slate-900 font-semibold focus:border-emerald-500 focus:bg-white outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1" x-text="lang === 'zh' ? '权限职责说明' : 'Deskripsi & Tanggung Jawab'"></label>
                    <textarea x-model="roleForm.description" rows="3" placeholder="Jelaskan cakupan akses atau divisi role ini..." class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-slate-900 font-medium focus:border-emerald-500 focus:bg-white outline-none"></textarea>
                </div>

                <div class="pt-3 border-t border-slate-200 flex items-center justify-end space-x-2">
                    <button type="button" @click="roleModalOpen = false" class="px-4 py-2 rounded-xl text-slate-600 hover:text-slate-800 font-semibold" x-text="lang === 'zh' ? '取消' : 'Batal'">Batal</button>
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold px-5 py-2 rounded-xl shadow-xs transition" x-text="roleModalMode === 'edit' ? (lang === 'zh' ? '更新角色' : 'Perbarui Role') : (lang === 'zh' ? '保存角色' : 'Simpan Role')">Simpan Role</button>
                </div>
            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
    function erpUserManager(initialTab, rawUsers, rawRoles) {
        return {
            activeTab: initialTab || 'user',
            searchQuery: '',
            roleFilter: 'all',
            usersList: rawUsers || [],
            rolesList: rawRoles || [],

            // Modals
            userModalOpen: false,
            userModalMode: 'create', // create / edit
            roleModalOpen: false,
            roleModalMode: 'create', // create / edit

            userForm: {
                id: null,
                name: '',
                email: '',
                usergrpid: 1,
                password: '',
                is_active: true
            },

            roleForm: {
                id: null,
                name: '',
                label: '',
                description: ''
            },

            switchTab(tab) {
                this.activeTab = tab;
                this.searchQuery = '';
                const url = new URL(window.location);
                url.searchParams.set('tab', tab);
                window.history.replaceState({}, '', url);
            },

            resetFilters() {
                this.searchQuery = '';
                this.roleFilter = 'all';
            },

            get filteredUsers() {
                const q = this.searchQuery.toLowerCase();
                const rf = this.roleFilter;
                return this.usersList.filter(u => {
                    const matchesSearch = !q || `${u.name || ''} ${u.email || ''} ${u.role || ''} ${u.role_model?.label || ''}`.toLowerCase().includes(q);
                    const matchesRole = (rf === 'all') || (u.usergrpid === Number(rf));
                    return matchesSearch && matchesRole;
                });
            },

            get filteredRoles() {
                const q = this.searchQuery.toLowerCase();
                return this.rolesList.filter(r => {
                    if (!q) return true;
                    return `${r.name || ''} ${r.label || ''} ${r.description || ''}`.toLowerCase().includes(q);
                });
            },

            getCreateButtonText() {
                if (this.lang === 'zh') {
                    return this.activeTab === 'user' ? '新增用户' : '新增角色';
                }
                return this.activeTab === 'user' ? 'Tambah User' : 'Tambah Role';
            },

            openCreateModal() {
                if (this.activeTab === 'user') {
                    this.userModalMode = 'create';
                    this.userForm = {
                        id: null,
                        name: '',
                        email: '',
                        usergrpid: this.rolesList[0]?.id || 1,
                        password: '',
                        is_active: true
                    };
                    this.userModalOpen = true;
                } else {
                    this.roleModalMode = 'create';
                    this.roleForm = {
                        id: null,
                        name: '',
                        label: '',
                        description: ''
                    };
                    this.roleModalOpen = true;
                }
            },

            openEditUserModal(user) {
                this.userModalMode = 'edit';
                this.userForm = {
                    id: user.id,
                    name: user.name,
                    email: user.email,
                    usergrpid: user.usergrpid || 1,
                    password: '',
                    is_active: Boolean(user.is_active)
                };
                this.userModalOpen = true;
            },

            openEditRoleModal(role) {
                this.roleModalMode = 'edit';
                this.roleForm = {
                    id: role.id,
                    name: role.name,
                    label: role.label,
                    description: role.description || ''
                };
                this.roleModalOpen = true;
            },

            formatDate(dt) {
                if (!dt) return '-';
                return new Date(dt).toLocaleDateString('id-ID', { year: 'numeric', month: 'short', day: 'numeric' });
            },

            async toggleUserStatus(user) {
                const actionLabel = user.is_active ? 'menonaktifkan' : 'mengaktifkan';
                if (!confirm(`Apakah Anda yakin ingin ${actionLabel} akun user ${user.name}?`)) {
                    return;
                }

                try {
                    const res = await fetch(`/erp/users/${user.id}/toggle-status`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                    const result = await res.json();
                    if (result.success) {
                        user.is_active = result.is_active;
                        alert(result.message);
                    } else {
                        alert(result.message || 'Gagal mengubah status user.');
                    }
                } catch(e) {
                    alert('Terjadi kesalahan pada server saat memperbarui status user.');
                }
            },

            async deleteRole(role) {
                if (!confirm(`Apakah Anda yakin ingin menghapus role '${role.label}'? Tindakan ini tidak dapat dibatalkan.`)) {
                    return;
                }

                try {
                    const res = await fetch(`/erp/roles/${role.id}/delete`, {
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
                        alert(result.message || 'Gagal menghapus role.');
                    }
                } catch(e) {
                    alert('Terjadi kesalahan pada server saat menghapus role.');
                }
            },

            async submitUserForm() {
                try {
                    const url = this.userModalMode === 'edit'
                        ? `/erp/users/${this.userForm.id}/update`
                        : '{{ route("erp.users.store") }}';

                    const res = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(this.userForm)
                    });
                    const result = await res.json();
                    if (result.success) {
                        alert(result.message);
                        window.location.reload();
                    } else {
                        alert('Gagal menyimpan: ' + (result.message || 'Periksa kembali data yang dimasukkan.'));
                    }
                } catch(e) {
                    alert('Terjadi kesalahan pada server.');
                }
            },

            async submitRoleForm() {
                try {
                    const url = this.roleModalMode === 'edit'
                        ? `/erp/roles/${this.roleForm.id}/update`
                        : '{{ route("erp.roles.store") }}';

                    const res = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(this.roleForm)
                    });
                    const result = await res.json();
                    if (result.success) {
                        alert(result.message);
                        window.location.reload();
                    } else {
                        alert('Gagal menyimpan: ' + (result.message || 'Periksa kembali data yang dimasukkan.'));
                    }
                } catch(e) {
                    alert('Terjadi kesalahan pada server.');
                }
            }
        };
    }
</script>
@endpush
@endsection
