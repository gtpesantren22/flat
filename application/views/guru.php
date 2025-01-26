    <?php include 'head.php' ?>

    <link rel="stylesheet" href="<?= base_url('assets/vendor/libs/flatpickr/flatpickr.css') ?>">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-lg-12 mb-4 order-0">
                <div class="card">
                    <h5 class="card-header">
                        Data Guru/Karyawan
                        <button class="btn btn-sm btn-primary float-end" data-bs-toggle="modal"
                            data-bs-target="#tambahModal"><i class='bx bxs-plus-circle'></i> Tambah Data</button>
                    </h5>
                    <div class="card-datatable table-responsive ">
                        <table class="table mb-10" id="table1">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Lembaga</th>
                                    <th>Jabatan</th>
                                    <th>SIK</th>
                                    <th>Ijzah</th>
                                    <th>TMT</th>
                                    <th>Golongan</th>
                                    <th>Ket</th>
                                    <th>#</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data as $data): ?>
                                    <tr>
                                        <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong><?= $data->nama ?></strong></td>
                                        <td><?= $data->nmlembaga ?></td>
                                        <td><?= $data->nmjabatan ?></td>
                                        <td><?= $data->sik ?></td>
                                        <td><?= $data->nmijazah ?></td>
                                        <td><?= $data->tmt ?> <span class="badge bg-secondary"><?= selisihTahun($data->tmt) ?> thn</span></td>
                                        <td><?= $data->nmgolongan . ' - ' . $data->nmkategori ?></td>
                                        <td><?= $data->santri ?></td>
                                        <td>
                                            <div class="dropdown">
                                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                    <i class="bx bx-dots-vertical-rounded"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item edit-btn" href="javascript:void(0);" data-bs-toggle="modal" data-id="<?= $data->guru_id ?>" data-nama="<?= $data->nama ?>" data-nipy="<?= $data->nipy ?>" data-nik="<?= $data->nik ?>" data-satminkal="<?= $data->satminkal ?>" data-jabatan="<?= $data->jabatan ?>" data-sik="<?= $data->sik ?>" data-ijazah="<?= $data->ijazah ?>" data-tmt="<?= $data->tmt ?>" data-golongan="<?= $data->golongan ?>" data-santri="<?= $data->santri ?>" data-kategori="<?= $data->kategori ?>" data-email="<?= $data->email ?>" data-hp="<?= $data->hp ?>" data-rekening="<?= $data->rekening ?>" data-bs-target="#editModal"><i class="bx bx-edit-alt me-1"></i> Edit</a>
                                                    <a class="dropdown-item tombol-hapus" href="<?= base_url('guru/hapus/' . $data->guru_id) ?>"><i class="bx bx-trash me-1"></i> Delete</a>
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
            <form action="<?= base_url('guru/tambah') ?>" method="post">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel1">Tambah Data Jabatan</h5>
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-2">
                            <label class="col-sm-2 col-form-label" for="a_nipy">NIPY</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="a_nipy" name="nipy" placeholder="NIPY" required />
                            </div>
                        </div>
                        <div class="row mb-2">
                            <label class="col-sm-2 col-form-label" for="a_nik">NIK</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="a_nik" name="nik" placeholder="NIK" required />
                            </div>
                        </div>
                        <div class="row mb-2">
                            <label class="col-sm-2 col-form-label" for="a_nama">Nama</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="a_nama" name="nama" placeholder="Nama Lengkap" required />
                            </div>
                        </div>
                        <div class="row mb-2">
                            <label class="col-sm-2 col-form-label" for="a_email">Email</label>
                            <div class="col-sm-10">
                                <input type="email" class="form-control" id="a_email" name="email" placeholder="Alamat Email" required />
                            </div>
                        </div>
                        <div class="row mb-2">
                            <label class="col-sm-2 col-form-label" for="a_hp">No. HP</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="a_hp" name="hp" placeholder="Nomor HP/WA" required />
                            </div>
                        </div>
                        <div class="row mb-2">
                            <label class="col-sm-2 col-form-label" for="a_rekening">No. Rekening</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="a_rekening" name="rekening" placeholder="Nomor Rekening" required />
                            </div>
                        </div>
                        <div class="row mb-2">
                            <label class="col-sm-2 col-form-label" for="a_satminkal">Satminkal</label>
                            <div class="col-sm-10">
                                <select class="form-control" id="a_satminkal" name="satminkal" required>
                                    <option value=""> -pilih- </option>
                                    <?php foreach ($lembagaOpt as $lembaga): ?>
                                        <option value="<?= $lembaga->id ?>"><?= $lembaga->nama ?></option>
                                    <?php endforeach ?>
                                </select>
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
                            <label class="col-sm-2 col-form-label" for="a_sik">SIK</label>
                            <div class="col-sm-10">
                                <select class="form-control" id="a_sik" name="sik" required>
                                    <option value=""> -pilih- </option>
                                    <option value="PTY">PTY</option>
                                    <option value="PTTY">PTTY</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <label class="col-sm-2 col-form-label" for="a_ijazah">Ijazah</label>
                            <div class="col-sm-10">
                                <select class="form-control" id="a_ijazah" name="ijazah" required>
                                    <option value=""> -pilih- </option>
                                    <?php foreach ($ijazahOpt as $ijazah): ?>
                                        <option value="<?= $ijazah->id ?>"><?= $ijazah->nama ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <label class="col-sm-2 col-form-label" for="a_tmt">TMT</label>
                            <div class="col-sm-10">
                                <input type="date" class="form-control flatpickr-date" id="a_tmt" name="tmt" placeholder="TMT Guru/Karyawan" required />
                            </div>
                        </div>
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
                            <label class="col-sm-2 col-form-label" for="a_santri">Jenis</label>
                            <div class="col-sm-10">
                                <select class="form-control" id="a_santri" name="santri" required>
                                    <option value=""> -pilih- </option>
                                    <option value="santri">Santri</option>
                                    <option value="non-santri">Non-Santri</option>
                                </select>
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
            <form action="<?= base_url('guru/edit') ?>" method="post">
                <input type="hidden" name="id" id="id">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel1">Edit Data Jabatan</h5>
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-2">
                            <label class="col-sm-2 col-form-label" for="nipy">NIPY</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="nipy" name="nipy" placeholder="NIPY" required />
                            </div>
                        </div>
                        <div class="row mb-2">
                            <label class="col-sm-2 col-form-label" for="nik">NIK</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="nik" name="nik" placeholder="NIK" required />
                            </div>
                        </div>
                        <div class="row mb-2">
                            <label class="col-sm-2 col-form-label" for="nama">Nama</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="nama" name="nama" placeholder="Nama Lengkap" required />
                            </div>
                        </div>
                        <div class="row mb-2">
                            <label class="col-sm-2 col-form-label" for="email">Email</label>
                            <div class="col-sm-10">
                                <input type="email" class="form-control" id="email" name="email" placeholder="Alamat Email" required />
                            </div>
                        </div>
                        <div class="row mb-2">
                            <label class="col-sm-2 col-form-label" for="hp">No. HP</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="hp" name="hp" placeholder="Nomor HP/WA" required />
                            </div>
                        </div>
                        <div class="row mb-2">
                            <label class="col-sm-2 col-form-label" for="rekening">No. Rekening</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="rekening" name="rekening" placeholder="Nomor Rekening" required />
                            </div>
                        </div>
                        <div class="row mb-2">
                            <label class="col-sm-2 col-form-label" for="satminkal">Satminkal</label>
                            <div class="col-sm-10">
                                <select class="form-control" id="satminkal" name="satminkal" required>
                                    <option value=""> -pilih- </option>
                                    <?php foreach ($lembagaOpt as $lembaga): ?>
                                        <option value="<?= $lembaga->id ?>"><?= $lembaga->nama ?></option>
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
                            <label class="col-sm-2 col-form-label" for="sik">SIK</label>
                            <div class="col-sm-10">
                                <select class="form-control" id="sik" name="sik" required>
                                    <option value=""> -pilih- </option>
                                    <option value="PTY">PTY</option>
                                    <option value="PTTY">PTTY</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <label class="col-sm-2 col-form-label" for="ijazah">Ijazah</label>
                            <div class="col-sm-10">
                                <select class="form-control" id="ijazah" name="ijazah" required>
                                    <option value=""> -pilih- </option>
                                    <?php foreach ($ijazahOpt as $ijazah): ?>
                                        <option value="<?= $ijazah->id ?>"><?= $ijazah->nama ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <label class="col-sm-2 col-form-label" for="tmt">TMT</label>
                            <div class="col-sm-10">
                                <input type="date" class="form-control" id="tmt" name="tmt" placeholder="TMT Guru/Karyawan" required />
                            </div>
                        </div>
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
                            <label class="col-sm-2 col-form-label" for="santri">Jenis</label>
                            <div class="col-sm-10">
                                <select class="form-control" id="santri" name="santri" required>
                                    <option value=""> -pilih- </option>
                                    <option value="santri">Santri</option>
                                    <option value="non-santri">Non-Santri</option>
                                </select>
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

    <script src="<?= base_url('assets/vendor/libs/flatpickr/flatpickr.js') ?>"></script>

    <script>
        $(document).ready(function() {

            $('.edit-btn').on('click', function() {
                var id = $(this).data('id');
                var nipy = $(this).data('nipy');
                var nik = $(this).data('nik');
                var nama = $(this).data('nama');
                var satminkal = $(this).data('satminkal');
                var jabatan = $(this).data('jabatan');
                var sik = $(this).data('sik');
                var ijazah = $(this).data('ijazah');
                var tmt = $(this).data('tmt');
                var golongan = $(this).data('golongan');
                var santri = $(this).data('santri');
                var kategori = $(this).data('kategori');
                var email = $(this).data('email');
                var hp = $(this).data('hp');
                var rekening = $(this).data('rekening');

                $('#nama').val(nama);
                $('#id').val(id);
                $('#nipy').val(nipy);
                $('#nik').val(nik);
                $('#satminkal').val(satminkal).change();
                $('#jabatan').val(jabatan).change();
                $('#sik').val(sik);
                $('#ijazah').val(ijazah).change();
                $('#tmt').val(tmt);
                $('#golongan').val(golongan).change();
                $('#santri').val(santri).change();
                $('#kategori').val(kategori).change();
                $('#email').val(email).change();
                $('#hp').val(hp).change();
                $('#rekening').val(rekening).change();

                $('#editModal').modal('show');
            });

            $('#table1').DataTable();
            $('.flatpickr-date').flatpickr({
                // monthSelectorType: "static"
            });

        });
    </script>