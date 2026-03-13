<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }} - Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --navy: #0A2A6E;
            --blue: #1565C0;
            --lt:   #42A5F5;
            --cyan: #00E5FF;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; overflow: hidden; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: #030d1f;
            color: #fff;
        }

        .scene { position: fixed; inset: 0; z-index: 0; overflow: hidden; }
        .scene-bg {
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse 160% 110% at 50% 130%, #0D47A1 0%, #061535 50%, #020b1c 100%);
        }

        .aurora { position: absolute; inset: 0; pointer-events: none; overflow: hidden; }
        .ab {
            position: absolute;
            left: -20%;
            width: 140%;
            height: 40%;
            border-radius: 50%;
            filter: blur(55px);
            mix-blend-mode: screen;
            animation: auroraDrift linear infinite;
        }
        .ab1 { top:-8%; background:linear-gradient(180deg,rgba(0,100,255,.30),transparent); animation-duration:22s; }
        .ab2 { top:4%;  background:linear-gradient(180deg,rgba(0,200,255,.18),transparent); animation-duration:28s; animation-delay:-8s; }
        .ab3 { top:-4%; background:linear-gradient(180deg,rgba(80,0,255,.10),transparent);  animation-duration:35s; animation-delay:-14s; }
        @keyframes auroraDrift {
            0%  {transform:translateX(0%)  scaleY(1);   opacity:.7}
            25% {transform:translateX(8%)  scaleY(1.35);opacity:1}
            50% {transform:translateX(3%)  scaleY(.8);  opacity:.55}
            75% {transform:translateX(-6%) scaleY(1.2); opacity:.9}
            100%{transform:translateX(0%)  scaleY(1);   opacity:.7}
        }

        .grid {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(66,165,245,.06) 1px,transparent 1px),
                linear-gradient(90deg,rgba(66,165,245,.06) 1px,transparent 1px);
            background-size: 56px 56px;
            mask-image: radial-gradient(ellipse 85% 85% at 50% 50%,black 0%,transparent 72%);
            animation: gridMove 20s linear infinite;
        }
        @keyframes gridMove { from{background-position:0 0} to{background-position:56px 56px} }

        .orb { position:absolute; border-radius:50%; filter:blur(72px); pointer-events:none; mix-blend-mode:screen; }
        .o1  { width:680px;height:680px;background:radial-gradient(circle,rgba(21,101,192,.50),transparent 65%);top:-220px;left:-220px; animation:o1 16s ease-in-out infinite; }
        .o2  { width:520px;height:520px;background:radial-gradient(circle,rgba(0,176,255,.35),transparent 65%);bottom:-160px;right:-120px; animation:o2 19s ease-in-out infinite; }
        .o3  { width:320px;height:320px;background:radial-gradient(circle,rgba(0,229,255,.22),transparent 65%);top:38%;left:58%; animation:o3 12s ease-in-out infinite; }
        .o4  { width:200px;height:200px;background:radial-gradient(circle,rgba(0,180,255,.30),transparent 65%);top:55%;left:12%; animation:o4 9s ease-in-out infinite; }
        @keyframes o1{0%,100%{transform:translate(0,0) scale(1)} 33%{transform:translate(50px,35px) scale(1.08)} 66%{transform:translate(20px,-25px) scale(.93)}}
        @keyframes o2{0%,100%{transform:translate(0,0) scale(1)} 40%{transform:translate(-40px,-50px) scale(1.12)} 70%{transform:translate(20px,20px) scale(.95)}}
        @keyframes o3{0%,100%{transform:translate(0,0)} 50%{transform:translate(-30px,25px) scale(1.15)}}
        @keyframes o4{0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(22px,-18px) scale(1.2)}}

        .rings { position:absolute; inset:0; pointer-events:none; display:flex; align-items:center; justify-content:center; }
        .ring  { position:absolute; border-radius:50%; border:1px solid rgba(0,229,255,.11); animation:ringExpand ease-out infinite; }
        .ring:nth-child(1){width:180px;height:180px;animation-duration:4s;animation-delay:0s}
        .ring:nth-child(2){width:180px;height:180px;animation-duration:4s;animation-delay:1.3s}
        .ring:nth-child(3){width:180px;height:180px;animation-duration:4s;animation-delay:2.6s}
        @keyframes ringExpand{0%{transform:scale(.8);opacity:.7}100%{transform:scale(5.5);opacity:0}}

        .scanline {
            position:absolute;
            inset:0;
            pointer-events:none;
            background:repeating-linear-gradient(0deg,transparent 0,transparent 3px,rgba(0,0,0,.04) 3px,rgba(0,0,0,.04) 4px);
            animation:scanMove 8s linear infinite;
        }
        @keyframes scanMove{from{background-position:0 0}to{background-position:0 100px}}

        .shimmer {
            position:absolute;
            inset:0;
            pointer-events:none;
            background:linear-gradient(108deg,transparent 38%,rgba(255,255,255,.026) 50%,transparent 62%);
            animation:sweep 7s ease-in-out infinite;
        }
        @keyframes sweep{0%,100%{transform:translateX(-120%)} 50%{transform:translateX(120%)}}

        .stars-layer { position:absolute; inset:0; pointer-events:none; }
        .star { position:absolute; border-radius:50%; background:rgba(255,255,255,.85); box-shadow:0 0 4px 1px rgba(0,229,255,.45); }
        .shoot { position:absolute; height:1.5px; border-radius:2px; background:linear-gradient(90deg,rgba(0,229,255,.9),transparent); animation:shoot linear infinite; }
        @keyframes shoot{0%{transform:translate(0,0);opacity:0}5%{opacity:1}80%{opacity:.4}100%{transform:translate(700px,200px);opacity:0}}
        @keyframes starTwinkle{0%,100%{opacity:.2;transform:scale(.8)} 50%{opacity:1;transform:scale(1.3)}}

        .particles { position:absolute; inset:0; pointer-events:none; }
        .p { position:absolute; border-radius:50%; animation:floatUp linear infinite; }
        @keyframes floatUp{
            0%  {transform:translateY(105vh) translateX(0) scale(0);opacity:0}
            8%  {opacity:1}
            50% {transform:translateY(50vh) translateX(var(--drift)) scale(1)}
            92% {opacity:.5}
            100%{transform:translateY(-8vh) translateX(var(--drift)) scale(.4);opacity:0}
        }

        .cursor-glow {
            position:fixed;
            pointer-events:none;
            z-index:999;
            width:320px;
            height:320px;
            border-radius:50%;
            background:radial-gradient(circle,rgba(0,150,255,.09),transparent 65%);
            transform:translate(-50%,-50%);
            transition:opacity .3s;
        }

        .card-wrap {
            position:relative;
            z-index:10;
            width:100%;
            max-width:560px;
            padding:0 20px;
            animation:cardIn .8s cubic-bezier(.34,1.45,.64,1) both;
        }
        @keyframes cardIn{
            from{opacity:0;transform:translateY(44px) scale(.93) rotateX(6deg)}
            to  {opacity:1;transform:translateY(0)    scale(1)   rotateX(0deg)}
        }

        .card {
            background:rgba(255,255,255,.065);
            backdrop-filter:blur(28px);
            -webkit-backdrop-filter:blur(28px);
            border:1px solid rgba(255,255,255,.13);
            border-radius:30px;
            padding:46px 42px 42px;
            box-shadow:
                0 0 0 1px rgba(0,229,255,.07),
                0 40px 90px rgba(0,0,0,.55),
                inset 0 1px 0 rgba(255,255,255,.13),
                inset 0 -1px 0 rgba(0,229,255,.06);
            position:relative;
            overflow:hidden;
            transform-style:preserve-3d;
            transition:box-shadow .4s ease;
        }
        .card:hover {
            box-shadow:
                0 0 0 1px rgba(0,229,255,.16),
                0 50px 100px rgba(0,0,0,.60),
                inset 0 1px 0 rgba(255,255,255,.16),
                inset 0 -1px 0 rgba(0,229,255,.10);
        }

        .card::before {
            content:'';
            position:absolute;
            top:0;
            left:15%;
            right:15%;
            height:1px;
            background:linear-gradient(90deg,transparent,rgba(0,229,255,.85),transparent);
            animation:topGlowPulse 3s ease-in-out infinite;
        }
        @keyframes topGlowPulse{0%,100%{opacity:.55}50%{opacity:1}}

        .card::after {
            content:'';
            position:absolute;
            top:-55%;
            left:-25%;
            width:150%;
            height:55%;
            background:radial-gradient(ellipse,rgba(66,165,245,.09) 0%,transparent 70%);
            pointer-events:none;
            animation:innerGlow 6s ease-in-out infinite;
        }
        @keyframes innerGlow{0%,100%{opacity:.5;transform:scaleX(1)}50%{opacity:1;transform:scaleX(1.1)}}

        .cc { position:absolute; width:22px; height:22px; pointer-events:none; animation:ccPulse 4s ease-in-out infinite; }
        .cc.tl{top:14px;left:14px; border-top:2px solid rgba(0,229,255,.5);border-left:2px solid rgba(0,229,255,.5);border-radius:5px 0 0 0;}
        .cc.tr{top:14px;right:14px;border-top:2px solid rgba(0,229,255,.5);border-right:2px solid rgba(0,229,255,.5);border-radius:0 5px 0 0;animation-delay:1s;}
        .cc.bl{bottom:14px;left:14px;border-bottom:2px solid rgba(0,229,255,.5);border-left:2px solid rgba(0,229,255,.5);border-radius:0 0 0 5px;animation-delay:2s;}
        .cc.br{bottom:14px;right:14px;border-bottom:2px solid rgba(0,229,255,.5);border-right:2px solid rgba(0,229,255,.5);border-radius:0 0 5px 0;animation-delay:3s;}
        @keyframes ccPulse{0%,100%{opacity:.4}50%{opacity:1}}

        .logo-wrap {
            text-align:center;
            margin-bottom:10px;
            animation:logoIn .7s .1s cubic-bezier(.34,1.56,.64,1) both;
            position: relative;
            z-index: 1;
        }
        @keyframes logoIn{from{opacity:0;transform:scale(.5) rotate(-12deg)}to{opacity:1;transform:scale(1) rotate(0deg)}}
        .logo-wrap img {
            width:96px;
            height:auto;
            object-fit:contain;
            border-radius:12px;
            filter:drop-shadow(0 4px 18px rgba(0,180,255,.42));
            animation:logoFloat 4s ease-in-out infinite;
        }
        @keyframes logoFloat{
            0%,100%{transform:translateY(0);filter:drop-shadow(0 4px 18px rgba(0,180,255,.42))}
            50%    {transform:translateY(-6px);filter:drop-shadow(0 12px 30px rgba(0,229,255,.60))}
        }
        .logo-fallback {
            width:64px;
            height:64px;
            border-radius:18px;
            margin:0 auto;
            background:linear-gradient(135deg,var(--navy),var(--lt));
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:24px;
            color:#fff;
            box-shadow:0 8px 28px rgba(0,100,200,.4);
            animation:logoFloat 4s ease-in-out infinite;
        }

        .card-title {
            font-family:'Nunito',sans-serif;
            text-align:center;
            margin-bottom:4px;
            animation:fadeUp .5s .32s ease both;
            position: relative;
            z-index: 1;
        }
        .card-title h1 {
            font-size:29px;
            font-weight:900;
            color:#fff;
            line-height:1.1;
            position:relative;
            display:inline-block;
        }
        .card-title h1::after {
            content:'';
            position:absolute;
            bottom:-4px;
            left:0;
            width:0;
            height:2px;
            background:linear-gradient(90deg,var(--cyan),var(--lt));
            border-radius:2px;
            animation:underlineGrow .8s .9s ease forwards;
        }
        @keyframes underlineGrow{to{width:100%}}

        .card-subtitle {
            text-align:center;
            font-size:12.5px;
            color:rgba(255,255,255,.40);
            margin-bottom:28px;
            animation:fadeUp .5s .40s ease both;
            min-height:18px;
            position: relative;
            z-index: 1;
        }

        .divider {
            height:1px;
            margin-bottom:24px;
            position:relative;
            overflow:visible;
            animation:fadeUp .5s .44s ease both;
            z-index: 1;
        }
        .divider::before {
            content:'';
            position:absolute;
            inset:0;
            background:linear-gradient(90deg,transparent,rgba(66,165,245,.28),transparent);
        }
        .divider-dot {
            position:absolute;
            top:50%;
            left:50%;
            transform:translate(-50%,-50%);
            width:7px;
            height:7px;
            border-radius:50%;
            background:var(--cyan);
            box-shadow:0 0 10px 3px rgba(0,229,255,.65);
            animation:dotBlink 2s ease-in-out infinite;
        }
        @keyframes dotBlink{0%,100%{opacity:.35;transform:translate(-50%,-50%) scale(.65)}50%{opacity:1;transform:translate(-50%,-50%) scale(1.3)}}

        .f-group { margin-bottom:18px; animation:fadeUp .5s ease both; position: relative; z-index: 1; }
        .f-group:nth-child(1){animation-delay:.50s}
        .f-group:nth-child(2){animation-delay:.58s}

        .f-label { display:flex; align-items:center; justify-content:space-between; margin-bottom:8px; }
        .f-label span { font-size:11.5px; font-weight:700; color:rgba(255,255,255,.52); letter-spacing:.05em; text-transform:uppercase; }

        .forgot { font-size:11.5px; color:rgba(0,229,255,.70); text-decoration:none; font-weight:700; transition:color .18s,text-shadow .18s; }
        .forgot:hover { color:var(--cyan); text-shadow:0 0 10px rgba(0,229,255,.5); }

        .f-wrap { position:relative; }

        .f-input {
            width:100%;
            padding:13px 48px 13px 46px;
            border-radius:14px;
            background:rgba(255,255,255,.07);
            border:1.5px solid rgba(255,255,255,.11);
            color:#fff;
            font-size:14px;
            font-family:'Plus Jakarta Sans',sans-serif;
            outline:none;
            transition:border-color .25s,background .25s,box-shadow .25s,transform .18s;
        }
        .f-input::placeholder { color:rgba(255,255,255,.27); }
        .f-input:hover:not(:focus) { border-color:rgba(255,255,255,.22); background:rgba(255,255,255,.09); }
        .f-input:focus {
            border-color:rgba(0,229,255,.58);
            background:rgba(255,255,255,.10);
            box-shadow:0 0 0 4px rgba(0,229,255,.10),0 4px 22px rgba(0,100,200,.18);
            transform:translateY(-1px);
        }

        .f-icon {
            position:absolute;
            left:16px;
            top:50%;
            transform:translateY(-50%);
            color:rgba(255,255,255,.28);
            font-size:14px;
            pointer-events:none;
            transition:color .25s,text-shadow .25s;
        }
        .f-wrap:focus-within .f-icon { color:rgba(0,229,255,.85); text-shadow:0 0 10px rgba(0,229,255,.5); }

        .f-glow {
            position:absolute;
            inset:0;
            border-radius:14px;
            pointer-events:none;
            opacity:0;
            transition:opacity .25s;
            box-shadow:0 0 18px 2px rgba(0,229,255,.18);
        }
        .f-wrap:focus-within .f-glow { opacity:1; }

        .toggle-pw {
            position:absolute;
            right:14px;
            top:50%;
            transform:translateY(-50%);
            background:none;
            border:none;
            color:rgba(255,255,255,.34);
            cursor:pointer;
            padding:4px;
            font-size:14px;
            transition:color .18s,text-shadow .18s;
        }
        .toggle-pw:hover { color:var(--cyan); text-shadow:0 0 8px rgba(0,229,255,.45); }

        .btn-wrap { margin-top:10px; animation:fadeUp .5s .66s ease both; position: relative; z-index: 1; }
        .btn-login {
            width:100%;
            padding:15px;
            border-radius:14px;
            border:none;
            cursor:pointer;
            font-family:'Nunito',sans-serif;
            font-size:15px;
            font-weight:900;
            letter-spacing:.10em;
            text-transform:uppercase;
            color:#062050;
            background:linear-gradient(135deg,#00E5FF 0%,#42A5F5 45%,#00B8D4 100%);
            background-size:200% 200%;
            box-shadow:0 6px 28px rgba(0,180,255,.42),inset 0 1px 0 rgba(255,255,255,.32),inset 0 -1px 0 rgba(0,0,0,.14);
            transition:all .28s cubic-bezier(.34,1.56,.64,1);
            animation:btnGlow 3s ease-in-out infinite;
            position:relative;
            overflow:hidden;
        }
        .btn-login::before {
            content:'';
            position:absolute;
            top:0;
            left:-80%;
            width:60%;
            height:100%;
            background:linear-gradient(105deg,transparent,rgba(255,255,255,.30),transparent);
            transform:skewX(-20deg);
            transition:left .55s ease;
        }
        .btn-login:hover::before { left:120%; }
        .btn-login:hover { transform:translateY(-3px) scale(1.01); box-shadow:0 16px 44px rgba(0,180,255,.58),inset 0 1px 0 rgba(255,255,255,.32); }
        .btn-login:active { transform:scale(.97); }
        .btn-login:disabled { opacity:.65; cursor:not-allowed; transform:none; animation:none; }
        @keyframes btnGlow{
            0%,100%{box-shadow:0 6px 28px rgba(0,180,255,.42),inset 0 1px 0 rgba(255,255,255,.32)}
            50%    {box-shadow:0 8px 44px rgba(0,229,255,.68),inset 0 1px 0 rgba(255,255,255,.32)}
        }

        .f-error {
            font-size: 11.5px;
            color: #fca5a5;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .f-error i { font-size: 10px; }

        .card-footer {
            text-align:center;
            margin-top:22px;
            font-size:11px;
            color:rgba(255,255,255,.20);
            animation:fadeUp .5s .74s ease both;
            letter-spacing:.04em;
            position: relative;
            z-index: 1;
        }

        @keyframes fadeUp{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)}}
        @keyframes rippleAnim{to{transform:scale(1);opacity:0}}

        /* SweetAlert toast custom styling */
        .swal-toast{border-radius:12px !important;box-shadow:0 12px 40px rgba(2,6,23,.18) !important;padding:10px 14px !important;font-weight:600 !important}
        .swal2-icon{box-shadow:none !important}
    </style>
