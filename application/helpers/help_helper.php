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

function kirim_person($key, $no_hp, $pesan)
{
    $curl2 = curl_init();
    curl_setopt_array(
        $curl2,
        array(
            CURLOPT_URL => 'http://31.97.179.141/:3000/api/sendMessage',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'apiKey=' . $key . '&phone=' . $no_hp . '&message=' . $pesan,
        )
    );
    $response = curl_exec($curl2);
    curl_close($curl2);

    return $response;
}

function kirim_group($key, $id_group, $pesan)
{
    $curl2 = curl_init();
    curl_setopt_array(
        $curl2,
        array(
            CURLOPT_URL => 'http://31.97.179.141/:3000/api/sendMessageGroup',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'apiKey=' . $key . '&id_group=' . $id_group . '&message=' . $pesan,
        )
    );
    $response = curl_exec($curl2);
    curl_close($curl2);
    return $response;
}
function kirim_media($key, $hp, $nama_file, $as_doc, $capt)
{
    $curl2 = curl_init();
    curl_setopt_array(
        $curl2,
        array(
            CURLOPT_URL => 'http://31.97.179.141/:3000/api/sendMediaFromUrl',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'apiKey=' . $key . '&phone=' . $hp . '&url_file=' . $nama_file . '&as_document=' . $as_doc . '&caption=' . $capt,
        )
    );
    $response = curl_exec($curl2);
    curl_close($curl2);
    return $response;
}

function fetchApiGet($url, $token)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer " . $token,
        "Accept: application/json"
    ]);

    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo json_encode([
            'status' => 'error',
            'message' => curl_error($ch)
        ]);
        curl_close($ch);
        return;
    }
    curl_close($ch);

    $decoded = json_decode($result, true);
    return $decoded;
}
