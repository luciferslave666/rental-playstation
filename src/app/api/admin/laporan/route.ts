// src/app/api/admin/laporan/route.ts

import { NextResponse } from 'next/server';
import prisma from '@/src/lib/prisma'; // Sesuaikan path jika perlu
import { getServerSession } from 'next-auth/next';
import { authOptions } from '@/src/app/api/auth/[...nextauth]/route'; // Impor authOptions

export async function GET(request: Request) {
  // 1. Cek Sesi & Role Admin (Perlindungan Lapis Kedua)
  const session = await getServerSession(authOptions);
  if (!session || session.user?.role !== 'admin') {
    return NextResponse.json(
      { error: 'Akses ditolak. Anda bukan admin.' },
      { status: 403 }
    );
  }

  try {
    // 2. Ambil Query Parameters dari URL
    const { searchParams } = new URL(request.url);
    
    // Ambil tanggal, jika tidak ada, default ke 30 hari terakhir
    const defaultEndDate = new Date();
    const defaultStartDate = new Date();
    defaultStartDate.setDate(defaultEndDate.getDate() - 30);

    // Ambil & format tanggal. Tambahkan jam 23:59:59 ke endDate
    const startDate = new Date(searchParams.get('startDate') || defaultStartDate);
    const endDate = new Date(searchParams.get('endDate') || defaultEndDate);
    endDate.setHours(23, 59, 59, 999); // Set ke akhir hari

    // 3. Buat Filter 'where' untuk Prisma
    const whereClause = {
      statusPembayaran: 'LUNAS', // Hanya ambil yang sudah lunas
      waktuSelesai: {
        gte: startDate, // gte = greater than or equal (lebih besar/sama dengan)
        lte: endDate,   // lte = less than or equal (lebih kecil/sama dengan)
      },
    };

    // 4. Jalankan 2 Query Sekaligus (Agregasi & Daftar Rinci)
    const [summary, transactions] = await prisma.$transaction([
      // Query 1: Untuk statistik (Total Pendapatan & Jumlah Transaksi)
      prisma.transaksi.aggregate({
        where: whereClause,
        _sum: {
          totalBiaya: true,
        },
        _count: {
          id: true,
        },
      }),

      // Query 2: Untuk daftar transaksi (ditampilkan di tabel)
      prisma.transaksi.findMany({
        where: whereClause,
        include: {
          user: { // Ambil data kasir
            select: { nama: true },
          },
          ruangan: { // Ambil data ruangan
            select: { nomorRuangan: true },
          },
          pelanggan: { // Ambil data pelanggan (jika ada)
            select: { namaPelanggan: true },
          },
        },
        orderBy: {
          waktuSelesai: 'desc', // Tampilkan dari yang terbaru
        },
      }),
    ]);

    // 5. Format Hasil & Kirim Respons
    const responseData = {
      summary: {
        totalPendapatan: summary._sum.totalBiaya || 0,
        totalTransaksi: summary._count.id || 0,
      },
      transactions: transactions,
      filter: {
        startDate,
        endDate,
      },
    };

    return NextResponse.json(responseData);
    
  } catch (error) {
    console.error('Error fetching report:', error);
    return NextResponse.json(
      { error: 'Gagal mengambil data laporan' },
      { status: 500 }
    );
  }
}