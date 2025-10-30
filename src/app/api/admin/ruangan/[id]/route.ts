// src/app/api/admin/ruangan/[id]/route.ts

import { NextResponse } from 'next/server';
// Pastikan path import prisma ini benar sesuai tsconfig.json kamu
import prisma from '@/src/lib/prisma'; 
import { Prisma } from '@prisma/client';
import { getServerSession } from 'next-auth/next';
// Pastikan path import authOptions ini benar
import { authOptions } from '@/src/app/api/auth/[...nextauth]/route';

// Definisikan tipe untuk params
interface RouteParams {
  id: string;
}

// --- FUNGSI UPDATE (PUT) ---
export async function PUT(
  request: Request,
  { params }: { params: Promise<RouteParams> }
) {
  // 1. Cek Sesi (Admin)
  const session = await getServerSession(authOptions);
  if (!session || session.user?.role !== 'admin') {
    return NextResponse.json({ error: 'Akses ditolak' }, { status: 403 });
  }

  try {
    // 2. Await params
    const { id: paramId } = await params;
    const id = parseInt(paramId);

    if (isNaN(id)) {
      return NextResponse.json({ error: 'ID tidak valid' }, { status: 400 });
    }

    // 3. Ambil data termasuk konsolIds
    const data = await request.json();
    const { 
      nomorRuangan, 
      tarifPerJam, 
      tipeRuangan, 
      deskripsiFasilitas, 
      konsolIds // Array ID [1, 2, ...]
    } = data;

    if (!nomorRuangan || tarifPerJam === undefined) {
      return NextResponse.json(
        { error: 'Nomor Ruangan dan Tarif Per Jam wajib diisi' },
        { status: 400 }
      );
    }

    const updatedRuangan = await prisma.ruangan.update({
      where: { id },
      data: {
        nomorRuangan: nomorRuangan,
        tarifPerJam: parseFloat(tarifPerJam),
        tipeRuangan: tipeRuangan,
        deskripsiFasilitas: deskripsiFasilitas,
        // 4. Update relasi Many-to-Many
        konsol: {
          set: konsolIds?.map((id: number) => ({ id: id })) || [],
        },
      },
    });

    return NextResponse.json(updatedRuangan);
  } catch (error) {
    if (error instanceof Prisma.PrismaClientKnownRequestError && error.code === 'P2025') {
      return NextResponse.json({ error: 'Ruangan tidak ditemukan' }, { status: 404 });
    }
    console.error(`Error updating ruangan:`, error);
    return NextResponse.json(
      { error: 'Gagal memperbarui ruangan' },
      { status: 500 }
    );
  }
}

// --- FUNGSI DELETE (DELETE) ---
export async function DELETE(
  request: Request,
  { params }: { params: Promise<RouteParams> }
) {
  // 1. Cek Sesi (Admin)
  const session = await getServerSession(authOptions);
  if (!session || session.user?.role !== 'admin') {
    return NextResponse.json({ error: 'Akses ditolak' }, { status: 403 });
  }

  try {
    // 2. Await params
    const { id: paramId } = await params;
    const id = parseInt(paramId);

     if (isNaN(id)) {
      return NextResponse.json({ error: 'ID tidak valid' }, { status: 400 });
    }

    await prisma.ruangan.delete({
      where: { id },
    });

    return NextResponse.json(
      { message: 'Ruangan berhasil dihapus' },
      { status: 200 }
    );
  } catch (error) {
    if (error instanceof Prisma.PrismaClientKnownRequestError) {
      if (error.code === 'P2003') {
        return NextResponse.json(
          { error: 'Gagal menghapus: Ruangan ini masih terikat dengan data transaksi.' },
          { status: 400 }
        );
      }
       if (error.code === 'P2025') {
        return NextResponse.json(
          { error: 'Ruangan tidak ditemukan' },
          { status: 404 }
        );
      }
    }
    console.error(`Error deleting ruangan:`, error);
    return NextResponse.json(
      { error: 'Gagal menghapus ruangan' },
      { status: 500 }
    );
  }
}