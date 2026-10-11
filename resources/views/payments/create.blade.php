@extends('layouts.app')

@section('header_title', 'Record Client Payment')

@section('content')
    <div class="bg-white border border-gray-200 rounded-3xl shadow-sm p-8 max-w-2xl">
        <div class="mb-8">
            <h3 class="text-xl font-black text-gray-900">Process Transaction</h3>
            <p class="text-sm text-gray-500 mt-1">Record a downpayment or final balance for an active order.</p>
        </div>

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

        <form action="{{ route('payments.store') }}" method="POST" class="flex flex-col gap-6">
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

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">Payment Type</label>
                    <select name="payment_type" required class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl text-sm text-gray-900 focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition-all">
                        <option value="Downpayment">50% Downpayment</option>
                        <option value="Final Payment">Final Balance</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">Payment Method</label>
                    <select name="payment_method" required class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl text-sm text-gray-900 focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition-all">
                        <option value="Cash">Cash</option>
                        <option value="GCash">GCash</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">Transaction Date</label>
                    <input type="date" name="payment_date" required value="{{ date('Y-m-d') }}" class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl text-sm text-gray-900 focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">Amount Paid (₱)</label>
                    <input type="number" step="0.01" name="amount" id="amount_paid" required class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl text-sm text-gray-900 font-bold focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition-all" placeholder="Select an order first...">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">Reference Number (Optional)</label>
                <input type="text" name="reference_number" placeholder="Required for GCash / Bank Transfers" class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl text-sm text-gray-900 focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition-all">
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full bg-gray-900 text-white font-bold py-3.5 rounded-xl hover:bg-gray-800 transition-colors shadow-sm">Process Payment</button>
            </div>
        </form>
    </div>

    <script>
        function updateAmountPlaceholder() {
            const select = document.getElementById('order_select');
            const input = document.getElementById('amount_paid');
            const selectedOption = select.options[select.selectedIndex];

            const amount = selectedOption.getAttribute('data-amount');

            if (amount) {
                const formattedAmount = parseFloat(amount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                input.placeholder = "Exact amount required: ₱" + formattedAmount;
            } else {
                input.placeholder = "Select an order first...";
            }
        }
    </script>
@endsection
