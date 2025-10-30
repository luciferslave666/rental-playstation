// src/components/PelangganForm.tsx
"use client";

import { useState, useEffect } from 'react';
import { Pelanggan } from '@/src/types'; // Import tipe Pelanggan

interface PelangganFormProps {
  initialData: Pelanggan | null;
  onSuccess: () => void;
  // Untuk menampilkan error dari API (misal No HP duplikat)
  apiError: string | null; 
  setApiError: (error: string | null) => void;
}

export default function PelangganForm({ 
  initialData, 
  onSuccess, 
  apiError, 
  setApiError 
}: PelangganFormProps) {
  const [namaPelanggan, setNamaPelanggan] = useState('');
  const [noHp, setNoHp] = useState('');
  const [isLoading, setIsLoading] = useState(false);

  useEffect(() => {
    if (initialData) {
      setNamaPelanggan(initialData.namaPelanggan);
      setNoHp(initialData.noHp || ''); // Set string kosong jika null
    } else {
      // Reset form
      setNamaPelanggan('');
      setNoHp('');
    }
    // Reset error API setiap ganti mode
    setApiError(null); 
  }, [initialData, setApiError]);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setIsLoading(true);
    setApiError(null); // Clear error lama

    const isEditMode = initialData !== null;
    const url = isEditMode 
      ? `/api/admin/pelanggan/${initialData.id}` 
      : '/api/admin/pelanggan';
    const method = isEditMode ? 'PUT' : 'POST';

    try {
      const res = await fetch(url, {
        method: method,
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          namaPelanggan,
          noHp,
        }),
      });

      const data = await res.json();
      
      if (!res.ok) {
        // Jika API mengembalikan error (misal no HP duplikat)
        throw new Error(data.error || 'Gagal menyimpan data');
      }
      
      onSuccess(); // Panggil fungsi onSuccess dari parent
      
    } catch (err) {
      if (err instanceof Error) {
        setApiError(err.message); // Tampilkan error ke user
      } else {
        setApiError('Terjadi kesalahan tidak diketahui');
      }
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <form onSubmit={handleSubmit} className="space-y-4 text-gray-700">
      {/* Tampilkan API Error di sini */}
      {apiError && (
        <div className="p-3 bg-red-100 border border-red-400 text-red-700 rounded-md">
          {apiError}
        </div>
      )}
      
      <div>
        <label className="block text-sm font-medium mb-1">Nama Pelanggan*</label>
        <input
          type="text"
          value={namaPelanggan}
          onChange={(e) => setNamaPelanggan(e.target.value)}
          required
          className="w-full p-2 border border-gray-300 rounded-md"
        />
      </div>
      <div>
        <label className="block text-sm font-medium mb-1">Nomor HP (Opsional)</label>
        <input
          type="text"
          value={noHp}
          onChange={(e) => setNoHp(e.target.value)}
          placeholder="Contoh: 08123456789"
          className="w-full p-2 border border-gray-300 rounded-md"
        />
      </div>
      <button
        type="submit"
        disabled={isLoading}
        className="w-full bg-blue-600 text-white p-2 rounded-md hover:bg-blue-700 disabled:opacity-50"
      >
        {isLoading ? 'Menyimpan...' : (initialData ? 'Update Pelanggan' : 'Simpan Pelanggan Baru')}
      </button>
    </form>
  );
}