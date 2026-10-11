<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CREST - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-zinc-950 text-zinc-300 font-sans antialiased h-screen flex items-center justify-center">

    <div class="w-full max-w-md bg-zinc-900 p-8 rounded-none border border-zinc-800 flex flex-col gap-6">
        <div class="flex flex-col items-center mb-4">
            <h1 class="text-2xl font-semibold text-zinc-100 tracking-tight">CREST System</h1>
            <p class="text-sm text-zinc-500 mt-1">Carldan Metal Awards</p>
        </div>

        <form action="{{ route('login') }}" method="POST" class="flex flex-col gap-5">
            @csrf

            <div class="flex flex-col gap-1.5">
                <label for="username" class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Username</label>
                <input type="text" name="username" id="username" value="{{ old('username') }}" required autofocus
                    class="bg-zinc-950 border border-zinc-800 text-zinc-100 text-sm focus:border-zinc-600 focus:outline-none focus:ring-0 p-2.5 transition-colors">
                @error('username')
                <span class="text-xs text-red-400 mt-1">{{ $message }}</span>
            @enderror
            </div>

            <div class="flex flex-col gap-1.5">
                <label for="password" class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Password</label>
                <input type="password" name="password" id="password" required
                    class="bg-zinc-950 border border-zinc-800 text-zinc-100 text-sm focus:border-zinc-600 focus:outline-none focus:ring-0 p-2.5 transition-colors">
            </div>

            <button type="submit"
                class="mt-2 bg-zinc-100 text-zinc-950 text-sm font-semibold py-2.5 hover:bg-zinc-300 transition-colors">
                Sign In
            </button>
        </form>
    </div>
</body>
</html>
