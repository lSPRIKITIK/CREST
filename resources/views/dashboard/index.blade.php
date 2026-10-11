@extends('layouts.app')

@section('header_title', 'Dashboard')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- Welcome Card -->
        <div class="col-span-1 md:col-span-2 bg-zinc-900 border border-zinc-800 p-6 flex flex-col justify-center">
            <h3 class="text-xl font-semibold text-zinc-100">Welcome back, {{ Auth::user()->employee->employee_fn }}.</h3>
            <p class="text-sm text-zinc-400 mt-2">
                You are logged in as <span class="font-medium text-zinc-300">{{ Auth::user()->employee->role }}</span>.
                Use the sidebar to navigate to your authorized modules.
            </p>
        </div>

        <!-- Quick Stats -->
        <div class="bg-zinc-900 border border-zinc-800 p-6 flex flex-col gap-2">
            <span class="text-xs font-semibold text-zinc-500 uppercase tracking-wider">System Status</span>
            <div class="flex items-center gap-2 mt-1">
                <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                <span class="text-sm text-zinc-300">Database Connected</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                <span class="text-sm text-zinc-300">RBAC Active</span>
            </div>
        </div>

    </div>
@endsection
