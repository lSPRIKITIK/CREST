@extends('layouts.app')

@section('header_title', 'Record Client Payment')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left Column: Payment Form -->
        <div class="bg-white border border-gray-200 rounded-3xl shadow-sm p-8 h-fit">
            <h3 class="text-xl font-black text-gray-900 mb-6">Process Transaction</h3>

            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 font-medium text-sm p-4 rounded-xl mb-6">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 font-medium text-sm p-4 rounded-xl mb-6">
                    {{ session('error') }}
                </div>
            @endif
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 text-sm p-4 rounded-xl mb-6 flex flex-col gap-1">
                    @foreach($errors->all() as $error)
                        <span class="font-medium">- {{ $error }}</span>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('payments.store') }}" method="POST" class="flex flex-col gap-5">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">Select Active Order</label>
                    <select name="order_id" id="order_select" required class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl text-sm text-gray-900 focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition-all" onchange="updateAmountPlaceholder()">
                        <option value="" data-amount="" disabled selected>Choose an order...</option>
                        @foreach($orders as $order)
                            @php
                                $isPayable = in_array($order->order_status, ['Pending Downpayment', 'Delivered']);
                                $totalPaid = $order->payments_sum_amount ?? 0;

                                if ($order->order_status === 'Pending Downpayment') {
                                    $requiredAmount = $order->total_amount / 2;
                                } else {
                                    $requiredAmount = $order->total_amount - $totalPaid;
                                }
                            @endphp

                            <option value="{{ $order->order_id }}"
                                data-amount="{{ number_format($requiredAmount, 2, '.', '') }}"
                                {{ !$isPayable ? 'disabled' : '' }}
                                class="{{ !$isPayable ? 'text-gray-400 italic bg-gray-50' : 'text-gray-900 font-medium' }}">
                                {{ $order->po_number }} - {{ $order->client->client_name }}
                                {{ !$isPayable ? '(Locked: ' . $order->order_status . ')' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 1. Master Payment Method -->
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">Payment Method</label>
                    <select name="payment_method" id="payment_method" required class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl text-sm text-gray-900 focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition-all" onchange="toggleProviders()">
                        <option value="Cash">Cash</option>
                        <option value="Online Payment">Online Payment</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                        <option value="Cheque">Cheque</option>
                    </select>
                </div>

                <!-- 2. Online Providers -->
                <div id="online_provider_div" class="hidden">
                    <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">E-Wallet Provider</label>
                    <select name="online_provider" id="online_provider" class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl text-sm text-gray-900 focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition-all" onchange="toggleCustomProvider()">
                        <option value="GCash">GCash</option>
                        <option value="Paymaya">Paymaya</option>
                        <option value="Maribank">Maribank</option>
                        <option value="Others">Others (Specify below)</option>
                    </select>
                </div>

                <!-- 3. Bank Providers -->
                <div id="bank_provider_div" class="hidden">
                    <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">Bank Name</label>
                    <select name="bank_provider" id="bank_provider" class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl text-sm text-gray-900 focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition-all" onchange="toggleCustomProvider()">
                        <option value="BDO">BDO</option>
                        <option value="BPI">BPI</option>
                        <option value="Landbank">Landbank</option>
                        <option value="Others">Others (Specify below)</option>
                    </select>
                </div>

                <!-- 4. Custom Manual Input (For 'Others') -->
                <div id="custom_provider_div" class="hidden">
                    <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">Specify Provider</label>
                    <input type="text" name="custom_provider" id="custom_provider" placeholder="Type bank or e-wallet name" class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl text-sm text-gray-900 focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition-all">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">Date</label>
                        <input type="date" name="transaction_date" required value="{{ date('Y-m-d') }}" class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl text-sm text-gray-900 focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">Amount Paid (₱)</label>
                        <input type="number" step="0.01" name="amount_paid" id="amount_paid" required class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl text-sm text-gray-900 font-bold focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition-all" placeholder="Select an order...">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">Reference Number (Optional)</label>
                    <input type="text" name="reference_number" placeholder="Required for GCash / Bank Transfers" class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl text-sm text-gray-900 focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition-all">
                </div>

                <button type="submit" class="w-full bg-gray-900 text-white font-bold py-3.5 rounded-xl hover:bg-gray-800 transition-colors shadow-sm mt-2">Process Payment</button>
            </form>
        </div>

        <!-- Right Column: Active Orders Tracking Board -->
        <div class="lg:col-span-2 bg-white border border-gray-200 rounded-3xl p-8 overflow-x-auto shadow-sm h-fit">
            <h3 class="text-xl font-black text-gray-900 mb-6">Active Orders Dashboard</h3>

            <table class="w-full text-left text-sm text-gray-700">
                <thead class="text-xs text-gray-400 uppercase border-b-2 border-gray-200">
                    <tr>
                        <th class="pb-3 px-2 font-bold tracking-wider">PO Number</th>
                        <th class="pb-3 px-2 font-bold tracking-wider">Client</th>
                        <th class="pb-3 px-2 font-bold tracking-wider">Status</th>
                        <th class="pb-3 px-2 font-bold tracking-wider text-right">Balance</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($orders as $order)
                        @php
                            $totalPaid = $order->payments_sum_amount ?? 0;
                            $balance = $order->total_amount - $totalPaid;

                            $badgeClass = 'bg-gray-50 text-gray-600 border-gray-200';
                            if($order->order_status === 'Pending Downpayment') {
                                $badgeClass = 'bg-amber-50 text-amber-700 border-amber-200';
                            } elseif($order->order_status === 'In Production') {
                                $badgeClass = 'bg-blue-50 text-blue-700 border-blue-200';
                            } elseif($order->order_status === 'Delivered') {
                                $badgeClass = 'bg-purple-50 text-purple-700 border-purple-200';
                            }
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="py-4 px-2 font-bold text-gray-900">{{ $order->po_number }}</td>
                            <td class="py-4 px-2 font-medium">{{ $order->client->client_name }}</td>
                            <td class="py-4 px-2">
                                <span class="px-3 py-1 text-[10px] font-bold uppercase tracking-wider border rounded-full {{ $badgeClass }}">
                                    {{ $order->order_status }}
                                </span>
                            </td>
                            <td class="py-4 px-2 text-right font-bold {{ $balance > 0 ? 'text-red-600' : 'text-emerald-600' }}">
                                ₱{{ number_format($balance, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-gray-400 font-medium">No active orders found in the system.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Restored your dynamic payment UI logic
        function toggleProviders() {
            const method = document.getElementById('payment_method').value;
            const onlineDiv = document.getElementById('online_provider_div');
            const bankDiv = document.getElementById('bank_provider_div');
            const customDiv = document.getElementById('custom_provider_div');

            onlineDiv.classList.add('hidden');
            bankDiv.classList.add('hidden');
            customDiv.classList.add('hidden');

            if (method === 'Online Payment') {
                onlineDiv.classList.remove('hidden');
            } else if (method === 'Bank Transfer') {
                bankDiv.classList.remove('hidden');
            }
            toggleCustomProvider();
        }

        function toggleCustomProvider() {
            const method = document.getElementById('payment_method').value;
            const customDiv = document.getElementById('custom_provider_div');

            let isOthers = false;
            if (method === 'Online Payment' && document.getElementById('online_provider').value === 'Others') {
                isOthers = true;
            } else if (method === 'Bank Transfer' && document.getElementById('bank_provider').value === 'Others') {
                isOthers = true;
            }

            if (isOthers) {
                customDiv.classList.remove('hidden');
            } else {
                customDiv.classList.add('hidden');
            }
        }

        // Restored the strict integer value calculation
        function updateAmountPlaceholder() {
            const select = document.getElementById('order_select');
            const input = document.getElementById('amount_paid');
            const selectedOption = select.options[select.selectedIndex];
            const amount = selectedOption.getAttribute('data-amount');

            if (amount) {
                input.value = amount;
                input.min = amount;
                input.max = amount;
            } else {
                input.value = "";
                input.removeAttribute('min');
                input.removeAttribute('max');
                input.placeholder = "Select an order...";
            }
        }

        // Ensure UI is synced if browser reloads
        document.addEventListener('DOMContentLoaded', function() {
            toggleProviders();
        });
    </script>
@endsection
