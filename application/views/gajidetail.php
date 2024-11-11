    <?php
    include 'head.php';
    $a = 1;
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
                            Data Gaji Guru/Karyawan
                            <a class="btn btn-primary btn-sm float-end tbl-confirm" value="Fitur ini akan men-generate ulang semua data yang sudah ada" href="<?= base_url('gaji/regenerate/' . $idgaji) ?>"><i class="bx bx-refresh"></i> Generate Ulang</a>
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
                                        <th>Gol</th>
                                        <th>SIK</th>
                                        <th>Ijazah</th>
                                        <th>TMT</th>
                                        <!-- <th>Gaji Pokok</th>
                                        <th>T. Fungsional</th>
                                        <th>T. Kinerja</th>
                                        <th>T. BPJS</th>
                                        <th>T. Struktural</th>
                                        <th>T. Walas</th>
                                        <th>T. Penyesuaian</th> -->
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
        <div class="modal-dialog" role="document">
            <form action="<?= base_url('gaji/tambah') ?>" method="post">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel1">Rincian Nominal Gaji</h5>
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <table class="table table-sm">
                                <tr>
                                    <td>Nama Guru</td>
                                    <td><b id="nama"></b></td>
                                </tr>
                                <tr>
                                    <td>Gaji Pokok</td>
                                    <td><b id="gapok"></b></td>
                                </tr>
                                <tr>
                                    <td>Tunjangan Fungsional</td>
                                    <td><b id="fungsional"></b></td>
                                </tr>
                                <tr>
                                    <td>Tunjangan Kinerja</td>
                                    <td><b id="kinerja"></b></td>
                                </tr>
                                <tr>
                                    <td>Tunjangan BPJS</td>
                                    <td><b id="bpjs"></b></td>
                                </tr>
                                <tr>
                                    <td>Tunjangan Struktural</td>
                                    <td><b id="struktural"></b></td>
                                </tr>
                                <tr>
                                    <td>Tunjangan Wali Kelas</td>
                                    <td><b id="walas"></b></td>
                                </tr>
                                <tr>
                                    <td>Tunjangan Penyesuaian</td>
                                    <td><b id="penyesuaian"></b></td>
                                </tr>
                                <tr>
                                    <th>TOTAL GAJI</th>
                                    <th><b id="total"></b></th>
                                </tr>
                            </table>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
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
                    "url": "<?= base_url('gaji/detail2/' . $idgaji); ?>",
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
                        "data": 5
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
                            return `
                                <b class="text-bold text-primary">${formatRupiah(total)}</b>
                            `;
                        }

                    },
                    {
                        "render": function(data, type, row) {
                            return `
                                <button class="btn btn-xs btn-warning btn-detail" data-nama="${row[2]}" data-gapok="${formatRupiah(row[9])}" data-fungsional="${formatRupiah(row[10])}" data-kinerja="${formatRupiah(row[11])}" data-bpjs="${formatRupiah(row[13])}" data-struktural="${formatRupiah(row[12])}" data-walas="${formatRupiah(row[14])}" data-penyesuaian="${formatRupiah(row[15])}" data-total="${formatRupiah(row[16])} ">Rincian</button>
                            `;
                        }

                    },

                ],
                "pageLength": 10,
                "searchDelay": 500
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
            var total = $(this).data('total');

            $('#nama').text(nama);
            $('#gapok').text(gapok);
            $('#fungsional').text(fungsional);
            $('#kinerja').text(kinerja);
            $('#bpjs').text(bpjs);
            $('#walas').text(walas);
            $('#struktural').text(struktural);
            $('#penyesuaian').text(penyesuaian);
            $('#penyesuaian').text(penyesuaian);
            $('#total').text(total);

            $('#editModal').modal('show');
        });

        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(number);
        }
    </script>