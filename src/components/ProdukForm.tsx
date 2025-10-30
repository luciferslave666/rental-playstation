// src/components/RuanganForm.tsx
"use client";

import { useState, useEffect } from 'react';
import useSWR from 'swr';
// 1. Import tipe Konsol dan pastikan RuanganAdminData sudah benar
import { RuanganAdminData, Konsol } from '@/src/types'; 

const fetcher = (url: string) => fetch(url).then((res) => res.json());

interface RuanganFormProps {
  initialData: RuanganAdminData | null;
  onSuccess: () => void;
  apiError: string | null;
  setApiError: (error: string | null) => void;
}

export default function RuanganForm({ 
  initialData, 
  onSuccess,
  apiError,
  setApiError
}: RuanganFormProps) {
  // State yang sudah ada
  const [nomorRuangan, setNomorRuangan] = useState('');
  const [tarifPerJam, setTarifPerJam] = useState(0);
  const [tipeRuangan, setTipeRuangan] = useState('REGULER');
  const [deskripsiFasilitas, setDeskripsiFasilitas] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  
  // 2. State baru untuk menyimpan ID konsol yang tercentang
  const [selectedKonsolIds, setSelectedKonsolIds] = useState<Set<number>>(new Set());

  // 3. Ambil daftar semua konsol yang tersedia
  const { data: konsolList, error: konsolError } = useSWR<Konsol[]>('/api/admin/konsol', fetcher);

  const isEditMode = initialData !== null;

  useEffect(() => {
    setApiError(null); // Selalu reset error saat data berubah
    if (initialData) {
      // Isi form dengan data yang ada (mode edit)
      setNomorRuangan(initialData.nomorRuangan);
      setTarifPerJam(initialData.tarifPerJam);
      setTipeRuangan(initialData.tipeRuangan);
      setDeskripsiFasilitas(initialData.deskripsiFasilitas);
      
      // 4. Isi state checkbox dari data relasi
      // Pastikan initialData.konsol ada (sesuai tipe RuanganAdminData)
      const initialKonsolIds = new Set(initialData.konsol?.map(k => k.id) || []);
      setSelectedKonsolIds(initialKonsolIds);
      
    } else {
      // Reset form (mode tambah baru)
      setNomorRuangan('');
      setTarifPerJam(0);
      setTipeRuangan('REGULER');
      setDeskripsiFasilitas('');
      setSelectedKonsolIds(new Set()); // Kosongkan set
    }
  }, [initialData, setApiError]);

  // 5. Handler untuk checkbox
  const handleKonsolChange = (konsolId: number) => {
    setSelectedKonsolIds(prevIds => {
      const newIds = new Set(prevIds);
      if (newIds.has(konsolId)) {
        newIds.delete(konsolId); // Uncheck
      } else {
        newIds.add(konsolId); // Check
      }
      return newIds;
    });
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setIsLoading(true);
    setApiError(null);

    const url = isEditMode 
      ? `/api/admin/ruangan/${initialData.id}` 
      : '/api/admin/ruangan';
    const method = isEditMode ? 'PUT' : 'POST';

    try {
      const res = await fetch(url, {
        method: method,
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          nomorRuangan,
          tarifPerJam,
          tipeRuangan,
          deskripsiFasilitas,
          // 6. Kirim ID konsol sebagai array
          konsolIds: Array.from(selectedKonsolIds), 
        }),
      });

      const data = await res.json();
      if (!res.ok) {
        throw new Error(data.error || 'Gagal menyimpan data');
      }
      
      onSuccess(); // Panggil fungsi onSuccess dari parent
      
    } catch (err) {
      if (err instanceof Error) {
        setApiError(err.message);
      } else {
        setApiError('Terjadi kesalahan tidak diketahui');
      }
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <form onSubmit={handleSubmit} className="space-y-4 text-gray-700">
      {apiError && (
        <div className="p-3 bg-red-100 border border-red-400 text-red-700 rounded-md">
          {apiError}
        </div>
      )}
      
      {/* ... (Input Nomor Ruangan, Tarif, Tipe, Deskripsi - tidak berubah) ... */}
      <div>
        <label className="block text-sm font-medium mb-1">Nomor Ruangan*</label>
        <input type="text" value={nomorRuangan} onChange={(e) => setNomorRuangan(e.target.value)} required className="w-full p-2 border border-gray-300 rounded-md" />
      </div>
      <div>
        <label className="block text-sm font-medium mb-1">Tarif per Jam (Rp)*</label>
        <input type="number" value={tarifPerJam} onChange={(e) => setTarifPerJam(parseFloat(e.target.value) || 0)} required className="w-full p-2 border border-gray-300 rounded-md" />
      </div>
      <div>
        <label className="block text-sm font-medium mb-1">Tipe Ruangan</label>
        <select value={tipeRuangan} onChange={(e) => setTipeRuangan(e.target.value)} className="w-full p-2 border border-gray-300 rounded-md bg-white">
          <option value="REGULER">REGULER</option>
          <option value="VIP">VIP</option>
        </select>
      </div>
      <div>
        <label className="block text-sm font-medium mb-1">Deskripsi Fasilitas</label>
        <textarea value={deskripsiFasilitas} onChange={(e) => setDeskripsiFasilitas(e.target.value)} rows={3} className="w-full p-2 border border-gray-300 rounded-md" />
      </div>

      {/* 7. Checkbox List untuk Konsol */}
      <div>
        <label className="block text-sm font-medium mb-2">Konsol di Ruangan Ini:</label>
        <div className="space-y-2">
          {konsolError && <div className="text-red-500 text-sm">Gagal memuat daftar konsol.</div>}
          {!konsolList && !konsolError && <div className="text-gray-500 text-sm">Memuat konsol...</div>}
          
          {konsolList?.map(konsol => (
            <div key={konsol.id} className="flex items-center">
              <input
                type="checkbox"
                id={`konsol-${konsol.id}`}
                checked={selectedKonsolIds.has(konsol.id)}
                onChange={() => handleKonsolChange(konsol.id)}
                className="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
              />
              <label htmlFor={`konsol-${konsol.id}`} className="ml-2 block text-sm text-gray-900">
                {konsol.seriKonsol}
              </label>
            </div>
          ))}
        </div>
      </div>
      
      <button
        type="submit"
        disabled={isLoading}
        className="w-full bg-blue-600 text-white p-2 rounded-md hover:bg-blue-700 disabled:opacity-50"
      >
        {isLoading ? 'Menyimpan...' : (initialData ? 'Update Ruangan' : 'Simpan Ruangan Baru')}
      </button>
    </form>
  );
}