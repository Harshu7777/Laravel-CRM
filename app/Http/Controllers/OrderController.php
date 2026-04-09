<?php

namespace App\Http\Controllers;

use App\Mail\InvoicePaid;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    // ── List Orders ──────────────────────────────────────────
    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'customer') {
            $orders = Order::with('items')
                ->where('user_id', $user->id)
                ->latest()
                ->get();
        } else {
            // Admin & Staff: sab orders
            $orders = Order::with('items')->latest()->get();
        }

        return view('orders.index', compact('orders'));
    }

    // ── Show ─────────────────────────────────────────────────
    public function show(string $id)
    {
        $user  = auth()->user();
        $query = Order::with('items');

        // Customer sirf apna order dekh sake
        if ($user->role === 'customer') {
            $query->where('user_id', $user->id);
        }

        $order = $query->findOrFail($id);
        return view('orders.show', compact('order'));
    }

    // ── Delete (Admin only) ───────────────────────────────────
    public function destroy(string $id)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Only admin can delete orders.');
        }

        $order = Order::findOrFail($id);
        $order->items()->delete();
        $order->delete();

        return redirect()->route('orders.index')->with('success', 'Order deleted successfully.');
    }

    // ── Update Shipping Status (Admin only) ──────────────────
    public function updateShipping(Request $request, string $id)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Only admin can update shipping status.');
        }

        $request->validate([
            'shipping_status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        ]);

        $order = Order::findOrFail($id);
        $order->update(['shipping_status' => $request->shipping_status]);

        return redirect()->back()->with('success', 'Shipping status updated.');
    }

    // ── Download Invoice PDF ──────────────────────────────────
    public function downloadInvoice(Order $order)
    {
        // Customer sirf apna invoice download kare
        if (auth()->user()->role === 'customer' && $order->user_id !== auth()->id()) {
            abort(403);
        }

        $pdf      = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.order-invoice', ['order' => $order]);
        $fileName = 'invoice-' . str_pad($order->id, 6, '0', STR_PAD_LEFT) . '.pdf';

        return $pdf->stream($fileName, ['Attachment' => false]);
    }
}