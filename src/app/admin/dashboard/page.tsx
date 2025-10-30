// src/app/admin/dashboard/page.tsx
"use client";

import useSWR from 'swr';
import Link from 'next/link';
import { useSession, signOut } from 'next-auth/react';
import { DashboardStats } from '@/src/types';

const fetcher = (url: string) => fetch(url).then((res) => res.json());

// Helper format Rupiah
function formatRupiah(angka: number) {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
  }).format(angka);
}

// Komponen Kartu Statistik dengan design modern
interface StatCardProps {
  title: string;
  value: string | number;
  description?: string;
  icon: string;
  gradient: string;
  iconBg: string;
}

function StatCard({ title, value, description, icon, gradient, iconBg }: StatCardProps) {
  return (
    <div className="group relative overflow-hidden rounded-2xl bg-slate-900/50 backdrop-blur-sm border border-slate-800/50 p-6 shadow-xl transition-all duration-300 hover:scale-105 hover:shadow-2xl">
      {/* Gradient Glow */}
      <div className={`absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300 ${gradient}`}></div>
      
      <div className="relative z-10">
        {/* Icon */}
        <div className={`inline-flex items-center justify-center w-14 h-14 rounded-xl mb-4 ${iconBg} shadow-lg`}>
          <span className="text-3xl">{icon}</span>
        </div>
        
        {/* Content */}
        <h3 className="text-sm font-semibold uppercase tracking-wider text-slate-400 mb-2">
          {title}
        </h3>
        <p className="text-4xl font-black text-white mb-2 bg-gradient-to-r from-white to-slate-300 bg-clip-text text-transparent">
          {value}
        </p>
        {description && (
          <p className="text-sm text-slate-500 font-medium">{description}</p>
        )}
      </div>

      {/* Corner Accent */}
      <div className={`absolute bottom-0 right-0 w-32 h-32 ${gradient} blur-3xl opacity-20 -mb-16 -mr-16`}></div>
    </div>
  );
}

// Quick Access Card Component
interface QuickAccessProps {
  href: string;
  title: string;
  icon: string;
  gradient: string;
  isHighlight?: boolean;
}

function QuickAccessCard({ href, title, icon, gradient, isHighlight = false }: QuickAccessProps) {
  return (
    <Link 
      href={href}
      className={`group relative overflow-hidden rounded-xl p-5 transition-all duration-300 hover:scale-105 border-2 ${
        isHighlight 
          ? 'bg-gradient-to-br from-blue-600 to-cyan-600 border-blue-400/50 shadow-lg shadow-blue-500/30' 
          : 'bg-slate-900/60 backdrop-blur-sm border-slate-700/50 hover:border-slate-600'
      }`}
    >
      <div className={`absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300 ${gradient}`}></div>
      
      <div className="relative z-10 flex items-center gap-4">
        <div className={`flex-shrink-0 w-12 h-12 rounded-lg flex items-center justify-center ${
          isHighlight ? 'bg-white/20' : 'bg-slate-800/80'
        }`}>
          <span className="text-2xl">{icon}</span>
        </div>
        <div className="flex-1 min-w-0">
          <h3 className={`font-bold text-sm truncate ${
            isHighlight ? 'text-white' : 'text-slate-200'
          }`}>
            {title}
          </h3>
        </div>
        <div className={`flex-shrink-0 ${isHighlight ? 'text-white' : 'text-slate-500'}`}>
          →
        </div>
      </div>
    </Link>
  );
}

