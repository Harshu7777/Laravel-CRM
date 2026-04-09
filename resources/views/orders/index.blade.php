@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>
                @if (auth()->user()->role === 'customer')
                    My Orders
                @else
                    All Orders
                @endif
            </h1>
            @if (auth()->user()->role === 'admin')
                <span class="badge bg-danger">Admin View</span>
            @elseif(auth()->user()->role === 'staff')
                <span class="badge bg-warning text-dark">Staff View</span>
            @endif
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($orders->isEmpty())
            <div class="alert alert-info text-center py-4">
                <i class="bi bi-inbox" style="font-size:2.5rem;opacity:0.6;"></i>
                <p class="mt-3 mb-0">No orders found.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            @if (auth()->user()->role === 'admin' || auth()->user()->role === 'staff')
                                <th>Customer</th>
                            @endif
                            <th>Total Amount</th>
                            <th>Payment Status</th>
                            <th>Shipping Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td><strong>#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</strong></td>
                                <td>{{ $order->created_at->format('d M, Y') }}</td>

                                {{-- Show customer only for admin & staff --}}
                                @if (auth()->user()->role !== 'customer')
                                    <td>
                                        {{ $order->full_name }}<br>
                                        <small class="text-muted">{{ $order->email }}</small>
                                    </td>
                                @endif

                                <td><strong>₹{{ number_format($order->total_amount, 2) }}</strong></td>

                                {{-- Payment Status --}}
                                <td>
                                    <span
                                        class="badge
                    @if ($order->payment_status == 'paid') bg-success
                    @elseif($order->payment_status == 'pending_payment') bg-warning
                    @else bg-danger @endif">
                                        {{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}
                                    </span>
                                </td>

                                {{-- Shipping Status --}}
                                <td>
                                    @if (auth()->user()->role === 'admin')
                                        <form action="{{ route('orders.updateShipping', $order->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('PATCH')

                                            <div class="input-group input-group-sm" style="min-width: 200px;">
                                                <select name="shipping_status" class="form-select form-select-sm">
                                                    @foreach (['pending', 'processing', 'shipped', 'delivered', 'cancelled'] as $status)
                                                        <option value="{{ $status }}"
                                                            {{ $order->shipping_status === $status ? 'selected' : '' }}>
                                                            {{ ucfirst($status) }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                                <button type="submit" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-check2"></i>
                                                </button>
                                            </div>
                                        </form>
                                    @else
                                        <span
                                            class="badge 
                        @if ($order->shipping_status == 'delivered') bg-success
                        @elseif($order->shipping_status == 'shipped') bg-primary
                        @elseif($order->shipping_status == 'processing') bg-warning text-dark
                        @elseif($order->shipping_status == 'cancelled') bg-danger
                        @else bg-secondary @endif">
                                            {{ ucfirst($order->shipping_status ?? 'pending') }}
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-primary">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
