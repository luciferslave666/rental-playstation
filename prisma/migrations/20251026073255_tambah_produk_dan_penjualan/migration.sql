/*
  Warnings:

  - You are about to drop the column `id_pelanggan` on the `transaksi` table. All the data in the column will be lost.
  - You are about to drop the column `id_ruangan` on the `transaksi` table. All the data in the column will be lost.
  - You are about to drop the column `id_user` on the `transaksi` table. All the data in the column will be lost.
  - Added the required column `idRuangan` to the `Transaksi` table without a default value. This is not possible if the table is not empty.
  - Added the required column `idUser` to the `Transaksi` table without a default value. This is not possible if the table is not empty.

*/
-- DropForeignKey
ALTER TABLE `transaksi` DROP FOREIGN KEY `Transaksi_id_pelanggan_fkey`;

-- DropForeignKey
ALTER TABLE `transaksi` DROP FOREIGN KEY `Transaksi_id_ruangan_fkey`;

-- DropForeignKey
ALTER TABLE `transaksi` DROP FOREIGN KEY `Transaksi_id_user_fkey`;

-- DropIndex
DROP INDEX `Transaksi_id_pelanggan_idx` ON `transaksi`;

-- DropIndex
DROP INDEX `Transaksi_id_ruangan_idx` ON `transaksi`;

-- DropIndex
DROP INDEX `Transaksi_id_user_idx` ON `transaksi`;

-- AlterTable
ALTER TABLE `transaksi` DROP COLUMN `id_pelanggan`,
    DROP COLUMN `id_ruangan`,
    DROP COLUMN `id_user`,
    ADD COLUMN `idPelanggan` INTEGER NULL,
    ADD COLUMN `idRuangan` INTEGER NOT NULL,
    ADD COLUMN `idUser` INTEGER NOT NULL;

-- CreateTable
CREATE TABLE `Produk` (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `nama` VARCHAR(191) NOT NULL,
    `harga` DOUBLE NOT NULL,
    `stok` INTEGER NOT NULL DEFAULT 0,

    UNIQUE INDEX `Produk_nama_key`(`nama`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `DetailPenjualan` (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `jumlah` INTEGER NOT NULL,
    `hargaSaatBeli` DOUBLE NOT NULL,
    `transaksiId` INTEGER NOT NULL,
    `produkId` INTEGER NOT NULL,

    INDEX `DetailPenjualan_transaksiId_idx`(`transaksiId`),
    INDEX `DetailPenjualan_produkId_idx`(`produkId`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateIndex
CREATE INDEX `Transaksi_idUser_idx` ON `Transaksi`(`idUser`);

-- CreateIndex
CREATE INDEX `Transaksi_idPelanggan_idx` ON `Transaksi`(`idPelanggan`);

-- CreateIndex
CREATE INDEX `Transaksi_idRuangan_idx` ON `Transaksi`(`idRuangan`);

-- AddForeignKey
ALTER TABLE `Transaksi` ADD CONSTRAINT `Transaksi_idUser_fkey` FOREIGN KEY (`idUser`) REFERENCES `User`(`id_user`) ON DELETE RESTRICT ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `Transaksi` ADD CONSTRAINT `Transaksi_idPelanggan_fkey` FOREIGN KEY (`idPelanggan`) REFERENCES `Pelanggan`(`id_pelanggan`) ON DELETE SET NULL ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `Transaksi` ADD CONSTRAINT `Transaksi_idRuangan_fkey` FOREIGN KEY (`idRuangan`) REFERENCES `Ruangan`(`id_ruangan`) ON DELETE RESTRICT ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `DetailPenjualan` ADD CONSTRAINT `DetailPenjualan_transaksiId_fkey` FOREIGN KEY (`transaksiId`) REFERENCES `Transaksi`(`id_transaksi`) ON DELETE RESTRICT ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `DetailPenjualan` ADD CONSTRAINT `DetailPenjualan_produkId_fkey` FOREIGN KEY (`produkId`) REFERENCES `Produk`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE;
