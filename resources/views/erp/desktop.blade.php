@extends('layouts.erp')

@section('title')
    @if($dashboardRole === 'sales')
        Dashboard Sales - Keylock Indonesia
    @elseif($dashboardRole === 'owner')
        Dashboard Founder & Owner - Keylock Indonesia
    @else
        Dashboard Administrator & Operasional - Keylock Indonesia
    @endif
@endsection

@section('breadcrumbs')
    <a href="{{ route('erp.index') }}" class="hover:text-slate-800">Home</a>
    <span>›</span>
    @if($dashboardRole === 'sales')
        <span class="text-slate-800 font-bold" x-text="lang === 'zh' ? '销售控制面板' : 'Dashboard Sales'">Dashboard Sales</span>
    @elseif($dashboardRole === 'owner')
        <span class="text-slate-800 font-bold" x-text="lang === 'zh' ? '创始人控制面板' : 'Dashboard Founder / Owner'">Dashboard Founder / Owner</span>
    @else
        <span class="text-slate-800 font-bold" x-text="lang === 'zh' ? '管理员控制面板' : 'Dashboard Administrator'">Dashboard Administrator</span>
    @endif
@endsection
@section('content')
<!-- ========================================================================= -->
<!-- MOBILE HERO BANNER (VISIBLE ON SCREENS < MD)                              -->
<!-- ========================================================================= -->
<div class="block md:hidden mb-5">
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-[#142A57] via-[#1a3875] to-[#2563eb] p-4 text-white shadow-md">
        <div class="relative z-10">
            <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-white/15 text-blue-100 backdrop-blur-xs mb-1.5">
                Keylock ERP System
            </span>
            <h2 class="text-base font-extrabold leading-snug tracking-tight text-white">
                Accurate, Efficient, and Innovative.
            </h2>
            <p class="text-xs text-blue-100/90 mt-1 leading-relaxed">
                Kelola operasional rental armada, lacak pengiriman, dan kendalikan invoicing secara efisien.
            </p>
        </div>
        <div class="absolute -right-4 -bottom-6 w-24 h-24 rounded-full bg-white/5 pointer-events-none"></div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- DYNAMIC ROLE-BASED DASHBOARDS (FOR BOTH DESKTOP & MOBILE)                 -->
<!-- ========================================================================= -->
<div class="space-y-6">
    @if($dashboardRole === 'sales')
        @include('erp.dashboards.sales')
    @elseif($dashboardRole === 'owner')
        @include('erp.dashboards.owner')
    @else
        @include('erp.dashboards.admin')
    @endif
</div>
@endsection
