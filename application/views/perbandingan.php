    <?php include 'head.php';
    $no = 1; ?>
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-lg-12 mb-4 order-0">
                <div class="card">
                    <h5 class="card-header">
                        Data Perbandingan Honor
                    </h5>
                    <div class="table-responsive card-datatable">
                        <table class="table mb-10 table-hover" id="table1">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama</th>
                                    <th>SIK</th>
                                    <th>Jabatan Lama</th>
                                    <th>Jabatan Baru</th>
                                    <th>Nominal Sebelum</th>
                                    <th>Nominal Flat</th>
                                    <th>Selisih</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($hasil as $data):
                                    $selisih = $data['total'] - $data['sebelum'];
                                ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td class="click-nama" data-id="<?= $data['id'] ?>"><?= $data['nama'] ?></td>
                                        <td><?= $data['sik'] ?></td>
                                        <td><?= $data['jabatan_old'] ?></td>
                                        <td><?= $data['jabatan'] ?></td>
                                        <td><?= rupiah($data['sebelum']) ?></td>
                                        <td><?= rupiah($data['total']) ?></td>
                                        <td>
                                            <?php if ($data['total'] > $data['sebelum']) { ?>
                                                <b class="text-success"><i class="bx bx-plus"></i><?= number_format($selisih) ?> <i class='bx bxs-up-arrow-alt'></i></b>
                                            <?php } elseif ($data['total'] < $data['sebelum']) { ?>
                                                <b class="text-danger"><?= number_format($selisih) ?> <i class='bx bxs-down-arrow-alt'></i></b>
                                            <?php } else { ?>
                                                <b><?= rupiah($selisih) ?></b>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <?php if ($data['total'] < $data['sebelum']) { ?>
                                                <!-- <form action="<?= base_url('perbandingan/sesuaikan') ?>" method="post">
                                                    <input type="hidden" name="guru_id" value="<?= $data['guru_id'] ?>">
                                                    <input type="hidden" name="flat" value="<?= $data['total'] ?>">
                                                    <input type="hidden" name="sebelum" value="<?= $data['sebelum'] ?>">
                                                    <button class="btn btn-sm btn-primary">Sesuaikan</button>
                                                </form> -->
                                            <?php }  ?>
                                            <input type="text" class="form-control form-input uang" data-id="<?= $data['guru_id'] ?>" value="<?= $data['penyesuaian'] ?>">
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                        <br>
                    </div>
                </div>
                <!-- Bootstrap Table with Caption -->

            </div>
        </div>

    </div>


    <!-- / Content -->
    <?php include 'foot.php' ?>
    <script src="<?= base_url(); ?>assets/js/jquery.mask.min.js"></script>

    <script>
        $('.uang').mask('000.000.000.000', {
            reverse: true
        });
        $(document).ready(function() {

            var table = $('#table1').DataTable();

            // Event klik pada baris tabel untuk toggle card
            $('#table1 tbody').on('click', '.click-nama', function() {
                var tr = $(this); // Baris yang diklik
                var row = table.row(tr);
                var id = tr.data('id'); // Ambil ID dari atribut data-id

                if (row.child.isShown()) {
                    // Tutup card jika sudah ada
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    // Tambahkan card dengan konten bawaan template
                    $.ajax({
                        url: '<?= base_url('perbandingan/detail') ?>', // Endpoint untuk ambil data
                        type: 'POST',
                        data: {
                            id: id
                        },
                        dataType: 'json',
                        success: function(data) {
                            // var detail = JSON.parse(data); // Parse data JSON

                            // Tambahkan card dengan data dari server
                            row.child(`
                                <div class="card shadow-none bg-transparent border border-primary mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title">${data.nama}</h5>
                                        <div class='row'>
                                            <div class='col-6'>
                                                <div class='table-responsive text-nowrap'>
                                                    <table class='table table-sm table-borderless'>
                                                        <tr>
                                                            <td>Satminkal</td>
                                                            <td>${data.satminkal}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Jabatan</td>
                                                            <td>${data.jabatan}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Golongan</td>
                                                            <td>${data.golongan}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>SIK</td>
                                                            <td>${data.sik}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Ijazah</td>
                                                            <td>${data.ijazah}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>TMT</td>
                                                            <td>${data.tmt} (${data.masa} tahun)</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Ket</td>
                                                            <td>${data.ket}</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class='col-6'>
                                                <div class='table-responsive text-nowrap'>
                                                    <table class='table table-sm table-borderless'>
                                                        <tr>
                                                            <td>Gapok/Honor</td>
                                                            <td>${formatRupiah(data.gapok)}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>T. Fungsional</td>
                                                            <td>${formatRupiah(data.fungsional)}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>T. Kinerja</td>
                                                            <td>${formatRupiah(data.kinerja)}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>T. Struktural</td>
                                                            <td>${formatRupiah(data.struktural)}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>T. BPJS</td>
                                                            <td>${formatRupiah(data.bpjs)}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>T. Wali Kelas</td>
                                                            <td>${formatRupiah(data.walas)}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>T. Penyesuaian</td>
                                                            <td>${formatRupiah(data.penyesuaian)}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>T. Tambahan</td>
                                                            <td>${formatRupiah(data.tambahan)}</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `).show();
                            tr.addClass('shown');
                        },
                        error: function() {
                            alert('Gagal mengambil data.');
                        }
                    });
                }
            });

            // $('#table1').DataTable();
        });

        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(number);
        }

        $('#table1').on('change', '.form-input', function() {
            var newValue = $(this).val(); // nilai baru dari input
            var id = $(this).data('id'); // id dari baris data

            $.ajax({
                url: '<?= base_url("perbandingan/editPenyesuaian") ?>', // endpoint untuk update data
                type: 'POST',
                data: {
                    id: id,
                    value: newValue
                },
                dataType: 'json',
                success: function(response) {
                    console.log('update Ok');
                },
                error: function() {
                    alert('Terjadi kesalahan saat mengupdate data');
                    console.error();
                }
            });
        });
    </script>