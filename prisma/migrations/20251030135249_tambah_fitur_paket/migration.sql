-- AlterTable
ALTER TABLE `transaksi` ADD COLUMN `paketId` INTEGER NULL;

-- CreateTable
CREATE TABLE `Paket` (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `nama_paket` VARCHAR(191) NOT NULL,
    `durasi_menit` INTEGER NOT NULL,
    `harga_paket` DOUBLE NOT NULL,

    UNIQUE INDEX `Paket_nama_paket_key`(`nama_paket`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- AddForeignKey
ALTER TABLE `Transaksi` ADD CONSTRAINT `Transaksi_paketId_fkey` FOREIGN KEY (`paketId`) REFERENCES `Paket`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;
