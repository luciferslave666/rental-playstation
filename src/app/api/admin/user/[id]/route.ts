// src/app/api/admin/user/[id]/route.ts
import { NextResponse } from 'next/server';
import prisma from '@/src/lib/prisma';
import { Prisma } from '@prisma/client';
import bcrypt from 'bcryptjs';

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

    // Validasi input
    if (!data.username || !data.nama || !data.role) {
      return NextResponse.json(
        { error: 'Nama, Username, dan Role wajib diisi' },
        { status: 400 }
      );
    }

    // Validasi role
    const validRoles = ['ADMIN', 'OWNER', 'RESEPSIONIS']; // Sesuaikan dengan role di schema
    if (!validRoles.includes(data.role)) {
      return NextResponse.json(
        { error: 'Role tidak valid' },
        { status: 400 }
      );
    }

    // Siapkan data untuk di-update
    const dataToUpdate: Prisma.UserUpdateInput = {
      nama: data.nama,
      username: data.username,
      role: data.role,
    };

    // Update password hanya jika diisi
    if (data.password && data.password.trim() !== '') {
      // Validasi panjang password
      if (data.password.length < 6) {
        return NextResponse.json(
          { error: 'Password minimal 6 karakter' },
          { status: 400 }
        );
      }
      dataToUpdate.password = await bcrypt.hash(data.password, 10);
    }

    const updatedUser = await prisma.user.update({
      where: { id },
      data: dataToUpdate,
      select: {
        id: true,
        nama: true,
        username: true,
        role: true,
      },
    });

    return NextResponse.json(updatedUser);
  } catch (error) {
    if (error instanceof Prisma.PrismaClientKnownRequestError) {
      // P2002: Unique constraint violation
      if (error.code === 'P2002') {
        return NextResponse.json(
          { error: 'Gagal: Username ini sudah terdaftar.' },
          { status: 400 }
        );
      }
      // P2025: Record not found
      if (error.code === 'P2025') {
        return NextResponse.json(
          { error: 'User tidak ditemukan' },
          { status: 404 }
        );
      }
    }
    console.error('Error updating user:', error);
    return NextResponse.json(
      { error: 'Gagal memperbarui user' },
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

    // Optional: Cek agar user tidak bisa menghapus dirinya sendiri
    // Implementasi dengan session/auth:
    // const session = await getServerSession(authOptions);
    // if (session?.user?.id === id) {
    //   return NextResponse.json(
    //     { error: 'Anda tidak bisa menghapus akun Anda sendiri' },
    //     { status: 403 }
    //   );
    // }

    await prisma.user.delete({
      where: { id },
    });

    return NextResponse.json(
      { message: 'User berhasil dihapus' },
      { status: 200 }
    );
  } catch (error) {
    if (error instanceof Prisma.PrismaClientKnownRequestError) {
      // P2003: Foreign key constraint violation
      if (error.code === 'P2003') {
        return NextResponse.json(
          { error: 'Gagal menghapus: User ini masih terikat dengan data transaksi.' },
          { status: 400 }
        );
      }
      // P2025: Record not found
      if (error.code === 'P2025') {
        return NextResponse.json(
          { error: 'User tidak ditemukan' },
          { status: 404 }
        );
      }
    }
    console.error('Error deleting user:', error);
    return NextResponse.json(
      { error: 'Gagal menghapus user' },
      { status: 500 }
    );
  }
}