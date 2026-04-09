{{-- resources/views/auth/login.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Sign In — {{ config('app.name') }}</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500;600&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
:root {
  --bg:         #0b0e13;
  --surface:    #111620;
  --surface-2:  #181d28;
  --border:     #1e2535;
  --border-lit: #2a3448;
  --accent:     #3b82f6;
  --accent-dim: #1d4ed8;
  --accent-glow:#3b82f622;
  --green:      #10b981;
  --green-dim:  #052e16;
  --red:        #ef4444;
  --text:       #e2e8f0;
  --text-muted: #64748b;
  --text-dim:   #334155;
  --mono:       'IBM Plex Mono', monospace;
  --sans:       'Outfit', sans-serif;
}
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html, body { height: 100%; background: var(--bg); color: var(--text); font-family: var(--sans); }

/* Grid background */
body::before {
  content: '';
  position: fixed; inset: 0;
  background-image:
    linear-gradient(var(--border) 1px, transparent 1px),
    linear-gradient(90deg, var(--border) 1px, transparent 1px);
  background-size: 48px 48px;
  mask-image: radial-gradient(ellipse 80% 80% at 50% 50%, black 30%, transparent 100%);
  opacity: .35; pointer-events: none; z-index: 0;
}

.page-wrap {
  position: relative; z-index: 1;
  min-height: 100vh; display: flex;
  align-items: center; justify-content: center; padding: 24px;
}

/* ── Auth Card ── */
.auth-card {
  width: 100%; max-width: 440px;
  background: var(--surface);
  border: 1px solid var(--border-lit);
  border-radius: 20px;
  padding: 40px 40px 32px;
  box-shadow: 0 0 0 1px #ffffff04, 0 32px 64px #00000060;
  animation: cardIn .4s cubic-bezier(.22,.68,0,1.2) both;
}
@keyframes cardIn {
  from { opacity: 0; transform: translateY(20px) scale(.97); }
  to   { opacity: 1; transform: none; }
}

/* Brand */
.brand-row { display: flex; align-items: center; gap: 10px; margin-bottom: 30px; }
.brand-icon {
  width: 36px; height: 36px; background: var(--accent);
  border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem;
}
.brand-name { font-family: var(--mono); font-weight: 600; font-size: .9rem; letter-spacing: .04em; }
.brand-name span { color: var(--accent); }

/* Headings */
.card-title { font-size: 1.5rem; font-weight: 700; letter-spacing: -.02em; margin-bottom: 4px; }
.card-sub   { font-size: .82rem; color: var(--text-muted); margin-bottom: 26px; }
.card-sub a { color: var(--accent); text-decoration: none; font-weight: 500; }
.card-sub a:hover { color: #60a5fa; }

/* Alert */
.alert-box {
  display: none; padding: 10px 14px; border-radius: 10px;
  font-size: .82rem; margin-bottom: 18px; align-items: center; gap: 8px;
}
.alert-box.show { display: flex; }
.alert-error   { background: #450a0a; border: 1px solid #7f1d1d; color: #fca5a5; }
.alert-success { background: var(--green-dim); border: 1px solid #14532d; color: #86efac; }

/* Step transitions */
.step { display: none; }
.step.active { display: block; animation: fadeIn .28s ease both; }
@keyframes fadeIn { from { opacity:0; transform:translateX(10px); } to { opacity:1; transform:none; } }

/* Fields */
.field { margin-bottom: 15px; }
.field-label {
  display: block; font-family: var(--mono);
  font-size: .67rem; font-weight: 500; letter-spacing: .1em;
  text-transform: uppercase; color: var(--text-muted); margin-bottom: 6px;
}
.input-wrap { position: relative; }
.input-wrap input {
  width: 100%; background: var(--surface-2);
  border: 1px solid var(--border-lit); border-radius: 10px;
  padding: 11px 40px 11px 40px;
  font-family: var(--sans); font-size: .9rem; color: var(--text);
  outline: none; transition: border-color .2s, box-shadow .2s;
}
.input-wrap input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-glow); }
.input-wrap input::placeholder { color: var(--text-dim); }
.input-icon  { position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-muted); pointer-events:none; font-size:.9rem; }
.toggle-eye  { position:absolute; right:12px; top:50%; transform:translateY(-50%); color:var(--text-muted); cursor:pointer; font-size:.9rem; transition:color .2s; }
.toggle-eye:hover { color: var(--text); }
.field-error { font-size:.74rem; color:#fca5a5; margin-top:4px; }

.forgot-row { text-align:right; margin-top:-6px; margin-bottom:14px; }
.forgot-row a { font-size:.76rem; color:var(--text-muted); text-decoration:none; }
.forgot-row a:hover { color:var(--accent); }

/* 2FA panel */
.twofa-panel {
  background: var(--surface-2); border: 1px solid var(--border-lit);
  border-radius: 14px; padding: 20px; margin-bottom: 18px;
}
.badge-2fa {
  display:inline-block; font-family:var(--mono); font-size:.65rem; font-weight:600;
  color:var(--green); background:var(--green-dim); border:1px solid #065f46;
  border-radius:100px; padding:2px 9px; letter-spacing:.06em; margin-bottom:8px;
}
.twofa-hint { font-size:.78rem; color:var(--text-muted); margin-bottom:16px; line-height:1.55; }

/* OTP boxes */
.otp-row { display:flex; gap:8px; }
.otp-box {
      width: 20px;
  flex:1; aspect-ratio:1; text-align:center;
  font-family:var(--mono); font-size:1.25rem; font-weight:600;
  background:var(--surface); border:1px solid var(--border-lit);
  border-radius:10px; color:var(--text); outline:none;
  transition:border-color .2s, box-shadow .2s, background .2s;
}
.otp-box:focus { border-color:var(--accent); box-shadow:0 0 0 3px var(--accent-glow); background:var(--surface-2); }
.otp-box.filled { border-color:#065f46; background:#052e1630; }

.otp-timer { margin-top:10px; font-family:var(--mono); font-size:.72rem; color:var(--text-dim); text-align:right; }
.otp-timer .cd { color:var(--accent); }

/* 2FA QR frame — mirrors register page */
.qr-frame {
  width:176px; height:176px;
  border:2px dashed var(--border-lit); border-radius:14px;
  margin:0 auto 14px; display:flex; align-items:center; justify-content:center;
  background:var(--surface-2); overflow:hidden; position:relative;
}
.qr-frame img { width:100%; height:100%; object-fit:contain; border-radius:12px; }
.qr-placeholder-text { font-size:.78rem; color:var(--text-dim); text-align:center; line-height:1.6; }

.manual-secret {
  background:var(--surface-2); border:1px solid var(--border-lit); border-radius:8px;
  padding:10px 14px; font-family:var(--mono); font-size:.8rem;
  letter-spacing:.08em; color:var(--text); word-break:break-all;
  cursor:pointer; transition:background .2s; margin-bottom:6px;
}
.manual-secret:hover { background:var(--border); }

/* Button */
.btn-submit {
  width:100%; background:var(--accent); color:#fff; border:none;
  border-radius:10px; padding:13px;
  font-family:var(--sans); font-size:.95rem; font-weight:600;
  cursor:pointer; transition:background .2s, transform .1s, box-shadow .2s;
  position:relative; overflow:hidden;
}
.btn-submit::after { content:''; position:absolute; inset:0; background:linear-gradient(135deg,#ffffff15 0%,transparent 60%); pointer-events:none; }
.btn-submit:hover { background:var(--accent-dim); box-shadow:0 4px 20px #3b82f640; }
.btn-submit:active { transform:scale(.98); }
.btn-submit:disabled { background:var(--border-lit); color:var(--text-dim); cursor:not-allowed; box-shadow:none; }

/* Footer */
.card-footer-row {
  margin-top:18px; padding-top:16px;
  border-top:1px solid var(--border);
  display:flex; align-items:center; justify-content:space-between;
}
.sec-badge { display:flex; align-items:center; gap:6px; font-family:var(--mono); font-size:.67rem; color:var(--text-dim); }
.dot-g { width:6px; height:6px; border-radius:50%; background:var(--green); box-shadow:0 0 6px var(--green); }
.footer-link { font-size:.74rem; color:var(--text-dim); text-decoration:none; }
.footer-link:hover { color:var(--text-muted); }

@media (max-width:480px) {
  .auth-card { padding:26px 18px 22px; }
}
</style>
</head>
<body>
<div class="page-wrap">
<div class="auth-card">

  <div class="brand-row">
    <div class="brand-icon">🔐</div>
    <div class="brand-name">{{ config('app.name') }}<span>.</span></div>
  </div>

  <div class="alert-box alert-error" id="alertBox"><span>⚠</span><span id="alertMsg"></span></div>

  @if(session('status'))
  <div class="alert-box alert-success show"><span>✓</span><span>{{ session('status') }}</span></div>
  @endif

  {{-- ─────── STEP 1: Credentials ─────── --}}
  <div class="step active" id="step1">
    <h1 class="card-title">Welcome back</h1>
    <p class="card-sub">No account? <a href="{{ route('register') }}">Create one →</a></p>

    <div class="field">
      <label class="field-label">Email address</label>
      <div class="input-wrap">
        <span class="input-icon">✉</span>
        <input type="email" id="email" value="{{ old('email') }}"
               placeholder="you@example.com" autocomplete="email"/>
      </div>
      @error('email')<p class="field-error">{{ $message }}</p>@enderror
    </div>

    <div class="field">
      <label class="field-label">Password</label>
      <div class="input-wrap">
        <span class="input-icon">🔑</span>
        <input type="password" id="password" placeholder="Your password" autocomplete="current-password"/>
        <span class="toggle-eye" onclick="togglePwd('password',this)">👁</span>
      </div>
      @error('password')<p class="field-error">{{ $message }}</p>@enderror
    </div>

    <div class="forgot-row">
      <a href="{{ route('password.request') }}">Forgot password?</a>
    </div>

    <button class="btn-submit" id="credBtn" onclick="submitCredentials()">Sign In</button>
  </div>

  {{-- ─────── STEP 2: QR Scanner ─────── --}}
  {{--
    CONTROLLER — generate QR with bacon/bacon-qr-code and return as JSON (AJAX) or Blade var:
    ────────────────────────────────────────────────────────────────────────────────────────────
    use BaconQrCode\Renderer\Image\ImagickImageBackEnd;   // PNG path
    use BaconQrCode\Renderer\Image\SvgImageBackEnd;       // SVG path (recommended)
    use BaconQrCode\Renderer\ImageRenderer;
    use BaconQrCode\Renderer\RendererStyle\RendererStyle;
    use BaconQrCode\Writer;

    $otpauthUrl = 'otpauth://totp/'
        . rawurlencode(config('app.name') . ':' . $user->email)
        . '?secret=' . $user->two_factor_secret
        . '&issuer=' . rawurlencode(config('app.name'))
        . '&algorithm=SHA1&digits=6&period=30';

    // SVG (inline, no disk write)
    $renderer = new ImageRenderer(new RendererStyle(200), new SvgImageBackEnd());
    $qrSvg    = (new Writer($renderer))->writeString($otpauthUrl);

    // For AJAX response, base64-encode a PNG:
    // $renderer2 = new ImageRenderer(new RendererStyle(200), new ImagickImageBackEnd());
    // $png       = (new Writer($renderer2))->writeString($otpauthUrl);
    // return response()->json([
    //     'status'         => 'success',
    //     'requires_2fa'   => true,
    //     'temp_token'     => $tempToken,
    //     'qr_code_image'  => 'data:image/png;base64,' . base64_encode($png),
    //     'secret_key'     => $user->two_factor_secret,
    // ]);

    // For Blade render, pass SVG directly:
    // return view('auth.login', ['qrSvg' => $qrSvg, 'secretKey' => $user->two_factor_secret]);
    ────────────────────────────────────────────────────────────────────────────────────────────
  --}}
  <div class="step" id="step2">
    <h1 class="card-title">Scan QR Code</h1>
    <p class="card-sub">Use <strong style="color:var(--text)">Google</strong> or <strong style="color:var(--text)">Microsoft Authenticator</strong></p>

    <div style="text-align:center;">

      {{-- QR Frame: same structure as register page --}}
      <div class="qr-frame" id="qrFrame">
        @if(!empty($qrSvg))
          {{-- Blade path: SVG injected server-side by bacon/bacon-qr-code --}}
          {!! $qrSvg !!}
        @else
          {{-- AJAX path: JS will replace this with the base64 PNG after login response --}}
          <div class="qr-placeholder-text" id="qrPlaceholder">
            Loading<br/>QR code…
          </div>
        @endif
      </div>

      <p style="font-size:.72rem;color:var(--text-muted);margin-bottom:6px;">
        Can't scan? Copy the secret key below and enter it manually in the app:
      </p>

      {{-- Clickable secret key box (same as register) --}}
      <div class="manual-secret" id="secretBox" onclick="copySecret2fa()">
        {{ $secretKey ?? 'Loading…' }}
      </div>
      <p id="copyFeedback2fa" style="font-size:.72rem;color:var(--green);display:none;margin-bottom:10px;">✓ Copied to clipboard!</p>

      {{-- Copyable secret input (same as register) --}}
      <div style="position:relative;margin-bottom:6px;">
        <input
          type="text"
          id="secretInput2fa"
          readonly
          value="{{ $secretKey ?? '' }}"
          style="width:100%;background:var(--surface-2);border:1px solid var(--accent);border-radius:10px;padding:12px 50px 12px 14px;font-family:var(--mono);font-size:.88rem;letter-spacing:.1em;color:var(--accent);outline:none;cursor:pointer;"
          onclick="copySecretInput2fa()"
        />
        <span onclick="copySecretInput2fa()" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);cursor:pointer;font-size:1rem;" title="Copy">📋</span>
      </div>
      <p id="copyFeedback2faInput" style="font-size:.72rem;color:var(--green);margin-top:4px;display:none;">✓ Copied!</p>

      {{-- Step-by-step instructions --}}
      <div style="background:var(--surface-2);border:1px solid var(--border-lit);border-radius:10px;padding:14px 16px;text-align:left;margin-top:14px;">
        <ol style="padding-left:16px;margin:0;">
          <li style="font-size:.8rem;color:var(--text-muted);margin-bottom:6px;">Open your authenticator app and tap <strong style="color:var(--text)">+</strong></li>
          <li style="font-size:.8rem;color:var(--text-muted);margin-bottom:6px;">Select <strong style="color:var(--text)">Scan QR code</strong> and point at the code above</li>
          <li style="font-size:.8rem;color:var(--text-muted);margin-bottom:6px;">Or choose <strong style="color:var(--text)">Enter a setup key</strong> and paste the key above</li>
          <li style="font-size:.8rem;color:var(--text-muted);margin-bottom:0;">Once added, tap <strong style="color:var(--text)">Continue</strong> below and enter the 6-digit code</li>
        </ol>
      </div>

    </div>

    <button class="btn-submit" onclick="goStep(3);" style="margin-top:16px;margin-bottom:10px;">
      I've Scanned — Continue →
    </button>

    <div style="text-align:center;">
      <a onclick="backToStep1()" style="font-size:.76rem;color:var(--text-muted);text-decoration:none;cursor:pointer">
        ← Different account
      </a>
    </div>
  </div>

  {{-- ─────── STEP 3: 2FA OTP ─────── --}}
  <div class="step" id="step3">
    <h1 class="card-title">Two-factor auth</h1>
    <p class="card-sub">Open your authenticator app and enter the 6-digit code</p>

    <div class="twofa-panel">
      <span class="badge-2fa">2FA REQUIRED</span>
      <p class="twofa-hint">
        Enter the code from <strong style="color:var(--text)">Google</strong> or
        <strong style="color:var(--text)">Microsoft Authenticator</strong>.
        Codes refresh every 30 s.
      </p>
      <div class="otp-row">
        <input class="otp-box" type="text" maxlength="1" inputmode="numeric" autocomplete="one-time-code"/>
        <input class="otp-box" type="text" maxlength="1" inputmode="numeric"/>
        <input class="otp-box" type="text" maxlength="1" inputmode="numeric"/>
        <input class="otp-box" type="text" maxlength="1" inputmode="numeric"/>
        <input class="otp-box" type="text" maxlength="1" inputmode="numeric"/>
        <input class="otp-box" type="text" maxlength="1" inputmode="numeric"/>
      </div>
      <div class="otp-timer">Expires in <span class="cd" id="cd">30</span>s</div>
    </div>

    <button class="btn-submit" id="tfaBtn" onclick="submitTwoFA()" disabled>Verify Code</button>

    <div style="text-align:center;margin-top:12px">
      <a onclick="goStep(2)" style="font-size:.76rem;color:var(--text-muted);text-decoration:none;cursor:pointer;margin-right:14px;">
        ← Back to Scanner
      </a>
      <a onclick="backToStep1()" style="font-size:.76rem;color:var(--text-dim);text-decoration:none;cursor:pointer">
        Different account
      </a>
    </div>
  </div>

  <div class="card-footer-row">
    <div class="sec-badge"><span class="dot-g"></span> SSL Encrypted</div>
    <a href="#" class="footer-link">Privacy Policy</a>
  </div>

</div>
</div>

{{-- Fallback form for non-AJAX environments --}}
<form id="finalForm" action="{{ route('login.submit') }}" method="POST" style="display:none">
  @csrf
  <input type="hidden" name="email"           id="fEmail"/>
  <input type="hidden" name="password"        id="fPassword"/>
  <input type="hidden" name="two_factor_code" id="fCode"/>
  <input type="hidden" name="temp_token"      id="fTempToken"/>
</form>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// ── State ──
let tempToken = '';
let cdTimer   = null;

// ── OTP wiring ──
const boxes = document.querySelectorAll('.otp-box');
boxes.forEach((b, i) => {
  b.addEventListener('input', () => {
    b.value = b.value.replace(/\D/,'').slice(-1);
    b.classList.toggle('filled', !!b.value);
    if (b.value && i < boxes.length - 1) boxes[i+1].focus();
    document.getElementById('tfaBtn').disabled = ![...boxes].every(x => x.value);
  });
  b.addEventListener('keydown', e => {
    if (e.key === 'Backspace' && !b.value && i > 0) {
      boxes[i-1].value = ''; boxes[i-1].classList.remove('filled'); boxes[i-1].focus();
    }
  });
  b.addEventListener('paste', e => {
    e.preventDefault();
    const d = (e.clipboardData||window.clipboardData).getData('text').replace(/\D/g,'').slice(0,6);
    [...d].forEach((ch,idx) => { if(boxes[idx]){boxes[idx].value=ch;boxes[idx].classList.add('filled');} });
    document.getElementById('tfaBtn').disabled = d.length < 6;
  });
});

function getCode() { return [...boxes].map(b=>b.value).join(''); }

function startTimer() {
  let t = 30;
  document.getElementById('cd').textContent = t;
  clearInterval(cdTimer);
  cdTimer = setInterval(() => {
    document.getElementById('cd').textContent = --t;
    if (t <= 0) { clearInterval(cdTimer); showAlert('Code expired — please sign in again.'); backToStep1(); }
  }, 1000);
}

// ── Navigate ──
function goStep(n) {
  document.querySelectorAll('.step').forEach(s => s.classList.remove('active'));
  document.getElementById('step'+n).classList.add('active');
  // When entering OTP step, start the timer and focus first box
  if (n === 3) {
    startTimer();
    setTimeout(() => boxes[0].focus(), 100);
  }
}
function backToStep1() {
  clearInterval(cdTimer); tempToken = '';
  boxes.forEach(b => { b.value=''; b.classList.remove('filled'); });
  document.getElementById('tfaBtn').disabled = true;
  hideAlert(); goStep(1);
}

// ── Step 1: submit credentials ──
async function submitCredentials() {
  const email    = document.getElementById('email').value.trim();
  const password = document.getElementById('password').value;
  if (!email || !password) { showAlert('Enter your email and password.'); return; }

  const btn = document.getElementById('credBtn');
  btn.disabled = true; btn.textContent = 'Signing in…'; hideAlert();

  try {
    const res = await fetch('{{ route("login.submit") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept':        'application/json',
        'X-CSRF-TOKEN':  document.querySelector('meta[name="csrf-token"]').content,
      },
       credentials: 'include', 
      body: JSON.stringify({ email, password }),
    });
    const data = await res.json();

    console.log('Login response:', data); // debug

    if ((data.status === 'success' || data.status === '2fa_required') && data.requires_2fa === true) {
      // ✅ two_factor_enabled = 1 → OTP step
      tempToken = data.temp_token ?? '';
      document.getElementById('fEmail').value     = email;
      document.getElementById('fPassword').value  = password;
      document.getElementById('fTempToken').value = tempToken;

      // Populate QR code from API response — same logic as register page
      if (data.qr_code_image) {
        // Base64 PNG from controller (bacon/bacon-qr-code with ImagickImageBackEnd)
        document.getElementById('qrFrame').innerHTML =
          `<img src="${data.qr_code_image}" alt="QR Code" style="width:100%;height:100%;border-radius:12px;"/>`;
      } else if (data.qr_code_url) {
        // otpauth:// URL → external QR API fallback
        const encoded = encodeURIComponent(data.qr_code_url);
        document.getElementById('qrFrame').innerHTML =
          `<img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encoded}" alt="QR Code" style="width:100%;height:100%;border-radius:12px;"/>`;
      }
      if (data.secret_key) {
        document.getElementById('secretBox').textContent      = data.secret_key;
        document.getElementById('secretInput2fa').value       = data.secret_key;
      }

      goStep(2); // → QR Scanner step

    } else if (data.status === 'success' && !data.requires_2fa) {
      // ✅ two_factor_enabled = 0 → seedha dashboard
      window.location.href = data.redirect ?? '/dashboard';

    } else {
      showAlert(data.message || 'Invalid credentials.');
    }
  } catch {
    // Fallback: plain form POST
    document.getElementById('fEmail').value    = email;
    document.getElementById('fPassword').value = password;
    document.getElementById('finalForm').submit();
  } finally {
    btn.disabled = false; btn.textContent = 'Sign In';
  }
}

// ── Step 2: verify 2FA ──
async function submitTwoFA() {
  const code = getCode();
  const btn  = document.getElementById('tfaBtn');
  btn.disabled = true; 
  btn.textContent = 'Verifying…'; 
  hideAlert();

  try {
    const res = await fetch('{{ route("2fa.verify") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept':       'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Authorization': `Bearer ${tempToken}`,   // ← Send temp_token as Bearer
      },
      credentials: 'include',
      body: JSON.stringify({ code: code }),       // temp_token already in header
    });

    const data = await res.json();

    if (data.status === 'success') {
      clearInterval(cdTimer);
      btn.textContent = '✓ Verified!';
      
      if (data.access_token) {
        localStorage.setItem('access_token', data.access_token);
        window.accessToken = data.access_token;
      }
      
      window.location.href = data.redirect ?? '/dashboard';
    } else {
      showAlert(data.message || 'Invalid code. Try again.');
      boxes.forEach(b => { b.value = ''; b.classList.remove('filled'); });
      boxes[0].focus();
      btn.disabled = false;
      btn.textContent = 'Verify Code';
    }
  } catch (err) {
    console.error('Error:', err);
    showAlert('Something went wrong. Please try again.');
    btn.disabled = false;
    btn.textContent = 'Verify Code';
  }
}

// ── Copy secret helpers (step 2 QR page) ──
function copySecret2fa() {
  const text = document.getElementById('secretBox').textContent.trim();
  navigator.clipboard.writeText(text).then(() => {
    const fb = document.getElementById('copyFeedback2fa');
    fb.style.display = 'block';
    setTimeout(() => { fb.style.display = 'none'; }, 2000);
  });
}
function copySecretInput2fa() {
  const text = document.getElementById('secretInput2fa').value;
  navigator.clipboard.writeText(text).then(() => {
    const fb = document.getElementById('copyFeedback2faInput');
    fb.style.display = 'block';
    setTimeout(() => { fb.style.display = 'none'; }, 2000);
  });
}

// ── Helpers ──
function togglePwd(id, el) {
  const inp = document.getElementById(id);
  inp.type  = inp.type === 'password' ? 'text' : 'password';
  el.textContent = inp.type === 'password' ? '👁' : '🙈';
}
function showAlert(m) { document.getElementById('alertMsg').textContent = m; document.getElementById('alertBox').classList.add('show'); }
function hideAlert()   { document.getElementById('alertBox').classList.remove('show'); }

document.addEventListener('keydown', e => {
  if (e.key !== 'Enter') return;
  if (document.getElementById('step1').classList.contains('active')) submitCredentials();
  if (document.getElementById('step2').classList.contains('active')) { goStep(3); startTimer(); }
  if (document.getElementById('step3').classList.contains('active') && !document.getElementById('tfaBtn').disabled) submitTwoFA();
});

@if(session('2fa_required'))
  tempToken = "{{ session('temp_token','') }}";
  document.getElementById('fTempToken').value = tempToken;
  goStep(2); // show QR scanner first
@endif
</script>
</body>
</html>