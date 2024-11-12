    <?php include 'head.php' ?>
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-lg-12 mb-4 order-0">
                <div class="card">
                    <h5 class="card-header">
                        Tunjangan Wali Kelas
                        <button class="btn btn-sm btn-primary float-end" data-bs-toggle="modal"
                            data-bs-target="#tambahModal"><i class='bx bxs-plus-circle'></i> Tambah Data</button>
                    </h5>
                    <div class="table-responsive card-datatable">
                        <table class="table " id="table1">
                            <thead>
                                <tr>
                                    <th>Satminkal/Lembaga</th>
                                    <th>Nominal</th>
                                    <th>#</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data as $data): ?>
                                    <tr>
                                        <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong><?= $data->nmsatminkal ?></strong></td>
                                        <td><?= rupiah($data->nominal) ?></td>
                                        <td>
                                            <div class="dropdown">
                                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                    <i class="bx bx-dots-vertical-rounded"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item edit-btn" href="javascript:void(0);" data-bs-toggle="modal" data-id="<?= $data->walas_id ?>" data-satminkal="<?= $data->satminkal_id ?>" data-nominal="<?= $data->nominal ?>" data-bs-target="#editModal"><i class="bx bx-edit-alt me-1"></i> Edit</a>

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
                            <label class="col-sm-2 col-form-label" for="a_satminkal">Lembaga</label>
                            <div class="col-sm-10">
                                <select class="form-select" data-allow-clear="true" id="a_satminkal" name="satminkal" required>
                                    <option value=""> -pilih- </option>
                                    <?php foreach ($satminkalOpt as $satminkal): ?>
                                        <option value="<?= $satminkal->id ?>"><?= $satminkal->nama ?></option>
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
                            <label class="col-sm-2 col-form-label" for="satminkal">Lembaga</label>
                            <div class="col-sm-10">
                                <select class="select2 form-select form-select-lg" id="satminkal" name="satminkal" required>
                                    <option value=""> -pilih- </option>
                                    <?php foreach ($satminkalOpt as $satminkal): ?>
                                        <option value="<?= $satminkal->id ?>"><?= $satminkal->nama ?></option>
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
    <script>
        $(document).ready(function() {

            $('.edit-btn').on('click', function() {

                var id = $(this).data('id');
                var satminkal = $(this).data('satminkal');
                var nominal = $(this).data('nominal');

                $('#id').val(id);
                $('#satminkal').val(satminkal).change();
                $('#nominal').val(nominal);
                $('#nominal').val(nominal);

                $('#editModal').modal('show');
            });

            $('#table1').DataTable();
            $('.uang').mask('000.000.000.000', {
                reverse: true
            });

        });
    </script>