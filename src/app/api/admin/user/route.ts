// src/app/api/admin/user/route.ts

import { NextResponse } from 'next/server';
import prisma from '@/src/lib/prisma'; // Sesuaikan path import jika perlu
import { Prisma } from '@prisma/client';
import bcrypt from 'bcryptjs';

// --- FUNGSI CREATE (POST) ---
export async function POST(request: Request) {
  try {
    const data = await request.json();

    if (!data.username || !data.password || !data.nama || !data.role) {
      return NextResponse.json(
        { error: 'Semua field (Nama, Username, Password, Role) wajib diisi' },
        { status: 400 }
      );
    }

    // --- HASHING PASSWORD ---
    const hashedPassword = await bcrypt.hash(data.password, 10); // Enkripsi password

    const user = await prisma.user.create({
      data: {
        nama: data.nama,
        username: data.username,
        role: data.role,
        password: hashedPassword, // Simpan password yang sudah di-hash
      },
      // Pilih data yang mau dikembalikan (JANGAN kembalikan password)
      select: {
        id: true,
        nama: true,
        username: true,
        role: true,
      },
    });

    return NextResponse.json(user, { status: 201 });
  } catch (error) {
    if (error instanceof Prisma.PrismaClientKnownRequestError) {
      // Error P2002: Unique constraint (Username duplikat)
      if (error.code === 'P2002') {
        return NextResponse.json(
          { error: 'Gagal: Username ini sudah terdaftar.' },
          { status: 400 }
        );
      }
    }
    console.error('Error creating user:', error);
    return NextResponse.json(
      { error: 'Gagal membuat user baru' },
      { status: 500 }
    );
  }
}

// --- FUNGSI READ (GET) ---
export async function GET() {
  try {
    const users = await prisma.user.findMany({
      orderBy: {
        nama: 'asc',
      },
      // PENTING: Pilih kolomnya agar password tidak ikut terkirim
      select: {
        id: true,
        nama: true,
        username: true,
        role: true,
      },
    });
    // users sekarang bertipe UserAdminData[] (tanpa password)
    return NextResponse.json(users);
  } catch (error) {
    console.error('Error fetching user list:', error);
    return NextResponse.json(
      { error: 'Gagal mengambil daftar user' },
      { status: 500 }
    );
  }
}