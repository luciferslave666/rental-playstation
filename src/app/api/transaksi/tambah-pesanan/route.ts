// src/app/api/transaksi/tambah-pesanan/route.ts

import { NextResponse } from 'next/server';
import prisma from '@/src/lib/prisma'; // Sesuaikan path jika perlu
import { Prisma } from '@prisma/client';
import { getServerSession } from 'next-auth/next';
import { authOptions } from '@/src/app/api/auth/[...nextauth]/route';

export async function POST(request: Request) {
  // Hanya user yang login bisa tambah pesanan
  const session = await getServerSession(authOptions);
  if (!session) {
    return NextResponse.json({ error: 'Akses ditolak' }, { status: 401 });
  }

  try {
    const { transaksiId, produkId, jumlah } = await request.json();

    // Validasi input dasar
    if (!transaksiId || !produkId || !jumlah || jumlah <= 0) {
      return NextResponse.json(
        { error: 'Input tidak valid (transaksiId, produkId, jumlah > 0)' },
        { status: 400 }
      );
    }

    const tId = parseInt(transaksiId);
    const pId = parseInt(produkId);
    const qty = parseInt(jumlah);

    // Gunakan transaksi database agar aman (cek stok & catat penjualan)
    const result = await prisma.$transaction(async (tx) => {
      // 1. Dapatkan info produk (harga & stok)
      const produk = await tx.produk.findUnique({
        where: { id: pId },
      });

      if (!produk) {
        throw new Error('Produk tidak ditemukan');
      }

      // 2. Cek apakah stok cukup
      if (produk.stok < qty) {
        throw new Error(`Stok ${produk.nama} tidak cukup (tersisa: ${produk.stok})`);
      }

      // 3. Kurangi stok produk
      await tx.produk.update({
        where: { id: pId },
        data: {
          stok: {
            decrement: qty, // Kurangi stok
          },
        },
      });

      // 4. Catat detail penjualan
      const detailPenjualan = await tx.detailPenjualan.create({
        data: {
          transaksiId: tId,
          produkId: pId,
          jumlah: qty,
          hargaSaatBeli: produk.harga, // Simpan harga saat ini
        },
      });

      return detailPenjualan;
    });

    return NextResponse.json(result, { status: 201 }); // 201 Created

  } catch (error) {
    // Tangani error spesifik (misal, stok habis)
    if (error instanceof Error && error.message.includes('Stok')) {
       return NextResponse.json({ error: error.message }, { status: 400 });
    }
    // Tangani error Prisma lainnya
    if (error instanceof Prisma.PrismaClientKnownRequestError) {
        // P2003: Foreign key constraint (Transaksi atau Produk ID salah)
        if (error.code === 'P2003') {
            return NextResponse.json({ error: 'ID Transaksi atau Produk tidak valid.' }, { status: 400 });
        }
    }
    console.error('Error adding order:', error);
    return NextResponse.json(
      { error: 'Gagal menambahkan pesanan' },
      { status: 500 }
    );
  }
}