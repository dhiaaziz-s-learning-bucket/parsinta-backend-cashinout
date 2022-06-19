<?php

namespace App\Http\Controllers;

use App\Http\Resources\CashResource;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

// use Dotenv\Util\Str;

// use Illuminate\Http\Request;

class CashController extends Controller
{
    public function index()
    {
        $debit = Auth::user()->cashes()
            ->whereBetween('when', [now()->firstOfMonth(), now()])
            ->where('amount', '>=', 0)
            ->get('amount')->sum('amount');
        $credit = Auth::user()->cashes()
            ->whereBetween('when', [now()->firstOfMonth(), now()])
            ->where('amount', '<', 0)
            ->get('amount')->sum('amount');

        $balances = Auth::user()->cashes()->get('amount')->sum('amount');
        $transactions = Auth::user()->cashes()
            ->whereBetween('when', [now()->firstOfMonth(), now()])
            ->latest()->get();
        // dd($transactions);
        return response()->json([
            'debit' => formatPrice($debit),
            'credit' => formatPrice($credit),
            'balances' => formatPrice($balances),
            'transactions' => CashResource::collection($transactions),
        ]);
    }

    public function store()
    {
        request()->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric',
            // 'type' => 'required|string|max:255',
            // 'date' => 'required|date',
        ]);
        $when = request('when') ?? now();
        $slug = request('name') . '-' . Str::random(6);
        Auth::user()->cashes()->create([
            'name' => request('name'),
            'slug' => Str::slug(request('name')),
            'amount' => request('amount'),
            'when' => $when,
            'description' => request('description'),
        ]);
        return response()->json([
            'message' => 'Transaction has been saved',
        ]);
    }
}
