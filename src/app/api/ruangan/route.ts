// app/api/ruangan/route.js

import { NextResponse } from 'next/server';
import prisma from '@/src/lib/prisma';

// Fungsi GET untuk ambil data dashboard
export async function GET(request: Request) {
  try {
    const ruangan = await prisma.ruangan.findMany({
      // Kita 'include' transaksi yang statusnya "AKTIF"
      // Ini PENTING agar frontend tahu ruangan ini lagi dipakai
      include: {
        transaksi: {
          where: {
            // Dulu kita pakai status "AKTIF" di 'Session'
            // Di ERD-mu, 'Transaksi' tidak punya status,
            // jadi kita cari yang 'waktuSelesai' nya masih null
            waktuSelesai: null, 
          },
        },
        // Kita juga bisa ambil data konsol jika perlu
        // konsol: true, 
      },
      orderBy: {
        nomorRuangan: 'asc', // Urutkan berdasarkan nomor
      },
    });
    return NextResponse.json(ruangan);
  } catch (error) {
    console.error("Error fetching ruangan:", error);
    return NextResponse.json({ error: 'Gagal mengambil data ruangan' }, { status: 500 });
  }
}