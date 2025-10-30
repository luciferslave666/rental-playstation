// src/types/index.ts

// Tipe data dasar dari Prisma
// PASTIKAN DI-EXPORT agar bisa dipakai 'extends'
export interface Ruangan {
  id: number;
  nomorRuangan: string;
  status: string;
  tarifPerJam: number;
  tipeRuangan: string;
  deskripsiFasilitas: string;
}

export interface DetailPenjualan {
  id: number;
  jumlah: number;
  hargaSaatBeli: number;
  transaksiId: number;
  produkId: number;
}

export interface Konsol {
  id: number;
  seriKonsol: string;
}

// Tipe data dasar dari Prisma
export interface Transaksi {
  id: number;
  waktuMulai: string; // JSON mengirim DateTime sebagai string ISO
  waktuSelesai: string | null;
  totalBiaya: number | null;
  statusPembayaran: string;
  idUser: number;
  idPelanggan: number | null;
  idRuangan: number;
}

export interface Pelanggan {
  id: number;
  namaPelanggan: string;
  noHp: string | null;
}

export interface User {
  id: number;
  nama: string;
  username: string;
  password: string; // Ini HANYA untuk backend
  role: string;
}

// Tipe data untuk Dashboard (/api/ruangan)
// Ini sudah sempurna.
export interface RuanganDashboradData extends Ruangan {
  transaksi: Transaksi[]; // Relasi transaksi aktif
}

// Tipe data untuk Laporan Transaksi (/api/laporan)
export interface LaporanTransaksi {
  id: number;
  waktuSelesai: string | null;
  totalBiaya: number | null;
  user: {
    nama: string;
  };
  ruangan: {
    nomorRuangan: string;
  };
  pelanggan: {
    namaPelanggan: string;
  } | null;
}

// Tipe data untuk respons API Laporan
export interface LaporanResponse {
  summary: {
    totalPendapatan: number;
    totalTransaksi: number;
  };
  transactions: LaporanTransaksi[];
  filter: {
    startDate: string;
    endDate: string;
  };
}

export interface Produk {
  id: number;
  nama: string;
  harga: number;
  stok: number;
}

export interface Paket {
  id: number;
  namaPaket: string;
  durasiMenit: number;
  hargaPaket: number;
}

export interface DashboardStats {
  totalPendapatanHariIni: number;
  totalTransaksiHariIni: number;
  ruanganTerisi: number;
  totalRuangan: number;
}

// Tipe data untuk Halaman Admin (/api/admin/ruangan)
// Ini juga sudah sempurna.
export interface RuanganAdminData extends Ruangan {
  konsol: Konsol[];
}
export type UserAdminData = Omit<User, 'password'>;
