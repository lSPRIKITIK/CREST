<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CREST - Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 antialiased">
<div class="min-h-screen w-full flex items-center justify-center px-4">
    <div class="max-w-md w-full bg-white border border-gray-200 rounded-3xl shadow-sm p-8">

        <!-- Header -->
        <div class="text-center mb-8">
            <h2 class="text-2xl font-black tracking-tight text-gray-900">CREST</h2>
            <p class="text-sm text-gray-500 mt-1">Carldan Metal Awards Management System</p>
        </div>

        @if(session('status'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 font-medium text-sm p-4 rounded-xl mb-6">
                {{ session('status') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 text-sm p-4 rounded-xl mb-6 flex flex-col gap-1">
                @foreach($errors->all() as $error)
                    <span class="font-medium">- {{ $error }}</span>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-5">
            @csrf

            <div>
                <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">Username</label>
                <input type="text" name="username" required autofocus class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl text-sm text-gray-900 focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition-all">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">Password</label>
                <input type="password" name="password" required class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl text-sm text-gray-900 focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition-all">
            </div>


            <div class="pt-2">
                <button type="submit" class="w-full bg-gray-900 text-white font-bold py-3.5 rounded-xl hover:bg-gray-800 transition-colors shadow-sm">
                    Sign In
                </button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
