// src/app/admin/laporan/page.tsx
"use client";

import { useState } from 'react';
import useSWR from 'swr';
import DatePicker from 'react-datepicker';
import { LaporanResponse } from '@/src/types'; // <-- 1. PERBAIKI PATH IMPORT
import { useSession } from 'next-auth/react'; // <-- 2. TAMBAHKAN useSession

// --- Helper Functions ---
const fetcher = (url: string) => fetch(url).then((res) => res.json());

function formatRupiah(angka: number) {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
  }).format(angka);
}

function formatDate(dateString: string | null | undefined) {
  if (!dateString) return "-";
  return new Date(dateString).toLocaleString('id-ID', {
    dateStyle: 'medium',
    timeStyle: 'short',
  });
}
// ------------------------

export default function LaporanPage() {
  const [startDate, setStartDate] = useState(() => {
    const d = new Date();
    d.setDate(d.getDate() - 30);
    return d;
  });
  const [endDate, setEndDate] = useState(new Date());

  // 3. Ambil status sesi
  const { data: session, status } = useSession();

  const startDateString = startDate.toISOString().split('T')[0];
  const endDateString = endDate.toISOString().split('T')[0];

  // 4. Buat SWR Key menjadi KONDISIONAL
  // SWR hanya akan fetch jika 'status' adalah 'authenticated'
  // Jika 'status' masih 'loading' atau 'unauthenticated', swrKey akan 'null' dan SWR akan "pause"
  const swrKey = 
    status === 'authenticated' && session.user.role === 'admin'
      ? `/api/admin/laporan?startDate=${startDateString}&endDate=${endDateString}`
      : null; 
  
  const { 
    data, 
    error, 
    isLoading 
  } = useSWR<LaporanResponse>(swrKey, fetcher); // fetcher hanya jalan jika swrKey tidak null

  // 5. Perbarui logika loading dan error
  if (status === 'loading' || (isLoading && status === 'authenticated')) {
    return <div className="min-h-screen bg-gray-100 p-8 text-blue-600">Memuat data laporan...</div>;
  }
  
  if (status === 'unauthenticated') {
     return <div className="min-h-screen bg-gray-100 p-8 text-red-600">Akses ditolak. Silakan login sebagai admin.</div>;
  }

  // Tampilkan SWR error HANYA jika sesi sudah terautentikasi
  if (error) {
     return <div className="min-h-screen bg-gray-100 p-8 text-red-600">Gagal memuat data: {error.message}</div>
  }

  return (
    <div className="min-h-screen bg-gray-100 p-8">
      <h1 className="mb-6 text-3xl font-bold text-gray-800">Laporan Pendapatan</h1>

      {/* Area Filter Tanggal */}
      <div className="mb-6 flex items-center space-x-4 rounded-lg bg-white p-4 shadow-md">
        <div className="flex flex-col">
          <label className="mb-1 text-sm font-medium text-gray-600">Tanggal Mulai</label>
          <DatePicker
            selected={startDate}
            onChange={(date) => date && setStartDate(date)}
            selectsStart
            startDate={startDate}
            endDate={endDate}
            className="w-full rounded-md border border-gray-300 p-2 text-gray-700"
          />
        </div>
        <div className="flex flex-col">
          <label className="mb-1 text-sm font-medium text-gray-600">Tanggal Selesai</label>
          <DatePicker
            selected={endDate}
            // 6. PERBAIKI BUG DI SINI (setEndDate, bukan setStartDate)
            onChange={(date) => date && setEndDate(date)} 
            selectsEnd
            startDate={startDate}
            endDate={endDate}
            minDate={startDate} 
            className="w-full rounded-md border border-gray-300 p-2 text-gray-700"
          />
        </div>
      </div>

      {/* Area Tampilan Data */}
      {/* Kita tidak perlu 'isLoading' di sini lagi karena sudah ditangani di atas.
        Kita hanya perlu cek 'data'.
      */}
      {!data && !error && (
        <div className="text-blue-600">Memuat data laporan...</div>
      )}

      {data && (
        <>
          {/* Statistik Ringkasan */}
          <div className="mb-6 grid grid-cols-1 gap-4 md:grid-cols-2">
            <div className="rounded-lg bg-white p-6 shadow-md">
              <h3 className="mb-2 text-sm font-medium uppercase text-gray-500">
                Total Pendapatan (Lunas)
              </h3>
              <p className="text-3xl font-bold text-green-600">
                {formatRupiah(data.summary.totalPendapatan)}
              </p>
            </div>
            <div className="rounded-lg bg-white p-6 shadow-md">
              <h3 className="mb-2 text-sm font-medium uppercase text-gray-500">
                Total Transaksi (Lunas)
              </h3>
              <p className="text-3xl font-bold text-blue-600">
                {data.summary.totalTransaksi} Transaksi
              </p>
            </div>
          </div>

          {/* Tabel Rincian Transaksi */}
          <div className="rounded-lg bg-white p-6 shadow-md">
            <h2 className="mb-4 text-xl font-semibold text-gray-700">
              Rincian Transaksi
            </h2>
            <div className="overflow-x-auto">
              <table className="w-full min-w-[600px] text-left text-gray-600">
                <thead className="border-b border-gray-200 bg-gray-50">
                  <tr>
                    <th className="p-3">ID Transaksi</th>
                    <th className="p-3">Waktu Selesai</th>
                    <th className="p-3">Ruangan</th>
                    <th className="p-3">Kasir</th>
                    <th className="p-3">Pelanggan</th>
                    <th className="p-3 text-right">Total Biaya (Rp)</th>
                  </tr>
                </thead>
                <tbody>
                  {data.transactions.length === 0 && (
                    <tr>
                      <td colSpan={6} className="p-4 text-center text-gray-500">
                        Tidak ada transaksi lunas pada rentang tanggal ini.
                      </td>
                    </tr>
                  )}
                  {data.transactions.map((tx) => (
                    <tr key={tx.id} className="border-b border-gray-100 hover:bg-gray-50">
                      <td className="p-3 font-medium">#{tx.id}</td>
                      <td className="p-3">{formatDate(tx.waktuSelesai)}</td>
                      <td className="p-3">{tx.ruangan.nomorRuangan}</td>
                      <td className="p-3">{tx.user.nama}</td>
                      <td className="p-3">{tx.pelanggan?.namaPelanggan || '-'}</td>
                      <td className="p-3 text-right font-medium text-gray-800">
                        {formatRupiah(tx.totalBiaya || 0)}
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>
        </>
      )}
    </div>
  );
}