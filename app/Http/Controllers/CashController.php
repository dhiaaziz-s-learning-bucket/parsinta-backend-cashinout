<?php

namespace App\Http\Controllers;

use App\Models\Cash;
use Illuminate\Support\Str;
use App\Http\Resources\CashResource;
use Illuminate\Support\Facades\Auth;

// use Dotenv\Util\Str;

// use Illuminate\Http\Request;

class CashController extends Controller
{
    public function getBalances($from, $to, $operator)
    {
        return Auth::user()->cashes()
            ->whereBetween('when', [$from, $to])
            ->where('amount', $operator, 0)
            ->get('amount')->sum('amount');
    }
    public function index()
    {
        $from = request('from');
        $to = request('to');

        if ($from && $to) {
            $debit = $this->getBalances($from, $to, '>=');
            $credit = $this->getBalances($from, $to, '<');

            $balances = Auth::user()->cashes()->get('amount')->sum('amount');
            $transactions = Auth::user()->cashes()
                ->whereBetween('when', [$from, $to])
                ->latest()->get();
        } else {
            $debit = $this->getBalances(now()->firstOfMonth(), now(), '>=');
            $credit = $this->getBalances(now()->firstOfMonth(), now(), '<');

            $balances = Auth::user()->cashes()->get('amount')->sum('amount');
            $transactions = Auth::user()->cashes()
                ->whereBetween('when', [now()->firstOfMonth(), now()])
                ->latest()->get();
        }

        // dd($transactions);
        return response()->json([
            'debit' => formatPrice($debit),
            'credit' => formatPrice($credit),
            'balances' => formatPrice($balances),
            'transactions' => CashResource::collection($transactions),
            'now' => now()->format("Y-m-d"),
            'firstOfMonth' => now()->firstOfMonth()->format("Y-m-d"),
            'from' => $from,
            'to' => $to,
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
        $cash = Auth::user()->cashes()->create([
            'name' => request('name'),
            'slug' => Str::slug($slug),
            'amount' => request('amount'),
            'when' => $when,
            'description' => request('description'),
        ]);
        return response()->json([
            'message' => 'Transaction has been saved',
            'cash' => new CashResource($cash),
        ]);
    }

    public function show(Cash $cash)
    {   
        // if(Auth::id() !== $cash->user_id){
        //     abort(403);
        // }
        $this->authorize('show', $cash);
        return new CashResource($cash);
    }
}
