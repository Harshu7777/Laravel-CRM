@extends('layouts.app')

@section('title', 'Profile')
@section('content')

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Profile — {{ config('app.name') }}</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500;600&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bg: #0b0e13;
            --surface: #111620;
            --surface-2: #181d28;
            --border: #1e2535;
            --border-lit: #2a3448;
            --accent: #3b82f6;
            --green: #10b981;
            --red: #ef4444;
            --text: #e2e8f0;
            --text-muted: #64748b;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        html, body {
            background: var(--bg);
            color: var(--text);
            font-family: 'Outfit', sans-serif;
            min-height: 100vh;
        }

        .navbar {
            background: var(--surface) !important;
            border-bottom: 1px solid var(--border);
        }

        .card-box {
            background: var(--surface);
            border: 1px solid var(--border-lit);
            border-radius: 16px;
            padding: 28px;
        }

        .badge-on {
            background: #052e16;
            color: #86efac;
            border: 1px solid #14532d;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.9rem;
        }

        .badge-off {
            background: #450a0a;
            color: #fca5a5;
            border: 1px solid #7f1d1d;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.9rem;
        }

        .toast-msg {
            position: fixed;
            bottom: 24px;
            right: 24px;
            background: var(--surface);
            border: 1px solid var(--border-lit);
            border-radius: 12px;
            padding: 14px 20px;
            z-index: 9999;
            display: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.6);
        }

        .toast-msg.show {
            display: block;
        }
    </style>
</head>
{{-- @php
    dd([
        'check' => auth()->user()
    ]);
@endphp --}}
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="/">🔐 {{ config('app.name') }}<span>.</span></a>
        
        <div class="ms-auto d-flex align-items-center gap-3">
            <div class="user-pill bg-dark px-3 py-2 rounded-pill">
                👤 <span id="navName">Loading...</span>
            </div>
            {{-- <button class="btn btn-danger btn-sm" onclick="logoutUser()">Logout</button> --}}
        </div>
    </div>
</nav>

<div class="hero text-center py-5">
    <h1>Welcome back! 👋</h1>
    <p id="heroSub">Loading your profile...</p>
</div>

<div class="container py-4">
    <div class="row g-4">

        <!-- Profile Card -->
        <div class="col-md-6">
            <div class="card-box">
                <h5>👤 Profile</h5>
                <div class="py-2 border-bottom d-flex justify-content-between">
                    <span class="text-muted">Full Name</span>
                    <span class="fw-semibold" id="pName">—</span>
                </div>
                <div class="py-2 border-bottom d-flex justify-content-between">
                    <span class="text-muted">Email</span>
                    <span class="fw-semibold" id="pEmail">—</span>
                </div>
                <div class="py-2 d-flex justify-content-between">
                    <span class="text-muted">Member Since</span>
                    <span class="fw-semibold" id="pJoined">—</span>
                </div>
            </div>
        </div>

        <!-- Security Card -->
        <div class="col-md-6">
            <div class="card-box">
                <h5>🔒 Security Settings</h5>
                
                <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                    <div>
                        <strong>Two-Factor Authentication</strong><br>
                        <span id="twoFaHint" class="text-muted small">Loading...</span>
                    </div>
                    <label class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="twoFaToggle" 
                               onchange="toggle2FA(this)" style="transform:scale(1.4);">
                    </label>
                </div>

                <div class="pt-3">
                    <span class="text-muted">Current Status:</span>
                    <span id="twoFaBadge">—</span>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Toast Notification -->
<div class="toast-msg" id="toast">
    <span id="toastMsg"></span>
</div>

<script>
    const accessToken = localStorage.getItem('access_token');
    if (!accessToken) {
        window.location.href = '{{ route("login") }}';
    }

    // Load User Data
    async function loadUser() {
        try {
            const res = await fetch('{{ url("/me") }}', {
                headers: {
                    'Authorization': 'Bearer ' + accessToken,
                    'Accept': 'application/json'
                }
            });

            if (res.status === 401) {
                localStorage.clear();
                window.location.href = '{{ route("login") }}';
                return;
            }

            const data = await res.json();
            const user = data.user || data;

            // Update Profile Info
            document.getElementById('navName').textContent = user.name || 'User';
            document.getElementById('heroSub').textContent = `Hello, ${user.name || 'User'}!`;
            document.getElementById('pName').textContent = user.name || '—';
            document.getElementById('pEmail').textContent = user.email || '—';
            document.getElementById('pJoined').textContent = user.created_at 
                ? new Date(user.created_at).toLocaleDateString('en-IN', { 
                    year: 'numeric', 
                    month: 'short', 
                    day: 'numeric' 
                  }) 
                : '—';

            // Update 2FA UI
            update2FAUI(!!user.two_factor_enabled);

        } catch (e) {
            console.error(e);
            document.getElementById('heroSub').innerHTML = 
                '<span class="text-danger">Failed to load profile</span>';
        }
    }

    function update2FAUI(enabled) {
        const toggle = document.getElementById('twoFaToggle');
        toggle.checked = enabled;

        document.getElementById('twoFaHint').textContent = enabled 
            ? 'Your account is protected with 2FA' 
            : 'Enable 2FA for extra security';

        document.getElementById('twoFaBadge').innerHTML = enabled 
            ? '<span class="badge-on">Enabled ✅</span>' 
            : '<span class="badge-off">Disabled ❌</span>';
    }

    // Toggle 2FA
    async function toggle2FA(checkbox) {
        const newState = checkbox.checked;
        checkbox.disabled = true;

        try {
            const res = await fetch('{{ url("/2fa/toggle") }}', {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + accessToken,
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ enable: newState })
            });

            const data = await res.json();

            if (data.status === 'success') {
                showToast(newState ? '✅ 2FA Enabled Successfully!' : '❌ 2FA Disabled Successfully!');
                await loadUser();   // Refresh UI
            } else {
                checkbox.checked = !newState;
                showToast(data.message || 'Failed to update 2FA');
            }
        } catch (e) {
            checkbox.checked = !newState;
            showToast('Error occurred. Please try again.');
        } finally {
            checkbox.disabled = false;
        }
    }

    // Logout
    function logoutUser() {
        localStorage.clear();
        window.location.href = '{{ route("login") }}';
    }

    // Show Toast
    function showToast(msg) {
        const toast = document.getElementById('toast');
        document.getElementById('toastMsg').innerHTML = msg;
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 3000);
    }

    // Initialize
    loadUser();
</script>

</body>
</html>

@endsection