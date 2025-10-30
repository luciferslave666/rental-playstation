// src/app/api/pelanggan/list/route.ts

import { NextResponse } from 'next/server';
import prisma from '@/src/lib/prisma'; // Sesuaikan path jika perlu
import { getServerSession } from 'next-auth/next';
import { authOptions } from '@/src/app/api/auth/[...nextauth]/route'; // Impor authOptions

// API ini bisa diakses oleh kasir/admin yang sudah login
export async function GET(request: Request) {
  // Cek sesi (minimal sudah login)
  const session = await getServerSession(authOptions);
  if (!session || !session.user) {
    return NextResponse.json(
      { error: 'Akses ditolak. Silakan login.' },
      { status: 401 } // 401 Unauthorized
    );
  }

  try {
    const pelanggan = await prisma.pelanggan.findMany({
      // Pilih hanya ID dan Nama
      select: {
        id: true,
        namaPelanggan: true,
      },
      orderBy: {
        namaPelanggan: 'asc',
      },
    });
    return NextResponse.json(pelanggan);
  } catch (error) {
    console.error('Error fetching pelanggan list:', error);
    return NextResponse.json(
      { error: 'Gagal mengambil daftar pelanggan' },
      { status: 500 }
    );
  }
}