// Komponen Utama Halaman
export default function AdminDashboardPage() {
  const { data: session } = useSession();

  const { 
    data: stats, 
    error, 
    isLoading 
  } = useSWR<DashboardStats>('/api/admin/dashboard-stats', fetcher, {
    refreshInterval: 10000
  });

  if (isLoading) {
    return (
      <main className="flex min-h-screen items-center justify-center bg-gradient-to-br from-slate-950 via-slate-900 to-blue-950">
        <div className="text-center space-y-4">
          <div className="inline-block animate-spin rounded-full h-16 w-16 border-4 border-blue-500 border-t-transparent"></div>
          <div className="text-blue-400 text-xl font-medium">Loading Admin Dashboard...</div>
        </div>
      </main>
    );
  }

  if (error) {
    return (
      <main className="flex min-h-screen items-center justify-center bg-gradient-to-br from-slate-950 via-slate-900 to-blue-950">
        <div className="text-center space-y-3">
          <div className="text-red-500 text-6xl">⚠️</div>
          <div className="text-red-400 text-xl font-medium">Failed to load statistics</div>
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

  if (!stats) {
    return (
      <main className="flex min-h-screen items-center justify-center bg-gradient-to-br from-slate-950 via-slate-900 to-blue-950">
        <div className="text-slate-400 text-xl">Data not available</div>
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
        <header className="mb-10">
          <div className="backdrop-blur-sm bg-slate-900/30 rounded-2xl p-6 border border-slate-800/50 shadow-2xl">
            <div className="flex items-center gap-4 mb-3">
              <div className="w-16 h-16 rounded-2xl bg-gradient-to-br from-purple-500 to-blue-500 flex items-center justify-center text-3xl shadow-lg shadow-purple-500/30">
                👑
              </div>
              <div>
                <h1 className="text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-purple-400 via-blue-400 to-cyan-400">
                  Admin Dashboard
                </h1>
                <p className="text-slate-400 text-sm mt-1 font-medium">
                  Welcome back, {session?.user?.name || 'Administrator'}
                </p>
              </div>
            </div>
            <div className="flex items-center gap-2 text-sm">
              <div className="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
              <span className="text-slate-400">Business Overview - Real-time Data</span>
            </div>
          </div>
        </header>

        {/* Statistics Cards */}
        <div className="mb-10 grid grid-cols-1 md:grid-cols-3 gap-6">
          <StatCard
            title="Revenue Today"
            value={formatRupiah(stats.totalPendapatanHariIni)}
            description={`${stats.totalTransaksiHariIni} completed transactions`}
            icon="💰"
            gradient="bg-gradient-to-br from-green-500/20 to-emerald-500/20"
            iconBg="bg-gradient-to-br from-green-500 to-emerald-600"
          />
          <StatCard
            title="Room Occupancy"
            value={`${stats.ruanganTerisi} / ${stats.totalRuangan}`}
            description="Current operational status"
            icon="🎮"
            gradient="bg-gradient-to-br from-blue-500/20 to-cyan-500/20"
            iconBg="bg-gradient-to-br from-blue-500 to-cyan-600"
          />
          <StatCard
            title="Total Transactions"
            value={stats.totalTransaksiHariIni}
            description="Completed today"
            icon="📊"
            gradient="bg-gradient-to-br from-purple-500/20 to-pink-500/20"
            iconBg="bg-gradient-to-br from-purple-500 to-pink-600"
          />
        </div>

        {/* Quick Access Section */}
        <div className="backdrop-blur-sm bg-slate-900/30 rounded-2xl p-6 border border-slate-800/50 shadow-2xl">
          <div className="flex items-center gap-3 mb-6">
            <div className="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-500 to-cyan-500 flex items-center justify-center text-xl shadow-lg">
              ⚡
            </div>
            <h2 className="text-2xl font-bold text-white">Quick Access</h2>
          </div>
          
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <QuickAccessCard
              href="/admin/laporan"
              title="Detailed Reports"
              icon="📈"
              gradient="bg-blue-500/10"
              isHighlight
            />
            <QuickAccessCard
              href="/admin/ruangan"
              title="Room Management"
              icon="🏠"
              gradient="bg-slate-500/10"
            />
            <QuickAccessCard
              href="/admin/produk"
              title="Product Management"
              icon="🛍️"
              gradient="bg-slate-500/10"
            />
            <QuickAccessCard
              href="/admin/user"
              title="User Management"
              icon="👥"
              gradient="bg-slate-500/10"
            />
            <QuickAccessCard
              href="/admin/pelanggan"
              title="Customer Management"
              icon="👤"
              gradient="bg-slate-500/10"
            />
            <QuickAccessCard
              href="/admin/paket"
              title="Package Management"
              icon="📦"
              gradient="bg-slate-500/10"
            />
            <QuickAccessCard
              href="/admin/konsol"
              title="Console Management"
              icon="🎯"
              gradient="bg-slate-500/10"
            />
<button
  onClick={() => signOut({ callbackUrl: '/login' })}
  className="rounded-lg bg-red-600 p-4 text-center font-bold text-white shadow hover:bg-red-700"
>
  Logout
</button>
          </div>
        </div>

        {/* Footer Info */}
        <div className="mt-8 text-center">
          <p className="text-slate-500 text-sm">
            Auto-refresh every 10 seconds • Last updated: {new Date().toLocaleTimeString('id-ID')}
          </p>
        </div>
      </div>
    </main>
  );
}