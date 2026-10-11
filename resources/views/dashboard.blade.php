@extends('layouts.app')

@section('header_title', 'Business Analytics')

@section('content')
    <!-- Top Metric Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

        <!-- Revenue Card -->
        <div class="bg-white border border-gray-200 rounded-3xl p-6 flex flex-col justify-center shadow-sm hover:shadow-md transition-shadow">
            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Total Revenue</h4>
            <span class="text-3xl font-black text-emerald-600">₱{{ number_format($totalRevenue, 2) }}</span>
        </div>

        <!-- Production Card -->
        <div class="bg-white border border-gray-200 rounded-3xl p-6 flex flex-col justify-center shadow-sm hover:shadow-md transition-shadow">
            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">In Production</h4>
            <span class="text-3xl font-black text-blue-600">{{ $activeProduction }}</span>
        </div>

        <!-- Deliveries Card -->
        <div class="bg-white border border-gray-200 rounded-3xl p-6 flex flex-col justify-center shadow-sm hover:shadow-md transition-shadow">
            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Pending Dispatches</h4>
            <span class="text-3xl font-black text-purple-600">{{ $pendingDeliveries }}</span>
        </div>

        <!-- Receivables Card -->
        <div class="bg-white border border-gray-200 rounded-3xl p-6 flex flex-col justify-center shadow-sm hover:shadow-md transition-shadow">
            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Awaiting Payments</h4>
            <span class="text-3xl font-black text-amber-500">{{ $pendingPayments }}</span>
        </div>

    </div>

    <!-- Recent Activity Section -->
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 relative">
        <h3 class="text-lg font-bold text-gray-900 mb-6">Recent Order Activity</h3>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-700">
                <thead class="text-xs text-gray-400 uppercase border-b-2 border-gray-200">
                    <tr>
                        <th class="pb-3 px-2 font-bold tracking-wider">PO Number</th>
                        <th class="pb-3 px-2 font-bold tracking-wider">Client</th>
                        <th class="pb-3 px-2 font-bold tracking-wider">Total Value</th>
                        <th class="pb-3 px-2 font-bold tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($recentOrders as $order)
                        @php
                            $statusColor = match($order->order_status) {
                                'Pending Downpayment' => 'bg-amber-50 text-amber-600 border-amber-200',
                                'In Production' => 'bg-blue-50 text-blue-600 border-blue-200',
                                'Ready for Delivery' => 'bg-purple-50 text-purple-600 border-purple-200',
                                'Delivered' => 'bg-orange-50 text-orange-600 border-orange-200',
                                'Completed' => 'bg-emerald-50 text-emerald-600 border-emerald-200',
                                default => 'bg-gray-50 text-gray-600 border-gray-200'
                            };
                        @endphp
                        <tr class="hover:bg-gray-50 hover:shadow-sm relative z-0 hover:z-10 transition-all duration-200 group">
                            <td class="py-4 px-2 font-bold text-gray-900 group-hover:text-blue-600 transition-colors">{{ $order->po_number }}</td>
                            <td class="py-4 px-2">{{ $order->client->client_name }}</td>
                            <td class="py-4 px-2 font-medium text-gray-900">₱{{ number_format($order->total_amount, 2) }}</td>
                            <td class="py-4 px-2">
                                <span class="px-3 py-1 text-[10px] font-bold uppercase tracking-wider border rounded-full {{ $statusColor }}">
                                    {{ $order->order_status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-gray-400">No recent activity.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
