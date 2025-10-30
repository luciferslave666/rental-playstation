// src/app/api/admin/pelanggan/[id]/route.ts

import { NextResponse } from 'next/server';
import prisma from '@/src/lib/prisma';
import { Prisma } from '@prisma/client';

// Tipe untuk params - di Next.js 15+, params adalah Promise
interface RouteParams {
  id: string;
}

// --- FUNGSI UPDATE (PUT) ---
export async function PUT(
  request: Request,
  { params }: { params: Promise<RouteParams> }
) {
  try {
    // Await params karena di Next.js 15+ params adalah Promise
    const { id: paramId } = await params;
    const id = parseInt(paramId);

    // Validasi ID
    if (isNaN(id)) {
      return NextResponse.json(
        { error: 'ID tidak valid' },
        { status: 400 }
      );
    }

    const data = await request.json();

    if (!data.namaPelanggan) {
      return NextResponse.json(
        { error: 'Nama Pelanggan wajib diisi' },
        { status: 400 }
      );
    }

    const updatedPelanggan = await prisma.pelanggan.update({
      where: { id },
      data: {
        namaPelanggan: data.namaPelanggan,
        noHp: data.noHp || null,
      },
    });

    return NextResponse.json(updatedPelanggan);
  } catch (error) {
    if (error instanceof Prisma.PrismaClientKnownRequestError) {
      if (error.code === 'P2002') {
        return NextResponse.json(
          { error: 'Gagal: Nomor HP ini sudah terdaftar.' },
          { status: 400 }
        );
      }
      if (error.code === 'P2025') {
        return NextResponse.json(
          { error: 'Pelanggan tidak ditemukan' },
          { status: 404 }
        );
      }
    }
    console.error('Error updating pelanggan:', error);
    return NextResponse.json(
      { error: 'Gagal memperbarui pelanggan' },
      { status: 500 }
    );
  }
}

// --- FUNGSI DELETE (DELETE) ---
export async function DELETE(
  request: Request,
  { params }: { params: Promise<RouteParams> }
) {
  try {
    // Await params
    const { id: paramId } = await params;
    const id = parseInt(paramId);

    // Validasi ID
    if (isNaN(id)) {
      return NextResponse.json(
        { error: 'ID tidak valid' },
        { status: 400 }
      );
    }

    await prisma.pelanggan.delete({
      where: { id },
    });

    return NextResponse.json(
      { message: 'Pelanggan berhasil dihapus' },
      { status: 200 }
    );
  } catch (error) {
    if (error instanceof Prisma.PrismaClientKnownRequestError) {
      if (error.code === 'P2003') {
        return NextResponse.json(
          { error: 'Gagal menghapus: Pelanggan ini masih terikat dengan data transaksi.' },
          { status: 400 }
        );
      }
      if (error.code === 'P2025') {
        return NextResponse.json(
          { error: 'Pelanggan tidak ditemukan' },
          { status: 404 }
        );
      }
    }
    console.error('Error deleting pelanggan:', error);
    return NextResponse.json(
      { error: 'Gagal menghapus pelanggan' },
      { status: 500 }
    );
  }
}