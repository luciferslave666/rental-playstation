// src/middleware.ts

import { withAuth } from "next-auth/middleware";
import { NextResponse } from "next/server";

export default withAuth(
  // `withAuth` me-wrap middleware kita
  function middleware(req) {
    // Ambil data token dan pathname
    const token = req.nextauth.token;
    const { pathname } = req.nextUrl;

    // --- LOGIKA REDIRECT BERDASARKAN ROLE ---

    // 1. Jika user adalah ADMIN
    if (token?.role === "admin") {
      // Jika Admin mencoba mengakses dashboard Kasir (halaman root '/')
      if (pathname === "/") {
        // Redirect dia ke dashboard Admin
        return NextResponse.redirect(new URL("/admin/dashboard", req.url));
      }
    }

    // 2. Jika user adalah KASIR
    if (token?.role === "kasir") {
      // Jika Kasir mencoba mengakses SEMUA halaman di bawah /admin
      if (pathname.startsWith("/admin")) {
        // Redirect dia kembali ke dashboard Kasir
        return NextResponse.redirect(new URL("/", req.url));
      }
    }

    // 3. Jika rolenya cocok (misal Admin di /admin/... atau Kasir di /)
    // Biarkan request-nya lanjut
    return NextResponse.next();
  },
  {
    // Opsi tambahan untuk 'withAuth'
    callbacks: {
      // Kita hanya izinkan user yang punya token (sudah login)
      authorized: ({ token }) => !!token,
    },
  }
);

// 3. Konfigurasi Matcher (Halaman yang Dilindungi)
// Pastikan kita melindungi SEMUA halaman yang relevan
export const config = {
  matcher: [
    /*
     * Lindungi semua rute ini:
     * / (dashboard kasir)
     * /admin/:path* (semua halaman admin, termasuk /admin/dashboard)
     */
    "/",
    "/admin/:path*",
  ],
};