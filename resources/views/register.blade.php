{{-- resources/views/auth/register.blade.php --}}
{{--
  2FA FLOW (same as Google Account):
  ┌─────────────────────────────────────────────────────────────┐
  │  User fills register form                                   │
  │    ├─ "2-Step Verification" toggle OFF → register           │
  │    │    → two_factor_enabled = false → redirect /dashboard  │
  │    │                                                        │
  │    └─ "2-Step Verification" toggle ON  → register           │
  │         → Step 2: Scan QR code                             │
  │         → Step 3: Enter 6-digit OTP to confirm             │
  │         → two_factor_enabled = true → redirect /dashboard   │
  └─────────────────────────────────────────────────────────────┘
  Login (login.blade.php):
    - two_factor_enabled = false → direct /dashboard
    - two_factor_enabled = true  → ask OTP → /dashboard
--}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Create Account — {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500;600&family=Outfit:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --bg: #0b0e13;
            --surface: #111620;
            --surface-2: #181d28;
            --border: #1e2535;
            --border-lit: #2a3448;
            --accent: #3b82f6;
            --accent-dim: #1d4ed8;
            --accent-glow: #3b82f622;
            --green: #10b981;
            --green-dim: #052e16;
            --red: #ef4444;
            --text: #e2e8f0;
            --text-muted: #64748b;
            --text-dim: #334155;
            --mono: 'IBM Plex Mono', monospace;
            --sans: 'Outfit', sans-serif;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html,
        body {
            background: var(--bg);
            color: var(--text);
            font-family: var(--sans);
            min-height: 100%;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: linear-gradient(var(--border) 1px, transparent 1px), linear-gradient(90deg, var(--border) 1px, transparent 1px);
            background-size: 48px 48px;
            mask-image: radial-gradient(ellipse 80% 80% at 50% 50%, black 30%, transparent 100%);
            opacity: .35;
            pointer-events: none;
            z-index: 0;
        }

        .page-wrap {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 24px;
        }

        .auth-card {
            width: 100%;
            max-width: 480px;
            background: var(--surface);
            border: 1px solid var(--border-lit);
            border-radius: 20px;
            padding: 40px 40px 32px;
            box-shadow: 0 0 0 1px #ffffff04, 0 32px 64px #00000060;
            animation: cardIn .4s cubic-bezier(.22, .68, 0, 1.2) both;
        }

        @keyframes cardIn {
            from {
                opacity: 0;
                transform: translateY(20px) scale(.97);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        /* Brand */
        .brand-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 28px;
        }

        .brand-icon {
            width: 36px;
            height: 36px;
            background: var(--accent);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }

        .brand-name {
            font-family: var(--mono);
            font-weight: 600;
            font-size: .9rem;
            letter-spacing: .04em;
        }

        .brand-name span {
            color: var(--accent);
        }

        /* Progress bar — hidden by default, shown only on steps 2+3 */
        .prog-wrap {
            display: none;
            gap: 6px;
            margin-bottom: 28px;
        }

        .prog-wrap.show {
            display: flex;
        }

        .prog-seg {
            height: 3px;
            flex: 1;
            border-radius: 100px;
            background: var(--border-lit);
            transition: background .4s;
        }

        .prog-seg.done {
            background: var(--green);
        }

        .prog-seg.active {
            background: var(--accent);
        }

        /* Step label */
        .step-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-family: var(--mono);
            font-size: .68rem;
            color: var(--text-muted);
            letter-spacing: .05em;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .step-badge {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: var(--border-lit);
            color: var(--text-dim);
            font-size: .65rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .step-badge.active {
            background: var(--accent);
            color: #fff;
        }

        .step-badge.done {
            background: var(--green);
            color: #fff;
        }

        /* Headings */
        .card-title {
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: -.02em;
            margin-bottom: 4px;
        }

        .card-sub {
            font-size: .82rem;
            color: var(--text-muted);
            margin-bottom: 24px;
        }

        .card-sub a {
            color: var(--accent);
            text-decoration: none;
            font-weight: 500;
        }

        /* Alert */
        .alert-box {
            display: none;
            padding: 10px 14px;
            border-radius: 10px;
            font-size: .81rem;
            margin-bottom: 16px;
            align-items: center;
            gap: 8px;
        }

        .alert-box.show {
            display: flex;
        }

        .alert-error {
            background: #450a0a;
            border: 1px solid #7f1d1d;
            color: #fca5a5;
        }

        .alert-success {
            background: var(--green-dim);
            border: 1px solid #14532d;
            color: #86efac;
        }

        /* Steps */
        .step {
            display: none;
        }

        .step.active {
            display: block;
            animation: fadeIn .28s ease both;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateX(10px);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        /* Fields */
        .field {
            margin-bottom: 14px;
        }

        .field-label {
            display: block;
            font-family: var(--mono);
            font-size: .67rem;
            font-weight: 500;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 6px;
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap input {
            width: 100%;
            background: var(--surface-2);
            border: 1px solid var(--border-lit);
            border-radius: 10px;
            padding: 11px 40px 11px 40px;
            font-family: var(--sans);
            font-size: .88rem;
            color: var(--text);
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }

        .input-wrap input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--accent-glow);
        }

        .input-wrap input::placeholder {
            color: var(--text-dim);
        }

        .input-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            pointer-events: none;
            font-size: .88rem;
        }

        .toggle-eye {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            cursor: pointer;
            font-size: .88rem;
        }

        .toggle-eye:hover {
            color: var(--text);
        }

        .field-error {
            font-size: .73rem;
            color: #fca5a5;
            margin-top: 4px;
        }

        /* Strength */
        .strength-wrap {
            height: 3px;
            background: var(--border-lit);
            border-radius: 100px;
            margin-top: 7px;
            overflow: hidden;
        }

        .strength-fill {
            height: 100%;
            border-radius: 100px;
            width: 0;
            transition: width .4s, background .4s;
        }

        .req-row {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 7px;
        }

        .req-chip {
            font-size: .72rem;
            color: var(--text-dim);
            display: flex;
            align-items: center;
            gap: 4px;
            transition: color .3s;
        }

        .req-chip.ok {
            color: var(--green);
        }

        .req-dot {
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: currentColor;
            flex-shrink: 0;
        }

        /* Terms */
        .terms-row {
            display: flex;
            align-items: flex-start;
            gap: 9px;
            font-size: .8rem;
            color: var(--text-muted);
            margin-bottom: 4px;
        }

        .terms-row input[type="checkbox"] {
            margin-top: 2px;
            accent-color: var(--accent);
            flex-shrink: 0;
        }

        .terms-row a {
            color: var(--accent);
            text-decoration: none;
        }

        /* ── 2FA Toggle Row (Google-style) ─────────────────────── */
        .twofa-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-top: 16px;
            padding: 14px 16px;
            background: var(--surface-2);
            border: 1px solid var(--border-lit);
            border-radius: 12px;
            cursor: pointer;
            transition: border-color .25s, background .25s;
        }

        .twofa-row:hover {
            border-color: var(--accent);
        }

        .twofa-row.on {
            border-color: #065f46;
            background: #052e1630;
        }

        .twofa-row-info {
            flex: 1;
        }

        .twofa-row-title {
            font-size: .875rem;
            font-weight: 600;
            color: var(--text);
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 3px;
        }

        .status-pill {
            font-family: var(--mono);
            font-size: .6rem;
            font-weight: 600;
            letter-spacing: .05em;
            padding: 2px 8px;
            border-radius: 100px;
            transition: all .3s;
        }

        .pill-off {
            background: var(--border-lit);
            color: var(--text-dim);
            border: 1px solid var(--border);
        }

        .pill-on {
            background: var(--green-dim);
            color: var(--green);
            border: 1px solid #065f46;
        }

        .twofa-row-desc {
            font-size: .75rem;
            color: var(--text-muted);
            line-height: 1.4;
        }

        /* Toggle switch */
        .tog {
            position: relative;
            width: 40px;
            height: 22px;
            flex-shrink: 0;
        }

        .tog input {
            opacity: 0;
            width: 0;
            height: 0;
            position: absolute;
        }

        .tog-track {
            position: absolute;
            inset: 0;
            border-radius: 100px;
            background: var(--border-lit);
            cursor: pointer;
            transition: background .3s;
        }

        .tog-track::before {
            content: '';
            position: absolute;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: var(--text-dim);
            bottom: 3px;
            left: 3px;
            transition: transform .3s, background .3s;
        }

        .tog input:checked~.tog-track {
            background: var(--green);
        }

        .tog input:checked~.tog-track::before {
            transform: translateX(18px);
            background: #fff;
        }

        /* QR */
        .qr-frame {
            width: 172px;
            height: 172px;
            border: 2px dashed var(--border-lit);
            border-radius: 14px;
            margin: 0 auto 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--surface-2);
            overflow: hidden;
        }

        .qr-frame img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 12px;
        }

        .qr-ph {
            font-size: .78rem;
            color: var(--text-dim);
            text-align: center;
            line-height: 1.6;
        }

        .secret-copy-wrap {
            position: relative;
            margin-bottom: 6px;
        }

        .secret-copy-wrap input {
            width: 100%;
            background: var(--surface);
            border: 1px solid var(--accent);
            border-radius: 10px;
            padding: 11px 44px 11px 14px;
            font-family: var(--mono);
            font-size: .85rem;
            letter-spacing: .08em;
            color: var(--accent);
            outline: none;
            cursor: pointer;
        }

        .copy-btn {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: .95rem;
        }

        .copy-ok {
            font-size: .72rem;
            color: var(--green);
            margin-top: 5px;
            display: none;
        }

        /* OTP */
        .otp-row {
            display: flex;
            gap: 8px;
        }

        .otp-box {
            flex: 1;
            aspect-ratio: 1;
            text-align: center;
            font-family: var(--mono);
            font-size: 1.2rem;
            font-weight: 600;
            background: var(--surface-2);
            border: 1px solid var(--border-lit);
            border-radius: 10px;
            color: var(--text);
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }

        .otp-box:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--accent-glow);
        }

        .otp-box.filled {
            border-color: #065f46;
            background: #052e1630;
        }

        .timer-hint {
            font-family: var(--mono);
            font-size: .72rem;
            color: var(--text-dim);
            text-align: right;
            margin-top: 8px;
        }

        .timer-hint .cd {
            color: var(--accent);
        }

        .back-link {
            text-align: center;
            margin-top: 12px;
        }

        .back-link a {
            font-size: .76rem;
            color: var(--text-dim);
            text-decoration: none;
            cursor: pointer;
        }

        .back-link a:hover {
            color: var(--text-muted);
        }

        /* Button */
        .btn-submit {
            width: 100%;
            background: var(--accent);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 13px;
            font-family: var(--sans);
            font-size: .95rem;
            font-weight: 600;
            cursor: pointer;
            transition: background .2s, transform .1s, box-shadow .2s;
            position: relative;
            overflow: hidden;
            margin-top: 18px;
        }

        .btn-submit::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, #ffffff15 0%, transparent 60%);
            pointer-events: none;
        }

        .btn-submit:hover {
            background: var(--accent-dim);
            box-shadow: 0 4px 20px #3b82f640;
        }

        .btn-submit:active {
            transform: scale(.98);
        }

        .btn-submit:disabled {
            background: var(--border-lit);
            color: var(--text-dim);
            cursor: not-allowed;
            box-shadow: none;
        }

        /* Success */
        .success-card {
            text-align: center;
            padding: 20px 0;
        }

        .success-icon {
            font-size: 3rem;
            margin-bottom: 14px;
        }

        .success-card h2 {
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 6px;
            letter-spacing: -.02em;
        }

        .success-card p {
            font-size: .85rem;
            color: var(--text-muted);
            line-height: 1.6;
        }

        /* Footer */
        .card-footer-row {
            margin-top: 18px;
            padding-top: 16px;
            border-top: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .sec-badge {
            display: flex;
            align-items: center;
            gap: 6px;
            font-family: var(--mono);
            font-size: .67rem;
            color: var(--text-dim);
        }

        .dot-g {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--green);
            box-shadow: 0 0 6px var(--green);
        }

        .footer-link {
            font-size: .74rem;
            color: var(--text-dim);
            text-decoration: none;
        }

        @media(max-width:480px) {
            .auth-card {
                padding: 26px 18px 22px;
            }
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

            {{-- Progress bar: hidden on step 1, shown on steps 2 & 3 --}}
            <div class="prog-wrap" id="progWrap">
                <div class="prog-seg" id="seg1"></div>
                <div class="prog-seg" id="seg2"></div>
                <div class="prog-seg" id="seg3"></div>
            </div>

            <div class="alert-box alert-error" id="alertBox"><span>⚠</span><span id="alertMsg"></span></div>

            <input type="hidden" id="h_secret" value="{{ $secret ?? '' }}">

            {{-- ═══════════════════════════════════════════════════════
       STEP 1 — Register form + optional 2FA toggle
       ═══════════════════════════════════════════════════════ --}}
            <div class="step active" id="step1">

                <h1 class="card-title">Create account</h1>
                <p class="card-sub">Already have one? <a href="{{ route('login') }}">Sign in →</a></p>

                <div class="field">
                    <label class="field-label">Full name</label>
                    <div class="input-wrap">
                        <span class="input-icon">👤</span>
                        <input type="text" id="name" value="{{ old('name') }}" placeholder="John Doe"
                            autocomplete="name" />
                    </div>
                    @error('name')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="field">
                    <label class="field-label">Email address</label>
                    <div class="input-wrap">
                        <span class="input-icon">✉</span>
                        <input type="email" id="reg_email" value="{{ old('email') }}" placeholder="you@example.com"
                            autocomplete="email" />
                    </div>
                    @error('email')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="field">
                    <label class="field-label">Password</label>
                    <div class="input-wrap">
                        <span class="input-icon">🔑</span>
                        <input type="password" id="pwd" placeholder="Min. 6 characters"
                            oninput="checkStrength(this.value)" />
                        <span class="toggle-eye" onclick="togglePwd('pwd',this)">👁</span>
                    </div>
                    <div class="strength-wrap">
                        <div class="strength-fill" id="sFill"></div>
                    </div>
                    <div class="req-row">
                        <span class="req-chip" id="r-len"><span class="req-dot"></span>6+ chars</span>
                        <span class="req-chip" id="r-up"><span class="req-dot"></span>Uppercase</span>
                        <span class="req-chip" id="r-num"><span class="req-dot"></span>Number</span>
                        <span class="req-chip" id="r-sym"><span class="req-dot"></span>Symbol</span>
                    </div>
                </div>

                <div class="field">
                    <label class="field-label">Confirm password</label>
                    <div class="input-wrap">
                        <span class="input-icon">🔑</span>
                        <input type="password" id="cpwd" placeholder="Repeat password" />
                        <span class="toggle-eye" onclick="togglePwd('cpwd',this)">👁</span>
                    </div>
                    @error('password')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="phone-field">
                    <label class="field-label">Phone number</label>
                    <div class="input-wrap">
                        <span class="input-icon">📱</span>
                        <input type="tel" id="phone" value="{{ old('phone') }}"
                            placeholder="+1 (555) 123-4567" autocomplete="tel" />
                    </div>
                    @error('phone')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="terms-row">
                    <input type="checkbox" id="terms" />
                    <label for="terms">I agree to the <a href="#">Terms of Service</a> and <a
                            href="#">Privacy Policy</a></label>
                </div>




                <button class="btn-submit" id="registerBtn" onclick="submitRegister()">
                    Create Account
                </button>
            </div>

            {{-- ═══════════════════════════════════════════════════════
       STEP 2 — Scan QR Code
       Only reached when user turned 2FA ON above
       ═══════════════════════════════════════════════════════ --}}
            <div class="step" id="step2">
                <div class="step-label">
                    <div class="step-badge active" id="badge2">2</div>
                    Set up authenticator
                </div>
                <h1 class="card-title">Scan QR code</h1>
                <p class="card-sub">Use <strong style="color:var(--text)">Google</strong> or <strong
                        style="color:var(--text)">Microsoft Authenticator</strong></p>

                <div style="text-align:center">
                    <div class="qr-frame" id="qrFrame">
                        <div class="qr-ph">Generating<br />QR code…</div>
                    </div>

                    <p style="font-size:.75rem;color:var(--text-dim);margin-bottom:10px;">
                        Can't scan? Copy the secret key and enter it manually:
                    </p>

                    <div class="secret-copy-wrap">
                        <input type="text" id="secretInput" readonly value="" onclick="copySecret()" />
                        <span class="copy-btn" onclick="copySecret()">📋</span>
                    </div>
                    <p class="copy-ok" id="copyOk">✓ Copied!</p>

                    <div
                        style="background:var(--surface-2);border:1px solid var(--border-lit);border-radius:10px;padding:14px 16px;text-align:left;margin-top:14px;">
                        <ol style="padding-left:16px;margin:0;">
                            <li style="font-size:.78rem;color:var(--text-muted);margin-bottom:5px;">Open your
                                authenticator app and tap <strong style="color:var(--text)">+</strong></li>
                            <li style="font-size:.78rem;color:var(--text-muted);margin-bottom:5px;">Select <strong
                                    style="color:var(--text)">Scan QR code</strong> or <strong
                                    style="color:var(--text)">Enter a setup key</strong></li>
                            <li style="font-size:.78rem;color:var(--text-muted);">Paste the secret key above, or point
                                camera at the QR</li>
                        </ol>
                    </div>
                </div>

                <button class="btn-submit" onclick="goStep3()">I've scanned it — Continue →</button>
            </div>

            {{-- ═══════════════════════════════════════════════════════
       STEP 3 — Verify OTP
       ═══════════════════════════════════════════════════════ --}}
            <div class="step" id="step3">
                <div class="step-label">
                    <div class="step-badge active" id="badge3">3</div>
                    Verify setup
                </div>
                <h1 class="card-title">Confirm your app</h1>
                <p class="card-sub">Enter the 6-digit code shown in your authenticator app</p>

                <div class="field">
                    <label class="field-label">One-time code</label>
                    <div class="otp-row">
                        <input class="otp-box" type="text" maxlength="1" inputmode="numeric"
                            autocomplete="one-time-code" />
                        <input class="otp-box" type="text" maxlength="1" inputmode="numeric" />
                        <input class="otp-box" type="text" maxlength="1" inputmode="numeric" />
                        <input class="otp-box" type="text" maxlength="1" inputmode="numeric" />
                        <input class="otp-box" type="text" maxlength="1" inputmode="numeric" />
                        <input class="otp-box" type="text" maxlength="1" inputmode="numeric" />
                    </div>
                    <div class="timer-hint">Code refreshes every <span class="cd" id="cd3">30</span>s</div>
                </div>

                <div
                    style="background:var(--surface-2);border:1px solid var(--border-lit);border-radius:10px;padding:12px 14px;font-size:.78rem;color:var(--text-muted);line-height:1.55;margin-bottom:14px;">
                    💡 Enter the code <em>before</em> it expires. If it changes, enter the new one.
                </div>

                <button class="btn-submit" id="verifyBtn" onclick="submitVerify()" disabled>
                    ✓ Verify &amp; Finish Setup
                </button>
                <div class="back-link"><a onclick="goStep(2)">← Back to QR code</a></div>
            </div>

            <div class="step" id="stepSuccess">
                <div class="success-card">
                    <div class="success-icon" id="sIcon">✅</div>
                    <h2 id="sTitle">You're all set!</h2>
                    <p id="sMsg">Redirecting to dashboard…</p>
                </div>
            </div>

            <div class="card-footer-row">
                <div class="sec-badge"><span class="dot-g"></span> SSL Encrypted</div>
                <a href="#" class="footer-link">Privacy Policy</a>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // ═══════════════════════════════════════
        //  STATE
        // ═══════════════════════════════════════
        let twoFASecret = document.getElementById('h_secret').value || '';
        let accessToken = '';
        let wants2FA = false;
        let cd3Timer = null;

        function handleToggleClick(e) {
            // Don't double-fire when the actual checkbox is clicked
            if (e.target.tagName === 'INPUT') return;
            const chk = document.getElementById('togChk');
            chk.checked = !chk.checked;
            syncToggle();
        }

        function syncToggle() {
            wants2FA = document.getElementById('togChk').checked;
            const row = document.getElementById('twofaRow');
            const pill = document.getElementById('statusPill');
            if (wants2FA) {
                row.classList.add('on');
                pill.textContent = 'On';
                pill.className = 'status-pill pill-on';
            } else {
                row.classList.remove('on');
                pill.textContent = 'Off';
                pill.className = 'status-pill pill-off';
            }
        }

        // ═══════════════════════════════════════
        //  STEP NAVIGATION
        // ═══════════════════════════════════════
        function goStep(n) {
            document.querySelectorAll('.step').forEach(s => s.classList.remove('active'));
            document.getElementById('step' + n).classList.add('active');

            // Show progress bar only on steps 2 & 3
            const pw = document.getElementById('progWrap');
            if (n === 2 || n === 3) {
                pw.classList.add('show');
                ['seg1', 'seg2', 'seg3'].forEach((id, i) => {
                    const el = document.getElementById(id);
                    el.classList.remove('active', 'done');
                    if (i + 1 < n) el.classList.add('done');
                    if (i + 1 === n) el.classList.add('active');
                });
            } else {
                pw.classList.remove('show');
            }

            // Fill secret input when reaching step 2
            if (n === 2 && twoFASecret) {
                document.getElementById('secretInput').value = twoFASecret;
            }
        }

        // ═══════════════════════════════════════
        //  STEP 1 → REGISTER
        //  wants2FA OFF → register → dashboard
        //  wants2FA ON  → register → step 2 (QR)
        // ═══════════════════════════════════════
        async function submitRegister() {
            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('reg_email').value.trim();
            const pwd = document.getElementById('pwd').value;
            const cpwd = document.getElementById('cpwd').value;
            const phone = document.getElementById('phone').value.trim();
            const terms = document.getElementById('terms').checked;

            if (!name || !email || !pwd || !cpwd || !phone) {
                showAlert('Please fill in all fields.');
                return;
            }
            if (pwd !== cpwd) {
                showAlert('Passwords do not match.');
                return;
            }
            if (pwd.length < 6) {
                showAlert('Password must be at least 6 characters.');
                return;
            }
            if (!phone) {
                showAlert('Please enter a phone number.');
                return;
            }
            if (!terms) {
                showAlert('Please accept the Terms of Service.');
                return;
            }
            hideAlert();

            const btn = document.getElementById('registerBtn');
            btn.disabled = true;
            btn.textContent = 'Creating account…';

            try {
                const res = await fetch('{{ route('register.submit') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        name,
                        email,
                        password: pwd,
                        password_confirmation: cpwd,
                        phone,
                        wants_2fa: wants2FA
                    }),
                });
                const data = await res.json();

                if (!res.ok) {
                    showAlert(data.message || Object.values(data.errors || {}).flat().join(' '));
                    btn.disabled = false;
                    btn.textContent = 'Create Account';
                    return;
                }

                // Store token (needed for 2FA enable call on step 3)
                if (data.access_token) accessToken = window.accessToken = data.access_token;

                // ── PATH A: 2FA toggle was OFF → go straight to dashboard ──
                if (!wants2FA) {
                    showSuccess('🎉', 'Account created!', 'Redirecting to dashboard…');
                    setTimeout(() => {
                        window.location.href = data.redirect ?? '/dashboard';
                    }, 1800);
                    return;
                }

                // ── PATH B: 2FA toggle was ON → show QR (step 2) ──
                if (data.qr_code_image) {
                    document.getElementById('qrFrame').innerHTML =
                        `<img src="${data.qr_code_image}" alt="QR" style="width:100%;height:100%;border-radius:12px;"/>`;
                } else if (data.qr_code_url) {
                    const enc = encodeURIComponent(data.qr_code_url);
                    document.getElementById('qrFrame').innerHTML =
                        `<img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${enc}" alt="QR" style="width:100%;height:100%;border-radius:12px;"/>`;
                }
                if (data.secret) twoFASecret = data.secret;
                goStep(2);

            } catch (err) {
                console.error(err);
                showAlert('Something went wrong. Please try again.');
                btn.disabled = false;
                btn.textContent = 'Create Account';
            }
        }

        // ═══════════════════════════════════════
        //  STEP 2 → 3
        // ═══════════════════════════════════════
        function goStep3() {
            goStep(3);
            startTimer();
            setTimeout(() => document.querySelectorAll('.otp-box')[0].focus(), 150);
        }

        // ═══════════════════════════════════════
        //  STEP 3 — VERIFY OTP
        //  Calls /2fa/enable → sets two_factor_enabled = true on user
        //  After this, loginUser() will ask for OTP on every login
        // ═══════════════════════════════════════
        async function submitVerify() {
            const code = [...document.querySelectorAll('.otp-box')].map(b => b.value).join('');
            const btn = document.getElementById('verifyBtn');
            const tok = accessToken || window.accessToken || '';

            btn.disabled = true;
            btn.textContent = 'Verifying…';
            hideAlert();

            try {
                const res = await fetch('{{ route('2fa.enable') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Authorization': `Bearer ${tok}`,
                    },
                    body: JSON.stringify({
                        code
                    }),
                });
                const data = await res.json();

                if (data.status === 'success') {
                    clearInterval(cd3Timer);
                    // two_factor_enabled is now true in DB
                    // Next login will trigger the OTP step in loginUser()
                    showSuccess('🔐', '2FA Enabled!', 'Your account is now protected.<br/>Redirecting…');
                    setTimeout(() => {
                        window.location.href = data.redirect ?? '/dashboard';
                    }, 2200);
                } else {
                    showAlert(data.message || 'Invalid code. Try again.');
                    document.querySelectorAll('.otp-box').forEach(b => {
                        b.value = '';
                        b.classList.remove('filled');
                    });
                    document.querySelectorAll('.otp-box')[0].focus();
                    btn.disabled = false;
                    btn.textContent = '✓ Verify & Finish Setup';
                }
            } catch (err) {
                console.error(err);
                showAlert('Something went wrong. Please try again.');
                btn.disabled = false;
                btn.textContent = '✓ Verify & Finish Setup';
            }
        }

        // ═══════════════════════════════════════
        //  OTP BOXES
        // ═══════════════════════════════════════
        document.querySelectorAll('.otp-box').forEach((b, i, all) => {
            b.addEventListener('input', () => {
                b.value = b.value.replace(/\D/, '').slice(-1);
                b.classList.toggle('filled', !!b.value);
                if (b.value && i < all.length - 1) all[i + 1].focus();
                document.getElementById('verifyBtn').disabled = ![...all].every(x => x.value);
            });
            b.addEventListener('keydown', e => {
                if (e.key === 'Backspace' && !b.value && i > 0) {
                    all[i - 1].value = '';
                    all[i - 1].classList.remove('filled');
                    all[i - 1].focus();
                }
            });
            b.addEventListener('paste', e => {
                e.preventDefault();
                const d = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '')
                    .slice(0, 6);
                [...d].forEach((ch, idx) => {
                    if (all[idx]) {
                        all[idx].value = ch;
                        all[idx].classList.add('filled');
                    }
                });
                document.getElementById('verifyBtn').disabled = d.length < 6;
                if (d.length) all[Math.min(d.length, all.length - 1)].focus();
            });
        });

        // ═══════════════════════════════════════
        //  COUNTDOWN
        // ═══════════════════════════════════════
        function startTimer() {
            let t = 30;
            document.getElementById('cd3').textContent = t;
            clearInterval(cd3Timer);
            cd3Timer = setInterval(() => {
                document.getElementById('cd3').textContent = --t;
                if (t <= 0) clearInterval(cd3Timer);
            }, 1000);
        }

        // ═══════════════════════════════════════
        //  COPY SECRET
        // ═══════════════════════════════════════
        function copySecret() {
            const val = document.getElementById('secretInput').value;
            if (!val) return;
            navigator.clipboard.writeText(val).then(() => {
                const ok = document.getElementById('copyOk');
                ok.style.display = 'block';
                setTimeout(() => {
                    ok.style.display = 'none';
                }, 2000);
            });
        }

        // ═══════════════════════════════════════
        //  SUCCESS SCREEN
        // ═══════════════════════════════════════
        function showSuccess(icon, title, msg) {
            document.getElementById('sIcon').textContent = icon;
            document.getElementById('sTitle').textContent = title;
            document.getElementById('sMsg').innerHTML = msg;
            document.getElementById('progWrap').classList.remove('show');
            document.querySelectorAll('.step').forEach(s => s.classList.remove('active'));
            document.getElementById('stepSuccess').classList.add('active');
        }

        // ═══════════════════════════════════════
        //  HELPERS
        // ═══════════════════════════════════════
        function checkStrength(v) {
            const c = {
                'r-len': v.length >= 6,
                'r-up': /[A-Z]/.test(v),
                'r-num': /[0-9]/.test(v),
                'r-sym': /[^A-Za-z0-9]/.test(v)
            };
            const s = Object.values(c).filter(Boolean).length;
            const f = document.getElementById('sFill');
            f.style.width = (s * 25) + '%';
            f.style.background = ['#ef4444', '#f59e0b', '#eab308', '#10b981'][s - 1] || '#ef4444';
            Object.entries(c).forEach(([id, ok]) => document.getElementById(id).classList.toggle('ok', ok));
        }

        function togglePwd(id, el) {
            const i = document.getElementById(id);
            i.type = i.type === 'password' ? 'text' : 'password';
            el.textContent = i.type === 'password' ? '👁' : '🙈';
        }

        function showAlert(m) {
            document.getElementById('alertMsg').textContent = m;
            document.getElementById('alertBox').classList.add('show');
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        function hideAlert() {
            document.getElementById('alertBox').classList.remove('show');
        }

        // Laravel validation errors
        @if ($errors->any())
            showAlert("{{ $errors->first() }}");
        @endif

        // Laravel session: user already registered, show QR (non-AJAX fallback)
        @if (session('2fa_setup'))
            wants2FA = true;
            twoFASecret = "{{ session('2fa_secret', '') }}";
            accessToken = window.accessToken = "{{ session('access_token', '') }}";
            @if (session('qr_code_url'))
                document.getElementById('qrFrame').innerHTML =
                    `<img src="{{ session('qr_code_url') }}" alt="QR" style="width:100%;height:100%;border-radius:12px;"/>`;
            @endif
            goStep(2);
        @endif
    </script>
</body>

</html>
