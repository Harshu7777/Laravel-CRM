@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('styles')
<style>
    /* ── Greeting banner ── */
    .greeting-banner {
        background: linear-gradient(135deg, rgba(79,142,247,.12) 0%, rgba(167,139,250,.08) 100%);
        border: 1px solid rgba(79,142,247,.2);
        border-radius: 16px;
        padding: 22px 26px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }

    .greeting-left {}

    .greeting-title {
        font-family: 'Syne', sans-serif;
        font-weight: 700;
        font-size: 1.5rem;
        color: var(--text);
        line-height: 1.2;
    }

    .greeting-sub {
        color: var(--muted);
        font-size: 0.875rem;
        margin-top: 6px;
    }

    .greeting-role-badge {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 6px 14px;
        border-radius: 22px;
        font-size: 0.8rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .badge-admin    { background: rgba(79,142,247,.15); color: var(--accent); border: 1px solid rgba(79,142,247,.25); }
    .badge-staff    { background: rgba(52,211,153,.15); color: var(--green);  border: 1px solid rgba(52,211,153,.25); }
    .badge-customer { background: rgba(167,139,250,.15); color: var(--accent2); border: 1px solid rgba(167,139,250,.25); }

    /* ── Stats ── */
    .stat-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 20px 22px;
        transition: transform .2s, border-color .2s;
        position: relative;
        overflow: hidden;
    }

    .stat-card::after {
        content: '';
        position: absolute;
        top: 0; right: 0;
        width: 80px; height: 80px;
        border-radius: 50%;
        opacity: .05;
        transform: translate(20px, -20px);
    }

    .stat-card:hover { transform: translateY(-2px); border-color: var(--accent); }

    .stat-icon {
        width: 42px; height: 42px;
        border-radius: 11px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem;
        margin-bottom: 14px;
    }

    .stat-value {
        font-family: 'Syne', sans-serif;
        font-size: 1.75rem;
        font-weight: 800;
        color: var(--text);
        line-height: 1;
    }

    .stat-label { font-size: 0.8rem; color: var(--muted); margin-top: 4px; }

    .stat-change {
        font-size: 0.72rem;
        font-weight: 600;
        margin-top: 10px;
        display: inline-flex;
        align-items: center;
        gap: 3px;
        padding: 3px 8px;
        border-radius: 20px;
    }

    .change-up   { background: rgba(52,211,153,.12); color: var(--green); }
    .change-down { background: rgba(248,113,113,.12); color: var(--red); }
    .change-neutral { background: rgba(107,114,128,.12); color: var(--muted); }

    /* ── Section header ── */
    .section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 14px;
    }

    .section-title {
        font-family: 'Syne', sans-serif;
        font-weight: 700;
        font-size: 0.95rem;
        color: var(--text);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .section-title i { color: var(--accent); font-size: 0.9rem; }

    .btn-view-all {
        font-size: 0.75rem;
        color: var(--accent);
        text-decoration: none;
        font-weight: 500;
        display: flex; align-items: center; gap: 4px;
        padding: 5px 12px;
        border-radius: 8px;
        border: 1px solid rgba(79,142,247,.25);
        transition: all .2s;
    }

    .btn-view-all:hover { background: rgba(79,142,247,.1); }

    /* ── Table card ── */
    .table-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px;
        overflow: hidden;
    }

    .table-card-header {
        padding: 16px 20px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .custom-table { width: 100%; border-collapse: collapse; }

    .custom-table thead th {
        padding: 10px 20px;
        font-size: 0.68rem;
        font-weight: 600;
        letter-spacing: 1px;
        text-transform: uppercase;
        color: var(--muted);
        background: var(--surface2);
        border-bottom: 1px solid var(--border);
    }

    .custom-table tbody td {
        padding: 13px 20px;
        font-size: 0.83rem;
        color: var(--text);
        border-bottom: 1px solid var(--border);
        vertical-align: middle;
    }

    .custom-table tbody tr:last-child td { border-bottom: none; }
    .custom-table tbody tr:hover td { background: var(--surface2); }

    .badge-status { padding: 3px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 600; }
    .badge-paid    { background: rgba(52,211,153,.12); color: var(--green); }
    .badge-pending { background: rgba(251,146,60,.12);  color: var(--orange); }
    .badge-failed  { background: rgba(248,113,113,.12); color: var(--red); }

    /* ── Quick actions ── */
    .quick-action {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 18px 14px;
        text-align: center;
        text-decoration: none;
        color: var(--text);
        display: block;
        transition: all .2s;
    }

    .quick-action:hover {
        border-color: var(--accent);
        background: rgba(79,142,247,.06);
        color: var(--accent);
        transform: translateY(-2px);
    }

    .qa-icon {
        width: 40px; height: 40px;
        border-radius: 11px;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 10px;
        font-size: 1.1rem;
        transition: all .2s;
    }

    .quick-action:hover .qa-icon { transform: scale(1.1); }
    .quick-action span { font-size: 0.78rem; font-weight: 500; display: block; }

    /* ── Activity ── */
    .activity-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 18px 20px;
    }

    .activity-item {
        display: flex;
        gap: 12px;
        padding: 11px 0;
        border-bottom: 1px solid var(--border);
    }

    .activity-item:last-child { border-bottom: none; padding-bottom: 0; }

    .activity-dot {
        width: 34px; height: 34px;
        border-radius: 9px;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.85rem;
        flex-shrink: 0;
    }

    .activity-msg  { font-size: 0.82rem; color: var(--text); line-height: 1.4; }
    .activity-time { font-size: 0.69rem; color: var(--muted); margin-top: 3px; display: flex; align-items: center; gap: 4px; }

    /* ── Profile card (for customer role) ── */
    .profile-summary {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .profile-av {
        width: 56px; height: 56px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--accent), var(--accent2));
        display: flex; align-items: center; justify-content: center;
        font-family: 'Syne', sans-serif;
        font-weight: 700; font-size: 1.2rem; color: #fff;
        flex-shrink: 0;
    }

    .profile-info { flex: 1; }
    .profile-name { font-family: 'Syne', sans-serif; font-weight: 700; font-size: 1.1rem; }
    .profile-meta { font-size: 0.78rem; color: var(--muted); margin-top: 4px; display: flex; flex-wrap: wrap; gap: 12px; }
    .profile-meta span { display: flex; align-items: center; gap: 5px; }

    .profile-actions { display: flex; gap: 8px; flex-wrap: wrap; }
    .btn-profile {
        padding: 7px 16px;
        border-radius: 9px;
        font-size: 0.78rem;
        font-weight: 600;
        text-decoration: none;
        border: 1px solid var(--border);
        background: var(--surface2);
        color: var(--text);
        transition: all .2s;
        display: flex; align-items: center; gap: 6px;
    }
    .btn-profile:hover { border-color: var(--accent); color: var(--accent); }
    .btn-profile.primary { background: var(--accent); border-color: var(--accent); color: #fff; }
    .btn-profile.primary:hover { background: #3a7de8; }
</style>
@endsection

@section('content')


@php
    $user     = auth()->user();

    // If session expired or unauthenticated, redirect to login with expiry message
    if (! $user) {
        session()->flash('error', 'Your session has expired. Please log in again.');
        header('Location: ' . route('login'));
        exit;
    }

    $role     = $user->role ?? 'customer';
    $name     = $user->name ?? 'there';
    $hour     = now()->hour;
    $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');

    $roleIcon = match($role) {
        'admin'    => 'bi-shield-fill',
        'staff'    => 'bi-person-badge-fill',
        'customer' => 'bi-person-fill',
        default    => 'bi-person-fill',
    };
@endphp

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- ── Greeting Banner ── --}}
    <div class="greeting-banner">
        <div class="greeting-left">
            <div class="greeting-title">{{ $greeting }}, {{ $name }} 👋</div>
            <div class="greeting-sub">
                @if($role === 'admin')
                    You have full control of the system. Here's your overview.
                @elseif($role === 'staff')
                    Here's what needs your attention today.
                @else
                    Welcome back! Here's your account summary.
                @endif
            </div>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="greeting-role-badge badge-{{ $role }}">
                <i class="bi {{ $roleIcon }}"></i>
                {{ ucfirst($role) }}
            </span>
            <a href="{{ route('profile') }}" class="btn-profile">
                <i class="bi bi-person-circle"></i> View Profile
            </a>
        </div>
    </div>

    
    @if($role === 'admin' || $role === 'staff')
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(79,142,247,.12);color:var(--accent);">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="stat-value">1,284</div>
                <div class="stat-label">Total Users</div>
                <div class="stat-change change-up"><i class="bi bi-arrow-up-short"></i> 12% this month</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(52,211,153,.12);color:var(--green);">
                    <i class="bi bi-bag-check-fill"></i>
                </div>
                <div class="stat-value">348</div>
                <div class="stat-label">Orders Today</div>
                <div class="stat-change change-up"><i class="bi bi-arrow-up-short"></i> 8% vs yesterday</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(167,139,250,.12);color:var(--accent2);">
                    <i class="bi bi-currency-dollar"></i>
                </div>
                <div class="stat-value">$9.4k</div>
                <div class="stat-label">Revenue</div>
                <div class="stat-change change-up"><i class="bi bi-arrow-up-short"></i> 5% this week</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(52,211,153,.12);color:var(--green);">
                    <i class="bi bi-lightning-charge-fill"></i>
                </div>
                <div class="stat-value">56</div>
                <div class="stat-label">Active Leads</div>
                <div class="stat-change change-neutral"><i class="bi bi-dash"></i> 4 converted</div>
            </div>
        </div>
    </div>
    @endif

    
    @if($role === 'customer')
    <div class="profile-summary">
        <div class="profile-av">{{ strtoupper(substr($name, 0, 1)) }}</div>
        <div class="profile-info">
            <div class="profile-name">{{ $name }}</div>
            <div class="profile-meta">
                <span><i class="bi bi-envelope-fill"></i> {{ $user->email }}</span>
                @if($user->phone)
                <span><i class="bi bi-telephone-fill"></i> {{ $user->phone }}</span>
                @endif
                @if($user->address)
                <span><i class="bi bi-geo-alt-fill"></i> {{ $user->address }}</span>
                @endif
            </div>
        </div>
        <div class="profile-actions">
            <a href="{{ route('profile') }}" class="btn-profile primary">
                <i class="bi bi-pencil-fill"></i> Edit Profile
            </a>
            <a href="{{ route('orders.index') }}" class="btn-profile">
                <i class="bi bi-bag-fill"></i> My Orders
            </a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(79,142,247,.12);color:var(--accent);">
                    <i class="bi bi-bag-fill"></i>
                </div>
                <div class="stat-value">12</div>
                <div class="stat-label">My Orders</div>
                <div class="stat-change change-neutral"><i class="bi bi-dash"></i> 2 pending</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(52,211,153,.12);color:var(--green);">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div class="stat-value">9</div>
                <div class="stat-label">Delivered</div>
                <div class="stat-change change-up"><i class="bi bi-arrow-up-short"></i> All on time</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(167,139,250,.12);color:var(--accent2);">
                    <i class="bi bi-star-fill"></i>
                </div>
                <div class="stat-value">4.8</div>
                <div class="stat-label">Avg Rating</div>
                <div class="stat-change change-up"><i class="bi bi-arrow-up-short"></i> Great feedback</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(251,146,60,.12);color:var(--orange);">
                    <i class="bi bi-arrow-repeat"></i>
                </div>
                <div class="stat-value">3</div>
                <div class="stat-label">Returns</div>
                <div class="stat-change change-neutral"><i class="bi bi-dash"></i> 1 in progress</div>
            </div>
        </div>
    </div>
    @endif

    {{-- ── Quick Actions ── --}}
    <div class="mb-4">
        <div class="section-header">
            <div class="section-title"><i class="bi bi-lightning-charge-fill"></i> Quick Actions</div>
        </div>
        <div class="row g-3">
            @if($role === 'admin')
            <div class="col-6 col-md-3">
                <a href="{{ route('users.create') }}" class="quick-action">
                    <div class="qa-icon" style="background:rgba(79,142,247,.12);color:var(--accent)"><i class="bi bi-person-plus-fill"></i></div>
                    <span>Add User</span>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="#" class="quick-action">
                    <div class="qa-icon" style="background:rgba(52,211,153,.12);color:var(--green)"><i class="bi bi-plus-square-fill"></i></div>
                    <span>New Order</span>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="{{ route('products.create') }}" class="quick-action">
                    <div class="qa-icon" style="background:rgba(167,139,250,.12);color:var(--accent2)"><i class="bi bi-box-seam-fill"></i></div>
                    <span>Add Product</span>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="{{ route('profile') }}" class="quick-action">
                    <div class="qa-icon" style="background:rgba(251,146,60,.12);color:var(--orange)"><i class="bi bi-person-circle"></i></div>
                    <span>My Profile</span>
                </a>
            </div>
            @elseif($role === 'staff')
            <div class="col-6 col-md-3">
                <a href="#" class="quick-action">
                    <div class="qa-icon" style="background:rgba(79,142,247,.12);color:var(--accent)"><i class="bi bi-lightning-fill"></i></div>
                    <span>Add Lead</span>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="#" class="quick-action">
                    <div class="qa-icon" style="background:rgba(52,211,153,.12);color:var(--green)"><i class="bi bi-person-lines-fill"></i></div>
                    <span>Customers</span>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="#" class="quick-action">
                    <div class="qa-icon" style="background:rgba(167,139,250,.12);color:var(--accent2)"><i class="bi bi-bag-fill"></i></div>
                    <span>View Orders</span>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="{{ route('profile') }}" class="quick-action">
                    <div class="qa-icon" style="background:rgba(251,146,60,.12);color:var(--orange)"><i class="bi bi-person-circle"></i></div>
                    <span>My Profile</span>
                </a>
            </div>
            @else
            <div class="col-6 col-md-3">
                <a href="#" class="quick-action">
                    <div class="qa-icon" style="background:rgba(79,142,247,.12);color:var(--accent)"><i class="bi bi-bag-fill"></i></div>
                    <span>My Orders</span>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="#" class="quick-action">
                    <div class="qa-icon" style="background:rgba(52,211,153,.12);color:var(--green)"><i class="bi bi-headset"></i></div>
                    <span>Support</span>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="{{ route('profile') }}" class="quick-action">
                    <div class="qa-icon" style="background:rgba(167,139,250,.12);color:var(--accent2)"><i class="bi bi-person-circle"></i></div>
                    <span>Edit Profile</span>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="#" class="quick-action">
                    <div class="qa-icon" style="background:rgba(251,146,60,.12);color:var(--orange)"><i class="bi bi-star-fill"></i></div>
                    <span>Reviews</span>
                </a>
            </div>
            @endif
        </div>
    </div>

    
    @if($role === 'admin' || $role === 'staff')
    <div class="row g-3">
        <div class="col-lg-7">
            <div class="table-card">
                <div class="table-card-header">
                    <div class="section-title mb-0"><i class="bi bi-bag-fill"></i> Recent Orders</div>
                    <a href="#" class="btn-view-all">View all <i class="bi bi-arrow-right"></i></a>
                </div>
                <div class="table-responsive">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td>#ORD-001</td><td>Alice Johnson</td><td>$120.00</td><td><span class="badge-status badge-paid">Paid</span></td></tr>
                            <tr><td>#ORD-002</td><td>Bob Smith</td><td>$85.50</td><td><span class="badge-status badge-pending">Pending</span></td></tr>
                            <tr><td>#ORD-003</td><td>Carol White</td><td>$240.00</td><td><span class="badge-status badge-paid">Paid</span></td></tr>
                            <tr><td>#ORD-004</td><td>David Lee</td><td>$60.00</td><td><span class="badge-status badge-failed">Failed</span></td></tr>
                            <tr><td>#ORD-005</td><td>Eva Martinez</td><td>$310.00</td><td><span class="badge-status badge-pending">Pending</span></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="activity-card">
                <div class="section-header mb-3">
                    <div class="section-title mb-0"><i class="bi bi-activity"></i> Recent Activity</div>
                    <a href="#" class="btn-view-all">View all <i class="bi bi-arrow-right"></i></a>
                </div>

                <div class="activity-item">
                    <div class="activity-dot" style="background:rgba(79,142,247,.12);color:var(--accent)">
                        <i class="bi bi-person-check-fill"></i>
                    </div>
                    <div>
                        <div class="activity-msg">New user <strong>Alice Johnson</strong> registered</div>
                        <div class="activity-time"><i class="bi bi-clock"></i> 2 minutes ago</div>
                    </div>
                </div>

                <div class="activity-item">
                    <div class="activity-dot" style="background:rgba(52,211,153,.12);color:var(--green)">
                        <i class="bi bi-bag-check-fill"></i>
                    </div>
                    <div>
                        <div class="activity-msg">Order <strong>#ORD-003</strong> marked as paid</div>
                        <div class="activity-time"><i class="bi bi-clock"></i> 15 minutes ago</div>
                    </div>
                </div>

                <div class="activity-item">
                    <div class="activity-dot" style="background:rgba(248,113,113,.12);color:var(--red)">
                        <i class="bi bi-x-circle-fill"></i>
                    </div>
                    <div>
                        <div class="activity-msg">Order <strong>#ORD-004</strong> payment failed</div>
                        <div class="activity-time"><i class="bi bi-clock"></i> 1 hour ago</div>
                    </div>
                </div>

                <div class="activity-item">
                    <div class="activity-dot" style="background:rgba(167,139,250,.12);color:var(--accent2)">
                        <i class="bi bi-lightning-fill"></i>
                    </div>
                    <div>
                        <div class="activity-msg">Lead <strong>Rahul Kumar</strong> converted to customer</div>
                        <div class="activity-time"><i class="bi bi-clock"></i> 3 hours ago</div>
                    </div>
                </div>

                <div class="activity-item">
                    <div class="activity-dot" style="background:rgba(251,146,60,.12);color:var(--orange)">
                        <i class="bi bi-bell-fill"></i>
                    </div>
                    <div>
                        <div class="activity-msg">Maintenance scheduled for <strong>Sunday</strong></div>
                        <div class="activity-time"><i class="bi bi-clock"></i> 5 hours ago</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ── CUSTOMER: My recent orders ── --}}
    @if($role === 'customer')
    <div class="table-card">
        <div class="table-card-header">
            <div class="section-title mb-0"><i class="bi bi-bag-fill"></i> My Orders</div>
            <a href="#" class="btn-view-all">View all <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Product</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td>#ORD-089</td><td>Widget Pro Max</td><td>$120.00</td><td><span class="badge-status badge-paid">Delivered</span></td><td style="color:var(--muted)">12 Mar 2025</td></tr>
                    <tr><td>#ORD-076</td><td>Gadget Basic</td><td>$45.00</td><td><span class="badge-status badge-pending">Pending</span></td><td style="color:var(--muted)">08 Mar 2025</td></tr>
                    <tr><td>#ORD-061</td><td>Smart Device X</td><td>$299.00</td><td><span class="badge-status badge-paid">Delivered</span></td><td style="color:var(--muted)">01 Mar 2025</td></tr>
                </tbody>
            </table>
        </div>
    </div>
    @endif

@endsection