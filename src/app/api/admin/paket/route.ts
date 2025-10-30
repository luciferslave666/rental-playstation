// src/app/api/admin/paket/route.ts

import { NextResponse } from 'next/server';
import prisma from '@/src/lib/prisma';
import { Prisma } from '@prisma/client';
import { getServerSession } from 'next-auth/next';
import { authOptions } from '@/src/app/api/auth/[...nextauth]/route';

// GET - Ambil semua paket
export async function GET() {
  const session = await getServerSession(authOptions);
  if (!session) {
    return NextResponse.json({ error: 'Akses ditolak' }, { status: 401 });
  }

  try {
    const paket = await prisma.paket.findMany({
      orderBy: {
        hargaPaket: 'asc',
      },
    });
    return NextResponse.json(paket);
  } catch (error) {
    console.error('Error fetching paket list:', error);
    return NextResponse.json(
      { error: 'Gagal mengambil daftar paket' },
      { status: 500 }
    );
  }
}

// POST - Buat paket baru
export async function POST(request: Request) {
  const session = await getServerSession(authOptions);
  if (!session || session.user?.role !== 'admin') {
    return NextResponse.json({ error: 'Akses ditolak' }, { status: 403 });
  }

  try {
    const data = await request.json();

    if (!data.namaPaket || !data.durasiMenit || !data.hargaPaket) {
      return NextResponse.json(
        { error: 'Nama, Durasi (menit), dan Harga wajib diisi' },
        { status: 400 }
      );
    }

    const paket = await prisma.paket.create({
      data: {
        namaPaket: data.namaPaket,
        durasiMenit: parseInt(data.durasiMenit),
        hargaPaket: parseFloat(data.hargaPaket),
      },
    });

    return NextResponse.json(paket, { status: 201 });
  } catch (error) {
    if (error instanceof Prisma.PrismaClientKnownRequestError) {
      if (error.code === 'P2002') {
        return NextResponse.json(
          { error: 'Gagal: Nama paket ini sudah ada.' },
          { status: 400 }
        );
      }
    }
    console.error('Error creating paket:', error);
    return NextResponse.json(
      { error: 'Gagal membuat paket baru' },
      { status: 500 }
    );
  }
}