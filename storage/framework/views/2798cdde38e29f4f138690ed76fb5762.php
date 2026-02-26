<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e(config('app.name', 'Laravel')); ?> - Login</title>
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
        .logo{width:56px;height:56px;border-radius:9999px;background:rgba(255,255,255,.3);display:flex;align-items:center;justify-content:center;
            font-size:14px;font-weight:600;margin:0 auto 12px}
        h3{margin:0 0 24px;text-align:center;font-weight:600;font-size:32px;line-height:1.2}
        h2{text-align:center}
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
        .alert-top{margin:0 auto 18px;max-width:480px;border-radius:12px;padding:12px 14px;font-weight:600;display:flex;gap:12px;align-items:center}
        .alert-error{background:linear-gradient(90deg,#ffefef,#ffdede);color:#7f1d1d;border:1px solid rgba(239,68,68,.12)}
        .alert-success{background:linear-gradient(90deg,#ecfdf5,#d1fae5);color:#065f46;border:1px solid rgba(16,185,129,.12)}
        /* SweetAlert toast custom styling */
        .swal-toast{border-radius:12px !important;box-shadow:0 12px 40px rgba(2,6,23,.18) !important;padding:10px 14px !important;font-weight:600 !important}
        .swal2-icon{box-shadow:none !important}
    </style>
</head>
<body>
<div class="card">
    
    <div style="text-align:center;margin-bottom:12px">
        <img src="/images/BGH LOGO.png" alt="Logo" style="width:120px;max-width:100%;height:auto;object-fit:contain;display:inline-block;border-radius:8px">
    </div>
    <h3>POS System</h3>
    <h2>Sign In</h2>
    <form method="POST" action="<?php echo e(route('login.post')); ?>">
        <?php echo csrf_field(); ?>
        <div>
            <label for="email">Email</label>
            <input class="input" id="email" type="email" name="email" value="<?php echo e(old('email')); ?>" required autofocus placeholder="Enter Email">
            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div class="actions">
            <label for="password" style="margin:0;color:var(--muted)">Password</label>
            <a class="forgot" href="<?php echo e(Route::has('password.request') ? route('password.request') : url('/password/reset')); ?>">Forgot Password ?</a>
        </div>
        <div class="row">
            <input class="input" id="password" type="password" name="password" required placeholder="Enter Password">
            <button type="button" class="toggle" id="togglePassword" aria-label="Toggle password visibility">👁️</button>
        </div>
        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        <div style="height:12px"></div>
        <button type="submit" class="btn">LOGIN</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// toggle password visibility
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

// Show flash messages using SweetAlert Toasts for a polished look
const Toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 2200,
  timerProgressBar: true,
  customClass: { popup: 'swal-toast' }
});

<?php if(session('error')): ?>
    Toast.fire({ icon: 'error', title: <?php echo json_encode(session('error')); ?>, background: 'linear-gradient(90deg,#fff1f0,#ffdce0)', color: '#7f1d1d', iconColor: '#ef4444' });
<?php endif; ?>

<?php if(session('success')): ?>
    Toast.fire({ icon: 'success', title: <?php echo json_encode(session('success')); ?>, background: 'linear-gradient(90deg,#ecfdf5,#d1fae5)', color: '#065f46', iconColor: '#10b981' });
    // After showing a short success toast, redirect to dashboard and add a marker so the dashboard won't
    // show the same success message again.
    setTimeout(function(){ window.location.href = '<?php echo e(route('dashboard')); ?>?from=login'; }, 1400);
<?php endif; ?>
</script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\POS\resources\views/login.blade.php ENDPATH**/ ?>