// src/app/admin/produk/page.tsx
"use client";

import { useState } from 'react';
import useSWR from 'swr';
import { Produk } from '@/src/types'; // Import tipe Produk
import ProdukForm from '@/src/components/ProdukForm'; // Import form baru

const fetcher = (url: string) => fetch(url).then((res) => res.json());

// Helper format Rupiah
function formatRupiah(angka: number) {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
  }).format(angka);
}

export default function AdminProdukPage() {
  const {
    data: produkList,
    error,
    isLoading,
    mutate,
  } = useSWR<Produk[]>('/api/admin/produk', fetcher);

  const [selectedProduk, setSelectedProduk] = useState<Produk | null>(null);
  const [apiError, setApiError] = useState<string | null>(null);

  const handleEdit = (produk: Produk) => {
    setSelectedProduk(produk);
    setApiError(null);
  };

  const handleDelete = async (id: number) => {
    if (!confirm('Apakah Anda yakin ingin menghapus produk ini?')) {
      return;
    }
    try {
      const res = await fetch(`/api/admin/produk/${id}`, {
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
        alert('Gagal menghapus produk');
      }
    }
  };

  const onFormSuccess = () => {
    setSelectedProduk(null); // Reset form
    setApiError(null); // Clear error
    mutate(); // Refresh tabel
  };

  if (error) return <div>Gagal memuat data</div>;
  if (isLoading) return <div>Loading...</div>;

  return (
    <div className="min-h-screen bg-gray-100 p-8">
      <h1 className="text-3xl font-bold text-gray-800 mb-6">
        Manajemen Produk (Snack/Minuman)
      </h1>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {/* Kolom Kiri: Form */}
        <div className="lg:col-span-1 bg-white p-6 rounded-lg shadow-md">
          <h2 className="text-2xl font-semibold mb-4 text-gray-700">
            {selectedProduk ? 'Edit Produk' : 'Tambah Produk Baru'}
          </h2>
          <ProdukForm
            initialData={selectedProduk}
            onSuccess={onFormSuccess}
            apiError={apiError}
            setApiError={setApiError}
          />
          {selectedProduk && (
            <button
              onClick={() => {
                setSelectedProduk(null);
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
            Daftar Produk
          </h2>
          <table className="w-full text-left text-gray-600">
            <thead className="bg-gray-50 border-b border-gray-200">
              <tr>
                <th className="p-3">Nama Produk</th>
                <th className="p-3">Harga</th>
                <th className="p-3">Stok</th>
                <th className="p-3">Aksi</th>
              </tr>
            </thead>
            <tbody>
              {produkList &&
                produkList.map((produk) => (
                  <tr
                    key={produk.id}
                    className="border-b border-gray-100 hover:bg-gray-50"
                  >
                    <td className="p-3 font-medium">{produk.nama}</td>
                    <td className="p-3">{formatRupiah(produk.harga)}</td>
                    <td className="p-3">{produk.stok} pcs</td>
                    <td className="p-3">
                      <button
                        onClick={() => handleEdit(produk)}
                        className="text-blue-600 hover:text-blue-800 font-medium mr-3"
                      >
                        Edit
                      </button>
                      <button
                        onClick={() => handleDelete(produk.id)}
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