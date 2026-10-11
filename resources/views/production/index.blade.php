@extends('layouts.app')

@section('header_title', 'Active Production')

@section('content')
    <div class="bg-white border border-gray-200 rounded-3xl shadow-sm p-8 relative">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h3 class="text-xl font-black text-gray-900">Factory Floor</h3>
                <p class="text-sm text-gray-500 mt-1">Orders currently being manufactured.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 font-medium text-sm p-4 rounded-xl mb-6">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-700">
                <thead class="text-xs text-gray-400 uppercase border-b-2 border-gray-200">
                    <tr>
                        <th class="pb-3 px-2 font-bold tracking-wider">PO Number</th>
                        <th class="pb-3 px-2 font-bold tracking-wider">Client</th>
                        <th class="pb-3 px-2 font-bold tracking-wider">Production Deadline</th>
                        <th class="pb-3 px-2 font-bold tracking-wider">Current Status</th>
                        <th class="pb-3 px-2 font-bold tracking-wider text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($orders as $order)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="py-5 px-2 font-bold text-gray-900">{{ $order->po_number }}</td>
                        <td class="py-5 px-2 font-medium">{{ $order->client->client_name }}</td>
                        <td class="py-5 px-2 font-bold text-blue-600">{{ \Carbon\Carbon::parse($order->deadline)->format('M d, Y') }}</td>
                        <td class="py-5 px-2">
                            <span class="px-3 py-1 text-[10px] font-bold uppercase tracking-wider border rounded-full bg-blue-50 text-blue-600 border-blue-200">
                                {{ $order->order_status }}
                            </span>
                        </td>
                        <td class="py-5 px-2 text-right">
                            <form action="{{ route('production.update', $order->order_id) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="bg-gray-900 text-white px-5 py-2 rounded-xl text-xs font-bold hover:bg-gray-800 transition-colors shadow-sm">
                                    Mark Ready for Delivery
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-gray-400 font-medium">No orders are currently in production.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

