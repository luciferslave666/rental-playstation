// src/components/ModalTambahPesanan.tsx
"use client";

import { useState } from 'react';
import useSWR from 'swr';
import { Produk } from '@/src/types';

const fetcher = (url: string) => fetch(url).then((res) => res.json());

interface ModalTambahPesananProps {
  transaksiId: number;
  onClose: () => void;
  onSuccess?: () => void; // TAMBAHKAN: Optional callback setelah berhasil tambah pesanan
}

export default function ModalTambahPesanan({ 
  transaksiId, 
  onClose,
  onSuccess // TAMBAHKAN parameter ini
}: ModalTambahPesananProps) {
  const [selectedProdukId, setSelectedProdukId] = useState<string>('');
  const [jumlah, setJumlah] = useState<number>(1);
  
  const [isLoading, setIsLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [successMessage, setSuccessMessage] = useState<string | null>(null);

  // Ambil daftar produk
  const { data: produkList, error: produkError } = useSWR<Produk[]>('/api/admin/produk', fetcher);

  const handleTambahPesanan = async () => {
    // Validasi input
    if (!selectedProdukId) {
      setError('Pilih produk terlebih dahulu.');
      return;
    }
    if (jumlah <= 0) {
      setError('Jumlah harus lebih dari 0.');
      return;
    }
    
    // Validasi stok
    const selectedProduk = produkList?.find(p => p.id === parseInt(selectedProdukId));
    if (selectedProduk && jumlah > selectedProduk.stok) {
      setError(`Stok tidak cukup. Tersedia: ${selectedProduk.stok}`);
      return;
    }
    
    setIsLoading(true);
    setError(null);
    setSuccessMessage(null);

    try {
      const res = await fetch('/api/transaksi/tambah-pesanan', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          transaksiId: transaksiId,
          produkId: parseInt(selectedProdukId),
          jumlah: jumlah,
        }),
      });

      const data = await res.json();
      if (!res.ok) {
        throw new Error(data.error || 'Gagal menambahkan pesanan');
      }
      
      // Tampilkan pesan sukses
      const namaProduk = produkList?.find(p => p.id === parseInt(selectedProdukId))?.nama || 'Produk';
      setSuccessMessage(`${jumlah}x ${namaProduk} berhasil ditambahkan!`);
      
      // Reset form
      setSelectedProdukId('');
      setJumlah(1);
      
      // TAMBAHKAN: Panggil callback onSuccess jika ada
      if (onSuccess) {
        onSuccess();
      }

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

  const selectedProduk = produkList?.find(p => p.id === parseInt(selectedProdukId));

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/75 backdrop-blur-sm p-4">
      <div className="w-full max-w-lg rounded-xl bg-gradient-to-br from-slate-900 to-slate-800 p-6 shadow-2xl border-2 border-slate-700 animate-in fade-in zoom-in duration-200">
        {/* Header */}
        <div className="mb-6 text-center">
          <h2 className="text-3xl font-black text-white bg-gradient-to-r from-emerald-400 to-teal-400 bg-clip-text text-transparent">
            🛒 Tambah Pesanan
          </h2>
          <p className="text-sm text-slate-400 mt-1">
            Transaksi #{transaksiId}
          </p>
        </div>

        {/* Alert Messages */}
        {error && (
          <div className="mb-4 rounded-lg border-2 border-red-500/50 bg-red-950/50 p-3 text-red-300 flex items-start gap-2 animate-in slide-in-from-top">
            <span className="text-lg">⚠️</span>
            <span className="flex-1">{error}</span>
          </div>
        )}
        
        {successMessage && (
          <div className="mb-4 rounded-lg border-2 border-green-500/50 bg-green-950/50 p-3 text-green-300 flex items-start gap-2 animate-in slide-in-from-top">
            <span className="text-lg">✅</span>
            <span className="flex-1">{successMessage}</span>
          </div>
        )}

        {/* Form */}
        <div className="space-y-5">
          {/* Dropdown Produk */}
          <div>
            <label className="mb-2 block text-sm font-bold text-slate-300 uppercase tracking-wide">
              Pilih Produk <span className="text-red-400">*</span>
            </label>
            <select
              value={selectedProdukId}
              onChange={(e) => {
                setSelectedProdukId(e.target.value);
                setJumlah(1); // Reset jumlah saat ganti produk
                setError(null);
                setSuccessMessage(null);
              }}
              disabled={produkError || !produkList}
              className="w-full rounded-lg border-2 border-slate-600 bg-slate-800 p-3 text-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 disabled:opacity-50 disabled:cursor-not-allowed transition-all"
            >
              <option value="">-- Pilih Produk --</option>
              {!produkList && !produkError && <option disabled>⏳ Memuat produk...</option>}
              {produkError && <option disabled>❌ Gagal memuat produk</option>}
              
              {/* Produk tersedia */}
              {produkList && produkList.length > 0 && produkList
                .filter(p => p.stok > 0)
                .map((p) => (
                  <option key={p.id} value={p.id}>
                    {p.nama} • Rp {p.harga.toLocaleString('id-ID')} • Stok: {p.stok}
                  </option>
              ))}
              
              {/* Produk habis stok */}
              {produkList && produkList
                .filter(p => p.stok <= 0)
                .map((p) => (
                  <option key={p.id} value={p.id} disabled>
                    {p.nama} • STOK HABIS
                  </option>
              ))}
              
              {/* Jika tidak ada produk sama sekali */}
              {produkList && produkList.length === 0 && (
                <option disabled>Tidak ada produk tersedia</option>
              )}
            </select>
          </div>

          {/* Detail Produk Terpilih */}
          {selectedProduk && (
            <div className="rounded-lg bg-slate-950/50 border border-slate-700 p-4 space-y-2">
              <div className="flex justify-between items-center">
                <span className="text-sm text-slate-400">Harga Satuan</span>
                <span className="text-lg font-bold text-white">
                  Rp {selectedProduk.harga.toLocaleString('id-ID')}
                </span>
              </div>
              <div className="flex justify-between items-center">
                <span className="text-sm text-slate-400">Stok Tersedia</span>
                <span className="text-lg font-bold text-emerald-400">
                  {selectedProduk.stok} pcs
                </span>
              </div>
            </div>
          )}

          {/* Input Jumlah */}
          <div>
            <label className="mb-2 block text-sm font-bold text-slate-300 uppercase tracking-wide">
              Jumlah <span className="text-red-400">*</span>
            </label>
            <div className="flex items-center gap-3">
              <button
                type="button"
                onClick={() => setJumlah(Math.max(1, jumlah - 1))}
                disabled={!selectedProdukId || jumlah <= 1}
                className="w-12 h-12 rounded-lg bg-slate-700 text-white font-bold text-xl hover:bg-slate-600 disabled:opacity-30 disabled:cursor-not-allowed transition-all"
              >
                −
              </button>
              
              <input
                type="number"
                value={jumlah}
                onChange={(e) => {
                  const val = parseInt(e.target.value) || 1;
                  setJumlah(Math.max(1, Math.min(val, selectedProduk?.stok || 999)));
                }}
                min="1"
                max={selectedProduk?.stok || 999}
                disabled={!selectedProdukId}
                className="flex-1 text-center rounded-lg border-2 border-slate-600 bg-slate-800 p-3 text-2xl font-bold text-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 disabled:opacity-50 disabled:cursor-not-allowed transition-all"
              />
              
              <button
                type="button"
                onClick={() => setJumlah(Math.min((selectedProduk?.stok || 999), jumlah + 1))}
                disabled={!selectedProdukId || (selectedProduk && jumlah >= selectedProduk.stok)}
                className="w-12 h-12 rounded-lg bg-slate-700 text-white font-bold text-xl hover:bg-slate-600 disabled:opacity-30 disabled:cursor-not-allowed transition-all"
              >
                +
              </button>
            </div>
          </div>

          {/* Total Harga Preview */}
          {selectedProduk && (
            <div className="rounded-lg bg-gradient-to-r from-emerald-950/50 to-teal-950/50 border-2 border-emerald-500/30 p-4">
              <div className="flex justify-between items-center">
                <span className="text-sm font-bold text-emerald-300 uppercase tracking-wide">
                  Total Harga
                </span>
                <span className="text-2xl font-black text-white">
                  Rp {(selectedProduk.harga * jumlah).toLocaleString('id-ID')}
                </span>
              </div>
            </div>
          )}

          {/* Tombol Tambah */}
          <button
            onClick={handleTambahPesanan}
            disabled={
              isLoading || 
              !selectedProdukId || 
              jumlah <= 0 || 
              (selectedProduk && jumlah > selectedProduk.stok)
            }
            className="w-full rounded-lg bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-4 text-lg font-bold text-white shadow-lg hover:shadow-emerald-500/50 disabled:opacity-50 disabled:cursor-not-allowed hover:scale-105 transition-all"
          >
            {isLoading ? (
              <span className="flex items-center justify-center gap-2">
                <div className="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                Menambahkan...
              </span>
            ) : (
              '✓ Tambah ke Transaksi'
            )}
          </button>
        </div>

        {/* Footer Buttons */}
        <div className="mt-6 pt-4 border-t border-slate-700 flex justify-end">
          <button
            onClick={onClose}
            className="rounded-lg bg-slate-700 px-6 py-2 text-white font-semibold hover:bg-slate-600 transition-all"
          >
            Selesai
          </button>
        </div>
      </div>
    </div>
  );
}