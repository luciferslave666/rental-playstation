// src/app/api/transaksi/start/route.ts

import { NextResponse } from 'next/server';
import prisma from '@/src/lib/prisma'; // Sesuaikan path jika perlu
import { Prisma } from '@prisma/client';

export async function POST(request: Request) {
  try {
    // 1. Ambil 'paketId' dari body request
    const { idRuangan, idUser, idPelanggan, paketId } = await request.json();

    if (!idRuangan || !idUser) {
      return NextResponse.json({ error: 'idRuangan dan idUser diperlukan' }, { status: 400 });
    }

    // Siapkan data dasar untuk transaksi
    let dataToCreate: any = {
      idRuangan: idRuangan,
      idUser: idUser,
      idPelanggan: idPelanggan || null,
      waktuMulai: new Date(),
      statusPembayaran: 'BELUM_BAYAR',
    };

    // 2. Logika jika kasir memilih PAKET
    if (paketId) {
      // Cari paket di database
      const paket = await prisma.paket.findUnique({
        where: { id: parseInt(paketId) },
      });

      if (!paket) {
        return NextResponse.json({ error: 'Paket tidak valid' }, { status: 400 });
      }

      // 3. Jika paket valid, tambahkan ke data transaksi
      dataToCreate.paketId = paket.id;
      // Langsung set totalBiaya sesuai harga paket
      dataToCreate.totalBiaya = paket.hargaPaket;
      
      // (Opsional: Kita juga bisa set waktuSelesai otomatis)
      // const waktuSelesaiPaket = new Date();
      // waktuSelesaiPaket.setMinutes(waktuSelesaiPaket.getMinutes() + paket.durasiMenit);
      // dataToCreate.waktuSelesai = waktuSelesaiPaket;
      
    } else {
      // Jika billing reguler (per jam), totalBiaya biarkan null
      dataToCreate.paketId = null;
      dataToCreate.totalBiaya = null;
    }

    // 4. Jalankan transaksi database
    const newTransaksi = await prisma.$transaction(async (tx) => {
      // 1. Ubah status Ruangan jadi 'TERISI'
      await tx.ruangan.update({
        where: { id: idRuangan },
        data: { status: 'TERISI' },
      });

      // 2. Buat data Transaksi baru dengan dataToCreate
      const transaksi = await tx.transaksi.create({
        data: dataToCreate,
      });

      return transaksi;
    });

    return NextResponse.json(newTransaksi, { status: 201 });
  } catch (error) {
    console.error("Error starting transaksi:", error);
    if (error instanceof Prisma.PrismaClientKnownRequestError) {
       if (error.code === 'P2003') { // Foreign key constraint (misal paketId salah)
            return NextResponse.json({ error: 'ID Ruangan, User, atau Paket tidak valid.' }, { status: 400 });
        }
    }
    return NextResponse.json({ error: 'Gagal memulai transaksi' }, { status: 500 });
  }
}