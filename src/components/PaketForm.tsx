// src/components/PaketForm.tsx
"use client";

import { useState, useEffect } from 'react';
import { Paket } from '@/src/types'; // Import tipe Paket

interface PaketFormProps {
  initialData: Paket | null;
  onSuccess: () => void;
  apiError: string | null;
  setApiError: (error: string | null) => void;
}

export default function PaketForm({
  initialData,
  onSuccess,
  apiError,
  setApiError,
}: PaketFormProps) {
  const [namaPaket, setNamaPaket] = useState('');
  const [durasiMenit, setDurasiMenit] = useState(0);
  const [hargaPaket, setHargaPaket] = useState(0);
  const [isLoading, setIsLoading] = useState(false);

  const isEditMode = initialData !== null;

  useEffect(() => {
    if (initialData) {
      setNamaPaket(initialData.namaPaket);
      setDurasiMenit(initialData.durasiMenit);
      setHargaPaket(initialData.hargaPaket);
    } else {
      // Reset form
      setNamaPaket('');
      setDurasiMenit(0);
      setHargaPaket(0);
    }
    setApiError(null);
  }, [initialData, setApiError]);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setIsLoading(true);
    setApiError(null);

    const url = isEditMode
      ? `/api/admin/paket/${initialData.id}`
      : '/api/admin/paket';
    const method = isEditMode ? 'PUT' : 'POST';

    try {
      const res = await fetch(url, {
        method: method,
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          namaPaket,
          durasiMenit,
          hargaPaket,
        }),
      });

      const data = await res.json();

      if (!res.ok) {
        throw new Error(data.error || 'Gagal menyimpan data');
      }

      onSuccess();

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

      <div>
        <label className="block text-sm font-medium mb-1">Nama Paket*</label>
        <input
          type="text"
          value={namaPaket}
          onChange={(e) => setNamaPaket(e.target.value)}
          required
          placeholder="Contoh: Paket 3 Jam"
          className="w-full p-2 border border-gray-300 rounded-md"
        />
      </div>
      <div>
        <label className="block text-sm font-medium mb-1">Durasi (Menit)*</label>
        <input
          type="number"
          value={durasiMenit}
          onChange={(e) => setDurasiMenit(parseInt(e.target.value) || 0)}
          required
          min="0"
          placeholder="Contoh: 180"
          className="w-full p-2 border border-gray-300 rounded-md"
        />
      </div>
       <div>
        <label className="block text-sm font-medium mb-1">Harga Paket (Rp)*</label>
        <input
          type="number"
          value={hargaPaket}
          onChange={(e) => setHargaPaket(parseFloat(e.target.value) || 0)}
          required
          min="0"
          placeholder="Contoh: 25000"
          className="w-full p-2 border border-gray-300 rounded-md"
        />
      </div>
      <button
        type="submit"
        disabled={isLoading}
        className="w-full bg-blue-600 text-white p-2 rounded-md hover:bg-blue-700 disabled:opacity-50"
      >
        {isLoading
          ? 'Menyimpan...'
          : initialData
          ? 'Update Paket'
          : 'Simpan Paket Baru'}
      </button>
    </form>
  );
}