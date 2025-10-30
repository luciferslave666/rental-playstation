// src/lib/prisma.ts

import { PrismaClient } from '@prisma/client';

// Deklarasikan 'global' untuk TypeScript
declare global {
  var prisma: PrismaClient | undefined;
}

// Cek untuk menghindari koneksi baru setiap hot-reload di development
const prisma = global.prisma || new PrismaClient();

if (process.env.NODE_ENV !== 'production') {
  global.prisma = prisma;
}

export default prisma;