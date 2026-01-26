    <?php include 'head.php' ?>
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-lg-12 mb-4 order-0">
                <div class="card">
                    <h5 class="card-header">
                        Data Nominal Perbandingan
                        <button onclick="window.location.href='<?= base_url() ?>/pembanding/reload'" class="btn btn-sm btn-primary float-end"><i class='bx bxs-plus-circle'></i> Reload Data</button>
                    </h5>
                    <div class="table-responsive card-datatable">
                        <table class="table mb-10" id="table1">
                            <thead>
                                <tr>
                                    <th>Nama Guru</th>
                                    <th>Nama Nominal</th>
                                    <th>#</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data as $data): ?>
                                    <tr>
                                        <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong><?= $data->nama ?></strong></td>
                                        <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong><?= rupiah($data->nominal) ?></strong></td>
                                        <td>
                                            <div class="dropdown">
                                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                    <i class="bx bx-dots-vertical-rounded"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item edit-btn" href="javascript:void(0);" data-bs-toggle="modal" data-id="<?= $data->id ?>" data-nama="<?= $data->nama ?>" data-nominal="<?= $data->nominal ?>" data-bs-target="#editModal"><i class="bx bx-edit-alt me-1"></i> Edit</a>
                                                    <a class="dropdown-item tombol-hapus" href="<?= base_url('pembanding/hapus/' . $data->id) ?>"><i class="bx bx-trash me-1"></i> Delete</a>
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

        </div>

    </div>

    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form action="<?= base_url('pembanding/edit') ?>" method="post">
                <input type="hidden" name="id" id="id">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel1">Edit Data Pembanding</h5>
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">Nama</label>
                                <input type="text" id="nama" name="nama" id="nameBasic" class="form-control" placeholder="Enter Name" required />
                            </div>
                            <div class="col mb-3">
                                <label for="nomBasic" class="form-label">Nominal</label>
                                <input type="text" id="nominal" name="nominal" id="nomBasic" class="form-control uang" placeholder="Enter Nominal" required />
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

                $('#nama').val(nama);
                $('#nominal').val(nominal);
                $('#id').val(id);
                $('#editModal').modal('show');
            });

            $('#table1').DataTable();

            $('.uang').mask('000.000.000.000', {
                reverse: true
            });
        });
    </script>