    <?php include 'head.php' ?>
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-lg-12 mb-4 order-0">
                <div class="card">
                    <h5 class="card-header">
                        Data Users
                        <button class="btn btn-sm btn-primary float-end" data-bs-toggle="modal"
                            data-bs-target="#tambahModal"><i class='bx bxs-plus-circle'></i> Tambah Data</button>
                    </h5>
                    <div class="table-responsive card-datatable">
                        <table class="table mb-10" id="table1">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Username</th>
                                    <th>Level</th>
                                    <th>Status</th>
                                    <th>#</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                foreach ($data as $data): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong><?= $data->nama ?></strong></td>
                                        <td><?= $data->username ?></td>
                                        <td><?= $data->level ?></td>
                                        <td><?= $data->aktif ?></td>
                                        <td>
                                            <div class="dropdown">
                                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                    <i class="bx bx-dots-vertical-rounded"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item edit-btn" href="javascript:void(0);" data-bs-toggle="modal" data-id="<?= $data->id_user ?>" data-nama="<?= $data->nama ?>" data-username="<?= $data->username ?>" data-level="<?= $data->level ?>" data-aktif="<?= $data->aktif ?>" data-bs-target="#editModal"><i class="bx bx-edit-alt me-1"></i> Edit</a>
                                                    <a class="dropdown-item tombol-hapus" href="<?= base_url('settings/delUser/' . $data->id_user) ?>"><i class="bx bx-trash me-1"></i> Delete</a>
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
            <form action="<?= base_url('settings/addUser') ?>" method="post">
                <input type="hidden" name="id" id="id">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel1">Tambah Data User</h5>
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="a_nama" class="form-label">Nama</label>
                                <input type="text" name="nama" id="a_nama" class="form-control" placeholder="Enter Name" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="a_username" class="form-label">Username</label>
                                <input type="text" name="username" id="a_username" class="form-control" placeholder="Enter UserName" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="a_password" class="form-label">Password</label>
                                <input type="text" name="password" id="a_password" class="form-control" placeholder="Enter Password" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="a_level" class="form-label">Level</label>
                                <select name="level" id="a_level" class="form-control" required>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="" class="form-label">Status</label>
                                <div class="form-check">
                                    <input name="aktif" class="form-check-input" type="radio" value="Y" id="a_defaultRadio1" />
                                    <label class="form-check-label" for="a_defaultRadio1">
                                        Aktif
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input name="aktif" class="form-check-input" type="radio" value="T" id="a_defaultRadio2" />
                                    <label class="form-check-label" for="a_defaultRadio2">
                                        Non-Aktif
                                    </label>
                                </div>
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
            <form action="<?= base_url('settings/updateUser') ?>" method="post">
                <input type="hidden" name="id" id="id">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel1">Edit Data User</h5>
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col mb-3">
                                    <label for="nama" class="form-label">Nama</label>
                                    <input type="text" name="nama" id="nama" class="form-control" placeholder="Enter Name" required />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" name="username" id="username" class="form-control" placeholder="Enter UserName" required />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col mb-3">
                                    <label for="level" class="form-label">Level</label>
                                    <select name="level" id="level" class="form-control" required>
                                        <option value=""> -pilih- </option>
                                        <option value="admin">Admin</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col mb-3">
                                    <label for="level" class="form-label">Status</label>
                                    <div class="form-check">
                                        <input name="aktif" class="form-check-input" type="radio" value="Y" id="defaultRadio1" />
                                        <label class="form-check-label" for="defaultRadio1">
                                            Aktif
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input name="aktif" class="form-check-input" type="radio" value="T" id="defaultRadio2" />
                                        <label class="form-check-label" for="defaultRadio2">
                                            Non-Aktif
                                        </label>
                                    </div>
                                </div>
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
                var username = $(this).data('username');
                var level = $(this).data('level');
                var aktif = $(this).data('aktif');

                $('#id').val(id);
                $('#nama').val(nama);
                $('#username').val(username);
                $('#level').val(level).change();
                $('input[name="aktif"][value="' + aktif + '"]').prop('checked', true).change();

                $('#editModal').modal('show');
            });

            $('#table1').DataTable();
        });
    </script>