<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use Carbon\Carbon;

class SalesHistoryController extends Controller
{
    // Column on the payments table that holds the date money was received.
    // Change to 'payment_date' / 'paid_at' if your table has one.
    private const DATE_COLUMN = 'created_at';

    public function index(Request $request)
    {
        $col = self::DATE_COLUMN;

        // Years that have data (newest first), always including the current year
        $earliest = Payment::min($col);
        $earliestYear = $earliest ? Carbon::parse($earliest)->year : now()->year;
        $years = range(now()->year, min($earliestYear, now()->year));

        $year = (int) $request->query('year', now()->year);
        if (!in_array($year, $years)) {
            $year = now()->year;
        }

        $start = Carbon::create($year, 1, 1)->startOfYear();
        $end   = $start->copy()->endOfYear();

        $byMonth = Payment::whereBetween($col, [$start, $end])
            ->get(['amount', $col])
            ->groupBy(fn ($p) => Carbon::parse($p->{$col})->month);

        $months = collect(range(1, 12))->map(fn ($m) => [
            'month' => $m,
            'name'  => Carbon::create($year, $m, 1)->format('F'),
            'total' => $byMonth->has($m) ? $byMonth[$m]->sum('amount') : 0,
            'count' => $byMonth->has($m) ? $byMonth[$m]->count() : 0,
        ]);

        $yearTotal = $months->sum('total');

        return view('sales.history', compact('months', 'years', 'year', 'yearTotal'));
    }

    public function show($year, $month)
    {
        abort_unless($month >= 1 && $month <= 12, 404);

        $col   = self::DATE_COLUMN;
        $start = Carbon::create((int) $year, (int) $month, 1)->startOfMonth();
        $end   = $start->copy()->endOfMonth();

        $payments = Payment::with('order.client')
            ->whereBetween($col, [$start, $end])
            ->orderBy($col, 'desc')
            ->get();

        $monthName = $start->format('F Y');
        $total     = $payments->sum('amount');

        return view('sales.history-month', compact('payments', 'monthName', 'total', 'year', 'col'));
    }
}

