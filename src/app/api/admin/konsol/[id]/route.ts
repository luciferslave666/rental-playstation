// src/app/api/admin/konsol/[id]/route.ts

import { NextResponse } from 'next/server';
import prisma from '@/src/lib/prisma'; // Sesuaikan path
import { Prisma } from '@prisma/client';
import { getServerSession } from 'next-auth/next';
import { authOptions } from '@/src/app/api/auth/[...nextauth]/route';

// Definisikan tipe untuk CONTEXT
interface RouteContext {
  params: {
    id: string;
  };
}

// --- FUNGSI UPDATE (PUT) ---
export async function PUT(request: Request, context: RouteContext) {
  const session = await getServerSession(authOptions);
  if (!session || session.user?.role !== 'admin') {
    return NextResponse.json({ error: 'Akses ditolak' }, { status: 403 });
  }

  const { params } = context;
  try {
    const id = parseInt(params.id);
    if (isNaN(id)) {
      return NextResponse.json({ error: 'ID tidak valid' }, { status: 400 });
    }

    const data = await request.json();
    if (!data.seriKonsol) {
      return NextResponse.json(
        { error: 'Nama/Seri Konsol wajib diisi' },
        { status: 400 }
      );
    }

    const updatedKonsol = await prisma.konsol.update({
      where: { id },
      data: {
        seriKonsol: data.seriKonsol,
      },
    });

    return NextResponse.json(updatedKonsol);
  } catch (error) {
    if (error instanceof Prisma.PrismaClientKnownRequestError && error.code === 'P2025') {
      return NextResponse.json(
        { error: 'Konsol tidak ditemukan' },
        { status: 404 }
      );
    }
    console.error(`Error updating konsol ${params.id}:`, error);
    return NextResponse.json(
      { error: 'Gagal memperbarui konsol' },
      { status: 500 }
    );
  }
}

// --- FUNGSI DELETE (DELETE) ---
export async function DELETE(request: Request, context: RouteContext) {
  const session = await getServerSession(authOptions);
  if (!session || session.user?.role !== 'admin') {
    return NextResponse.json({ error: 'Akses ditolak' }, { status: 403 });
  }

  const { params } = context;
  try {
    const id = parseInt(params.id);
    if (isNaN(id)) {
      return NextResponse.json({ error: 'ID tidak valid' }, { status: 400 });
    }

    await prisma.konsol.delete({
      where: { id },
    });

    return NextResponse.json(
      { message: 'Konsol berhasil dihapus' },
      { status: 200 }
    );
  } catch (error) {
    if (error instanceof Prisma.PrismaClientKnownRequestError) {
      // P2003: Foreign key (jika konsol masih terelasi dengan ruangan)
      if (error.code === 'P2003') {
        return NextResponse.json(
          { error: 'Gagal menghapus: Konsol ini masih terhubung ke Ruangan.' },
          { status: 400 }
        );
      }
      if (error.code === 'P2025') {
        return NextResponse.json(
          { error: 'Konsol tidak ditemukan' },
          { status: 404 }
        );
      }
    }
    console.error(`Error deleting konsol ${params.id}:`, error);
    return NextResponse.json(
      { error: 'Gagal menghapus konsol' },
      { status: 500 }
    );
  }
}