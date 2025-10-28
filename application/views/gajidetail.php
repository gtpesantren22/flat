    <?php
    include 'head.php';
    $a = 1;
    $url = $datagaji->status == 'kunci' ? base_url('gaji/detail3/' . $datagaji->gaji_id) : base_url('gaji/detail2/' . $datagaji->gaji_id);
    ?>

    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-lg-12 mb-4 order-0">
                <div class="card">
                    <!-- <div class="card-header">
                        Data Gaji Guru/Karyawan
                    </div> -->
                    <div class="card-body">
                        <h5 class="card-title">
                            <?= $datagaji->status == 'kunci' ? "<span class='text-danger bx bxs-key'>locked</span> || " : '' ?> Data Gaji Guru/Karyawan
                            <a class="btn btn-outline-primary btn-sm float-end tbl-confirm" value="Fitur ini akan men-generate ulang semua data yang sudah ada" href="<?= base_url('gaji/regenerate/' . $idgaji) ?>"><i class="bx bx-refresh"></i> Generate Ulang</a>
                            <!-- <a class="btn btn-outline-danger btn-sm float-end tbl-confirm" value="Fitur ini akan mengunci dan mempermanenkan data" href="<?= base_url('gaji/kunci/' . $idgaji) ?>"><i class="bx bxs-key"></i> Kunci Data</a> -->
                            <button class="btn btn-outline-danger btn-sm float-end" data-bs-toggle="modal" data-bs-target="#modal-kunci"><i class="bx bxs-key"></i> Kunci Data</button>
                            <div class="btn-group float-end">
                                <button type="button" class="btn btn-outline-success btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="bx bx-spreadsheet"></i> Export to Excel</button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item tbl-confirm" value="Pastikan data gaji nya sudah dikunci terlebih dahulu" href="<?= base_url('gaji/exportGajiV2/' . $idgaji) ?>">Export Gaji</a></li>
                                    <li><a class="dropdown-item" href="<?= base_url('gaji/exportPotongan/' . $potong->potongan_id) ?>">Export Potongan</a></li>
                                </ul>
                            </div>
                        </h5>
                        <p class="card-text"></p>
                        <div class="table-responsive card-table">
                            <table class="table table-sm" id="table1">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nama Guru</th>
                                        <th>Satminkal</th>
                                        <th>Jabatan</th>
                                        <th>Golongan</th>
                                        <th>SIK</th>
                                        <th>Ijazah</th>
                                        <th>TMT</th>
                                        <th>TOTAL</th>
                                        <th>#</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                        <!-- <button type="submit" class="btn btn-primary">Simpan</button> -->
                    </div>
                </div>
                <!-- Bootstrap Table with Caption -->

            </div>

        </div>

    </div>

    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <!-- <h5 class="modal-title" id="exampleModalLabel1">Rincian Nominal Gaji</h5> -->
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-3">
                            <table>
                                <tr>
                                    <td>Nama</td>
                                    <td>:</td>
                                    <td><b id="nama" class="text-primary"></b></td>
                                </tr>
                                <tr>
                                    <td>Lembaga</td>
                                    <td>:</td>
                                    <td><b id="lembaga" class="text-primary"></b></td>
                                </tr>
                                <tr>
                                    <td>Kriteria</td>
                                    <td>:</td>
                                    <td><b id="kriteria" class="text-primary"></b></td>
                                </tr>
                                <tr>
                                    <td>Gaji</td>
                                    <td>:</td>
                                    <td><b id="waktu" class="text-primary"></b></td>
                                </tr>
                            </table>
                            <hr>
                            <h5>Total akhir</h5>
                            <h3 id="total-akhir"></h3>
                            <hr>
                            <?php if ($datagaji->status != 'kunci'): ?>
                                <form action="<?= base_url("gaji/reloadGaji") ?>" method="post" class="form-update">
                                    <input type="hidden" name="guru_id" id="guru_id">
                                    <input type="hidden" name="gaji_id" value="<?= $idgaji ?>">
                                    <button type="submit" class="btn btn-outline-success">Refresh Data</button>
                                </form>
                            <?php endif ?>
                        </div>
                        <div class="col-md-5">
                            <table class="table table-sm">
                                <tr>
                                    <td class="text-bold text-primary" colspan="2">Rincian Gaji</td>
                                    <!-- <td class="text-bold text-primary"><b id="nama"></b></td> -->
                                </tr>
                                <tr>
                                    <td>
                                        <label for="" id="nama_gaji"></label>
                                    </td>
                                    <td><b id="gapok"></b></td>
                                </tr>
                                <tr>
                                    <td>
                                        Tunjangan Fungsional
                                    </td>
                                    <td><b id="fungsional"></b></td>
                                </tr>
                                <tr>
                                    <td>
                                        Tunjangan Kinerja
                                    </td>
                                    <td><b id="kinerja"></b></td>
                                </tr>
                                <tr>
                                    <td>
                                        Tunjangan BPJS
                                    </td>
                                    <td><b id="bpjs"></b></td>
                                </tr>
                                <tr>
                                    <td>
                                        Tunjangan Struktural
                                    </td>
                                    <td><b id="struktural"></b></td>
                                </tr>
                                <tr>
                                    <td>
                                        Tunjangan Wali Kelas
                                    </td>
                                    <td><b id="walas"></b></td>
                                </tr>
                                <tr>
                                    <td>
                                        Tunjangan Penyesuaian
                                    </td>
                                    <td><b id="penyesuaian"></b></td>
                                </tr>
                                <tr>
                                    <td>
                                        Tunjangan Tambahan
                                    </td>
                                    <td><b id="tambahan"></b></td>
                                </tr>
                                <tr>
                                    <th class="text-bold text-primary">TOTAL GAJI</th>
                                    <th class="text-bold text-primary"><b id="total"></b></th>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-4">
                            <div id="hasil-potong"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="modal fade" id="modal-kunci" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="backDropModalTitle">Kunci Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php if ($datagaji->status == 'kunci') { ?>
                        <div class="text-center">
                            <b class="text-danger">Data sudah terkunci</b><br>
                        </div>
                    <?php } else { ?>
                        <div class="text-center">
                            <b class="">Proses penguncian data</b><br>
                            <strong class="text-danger"><i class='bx bx-info-circle'></i> Pastikan data sudah valid sebelum terkuci!</strong><br><br>
                            <button type="button" class="btn btn-primary" id="proses-kunci" data-id="<?= $datagaji->gaji_id ?>"><span class="tf-icons bx bx-bolt-circle bx-18px me-2"></span>Lanjutkan Proses!</button>
                            <br>
                        </div>
                        <div id="view-hasil" class="mt-5"></div>
                    <?php } ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <!-- <button type="button" class="btn btn-primary">Save</button> -->
                </div>
            </form>
        </div>
    </div>

    <!-- / Content -->
    <?php include 'foot.php' ?>

    <script>
        $('document').ready(function() {

            $('#table1').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "<?= $url ?>",
                    "type": "POST",

                },
                "columns": [{
                        "data": null,
                        "render": function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1; // Nomor urut
                        }
                    },
                    {
                        "data": 2
                    },
                    {
                        "data": 3
                    },
                    {
                        "data": 4
                    },
                    {
                        "render": function(data, type, row, meta) {
                            return row[5] + '-' + row[17];
                        }
                    },
                    {
                        "data": 6
                    },
                    {
                        "data": 7
                    },
                    {
                        "render": function(data, type, row) {
                            var tmt = row[8];
                            var startYear = new Date(tmt).getFullYear();
                            var currentYear = new Date().getFullYear();
                            var difference = currentYear - startYear;
                            return tmt + `
                                <span class="badge bg-secondary">${difference} thn</span>
                            `;
                        }
                    },
                    {
                        "render": function(data, type, row) {
                            var total = row[16];
                            var potong = row[18];
                            return `
                                <b class="text-bold text-primary">${formatRupiah(total-potong)}</b>
                            `;
                        }

                    },
                    {
                        "render": function(data, type, row) {
                            return `
                                <button class="btn btn-xs btn-warning btn-detail" data-id="${row[1]}" data-nama="${row[2]}" data-satminkal="${row[3]}" data-sik="${row[6]}" data-gapok="${formatRupiah(row[9])}" data-fungsional="${formatRupiah(row[10])}" data-kinerja="${formatRupiah(row[11])}" data-bpjs="${formatRupiah(row[13])}" data-struktural="${formatRupiah(row[12])}" data-walas="${formatRupiah(row[14])}" data-penyesuaian="${formatRupiah(row[15])}" data-tambahan="${formatRupiah(row[20])}" data-total="${formatRupiah(row[16])}" data-guru_id="${row[19]}" data-kriteria="${row[21]}">Rincian</button>
                            `;
                        }

                    },

                ],
                "pageLength": 10,
                "searchDelay": 500
            });

            $(document).on('submit', '.form-update', function(e) {
                e.preventDefault(); // Mencegah form dari reload halaman

                var form = $(this); // Form yang dikirim
                var formData = form.serialize(); // Serialize data form
                var button = $(this).find("button[type='submit']");
                button.prop("disabled", true).text("Memproses...");

                // Eksekusi AJAX
                $.ajax({
                    url: form.attr('action'), // Mengambil URL dari atribut form
                    type: 'POST',
                    data: formData,
                    dataType: 'json', // Pastikan format respon JSON
                    success: function(response) {
                        if (response) {
                            // console.log(response)
                            button.prop("disabled", false).text("Refresh Data");

                            var total = response.total;
                            $('#gapok').text(formatRupiah(response.gapok));
                            $('#fungsional').text(formatRupiah(response.fungsional));
                            $('#kinerja').text(formatRupiah(response.kinerja));
                            $('#bpjs').text(formatRupiah(response.bpjs));
                            $('#walas').text(formatRupiah(response.walas));
                            $('#struktural').text(formatRupiah(response.struktural));
                            $('#penyesuaian').text(formatRupiah(response.penyesuaian));
                            $('#total').text(formatRupiah(response.total));

                            $.ajax({
                                type: "POST",
                                url: "<?= base_url('gaji/getPotongan') ?>",
                                data: formData,
                                dataType: 'json',
                                success: function(data) {
                                    // console.log(data);
                                    // let cleanedStr = total.replace(/[^\d]/g, '');
                                    let num = parseInt(total, 10);
                                    let total_akhir = parseInt(num, 10) - parseInt(data.total_potong, 10);
                                    $('#hasil-potong').empty();
                                    $('#lembaga').empty();
                                    $('#total-akhir').empty();
                                    $('#waktu').empty();
                                    $('#hasil-potong').html(data.hasil);
                                    $('#lembaga').text(data.lembaga);
                                    $('#total-akhir').text(formatRupiah(total_akhir));
                                    $('#waktu').text(data.bulan + ' ' + data.tahun);
                                },
                                error: function(xhr, status, error) {
                                    console.log(xhr.responseText);
                                }
                            })
                        } else {
                            alert('Respon kosong. Pastikan server mengembalikan data dengan benar.');
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('Error: ' + xhr.responseText);
                    }
                });
            });
        })
        $(document).on('click', '.btn-detail', function() {

            var nama = $(this).data('nama');
            var gapok = $(this).data('gapok');
            var fungsional = $(this).data('fungsional');
            var kinerja = $(this).data('kinerja');
            var bpjs = $(this).data('bpjs');
            var walas = $(this).data('walas');
            var struktural = $(this).data('struktural');
            var penyesuaian = $(this).data('penyesuaian');
            var tambahan = $(this).data('tambahan');
            var total = $(this).data('total');
            var guru_id = $(this).data('guru_id');
            var id = $(this).data('id');
            var sik = $(this).data('sik');
            var info = sik == 'PTY' ? 'Gaji Pokok' : 'Honor Insentif';
            var kriteria = $(this).data('kriteria');

            $('#nama').text(nama);
            $('#gapok').text(gapok);
            $('#fungsional').text(fungsional);
            $('#kinerja').text(kinerja);
            $('#bpjs').text(bpjs);
            $('#walas').text(walas);
            $('#struktural').text(struktural);
            $('#penyesuaian').text(penyesuaian);
            $('#tambahan').text(tambahan);
            $('#kriteria').text(kriteria);
            $('#total').text(total);
            $('#guru_id').val(guru_id);
            $('#nama_gaji').text(info);

            $('#editModal').modal('show');

            $.ajax({
                type: "POST",
                url: "<?= base_url('gaji/getPotongan') ?>",
                data: {
                    gaji_id: id,
                    guru_id: guru_id,
                },
                dataType: 'json',
                success: function(data) {
                    // console.log(data);
                    let cleanedStr = total.replace(/[^\d]/g, '');
                    let num = parseInt(cleanedStr, 10);
                    let total_akhir = parseInt(num, 10) - parseInt(data.total_potong, 10);
                    $('#hasil-potong').empty();
                    $('#lembaga').empty();
                    $('#total-akhir').empty();
                    $('#waktu').empty();
                    $('#hasil-potong').html(data.hasil);
                    $('#lembaga').text(data.lembaga);
                    $('#total-akhir').text(formatRupiah(total_akhir));
                    $('#waktu').text(data.bulan + ' ' + data.tahun);
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                }
            })
        });

        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(number);
        }

        $(document).on('click', '#proses-kunci', function() {
            // e.preventDefault();
            const id = $(this).data('id');

            var $button = $('#proses-kunci');

            $.ajax({
                url: '<?= base_url("gaji/getGajiRinci") ?>',
                type: 'POST',
                data: {
                    gaji_id: id,
                },
                dataType: 'json',
                success: function(response) {
                    const hasil = $('#view-hasil');
                    let berhasil = 0;
                    let gagal = 0;
                    let persen = 0;
                    let errorMessages = [];

                    $button.prop('disabled', true);
                    $button.text('Processing...');

                    function updateProgress() {
                        const total = Number(response.total); // Konversi ke number
                        if (isNaN(total) || total === 0) {
                            persen = 0; // Jika total tidak valid atau 0, set persen ke 0
                        } else {
                            // Hitung persentase
                            processed = berhasil + gagal; // Total yang sudah diproses
                            persen = (processed / total) * 100;
                        }

                        const errorList = errorMessages.length > 0 ?
                            `<ul class="text-start mt-2 text-danger" style="font-size: 0.9rem;">${errorMessages.map(msg => `<li>${msg}</li>`).join('')}</ul>` :
                            '';

                        hasil.html(`
                            <div class="text-center">
                                <strong class="mb-2">Proses kunci data ...</strong>
                                <div class="progress mb-3" style="height: 17px;">
                                    <div class="progress-bar" role="progressbar" style="width: ${persen}%;" aria-valuenow="${persen}" aria-valuemin="0" aria-valuemax="100">${persen.toFixed(2)}%</div>
                                </div>
                            </div>
                            <strong class="mb-1">Total success : ${berhasil}</strong><br>
                            <strong class="mb-1 text-danger">Total error : ${gagal}</strong><br>
                             ${errorList}
                        `);
                    }

                    const ajaxRequests = response.data.map((item, index) => {
                        return new Promise((resolve) => {
                            setTimeout(() => {
                                $.ajax({
                                    url: '<?= base_url("gaji/updateGaji") ?>',
                                    type: 'POST',
                                    data: {
                                        gaji_id: item.gaji_id,
                                        guru_id: item.guru_id,
                                    },
                                    dataType: 'json',
                                    success: function(res) {
                                        if (res.status === 'success') {
                                            berhasil++;
                                        } else {
                                            gagal++;
                                            errorMessages.push(res.message || 'Gagal tanpa pesan');
                                        }
                                        updateProgress();
                                    },
                                    error: function() {
                                        gagal++;
                                        errorMessages.push(errorThrown || 'Error tidak diketahui');
                                        updateProgress();
                                    },
                                    complete: resolve // Menandai bahwa AJAX request selesai
                                });
                            }, index * 750); // Menambah jeda 500ms per request
                        });
                    });

                    Promise.all(ajaxRequests)
                        .then(function() {
                            // console.log('Semua permintaan AJAX selesai');
                            $.ajax({
                                url: '<?= base_url("gaji/updateKunci") ?>',
                                type: 'POST',
                                data: {
                                    gaji_id: id,
                                },
                                dataType: 'json',
                                success: function(response) {
                                    window.location.reload()
                                },
                                error: function() {
                                    gagal++; // Tambahkan 1 ke variabel gagal
                                    updateProgress(); // Perbarui tampilan progress bar dan hasil
                                    console.error('Failed to update data');
                                }
                            });
                        })
                        .catch(function(error) {
                            console.error('Ada permintaan AJAX yang gagal', error);
                        });
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                    console.log(status);
                    console.log(error);
                    // alert(xhr.responseText);
                }
            });
        })
    </script>