<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MyApp')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --bg:        #0d0f14;
            --surface:   #13161e;
            --surface2:  #1a1e29;
            --border:    #252a38;
            --accent:    #4f8ef7;
            --accent2:   #a78bfa;
            --green:     #34d399;
            --orange:    #fb923c;
            --red:       #f87171;
            --text:      #e8eaf0;
            --muted:     #6b7280;
            --sidebar-w: 240px;
        }

        body.light {
            --bg:       #f0f2f7;
            --surface:  #ffffff;
            --surface2: #f5f6fa;
            --border:   #e2e5ee;
            --text:     #1a1e29;
            --muted:    #8b93a7;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            transition: background .3s, color .3s;
        }

        /* ── Sidebar ── */
        .sidebar {
            width: var(--sidebar-w);
            background: var(--surface);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0;
            height: 100vh;
            z-index: 100;
            transition: transform .3s ease, background .3s, border-color .3s;
        }

        .sidebar-brand {
            padding: 22px 20px 18px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .brand-logo {
            width: 36px; height: 36px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem; color: #fff; flex-shrink: 0;
        }

        .brand-text { flex: 1; }
        .brand-name {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 1.1rem;
            color: var(--text);
            letter-spacing: -0.5px;
            line-height: 1;
        }
        .brand-name span { color: var(--accent); }
        .brand-tag {
            font-size: 0.62rem;
            color: var(--muted);
            margin-top: 2px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .nav-section {
            padding: 16px 10px 8px;
            flex: 1;
            overflow-y: auto;
        }

        .nav-label {
            font-size: 0.62rem;
            font-weight: 600;
            color: var(--muted);
            letter-spacing: 1.5px;
            text-transform: uppercase;
            padding: 0 10px;
            margin-bottom: 5px;
        }

        .nav-link-item {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 9px 12px;
            border-radius: 9px;
            color: var(--muted);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 1px;
            transition: all .18s;
            position: relative;
        }

        .nav-link-item:hover { background: var(--surface2); color: var(--text); }

        .nav-link-item.active {
            background: rgba(79,142,247,.13);
            color: var(--accent);
        }

        .nav-link-item.active::before {
            content: '';
            position: absolute;
            left: 0; top: 20%; bottom: 20%;
            width: 3px;
            border-radius: 0 3px 3px 0;
            background: var(--accent);
        }

        .nav-link-item i { font-size: 0.95rem; width: 18px; text-align: center; flex-shrink: 0; }

        .nav-badge {
            margin-left: auto;
            background: var(--accent);
            color: #fff;
            font-size: 0.6rem;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 20px;
            line-height: 1.4;
        }

        /* ── Sidebar user chip (bottom) ── */
        .sidebar-footer {
            padding: 12px;
            border-top: 1px solid var(--border);
        }

        .user-chip {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 10px;
            background: var(--surface2);
            border: 1px solid var(--border);
            transition: all .2s;
            text-decoration: none;
            cursor: pointer;
        }

        .user-chip:hover { border-color: var(--accent); }

        .avatar {
            width: 34px; height: 34px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            display: flex; align-items: center; justify-content: center;
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 0.8rem;
            color: #fff;
            flex-shrink: 0;
        }

        .user-info { flex: 1; min-width: 0; }
        .user-name { font-size: 0.82rem; font-weight: 600; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .user-role { font-size: 0.68rem; color: var(--muted); display: flex; align-items: center; gap: 4px; margin-top: 1px; }

        .role-dot {
            width: 5px; height: 5px;
            border-radius: 50%;
            display: inline-block;
        }
        .role-dot.admin    { background: var(--accent); }
        .role-dot.staff    { background: var(--green); }
        .role-dot.customer { background: var(--accent2); }

        .sidebar-footer-actions {
            display: flex;
            gap: 6px;
            margin-top: 8px;
        }

        .sf-btn {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 7px 10px;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 500;
            text-decoration: none;
            border: 1px solid var(--border);
            background: transparent;
            color: var(--muted);
            cursor: pointer;
            transition: all .2s;
        }

        .sf-btn:hover { background: var(--surface2); color: var(--text); }
        .sf-btn.danger { color: var(--red); }
        .sf-btn.danger:hover { background: rgba(248,113,113,.1); border-color: rgba(248,113,113,.3); }

        /* ── Main ── */
        .main-wrap {
            margin-left: var(--sidebar-w);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* ── Topbar ── */
        .topbar {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 0 24px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 50;
            transition: background .3s, border-color .3s;
        }

        .topbar-left { display: flex; align-items: center; gap: 12px; }

        .breadcrumb-wrap {
            display: flex;
            flex-direction: column;
        }

        .topbar-title {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 1rem;
            color: var(--text);
            line-height: 1;
        }

        .topbar-sub {
            font-size: 0.72rem;
            color: var(--muted);
            margin-top: 2px;
        }

        .topbar-actions { display: flex; align-items: center; gap: 8px; }

        .icon-btn {
            width: 36px; height: 36px;
            border-radius: 9px;
            background: var(--surface2);
            border: 1px solid var(--border);
            color: var(--muted);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            transition: all .2s;
            text-decoration: none;
            font-size: 0.95rem;
            position: relative;
        }

        .icon-btn:hover { background: var(--border); color: var(--text); }

        .notif-dot::after {
            content: '';
            position: absolute;
            top: 7px; right: 7px;
            width: 6px; height: 6px;
            border-radius: 50%;
            background: var(--red);
            border: 1.5px solid var(--surface);
        }

        /* ── Avatar dropdown ── */
        .avatar-wrap { position: relative; }

        .avatar-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 4px 10px 4px 4px;
            border-radius: 22px;
            border: 1px solid var(--border);
            background: var(--surface2);
            cursor: pointer;
            transition: all .2s;
        }

        .avatar-btn:hover { border-color: var(--accent); }

        .avatar-btn .avatar { width: 30px; height: 30px; font-size: 0.72rem; }

        .avatar-btn-info { display: flex; flex-direction: column; line-height: 1; }
        .avatar-btn-name { font-size: 0.78rem; font-weight: 600; color: var(--text); }
        .avatar-btn-role { font-size: 0.65rem; color: var(--muted); margin-top: 1px; }

        .avatar-dropdown {
            display: none;
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            width: 220px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 6px;
            z-index: 999;
            box-shadow: 0 8px 32px rgba(0,0,0,.3);
        }

        .avatar-dropdown.show { display: block; animation: dropIn .18s ease; }

        @keyframes dropIn { from{opacity:0;transform:translateY(-6px)} to{opacity:1;transform:none} }

        .dd-header {
            padding: 10px 12px 10px;
            border-bottom: 1px solid var(--border);
            margin-bottom: 4px;
        }

        .dd-name { font-size: 0.85rem; font-weight: 600; color: var(--text); }
        .dd-email { font-size: 0.72rem; color: var(--muted); margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .dd-role-pill {
            display: inline-flex; align-items: center; gap: 4px;
            font-size: 0.65rem; font-weight: 600;
            padding: 2px 8px; border-radius: 20px; margin-top: 6px;
        }
        .dd-role-pill.admin    { background: rgba(79,142,247,.15); color: var(--accent); }
        .dd-role-pill.staff    { background: rgba(52,211,153,.15); color: var(--green); }
        .dd-role-pill.customer { background: rgba(167,139,250,.15); color: var(--accent2); }

        .dd-item {
            display: flex; align-items: center; gap: 10px;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 0.82rem;
            color: var(--muted);
            text-decoration: none;
            transition: all .15s;
            cursor: pointer;
            border: none; background: transparent; width: 100%; text-align: left;
        }

        .dd-item:hover { background: var(--surface2); color: var(--text); }
        .dd-item.danger { color: var(--red); }
        .dd-item.danger:hover { background: rgba(248,113,113,.1); }
        .dd-item i { width: 16px; text-align: center; font-size: 0.85rem; }

        .dd-divider { height: 1px; background: var(--border); margin: 4px 6px; }

        /* ── Content ── */
        .content { padding: 24px; flex: 1; }

        /* ── Footer ── */
        .footer {
            background: var(--surface);
            border-top: 1px solid var(--border);
            padding: 14px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 8px;
            transition: background .3s, border-color .3s;
        }

        .footer-left { font-size: 0.75rem; color: var(--muted); }
        .footer-left span { color: var(--accent); font-weight: 600; }
        .footer-links { display: flex; gap: 14px; }
        .footer-links a { font-size: 0.75rem; color: var(--muted); text-decoration: none; transition: color .2s; }
        .footer-links a:hover { color: var(--text); }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main-wrap { margin-left: 0; }
            .avatar-btn-info { display: none; }
            .footer { justify-content: center; text-align: center; }
            .footer-links { justify-content: center; }
        }
    </style>

    @yield('styles')
</head>
<body>

{{-- ══════════ SIDEBAR ══════════ --}}
<aside class="sidebar" id="sidebar">

    <div class="sidebar-brand">
        <div class="brand-logo">⚡</div>
        <div class="brand-text">
            <a href="{{ route('index') }}" style="text-decoration: none">
                <div class="brand-name">New<span>Bazar</span></div>
            </a>
            <div class="brand-tag">Management Panel</div>
        </div>
    </div>

    <nav class="nav-section">
        <div class="nav-label mb-2">Main</div>

        <a href="{{ route('dashboard') }}"
           class="nav-link-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill"></i> Dashboard
        </a>

            <!-- Users Menu Item -->
        <a href="{{ route('users.index') }}" 
        class="nav-link-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
            <i class="bi bi-people-fill"></i> 
            <span>Users</span>
        </a>

        <a href="{{ route('orders.index') }}" class="nav-link-item {{ request()->routeIs('orders.*') ? 'active' : '' }}">
            <i class="bi bi-bag-fill"></i> Orders
        </a>

        <a href="{{ route('categories.index') }}" class="nav-link-item {{ request()->routeIs('categories.*') ? 'active' : '' }}">
            <i class="bi bi-bag-fill"></i> Categories
        </a>

        <a href="{{ route('products.index') }}" class="nav-link-item {{ request()->routeIs('products.*') ? 'active' : '' }}">
            <i class="bi bi-box-seam-fill"></i> Products
        </a>

        <div class="nav-label mt-3 mb-2">CRM</div>

        <a href="{{ route('lead') }}" class="nav-link-item {{ request()->routeIs('leads.*') ? 'active' : '' }}">
            <i class="bi bi-lightning-charge-fill"></i> Leads
        </a>

        <a href="#" class="nav-link-item {{ request()->routeIs('customers.*') ? 'active' : '' }}">
            <i class="bi bi-person-lines-fill"></i> Customers
        </a>

        <div class="nav-label mt-3 mb-2">System</div>

        <a href="#" class="nav-link-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
            <i class="bi bi-bar-chart-fill"></i> Reports
        </a>

        <a href="{{ route('profile') }}"
           class="nav-link-item {{ request()->routeIs('profile') ? 'active' : '' }}">
            <i class="bi bi-person-circle"></i> My Profile
        </a>

        <a href="#" class="nav-link-item {{ request()->routeIs('settings.*') ? 'active' : '' }}">
            <i class="bi bi-gear-fill"></i> Settings
        </a>
    </nav>

    <div class="sidebar-footer">
        {{-- User chip links to profile --}}
        <a href="{{ route('profile') }}" class="user-chip" style="text-decoration:none">
            <div class="avatar">
                {{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 1)) }}
            </div>
            <div class="user-info">
                <div class="user-name">{{ auth()->user()?->name ?? 'User' }}</div>
                <div class="user-role">
                    @php $role = auth()->user()?->role ?? 'customer'; @endphp
                    <span class="role-dot {{ $role }}"></span>
                    {{ ucfirst($role) }}
                </div>
            </div>
            <i class="bi bi-chevron-right" style="font-size:.65rem;color:var(--muted)"></i>
        </a>

        <div class="sidebar-footer-actions">
            <a href="{{ route('profile') }}" class="sf-btn">
                <i class="bi bi-person-fill"></i> Profile
            </a>
            <form method="POST" action="{{ route('logout') }}" style="flex:1">
                @csrf
                <button type="submit" class="sf-btn danger" style="width:100%">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </button>
            </form>
        </div>
    </div>

</aside>

{{-- ══════════ MAIN WRAP ══════════ --}}
<div class="main-wrap">

    {{-- ── Topbar ── --}}
    <header class="topbar">
        <div class="topbar-left">
            <button class="icon-btn d-md-none" id="sidebarToggle">
                <i class="bi bi-list"></i>
            </button>
            <div class="breadcrumb-wrap">
                <div class="topbar-title">@yield('page_title', 'Dashboard')</div>
                <div class="topbar-sub">
                    {{ date('l, d M Y') }} &nbsp;·&nbsp;
                    @php $role = auth()->user()?->role ?? 'customer'; @endphp
                    <span style="color:var(--accent)">{{ ucfirst($role) }}</span> panel
                </div>
            </div>
        </div>

        <div class="topbar-actions">

            {{-- Theme Toggle --}}
            <button class="icon-btn" id="themeToggle" title="Toggle theme">
                <i class="bi bi-moon-fill" id="themeIcon"></i>
            </button>

            {{-- Notifications --}}
            <a href="#" class="icon-btn notif-dot" title="Notifications">
                <i class="bi bi-bell"></i>
            </a>

            {{-- Messages --}}
            <a href="#" class="icon-btn" title="Messages">
                <i class="bi bi-chat-dots"></i>
            </a>

            {{-- Avatar + Dropdown --}}
            <div class="avatar-wrap">
                <div class="avatar-btn" id="avatarBtn">
                    <div class="avatar">
                        {{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="avatar-btn-info">
                        <span class="avatar-btn-name">{{ auth()->user()?->name ?? 'User' }}</span>
                        <span class="avatar-btn-role">{{ ucfirst(auth()->user()?->role ?? 'customer') }}</span>
                    </div>
                    <i class="bi bi-chevron-down" style="font-size:.65rem;color:var(--muted);margin-left:2px"></i>
                </div>

                <div class="avatar-dropdown" id="avatarDropdown">
                    <div class="dd-header">
                        <div class="dd-name">{{ auth()->user()?->name ?? 'User' }}</div>
                        <div class="dd-email">{{ auth()->user()?->email ?? '' }}</div>
                        <span class="dd-role-pill {{ auth()->user()?->role ?? 'customer' }}">
                            <i class="bi bi-shield-check-fill"></i>
                            {{ ucfirst(auth()->user()?->role ?? 'customer') }}
                        </span>
                    </div>

                    <a href="{{ route('profile') }}" class="dd-item">
                        <i class="bi bi-person-fill"></i> My Profile
                    </a>
                    <a href="#" class="dd-item">
                        <i class="bi bi-gear-fill"></i> Account Settings
                    </a>
                    <a href="#" class="dd-item">
                        <i class="bi bi-shield-lock-fill"></i> Security
                    </a>

                    <div class="dd-divider"></div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dd-item danger">
                            <i class="bi bi-box-arrow-right"></i> Sign out
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </header>

    {{-- ── Page Content ── --}}
    <main class="content">
        @yield('content')
    </main>

    {{-- ── Footer ── --}}
    <footer class="footer">
        <div class="footer-left">
            &copy; {{ date('Y') }} <span>MyApp</span>. All rights reserved.
        </div>
        <div class="footer-links">
            <a href="#">Privacy Policy</a>
            <a href="#">Terms of Use</a>
            <a href="#">Support</a>
        </div>
    </footer>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Mobile sidebar
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    if (sidebarToggle) sidebarToggle.addEventListener('click', () => sidebar.classList.toggle('open'));

    // Dark/light theme
    const themeBtn  = document.getElementById('themeToggle');
    const themeIcon = document.getElementById('themeIcon');
    if (localStorage.getItem('theme') === 'light') {
        document.body.classList.add('light');
        themeIcon.className = 'bi bi-sun-fill';
    }
    themeBtn.addEventListener('click', () => {
        document.body.classList.toggle('light');
        const isLight = document.body.classList.contains('light');
        themeIcon.className = isLight ? 'bi bi-sun-fill' : 'bi bi-moon-fill';
        localStorage.setItem('theme', isLight ? 'light' : 'dark');
    });

    // Avatar dropdown
    const avatarBtn      = document.getElementById('avatarBtn');
    const avatarDropdown = document.getElementById('avatarDropdown');
    avatarBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        avatarDropdown.classList.toggle('show');
    });
    document.addEventListener('click', () => avatarDropdown.classList.remove('show'));
</script>

@yield('scripts')


</body>
</html>