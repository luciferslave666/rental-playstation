// src/app/api/admin/produk/route.ts

import { NextResponse } from 'next/server';
import prisma from '@/src/lib/prisma'; // Sesuaikan path jika perlu
import { Prisma } from '@prisma/client';
import { getServerSession } from 'next-auth/next';
import { authOptions } from '@/src/app/api/auth/[...nextauth]/route';

// --- FUNGSI CREATE (POST) ---
export async function POST(request: Request) {
  // Cek jika user adalah admin
  const session = await getServerSession(authOptions);
  if (!session || session.user?.role !== 'admin') {
    return NextResponse.json({ error: 'Akses ditolak' }, { status: 403 });
  }

  try {
    const data = await request.json();

    if (!data.nama || data.harga === undefined || data.stok === undefined) {
      return NextResponse.json(
        { error: 'Nama, Harga, dan Stok wajib diisi' },
        { status: 400 }
      );
    }

    const produk = await prisma.produk.create({
      data: {
        nama: data.nama,
        harga: parseFloat(data.harga),
        stok: parseInt(data.stok),
      },
    });

    return NextResponse.json(produk, { status: 201 });
  } catch (error) {
    if (error instanceof Prisma.PrismaClientKnownRequestError) {
      // P2002: Unique constraint (Nama produk duplikat)
      if (error.code === 'P2002') {
        return NextResponse.json(
          { error: 'Gagal: Nama produk ini sudah ada.' },
          { status: 400 }
        );
      }
    }
    console.error('Error creating produk:', error);
    return NextResponse.json(
      { error: 'Gagal membuat produk baru' },
      { status: 500 }
    );
  }
}

// --- FUNGSI READ (GET) ---
export async function GET() {
  // API ini bisa diakses kasir (untuk modal pesanan) atau admin
  const session = await getServerSession(authOptions);
  if (!session) {
    return NextResponse.json({ error: 'Akses ditolak' }, { status: 401 });
  }

  try {
    const produk = await prisma.produk.findMany({
      orderBy: {
        nama: 'asc',
      },
    });
    return NextResponse.json(produk);
  } catch (error) {
    console.error('Error fetching produk list:', error);
    return NextResponse.json(
      { error: 'Gagal mengambil daftar produk' },
      { status: 500 }
    );
  }
}