</head>
<body>

<div class="cursor-glow" id="cursorGlow"></div>

<div class="scene">
    <div class="scene-bg"></div>
    <div class="aurora">
        <div class="ab ab1"></div>
        <div class="ab ab2"></div>
        <div class="ab ab3"></div>
    </div>
    <div class="grid"></div>
    <div class="orb o1"></div>
    <div class="orb o2"></div>
    <div class="orb o3"></div>
    <div class="orb o4"></div>
    <div class="rings"><div class="ring"></div><div class="ring"></div><div class="ring"></div></div>
    <div class="scanline"></div>
    <div class="shimmer"></div>
    <div class="stars-layer" id="starsLayer"></div>
    <div class="particles" id="particles"></div>
</div>

<div class="card-wrap">
    <div class="card" id="card">
        <div class="cc tl"></div><div class="cc tr"></div>
        <div class="cc bl"></div><div class="cc br"></div>

        <div class="logo-wrap">
            <img src="/images/BGH LOGO.png" alt="BGH Logo" onerror="this.style.display='none';document.getElementById('lf').style.display='flex';">
            <div class="logo-fallback" id="lf" style="display:none;"><i class="fas fa-store"></i></div>
        </div>

        <div class="card-title">
            <h1>Sign In</h1>
        </div>
        <p class="card-subtitle" id="subtitle"></p>

        <div class="divider"><div class="divider-dot"></div></div>

        <form method="POST" action="{{ route('login.post') }}" id="loginForm">
            @csrf

            <div class="f-group">
                <div class="f-label"><span>Email Address</span></div>
                <div class="f-wrap">
                    <input class="f-input" id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="Enter your email">
                    <i class="fas fa-envelope f-icon"></i>
                    <div class="f-glow"></div>
                </div>
                @error('email')
                    <div class="f-error"><i class="fas fa-exclamation-circle"></i>{{ $message }}</div>
                @enderror
            </div>

            <div class="f-group">
                <div class="f-label">
                    <span>Password</span>
                    <a class="forgot" href="{{ Route::has('password.request') ? route('password.request') : url('/password/reset') }}">Forgot Password ?</a>
                </div>
                <div class="f-wrap">
                    <input class="f-input" id="password" type="password" name="password" required placeholder="Enter your password">
                    <i class="fas fa-lock f-icon"></i>
                    <div class="f-glow"></div>
                    <button type="button" class="toggle-pw" id="togglePassword" aria-label="Toggle password"><i class="fas fa-eye" id="eyeIcon"></i></button>
                </div>
                @error('password')
                    <div class="f-error"><i class="fas fa-exclamation-circle"></i>{{ $message }}</div>
                @enderror
            </div>

            <div class="btn-wrap">
                <button type="submit" class="btn-login" id="submitBtn">
                    <i class="fas fa-sign-in-alt" style="margin-right:9px;"></i> LOGIN
                </button>
            </div>
        </form>

        <div class="card-footer">&copy; {{ date('Y') }} BGH Solutions &mdash;</div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
