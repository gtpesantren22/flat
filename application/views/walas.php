    <?php include 'head.php' ?>
    <link rel="stylesheet" href="<?= base_url('assets/vendor/libs/select2/select2.css') ?>">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Card Atur Nominal per Lembaga -->
        <div class="row mb-4">
            <div class="col-lg-12">
                <div class="card">
                    <h5 class="card-header pb-2 text-warning"><i class='bx bx-coin-stack me-1'></i> Atur Nominal per Lembaga</h5>
                    <div class="card-body">
                        <form action="<?= base_url('walas/update_nominal_lembaga') ?>" method="post">
                            <?php if (empty($lembagaList)): ?>
                                <div class="text-center text-muted">Tidak ada data lembaga yang terdeteksi di tunjangan Wali Kelas.</div>
                            <?php else: ?>
                                <p class="text-muted small mb-3">Mengubah nominal di bawah ini akan memperbarui nominal seluruh guru Wali Kelas di lembaga tersebut secara otomatis.</p>
                                <div class="row">
                                    <?php foreach ($lembagaList as $lem): ?>
                                        <div class="col-md-3 col-sm-6 mb-3">
                                            <label class="form-label fw-bold"><?= $lem->nama ?></label>
                                            <div class="input-group">
                                                <span class="input-group-text">Rp</span>
                                                <input type="text" class="form-control uang" name="nominal[<?= $lem->satminkal ?>]" value="<?= (int)$lem->nominal ?>" placeholder="Nominal" required />
                                            </div>
                                        </div>
                                    <?php endforeach ?>
                                </div>
                                <div class="row mt-1">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary btn-sm"><i class='bx bx-save'></i> Simpan Perubahan</button>
                                    </div>
                                </div>
                            <?php endif ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12 mb-4 order-0">
                <div class="card">
                    <h5 class="card-header">
                        Tunjangan Wali Kelas
                        <button class="btn btn-sm btn-primary float-end" data-bs-toggle="modal"
                            data-bs-target="#tambahModal"><i class='bx bxs-plus-circle'></i> Tambah Data</button>
                        <a href="<?= base_url('walas/sync') ?>" class="btn btn-sm btn-info float-end me-2" id="syncBtn">
                            <i class='bx bx-refresh'></i> Sinkron Data
                        </a>
                    </h5>
                    <div class="table-responsive card-datatable">
                        <table class="table " id="table1">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Guru</th>
                                    <th>Satminkal</th>
                                    <th>Jabatan</th>
                                    <th>Nominal</th>
                                    <th>#</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1;
                                foreach ($data as $data): ?>
                                    <tr>
                                        <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong><?= $no++ ?></strong></td>
                                        <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong><?= $data->nmguru ?></strong></td>
                                        <td><?= $data->lembaga ?></td>
                                        <td><span class="badge bg-label-info">Wali Kelas</span></td>
                                        <td><?= rupiah($data->nominal) ?></td>
                                        <td>
                                            <div class="dropdown">
                                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                    <i class="bx bx-dots-vertical-rounded"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item edit-btn" href="javascript:void(0);" data-bs-toggle="modal" data-id="<?= $data->walas_id ?>" data-guru="<?= $data->guru_id ?>" data-nominal="<?= $data->nominal ?>" data-bs-target="#editModal"><i class="bx bx-edit-alt me-1"></i> Edit</a>

                                                    <a class="dropdown-item tombol-hapus" href="<?= base_url('walas/hapus/' . $data->walas_id) ?>"><i class="bx bx-trash me-1"></i> Delete</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>

                    </div>
                </div>
                <!-- Bootstrap Table with Caption -->

            </div>

        </div>

    </div>

    <div class="modal fade" id="tambahModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form action="<?= base_url('walas/tambah') ?>" method="post">
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
                            <label class="col-sm-2 col-form-label" for="a_guru">Guru</label>
                            <div class="col-sm-10">
                                <select class="select2 form-select form-select-lg" id="a_guru" name="guru" required>
                                    <option value=""> -pilih- </option>
                                    <?php foreach ($guruOpt as $guru): ?>
                                        <option value="<?= $guru->guru_id ?>"><?= $guru->nama ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <label class="col-sm-2 col-form-label" for="a_nominal">Nominal</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control uang" id="a_nominal" name="nominal" placeholder="Nominal Tunjangan" required />
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
            <form action="<?= base_url('walas/edit') ?>" method="post">
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
                            <label class="col-sm-2 col-form-label" for="guru">Guru</label>
                            <div class="col-sm-10">
                                <select class="select2 form-select form-select-lg" id="guru" name="guru" required>
                                    <option value=""> -pilih- </option>
                                    <?php foreach ($guruOpt as $guru): ?>
                                        <option value="<?= $guru->guru_id ?>"><?= $guru->nama ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <label class="col-sm-2 col-form-label" for="nominal">Nominal</label>
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
    <script src="<?= base_url(); ?>assets/vendor/libs/select2/select2.js"></script>
    <script src="<?= base_url(); ?>assets/vendor/libs/select2/forms-selects.js"></script>
    <script>
        $(document).ready(function() {

            $('.edit-btn').on('click', function() {

                var id = $(this).data('id');
                var guru = $(this).data('guru');
                var nominal = $(this).data('nominal');

                $('#id').val(id);
                $('#guru').val(guru).change();
                $('#nominal').val(nominal);

                $('#editModal').modal('show');
            });

            $('#table1').DataTable();
            $('.uang').mask('000.000.000.000', {
                reverse: true
            });

            $('#syncBtn').on('click', function(e) {
                e.preventDefault();
                var url = $(this).attr('href');
                Swal.fire({
                    title: 'Sinkronisasi Tunjangan Walas?',
                    text: "Sistem akan mengambil semua data Guru yang bertugas sebagai Wali Kelas di tabel registrasi.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Sinkronkan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.value) {
                        Swal.fire({
                            title: 'Mensinkronkan...',
                            text: 'Harap tunggu sebentar',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        window.location.href = url;
                    }
                });
            });

        });
    </script>