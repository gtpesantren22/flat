    <?php include 'head.php' ?>
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-lg-12 mb-4 order-0">
                <div class="card">
                    <h5 class="card-header">
                        Tunjangan Struktural
                        <!-- <button class="btn btn-sm btn-primary float-end" data-bs-toggle="modal" data-bs-target="#tambahModal"><i class='bx bxs-plus-circle'></i> Tambah Data</button> -->
                        <a href="<?= base_url('struktural/reload') ?>" class="btn btn-sm btn-primary float-end tbl-confirm" value="Data tunjangan Struktural akan dimuat ulang"><i class='bx bxs-plus-circle'></i> Reload Data</a>
                    </h5>
                    <div class="table-responsive card-datatable">
                        <table class="table mb-10" id="table1">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Jabatan</th>
                                    <th>Satminkal</th>
                                    <!-- <th>Masa Kerja</th> -->
                                    <!-- <th>Jam Kerja</th> -->
                                    <th>Nominal</th>
                                    <th>#</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1;
                                foreach ($datas as $data): ?>
                                    <tr>
                                        <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong><?= $no++ ?></strong></td>
                                        <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong><?= $data->nmjabatan ?></strong></td>
                                        <td><?= $data->nmsatminkal ?></td>
                                        <!-- <td><?= $data->masa_kerja ?> tahun</td> -->
                                        <!-- <td><?= $data->jam_kerja ?> jp</td> -->
                                        <td><?= rupiah($data->nominal) ?></td>
                                        <td>
                                            <div class="dropdown">
                                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                    <i class="bx bx-dots-vertical-rounded"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item edit-btn" href="javascript:void(0);" data-bs-toggle="modal" data-id="<?= $data->struktural_id ?>" data-jabatan="<?= $data->jabatan_id ?>" data-satminkal="<?= $data->satminkal_id ?>" data-nominal="<?= $data->nominal ?>" data-masa_kerja="<?= $data->masa_kerja ?>" data-jam_kerja="<?= $data->jam_kerja ?>" data-bs-target="#editModal"><i class="bx bx-edit-alt me-1"></i> Edit</a>

                                                    <a class="dropdown-item tombol-hapus" href="<?= base_url('struktural/hapus/' . $data->struktural_id) ?>"><i class="bx bx-trash me-1"></i> Delete</a>
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
            <form action="<?= base_url('struktural/tambah') ?>" method="post">
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
                            <label class="col-sm-2 col-form-label" for="">Satminkal</label>
                            <div class="col-sm-10">
                                <!-- <select class="form-control" id="a_satminkal" name="satminkal" required>
                                    <option value=""> -pilih- </option>
                                    <?php foreach ($satminkalOpt as $satminkal): ?>
                                        <option value="<?= $satminkal->id ?>"><?= $satminkal->nama ?></option>
                                        <?php endforeach ?>
                                    </select> -->
                                <?php foreach ($satminkalOpt as $satminkal): ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="satminkal[]" value="<?= $satminkal->id ?>" id="ck_<?= $satminkal->id ?>" />
                                        <label class="form-check-label" for="ck_<?= $satminkal->id ?>">
                                            <?= $satminkal->nama ?>
                                        </label>
                                    </div>
                                <?php endforeach ?>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <label class="col-sm-2 col-form-label" for="a_jabatan">Jabatan</label>
                            <div class="col-sm-10">
                                <select class="form-control" id="a_jabatan" name="jabatan" required>
                                    <option value=""> -pilih- </option>
                                    <?php foreach ($jabatanOpt as $jabatan): ?>
                                        <option value="<?= $jabatan->jabatan_id ?>"><?= $jabatan->nama ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <label class="col-sm-2 col-form-label" for="a_masa_kerja">Masa kerja</label>
                            <div class="col-sm-10">
                                <input type="number" class="form-control" id="a_masa_kerja" name="masa_kerja" value="0" placeholder="Masa Kerja Karyawan" required />
                            </div>
                        </div>
                        <div class="row mb-2">
                            <label class="col-sm-2 col-form-label" for="a_jam_kerja">Jam kerja</label>
                            <div class="col-sm-10">
                                <input type="number" class="form-control" id="a_jam_kerja" name="jam_kerja" value="0" placeholder="Jam Kerja Karyawan" required />
                            </div>
                        </div>
                        <div class="row mb-2">
                            <label class="col-sm-2 col-form-label" for="a_nominal">Nominal</label>
                            <div class="col-sm-10">
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
            <form action="<?= base_url('struktural/edit') ?>" method="post">
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
                            <label class="col-sm-2 col-form-label" for="satminkal">Satminkal</label>
                            <div class="col-sm-10">
                                <select class="form-control" id="satminkal" name="satminkal" required>
                                    <option value=""> -pilih- </option>
                                    <?php foreach ($satminkalOpt as $satminkal): ?>
                                        <option value="<?= $satminkal->id ?>"><?= $satminkal->nama ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <label class="col-sm-2 col-form-label" for="jabatan">Jabatan</label>
                            <div class="col-sm-10">
                                <select class="form-control" id="jabatan" name="jabatan" required>
                                    <option value=""> -pilih- </option>
                                    <?php foreach ($jabatanOpt as $jabatan): ?>
                                        <option value="<?= $jabatan->jabatan_id ?>"><?= $jabatan->nama ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <label class="col-sm-2 col-form-label" for="masa_kerja">Masa kerja</label>
                            <div class="col-sm-10">
                                <input type="number" class="form-control" id="masa_kerja" name="masa_kerja" placeholder="Masa Kerja struktural/Karyawan" required />
                            </div>
                        </div>
                        <div class="row mb-2">
                            <label class="col-sm-2 col-form-label" for="jam_kerja">Jam kerja</label>
                            <div class="col-sm-10">
                                <input type="number" class="form-control" id="jam_kerja" name="jam_kerja" placeholder="Jam Kerja struktural/Karyawan" required />
                            </div>
                        </div>
                        <div class="row mb-2">
                            <label class="col-sm-2 col-form-label" for="nominal">Nominal</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control uang" id="nominal" name="nominal" placeholder="Nominal Besaran" required />
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
                var jabatan = $(this).data('jabatan');
                var masa_kerja = $(this).data('masa_kerja');
                var jam_kerja = $(this).data('jam_kerja');
                var nominal = $(this).data('nominal');

                $('#id').val(id);
                $('#satminkal').val(satminkal).change();
                $('#jabatan').val(jabatan).change();
                $('#masa_kerja').val(masa_kerja);
                $('#jam_kerja').val(jam_kerja);
                $('#nominal').val(nominal);

                $('#editModal').modal('show');
            });

            $('#table1').DataTable();
            $('.uang').mask('000.000.000.000', {
                reverse: true
            });

        });
    </script>