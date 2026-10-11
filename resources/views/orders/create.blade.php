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
                        <input type="text" name="client_name" value="{{ old('client_name') }}" required class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl text-sm text-gray-900 focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">Contact Person</label>
                        <input type="text" name="contact_person" value="{{ old('contact_person') }}" class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl text-sm text-gray-900 focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">Contact Number</label>
                        <input type="text" name="contact_number" value="{{ old('contact_number') }}" required class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl text-sm text-gray-900 focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">Delivery Address</label>
                        <input type="text" name="client_address" value="{{ old('client_address') }}" required class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl text-sm text-gray-900 focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition-all">
                    </div>
                </div>
            </div>

            <!-- Order Details Section -->
            <div>
                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 border-b border-gray-100 pb-2">2. Order Specifications</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">PO Number</label>
                        <input type="text" name="po_number" value="{{ old('po_number') }}" required class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl text-sm text-gray-900 focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">Item Type</label>
                        <select name="item_type" required class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl text-sm text-gray-900 focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition-all">
                            <option value="Medal" {{ old('item_type') == 'Medal' ? 'selected' : '' }}>Medal</option>
                            <option value="Trophy" {{ old('item_type') == 'Trophy' ? 'selected' : '' }}>Trophy</option>
                            <option value="Plaque" {{ old('item_type') == 'Plaque' ? 'selected' : '' }}>Plaque</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">Due Date</label>
                        <input type="date" name="due_date" value="{{ old('due_date') }}" required class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl text-sm text-gray-900 focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition-all">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">Specifications (Design details)</label>
                        <textarea name="specifications" rows="2" class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl text-sm text-gray-900 focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition-all">{{ old('specifications') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">Quantity</label>
                        <input type="number" name="quantity" value="{{ old('quantity') }}" min="1" required class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl text-sm text-gray-900 focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">Unit Price (₱)</label>
                        <input type="number" step="0.01" name="unit_price" value="{{ old('unit_price') }}" min="0" required class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl text-sm text-gray-900 focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition-all">
                    </div>
                </div>
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full bg-gray-900 text-white font-bold py-3.5 rounded-xl hover:bg-gray-800 transition-colors shadow-sm">Save & Register Order</button>
            </div>
        </form>
    </div>
@endsection
