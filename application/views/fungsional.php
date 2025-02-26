    <?php include 'head.php' ?>
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-lg-12 mb-4 order-0">
                <div class="card">
                    <h5 class="card-header">
                        Tunjangan Fungsional
                        <button class="btn btn-sm btn-primary float-end" data-bs-toggle="modal"
                            data-bs-target="#tambahModal"><i class='bx bxs-plus-circle'></i> Tambah Data</button>
                    </h5>
                    <div class="table-responsive card-datatable">
                        <table class="table mb-10" id="table1">
                            <thead>
                                <tr>
                                    <th>Golongan</th>
                                    <th>Kategori</th>
                                    <!-- <th>Masa Kerja</th> -->
                                    <th>Nominal</th>
                                    <th>#</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data as $data): ?>
                                    <tr>
                                        <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong><?= $data->nmgolongan ?></strong></td>
                                        <td><?= $data->nmkategori ?></td>
                                        <!-- <td><?= $data->masa_kerja ?> tahun</td> -->
                                        <td><?= rupiah($data->nominal) ?></td>
                                        <td>
                                            <div class="dropdown">
                                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                    <i class="bx bx-dots-vertical-rounded"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item edit-btn" href="javascript:void(0);" data-bs-toggle="modal" data-id="<?= $data->fungsional_id ?>" data-kategori="<?= $data->kategori ?>" data-golongan="<?= $data->golongan_id ?>" data-nominal="<?= $data->nominal ?>" data-masa_kerja="<?= $data->masa_kerja ?>" data-bs-target="#editModal"><i class="bx bx-edit-alt me-1"></i> Edit</a>

                                                    <a class="dropdown-item tombol-hapus" href="<?= base_url('fungsional/hapus/' . $data->fungsional_id) ?>"><i class="bx bx-trash me-1"></i> Delete</a>
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

    <div class="modal fade" id="tambahModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form action="<?= base_url('fungsional/tambah') ?>" method="post">
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
                            <label class="col-sm-2 col-form-label" for="a_golongan">Golongan</label>
                            <div class="col-sm-10">
                                <select class="form-control" id="a_golongan" name="golongan" required>
                                    <option value=""> -pilih- </option>
                                    <?php foreach ($golonganOpt as $golongan): ?>
                                        <option value="<?= $golongan->id ?>"><?= $golongan->nama ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <label class="col-sm-2 col-form-label" for="a_kategori">Kategori</label>
                            <div class="col-sm-10">
                                <select class="form-control" id="a_kategori" name="kategori" required>
                                    <option value=""> -pilih- </option>
                                    <?php foreach ($kategoriOpt as $kategori): ?>
                                        <option value="<?= $kategori->id ?>"><?= $kategori->nama ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <label class="col-sm-2 col-form-label" for="a_masa_kerja">Masa kerja</label>
                            <div class="col-sm-10">
                                <input type="number" class="form-control" id="a_masa_kerja" name="masa_kerja" value="0" required />
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
            <form action="<?= base_url('fungsional/edit') ?>" method="post">
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
                            <label class="col-sm-2 col-form-label" for="golongan">Golongan</label>
                            <div class="col-sm-10">
                                <select class="form-control" id="golongan" name="golongan" required>
                                    <option value=""> -pilih- </option>
                                    <?php foreach ($golonganOpt as $golongan): ?>
                                        <option value="<?= $golongan->id ?>"><?= $golongan->nama ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <label class="col-sm-2 col-form-label" for="kategori">Kategori</label>
                            <div class="col-sm-10">
                                <select class="form-control" id="kategori" name="kategori" required>
                                    <option value=""> -pilih- </option>
                                    <?php foreach ($kategoriOpt as $kategori): ?>
                                        <option value="<?= $kategori->id ?>"><?= $kategori->nama ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <label class="col-sm-2 col-form-label" for="masa_kerja">Masa kerja</label>
                            <div class="col-sm-10">
                                <input type="number" class="form-control" id="masa_kerja" name="masa_kerja" value="0" required />
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
                var golongan = $(this).data('golongan');
                var kategori = $(this).data('kategori');
                var masa_kerja = $(this).data('masa_kerja');
                var nominal = $(this).data('nominal');

                $('#id').val(id);
                $('#golongan').val(golongan).change();
                $('#kategori').val(kategori);
                $('#masa_kerja').val(masa_kerja);
                $('#nominal').val(nominal);

                $('#editModal').modal('show');
            });
            $('#table1').DataTable();
            $('.uang').mask('000.000.000.000', {
                reverse: true
            });

        });
    </script>