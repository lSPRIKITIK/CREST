@extends('layouts.app')

@section('header_title', 'Sales History')

@section('content')
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6">

        <!-- Header + Year Filter -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h3 class="text-lg font-bold text-gray-900">Monthly Sales</h3>
                <p class="text-xs text-gray-400 mt-1">Click a month to see its payments.</p>
            </div>

            <form method="GET" action="{{ route('sales.history') }}" class="flex items-center gap-2">
                <label for="year" class="text-xs font-bold text-gray-400 uppercase tracking-wider">Year</label>
                <select id="year" name="year" onchange="this.form.submit()"
                        class="border border-gray-200 rounded-xl px-3 py-2 text-sm font-bold text-gray-900 bg-white focus:outline-none focus:border-gray-900">
                    @foreach($years as $y)
                        <option value="{{ $y }}" @selected($y == $year)>{{ $y }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-700">
                <thead class="text-xs text-gray-400 uppercase border-b-2 border-gray-200">
                    <tr>
                        <th class="pb-3 px-2 font-bold tracking-wider">Month</th>
                        <th class="pb-3 px-2 font-bold tracking-wider">Payments</th>
                        <th class="pb-3 px-2 font-bold tracking-wider text-right">Total Sales</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($months as $m)
                        <tr class="hover:bg-gray-50 transition-colors group {{ $m['count'] === 0 ? 'text-gray-400' : '' }}">
                            <td class="py-4 px-2 font-bold">
                                <a href="{{ route('sales.history.month', ['year' => $year, 'month' => $m['month']]) }}"
                                   class="block text-gray-900 group-hover:text-blue-600 transition-colors">
                                    {{ $m['name'] }} {{ $year }}
                                </a>
                            </td>
                            <td class="py-4 px-2">{{ $m['count'] }}</td>
                            <td class="py-4 px-2 text-right font-medium {{ $m['count'] === 0 ? '' : 'text-gray-900' }}">
                                ₱{{ number_format($m['total'], 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="border-t-2 border-gray-200">
                    <tr>
                        <td class="pt-4 px-2 font-black text-gray-900" colspan="2">Total for {{ $year }}</td>
                        <td class="pt-4 px-2 text-right font-black text-emerald-600">₱{{ number_format($yearTotal, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection
