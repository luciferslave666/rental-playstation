"use client"; 

import useSWR from 'swr';
import RuanganCard from '@/src/components/RuanganCard';
import { RuanganDashboradData } from '@/src/types';
import { useSession, signOut } from 'next-auth/react';

const fetcher = (url: string): Promise<RuanganDashboradData[]> => 
  fetch(url).then((res) => res.json());

export default function Home() {
  const { data: session } = useSession();

  const { 
    data: ruanganList, 
    error, 
    isLoading: isLoadingSWR,
    mutate 
  } = useSWR<RuanganDashboradData[]>(
    '/api/ruangan', 
    fetcher, 
    { refreshInterval: 3000 }
  );
  
  if (isLoadingSWR || !session) {
    return (
      <main className="flex min-h-screen items-center justify-center bg-gradient-to-br from-slate-950 via-slate-900 to-blue-950">
        <div className="text-center space-y-4">
          <div className="inline-block animate-spin rounded-full h-16 w-16 border-4 border-blue-500 border-t-transparent"></div>
          <div className="text-blue-400 text-xl font-medium">Loading Dashboard...</div>
        </div>
      </main>
    );
  }

  if (error) {
    return (
      <main className="flex min-h-screen items-center justify-center bg-gradient-to-br from-slate-950 via-slate-900 to-blue-950">
        <div className="text-center space-y-3">
          <div className="text-red-500 text-6xl">⚠️</div>
          <div className="text-red-400 text-xl font-medium">Failed to load data</div>
          <button 
            onClick={() => window.location.reload()} 
            className="mt-4 px-6 py-2 bg-red-500/20 border border-red-500 text-red-400 rounded-lg hover:bg-red-500/30 transition-colors"
          >
            Retry
          </button>
        </div>
      </main>
    );
  }

  return (
    <main className="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-blue-950 relative overflow-hidden">
      {/* Gaming Grid Background */}
      <div className="absolute inset-0 bg-[linear-gradient(rgba(59,130,246,0.03)_1px,transparent_1px),linear-gradient(90deg,rgba(59,130,246,0.03)_1px,transparent_1px)] bg-[size:50px_50px] [mask-image:radial-gradient(ellipse_80%_50%_at_50%_50%,black,transparent)]"></div>
      
      {/* Glow Effects */}
      <div className="absolute top-0 right-0 w-96 h-96 bg-blue-500/20 rounded-full blur-[120px] pointer-events-none"></div>
      <div className="absolute bottom-0 left-0 w-96 h-96 bg-purple-500/10 rounded-full blur-[120px] pointer-events-none"></div>
      
      <div className="relative z-10 p-6 md:p-8 lg:p-12 max-w-[1800px] mx-auto">
        {/* Header */}
        <header className="mb-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-4 backdrop-blur-sm bg-slate-900/30 rounded-2xl p-6 border border-slate-800/50 shadow-2xl">
          <div>
            <h1 className="text-4xl md:text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-blue-400 via-cyan-400 to-blue-500 drop-shadow-lg">
              PS RENTAL
            </h1>
            <p className="text-slate-400 text-sm mt-1 font-medium">Gaming Station Dashboard</p>
          </div>
          
          <div className="flex items-center gap-4">
            {/* User Info */}
            <div className="flex items-center gap-3 bg-slate-800/50 rounded-xl px-4 py-2.5 border border-slate-700/50">
              <div className="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-cyan-500 flex items-center justify-center text-white font-bold text-lg shadow-lg">
                {(session.user?.name || session.user?.username || 'U')[0].toUpperCase()}
              </div>
              <div className="hidden md:block">
                <div className="text-slate-300 text-sm font-medium">
                  {session.user?.name || session.user?.username}
                </div>
                <div className="text-slate-500 text-xs">Administrator</div>
              </div>
            </div>
            
            {/* Logout Button */}
            <button
              onClick={() => signOut({ callbackUrl: '/login' })}
              className="group relative px-5 py-2.5 bg-gradient-to-r from-red-600 to-red-700 rounded-xl text-white font-medium shadow-lg hover:shadow-red-500/50 hover:scale-105 transition-all duration-300 border border-red-500/30"
            >
              <span className="relative z-10">Logout</span>
              <div className="absolute inset-0 bg-gradient-to-r from-red-500 to-red-600 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity blur"></div>
            </button>
          </div>
        </header>

        {/* Stats Bar */}
        <div className="mb-8 grid grid-cols-2 md:grid-cols-4 gap-4">
          <div className="bg-slate-900/40 backdrop-blur-sm rounded-xl p-5 border border-slate-800/50 hover:border-blue-500/30 transition-colors">
            <div className="text-slate-400 text-sm mb-1">Total Ruangan</div>
            <div className="text-3xl font-bold text-blue-400">{ruanganList?.length || 0}</div>
          </div>
          <div className="bg-slate-900/40 backdrop-blur-sm rounded-xl p-5 border border-slate-800/50 hover:border-green-500/30 transition-colors">
            <div className="text-slate-400 text-sm mb-1">Tersedia</div>
            <div className="text-3xl font-bold text-green-400">
              {ruanganList?.filter(r => r.status !== 'TERISI').length || 0}
            </div>
          </div>
          <div className="bg-slate-900/40 backdrop-blur-sm rounded-xl p-5 border border-slate-800/50 hover:border-red-500/30 transition-colors">
            <div className="text-slate-400 text-sm mb-1">Sedang Dipakai</div>
            <div className="text-3xl font-bold text-red-400">
              {ruanganList?.filter(r => r.status === 'TERISI').length || 0}
            </div>
          </div>
          <div className="bg-slate-900/40 backdrop-blur-sm rounded-xl p-5 border border-slate-800/50 hover:border-purple-500/30 transition-colors">
            <div className="text-slate-400 text-sm mb-1">Status</div>
            <div className="flex items-center gap-2">
              <div className="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
              <div className="text-lg font-bold text-green-400">Online</div>
            </div>
          </div>
        </div>
        
        {/* Ruangan Grid */}
        <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4 md:gap-6">
          {ruanganList && ruanganList.map((ruangan) => (
            <RuanganCard
              key={ruangan.id}
              ruangan={ruangan}
              onSessionChange={mutate} 
            />
          ))}
        </div>

        {/* Empty State */}
        {ruanganList && ruanganList.length === 0 && (
          <div className="text-center py-20">
            <div className="text-slate-600 text-6xl mb-4">🎮</div>
            <div className="text-slate-400 text-xl font-medium">Belum ada ruangan</div>
            <div className="text-slate-500 text-sm mt-2">Tambahkan ruangan untuk memulai</div>
          </div>
        )}
      </div>
    </main>
  );
}