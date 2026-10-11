@extends('layouts.app')

@section('header_title', 'Raw Materials & Inventory')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Add New Material Form -->
        <div class="bg-white border border-gray-200 rounded-3xl p-8 h-fit shadow-sm">
            <h3 class="text-xl font-black text-gray-900 mb-6">Register Material</h3>

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

            <form action="{{ route('inventory.store') }}" method="POST" class="flex flex-col gap-5">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">Material Name</label>
                    <input type="text" name="item_name" required placeholder="e.g. 1/4 inch Clear Acrylic" class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl text-sm text-gray-900 focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition-all">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">Category</label>
                        <select name="category" required class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl text-sm text-gray-900 focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition-all">
                            <option value="Metal">Metal</option>
                            <option value="Acrylic">Acrylic</option>
                            <option value="Wood">Wood</option>
                            <option value="Chemicals">Chemicals</option>
                            <option value="Hardware">Hardware</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">Unit</label>
                        <input type="text" name="unit" required placeholder="sheets, kg" class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl text-sm text-gray-900 focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition-all">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">Start Stock</label>
                        <input type="number" step="0.01" name="quantity" required class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl text-sm text-gray-900 focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">Warning At</label>
                        <input type="number" step="0.01" name="low_stock_threshold" required class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl text-sm text-gray-900 focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition-all">
                    </div>
                </div>

                <button type="submit" class="w-full bg-gray-900 text-white font-bold py-3.5 rounded-xl hover:bg-gray-800 transition-colors shadow-sm mt-2">Add to System</button>
            </form>
        </div>

        <!-- Inventory List -->
        <div class="lg:col-span-2 bg-white border border-gray-200 rounded-3xl p-8 overflow-x-auto shadow-sm">
            <h3 class="text-xl font-black text-gray-900 mb-6">Current Stock Levels</h3>

            <table class="w-full text-left text-sm text-gray-700">
                <thead class="text-xs text-gray-400 uppercase border-b-2 border-gray-200">
                    <tr>
                        <th class="pb-3 px-2 font-bold tracking-wider">Material</th>
                        <th class="pb-3 px-2 font-bold tracking-wider">Category</th>
                        <th class="pb-3 px-2 font-bold tracking-wider">Current Stock</th>
                        <th class="pb-3 px-2 font-bold tracking-wider text-right">Adjust Stock</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($inventory as $item)
                        @php
                            $isLowStock = $item->quantity <= $item->low_stock_threshold;
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="py-4 px-2 font-bold text-gray-900">
                                {{ $item->item_name }}
                                @if($isLowStock)
                                    <span class="ml-2 px-2 py-1 text-[9px] font-black uppercase tracking-widest bg-red-50 text-red-600 border border-red-200 rounded-full">Low Stock</span>
                                @endif
                            </td>
                            <td class="py-4 px-2 font-medium">{{ $item->category }}</td>
                            <td class="py-4 px-2 font-black text-lg {{ $isLowStock ? 'text-red-500' : 'text-emerald-600' }}">
                                {{ $item->quantity }} <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">{{ $item->unit }}</span>
                            </td>
                            <td class="py-4 px-2">
                                <form action="{{ route('inventory.adjust', $item->item_id) }}" method="POST" class="flex justify-end gap-2 m-0 items-center">
                                    @csrf
                                    @method('PATCH')
                                    <input type="number" step="0.01" name="amount" required placeholder="Amt" class="w-20 bg-gray-50 border border-gray-200 p-2 rounded-lg text-sm text-center font-bold text-gray-900 focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition-all">
                                    <button type="submit" name="adjustment_type" value="deduct" class="bg-white border border-amber-200 text-amber-600 px-4 py-2 rounded-lg text-xs font-bold hover:bg-amber-50 transition-colors shadow-sm">- Use</button>
                                    <button type="submit" name="adjustment_type" value="add" class="bg-white border border-blue-200 text-blue-600 px-4 py-2 rounded-lg text-xs font-bold hover:bg-blue-50 transition-colors shadow-sm">+ Buy</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-gray-400 font-medium">No inventory materials registered.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