/* cursor glow */
(function(){
  var cg = document.getElementById('cursorGlow');
  if(!cg) return;
  document.addEventListener('mousemove', function(e){
    cg.style.left = e.clientX + 'px';
    cg.style.top  = e.clientY + 'px';
  });
})();

/* 3D card tilt */
(function(){
  var card = document.getElementById('card');
  if(!card) return;
  document.addEventListener('mousemove', function(e){
    var r = card.getBoundingClientRect();
    var dx = (e.clientX - r.left - r.width/2) / (r.width/2);
    var dy = (e.clientY - r.top - r.height/2) / (r.height/2);
    card.style.transform = 'rotateY(' + (dx*7) + 'deg) rotateX(' + (-dy*7) + 'deg)';
    card.style.transition = 'transform .08s linear, box-shadow .4s';
  });
  document.addEventListener('mouseleave', function(){
    card.style.transform = 'rotateY(0) rotateX(0)';
    card.style.transition = 'transform .6s ease, box-shadow .4s';
  });
})();

/* Stars */
(function(){
  var layer = document.getElementById('starsLayer');
  if(!layer) return;
  for(var i=0;i<65;i++){
    var s=document.createElement('div');
    s.className='star';
    var sz=Math.random()*2.2+.7;
    s.style.width=sz+'px';
    s.style.height=sz+'px';
    s.style.left=(Math.random()*100)+'%';
    s.style.top=(Math.random()*100)+'%';
    s.style.opacity=(Math.random()*.7+.2);
    s.style.animation='starTwinkle '+(Math.random()*3+2)+'s ease-in-out '+(Math.random()*5)+'s infinite';
    layer.appendChild(s);
  }
  function spawnShoot(){
    var sh=document.createElement('div');
    sh.className='shoot';
    var len=Math.random()*140+60;
    sh.style.width=len+'px';
    sh.style.top=(Math.random()*55)+'%';
    sh.style.left=(Math.random()*45)+'%';
    sh.style.opacity='0';
    sh.style.animationDuration=(Math.random()*2+1.3)+'s';
    layer.appendChild(sh);
    setTimeout(function(){ sh.remove(); }, 3800);
  }
  spawnShoot();
  setInterval(function(){ spawnShoot(); }, Math.random()*2200+1400);
})();

