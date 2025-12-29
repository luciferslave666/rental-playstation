<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PS Rental Sys</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-900 font-sans antialiased text-white flex items-center justify-center min-h-screen relative overflow-hidden">

    <div class="absolute top-0 left-0 w-full h-full overflow-hidden -z-10">
        <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-blue-600/20 rounded-full blur-3xl"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-96 h-96 bg-purple-600/20 rounded-full blur-3xl"></div>
    </div>

    <div class="w-full max-w-md p-8">
        <div class="text-center mb-10">
            <i class="fa-brands fa-playstation text-6xl text-ps-blue animate-pulse mb-4"></i>
            <h1 class="text-3xl font-bold tracking-wider">PS RENTAL <span class="text-ps-blue">SYS</span></h1>
            <p class="text-slate-400 mt-2 text-sm">Masuk untuk mengelola rental</p>
        </div>

        <div class="bg-slate-800/50 backdrop-blur-md border border-slate-700 p-8 rounded-2xl shadow-2xl">
            <form action="{{ route('login.process') }}" method="POST">
                @csrf
                
                @if($errors->any())
                    <div class="mb-4 p-3 rounded-lg bg-red-500/10 border border-red-500/20 text-red-400 text-sm flex items-center gap-2">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        {{ $errors->first() }}
                    </div>
                @endif

                <div class="mb-5">
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-2">Username</label>
                    <div class="relative">
                        <span class="absolute left-4 top-3.5 text-slate-500"><i class="fa-solid fa-user"></i></span>
                        <input type="text" name="username" value="{{ old('username') }}" class="w-full bg-slate-900 border border-slate-700 text-white pl-10 pr-4 py-3 rounded-xl focus:outline-none focus:border-ps-blue focus:ring-1 focus:ring-ps-blue transition placeholder-slate-600" placeholder="admin / kasir" required autofocus>
                    </div>
                </div>

                <div class="mb-8">
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-2">Password</label>
                    <div class="relative">
                        <span class="absolute left-4 top-3.5 text-slate-500"><i class="fa-solid fa-lock"></i></span>
                        <input type="password" name="password" class="w-full bg-slate-900 border border-slate-700 text-white pl-10 pr-4 py-3 rounded-xl focus:outline-none focus:border-ps-blue focus:ring-1 focus:ring-ps-blue transition placeholder-slate-600" placeholder="••••••••" required>
                    </div>
                </div>

                <button type="submit" class="w-full bg-ps-blue hover:bg-blue-600 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-blue-600/30 transition transform hover:-translate-y-1">
                    MASUK SYSTEM
                </button>
            </form>
        </div>
        
        <p class="text-center text-slate-600 text-xs mt-8">
            &copy; {{ date('Y') }} Sistem Informasi PlayStation
        </p>
    </div>

</body>
</html>