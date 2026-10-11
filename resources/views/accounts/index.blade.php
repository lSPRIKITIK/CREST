@extends('layouts.app')

@section('header_title', 'Manage Accounts')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Add New Account Form -->
        <div class="bg-white border border-gray-200 rounded-3xl p-8 h-fit shadow-sm">
            <h3 class="text-xl font-black text-gray-900 mb-6">Register Employee</h3>

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

            <form action="{{ route('accounts.store') }}" method="POST" class="flex flex-col gap-5">
                @csrf
                <div class="grid grid-cols-5 gap-4">
                    <div class="col-span-2">
                        <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">First Name</label>
                        <input type="text" name="employee_fn" required class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl text-sm text-gray-900 focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition-all">
                    </div>
                    <div class="col-span-1">
                        <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">M.I.</label>
                        <input type="text" name="employee_mi" maxlength="5" class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl text-sm text-gray-900 focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition-all text-center">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">Last Name</label>
                        <input type="text" name="employee_ln" required class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl text-sm text-gray-900 focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition-all">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">Role</label>
                    <select name="role" required class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl text-sm text-gray-900 focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition-all">
                        <option value="Sales & Record Personnel">Sales & Record Personnel</option>
                        <option value="Production Manager">Production Manager</option>
                        <option value="Delivery Personnel">Delivery Personnel</option>
                        <option value="Founders/General Manager">Founders / General Manager</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">System Username</label>
                    <input type="text" name="username" required class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl text-sm text-gray-900 focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition-all">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">Password</label>
                    <input type="password" name="password" required class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl text-sm text-gray-900 focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition-all">
                </div>

                <button type="submit" class="w-full bg-gray-900 text-white font-bold py-3.5 rounded-xl hover:bg-gray-800 transition-colors shadow-sm mt-2">Create Account</button>
            </form>
        </div>

        <!-- Account List -->
        <div class="lg:col-span-2 bg-white border border-gray-200 rounded-3xl p-8 overflow-x-auto shadow-sm">
            <h3 class="text-xl font-black text-gray-900 mb-6">System Accounts</h3>

            <table class="w-full text-left text-sm text-gray-700">
                <thead class="text-xs text-gray-400 uppercase border-b-2 border-gray-200">
                    <tr>
                        <th class="pb-3 px-2 font-bold tracking-wider">Employee Name</th>
                        <th class="pb-3 px-2 font-bold tracking-wider">Username</th>
                        <th class="pb-3 px-2 font-bold tracking-wider">Role</th>
                        <th class="pb-3 px-2 font-bold tracking-wider text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($accounts as $account)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="py-4 px-2 font-bold text-gray-900">
                                {{ $account->employee_fn }}
                                @if($account->employee_mi)
                                    {{ strtoupper(substr(trim($account->employee_mi), 0, 1)) }}.
                                @endif
                                {{ $account->employee_ln }}
                            </td>
                            <td class="py-4 px-2 font-medium text-gray-500">{{ $account->user->username ?? 'No Login' }}</td>

                            <td class="py-4 px-2">
                                <span class="px-3 py-1 text-[10px] font-bold uppercase tracking-wider border rounded-full bg-gray-50 text-gray-600 border-gray-200">
                                    {{ $account->role }}
                                </span>
                            </td>

                            <td class="py-4 px-2 text-right">
                                <!-- Protection Logic Applied Here -->
                                @if($account->role === 'Founders/General Manager')
                                    <span class="text-[10px] font-black uppercase tracking-widest text-emerald-600 bg-emerald-50 border border-emerald-200 px-3 py-1.5 rounded-lg">Protected</span>
                                @else
                                    <form action="{{ route('accounts.destroy', $account->employee_id) }}" method="POST" class="inline m-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Are you sure you want to deactivate this account?')" class="bg-white border border-red-200 text-red-600 px-4 py-2 rounded-lg text-xs font-bold hover:bg-red-50 hover:text-red-700 transition-colors shadow-sm">Deactivate</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-gray-400 font-medium">No system accounts found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
