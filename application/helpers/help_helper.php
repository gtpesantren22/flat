<?php
function selisihTahun($tanggal)
{
    // Ubah parameter tanggal menjadi objek DateTime
    $tanggalAwal = new DateTime($tanggal);
    // Buat objek DateTime untuk tanggal hari ini
    $tanggalSekarang = new DateTime();

    // Hitung selisih menggunakan diff()
    $selisih = $tanggalAwal->diff($tanggalSekarang);

    // Kembalikan selisih dalam tahun
    return $selisih->y;
}

function rupiah($rp)
{
    if ($rp != null) {
        return 'Rp. ' . number_format($rp, 0, ',', '.');
    } else {
        return 'Rp. ' . number_format(0, 0, ',', '.');
    }
}

function rmRp($string)
{
    return preg_replace("/[^0-9]/", "", $string);
}

function bulan($bulan)
{
    $namaBulan = [
        "",
        "Januari",
        "Februari",
        "Maret",
        "April",
        "Mei",
        "Juni",
        "Juli",
        "Agustus",
        "September",
        "Oktober",
        "November",
        "Desember"
    ];

    return isset($namaBulan[$bulan]) ? $namaBulan[$bulan] : date('F');
}
