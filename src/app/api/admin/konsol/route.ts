// src/app/api/admin/konsol/route.ts

import { NextResponse } from 'next/server';
import prisma from '@/src/lib/prisma'; // Sesuaikan path
import { Prisma } from '@prisma/client';
import { getServerSession } from 'next-auth/next';
import { authOptions } from '@/src/app/api/auth/[...nextauth]/route';

// --- FUNGSI CREATE (POST) ---
export async function POST(request: Request) {
  const session = await getServerSession(authOptions);
  if (!session || session.user?.role !== 'admin') {
    return NextResponse.json({ error: 'Akses ditolak' }, { status: 403 });
  }

  try {
    const data = await request.json();

    if (!data.seriKonsol) {
      return NextResponse.json(
        { error: 'Nama/Seri Konsol wajib diisi' },
        { status: 400 }
      );
    }

    const konsol = await prisma.konsol.create({
      data: {
        seriKonsol: data.seriKonsol,
      },
    });

    return NextResponse.json(konsol, { status: 201 });
  } catch (error) {
    console.error('Error creating konsol:', error);
    return NextResponse.json(
      { error: 'Gagal membuat konsol baru' },
      { status: 500 }
    );
  }
}

// --- FUNGSI READ (GET) ---
export async function GET() {
  const session = await getServerSession(authOptions);
  if (!session) {
    return NextResponse.json({ error: 'Akses ditolak' }, { status: 401 });
  }

  try {
    const konsol = await prisma.konsol.findMany({
      orderBy: {
        seriKonsol: 'asc',
      },
    });
    return NextResponse.json(konsol);
  } catch (error) {
    console.error('Error fetching konsol list:', error);
    return NextResponse.json(
      { error: 'Gagal mengambil daftar konsol' },
      { status: 500 }
    );
  }
}