// src/app/admin/konsol/page.tsx
"use client";

import { useState } from 'react';
import useSWR from 'swr';
import { Konsol } from '@/src/types'; // Import tipe Konsol
import KonsolForm from '@/src/components/KonsolForm'; // Import form baru

const fetcher = (url: string) => fetch(url).then((res) => res.json());

export default function AdminKonsolPage() {
  const {
    data: konsolList,
    error,
    isLoading,
    mutate,
  } = useSWR<Konsol[]>('/api/admin/konsol', fetcher);

  const [selectedKonsol, setSelectedKonsol] = useState<Konsol | null>(null);
  const [apiError, setApiError] = useState<string | null>(null);

  const handleEdit = (konsol: Konsol) => {
    setSelectedKonsol(konsol);
    setApiError(null);
  };

  const handleDelete = async (id: number) => {
    if (!confirm('Apakah Anda yakin ingin menghapus konsol ini?')) {
      return;
    }
    try {
      const res = await fetch(`/api/admin/konsol/${id}`, {
        method: 'DELETE',
      });
      const data = await res.json();
      if (!res.ok) {
        throw new Error(data.error || 'Gagal menghapus');
      }
      mutate(); // Refresh tabel
    } catch (err) {
      if (err instanceof Error) {
        alert(err.message);
      } else {
        alert('Gagal menghapus konsol');
      }
    }
  };

  const onFormSuccess = () => {
    setSelectedKonsol(null); // Reset form
    setApiError(null); // Clear error
    mutate(); // Refresh tabel
  };

  if (error) return <div>Gagal memuat data</div>;
  if (isLoading) return <div>Loading...</div>;

  return (
    <div className="min-h-screen bg-gray-100 p-8">
      <h1 className="text-3xl font-bold text-gray-800 mb-6">
        Manajemen Konsol (PS4/PS5)
      </h1>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {/* Kolom Kiri: Form */}
        <div className="lg:col-span-1 bg-white p-6 rounded-lg shadow-md">
          <h2 className="text-2xl font-semibold mb-4 text-gray-700">
            {selectedKonsol ? 'Edit Konsol' : 'Tambah Konsol Baru'}
          </h2>
          <KonsolForm
            initialData={selectedKonsol}
            onSuccess={onFormSuccess}
            apiError={apiError}
            setApiError={setApiError}
          />
          {selectedKonsol && (
            <button
              onClick={() => {
                setSelectedKonsol(null);
                setApiError(null);
              }}
              className="mt-4 text-sm text-blue-600 hover:underline"
            >
              Batal Edit (Kembali ke mode Tambah)
            </button>
          )}
        </div>

        {/* Kolom Kanan: Tabel */}
        <div className="lg:col-span-2 bg-white p-6 rounded-lg shadow-md">
          <h2 className="text-2xl font-semibold mb-4 text-gray-700">
            Daftar Konsol
          </h2>
          <table className="w-full text-left text-gray-600">
            <thead className="bg-gray-50 border-b border-gray-200">
              <tr>
                <th className="p-3">Nama/Seri Konsol</th>
                <th className="p-3">Aksi</th>
              </tr>
            </thead>
            <tbody>
              {konsolList &&
                konsolList.map((konsol) => (
                  <tr
                    key={konsol.id}
                    className="border-b border-gray-100 hover:bg-gray-50"
                  >
                    <td className="p-3 font-medium">{konsol.seriKonsol}</td>
                    <td className="p-3">
                      <button
                        onClick={() => handleEdit(konsol)}
                        className="text-blue-600 hover:text-blue-800 font-medium mr-3"
                      >
                        Edit
                      </button>
                      <button
                        onClick={() => handleDelete(konsol.id)}
                        className="text-red-600 hover:text-red-800 font-medium"
                      >
                        Delete
                      </button>
                    </td>
                  </tr>
                ))}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
}