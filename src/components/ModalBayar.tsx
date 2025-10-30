// src/components/ModalBayar.tsx
"use client";

import { useState } from 'react';
import { Transaksi, Ruangan, DetailPenjualan, Produk, Paket } from '@/src/types'; 

// Definisikan tipe data yang kita terima dari API 'stop'
type TransaksiWithDetails = Transaksi & {
  ruangan: Ruangan;
  paket: Paket | null; // Paket bisa jadi null (jika reguler)
  detailPenjualan: (DetailPenjualan & {
    produk: Produk;
  })[];
};

interface ModalBayarProps {
  transaksi: TransaksiWithDetails; // Gunakan tipe baru
  onClose: () => void;
  onSuccess: () => void;
}

// --- Helper Functions ---
function formatRupiah(angka: number | null | undefined) {
  if (angka === null || angka === undefined) return "Rp 0";
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
  }).format(angka);
}

function formatDurasi(start: string, end: string | null) {
  if (!end) return "0 Menit";
  const startDate = new Date(start);
  const endDate = new Date(end);
  const diffMs = endDate.getTime() - startDate.getTime();
  const diffMinutes = Math.ceil(diffMs / (1000 * 60));
  return `${diffMinutes} Menit`;
}
// ------------------------

export default function ModalBayar({ transaksi, onClose, onSuccess }: ModalBayarProps) {
  const [isLoading, setIsLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  // --- 3. Logika Perhitungan Rincian (BARU) ---
  const totalProdukCost = transaksi.detailPenjualan.reduce((total, item) => {
    return total + (item.jumlah * item.hargaSaatBeli);
  }, 0);

  const isPaket = !!transaksi.paket;
  
  let baseCost = 0;
  let baseLabel = "Biaya Sewa:";
  let overtimeCost = 0;

  const totalTagihan = transaksi.totalBiaya || 0;

  if (isPaket && transaksi.paket) {
    baseCost = transaksi.paket.hargaPaket;
    baseLabel = `Paket: ${transaksi.paket.namaPaket}`;
    
    // Hitung overtime (Total - Paket - Produk)
    overtimeCost = totalTagihan - baseCost - totalProdukCost;
    // Pastikan overtime tidak negatif (jika ada pembulatan)
    if (overtimeCost < 0) overtimeCost = 0; 
    
  } else {
    // Jika reguler, biaya sewa adalah sisanya
    baseCost = totalTagihan - totalProdukCost;
  }
  // ------------------------------------

  const handleBayar = async () => {
    // ... (Logika handleBayar tidak berubah)
    setIsLoading(true);
    setError(null);
    try {
      const res = await fetch('/api/transaksi/bayar', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ transaksiId: transaksi.id }),
      });
      const data = await res.json();
      if (!res.ok) {
        throw new Error(data.error || 'Gagal konfirmasi pembayaran');
      }
      onSuccess();
    } catch (err) {
      if (err instanceof Error) {
        setError(err.message);
      } else {
        setError('Terjadi kesalahan tidak diketahui');
      }
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 backdrop-blur-sm">
      <div className="w-full max-w-md rounded-lg bg-white p-6 shadow-lg text-gray-800">
        <h2 className="mb-4 text-center text-2xl font-bold">
          Konfirmasi Pembayaran
        </h2>

        {/* 4. Rincian Tagihan (BARU) */}
        <div className="mb-4 space-y-2">
          <div className="flex justify-between">
            <span className="font-medium">Ruangan:</span>
            <span>{transaksi.ruangan.nomorRuangan}</span>
          </div>
          <div className="flex justify-between">
            <span className="font-medium">Durasi Main:</span>
            <span>{formatDurasi(transaksi.waktuMulai, transaksi.waktuSelesai)}</span>
          </div>
          <div className="flex justify-between">
            <span className="font-medium">{baseLabel}</span>
            <span>{formatRupiah(baseCost)}</span>
          </div>
          
          {/* Tampilkan Overtime HANYA JIKA ada */}
          {overtimeCost > 0 && (
             <div className="flex justify-between text-red-600">
                <span className="font-medium">Biaya Overtime:</span>
                <span>{formatRupiah(overtimeCost)}</span>
             </div>
          )}

          {/* Rincian Produk (Jika ada) */}
          {transaksi.detailPenjualan.length > 0 && (
            <>
              <hr className="my-1" />
              <div className="font-medium">Pesanan Tambahan:</div>
              <div className="pl-4 text-sm text-gray-600">
                {transaksi.detailPenjualan.map((item) => (
                  <div key={item.id} className="flex justify-between">
                    <span>{item.jumlah}x {item.produk.nama}</span>
                    <span>{formatRupiah(item.jumlah * item.hargaSaatBeli)}</span>
                  </div>
                ))}
              </div>
              <div className="flex justify-between font-medium">
                <span>Total Produk:</span>
                <span>{formatRupiah(totalProdukCost)}</span>
              </div>
            </>
          )}

          {/* Total Keseluruhan */}
          <hr className="my-2 border-t-2 border-gray-300" />
          <div className="flex justify-between text-xl font-bold text-blue-600">
            <span>Total Tagihan:</span>
            <span>{formatRupiah(totalTagihan)}</span>
          </div>
        </div>

        {/* Error */}
        {error && (
          <div className="mb-4 rounded-md border border-red-400 bg-red-100 p-3 text-red-700">
            {error}
          </div>
        )}

        {/* Tombol Aksi */}
        <div className="mt-6 flex justify-end space-x-4">
          <button
            onClick={onClose}
            disabled={isLoading}
            className="rounded-md bg-gray-300 px-4 py-2 text-gray-800 hover:bg-gray-400 disabled:opacity-50"
          >
            Batal
          </button>
          <button
            onClick={handleBayar}
            disabled={isLoading}
            className="rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 disabled:opacity-50"
          >
            {isLoading ? 'Memproses...' : 'Konfirmasi Lunas'}
          </button>
        </div>
      </div>
    </div>
  );
}