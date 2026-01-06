<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }} - Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root { --bg1:#5E60CE; --bg2:#7A5CF4; --bg3:#5DA8F2; --muted:rgba(255,255,255,.8); --card:rgba(255,255,255,.12); --card-border:rgba(255,255,255,.25); }
        *{box-sizing:border-box}
        html,body{height:100%}
        body{margin:0;font-family:Inter,system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif;color:#fff;
            background: radial-gradient(1200px 800px at 10% -10%, rgba(255,255,255,.08), transparent 50%),
                        linear-gradient(135deg, var(--bg1), var(--bg2) 50%, var(--bg3));
            padding:24px}
        .container{max-width:1120px;margin:0 auto}
        .header{display:flex;align-items:center;justify-content:space-between;margin-bottom:24px}
        .brand{display:flex;align-items:center;gap:12px}
        .logo{width:40px;height:40px;border-radius:9999px;background:rgba(255,255,255,.3);display:flex;align-items:center;justify-content:center;font-weight:600}
        .title{margin:0;font-size:28px;font-weight:600}
        .muted{color:var(--muted);font-size:14px}
        .grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:16px}
        .panel{background:var(--card);border:1px solid var(--card-border);border-radius:16px;padding:20px;backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);box-shadow:0 20px 60px rgba(0,0,0,.20)}
        .panel h3{margin:0 0 8px;font-weight:600}
        .value{font-size:28px;font-weight:600;margin:6px 0 0}
        .actions{display:flex;gap:12px}
        .btn{padding:10px 14px;border-radius:10px;background:rgba(255,255,255,.25);color:#fff;font-weight:600;border:1px solid rgba(255,255,255,.3);cursor:pointer;font-size:14px;text-decoration:none}
        .btn:hover{background:rgba(255,255,255,.35)}
        .row{display:grid;grid-template-columns:1.2fr .8fr;gap:16px;margin-top:16px}
        @media (max-width: 900px){.row{grid-template-columns:1fr}}
        /* Dashboard redesign overrides */
        :root { --bg:#f5f7fb; --surface:#ffffff; --surface-2:#f3f4f6; --border:#e5e7eb; --text:#1f2937; --muted-text:#6b7280; --primary:#7A5CF4; --primary-2:#5E60CE; --primary-3:#5DA8F2; --danger:#ef4444; }
        body{background:var(--bg);color:var(--text)}
        .layout{display:grid;grid-template-columns:260px 1fr;gap:16px;min-height:100vh}
        .sidebar{background:var(--surface);border-right:1px solid var(--border);padding:16px 12px;position:sticky;top:0;height:100vh}
        .profile{display:flex;align-items:center;gap:10px;margin-bottom:12px}
        .avatar{width:36px;height:36px;border-radius:9999px;background:#e5e7eb;display:flex;align-items:center;justify-content:center;font-weight:600;color:#374151}
        .brand{font-weight:600}
        .menu-btn{margin-left:auto;background:none;border:0;font-size:18px;color:#6b7280;cursor:pointer}
        .nav{display:flex;flex-direction:column;gap:4px;margin-top:8px}
        .nav-title{padding:10px 12px;color:#6b7280;font-size:13px}
        .nav-item{display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:10px;color:#374151;text-decoration:none}
        .nav-item:hover{background:#f3f4f6}
        .nav-item.active{background:#eef2ff;color:#4f46e5;box-shadow:inset 2px 0 0 #8b5cf6}
        .main{padding:12px}
        .topbar{background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:10px 12px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:12px;z-index:10}
        .tabs{display:flex;gap:10px}
        .tab{display:flex;align-items:center;gap:10px;padding:8px 12px;border-radius:9999px;background:#f3f4f6;color:#374151;font-weight:500}
        .tab.active{background:linear-gradient(135deg,var(--primary),var(--primary-2));color:#fff;box-shadow:0 6px 20px rgba(124,58,237,.35)}
        .right{display:flex;align-items:center;gap:12px}
        .badge{background:linear-gradient(135deg,var(--primary),var(--primary-2));color:#fff;padding:8px 12px;border-radius:12px;font-weight:600}
        .icon-btn{width:36px;height:36px;border-radius:10px;border:1px solid var(--border);background:var(--surface);display:flex;align-items:center;justify-content:center;cursor:pointer}
        .user{display:flex;align-items:center;gap:10px}
        .user-avatar{width:36px;height:36px;border-radius:9999px;background:#e5e7eb;color:#374151;font-weight:600;display:flex;align-items:center;justify-content:center}
        .user-name{font-weight:500}
        .content{margin-top:12px}
        .toolbar{display:flex;align-items:center;justify-content:space-between;gap:12px;margin:14px 0}
        .search{display:flex;align-items:center;gap:8px;background:var(--surface);border:1px solid var(--border);border-radius:10px;padding:10px 12px;max-width:420px;width:100%;color:var(--muted-text)}
        .search input{border:0;outline:none;width:100%;background:transparent;color:inherit}
        .btn{padding:10px 14px;border-radius:10px;background:var(--surface);color:#374151;border:1px solid var(--border);cursor:pointer;font-weight:600;text-decoration:none}
        .btn.primary{background:linear-gradient(135deg,var(--primary),var(--primary-2));border:0;color:#fff}
        .btn.ghost{background:rgba(124,58,237,.08);color:#4f46e5;border:1px solid rgba(124,58,237,.25)}
        .card{background:var(--surface);border:1px solid var(--border);border-radius:12px}
        .table-header,.table-row{display:grid;grid-template-columns:1.6fr 1fr .9fr .6fr;gap:12px;align-items:center;padding:14px 16px}
        .table-header{font-size:12px;text-transform:uppercase;color:var(--muted-text);letter-spacing:.04em}
        .table-row+.table-row{border-top:1px solid var(--border)}
        .customer a{color:#4f46e5;font-weight:600;text-decoration:none}
        .customer small{display:block;color:#6b7280}
        .badge-date{display:inline-block;background:#E8F0FF;color:#1f3b77;border-radius:8px;padding:6px 10px;line-height:1.1;text-align:center}
        .actions-cell{display:flex;gap:10px;justify-content:flex-start}
        .icon-link{width:34px;height:34px;border-radius:8px;border:1px solid var(--border);display:flex;align-items:center;justify-content:center;text-decoration:none;color:#4f46e5;background:#f8fafc}
        .icon-link.danger{color:#ef4444;border-color:#fecaca;background:#fff7f7}
        @media (max-width: 980px){.layout{grid-template-columns:1fr}.sidebar{position:static;height:auto}.toolbar{flex-direction:column;align-items:stretch}.right .user-name{display:none}.table-header,.table-row{grid-template-columns:1.4fr .9fr .8fr .6fr}}
    </style>
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <div class="profile">
            <div class="avatar">P</div>
            <div class="brand">BGH POS</div>
            <button class="menu-btn" aria-label="Menu">≡</button>
        </div>
        <nav class="nav">
            <div class="nav-title">Menu</div>
            <a class="nav-item" href="#">Products</a>
            
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if(session('success'))
<script>
  Swal.fire({ icon: 'success', title: 'Success', text: {!! json_encode(session('success')) !!} });
</script>
@endif
</body>
</html>
