// src/app/admin/paket/page.tsx
"use client";

import { useState } from 'react';
import useSWR from 'swr';
import { Paket } from '@/src/types'; // Import tipe Paket
import PaketForm from '@/src/components/PaketForm'; // Import form baru

const fetcher = (url: string) => fetch(url).then((res) => res.json());

// Helper format Rupiah
function formatRupiah(angka: number) {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
  }).format(angka);
}

export default function AdminPaketPage() {
  const {
    data: paketList,
    error,
    isLoading,
    mutate,
  } = useSWR<Paket[]>('/api/admin/paket', fetcher);

  const [selectedPaket, setSelectedPaket] = useState<Paket | null>(null);
  const [apiError, setApiError] = useState<string | null>(null);

  const handleEdit = (paket: Paket) => {
    setSelectedPaket(paket);
    setApiError(null);
  };

  const handleDelete = async (id: number) => {
    if (!confirm('Apakah Anda yakin ingin menghapus paket ini?')) {
      return;
    }
    try {
      const res = await fetch(`/api/admin/paket/${id}`, {
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
        alert('Gagal menghapus paket');
      }
    }
  };

  const onFormSuccess = () => {
    setSelectedPaket(null); // Reset form
    setApiError(null); // Clear error
    mutate(); // Refresh tabel
  };

  if (error) return <div>Gagal memuat data</div>;
  if (isLoading) return <div>Loading...</div>;

  return (
    <div className="min-h-screen bg-gray-100 p-8">
      <h1 className="text-3xl font-bold text-gray-800 mb-6">
        Manajemen Paket Billing
      </h1>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {/* Kolom Kiri: Form */}
        <div className="lg:col-span-1 bg-white p-6 rounded-lg shadow-md">
          <h2 className="text-2xl font-semibold mb-4 text-gray-700">
            {selectedPaket ? 'Edit Paket' : 'Tambah Paket Baru'}
          </h2>
          <PaketForm
            initialData={selectedPaket}
            onSuccess={onFormSuccess}
            apiError={apiError}
            setApiError={setApiError}
          />
          {selectedPaket && (
            <button
              onClick={() => {
                setSelectedPaket(null);
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
            Daftar Paket
          </h2>
          <table className="w-full text-left text-gray-600">
            <thead className="bg-gray-50 border-b border-gray-200">
              <tr>
                <th className="p-3">Nama Paket</th>
                <th className="p-3">Durasi</th>
                <th className="p-3">Harga</th>
                <th className="p-3">Aksi</th>
              </tr>
            </thead>
            <tbody>
              {paketList &&
                paketList.map((paket) => (
                  <tr
                    key={paket.id}
                    className="border-b border-gray-100 hover:bg-gray-50"
                  >
                    <td className="p-3 font-medium">{paket.namaPaket}</td>
                    <td className="p-3">{paket.durasiMenit} Menit</td>
                    <td className="p-3">{formatRupiah(paket.hargaPaket)}</td>
                    <td className="p-3">
                      <button
                        onClick={() => handleEdit(paket)}
                        className="text-blue-600 hover:text-blue-800 font-medium mr-3"
                      >
                        Edit
                      </button>
                      <button
                        onClick={() => handleDelete(paket.id)}
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