<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }} - Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root { --bg1:#5E60CE; --bg2:#7A5CF4; --bg3:#5DA8F2; --muted:rgba(255,255,255,.75); --card:rgba(255,255,255,.12); --card-border:rgba(255,255,255,.25); }
        *{box-sizing:border-box}
        html,body{height:100%}
        body{margin:0;font-family:Inter,system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif;color:#fff;
            background: radial-gradient(1200px 800px at 10% -10%, rgba(255,255,255,.08), transparent 50%),
                        linear-gradient(135deg, var(--bg1), var(--bg2) 50%, var(--bg3));
            display:flex;align-items:center;justify-content:center;padding:24px}
        .card{width:100%;max-width:560px;padding:40px;border-radius:18px;background:var(--card);
            backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);border:1px solid var(--card-border);
            box-shadow:0 20px 60px rgba(0,0,0,.25);min-height:460px}
        .logo{width:56px;height:56px;border-radius:9999px;background:rgba(255,255,255,.3);display:flex;align-items:center;justify-content:center;
            font-size:14px;font-weight:600;margin:0 auto 12px}
        h1{margin:0 0 24px;text-align:center;font-weight:600;font-size:32px;line-height:1.2}
        h3{text-align:center}
        label{display:block;font-size:15px;margin-bottom:8px;color:var(--muted)}
        .input{width:100%;padding:16px 16px;border-radius:12px;background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.25);color:#fff;outline:none;font-size:16px}
        .input::placeholder{color:rgba(255,255,255,.7)}
        .input:focus{border-color:rgba(255,255,255,.6);box-shadow:0 0 0 3px rgba(255,255,255,.15)}
        .row{position:relative}
        .toggle{position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:0;color:rgba(255,255,255,.85);cursor:pointer;padding:4px;font-size:18px}
        .actions{display:flex;align-items:center;justify-content:space-between;margin-top:12px;margin-bottom:16px}
        .forgot{font-size:14px;color:var(--muted);text-decoration:none}
        .forgot:hover{color:#fff;text-decoration:underline}
        .btn{width:100%;padding:14px 16px;border-radius:12px;background:rgba(255,255,255,.25);color:#fff;font-weight:600;border:1px solid rgba(255,255,255,.3);cursor:pointer;font-size:16px}
        .btn:hover{background:rgba(255,255,255,.35)}
        .error{color:#ffe2e2;font-size:14px;margin-top:6px}
    </style>
</head>
<body>
<div class="card">
    <h3>POS System</h3>
    <h1>Sign In</h1>
    <form method="POST" action="{{ route('login.post') }}">
        @csrf
        <div>
            <label for="email">Email</label>
            <input class="input" id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="Enter Email">
            @error('email')<div class="error">{{ $message }}</div>@enderror
        </div>
        <div class="actions">
            <label for="password" style="margin:0;color:var(--muted)">Password</label>
            @if (Route::has('password.request'))
                <a class="forgot" href="{{ route('password.request') }}">Forgot Password ?</a>
            @endif
        </div>
        <div class="row">
            <input class="input" id="password" type="password" name="password" required placeholder="Enter Password">
            <button type="button" class="toggle" id="togglePassword" aria-label="Toggle password visibility">👁️</button>
        </div>
        @error('password')<div class="error">{{ $message }}</div>@enderror
        <div style="height:12px"></div>
        <button type="submit" class="btn">LOGIN</button>
    </form>
</div>
<script>
(function(){
 var btn=document.getElementById('togglePassword');
 var input=document.getElementById('password');
 if(btn&&input){
  btn.addEventListener('click',function(){
   var isPw=input.getAttribute('type')==='password';
   input.setAttribute('type',isPw?'text':'password');
  });
 }
})();
</script>
</body>
</html>
