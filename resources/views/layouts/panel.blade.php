@extends('layouts.app')

@section('content')
<script>
/* Apply navigation state before paint to prevent sidebar flashes/overlap. */
(function () {
    try {
        var root = document.documentElement;
        if (window.matchMedia('(max-width: 900px)').matches) {
            root.classList.add('sipadu-mobile');
        } else if (localStorage.getItem('sipadu-sidebar-state') === 'closed') {
            root.classList.add('sipadu-sidebar-closed');
        }
    } catch (e) {}
})();
</script>
<style>
.app-layout{display:flex;width:100%;min-height:100vh;background:var(--bg-canvas);overflow:visible}
.app-layout>.sidebar{position:fixed!important;inset:0 auto 0 0!important;width:248px!important;min-width:248px!important;height:100vh!important;z-index:50;background:rgba(255,255,255,.88)!important;border-right:1px solid rgba(220,229,224,.82)!important;overflow-x:hidden;overflow-y:auto;transition:width .24s ease,min-width .24s ease!important}
.app-layout>main{margin-left:248px!important;flex:1;min-width:0;min-height:100vh;background:var(--bg-canvas)!important;overflow:visible!important;width:auto!important;transition:margin-left .24s ease!important}
/* collapsed desktop sidebar: icon rail remains visible */
.app-layout.sidebar-closed>.sidebar,html.sipadu-sidebar-closed .app-layout>.sidebar{transform:none!important;width:78px!important;min-width:78px!important}
.app-layout.sidebar-closed>main,html.sipadu-sidebar-closed .app-layout>main{margin-left:78px!important}
/* No animation during initial render, so the sidebar never flashes open/closed when changing pages. */
html:not(.sidebar-ready) .app-layout>.sidebar,html:not(.sidebar-ready) .app-layout>main{transition:none!important}
.app-layout>main .topbar{position:sticky;top:0;z-index:40;display:flex;align-items:center;justify-content:space-between;min-height:60px;height:60px;padding:0 22px;background:rgba(245,249,247,.72)!important;border-bottom:1px solid rgba(220,229,224,.68)!important;backdrop-filter:blur(18px) saturate(150%);-webkit-backdrop-filter:blur(18px) saturate(150%);box-shadow:0 8px 28px rgba(20,32,26,.025)}
#sidebarToggle{display:flex!important;width:38px;height:38px;align-items:center;justify-content:center;border:1px solid rgba(220,229,224,.85)!important;border-radius:12px;background:rgba(255,255,255,.78)!important;color:var(--text-main)!important;cursor:pointer;flex:none;box-shadow:0 2px 8px rgba(20,32,26,.035)}
#sidebarToggle:hover{background:var(--bg-soft)!important;color:var(--brand-primary)!important}
.topbar-right{margin-left:auto;display:flex;align-items:center;gap:18px}.topbar-user{display:flex;align-items:center;gap:10px;text-decoration:none!important;color:var(--text-main)!important}.topbar-date{font-size:11px;color:var(--text-muted);white-space:nowrap}.topbar-user-info{text-align:right}.topbar-user-info strong{display:block;font-size:12px}.topbar-user-info small{font-size:10px;color:var(--text-muted)}.topbar-avatar{width:36px;height:36px;border-radius:50%;background:var(--brand-primary);color:#fff;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800}
.app-layout>main>.main-content{padding:28px;max-width:1600px;margin:0 auto;opacity:0;transform:translateY(6px);transition:opacity .18s ease,transform .18s ease}
html.page-ready .app-layout>main>.main-content{opacity:1;transform:none}
@media(max-width:900px){
    html,body{overflow-x:hidden!important;width:100%!important}
    .app-layout{display:block!important;overflow-x:hidden!important}
    .app-layout>main{margin-left:0!important;width:100%!important;min-width:0!important}
    .app-layout>main .topbar{padding:0 16px!important;width:100%!important}
    .app-layout>main>.main-content{padding:18px!important;width:100%!important;max-width:100%!important}
    .app-layout>.sidebar{width:min(84vw,300px)!important;min-width:min(84vw,300px)!important;transform:translateX(-110%)!important;z-index:1000!important}
    html.sipadu-mobile .app-layout.mobile-sidebar-open>.sidebar{transform:translateX(0)!important}
    .sidebar-backdrop{position:fixed!important;inset:0!important;z-index:990!important;display:block!important;background:rgba(13,31,23,.30)!important;opacity:0!important;pointer-events:none!important;transition:opacity .2s ease!important}
    html.sipadu-mobile .app-layout.mobile-sidebar-open .sidebar-backdrop{opacity:1!important;pointer-events:auto!important}
    .topbar-date{display:none!important}.topbar-right{gap:8px!important}
}
@media(max-width:520px){.app-layout>main>.main-content{padding:14px!important}.app-layout>main .topbar{padding:0 12px!important}.topbar-user-info{display:none!important}.topbar-right{gap:6px!important}#sidebarToggle{width:36px!important;height:36px!important}}
</style>
<div class="app-layout">
    @include('partials.nav')
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>
    <main>
        <div class="topbar">
            <button type="button" id="sidebarToggle" aria-label="Buka atau tutup sidebar"><i data-lucide="panel-left-open"></i></button>
            <div class="topbar-right">
                @auth
                    @php
                        $topbarUser = auth()->user();
                        $profileRoute = $topbarUser->role === 'admin' ? route('admin.profile') : route('borrower.profile');
                    @endphp
                    <span class="topbar-date">{{ now()->format('d/m/Y') }}</span>
                    <a href="{{ $profileRoute }}" class="topbar-user">
                        <span class="topbar-user-info"><strong>{{ $topbarUser->name }}</strong><small>{{ $topbarUser->role === 'admin' ? 'Administrator' : 'Peminjam' }}</small></span>
                        <span class="topbar-avatar">{{ strtoupper(substr($topbarUser->name, 0, 2)) }}</span>
                    </a>
                @endauth
            </div>
        </div>
        <div class="main-content">@yield('panel')</div>
    </main>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const layout = document.querySelector('.app-layout');
    const toggle = document.getElementById('sidebarToggle');
    const backdrop = document.getElementById('sidebarBackdrop');
    const storageKey = 'sipadu-sidebar-state';
    if (!layout || !toggle) return;

    const mobile = () => window.matchMedia('(max-width: 900px)').matches;
    const saved = (() => { try { return localStorage.getItem(storageKey) || 'open'; } catch(e) { return 'open'; } })();

    function desktopOpen(open, persist = true) {
        layout.classList.toggle('sidebar-closed', !open);
        document.documentElement.classList.toggle('sipadu-sidebar-closed', !open);
        if (persist) { try { localStorage.setItem(storageKey, open ? 'open' : 'closed'); } catch(e) {} }
        renderIcon(open);
    }
    function mobileOpen(open) {
        layout.classList.toggle('mobile-sidebar-open', open);
        renderIcon(open);
    }
    function renderIcon(open) {
        toggle.innerHTML = mobile() ? '<i data-lucide="menu"></i>' : (open ? '<i data-lucide="panel-left-close"></i>' : '<i data-lucide="panel-left-open"></i>');
        if (window.lucide) window.lucide.createIcons();
    }
    function sync() {
        if (mobile()) {
            document.documentElement.classList.add('sipadu-mobile');
            layout.classList.remove('mobile-sidebar-open');
            layout.classList.remove('sidebar-closed');
            renderIcon(false);
        } else {
            document.documentElement.classList.remove('sipadu-mobile');
            const open = saved !== 'closed';
            desktopOpen(open, false);
        }
    }
    sync();
    requestAnimationFrame(() => { document.documentElement.classList.add('sidebar-ready','page-ready'); });

    toggle.addEventListener('click', () => {
        if (mobile()) mobileOpen(!layout.classList.contains('mobile-sidebar-open'));
        else desktopOpen(layout.classList.contains('sidebar-closed'));
    });
    if (backdrop) backdrop.addEventListener('click', () => mobileOpen(false));
    document.addEventListener('keydown', e => { if (e.key === 'Escape' && mobile()) mobileOpen(false); });
    window.addEventListener('resize', sync);
});
</script>
@endsection
<style>
@media(max-width:900px){
  .app-layout{position:relative;overflow-x:hidden!important}
  .app-layout>main{width:100%!important;min-width:0!important}
  .app-layout>.sidebar{z-index:80!important;box-shadow:18px 0 40px rgba(15,35,28,.12);transition:transform .24s ease!important}
  .app-layout.sidebar-closed>.sidebar{transform:translateX(-100%)!important}
  .app-layout:not(.sidebar-closed)>.sidebar{transform:translateX(0)!important}
  .app-layout>main .topbar{min-width:0!important;width:100%!important}
  .topbar-user-info{max-width:150px;overflow:hidden}.topbar-user-info strong,.topbar-user-info small{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
}
@media(max-width:520px){
  .app-layout>main>.main-content{padding:14px!important}
  .app-layout>main .topbar{padding:0 12px!important}
  .topbar-user-info{display:none!important}
  .topbar-right{gap:6px!important}
  #sidebarToggle{width:36px!important;height:36px!important}
}
</style>
