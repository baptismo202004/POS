<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }} - Reset Password</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root { --bg1:#5E60CE; --bg2:#7A5CF4; --bg3:#5DA8F2; --muted:rgba(255,255,255,.75); --card:rgba(255,255,255,.12); --card-border:rgba(255,255,255,.25); }
        *{box-sizing:border-box}
        html,body{height:100%}
        body{margin:0;font-family:Inter,system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif;color:#fff;
            background: #7FAAFF;
            display:flex;align-items:center;justify-content:center;padding:24px}
        .card{width:100%;max-width:560px;padding:40px;border-radius:18px;background:var(--card);
            backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);border:1px solid var(--card-border);
            box-shadow:0 20px 60px rgba(0,0,0,.25);min-height:460px}
        h2{text-align:center;margin:0 0 18px;font-weight:600;font-size:32px;line-height:1.2}
        p{margin:0 0 24px;text-align:center;color:var(--muted);font-size:14px;line-height:1.5}
        label{display:block;font-size:15px;margin-bottom:8px;color:var(--muted)}
        .input{width:100%;padding:16px 16px;border-radius:12px;background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.25);color:#fff;outline:none;font-size:16px}
        .input::placeholder{color:rgba(255,255,255,.7)}
        .input:focus{border-color:rgba(255,255,255,.6);box-shadow:0 0 0 3px rgba(255,255,255,.15)}
        .row{position:relative}
        .toggle{position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:0;color:rgba(255,255,255,.85);cursor:pointer;padding:4px;font-size:18px}
        .actions{display:flex;align-items:center;justify-content:space-between;margin-top:14px}
        .forgot{font-size:14px;color:var(--muted);text-decoration:none}
        .forgot:hover{color:#fff;text-decoration:underline}
        .btn{width:100%;padding:14px 16px;border-radius:12px;background:rgba(255,255,255,.25);color:#fff;font-weight:600;border:1px solid rgba(255,255,255,.3);cursor:pointer;font-size:16px}
        .btn:hover{background:rgba(255,255,255,.35)}
        .error{color:#ffe2e2;font-size:14px;margin-top:6px}
        .swal-toast{border-radius:12px !important;box-shadow:0 12px 40px rgba(2,6,23,.18) !important;padding:10px 14px !important;font-weight:600 !important}
        .swal2-icon{box-shadow:none !important}
    </style>
</head>
<body>
<div class="card">
    <div style="text-align:center;margin-bottom:12px">
        <img src="/images/BGH LOGO.png" alt="Logo" style="width:120px;max-width:100%;height:auto;object-fit:contain;display:inline-block;border-radius:8px">
    </div>

    <h2>Set New Password</h2>
    <p>Enter your email and a new password.</p>

    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div>
            <label for="email">Email</label>
            <input class="input" id="email" type="email" name="email" value="{{ old('email', $email) }}" required autofocus placeholder="Enter Email">
            @error('email')<div class="error">{{ $message }}</div>@enderror
        </div>

        <div style="height:12px"></div>

        <div>
            <label for="password">New Password</label>
            <div class="row">
                <input class="input" id="password" type="password" name="password" required placeholder="Enter New Password">
                <button type="button" class="toggle" id="togglePassword" aria-label="Toggle password visibility">👁️</button>
            </div>
            @error('password')<div class="error">{{ $message }}</div>@enderror
        </div>

        <div style="height:12px"></div>

        <div>
            <label for="password_confirmation">Confirm Password</label>
            <div class="row">
                <input class="input" id="password_confirmation" type="password" name="password_confirmation" required placeholder="Confirm Password">
                <button type="button" class="toggle" id="togglePassword2" aria-label="Toggle password confirmation visibility">👁️</button>
            </div>
        </div>

        <div style="height:12px"></div>
        <button type="submit" class="btn">RESET PASSWORD</button>

        <div class="actions">
            <div></div>
            <a class="forgot" href="{{ route('login') }}">Back to login</a>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
(function(){
 var btn=document.getElementById('togglePassword');
 var input=document.getElementById('password');
 if(btn&&input){
    btn.addEventListener('click',function(){
     var isPw=input.getAttribute('type')==='password';
     input.setAttribute('type',isPw?'text':'password');
     btn.textContent = isPw ? '🙈' : '👁️';
    });
 }
})();

(function(){
 var btn=document.getElementById('togglePassword2');
 var input=document.getElementById('password_confirmation');
 if(btn&&input){
    btn.addEventListener('click',function(){
     var isPw=input.getAttribute('type')==='password';
     input.setAttribute('type',isPw?'text':'password');
     btn.textContent = isPw ? '🙈' : '👁️';
    });
 }
})();

const Toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 2200,
  timerProgressBar: true,
  customClass: { popup: 'swal-toast' }
});

@if(session('error'))
    Toast.fire({ icon: 'error', title: {!! json_encode(session('error')) !!}, background: 'linear-gradient(90deg,#fff1f0,#ffdce0)', color: '#7f1d1d', iconColor: '#ef4444' });
@endif

@if(session('success'))
    Toast.fire({ icon: 'success', title: {!! json_encode(session('success')) !!}, background: 'linear-gradient(90deg,#ecfdf5,#d1fae5)', color: '#065f46', iconColor: '#10b981' });
@endif
</script>
</body>
</html>
