// src/components/RuanganCard.tsx
"use client";

import { useState } from 'react';
import Timer from './Timer';
import ModalBayar from './ModalBayar';
import ModalMulaiSesi from './ModalMulaiSesi';
import ModalTambahPesanan from './ModalTambahPesanan';
import { RuanganDashboradData, Transaksi, Ruangan, RuanganAdminData, DetailPenjualan, Produk, Paket } from '@/src/types';
import { useSession } from "next-auth/react";

type TransaksiWithRuangan = Transaksi & {
  ruangan: Ruangan;
  paket: Paket | null;
  detailPenjualan: (DetailPenjualan & {
    produk: Produk;
  })[];
};

interface RuanganCardProps {
  ruangan: RuanganDashboradData;
  onSessionChange: () => void;
}

export default function RuanganCard({ ruangan, onSessionChange }: RuanganCardProps) {
  const [isLoading, setIsLoading] = useState(false);
  const { data: session } = useSession();
  
  // State untuk modal
  const [modalBayarData, setModalBayarData] = useState<TransaksiWithRuangan | null>(null);
  const [showModalMulai, setShowModalMulai] = useState<boolean>(false);
  const [showModalTambahPesanan, setShowModalTambahPesanan] = useState<boolean>(false);

  const isTerisi = ruangan.status === 'TERISI';
  const activeTransaksi: Transaksi | null = isTerisi && ruangan.transaksi.length > 0 
    ? ruangan.transaksi[0] 
    : null;

  const handleStop = async () => {
    if (!activeTransaksi) return;
    setIsLoading(true);
    try {
      const res = await fetch('/api/transaksi/stop', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ 
          transaksiId: activeTransaksi.id 
        }),
      });
      
      if (!res.ok) {
        throw new Error('Failed to stop transaction');
      }
      
      const data: TransaksiWithRuangan = await res.json();
      setModalBayarData(data);
      
    } catch (err) {
      console.error(err);
      alert('Gagal menghentikan transaksi');
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <>
      {/* Card Desain Baru */}
      <div className={`group relative overflow-hidden rounded-2xl transition-all duration-300 hover:scale-105 ${
        isTerisi 
          ? 'bg-gradient-to-br from-red-950/90 to-red-900/80 border-2 border-red-500/50 shadow-lg shadow-red-500/20 hover:shadow-red-500/40' 
          : 'bg-gradient-to-br from-slate-900/90 to-slate-800/80 border-2 border-slate-700/50 shadow-lg shadow-blue-500/10 hover:shadow-blue-500/30'
      }`}>
        {/* Glow Effect on Hover */}
        <div className={`absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300 ${
          isTerisi ? 'bg-red-500/5' : 'bg-blue-500/5'
        }`}></div>
        
        {/* Status Indicator */}
        <div className="absolute top-3 right-3 z-10">
          <div className={`flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold backdrop-blur-sm ${
            isTerisi 
              ? 'bg-red-500/20 text-red-300 border border-red-500/30' 
              : 'bg-green-500/20 text-green-300 border border-green-500/30'
          }`}>
            <div className={`w-1.5 h-1.5 rounded-full ${isTerisi ? 'bg-red-400 animate-pulse' : 'bg-green-400'}`}></div>
            {isTerisi ? 'BUSY' : 'FREE'}
          </div>
        </div>

        <div className="relative z-10 p-5">
          {/* Room Header */}
          <div className="mb-4">
            <div className="flex items-center gap-2 mb-1">
              <div className={`w-8 h-8 rounded-lg flex items-center justify-center text-lg font-black ${
                isTerisi 
                  ? 'bg-gradient-to-br from-red-500 to-red-600 text-white shadow-lg shadow-red-500/30' 
                  : 'bg-gradient-to-br from-blue-500 to-cyan-500 text-white shadow-lg shadow-blue-500/30'
              }`}>
                🎮
              </div>
              <h3 className="text-xl font-black text-white truncate">
                {ruangan.nomorRuangan}
              </h3>
            </div>
            <p className="text-xs text-slate-400 font-medium uppercase tracking-wider">
              {ruangan.tipeRuangan}
            </p>
          </div>

          {/* Timer/Status Section */}
          <div className="mb-4">
            {isTerisi && activeTransaksi ? (
              <div className="bg-slate-950/50 backdrop-blur-sm rounded-xl p-4 border border-red-500/20">
                <div className="text-xs text-red-300 font-semibold mb-2 uppercase tracking-wide">
                  ⏱️ Session Active
                </div>
                <Timer startTime={activeTransaksi.waktuMulai} />
              </div>
            ) : (
              <div className="bg-slate-950/50 backdrop-blur-sm rounded-xl p-4 border border-slate-700/30">
                <div className="text-xs text-slate-500 font-semibold mb-2 uppercase tracking-wide">
                  Ready to Start
                </div>
                <div className="text-3xl font-mono font-bold text-slate-600 tracking-wider">
                  00:00:00
                </div>
              </div>
            )}
          </div>

          {/* Action Buttons */}
          {isTerisi ? (
            <div className="space-y-3">
              {/* Tombol Stop */}
              <button
                onClick={handleStop}
                disabled={isLoading}
                className="relative w-full group/btn overflow-hidden rounded-xl bg-gradient-to-r from-red-600 to-red-700 py-3 px-4 font-bold text-white shadow-lg transition-all hover:shadow-red-500/50 disabled:opacity-50 disabled:cursor-not-allowed hover:scale-105"
              >
                <span className="relative z-10 flex items-center justify-center gap-2">
                  {isLoading ? (
                    <>
                      <div className="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                      Processing...
                    </>
                  ) : (
                    <>
                      <span>⏹️</span>
                      Stop & Calculate
                    </>
                  )}
                </span>
                <div className="absolute inset-0 bg-gradient-to-r from-red-500 to-red-600 opacity-0 group-hover/btn:opacity-100 transition-opacity"></div>
              </button>

              {/* Tombol Tambah Pesanan */}
              <button
                onClick={() => setShowModalTambahPesanan(true)}
                disabled={isLoading || !activeTransaksi}
                className="relative w-full group/btn overflow-hidden rounded-xl bg-gradient-to-r from-emerald-600 to-teal-700 py-2 px-4 text-sm font-bold text-white shadow transition-all hover:shadow-emerald-500/40 disabled:opacity-50 disabled:cursor-not-allowed hover:scale-105"
              >
                <span className="relative z-10 flex items-center justify-center gap-1.5">
                  <span>🛒</span>
                  Tambah Pesanan
                </span>
                <div className="absolute inset-0 bg-gradient-to-r from-emerald-500 to-teal-600 opacity-0 group-hover/btn:opacity-100 transition-opacity"></div>
              </button>
            </div>
          ) : (
            // Tombol Mulai
            <button
              onClick={() => setShowModalMulai(true)}
              disabled={isLoading || !session}
              className="relative w-full group/btn overflow-hidden rounded-xl bg-gradient-to-r from-blue-600 to-cyan-600 py-3 px-4 font-bold text-white shadow-lg transition-all hover:shadow-blue-500/50 disabled:opacity-50 disabled:cursor-not-allowed hover:scale-105"
            >
              <span className="relative z-10 flex items-center justify-center gap-2">
                {isLoading ? (
                  <>
                    <div className="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                    Starting...
                  </>
                ) : (
                  <>
                    <span>▶️</span>
                    Start Session
                  </>
                )}
              </span>
              <div className="absolute inset-0 bg-gradient-to-r from-blue-500 to-cyan-500 opacity-0 group-hover/btn:opacity-100 transition-opacity"></div>
            </button>
          )}
        </div>

        {/* Corner Accent */}
        <div className={`absolute bottom-0 right-0 w-20 h-20 ${
          isTerisi ? 'bg-red-500/10' : 'bg-blue-500/10'
        } blur-2xl rounded-full -mb-10 -mr-10`}></div>
      </div>

      {/* Modal Pembayaran */}
      {modalBayarData && (
        <ModalBayar
          transaksi={modalBayarData}
          onClose={() => setModalBayarData(null)}
          onSuccess={() => {
            setModalBayarData(null);
            onSessionChange();
          }}
        />
      )}

      {/* Modal Mulai Sesi */}
      {showModalMulai && (
        <ModalMulaiSesi
          ruangan={ruangan}
          onClose={() => setShowModalMulai(false)}
          onSuccess={() => {
            setShowModalMulai(false);
            onSessionChange();
          }}
        />
      )}

      {/* Modal Tambah Pesanan */}
      {showModalTambahPesanan && activeTransaksi && (
        <ModalTambahPesanan
          transaksiId={activeTransaksi.id}
          onClose={() => setShowModalTambahPesanan(false)}
          onSuccess={() => {
            // Refresh data jika diperlukan (misal: update total pesanan)
            // Modal tidak otomatis tertutup, biarkan user menambah item lain
            onSessionChange();
          }}
        />
      )}
    </>
  );
}