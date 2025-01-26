    <?php include 'head.php' ?>
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-lg-7 mb-4 order-0">
                <div class="card">
                    <h5 class="card-header">
                        Tunjangan Kinerja
                        <button class="btn btn-sm btn-primary float-end" data-bs-toggle="modal"
                            data-bs-target="#tambahModal"><i class='bx bxs-plus-circle'></i> Tambah Data</button>
                    </h5>
                    <div class="table-responsive card-datatable">
                        <table class="table mb-10 table1" id="">
                            <thead>
                                <tr>
                                    <th>Masa Kerja</th>
                                    <th>Besaran</th>
                                    <th>Nominal Tukin</th>
                                    <th>#</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data as $data): ?>
                                    <tr>
                                        <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong><?= $data->masa_kerja ?> tahun</strong></td>
                                        <td><?= rupiah($data->nominal) ?></td>
                                        <!-- <td><?= rupiah($data->nominal * 24) ?></td> -->
                                        <td>(x jumlah kehadiran)</td>
                                        <td>
                                            <div class="dropdown">
                                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                    <i class="bx bx-dots-vertical-rounded"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item edit-btn" href="javascript:void(0);" data-bs-toggle="modal" data-id="<?= $data->kinerja_id ?>" data-nominal="<?= $data->nominal ?>" data-masa_kerja="<?= $data->masa_kerja ?>" data-bs-target="#editModal"><i class="bx bx-edit-alt me-1"></i> Edit</a>

                                                    <a class="dropdown-item tombol-hapus" href="<?= base_url('kinerja/hapus/' . $data->kinerja_id) ?>"><i class="bx bx-trash me-1"></i> Delete</a>
                                                </div>
                                            </div>
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
            <div class="col-lg-5 mb-4 order-0">
                <div class="card">
                    <h5 class="card-header">
                        Data Kehadiran
                        <button class="btn btn-sm btn-primary float-end" data-bs-toggle="modal"
                            data-bs-target="#tambahKehadiran"><i class='bx bxs-plus-circle'></i> Tambah Data</button>
                    </h5>
                    <div class="table-responsive card-datatable">
                        <table class="table mb-10 table1" id="">
                            <thead>
                                <tr>
                                    <th>Bulan</th>
                                    <th>#</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($absen as $absen): ?>
                                    <tr>
                                        <td><?= bulan($absen->bulan) . ' ' . $absen->tahun ?></td>
                                        <td>
                                            <button class="btn btn-xs btn-info btn-cek" data-id="<?= $absen->id ?>">Cek</button>
                                            <button class="btn btn-xs btn-danger btn-refresh" data-id="<?= $absen->id ?>">Refresh</button>
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
            <div class="col-lg-12 mb-4 order-0">
                <div class="card">
                    <div class="table-responsive card-datatable">
                        <table class="table mb-10" id="table-kehadiran">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Bulan</th>
                                    <th>Nama</th>
                                    <th>Satminkal</th>
                                    <th>TMT</th>
                                    <th>Jml Jam</th>
                                    <th>Nominal</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                        <br>
                    </div>
                </div>
                <!-- Bootstrap Table with Caption -->

            </div>

        </div>

    </div>

    <div class="modal fade" id="tambahModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form action="<?= base_url('kinerja/tambah') ?>" method="post">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel1">Tambah Data Tunjangan</h5>
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-2">
                            <label class="col-sm-2 col-form-label" for="a_masa_kerja">Masa kerja</label>
                            <div class="col-sm-10">
                                <input type="number" class="form-control" id="a_masa_kerja" name="masa_kerja" placeholder="Masa Kerja Karyawan" required />
                            </div>
                        </div>
                        <div class="row mb-2">
                            <label class="col-sm-2 col-form-label" for="a_nominal">Besaran</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control uang" id="a_nominal" name="nominal" placeholder="Nominal Gapok" required />
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            Close
                        </button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form action="<?= base_url('kinerja/edit') ?>" method="post">
                <input type="hidden" name="id" id="id">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel1">Edit Data Tunjangan</h5>
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-2">
                            <label class="col-sm-2 col-form-label" for="masa_kerja">Masa kerja</label>
                            <div class="col-sm-10">
                                <input type="number" class="form-control" id="masa_kerja" name="masa_kerja" placeholder="Masa Kerja Karyawan" required />
                            </div>
                        </div>
                        <div class="row mb-2">
                            <label class="col-sm-2 col-form-label" for="nominal">Besaran</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control uang" id="nominal" name="nominal" placeholder="Nominal" required />
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            Close
                        </button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="tambahKehadiran" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form action="<?= base_url('kinerja/buatBaru') ?>" method="post">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel1">Tambah Data Kehadiran</h5>
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">Bulan</label>
                                <select name="bulan" class="form-select" required>
                                    <option value=""> -pilih- </option>
                                    <?php for ($i = 1; $i <= 12; $i++): ?>
                                        <option value="<?= $i ?>"><?= bulan($i) ?></option>
                                    <?php endfor ?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="tahun" class="form-label">Tahun</label>
                                <input type="text" name="tahun" id="tahun" class="form-control" value="<?= date('Y') ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            Close
                        </button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- / Content -->
    <?php include 'foot.php' ?>

    <script src="<?= base_url(); ?>assets/js/jquery.mask.min.js"></script>
    <script>
        $(document).ready(function() {

            $('.edit-btn').on('click', function() {

                var id = $(this).data('id');
                var masa_kerja = $(this).data('masa_kerja');
                var nominal = $(this).data('nominal');

                $('#id').val(id);
                $('#masa_kerja').val(masa_kerja);
                $('#nominal').val(nominal);

                $('#editModal').modal('show');
            });

            $('.table1').DataTable();
            $('.uang').mask('000.000.000.000', {
                reverse: true
            });

        });
        $('.btn-cek').on('click', function() {
            var id = $(this).data('id');
            if ($.fn.DataTable.isDataTable('#table-kehadiran')) {
                $('#table-kehadiran').DataTable().destroy();
                $('#table-kehadiran tbody').empty();
            }
            showTable(id);
        })

        function showTable(params) {
            $('#table-kehadiran').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": '<?= base_url('kinerja/rincian') ?>',
                    "type": "POST",
                    "data": {
                        'id': params
                    }
                },
                "columns": [{
                        "data": null,
                        "render": function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1; // Nomor urut
                        }
                    },
                    {
                        "data": 6
                    },
                    {
                        "data": 1
                    },
                    {
                        "data": 8
                    },
                    {
                        "render": function(data, type, row, meta) {
                            return `${row[2]} tahun`;
                        }
                    },
                    {
                        "data": 3,
                        "render": function(data, type, row) {
                            return `<input type="text" class="form-control form-input" data-id="${row[5]}" value="${data}">`;
                        }
                    },
                    // {
                    //     "render": function(data, type, row, meta) {
                    //         return `<strong id='hasil-bagi-${row[5]}'>${(row[3] / 4)}</strong>`;
                    //     }
                    // },
                    {
                        "render": function(data, type, row, meta) {
                            return `<b id='hasil-honor-${row[5]}'>${formatRupiah(row[4])}</b>`;
                        }
                    }
                ],
                "pageLength": 10,
                "searchDelay": 500
            });
        }

        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(number);
        }

        $('#table-kehadiran').on('change', '.form-input', function() {
            var newValue = $(this).val(); // nilai baru dari input
            var id = $(this).data('id'); // id dari baris data

            $.ajax({
                url: '<?= base_url("kinerja/editJam") ?>', // endpoint untuk update data
                type: 'POST',
                data: {
                    id: id,
                    value: newValue
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status == 'ok') {
                        $(this).val(newValue);
                        $(`#hasil-honor-${id}`).text(formatRupiah(newValue * response.besaran));
                        $(`#hasil-bagi-${id}`).text(newValue / 4);
                    } else {
                        alert('Gagal mengupdate data');
                    }
                },
                error: function() {
                    alert('Terjadi kesalahan saat mengupdate data');
                }
            });
        });

        $('.btn-refresh').on('click', function() {
            var id = $(this).data('id');
            $.ajax({
                url: '<?= base_url("kinerja/refresh") ?>', // endpoint untuk update data
                type: 'POST',
                data: {
                    id: id
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status == 'ok') {
                        if ($.fn.DataTable.isDataTable('#table-kehadiran')) {
                            $('#table-kehadiran').DataTable().destroy();
                            $('#table-kehadiran').empty(); // Kosongkan elemen tabel
                        }
                        showTable(id);
                    } else {
                        alert('Gagal mengupdate data');
                    }
                },
                error: function() {
                    alert('Terjadi kesalahan saat mengupdate data');
                }
            });
        })
    </script>