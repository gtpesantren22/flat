    <?php include 'head.php';
    $no = 1; ?>
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-lg-12 mb-4 order-0">
                <div class="card">
                    <h5 class="card-header">
                        Data Potongan Gaji
                        <button class="btn btn-primary btn-sm float-end" data-bs-toggle="modal" data-bs-target="#tambahModal"><i class="bx bx-plus-circle"></i> Buat Data</button>
                    </h5>
                    <div class="table-responsive card-datatable">
                        <table class="table mb-10" id="table1">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Bulan</th>
                                    <th>#</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data as $data): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= bulan($data->bulan) . ' ' . $data->tahun ?></td>
                                        <td>
                                            <a class="btn btn-primary btn-xs" href="<?= base_url('potongan/detail/' . $data->potongan_id) ?>">Detail</a>
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
            <form action="<?= base_url('potongan/tambah') ?>" method="post">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel1">Tambah Data Potongan</h5>
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
                            <div class="col mb-3">
                                <label for="" class="form-label">Tahun</label>
                                <input type="text" name="tahun" class="form-control" value="<?= date('Y') ?>" required>
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

    <script>
        $(document).ready(function() {

            $('.edit-btn').on('click', function() {
                var id = $(this).data('id');
                var nama = $(this).data('nama');

                $('#nama').val(nama);
                $('#id').val(id);
                $('#editModal').modal('show');
            });

            $('#table1').DataTable();
        });
    </script>