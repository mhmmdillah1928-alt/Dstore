<?php

namespace App\Http\Controllers;

use App\Models\Dispute;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = $request->user()->orders()->with('store', 'items.product')->latest()->paginate(10);
        return view('orders.index', compact('orders'));
    }

    public function show(Request $request, Order $order)
    {
        $this->own($request, $order);
        $order->load('store', 'items.product.images', 'items.review', 'shipment', 'dispute');
        return view('orders.show', compact('order'));
    }

    public function payment(Request $request, Order $order)
    {
        $this->own($request, $order);
        abort_unless(
            $order->payment_status === 'pending'
            && $order->order_status === 'pending',
            422
        );

        return view('orders.payment', compact('order'));
    }

    public function submitPaymentProof(Request $request, Order $order)
    {
        $this->own($request, $order);
        abort_unless(
            $order->payment_status === 'pending'
            && $order->order_status === 'pending'
            && in_array($order->payment_review_status, ['not_submitted', 'rejected'], true),
            422
        );

        $data = $request->validate([
            'payment_proof' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $previousProof = $order->payment_proof_path;
        $proofPath = $data['payment_proof']->store('payment-proofs', 'local');

        $order->update([
            'payment_proof_path' => $proofPath,
            'payment_review_status' => 'pending_review',
            'payment_rejection_reason' => null,
        ]);

        if ($previousProof) {
            Storage::disk('local')->delete($previousProof);
        }

        return back()->with('success', 'Bukti transfer berhasil dikirim dan sedang menunggu persetujuan penjual.');
    }

    public function cancel(Request $request, Order $order)
    {
        $this->own($request, $order);
        abort_unless($order->order_status === 'pending' && $order->payment_status === 'pending', 422);
        DB::transaction(function () use ($order) {
            foreach ($order->items as $i) {
                $i->product()->increment('stock', $i->qty);
            }
            $order->update(['order_status' => 'cancelled', 'payment_status' => 'failed']);
        });
        return back()->with('success', 'Pesanan dibatalkan.');
    }

    public function complete(Request $request, Order $order)
    {
        $this->own($request, $order);
        abort_unless($order->order_status === 'shipped' && !$order->dispute()->where('status', 'open')->exists(), 422);

        DB::transaction(function () use ($order) {
            $order->update(['order_status' => 'completed']);
            // Dana masuk saldo toko setelah dipotong komisi
            $order->store()->increment('balance', $order->total_amount - $order->commission_amount);
        });

        return back()->with('success', 'Pesanan selesai. Silakan beri ulasan.');
    }

    public function review(Request $request, OrderItem $item)
    {
        $order = $item->order;
        $this->own($request, $order);
        abort_unless($order->order_status === 'completed' && !$item->review, 422);

        $data = $request->validate(['rating' => 'required|integer|between:1,5', 'comment' => 'nullable|string|max:1000']);
        $item->review()->create($data + ['user_id' => $request->user()->id, 'product_id' => $item->product_id]);

        return back()->with('success', 'Terima kasih atas ulasan Anda.');
    }

    public function dispute(Request $request, Order $order)
    {
        $this->own($request, $order);
        abort_unless($order->order_status === 'shipped' && !$order->dispute, 422);

        $data = $request->validate(['reason' => 'required|string|min:10']);
        Dispute::create(['order_id' => $order->id, 'user_id' => $request->user()->id, 'reason' => $data['reason']]);

        return back()->with('success', 'Pengajuan retur/sengketa dikirim ke admin.');
    }

    private function own(Request $request, Order $order): void
    {
        abort_unless($order->customer_id === $request->user()->id, 403);
    }
}
