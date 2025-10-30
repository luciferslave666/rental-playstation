// src/components/ModalMulaiSesi.tsx
"use client";

import { useState } from 'react';
import useSWR from 'swr'; // 1. useSWR sudah di-import
import { useSession } from 'next-auth/react';
// 2. Import tipe data 'Paket'
import { Pelanggan, Ruangan, Paket } from '@/src/types'; // Sesuaikan path jika perlu

const fetcher = (url: string) => fetch(url).then((res) => res.json());

// 3. Tambahkan helper format Rupiah (untuk dropdown paket)
function formatRupiah(angka: number) {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
  }).format(angka);
}

interface ModalMulaiSesiProps {
  ruangan: Ruangan; 
  onClose: () => void;
  onSuccess: () => void;
}

export default function ModalMulaiSesi({ ruangan, onClose, onSuccess }: ModalMulaiSesiProps) {
  const { data: session } = useSession();
  
  // State yang sudah ada
  const [selectedPelangganId, setSelectedPelangganId] = useState<string>('');
  const [isLoading, setIsLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  // 4. Tambahkan state baru untuk paket yang dipilih
  const [selectedPaketId, setSelectedPaketId] = useState<string>(''); // Default: string kosong (Reguler)

  // SWR untuk pelanggan (sudah ada)
  const { data: pelangganList, error: pelangganError } = useSWR<Pelanggan[]>('/api/pelanggan/list', fetcher);
  
  // 5. Tambahkan SWR baru untuk mengambil daftar paket
  const { data: paketList, error: paketError } = useSWR<Paket[]>('/api/admin/paket', fetcher);


  const handleMulai = async () => {
    if (!session || !session.user) {
      setError("Sesi tidak valid. Silakan login ulang.");
      return;
    }

    setIsLoading(true);
    setError(null);

    const kasirId = parseInt(session.user.id);
    const pelangganId = selectedPelangganId ? parseInt(selectedPelangganId) : null;
    
    // 6. Siapkan paketId untuk dikirim ke API
    const paketId = selectedPaketId ? parseInt(selectedPaketId) : null;

    try {
      // 7. Modifikasi body fetch untuk mengirim 'paketId'
      const res = await fetch('/api/transaksi/start', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          idRuangan: ruangan.id,
          idUser: kasirId,
          idPelanggan: pelangganId, 
          paketId: paketId, // <-- KIRIM PAKET ID
        }),
      });

      const data = await res.json();
      if (!res.ok) {
        throw new Error(data.error || 'Gagal memulai transaksi');
      }

      onSuccess(); // Panggil onSuccess (refresh dashboard & tutup modal)

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
      <div className="w-full max-w-md rounded-lg bg-slate-800 p-6 shadow-lg border border-slate-700">
        <h2 className="mb-4 text-center text-2xl font-bold text-white">
          Mulai Sesi - {ruangan.nomorRuangan}
        </h2>

        {error && (
          <div className="mb-4 rounded-md border border-red-400 bg-red-900 p-3 text-red-300">
            {error}
          </div>
        )}
        
        <div className="space-y-4">
          {/* 8. TAMBAHKAN Dropdown Tipe Billing / Paket */}
          <div>
            <label className="mb-1 block text-sm font-medium text-slate-300">
              Tipe Billing*
            </label>
            <select
              value={selectedPaketId}
              onChange={(e) => setSelectedPaketId(e.target.value)}
              disabled={paketError || !paketList}
              className="w-full rounded-md border border-slate-600 p-2 text-white bg-slate-700"
            >
              <option value="">-- Reguler (Per Jam) --</option>
              {!paketList && !paketError && <option disabled>Memuat paket...</option>}
              {paketError && <option disabled>Gagal memuat paket</option>}
              {paketList && paketList.map((p) => (
                <option key={p.id} value={p.id}>
                  {p.namaPaket} ({formatRupiah(p.hargaPaket)})
                </option>
              ))}
            </select>
          </div>

          {/* Dropdown Pelanggan (Sudah ada) */}
          <div>
            <label className="mb-1 block text-sm font-medium text-slate-300">
              Pilih Pelanggan (Opsional)
            </label>
            <select
              value={selectedPelangganId}
              onChange={(e) => setSelectedPelangganId(e.target.value)}
              disabled={pelangganError || !pelangganList}
              className="w-full rounded-md border border-slate-600 p-2 text-white bg-slate-700"
            >
              <option value="">-- Non-Member --</option> 
              {!pelangganList && !pelangganError && <option disabled>Memuat pelanggan...</option>}
              {pelangganError && <option disabled>Gagal memuat pelanggan</option>}
              {pelangganList && pelangganList.map((p) => (
                <option key={p.id} value={p.id}>
                  {p.namaPelanggan}
                </option>
              ))}
            </select>
          </div>
        </div>

        {/* Tombol Aksi */}
        <div className="mt-6 flex justify-end space-x-4">
          <button
            onClick={onClose}
            disabled={isLoading}
            className="rounded-md bg-slate-600 px-4 py-2 text-white hover:bg-slate-500 disabled:opacity-50"
          >
            Batal
          </button>
          <button
            onClick={handleMulai}
            disabled={isLoading || !session}
            className="rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 disabled:opacity-50"
          >
            {isLoading ? 'Memulai...' : 'Konfirmasi Mulai Sesi'}
          </button>
        </div>
      </div>
    </div>
  );
}