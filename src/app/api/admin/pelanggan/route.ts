// src/app/api/admin/pelanggan/route.ts

import { NextResponse } from 'next/server';
import prisma from '@/src/lib/prisma'; // Sesuaikan path import jika perlu
import { Prisma } from '@prisma/client';

// --- FUNGSI CREATE (POST) ---
export async function POST(request: Request) {
  try {
    const data = await request.json();

    if (!data.namaPelanggan) {
      return NextResponse.json(
        { error: 'Nama Pelanggan wajib diisi' },
        { status: 400 }
      );
    }

    const pelanggan = await prisma.pelanggan.create({
      data: {
        namaPelanggan: data.namaPelanggan,
        noHp: data.noHp || null, // Set null jika string kosong
      },
    });

    return NextResponse.json(pelanggan, { status: 201 });
  } catch (error) {
    if (error instanceof Prisma.PrismaClientKnownRequestError) {
      // Error P2002: Unique constraint failed (No HP duplikat)
      if (error.code === 'P2002') {
        return NextResponse.json(
          { error: 'Gagal: Nomor HP ini sudah terdaftar.' },
          { status: 400 }
        );
      }
    }
    console.error('Error creating pelanggan:', error);
    return NextResponse.json(
      { error: 'Gagal membuat pelanggan baru' },
      { status: 500 }
    );
  }
}

// --- FUNGSI READ (GET) ---
export async function GET() {
  try {
    const pelanggan = await prisma.pelanggan.findMany({
      orderBy: {
        namaPelanggan: 'asc',
      },
    });
    return NextResponse.json(pelanggan);
  } catch (error) {
    console.error('Error fetching pelanggan list:', error);
    return NextResponse.json(
      { error: 'Gagal mengambil daftar pelanggan' },
      { status: 500 }
    );
  }
}