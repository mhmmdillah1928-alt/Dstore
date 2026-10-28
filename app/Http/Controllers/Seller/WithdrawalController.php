<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WithdrawalController extends Controller
{
    public function index(Request $request)
    {
        $store = $request->user()->store;
        return view('seller.withdrawals', [
            'store' => $store,
            'withdrawals' => $store->withdrawals()->latest()->paginate(10),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate(['amount' => 'required|numeric|min:10000']);

        // Saldo dipotong saat pengajuan; dikembalikan bila ditolak admin
        $ok = DB::transaction(function () use ($request) {
            $store = $request->user()->store()->lockForUpdate()->first();
            if ($store->balance < $request->amount) {
                return false;
            }
            $store->decrement('balance', $request->amount);
            $store->withdrawals()->create(['amount' => $request->amount]);
            return true;
        });

        return back()->with($ok ? 'success' : 'error', $ok ? 'Pengajuan penarikan dikirim.' : 'Saldo tidak mencukupi.');
    }
}
