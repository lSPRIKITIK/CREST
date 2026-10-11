<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CREST Dashboard</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 text-gray-900 font-sans antialiased h-screen flex overflow-hidden">

    <!-- Mobile Dark Overlay -->
    <div id="mobile-overlay" class="fixed inset-0 bg-black/80 z-40 hidden md:hidden" onclick="toggleSidebar()"></div>

    <!-- Sidebar Navigation -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200 flex flex-col transform -translate-x-full transition-transform duration-300 md:relative md:translate-x-0 shadow-sm">

        <!-- Sidebar Header (Pinned to Top) -->
        <div class="p-6 border-b border-gray-200 flex justify-between items-center shrink-0">
            <h1 class="text-xl font-black tracking-tight text-gray-900">CREST</h1>
            <button class="md:hidden text-gray-400 hover:text-gray-900" onclick="toggleSidebar()">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Navigation Links (Scrollable Middle Section) -->
        <nav class="py-4 flex-1 overflow-y-auto flex flex-col gap-1">

            <!-- Main / Dashboard Section -->
            <div class="mt-2 mb-2">
                <p class="text-xs font-bold text-gray-800 uppercase tracking-widest px-6">Main</p>
            </div>
            <a href="{{ url('/dashboard') }}" class="block px-6 py-2.5 text-sm font-bold text-gray-700 hover:text-gray-900 hover:bg-gray-50 border-l-4 border-transparent hover:border-gray-900 transition-all">Dashboard</a>

            @if(Auth::check())
                @php $role = Auth::user()->employee->role; @endphp

                @if($role === 'Sales & Record Personnel')
                    <div class="mt-4 mb-2">
                        <hr class="border-gray-100 mb-4 mx-4">
                        <p class="text-xs font-bold text-gray-800 uppercase tracking-widest px-6">Sales</p>
                    </div>
                    <a href="{{ route('orders.create') }}" class="block px-6 py-2.5 text-sm font-medium text-gray-500 hover:text-gray-900 hover:bg-gray-50 border-l-4 border-transparent hover:border-gray-900 transition-all">Create Order</a>
                    <a href="{{ route('payments.create') }}" class="block px-6 py-2.5 text-sm font-medium text-gray-500 hover:text-gray-900 hover:bg-gray-50 border-l-4 border-transparent hover:border-gray-900 transition-all">Record Payment</a>
                @endif

                @if(in_array($role, ['Production Manager', 'Founders/General Manager', 'Craftsmen']))
                    <div class="mt-4 mb-2">
                        <hr class="border-gray-100 mb-4 mx-4">
                        <p class="text-xs font-bold text-gray-800 uppercase tracking-widest px-6">Production</p>
                    </div>
                    <a href="{{ route('production.index') }}" class="block px-6 py-2.5 text-sm font-medium text-gray-500 hover:text-gray-900 hover:bg-gray-50 border-l-4 border-transparent hover:border-gray-900 transition-all">Active Production</a>
                    <a href="{{ route('inventory.index') }}" class="block px-6 py-2.5 text-sm font-medium text-gray-500 hover:text-gray-900 hover:bg-gray-50 border-l-4 border-transparent hover:border-gray-900 transition-all">Inventory Tracking</a>
                @endif

                @if(in_array($role, ['Delivery Personnel', 'Founders/General Manager']))
                    <div class="mt-4 mb-2">
                        <hr class="border-gray-100 mb-4 mx-4">
                        <p class="text-xs font-bold text-gray-800 uppercase tracking-widest px-6">Logistics</p>
                    </div>
                    <a href="{{ route('delivery.index') }}" class="block px-6 py-2.5 text-sm font-medium text-gray-500 hover:text-gray-900 hover:bg-gray-50 border-l-4 border-transparent hover:border-gray-900 transition-all">Delivery Board</a>
                @endif

                @if($role === 'Founders/General Manager')
                    <div class="mt-4 mb-2">
                        <hr class="border-gray-100 mb-4 mx-4">
                        <p class="text-xs font-bold text-gray-800 uppercase tracking-widest px-6">Admin</p>
                    </div>
                    <a href="{{ route('tracking.index') }}" class="block px-6 py-2.5 text-sm font-medium text-gray-500 hover:text-gray-900 hover:bg-gray-50 border-l-4 border-transparent hover:border-gray-900 transition-all">Order Tracking</a>
                    <a href="{{ route('accounts.index') }}" class="block px-6 py-2.5 text-sm font-medium text-gray-500 hover:text-gray-900 hover:bg-gray-50 border-l-4 border-transparent hover:border-gray-900 transition-all">Manage Accounts</a>
                @endif
            @endif
        </nav>

        <!-- User Profile & Logout (Pinned to Bottom) -->
        @if(Auth::check())
        <div class="p-6 border-t border-gray-200 bg-gray-50 shrink-0">
            <p class="text-sm font-bold text-gray-900 truncate">{{ Auth::user()->employee->employee_fn }} {{ Auth::user()->employee->employee_ln }}</p>
            <p class="text-xs text-gray-500 mb-3 truncate">{{ Auth::user()->employee->role }}</p>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm text-red-500 hover:text-red-600 font-bold tracking-wide">Sign Out</button>
            </form>
        </div>
        @endif
    </aside>

    <!-- Main Workspace -->
    <main class="flex-1 flex flex-col overflow-y-auto min-w-0">
        <!-- Mobile Top Bar -->
        <div class="md:hidden bg-white border-b border-gray-200 p-4 flex items-center gap-4 sticky top-0 z-30 shadow-sm">
            <button class="text-gray-500 hover:text-gray-900" onclick="toggleSidebar()">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
            <h1 class="text-lg font-bold text-gray-900 truncate">@yield('header_title', 'CREST')</h1>
        </div>

        <div class="p-4 md:p-8 lg:p-10">
            <div class="hidden md:block mb-8">
                <h2 class="text-2xl font-black tracking-tight text-gray-900">@yield('header_title')</h2>
            </div>
            @yield('content')
        </div>
    </main>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobile-overlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }
    </script>
</body>
</html>
