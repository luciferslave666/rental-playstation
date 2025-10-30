// src/app/api/admin/dashboard-stats/route.ts

import { NextResponse } from 'next/server';
import prisma from '@/src/lib/prisma'; // Sesuaikan path jika perlu
import { getServerSession } from 'next-auth/next';
import { authOptions } from '@/src/app/api/auth/[...nextauth]/route'; // Impor authOptions

export async function GET(request: Request) {
  // 1. Cek Sesi & Role Admin
  const session = await getServerSession(authOptions);
  if (!session || session.user?.role !== 'admin') {
    return NextResponse.json(
      { error: 'Akses ditolak. Anda bukan admin.' },
      { status: 403 }
    );
  }

  try {
    // 2. Tentukan rentang waktu "Hari Ini" (dari jam 00:00 sampai sekarang)
    const now = new Date();
    // Set 'todayStart' ke jam 00:00:00 hari ini
    const todayStart = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 0, 0, 0);

    // 3. Jalankan 2 query statistik secara paralel
    const [laporanHariIni, ruanganAktif] = await prisma.$transaction([
      
      // Query 1: Menghitung total pendapatan & transaksi LUNAS hari ini
      prisma.transaksi.aggregate({
        where: {
          statusPembayaran: 'LUNAS',
          waktuSelesai: {
            gte: todayStart, // Lebih besar atau sama dengan (>=) jam 00:00
            lte: now,        // Lebih kecil atau sama dengan (<=) jam sekarang
          }
        },
        _sum: {
          totalBiaya: true, // Jumlahkan semua totalBiaya
        },
        _count: {
          id: true, // Hitung jumlah transaksi (ID)
        }
      }),

      // Query 2: Menghitung ruangan yang sedang TERISI
      prisma.ruangan.count({
        where: {
          status: 'TERISI'
        }
      })
    ]);
    
    // 4. (Opsional) Ambil juga total ruangan
    const totalRuangan = await prisma.ruangan.count();

    // 5. Format data respons
    const stats = {
      totalPendapatanHariIni: laporanHariIni._sum.totalBiaya || 0,
      totalTransaksiHariIni: laporanHariIni._count.id || 0,
      ruanganTerisi: ruanganAktif,
      totalRuangan: totalRuangan,
    };

    return NextResponse.json(stats);

  } catch (error) {
    console.error('Error fetching dashboard stats:', error);
    return NextResponse.json(
      { error: 'Gagal mengambil statistik dashboard' },
      { status: 500 }
    );
  }
}