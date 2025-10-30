-- CreateTable
CREATE TABLE `User` (
    `id_user` INTEGER NOT NULL AUTO_INCREMENT,
    `nama` VARCHAR(191) NOT NULL,
    `username` VARCHAR(191) NOT NULL,
    `password` VARCHAR(191) NOT NULL,
    `role` VARCHAR(191) NOT NULL,

    UNIQUE INDEX `User_username_key`(`username`),
    PRIMARY KEY (`id_user`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `Pelanggan` (
    `id_pelanggan` INTEGER NOT NULL AUTO_INCREMENT,
    `nama_pelanggan` VARCHAR(191) NOT NULL,
    `no_hp` VARCHAR(191) NULL,

    UNIQUE INDEX `Pelanggan_no_hp_key`(`no_hp`),
    PRIMARY KEY (`id_pelanggan`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `Ruangan` (
    `id_ruangan` INTEGER NOT NULL AUTO_INCREMENT,
    `nomor_ruangan` VARCHAR(191) NOT NULL,
    `status` VARCHAR(191) NOT NULL DEFAULT 'KOSONG',
    `tarif_per_jam` DOUBLE NOT NULL,
    `tipe_ruangan` VARCHAR(191) NOT NULL,
    `deskripsi_fasilitas` VARCHAR(191) NOT NULL,

    UNIQUE INDEX `Ruangan_nomor_ruangan_key`(`nomor_ruangan`),
    PRIMARY KEY (`id_ruangan`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `Konsol` (
    `id_konsol` INTEGER NOT NULL AUTO_INCREMENT,
    `seri_konsol` VARCHAR(191) NOT NULL,

    PRIMARY KEY (`id_konsol`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `Transaksi` (
    `id_transaksi` INTEGER NOT NULL AUTO_INCREMENT,
    `waktu_mulai` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
    `waktu_selesai` DATETIME(3) NULL,
    `total_biaya` DOUBLE NULL,
    `status_pembayaran` VARCHAR(191) NOT NULL DEFAULT 'BELUM_BAYAR',
    `id_user` INTEGER NOT NULL,
    `id_pelanggan` INTEGER NULL,
    `id_ruangan` INTEGER NOT NULL,

    INDEX `Transaksi_id_user_idx`(`id_user`),
    INDEX `Transaksi_id_pelanggan_idx`(`id_pelanggan`),
    INDEX `Transaksi_id_ruangan_idx`(`id_ruangan`),
    PRIMARY KEY (`id_transaksi`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `_KonsolToRuangan` (
    `A` INTEGER NOT NULL,
    `B` INTEGER NOT NULL,

    UNIQUE INDEX `_KonsolToRuangan_AB_unique`(`A`, `B`),
    INDEX `_KonsolToRuangan_B_index`(`B`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- AddForeignKey
ALTER TABLE `Transaksi` ADD CONSTRAINT `Transaksi_id_user_fkey` FOREIGN KEY (`id_user`) REFERENCES `User`(`id_user`) ON DELETE RESTRICT ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `Transaksi` ADD CONSTRAINT `Transaksi_id_pelanggan_fkey` FOREIGN KEY (`id_pelanggan`) REFERENCES `Pelanggan`(`id_pelanggan`) ON DELETE SET NULL ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `Transaksi` ADD CONSTRAINT `Transaksi_id_ruangan_fkey` FOREIGN KEY (`id_ruangan`) REFERENCES `Ruangan`(`id_ruangan`) ON DELETE RESTRICT ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `_KonsolToRuangan` ADD CONSTRAINT `_KonsolToRuangan_A_fkey` FOREIGN KEY (`A`) REFERENCES `Konsol`(`id_konsol`) ON DELETE CASCADE ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `_KonsolToRuangan` ADD CONSTRAINT `_KonsolToRuangan_B_fkey` FOREIGN KEY (`B`) REFERENCES `Ruangan`(`id_ruangan`) ON DELETE CASCADE ON UPDATE CASCADE;
