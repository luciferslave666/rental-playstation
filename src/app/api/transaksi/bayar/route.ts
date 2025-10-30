// src/app/api/transaksi/bayar/route.ts

import { NextResponse } from 'next/server';
import prisma from '@/src/lib/prisma'; // Sesuaikan path jika perlu
import { Prisma } from '@prisma/client';

export async function PUT(request: Request) {
  try {
    // Ambil ID transaksi dari body
    const { transaksiId } = await request.json();

    if (!transaksiId) {
      return NextResponse.json(
        { error: 'transaksiId diperlukan' },
        { status: 400 }
      );
    }

    const id = parseInt(transaksiId);

    // Update status pembayaran di database
    const updatedTransaksi = await prisma.transaksi.update({
      where: { id: id },
      data: {
        statusPembayaran: 'LUNAS',
      },
    });

    return NextResponse.json(updatedTransaksi);

  } catch (error) {
    if (error instanceof Prisma.PrismaClientKnownRequestError) {
      // P2025: Record not found (Data tidak ditemukan)
      if (error.code === 'P2025') {
        return NextResponse.json(
          { error: 'Transaksi tidak ditemukan' },
          { status: 404 }
        );
      }
    }
    console.error('Error updating payment status:', error);
    return NextResponse.json(
      { error: 'Gagal mengupdate status pembayaran' },
      { status: 500 }
    );
  }
}