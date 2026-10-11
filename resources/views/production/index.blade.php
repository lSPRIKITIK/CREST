@extends('layouts.app')

@section('header_title', 'Active Production')

@section('content')
    @php
        $canEdit = auth()->user()->employee->role === 'Production Manager';
    @endphp

    <div class="bg-white border border-gray-200 rounded-3xl shadow-sm p-8 relative">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h3 class="text-xl font-black text-gray-900">Factory Floor</h3>
                <p class="text-sm text-gray-500 mt-1">Orders currently being manufactured. Click a row to view production requirements.</p>
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
                        @if($canEdit)
                            <th class="pb-3 px-2 font-bold tracking-wider text-right">Action</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($orders as $order)
                    <tr onclick="openModal({{ $order->order_id }})" class="cursor-pointer hover:bg-gray-50 hover:shadow-md relative z-0 hover:z-10 transition-all duration-200 group">
                        <td class="py-5 px-2 font-bold text-gray-900 group-hover:text-blue-600 transition-colors">{{ $order->po_number }}</td>
                        <td class="py-5 px-2 font-medium">{{ $order->client ? $order->client->client_name : 'N/A' }}</td>

                        <td class="py-5 px-2 font-bold text-blue-600">
                            {{ $order->due_date ? \Carbon\Carbon::parse($order->due_date)->format('M d, Y') : 'N/A' }}
                        </td>

                        <td class="py-5 px-2">
                            <span class="px-3 py-1 text-[10px] font-bold uppercase tracking-wider border rounded-full
                                {{ $order->order_status === 'Prototype Approval' ? 'bg-amber-50 text-amber-600 border-amber-200' : 'bg-blue-50 text-blue-600 border-blue-200' }}">
                                {{ $order->order_status }}
                            </span>
                        </td>
                        @if($canEdit)
                            <td class="py-5 px-2 text-right" onclick="event.stopPropagation()">
                                <form action="{{ route('production.update', $order->order_id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="bg-gray-900 text-white px-5 py-2 rounded-xl text-xs font-bold hover:bg-gray-800 transition-colors shadow-sm">
                                        {{ $order->order_status === 'Prototype Approval' ? 'Approve Prototype' : 'Mark Ready for Delivery' }}
                                    </button>
                                </form>
                            </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ $canEdit ? '5' : '4' }}" class="py-12 text-center text-gray-400 font-medium">No orders are currently in production.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- The Production Details Modal -->
    <div id="production-modal" class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm z-50 flex items-center justify-center hidden">
        <div class="bg-white border border-gray-200 w-full max-w-4xl rounded-3xl shadow-2xl overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-white">
                <div>
                    <h2 class="text-xl font-black tracking-tight text-gray-900" id="modal-po">PO Number</h2>
                    <p class="text-sm text-gray-500 font-medium mt-1" id="modal-client">Client Name</p>
                </div>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 p-2 rounded-full transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="p-6 bg-gray-50">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Manufacturing Requirements</h3>

                <div class="border border-gray-200 bg-white rounded-xl overflow-hidden shadow-sm">
                    <table class="w-full text-left text-sm text-gray-700">
                        <thead class="text-xs text-gray-500 uppercase border-b border-gray-100 bg-gray-50/50">
                            <tr>
                                <th class="p-4 font-bold tracking-wider">Item Name</th>
                                <th class="p-4 font-bold tracking-wider">Description / Specifications</th>
                                <th class="p-4 font-bold tracking-wider text-center">Qty</th>
                            </tr>
                        </thead>
                        <tbody id="modal-items-body" class="divide-y divide-gray-100">
                            <!-- Injected via JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="p-4 border-t border-gray-100 bg-white flex justify-end">
                <button onclick="closeModal()" class="bg-gray-900 text-white font-medium px-6 py-2.5 rounded-xl hover:bg-gray-800 transition-colors">Close Window</button>
            </div>
        </div>
    </div>

    <!-- JavaScript to populate the modal -->
    <script>
        const ordersData = @json($orders->keyBy('order_id'));

        function openModal(orderId) {
            const order = ordersData[orderId];
            if (!order) return;

            document.getElementById('modal-po').innerText = "Order: " + order.po_number;

            let deadlineText = "N/A";
            if (order.due_date) {
                deadlineText = new Date(order.due_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            }
            const clientName = order.client ? order.client.client_name : 'N/A';
            document.getElementById('modal-client').innerText = "Client: " + clientName + " | Deadline: " + deadlineText;

            const tbody = document.getElementById('modal-items-body');
            tbody.innerHTML = '';

            if (order.items && order.items.length > 0) {
                order.items.forEach(item => {

                   const itemName = item.item_type || 'N/A'; // Changed to item_type
                    const description = item.specifications || '<span class="text-gray-400 italic">No additional specifications provided.</span>'; // Changed to specifications

                    const qty = item.quantity || 1;

                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td class="p-4 font-bold text-gray-900 w-1/4">${itemName}</td>
                        <td class="p-4 text-gray-600">${description}</td>
                        <td class="p-4 text-center font-black text-blue-600 text-lg w-16">${qty}</td>
                    `;
                    tbody.appendChild(tr);
                });
            } else {
                tbody.innerHTML = `<tr><td colspan="3" class="p-8 text-center text-gray-400 text-xs italic">No items listed for this order.</td></tr>`;
            }

            document.getElementById('production-modal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('production-modal').classList.add('hidden');
        }

        document.getElementById('production-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>
@endsection
