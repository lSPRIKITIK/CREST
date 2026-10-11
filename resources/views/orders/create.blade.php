@extends('layouts.app')

@section('header_title', 'Create New Order')

@section('content')
    <div class="bg-white border border-gray-200 rounded-3xl shadow-sm p-8 max-w-4xl">
        <div class="mb-8">
            <h3 class="text-xl font-black text-gray-900">Purchase Order Details</h3>
            <p class="text-sm text-gray-500 mt-1">Enter the client information and technical specifications.</p>
        </div>

        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 font-medium text-sm p-4 rounded-xl mb-6">
                {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 text-sm p-4 rounded-xl mb-6 flex flex-col gap-1">
                @foreach($errors->all() as $error)
                    <span class="font-medium">- {{ $error }}</span>
                @endforeach
            </div>
        @endif

        <form action="{{ route('orders.store') }}" method="POST" class="flex flex-col gap-8">
            @csrf

            <!-- Client Section -->
            <div>
                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 border-b border-gray-100 pb-2">1. Client Information</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">Client / Company Name</label>
                        <input type="text" name="client_name" required class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl text-sm text-gray-900 focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">Contact Number</label>
                        <input type="text" name="contact_number" required class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl text-sm text-gray-900 focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">Email Address</label>
                        <input type="email" name="email" class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl text-sm text-gray-900 focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">Delivery Address</label>
                        <input type="text" name="address" required class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl text-sm text-gray-900 focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition-all">
                    </div>
                </div>
            </div>

            <!-- Order Details Section -->
            <div>
                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 border-b border-gray-100 pb-2">2. Order Specifications</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">PO Number</label>
                        <input type="text" name="po_number" required class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl text-sm text-gray-900 focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">Total Amount (₱)</label>
                        <input type="number" step="0.01" name="total_amount" required class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl text-sm text-gray-900 focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">Production Deadline</label>
                        <input type="date" name="deadline" required class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl text-sm text-gray-900 focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">Target Delivery Date</label>
                        <input type="date" name="expected_delivery_date" required class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl text-sm text-gray-900 focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition-all">
                    </div>
                </div>
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full bg-gray-900 text-white font-bold py-3.5 rounded-xl hover:bg-gray-800 transition-colors shadow-sm">Save & Register Order</button>
            </div>
        </form>
    </div>
@endsection
