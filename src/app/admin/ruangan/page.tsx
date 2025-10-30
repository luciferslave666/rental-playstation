"use client";

import { useState } from 'react';
import useSWR from 'swr';
// Pastikan impor tipe data yang benar
import { RuanganAdminData } from '@/src/types'; 
import RuanganForm from '@/src/components/RuanganForm';

const fetcher = (url: string) => fetch(url).then((res) => res.json());

export default function AdminRuanganPage() {
  // SWR ambil data dari API list (GET)
  const { 
    data: ruanganList, 
    error, 
    isLoading, 
    mutate 
  } = useSWR<RuanganAdminData[]>('/api/admin/ruangan', fetcher);

  const [selectedRuangan, setSelectedRuangan] = useState<RuanganAdminData | null>(null);
  const [apiError, setApiError] = useState<string | null>(null);

  const handleEdit = (ruangan: RuanganAdminData) => {
    setSelectedRuangan(ruangan);
    setApiError(null);
  };

  const handleDelete = async (id: number) => {
    if (!confirm('Apakah Anda yakin ingin menghapus ruangan ini?')) {
      return;
    }
    try {
      // Panggil API DELETE (yang sudah kita perbaiki)
      const res = await fetch(`/api/admin/ruangan/${id}`, {
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
        alert('Gagal menghapus ruangan');
      }
    }
  };

  // Dipanggil setelah form sukses submit (create atau update)
  const onFormSuccess = () => {
    setSelectedRuangan(null); // Reset form ke mode 'Tambah Baru'
    mutate(); // Refresh tabel
    setApiError(null);
  };

  if (error) return <div>Gagal memuat data</div>;
  if (isLoading) return <div>Loading...</div>;

  return (
    <div className="min-h-screen bg-gray-100 p-8">
      <h1 className="text-3xl font-bold text-gray-800 mb-6">Manajemen Ruangan</h1>
      
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {/* Kolom Kiri: Form */}
        <div className="lg:col-span-1 bg-white p-6 rounded-lg shadow-md">
          <h2 className="text-2xl font-semibold mb-4 text-gray-700">
            {selectedRuangan ? 'Edit Ruangan' : 'Tambah Ruangan Baru'}
          </h2>
          <RuanganForm
            initialData={selectedRuangan}
            onSuccess={onFormSuccess}
            apiError={apiError} // <-- TAMBAHKAN INI
  setApiError={setApiError} // <-- TAMBAHKAN INI
          />
          {selectedRuangan && (
            <button
              onClick={() => setSelectedRuangan(null)}
              className="mt-4 text-sm text-blue-600 hover:underline"
            >
              Batal Edit (Kembali ke mode Tambah)
            </button>
          )}
        </div>

        {/* Kolom Kanan: Tabel */}
        <div className="lg:col-span-2 bg-white p-6 rounded-lg shadow-md">
          <h2 className="text-2xl font-semibold mb-4 text-gray-700">Daftar Ruangan</h2>
          <table className="w-full text-left text-gray-600">
            <thead className="bg-gray-50 border-b border-gray-200">
              <tr>
                <th className="p-3">No. Ruangan</th>
                <th className="p-3">Tipe</th>
                <th className="p-3">Tarif/Jam (Rp)</th>
                <th className="p-3">Aksi</th>
              </tr>
            </thead>
            <tbody>
              {ruanganList && ruanganList.map((ruangan) => (
                <tr key={ruangan.id} className="border-b border-gray-100 hover:bg-gray-50">
                  <td className="p-3 font-medium">{ruangan.nomorRuangan}</td>
                  <td className="p-3">{ruangan.tipeRuangan}</td>
                  <td className="p-3">{ruangan.tarifPerJam.toLocaleString('id-ID')}</td>
                  <td className="p-3">
                    <button
                      onClick={() => handleEdit(ruangan)}
                      className="text-blue-600 hover:text-blue-800 font-medium mr-3"
                    >
                      Edit
                    </button>
                    <button
                      onClick={() => handleDelete(ruangan.id)}
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