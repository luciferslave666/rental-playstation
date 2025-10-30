// src/app/api/admin/paket/[id]/route.ts

import { NextResponse } from 'next/server';
import prisma from '@/src/lib/prisma';
import { Prisma } from '@prisma/client';
import { getServerSession } from 'next-auth/next';
import { authOptions } from '@/src/app/api/auth/[...nextauth]/route';

// PUT - Update paket
export async function PUT(
  request: Request,
  context: { params: Promise<{ id: string }> }
) {
  const session = await getServerSession(authOptions);
  if (!session || session.user?.role !== 'admin') {
    return NextResponse.json({ error: 'Akses ditolak' }, { status: 403 });
  }

  try {
    const { id: paramId } = await context.params;
    const id = parseInt(paramId);
    
    if (isNaN(id)) {
      return NextResponse.json({ error: 'ID tidak valid' }, { status: 400 });
    }

    const data = await request.json();
    
    if (!data.namaPaket || !data.durasiMenit || !data.hargaPaket) {
      return NextResponse.json(
        { error: 'Nama, Durasi (menit), dan Harga wajib diisi' },
        { status: 400 }
      );
    }

    const updatedPaket = await prisma.paket.update({
      where: { id },
      data: {
        namaPaket: data.namaPaket,
        durasiMenit: parseInt(data.durasiMenit),
        hargaPaket: parseFloat(data.hargaPaket),
      },
    });

    return NextResponse.json(updatedPaket);
  } catch (error) {
    if (error instanceof Prisma.PrismaClientKnownRequestError) {
      if (error.code === 'P2002') {
        return NextResponse.json(
          { error: 'Gagal: Nama paket ini sudah ada.' },
          { status: 400 }
        );
      }
      if (error.code === 'P2025') {
        return NextResponse.json(
          { error: 'Paket tidak ditemukan' },
          { status: 404 }
        );
      }
    }
    console.error('Error updating paket:', error);
    return NextResponse.json(
      { error: 'Gagal memperbarui paket' },
      { status: 500 }
    );
  }
}

// DELETE - Hapus paket
export async function DELETE(
  request: Request,
  context: { params: Promise<{ id: string }> }
) {
  const session = await getServerSession(authOptions);
  if (!session || session.user?.role !== 'admin') {
    return NextResponse.json({ error: 'Akses ditolak' }, { status: 403 });
  }

  try {
    const { id: paramId } = await context.params;
    const id = parseInt(paramId);
    
    if (isNaN(id)) {
      return NextResponse.json({ error: 'ID tidak valid' }, { status: 400 });
    }

    await prisma.paket.delete({
      where: { id },
    });

    return NextResponse.json(
      { message: 'Paket berhasil dihapus' },
      { status: 200 }
    );
  } catch (error) {
    if (error instanceof Prisma.PrismaClientKnownRequestError) {
      if (error.code === 'P2003') {
        return NextResponse.json(
          { error: 'Gagal menghapus: Paket ini masih terikat dengan data transaksi.' },
          { status: 400 }
        );
      }
      if (error.code === 'P2025') {
        return NextResponse.json(
          { error: 'Paket tidak ditemukan' },
          { status: 404 }
        );
      }
    }
    console.error('Error deleting paket:', error);
    return NextResponse.json(
      { error: 'Gagal menghapus paket' },
      { status: 500 }
    );
  }
}