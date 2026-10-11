@extends('layouts.app')

@section('header_title', 'Master Order Tracking')

@section('content')
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 relative">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-bold text-gray-900">All System Orders</h3>
            <p class="text-xs font-medium text-gray-500 bg-gray-100 px-3 py-1 rounded-full">Click any row to view payments</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-700">
                <!-- Darker border-bottom for the header -->
                <thead class="text-xs text-gray-400 uppercase border-b-2 border-gray-200">
                    <tr>
                        <th class="pb-3 px-2 font-bold tracking-wider">Date</th>
                        <th class="pb-3 px-2 font-bold tracking-wider">PO Number</th>
                        <th class="pb-3 px-2 font-bold tracking-wider">Client</th>
                        <th class="pb-3 px-2 font-bold tracking-wider">Total Value</th>
                        <th class="pb-3 px-2 font-bold tracking-wider">Amount Paid</th>
                        <th class="pb-3 px-2 font-bold tracking-wider">Current Status</th>
                    </tr>
                </thead>
                <!-- Darker divide lines between rows -->
                <tbody class="divide-y divide-gray-200">
                    @forelse ($orders as $order)
                    @php
                        $totalPaid =$order->payments_sum_amount ?? 0;

                        $statusColor = match($order->order_status) {
                            'Pending Downpayment' => 'bg-amber-50 text-amber-600 border-amber-200',
                            'In Production' => 'bg-blue-50 text-blue-600 border-blue-200',
                            'Ready for Delivery' => 'bg-purple-50 text-purple-600 border-purple-200',
                            'Delivered' => 'bg-orange-50 text-orange-600 border-orange-200',
                            'Completed' => 'bg-emerald-50 text-emerald-600 border-emerald-200',
                            default => 'bg-gray-50 text-gray-600 border-gray-200'
                        };
                    @endphp
                    <!-- Enhanced hover effect -->
                    <tr onclick="openModal({{ $order->order_id }})" class="cursor-pointer hover:bg-gray-50 hover:shadow-md relative z-0 hover:z-10 transition-all duration-200 group">
                        <td class="py-4 px-2">{{ $order->created_at->format('M d, Y') }}</td>
                        <td class="py-4 px-2 font-bold text-gray-900 group-hover:text-blue-600 transition-colors">{{ $order->po_number }}</td>
                        <td class="py-4 px-2">{{ $order->client->client_name }}</td>
                        <td class="py-4 px-2 font-medium text-gray-900">₱{{ number_format($order->total_amount, 2) }}</td>
                        <td class="py-4 px-2 {{ round($totalPaid, 2) >= round($order->total_amount, 2) ? 'text-emerald-600 font-bold' : 'text-gray-500 font-medium' }}">
                            ₱{{ number_format($totalPaid, 2) }}
                        </td>
                        <td class="py-4 px-2">
                            <span class="px-3 py-1 text-[10px] font-bold uppercase tracking-wider border rounded-full {{ $statusColor }}">
                                {{ $order->order_status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-gray-400">No orders have been recorded in the system yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- The Details Modal -->
    <div id="order-modal" class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm z-50 flex items-center justify-center hidden">
        <div class="bg-white border border-gray-200 w-full max-w-3xl rounded-3xl shadow-2xl overflow-hidden">
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
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Payment & Transaction History</h3>

                <div class="border border-gray-200 bg-white rounded-xl overflow-hidden shadow-sm">
                    <table class="w-full text-left text-sm text-gray-700">
                        <thead class="text-xs text-gray-500 uppercase border-b border-gray-100 bg-gray-50/50">
                            <tr>
                                <th class="p-4 font-bold tracking-wider">Date Paid</th>
                                <th class="p-4 font-bold tracking-wider">Type</th>
                                <th class="p-4 font-bold tracking-wider">Method</th>
                                <th class="p-4 font-bold tracking-wider">Ref #</th>
                                <th class="p-4 font-bold tracking-wider text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody id="modal-payments-body" class="divide-y divide-gray-100">
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

    <script>
        const ordersData = @json($orders->keyBy('order_id'));

        function openModal(orderId) {
            const order = ordersData[orderId];

            document.getElementById('modal-po').innerText = "Order: " + order.po_number;
            document.getElementById('modal-client').innerText = "Client: " + order.client.client_name + " | Total: ₱" + parseFloat(order.total_amount).toLocaleString('en-US', {minimumFractionDigits: 2});

            const tbody = document.getElementById('modal-payments-body');
            tbody.innerHTML = '';

            if (order.payments && order.payments.length > 0) {
                order.payments.forEach(payment => {
                    const amount = parseFloat(payment.amount).toLocaleString('en-US', {minimumFractionDigits: 2});
                    const ref = payment.reference_number ? payment.reference_number : '<span class="text-gray-400 italic">N/A</span>';

                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td class="p-4 whitespace-nowrap">${payment.payment_date}</td>
                        <td class="p-4 font-bold text-gray-900">${payment.payment_type}</td>
                        <td class="p-4">${payment.payment_method}</td>
                        <td class="p-4 font-mono text-xs text-gray-500">${ref}</td>
                        <td class="p-4 text-right text-emerald-600 font-bold">₱${amount}</td>
                    `;
                    tbody.appendChild(tr);
                });
            } else {
                tbody.innerHTML = `<tr><td colspan="5" class="p-8 text-center text-gray-400 text-xs italic">No payments have been recorded for this order yet.</td></tr>`;
            }

            document.getElementById('order-modal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('order-modal').classList.add('hidden');
        }

        document.getElementById('order-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>




@endsection
