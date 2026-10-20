@extends('layouts.erp')

@section('title', 'Master Config & Dropdowns - Keylock ERP')

@section('breadcrumbs')
    <a href="{{ route('erp.index') }}" class="hover:text-slate-800">Home</a>
    <span>›</span>
    <span class="text-slate-800 font-semibold" x-text="lang === 'zh' ? '系统配置与下拉选项' : 'Config & Dropdown'">Config & Dropdown</span>
@endsection

@section('content')
<div x-data="erpConfigManager()">

    <!-- ========================================================================= -->
    <!-- 1. PAGE HEADER                                                            -->
    <!-- ========================================================================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-2xl bg-amber-400 text-slate-950 flex items-center justify-center font-black shadow-xs">
                    <svg class="w-5 h-5 text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-900 tracking-tight" x-text="lang === 'zh' ? '系统配置与下拉选项管理 (Master Config)' : 'Pengaturan Sistem & Dropdown (Master Config)'">
                        Pengaturan Sistem & Dropdown (Master Config)
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5" x-text="lang === 'zh' ? '动态管理全系统下拉选择项、双语词库 (印尼语/中文)、状态定义以及公司基本资料，免硬编码。' : 'Kelola opsi pilihan dropdown, terjemahan dwibahasa (ID & ZH), status transaksi, dan profil perusahaan tanpa perlu hardcode.'">
                        Kelola opsi pilihan dropdown, terjemahan dwibahasa (ID & ZH), status transaksi, dan profil perusahaan tanpa perlu hardcode.
                    </p>
                </div>
            </div>
        </div>

        @if($currentGroup !== 'company')
            <button type="button" 
                    @click="openCreateOptionModal('{{ $currentGroup }}')" 
                    class="bg-blue-600 hover:bg-blue-700 text-white font-extrabold px-5 py-2.5 rounded-full shadow-md text-xs transition flex items-center space-x-2 self-start md:self-auto active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                <span x-text="lang === 'zh' ? '+ 新增下拉配置选项' : '+ Tambah Opsi Dropdown'">+ Tambah Opsi Dropdown</span>
            </button>
        @endif
    </div>

    <!-- ========================================================================= -->
    <!-- 2. GROUP TABS NAVIGATION                                                  -->
    <!-- ========================================================================= -->
    <div class="flex flex-wrap items-center gap-2 mb-6 border-b border-slate-200/80 pb-3">
        @foreach($groupLabels as $gKey => $gMeta)
            @php
                $isActive = ($currentGroup === $gKey);
                $cnt = $groupCounts[$gKey] ?? 0;
            @endphp
            <a href="{{ route('erp.configs.index', ['group' => $gKey]) }}" 
               class="px-4 py-2.5 rounded-2xl text-xs font-bold transition flex items-center space-x-2 {{ $isActive ? 'bg-slate-900 text-white shadow-xs' : 'bg-white text-slate-600 hover:text-slate-900 border border-slate-200/90' }}">
                <span x-text="lang === 'zh' ? '{{ $gMeta['name_zh'] }}' : '{{ $gMeta['name'] }}'">{{ $gMeta['name'] }}</span>
                @if($gKey !== 'company')
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $isActive ? 'bg-slate-800 text-amber-300' : 'bg-slate-100 text-slate-700' }}">
                        {{ $cnt }}
                    </span>
                @endif
            </a>
        @endforeach
    </div>

    <!-- ========================================================================= -->
    <!-- 3. TAB CONTENT: DROPDOWN OPTIONS TABLE                                     -->
    <!-- ========================================================================= -->
    @if($currentGroup !== 'company')
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden mb-6">
            
            <!-- Description Banner & Search Box -->
            <div class="p-4 sm:p-5 border-b border-slate-200/80 bg-slate-50/70 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h3 class="font-black text-slate-900 text-sm" x-text="lang === 'zh' ? '{{ $groupLabels[$currentGroup]['name_zh'] }}' : '{{ $groupLabels[$currentGroup]['name'] }}'">
                        {{ $groupLabels[$currentGroup]['name'] }}
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">{{ $groupLabels[$currentGroup]['desc'] }}</p>
                </div>

                <form method="GET" action="{{ route('erp.configs.index') }}" class="relative w-full sm:w-72">
                    <input type="hidden" name="group" value="{{ $currentGroup }}">
                    <input type="text" 
                           name="search" 
                           value="{{ $search ?? '' }}" 
                           placeholder="Cari kode atau label opsi..." 
                           class="w-full h-9 bg-white border border-slate-300 rounded-xl pl-8 pr-12 text-xs font-medium focus:border-amber-400 focus:outline-none">
                    <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    <button type="submit" class="absolute right-1 top-1/2 -translate-y-1/2 px-2 py-0.5 bg-slate-900 text-white rounded-lg text-[10px] font-bold">Cari</button>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase tracking-wider text-[10px] font-black">
                            <th class="py-3 px-3 w-12 text-center">Urutan</th>
                            <th class="py-3 px-4 w-44">Kode / Key Teknis</th>
                            <th class="py-3 px-4">Label Indonesia (ID)</th>
                            <th class="py-3 px-4">Label Mandarin (ZH)</th>
                            <th class="py-3 px-3 w-28 text-center">Status</th>
                            <th class="py-3 px-3 w-32 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($options as $opt)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3 px-3 text-center font-mono font-bold text-slate-400">{{ $opt->order_num }}</td>
                                <td class="py-3 px-4 font-mono font-bold text-slate-900">
                                    <span class="px-2 py-0.5 rounded bg-slate-100 border border-slate-200 text-slate-800 text-[11px]">{{ $opt->key }}</span>
                                </td>
                                <td class="py-3 px-4 font-bold text-slate-800">
                                    <div class="flex items-center space-x-1.5">
                                        <span class="w-2 h-2 rounded-full bg-{{ $opt->badge_color ?? 'slate' }}-500"></span>
                                        <span>{{ $opt->label_id }}</span>
                                    </div>
                                    @if($opt->description)
                                        <p class="text-[10px] text-slate-400 mt-0.5 font-normal">{{ $opt->description }}</p>
                                    @endif
                                </td>
                                <td class="py-3 px-4 font-semibold text-slate-700">
                                    {{ $opt->label_zh ?: '—' }}
                                </td>
                                <td class="py-3 px-3 text-center">
                                    <form action="{{ route('erp.configs.toggle_status', $opt->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" 
                                                title="Klik untuk ubah status aktif/nonaktif"
                                                class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold border transition {{ $opt->is_active ? 'bg-emerald-100 text-emerald-800 border-emerald-300 hover:bg-emerald-200' : 'bg-slate-100 text-slate-500 border-slate-300 hover:bg-slate-200' }}">
                                            {{ $opt->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="py-3 px-3 text-center">
                                    <div class="flex items-center justify-center space-x-1">
                                        <button type="button" 
                                                @click="openEditOptionModal({{ json_encode($opt) }})" 
                                                class="p-1.5 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 transition" 
                                                title="Edit Opsi">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>

                                        <form action="{{ route('erp.configs.destroy', $opt->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus opsi ini?')">
                                            @csrf
                                            <button type="submit" class="p-1.5 rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-100 transition" title="Hapus Opsi">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400">
                                    Belum ada opsi dropdown untuk kategori ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    <!-- ========================================================================= -->
    <!-- 4. TAB CONTENT: PROFIL PERUSAHAAN & BANK CONFIG                           -->
    <!-- ========================================================================= -->
    @else
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-6 mb-6">
            <div class="mb-5 pb-4 border-b border-slate-200">
                <h3 class="font-black text-slate-900 text-base" x-text="lang === 'zh' ? '企业信息与发票资料' : 'Profil Perusahaan, NPWP & Rekening Bank'">Profil Perusahaan, NPWP & Rekening Bank</h3>
                <p class="text-xs text-slate-500 mt-0.5">Informasi resmi ini langsung tercetak otomatis pada cetakan Invoice, Quotation PDF, dan Surat Jalan DO.</p>
            </div>

            <form action="{{ route('erp.configs.company.update') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Nama Resmi Perusahaan *</label>
                        <input type="text" name="company_name" value="{{ $companyConfigs['company_name']->value ?? 'PT KEY LOCK INDONESIA' }}" required class="w-full h-10 bg-slate-50 border border-slate-300 rounded-xl px-3 font-bold text-xs focus:bg-white focus:border-amber-400 focus:outline-none">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">NPWP Perusahaan</label>
                        <input type="text" name="company_npwp" value="{{ $companyConfigs['company_npwp']->value ?? '1000 0000 0573 6973' }}" class="w-full h-10 bg-slate-50 border border-slate-300 rounded-xl px-3 font-mono text-xs focus:bg-white focus:border-amber-400 focus:outline-none">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block font-bold text-slate-700 mb-1">Alamat Resmi Kantor / Pool *</label>
                        <textarea name="company_address" rows="2" required class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-xs focus:bg-white focus:border-amber-400 focus:outline-none">{{ $companyConfigs['company_address']->value ?? 'Jalan Arteri KIIC Rolling Hills Ruko Pacific Plaza No. 91, Desa Margakaya, Kecamatan Telukjambe Barat, Kabupaten Karawang 41361.' }}</textarea>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Nomor Telepon & WhatsApp</label>
                        <input type="text" name="company_phone" value="{{ $companyConfigs['company_phone']->value ?? '+62 21 8990 1234 / +62 811 2345 6789' }}" class="w-full h-10 bg-slate-50 border border-slate-300 rounded-xl px-3 text-xs focus:bg-white focus:border-amber-400 focus:outline-none">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Email Resmi</label>
                        <input type="email" name="company_email" value="{{ $companyConfigs['company_email']->value ?? 'keylockindo@outlook.com' }}" class="w-full h-10 bg-slate-50 border border-slate-300 rounded-xl px-3 text-xs focus:bg-white focus:border-amber-400 focus:outline-none">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Nama Direktur Utama</label>
                        <input type="text" name="director_name" value="{{ $companyConfigs['director_name']->value ?? 'QI SHUJUN 齐书骏' }}" class="w-full h-10 bg-slate-50 border border-slate-300 rounded-xl px-3 text-xs focus:bg-white focus:border-amber-400 focus:outline-none">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Jabatan Direktur</label>
                        <input type="text" name="director_title" value="{{ $companyConfigs['director_title']->value ?? 'Presiden Direktur 董事长' }}" class="w-full h-10 bg-slate-50 border border-slate-300 rounded-xl px-3 text-xs focus:bg-white focus:border-amber-400 focus:outline-none">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Nomor Rekening Bank BCA Resmi</label>
                        <input type="text" name="bank_bca_account" value="7260555116" class="w-full h-10 bg-slate-50 border border-slate-300 rounded-xl px-3 font-mono text-xs focus:bg-white focus:border-amber-400 focus:outline-none">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Nomor Rekening Bank BRI Resmi</label>
                        <input type="text" name="bank_bri_account" value="609900978" class="w-full h-10 bg-slate-50 border border-slate-300 rounded-xl px-3 font-mono text-xs focus:bg-white focus:border-amber-400 focus:outline-none">
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-200 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-black shadow-md transition">
                        Simpan Perubahan Profil Perusahaan
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- 5. MODAL TAMBAH / EDIT OPSI DROPDOWN                                      -->
    <!-- ========================================================================= -->
    <div x-show="modalOptionOpen" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-5 bg-slate-900/60 backdrop-blur-xs"
         @click.self="modalOptionOpen = false">
        
        <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl border border-slate-200 overflow-hidden my-auto animate-in zoom-in-95 duration-150">
            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-black text-slate-900" x-text="isEditOption ? 'Edit Opsi Dropdown' : 'Tambah Opsi Dropdown Baru'"></h3>
                    <p class="text-xs text-slate-500">Konfigurasi nilai teknis dan label bahasa Indonesia & Mandarin.</p>
                </div>
                <button type="button" @click="modalOptionOpen = false" class="text-slate-400 hover:text-slate-600 p-2 rounded-xl hover:bg-slate-200 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form :action="isEditOption ? '/erp/system-configs/' + optionForm.id + '/update' : '{{ route('erp.configs.store') }}'" method="POST" class="p-6 space-y-4 text-xs">
                @csrf
                <input type="hidden" name="group" x-model="optionForm.group">

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Kode / Key Teknis *</label>
                    <input type="text" name="key" x-model="optionForm.key" :readonly="isEditOption" required placeholder="Contoh: sub_rental, oil_change, Net 45 Days" class="w-full h-10 bg-slate-50 border border-slate-300 rounded-xl px-3 font-mono font-bold text-xs focus:bg-white focus:border-amber-400 focus:outline-none">
                    <p class="text-[10px] text-slate-400 mt-0.5">Kode unik yang disimpan ke database sistem.</p>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Label Bahasa Indonesia (ID) *</label>
                    <input type="text" name="label_id" x-model="optionForm.label_id" required placeholder="Contoh: Sewa Unit (Sub-Rent)" class="w-full h-10 bg-slate-50 border border-slate-300 rounded-xl px-3 text-xs font-bold text-slate-900 focus:bg-white focus:border-amber-400 focus:outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Label Bahasa Mandarin (ZH)</label>
                    <input type="text" name="label_zh" x-model="optionForm.label_zh" placeholder="Contoh: 设备转租 (Sub-Rent)" class="w-full h-10 bg-slate-50 border border-slate-300 rounded-xl px-3 text-xs font-semibold text-slate-800 focus:bg-white focus:border-amber-400 focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Urutan Tampilan</label>
                        <input type="number" name="order_num" x-model="optionForm.order_num" min="0" class="w-full h-10 bg-slate-50 border border-slate-300 rounded-xl px-3 font-mono text-xs focus:bg-white focus:border-amber-400 focus:outline-none">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Warna Badge</label>
                        <select name="badge_color" x-model="optionForm.badge_color" class="w-full h-10 bg-slate-50 border border-slate-300 rounded-xl px-3 text-xs font-bold focus:bg-white focus:border-amber-400 focus:outline-none">
                            <option value="blue">Biru (Blue)</option>
                            <option value="emerald">Hijau (Emerald)</option>
                            <option value="amber">Kuning / Oranye (Amber)</option>
                            <option value="purple">Ungu (Purple)</option>
                            <option value="rose">Merah (Rose)</option>
                            <option value="slate">Abu-abu (Slate)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Keterangan / Deskripsi</label>
                    <textarea name="description" x-model="optionForm.description" rows="2" placeholder="Keterangan penggunaan opsi ini..." class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-xs focus:bg-white focus:border-amber-400 focus:outline-none"></textarea>
                </div>

                <div class="pt-3 border-t border-slate-200 flex items-center justify-end space-x-2">
                    <button type="button" @click="modalOptionOpen = false" class="px-4 py-2.5 rounded-xl border border-slate-300 font-bold text-slate-600 hover:bg-slate-100 transition">Batal</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold shadow-md transition">Simpan Opsi</button>
                </div>
            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
    function erpConfigManager() {
        return {
            modalOptionOpen: false,
            isEditOption: false,
            optionForm: {
                id: null,
                group: '{{ $currentGroup }}',
                key: '',
                label_id: '',
                label_zh: '',
                order_num: 0,
                badge_color: 'slate',
                description: ''
            },

            openCreateOptionModal(grp) {
                this.isEditOption = false;
                this.optionForm = {
                    id: null,
                    group: grp,
                    key: '',
                    label_id: '',
                    label_zh: '',
                    order_num: 1,
                    badge_color: 'blue',
                    description: ''
                };
                this.modalOptionOpen = true;
            },

            openEditOptionModal(opt) {
                this.isEditOption = true;
                this.optionForm = {
                    id: opt.id,
                    group: opt.group,
                    key: opt.key,
                    label_id: opt.label_id,
                    label_zh: opt.label_zh || '',
                    order_num: opt.order_num || 0,
                    badge_color: opt.badge_color || 'slate',
                    description: opt.description || ''
                };
                this.modalOptionOpen = true;
            }
        };
    }
</script>
@endpush
@endsection
