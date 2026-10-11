@extends('layouts.app')

@section('header_title', 'Edit Account')

@section('content')
<div class="max-w-2xl mx-auto bg-white border border-gray-200 rounded-3xl p-8 shadow-sm">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-xl font-black text-gray-900">Edit Employee Account</h3>
        <a href="{{ route('accounts.index') }}" class="text-sm font-bold text-gray-500 hover:text-gray-900 transition-colors">← Back to Accounts</a>
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

    <form action="{{ route('accounts.update', $employee->employee_id) }}" method="POST" class="flex flex-col gap-5">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-5 gap-4">
            <div class="col-span-2">
                <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">First Name</label>
                <input type="text" name="employee_fn" value="{{ old('employee_fn', $employee->employee_fn) }}" required class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl text-sm text-gray-900 focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition-all">
            </div>
            <div class="col-span-1">
                <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">M.I.</label>
                <input type="text" name="employee_mi" value="{{ old('employee_mi', $employee->employee_mi) }}" maxlength="5" class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl text-sm text-gray-900 focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition-all text-center">
            </div>
            <div class="col-span-2">
                <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">Last Name</label>
                <input type="text" name="employee_ln" value="{{ old('employee_ln', $employee->employee_ln) }}" required class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl text-sm text-gray-900 focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition-all">
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">Role</label>
            <select name="role" required class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl text-sm text-gray-900 focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition-all">
                <option value="Sales & Record Personnel" {{ $employee->role === 'Sales & Record Personnel' ? 'selected' : '' }}>Sales & Record Personnel</option>
                <option value="Production Manager" {{ $employee->role === 'Production Manager' ? 'selected' : '' }}>Production Manager</option>
                <option value="Delivery Personnel" {{ $employee->role === 'Delivery Personnel' ? 'selected' : '' }}>Delivery Personnel</option>
                <option value="Founders/General Manager" {{ $employee->role === 'Founders/General Manager' ? 'selected' : '' }}>Founders / General Manager</option>
            </select>
        </div>

        <div class="mt-4 pt-4 border-t border-gray-100">
            <h4 class="text-xs font-black text-gray-900 uppercase tracking-widest mb-4">Login Credentials</h4>

            <div class="flex flex-col gap-5">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">System Username</label>
                    <input type="text" name="username" value="{{ old('username', $employee->user->username ?? '') }}" required class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl text-sm text-gray-900 focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition-all">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">Reset Password</label>
                    <input type="password" name="password" placeholder="Leave blank to keep current password" class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl text-sm text-gray-900 focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition-all">
                    <p class="text-[10px] text-gray-400 mt-2 font-medium">Only type a new password here if you want to overwrite the employee's current password.</p>
                </div>
            </div>
        </div>

        <button type="submit" class="w-full bg-gray-900 text-white font-bold py-3.5 rounded-xl hover:bg-gray-800 transition-colors shadow-sm mt-4">Save Changes</button>
    </form>
</div>
@endsection
