<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = $request->user()->store->orders()->with('customer', 'items.product')->latest()->paginate(10);
        return view('seller.orders.index', compact('orders'));
    }

    public function show(Request $request, Order $order)
    {
        $this->own($request, $order);
        $order->load('customer', 'items.product', 'shipment', 'dispute');
        return view('seller.orders.show', compact('order'));
    }

    public function paymentProof(Request $request, Order $order)
    {
        $this->own($request, $order);
        abort_unless(
            $order->payment_proof_path
            && Storage::disk('local')->exists($order->payment_proof_path),
            404
        );

        $disk = Storage::disk('local');
        $path = $disk->path($order->payment_proof_path);
        $mimeType = $disk->mimeType($order->payment_proof_path);

        abort_unless($mimeType && str_starts_with($mimeType, 'image/'), 404);

        return response()->file($path, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline',
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function approvePayment(Request $request, Order $order)
    {
        $this->own($request, $order);

        DB::transaction(function () use ($order) {
            $lockedOrder = Order::lockForUpdate()->findOrFail($order->id);
            abort_unless(
                $lockedOrder->payment_status === 'pending'
                && $lockedOrder->order_status === 'pending'
                && $lockedOrder->payment_review_status === 'pending_review'
                && $lockedOrder->payment_proof_path,
                422
            );

            $lockedOrder->update([
                'payment_status' => 'paid',
                'payment_review_status' => 'approved',
                'payment_rejection_reason' => null,
            ]);
        });

        return back()->with('success', 'Pembayaran disetujui. Pesanan siap diproses.');
    }

    public function rejectPayment(Request $request, Order $order)
    {
        $this->own($request, $order);
        $data = $request->validate([
            'payment_rejection_reason' => 'required|string|min:5|max:1000',
        ]);

        DB::transaction(function () use ($order, $data) {
            $lockedOrder = Order::lockForUpdate()->findOrFail($order->id);
            abort_unless(
                $lockedOrder->payment_status === 'pending'
                && $lockedOrder->order_status === 'pending'
                && $lockedOrder->payment_review_status === 'pending_review'
                && $lockedOrder->payment_proof_path,
                422
            );

            $lockedOrder->update([
                'payment_review_status' => 'rejected',
                'payment_rejection_reason' => $data['payment_rejection_reason'],
            ]);
        });

        return back()->with('success', 'Bukti pembayaran ditolak. Customer dapat mengirim bukti transfer yang benar.');
    }

    public function confirm(Request $request, Order $order)
    {
        $this->own($request, $order);
        abort_unless($order->payment_status === 'paid' && $order->order_status === 'pending', 422);
        $order->update(['order_status' => 'processing']);
        return back()->with('success', 'Pesanan dikonfirmasi.');
    }

    public function ship(Request $request, Order $order)
    {
        $this->own($request, $order);
        abort_unless($order->order_status === 'processing', 422);

        $data = $request->validate([
            'courier_name' => 'required|string|max:100',
            'tracking_number' => 'required|string|max:100',
            'shipping_cost' => 'nullable|numeric|min:0',
        ]);
        $data['shipping_cost'] = $data['shipping_cost'] ?? $order->shipment?->shipping_cost ?? 0;

        $order->shipment()->updateOrCreate([], $data);
        $order->update(['order_status' => 'shipped']);

        return back()->with('success', 'Nomor resi disimpan, pesanan berstatus dikirim.');
    }

    private function own(Request $request, Order $order): void
    {
        abort_unless($order->store_id === $request->user()->store->id, 403);
    }
}