/* Particles */
(function(){
  var c=document.getElementById('particles');
  if(!c) return;
  var colors=['rgba(0,229,255,.75)','rgba(66,165,245,.65)','rgba(0,176,255,.55)','rgba(255,255,255,.45)'];
  for(var i=0;i<36;i++){
    var p=document.createElement('div');
    p.className='p';
    var sz=Math.random()*3.5+1.2;
    var drift=(Math.random()-.5)*90;
    var col=colors[Math.floor(Math.random()*colors.length)];
    p.style.width=sz+'px';
    p.style.height=sz+'px';
    p.style.left=(Math.random()*100)+'%';
    p.style.background=col;
    p.style.setProperty('--drift', drift+'px');
    p.style.animationDuration=(Math.random()*14+8)+'s';
    p.style.animationDelay=(Math.random()*14)+'s';
    p.style.boxShadow='0 0 '+(sz*2)+'px '+col;
    c.appendChild(p);
  }
})();

/* Password toggle */
(function(){
  var btn=document.getElementById('togglePassword');
  var inp=document.getElementById('password');
  var ico=document.getElementById('eyeIcon');
  if(!btn||!inp) return;
  btn.addEventListener('click', function(){
    var isPw = inp.type === 'password';
    inp.type = isPw ? 'text' : 'password';
    if(ico) ico.className = isPw ? 'fas fa-eye-slash' : 'fas fa-eye';
  });
})();

