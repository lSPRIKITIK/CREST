@extends('layouts.app')

@section('header_title', 'Edit Staff Account')

@section('content')
    <div class="max-w-xl bg-zinc-900 border border-zinc-800 p-6">
        <div class="flex justify-between items-center border-b border-zinc-800 pb-4 mb-6">
            <h3 class="text-lg font-medium text-zinc-100">Update Account Details</h3>
            <a href="{{ route('accounts.index') }}" class="text-sm text-zinc-400 hover:text-zinc-200">Back to List</a>
        </div>

        @if($errors->any())
            <div class="bg-red-950/50 border border-red-900 text-red-400 text-sm p-3 mb-6 flex flex-col gap-1">
                @foreach($errors->all() as $error)
                    <span>- {{ $error }}</span>
                @endforeach
            </div>
        @endif

        <form action="{{ route('accounts.update', $employee->employee_id) }}" method="POST" class="flex flex-col gap-4">
            @csrf
            @method('PUT')

            <div class="flex gap-4">
                <div class="flex-1">
                    <label class="block text-xs text-zinc-400 mb-1">First Name</label>
                    <input type="text" name="employee_fn" value="{{ old('employee_fn', $employee->employee_fn) }}" required class="w-full bg-zinc-950 border border-zinc-800 p-2 text-sm text-zinc-100">
                </div>
                <div class="flex-1">
                    <label class="block text-xs text-zinc-400 mb-1">Last Name</label>
                    <input type="text" name="employee_ln" value="{{ old('employee_ln', $employee->employee_ln) }}" required class="w-full bg-zinc-950 border border-zinc-800 p-2 text-sm text-zinc-100">
                </div>
            </div>

            <div>
                <label class="block text-xs text-zinc-400 mb-1">System Role</label>
                <select name="role" required class="w-full bg-zinc-950 border border-zinc-800 p-2 text-sm text-zinc-100">
                    <option value="Founders/General Manager" {{ $employee->role === 'Founders/General Manager' ? 'selected' : '' }}>Founders/General Manager</option>
                    <option value="Sales & Record Personnel" {{ $employee->role === 'Sales & Record Personnel' ? 'selected' : '' }}>Sales & Record Personnel</option>
                    <option value="Production Manager" {{ $employee->role === 'Production Manager' ? 'selected' : '' }}>Production Manager</option>
                    <option value="Craftsmen" {{ $employee->role === 'Craftsmen' ? 'selected' : '' }}>Craftsmen</option>
                    <option value="Delivery Personnel" {{ $employee->role === 'Delivery Personnel' ? 'selected' : '' }}>Delivery Personnel</option>
                </select>
            </div>

            <div>
                <label class="block text-xs text-zinc-400 mb-1">Username</label>
                <input type="text" name="username" value="{{ old('username', $employee->user ? $employee->user->username : '') }}" required class="w-full bg-zinc-950 border border-zinc-800 p-2 text-sm text-zinc-100">
            </div>

            <div>
                <label class="block text-xs text-zinc-400 mb-1">New Password (leave blank to keep current)</label>
                <input type="password" name="password" class="w-full bg-zinc-950 border border-zinc-800 p-2 text-sm text-zinc-100" placeholder="••••••••">
            </div>

            <button type="submit" class="bg-blue-600 text-zinc-100 font-medium py-2 mt-4 hover:bg-blue-500 transition-colors">
                Save Changes
            </button>
        </form>
    </div>
@endsection
