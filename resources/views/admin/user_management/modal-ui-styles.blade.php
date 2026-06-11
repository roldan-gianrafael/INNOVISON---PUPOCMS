<style>
    .um-modal-content {
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, .62);
        border-bottom: 4px solid #70131b;
        border-radius: 24px;
        background: #fff;
    }
    .um-modal-head {
        align-items: center;
        padding: 18px 20px !important;
        border-bottom: 0;
        background: linear-gradient(135deg, #7f1d1d, #991b1b 55%, #b91c1c);
        color: #fff;
    }
    .um-modal-head-main { display:flex; min-width:0; align-items:center; gap:14px; }
    .um-modal-head-badge {
        display:inline-flex; width:46px; height:46px; flex:0 0 46px; align-items:center; justify-content:center;
        border:1px solid rgba(255,255,255,.24); border-radius:14px; background:rgba(255,255,255,.16);
        color:#fff; font-size:12px; font-weight:900;
    }
    .um-modal-head h3 { color:#fff !important; font-size:1rem; font-weight:900; text-transform:uppercase; }
    .um-modal-head .um-note { margin-top:5px; color:rgba(255,255,255,.92) !important; font-size:12px; }
    .um-modal-close {
        position:relative; display:inline-flex; width:40px; height:40px; min-width:40px; padding:0; overflow:hidden;
        align-items:center; justify-content:center; border:1px solid #8f2230; border-radius:50%; background:#70131b;
        color:#fff; cursor:pointer; font-size:24px; line-height:1;
        transition:border-color .18s ease, box-shadow .18s ease, transform .18s ease;
    }
    .um-modal-close::after {
        position:absolute; inset:0; background:linear-gradient(120deg, transparent, rgba(255,239,181,.52), transparent);
        content:""; transform:translateX(-135%); transition:transform .65s ease;
    }
    .um-modal-close:hover, .um-modal-close:focus-visible {
        border-color:#facc15; box-shadow:0 0 0 3px rgba(250,204,21,.18); outline:none; transform:translateY(-1px);
    }
    .um-modal-close:hover::after, .um-modal-close:focus-visible::after { transform:translateX(135%); }
    .um-modal-body {
        max-height:calc(100vh - 145px); overflow-y:auto; background:#f8fafc;
        scrollbar-color:#8f2230 #e5e7eb; scrollbar-width:thin;
    }
    #settingsModal .um-detail-card {
        border-radius:14px; background:#fff; box-shadow:0 10px 24px rgba(15,23,42,.06);
    }
    #settingsModal .um-section-block { border-radius:14px; background:#fff; }

    .um-custom-select { position:relative; }
    .um-custom-select-native {
        position:absolute !important; width:1px !important; height:1px !important; margin:0 !important;
        padding:0 !important; overflow:hidden; border:0 !important; opacity:0; pointer-events:none;
    }
    .um-custom-select-button {
        position:relative; width:100%; min-height:52px; padding:14px 48px 14px 16px;
        border:1px solid rgba(127,29,29,.22); border-radius:18px;
        background:linear-gradient(180deg,#fff,#fff8f6); color:#111827;
        box-shadow:0 10px 22px rgba(15,23,42,.08); cursor:pointer; font:inherit;
        font-size:14px; font-weight:800; text-align:left;
    }
    .um-custom-select-button::after {
        position:absolute; top:50%; right:18px; width:8px; height:8px;
        border-right:2px solid #70131b; border-bottom:2px solid #70131b; content:"";
        transform:translateY(-70%) rotate(45deg); transition:transform .18s ease;
    }
    .um-custom-select.is-open .um-custom-select-button {
        border-color:#8f2230; box-shadow:0 0 0 3px rgba(143,34,48,.10),0 12px 24px rgba(15,23,42,.10);
    }
    .um-custom-select.is-open .um-custom-select-button::after { transform:translateY(-25%) rotate(225deg); }
    .um-custom-select-menu {
        position:absolute; z-index:5100; top:calc(100% + 8px); right:0; left:0; display:none; gap:8px;
        max-height:250px; overflow-y:auto; padding:10px; border:1px solid rgba(127,29,29,.18);
        border-radius:16px; background:#fff; box-shadow:0 18px 38px rgba(15,23,42,.18);
    }
    .um-custom-select.is-open .um-custom-select-menu { display:grid; }
    .um-custom-select-option {
        width:100%; padding:11px 14px; border:1px solid rgba(148,163,184,.22); border-radius:999px;
        background:linear-gradient(180deg,#fff,#f8fafc); color:#1e293b; cursor:pointer; font:inherit;
        font-size:13px; font-weight:800; text-align:left;
        transition:background .18s ease,border-color .18s ease,color .18s ease,transform .18s ease;
    }
    .um-custom-select-option:hover, .um-custom-select-option.is-selected {
        border-color:#8b0000; background:linear-gradient(135deg,#8b0000,#70131b); color:#facc15;
        transform:translateY(-1px);
    }
    html[data-theme="dark"] .um-modal-body,
    html[data-theme="dark"] #settingsModal .um-detail-card,
    html[data-theme="dark"] #settingsModal .um-section-block { background:#111827; }
    html[data-theme="dark"] .um-custom-select-button,
    html[data-theme="dark"] .um-custom-select-menu { border-color:rgba(248,113,113,.28); background:#0f172a; color:#fff; }
    html[data-theme="dark"] .um-custom-select-option {
        border-color:rgba(148,163,184,.22); background:#172033; color:#fff;
    }
</style>
