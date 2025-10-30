// src/app/api/admin/ruangan/route.ts

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
    
    // 1. Ambil konsolIds dari data yang dikirim
    const { 
      nomorRuangan, 
      tarifPerJam, 
      tipeRuangan, 
      deskripsiFasilitas, 
      konsolIds // Ini adalah array ID [1, 2, ...]
    } = data;

    if (!nomorRuangan || tarifPerJam === undefined) {
      return NextResponse.json(
        { error: 'Nomor Ruangan dan Tarif Per Jam wajib diisi' },
        { status: 400 }
      );
    }

    const ruangan = await prisma.ruangan.create({
      data: {
        nomorRuangan: nomorRuangan,
        tarifPerJam: parseFloat(tarifPerJam),
        tipeRuangan: tipeRuangan,
        deskripsiFasilitas: deskripsiFasilitas,
        
        // 2. Hubungkan relasi Many-to-Many saat membuat
        konsol: {
          connect: konsolIds?.map((id: number) => ({ id: id })) || [],
        },
      },
    });

    return NextResponse.json(ruangan, { status: 201 });
  } catch (error) {
    console.error('Error creating ruangan:', error);
    return NextResponse.json(
      { error: 'Gagal membuat ruangan baru' },
      { status: 500 }
    );
  }
}

// --- FUNGSI READ (GET) ---
// (Kita pindahkan GET dari /api/ruangan ke sini agar terpusat)
// API ini akan mengambil SEMUA ruangan untuk ditampilkan di tabel admin
export async function GET() {
  const session = await getServerSession(authOptions);
  if (!session) {
    return NextResponse.json({ error: 'Akses ditolak' }, { status: 401 });
  }

  try {
    const ruangan = await prisma.ruangan.findMany({
      orderBy: {
        nomorRuangan: 'asc',
      },
      // 3. Sertakan data konsol saat mengambil daftar ruangan
      include: {
        konsol: true,
      },
    });
    return NextResponse.json(ruangan);
  } catch (error) {
    console.error('Error fetching ruangan list:', error);
    return NextResponse.json(
      { error: 'Gagal mengambil daftar ruangan' },
      { status: 500 }
    );
  }
}