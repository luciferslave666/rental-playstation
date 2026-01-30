<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Informasi PlayStation')</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="text-white antialiased selection:bg-ps-blue selection:text-white">

    <div class="flex h-screen overflow-hidden">
        
        <aside class="w-64 sidebar-gradient flex-shrink-0 border-r border-slate-700 hidden md:flex flex-col transition-all duration-300 z-20">
            <div class="h-20 flex items-center justify-center border-b border-slate-700/50">
                <div class="flex items-center gap-3">
                    <i class="fa-brands fa-playstation text-4xl text-ps-blue animate-pulse-slow"></i>
                    <h1 class="text-xl font-bold tracking-wider text-white">PS RENTAL <span class="text-ps-blue">SYS</span></h1>
                </div>
            </div>

            <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-2">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2 px-2">Menu Utama</p>
                
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('dashboard') ? 'bg-ps-blue/20 text-ps-blue border border-ps-blue/30 shadow-[0_0_15px_rgba(0,112,209,0.4)]' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}">
                    <i class="fa-solid fa-gamepad"></i>
                    <span class="font-medium">Dashboard</span>
                </a>

            <a href="{{ route('transaksi.index') }}" class="nav-item flex items-center gap-3 px-4 py-3 text-slate-300 hover:bg-slate-700/50 hover:text-white rounded-xl transition-all {{ request()->routeIs('transaksi.index') ? 'bg-slate-700 text-white' : '' }}">
                <i class="fa-solid fa-receipt"></i>
                <span>Riwayat Transaksi</span>
            </a>
            <a href="{{ route('pelanggan.index') }}" class="nav-item flex items-center gap-3 px-4 py-3 text-slate-300 hover:bg-slate-700/50 hover:text-white rounded-xl transition-all {{ request()->routeIs('pelanggan*') ? 'bg-slate-700 text-white' : '' }}">
    <i class="fa-solid fa-users"></i>
    <span>Pelanggan</span>
</a>
<a href="{{ route('ruangan.index') }}" class="nav-item flex items-center gap-3 px-4 py-3 text-slate-300 hover:bg-slate-700/50 hover:text-white rounded-xl transition-all {{ request()->routeIs('ruangan*') ? 'bg-slate-700 text-white' : '' }}">
    <i class="fa-solid fa-tv"></i>
    <span>Data Ruangan</span>
</a>
<a href="{{ route('user.index') }}" class="nav-item flex items-center gap-3 px-4 py-3 text-slate-300 hover:bg-slate-700/50 hover:text-white rounded-xl transition-all {{ request()->routeIs('user*') ? 'bg-slate-700 text-white' : '' }}">
    <i class="fa-solid fa-user-shield"></i>
    <span>Data Pegawai</span>
</a>
<a href="{{ route('paket.index') }}" class="nav-item flex items-center gap-3 px-4 py-3 text-slate-300 hover:bg-slate-700/50 hover:text-white rounded-xl transition-all {{ request()->routeIs('paket*') ? 'bg-slate-700 text-white' : '' }}">
    <i class="fa-solid fa-box-archive w-5 text-center"></i>
    <span>Kelola Paket</span>
</a>
            </nav>

<div class="p-4 border-t border-slate-700/50">
    <div class="flex items-center justify-between gap-2 p-3 rounded-xl bg-slate-800/50">
        <div class="flex items-center gap-3 overflow-hidden">
            <div class="w-9 h-9 flex-shrink-0 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm">
                {{ substr(Auth::user()->nama ?? 'U', 0, 1) }}
            </div>
            <div class="min-w-0">
                <p class="text-sm font-semibold text-white truncate">{{ Auth::user()->nama ?? 'Guest' }}</p>
                <p class="text-xs text-slate-400 capitalize">{{ Auth::user()->role ?? 'Staff' }}</p>
            </div>
        </div>
        
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="text-slate-400 hover:text-red-400 transition" title="Keluar">
                <i class="fa-solid fa-right-from-bracket"></i>
            </button>
        </form>
    </div>
</div>
        </aside>

        <main class="flex-1 flex flex-col relative z-10 overflow-hidden">
            <div class="absolute top-0 right-0 -z-10 w-96 h-96 bg-blue-600/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 -z-10 w-64 h-64 bg-purple-600/10 rounded-full blur-3xl pointer-events-none"></div>

            <header class="h-20 flex items-center justify-between px-8 border-b border-slate-700/50 bg-slate-900/80 backdrop-blur-md sticky top-0 z-20">
                <h2 class="text-2xl font-bold text-white">@yield('header', 'Dashboard')</h2>
                <div class="flex items-center gap-4">
<a href="{{ route('transaksi.create') }}" class="bg-ps-blue hover:bg-blue-600 text-white px-5 py-2 rounded-full font-medium transition shadow-lg shadow-blue-600/30 flex items-center gap-2">
    <i class="fa-solid fa-plus"></i> Transaksi Baru
</a>
                </div>
            </header>

            <div class="flex-1 overflow-y-auto p-8">
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>