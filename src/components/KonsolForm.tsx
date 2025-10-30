// src/components/KonsolForm.tsx
"use client";

import { useState, useEffect } from 'react';
import { Konsol } from '@/src/types'; // Import tipe Konsol

interface KonsolFormProps {
  initialData: Konsol | null;
  onSuccess: () => void;
  apiError: string | null;
  setApiError: (error: string | null) => void;
}

export default function KonsolForm({
  initialData,
  onSuccess,
  apiError,
  setApiError,
}: KonsolFormProps) {
  const [seriKonsol, setSeriKonsol] = useState('');
  const [isLoading, setIsLoading] = useState(false);

  const isEditMode = initialData !== null;

  useEffect(() => {
    if (initialData) {
      setSeriKonsol(initialData.seriKonsol);
    } else {
      // Reset form
      setSeriKonsol('');
    }
    setApiError(null);
  }, [initialData, setApiError]);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setIsLoading(true);
    setApiError(null);

    const url = isEditMode
      ? `/api/admin/konsol/${initialData.id}`
      : '/api/admin/konsol';
    const method = isEditMode ? 'PUT' : 'POST';

    try {
      const res = await fetch(url, {
        method: method,
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          seriKonsol,
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
        <label className="block text-sm font-medium mb-1">Nama/Seri Konsol*</label>
        <input
          type="text"
          value={seriKonsol}
          onChange={(e) => setSeriKonsol(e.target.value)}
          required
          placeholder="Contoh: PS5, PS4, Nintendo Switch"
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
          ? 'Update Konsol'
          : 'Simpan Konsol Baru'}
      </button>
    </form>
  );
}