/* Ripple on button */
(function(){
  var btn=document.getElementById('submitBtn');
  if(!btn) return;
  btn.addEventListener('click', function(e){
    var r = this.getBoundingClientRect();
    var sz = Math.max(r.width, r.height) * 2;
    var rip = document.createElement('span');
    rip.style.position = 'absolute';
    rip.style.borderRadius = '50%';
    rip.style.pointerEvents = 'none';
    rip.style.width = sz + 'px';
    rip.style.height = sz + 'px';
    rip.style.left = (e.clientX - r.left - sz/2) + 'px';
    rip.style.top  = (e.clientY - r.top  - sz/2) + 'px';
    rip.style.background = 'rgba(255,255,255,.18)';
    rip.style.transform = 'scale(0)';
    rip.style.animation = 'rippleAnim .55s ease-out forwards';
    this.appendChild(rip);
    setTimeout(function(){ rip.remove(); }, 600);
  });
})();

/* Submit loader */
(function(){
  var form = document.getElementById('loginForm');
  if(!form) return;
  form.addEventListener('submit', function(){
    var btn = document.getElementById('submitBtn');
    if(!btn) return;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-circle-notch fa-spin" style="margin-right:9px;"></i> Signing in...';
  });
})();

/* Typing effect */
(function(){
  var el = document.getElementById('subtitle');
  if(!el) return;
  var txt = 'Enter your credentials to continue';
  var i = 0;
  setTimeout(function(){
    var t = setInterval(function(){
      el.textContent += txt[i++];
      if(i >= txt.length) clearInterval(t);
    }, 38);
  }, 950);
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

@if(session('error'))
    Toast.fire({ icon: 'error', title: {!! json_encode(session('error')) !!}, background: 'linear-gradient(90deg,#fff1f0,#ffdce0)', color: '#7f1d1d', iconColor: '#ef4444' });
@endif

@if(session('success'))
    Toast.fire({ icon: 'success', title: {!! json_encode(session('success')) !!}, background: 'linear-gradient(90deg,#ecfdf5,#d1fae5)', color: '#065f46', iconColor: '#10b981' });
    // After showing a short success toast, redirect to dashboard and add a marker so the dashboard won't
    // show the same success message again.
    setTimeout(function(){ window.location.href = '{{ route('dashboard') }}?from=login'; }, 1400);
@endif
</script>
</body>
</html>
