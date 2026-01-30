<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #{{ $transaksi->id_transaksi }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace; /* Font struk */
            font-size: 12px;
            margin: 0;
            padding: 10px;
            width: 58mm; /* Ukuran kertas thermal standar */
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .line { border-bottom: 1px dashed #000; margin: 5px 0; }
        .flex { display: flex; justify-content: space-between; }
        
        /* Hilangkan elemen browser saat print */
        @media print {
            @page { margin: 0; }
            body { margin: 0; padding: 10px; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="text-center">
        <h2 style="margin:0">RENTAL PS MANTAP</h2>
        <p style="margin:0">Jl. Contoh No. 123, Bandung</p>
        <p style="margin:0">WA: 0812-3456-7890</p>
    </div>

    <div class="line"></div>

    <div class="flex">
        <span>Tgl:</span>
        <span>{{ \Carbon\Carbon::parse($transaksi->created_at)->format('d/m/y H:i') }}</span>
    </div>
    <div class="flex">
        <span>Kasir:</span>
        <span>{{ $transaksi->user->nama ?? 'Admin' }}</span>
    </div>
    <div class="flex">
        <span>Plg:</span>
        <span>{{ substr($transaksi->pelanggan->nama_pelanggan, 0, 15) }}</span>
    </div>

    <div class="line"></div>

    <div class="bold">{{ $transaksi->ruangan->nomor_ruangan }} ({{ $transaksi->ruangan->tipe_ruangan }})</div>
    
    @if($transaksi->id_paket)
        <div>{{ $transaksi->paket->nama_paket }}</div>
        <div class="text-right">Rp {{ number_format($transaksi->total_biaya, 0,',','.') }}</div>
    @else
        <div>Reguler (Per Jam)</div>
        <div class="flex">
            <span>{{ \Carbon\Carbon::parse($transaksi->waktu_mulai)->diffInMinutes(\Carbon\Carbon::parse($transaksi->waktu_selesai)) }} Menit</span>
            <span>Rp {{ number_format($transaksi->total_biaya, 0,',','.') }}</span>
        </div>
    @endif

    <div class="line"></div>

    <div class="flex bold" style="font-size: 14px">
        <span>TOTAL</span>
        <span>Rp {{ number_format($transaksi->total_biaya, 0,',','.') }}</span>
    </div>

    <div class="line"></div>
    
    <div class="text-center" style="margin-top: 10px;">
        <p>Terima Kasih!</p>
        <p>Simpan nomor kami untuk<br>Booking via WhatsApp</p>
    </div>

</body>
</html>