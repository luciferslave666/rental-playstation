// src/app/api/admin/produk/[id]/route.ts

import { NextResponse } from 'next/server';
import prisma from '@/src/lib/prisma'; // Sesuaikan path jika perlu
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
    if (!data.nama || data.harga === undefined || data.stok === undefined) {
      return NextResponse.json(
        { error: 'Nama, Harga, dan Stok wajib diisi' },
        { status: 400 }
      );
    }

    const updatedProduk = await prisma.produk.update({
      where: { id },
      data: {
        nama: data.nama,
        harga: parseFloat(data.harga),
        stok: parseInt(data.stok),
      },
    });

    return NextResponse.json(updatedProduk);
  } catch (error) {
    if (error instanceof Prisma.PrismaClientKnownRequestError) {
      if (error.code === 'P2002') {
        return NextResponse.json(
          { error: 'Gagal: Nama produk ini sudah ada.' },
          { status: 400 }
        );
      }
      if (error.code === 'P2025') {
        return NextResponse.json(
          { error: 'Produk tidak ditemukan' },
          { status: 404 }
        );
      }
    }
    console.error(`Error updating produk ${params.id}:`, error);
    return NextResponse.json(
      { error: 'Gagal memperbarui produk' },
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

    await prisma.produk.delete({
      where: { id },
    });

    return NextResponse.json(
      { message: 'Produk berhasil dihapus' },
      { status: 200 }
    );
  } catch (error) {
    if (error instanceof Prisma.PrismaClientKnownRequestError) {
      if (error.code === 'P2003') {
        return NextResponse.json(
          { error: 'Gagal menghapus: Produk ini masih terikat dengan data penjualan.' },
          { status: 400 }
        );
      }
      if (error.code === 'P2025') {
        return NextResponse.json(
          { error: 'Produk tidak ditemukan' },
          { status: 404 }
        );
      }
    }
    console.error(`Error deleting produk ${params.id}:`, error);
    return NextResponse.json(
      { error: 'Gagal menghapus produk' },
      { status: 500 }
    );
  }
}