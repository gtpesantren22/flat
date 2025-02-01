<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembayaran</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f3f4f6;
            /* Latar belakang halaman */
        }

        .struk-container {
            position: relative;
            border-radius: 0.5rem;
            overflow: hidden;
            width: 384px;
            /* Sesuaikan dengan ukuran struk */
        }

        .background-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            /* Pastikan gambar menutupi seluruh area */
            z-index: 1;
        }

        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.8);
            /* Overlay putih semi-transparan */
            z-index: 2;
        }

        .content {
            position: relative;
            z-index: 3;
            /* Letakkan di atas overlay dan gambar */
            padding: 1.5rem;
            /* Sesuaikan dengan padding yang diinginkan */
        }
    </style>
</head>


<div id="capture">

    <body class="flex justify-center items-center min-h-screen">
        <div class="struk-container">
            <!-- Gambar Latar Belakang -->
            <img src="<?= base_url('assets/img/backgrounds/watermark3.jpg') ?>" alt="Background" class="background-image">

            <!-- Overlay untuk memudahkan membaca teks -->
            <div class="overlay"></div>

            <!-- Konten Struk -->
            <div class="content">
                <!-- Header -->
                <div class="text-center mb-4">
                    <h1 class="text-xl font-bold text-blue-600">SLIP GAJI</h1>
                    <h1 class="text-md font-bold text-blue-600">PP. DARUL LUGHAH WAL KAROMAH</h1>
                    <p class="text-sm text-gray-600">Jl. Pandjaitan No. 12 Sidomukti-Kraksaan-Probolinggo</p>
                </div>


                <!-- Informasi Transaksi -->
                <div class="border-b border-gray-300 pb-3 mb-4">
                    <h3 class="font-semibold text-gray-800 mb-2">Kpd.Yth</h3>
                    <table class="w-full">
                        <tr>
                            <td class="text-sm font-medium text-gray-700">Nama</td>
                            <td class="text-sm">: <?= $data->nama ?></td>
                        </tr>
                        <tr>
                            <td class="text-sm font-medium text-gray-700">Email</td>
                            <td class="text-sm">: <?= $data->email ?></td>
                        </tr>
                        <tr>
                            <td class="text-sm font-medium text-gray-700">No. Rekening</td>
                            <td class="text-sm">: <?= $data->rekening ?></td>
                        </tr>
                        <tr>
                            <td class="text-sm font-medium text-gray-700">Nominal</td>
                            <td class="text-sm">: <?= rupiah(0) ?></td>
                        </tr>
                        <tr>
                            <td class="text-sm font-medium text-gray-700">Ket</td>
                            <td class="text-sm">: HR Flat Januari 2025</td>
                        </tr>
                    </table>
                </div>

                <!-- Detail Transaksi -->
                <div class="border-b border-gray-300 pb-3 mb-4">
                    <h3 class="font-semibold text-gray-800 mb-2">Rincian Honor</h3>
                    <table class="w-full">
                        <tr>
                            <td class="text-sm font-medium text-gray-700">Gaji Pokok</td>
                            <td class="text-sm">: <?= rupiah($data->gapok) ?></td>
                        </tr>
                        <tr>
                            <td class="text-sm font-medium text-gray-700">T. Fungsional</td>
                            <td class="text-sm">: <?= rupiah($data->fungsional) ?></td>
                        </tr>
                        <tr>
                            <td class="text-sm font-medium text-gray-700">T. Kinerja</td>
                            <td class="text-sm">: <?= rupiah($data->kinerja) ?></td>
                        </tr>
                        <tr>
                            <td class="text-sm font-medium text-gray-700">T. BPJS</td>
                            <td class="text-sm">: <?= rupiah($data->bpjs) ?></td>
                        </tr>
                        <tr>
                            <td class="text-sm font-medium text-gray-700">T. STRUKTURAL</td>
                            <td class="text-sm">: <?= rupiah($data->struktural) ?></td>
                        </tr>
                        <tr>
                            <td class="text-sm font-medium text-gray-700">T. WALI KELAS</td>
                            <td class="text-sm">: <?= rupiah($data->walas) ?></td>
                        </tr>
                        <tr>
                            <td class="text-sm font-medium text-gray-700">T. PENYESUAIAN</td>
                            <td class="text-sm">: <?= rupiah($data->penyesuaian) ?></td>
                        </tr>
                    </table>
                </div>

                <!-- Rekening Sumber -->
                <div class="border-b border-gray-300 pb-3 mb-4">
                    <h3 class="font-semibold text-gray-800 mb-2">Rincian Potongan</h3>
                    <table class="w-full">
                        <tr>
                            <td class="text-sm font-medium text-gray-700">BPJS</td>
                            <td class="text-sm">: RP. 50,000</td>
                        </tr>
                        <tr>
                            <td class="text-sm font-medium text-gray-700">Infaq TPP</td>
                            <td class="text-sm">: </td>
                        </tr>
                        <tr>
                            <td class="text-sm font-medium text-gray-700">Insijam</td>
                            <td class="text-sm">: </td>
                        </tr>
                        <tr>
                            <td class="text-sm font-medium text-gray-700">Kalender</td>
                            <td class="text-sm">: </td>
                        </tr>
                        <tr>
                            <td class="text-sm font-medium text-gray-700">Koperasi/Cicilan</td>
                            <td class="text-sm">: </td>
                        </tr>
                        <tr>
                            <td class="text-sm font-medium text-gray-700">Lain-lain</td>
                            <td class="text-sm">: </td>
                        </tr>
                        <tr>
                            <td class="text-sm font-medium text-gray-700">Pinjaman Bank</td>
                            <td class="text-sm">: </td>
                        </tr>
                        <tr>
                            <td class="text-sm font-medium text-gray-700">Pulsa</td>
                            <td class="text-sm">: </td>
                        </tr>
                        <tr>
                            <td class="text-sm font-medium text-gray-700">SIMPOK</td>
                            <td class="text-sm">: </td>
                        </tr>
                        <tr>
                            <td class="text-sm font-medium text-gray-700">SIMWA</td>
                            <td class="text-sm">: </td>
                        </tr>
                        <tr>
                            <td class="text-sm font-medium text-gray-700">Tabungan Wajib</td>
                            <td class="text-sm">: Rp. 50,000</td>
                        </tr>
                        <tr>
                            <td class="text-sm font-medium text-gray-700">Verval SIMPATIKA</td>
                            <td class="text-sm">: </td>
                        </tr>
                        <tr>
                            <td class="text-sm font-medium text-gray-700">Verval TPP</td>
                            <td class="text-sm">: </td>
                        </tr>
                    </table>
                </div>

                <!-- Footer -->
                <div class="text-center text-sm text-gray-600">
                    <p class="mb-2">Semoga Barokah</p>
                    <p class="text-xs text-gray-500">Terimaksih atas pengabdiannya.</p>
                </div>
            </div>
        </div>
    </body>
</div>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    window.onload = function() {
        setTimeout(() => { // Beri jeda agar halaman sepenuhnya dimuat
            html2canvas(document.querySelector("#capture")).then(canvas => {
                let imageData = canvas.toDataURL("image/png"); // Ubah canvas ke base64
                $.ajax({
                    url: "<?= base_url('welcome/saveImage') ?>",
                    type: "POST",
                    data: {
                        image: imageData
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.status === "success") {
                            console.log("Screenshot disimpan:", response.file);
                        } else {
                            console.error("Gagal menyimpan gambar");
                        }
                    },
                    error: function() {
                        console.error("Terjadi kesalahan AJAX");
                    }
                });
            });
        }, 1000);
    };
</script>


</html>