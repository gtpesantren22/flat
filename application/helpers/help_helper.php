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

function formatUangSingkat($angka)
{
    // Jika angka lebih dari atau sama dengan 1 miliar
    if ($angka >= 1000000000) {
        return number_format($angka / 1000000000, 2) . ' B';
    }
    // Jika angka lebih dari atau sama dengan 1 juta
    elseif ($angka >= 1000000) {
        return number_format($angka / 1000000, 2) . ' M';
    }
    // Jika angka lebih dari atau sama dengan 1 ribu
    elseif ($angka >= 1000) {
        return number_format($angka / 1000, 2) . ' K';
    }
    // Jika angka di bawah 1 ribu
    return number_format($angka);
}
