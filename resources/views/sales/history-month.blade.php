@extends('layouts.app')

@section('header_title', 'Sales History')

@section('content')
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <a href="{{ route('sales.history', ['year' => $year]) }}"
                   class="text-xs font-bold text-gray-400 hover:text-gray-900 uppercase tracking-wider transition-colors">
                    &larr; Back to {{ $year }}
                </a>
                <h3 class="text-lg font-bold text-gray-900 mt-2">{{ $monthName }}</h3>
            </div>
            <div class="sm:text-right">
                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Month Total</h4>
                <span class="text-2xl font-black text-emerald-600">₱{{ number_format($total, 2) }}</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-700">
                <thead class="text-xs text-gray-400 uppercase border-b-2 border-gray-200">
                    <tr>
                        <th class="pb-3 px-2 font-bold tracking-wider">Date</th>
                        <th class="pb-3 px-2 font-bold tracking-wider">PO Number</th>
                        <th class="pb-3 px-2 font-bold tracking-wider">Client</th>
                        <th class="pb-3 px-2 font-bold tracking-wider text-right">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($payments as $payment)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="py-4 px-2">{{ \Carbon\Carbon::parse($payment->{$col})->format('M d, Y') }}</td>
                            <td class="py-4 px-2 font-bold text-gray-900">{{ $payment->order?->po_number ?? '—' }}</td>
                            <td class="py-4 px-2">{{ $payment->order?->client?->client_name ?? '—' }}</td>
                            <td class="py-4 px-2 text-right font-medium text-gray-900">₱{{ number_format($payment->amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-gray-400">No payments recorded this month.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
