    <?php include 'head.php';
    $a = 1;
    ?>
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-lg-6 mb-4 order-0">
                <div class="card">
                    <h5 class="card-header">
                        Tunjangan Tambahan
                        <button class="btn btn-sm btn-primary float-end" data-bs-toggle="modal"
                            data-bs-target="#tambahModal"><i class='bx bxs-plus-circle'></i> Tambah Data</button>
                    </h5>
                    <div class="table-responsive card-datatable">
                        <table class="table mb-10" id="table1">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Gaji</th>
                                    <th>Nominal</th>
                                    <th>#</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data as $data): ?>
                                    <tr>
                                        <td><?= $a++ ?></td>
                                        <td><?= $data->nama ?></td>
                                        <td><?= rupiah($data->nominal) ?></td>
                                        <td>
                                            <div class="dropdown">
                                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                    <i class="bx bx-dots-vertical-rounded"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item edit-btn" data-id="<?= $data->id_tambahan ?>" data-nominal="<?= $data->nominal ?>" data-nama="<?= $data->nama ?>"><i class="bx bx-edit-alt me-1"></i> Edit</a>

                                                    <a class="dropdown-item tombol-hapus" href="<?= base_url('tambahan/hapus/' . $data->id_tambahan) ?>"><i class="bx bx-trash me-1"></i> Delete</a>
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
            <div class="col-lg-6 mb-4 order-0">
                <div class="card">
                    <h5 class="card-header">
                        Daftar List Gaji Tambahan
                    </h5>
                    <div class="table-responsive card-datatable">
                        <table class="table table-sm" id="">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Keterangan</th>
                                    <th>Tahun Pelajaran</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($gaji as $gaji): ?>
                                    <tr>
                                        <td><?= $a++; ?></td>
                                        <td><?= $gaji->status == 'kunci' ? "<span class='text-warning bx bxs-key'></span>" : '' ?> <?= bulan($gaji->bulan) . ' ' . $gaji->tahun ?></td>
                                        <td><?= $gaji->tapel ?></td>
                                        <td>
                                            <button class="btn btn-primary btn-sm" onclick="window.location='<?= base_url('tambahan/cek/' . $gaji->gaji_id) ?>'">Cek</button>
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

    <div class="modal fade" id="tambahModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form action="<?= base_url('tambahan/tambah') ?>" method="post">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel1">Tambah Data Tunjangan Tambahan</h5>
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-2">
                            <label class="col-sm-2 col-md-2 col-form-label" for="a_nama">Nama </label>
                            <div class="col-sm-10 col-md-10">
                                <input type="text" class="form-control" id="a_nama" name="nama" placeholder="Nama Tunjangan Tambahan" required />
                            </div>
                        </div>
                        <div class="row mb-2">
                            <label class="col-sm-2 col-md-2 col-form-label" for="a_nominal">Nominal</label>
                            <div class="col-sm-10 col-md-10">
                                <input type="text" class="form-control uang" id="a_nominal" name="nominal" placeholder="Nominal Besaran" required />
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
            <form action="<?= base_url('tambahan/edit') ?>" method="post">
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
                            <label class="col-sm-2 col-form-label" for="nama">Nama</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="nama" name="nama" placeholder="Nama Tunjangan" required />
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

    <!-- / Content -->
    <?php include 'foot.php' ?>

    <script src="<?= base_url(); ?>assets/js/jquery.mask.min.js"></script>
    <script>
        $(document).ready(function() {

            $('.edit-btn').on('click', function() {

                var id = $(this).data('id');
                var nama = $(this).data('nama');
                var nominal = $(this).data('nominal');

                $('#id').val(id);
                $('#nama').val(nama);
                $('#nominal').val(nominal);

                $('#editModal').modal('show');
            });

            $('#table1').DataTable();
            $('.uang').mask('000.000.000.000', {
                reverse: true
            });

        });
    </script>