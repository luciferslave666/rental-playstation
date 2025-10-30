// src/app/api/transaksi/stop/route.ts

import { NextResponse } from 'next/server';
import { Prisma } from '@prisma/client';
import prisma from '@/src/lib/prisma'; // Sesuaikan path jika perlu

// Helper untuk menghitung durasi dalam menit
function getDurationInMinutes(startTime: Date, endTime: Date): number {
  const diffMs = endTime.getTime() - startTime.getTime();
  const diffMinutes = Math.ceil(diffMs / (1000 * 60));
  return diffMinutes < 1 ? 1 : diffMinutes; // Minimal 1 menit
}

export async function POST(request: Request) {
  try {
    const { transaksiId } = await request.json();
    if (!transaksiId) {
      return NextResponse.json({ error: 'transaksiId diperlukan' }, { status: 400 });
    }

    const tId = parseInt(transaksiId);
    const endTime = new Date();

    const updatedTransaksi = await prisma.$transaction(async (tx) => {
      
      // 1. Ambil data transaksi, TERMASUK relasi paket dan ruangan
      const transaksi = await tx.transaksi.findUnique({
        where: { id: tId },
        include: {
          paket: true, // Ambil data paket (jika ada)
          ruangan: { select: { tarifPerJam: true, nomorRuangan: true } }, // Ambil tarif & nama
        },
      });

      if (!transaksi) {
        throw new Error('Transaksi tidak ditemukan');
      }
      if (!transaksi.ruangan) {
        throw new Error('Ruangan tidak terhubung dengan transaksi');
      }

      let baseCost = 0;
      let overtimeCost = 0; // Variabel baru untuk biaya overtime
      const actualDurationInMinutes = getDurationInMinutes(transaksi.waktuMulai, endTime);
      const tarifPerMenit = transaksi.ruangan.tarifPerJam / 60;

      // 2. Tentukan Biaya Dasar (Sewa/Paket)
      if (transaksi.paket) {
        // --- INI LOGIKA BARU UNTUK PAKET ---
        baseCost = transaksi.paket.hargaPaket; // Biaya dasar adalah harga paket

        // Cek jika ada overtime
        const overtimeMinutes = actualDurationInMinutes - transaksi.paket.durasiMenit;
        if (overtimeMinutes > 0) {
          // Jika main lebih lama dari durasi paket, hitung biaya overtime
          overtimeCost = Math.round(overtimeMinutes * tarifPerMenit);
        }
        
      } else {
        // --- INI LOGIKA LAMA (REGULER) ---
        // Hitung biaya sewa berdasarkan durasi
        baseCost = Math.round(actualDurationInMinutes * tarifPerMenit);
      }

      // 3. Hitung total biaya produk (sama seperti sebelumnya)
      const saleDetails = await tx.detailPenjualan.findMany({
        where: { transaksiId: tId },
      });
      const productCost = saleDetails.reduce((total, item) => {
        return total + (item.jumlah * item.hargaSaatBeli);
      }, 0);

      // 4. Hitung Total Biaya Akhir (Biaya Dasar + Overtime + Produk)
      const finalTotalBiaya = baseCost + overtimeCost + productCost;

      // 5. Update Transaksi dengan data final
      const finishedTransaksi = await tx.transaksi.update({
        where: { id: tId },
        data: {
          waktuSelesai: endTime,
          totalBiaya: finalTotalBiaya, // Simpan total biaya gabungan
        },
        // 6. Include semua data yang relevan untuk modal pembayaran
        include: {
          ruangan: true, 
          paket: true, // Kirim data paket ke modal
          detailPenjualan: {
            include: {
              produk: true, // Sertakan nama & harga produk
            },
          },
        },
      });

      // 7. Update status Ruangan jadi 'KOSONG'
      await tx.ruangan.update({
        where: { id: finishedTransaksi.idRuangan },
        data: { status: 'KOSONG' },
      });

      return finishedTransaksi;
    });

    return NextResponse.json(updatedTransaksi);

  } catch (error) {
    console.error("Error stopping transaksi:", error);
    const errorMessage = error instanceof Error ? error.message : 'Gagal menghentikan transaksi';
    return NextResponse.json({ error: errorMessage }, { status: 500 });
  }
}