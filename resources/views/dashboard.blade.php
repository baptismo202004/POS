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
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <div class="brand">
            <div class="logo">POS</div>
            <div>
                <h1 class="title">Dashboard</h1>
                <div class="muted">Welcome back</div>
            </div>
        </div>
        <div class="actions">
            <a class="btn" href="{{ url('/') }}">Login</a>
        </div>
    </div>

    <div class="grid" style="margin-bottom:16px">
        <div class="panel"><h3>Today's Sales</h3><div class="muted">Total revenue</div><div class="value">₱0.00</div></div>
        <div class="panel"><h3>Orders</h3><div class="muted">Completed orders</div><div class="value">0</div></div>
        <div class="panel"><h3>Items</h3><div class="muted">In inventory</div><div class="value">0</div></div>
        <div class="panel"><h3>Customers</h3><div class="muted">Active customers</div><div class="value">0</div></div>
    </div>

    <div class="row">
        <div class="panel">
            <h3>Recent Activity</h3>
            <div class="muted">No recent activity.</div>
        </div>
        <div class="panel">
            <h3>Shortcuts</h3>
            <div class="actions" style="margin-top:8px">
                <a class="btn" href="#">New Sale</a>
                <a class="btn" href="#">Add Item</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
