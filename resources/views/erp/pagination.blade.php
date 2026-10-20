@if ($paginator->hasPages())
    <div class="px-6 py-4 bg-white border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
        <!-- Result count summary & Per-page option -->
        <div class="flex flex-wrap items-center gap-3 text-xs text-slate-500 font-medium">
            <span x-text="lang === 'zh' ? '显示第 {{ $paginator->firstItem() }} 至 {{ $paginator->lastItem() }} 条，共 {{ $paginator->total() }} 条' : 'Menampilkan {{ $paginator->firstItem() }} - {{ $paginator->lastItem() }} dari {{ $paginator->total() }} data'">
                Menampilkan {{ $paginator->firstItem() }} - {{ $paginator->lastItem() }} dari {{ $paginator->total() }} data
            </span>
            <span class="text-slate-300">|</span>
            <div class="flex items-center space-x-1.5">
                <span class="text-[11px] text-slate-400 font-semibold" x-text="lang === 'zh' ? '每页显示:' : 'Tampilkan:'">Tampilkan:</span>
                <select onchange="window.setTablePerPage(this.value)" class="bg-slate-50 border border-slate-200 rounded-lg px-2 py-1 text-xs font-black text-slate-700 outline-none focus:border-blue-500 cursor-pointer shadow-2xs hover:bg-white transition">
                    @foreach([10, 15, 25, 50, 100] as $size)
                        <option value="{{ $size }}" {{ $paginator->perPage() == $size ? 'selected' : '' }}>{{ $size }}</option>
                    @endforeach
                </select>
                <span class="text-[11px] text-slate-400 font-semibold" x-text="lang === 'zh' ? '条数据' : 'data / hal'">data / hal</span>
            </div>
        </div>

        <!-- Navigation Controls -->
        <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center space-x-1.5">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="w-8 h-8 rounded-xl border border-slate-200/80 bg-slate-50 text-slate-300 text-xs font-bold flex items-center justify-center cursor-not-allowed select-none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15 19l-7-7 7-7"></path></svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="w-8 h-8 rounded-xl border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 text-xs font-bold flex items-center justify-center transition shadow-2xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15 19l-7-7 7-7"></path></svg>
                </a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span class="w-8 h-8 flex items-center justify-center text-slate-400 text-xs font-bold">{{ $element }}</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="w-8 h-8 rounded-xl bg-slate-900 text-white text-xs font-black flex items-center justify-center shadow-xs select-none">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="w-8 h-8 rounded-xl border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 text-xs font-bold flex items-center justify-center transition shadow-2xs">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="w-8 h-8 rounded-xl border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 text-xs font-bold flex items-center justify-center transition shadow-2xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            @else
                <span class="w-8 h-8 rounded-xl border border-slate-200/80 bg-slate-50 text-slate-300 text-xs font-bold flex items-center justify-center cursor-not-allowed select-none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 5l7 7-7 7"></path></svg>
                </span>
            @endif
        </nav>
    </div>
@elseif($paginator->total() > 0)
    <div class="px-6 py-3.5 bg-slate-50/50 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500 font-medium">
        <div class="flex flex-wrap items-center gap-3">
            <span x-text="lang === 'zh' ? '显示共 {{ $paginator->total() }} 条记录' : 'Menampilkan seluruh {{ $paginator->total() }} data'">
                Menampilkan seluruh {{ $paginator->total() }} data
            </span>
            <span class="text-slate-300">|</span>
            <div class="flex items-center space-x-1.5">
                <span class="text-[11px] text-slate-400 font-semibold" x-text="lang === 'zh' ? '每页显示:' : 'Tampilkan:'">Tampilkan:</span>
                <select onchange="window.setTablePerPage(this.value)" class="bg-white border border-slate-200 rounded-lg px-2 py-1 text-xs font-black text-slate-700 outline-none focus:border-blue-500 cursor-pointer shadow-2xs hover:bg-slate-50 transition">
                    @foreach([10, 15, 25, 50, 100] as $size)
                        <option value="{{ $size }}" {{ $paginator->perPage() == $size ? 'selected' : '' }}>{{ $size }}</option>
                    @endforeach
                </select>
                <span class="text-[11px] text-slate-400 font-semibold" x-text="lang === 'zh' ? '条数据' : 'data / hal'">data / hal</span>
            </div>
        </div>
        <span class="text-[11px] text-slate-400 font-semibold" x-text="lang === 'zh' ? '第 1 页 (共 1 页)' : 'Halaman 1 dari 1'">Halaman 1 dari 1</span>
    </div>
@endif
