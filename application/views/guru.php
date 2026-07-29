    <?php include 'head.php' ?>

    <link rel="stylesheet" href="<?= base_url('assets/vendor/libs/flatpickr/flatpickr.css') ?>">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Alert/Card for Mismatched Guru -->
        <?php if (!empty($mismatched_gurus)): ?>
            <div class="card border border-danger mb-4">
                <div class="card-header bg-label-danger d-flex justify-content-between align-items-center py-2">
                    <h5 class="card-title text-danger mb-0">
                        <i class="bx bx-error-circle me-1"></i> Data Guru Tidak Sinkron (Perlu Dihapus)
                    </h5>
                    <a href="<?= base_url('guru/hapus_mismatch_all') ?>" class="btn btn-xs btn-danger tbl-confirm" value="SEMUA guru tidak sinkron ini beserta data absensi, potongan, honor, dan gaji bulan berjalan yang belum dikunci akan dihapus secara permanen!">
                        <i class="bx bx-trash me-1"></i> Hapus Semua (<?= count($mismatched_gurus) ?>)
                    </a>
                </div>
                <div class="card-body pt-3">
                    <p class="card-text text-muted small mb-3">
                        Guru-guru di bawah ini tercatat di database lokal Anda, tetapi tidak ditemukan di server API pusat. 
                        Menghapus data di bawah ini akan membersihkan profil beserta data absensi, potongan, dan honor pada periode gaji berjalan yang belum dikunci secara permanen.
                    </p>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th>Nama</th>
                                    <th>NIPY</th>
                                    <th>NIK</th>
                                    <th style="width: 100px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $m_no = 1; foreach ($mismatched_gurus as $mg): ?>
                                    <tr>
                                        <td><?= $m_no++ ?></td>
                                        <td class="fw-bold text-danger"><?= $mg->nama ?></td>
                                        <td><?= $mg->nipy ?></td>
                                        <td><?= $mg->nik ?></td>
                                        <td>
                                            <a href="<?= base_url('guru/hapus_mismatch/' . $mg->guru_id) ?>" class="btn btn-xs btn-outline-danger tbl-confirm" value="Guru '<?= addslashes($mg->nama) ?>' beserta data absensi, potongan, honor, dan gaji bulan berjalan yang belum dikunci akan dihapus secara permanen!">
                                                <i class="bx bx-trash me-1"></i> Hapus
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-12 mb-4 order-0">
                <div class="card">
                    <h5 class="card-header">
                        Data Guru/Karyawan
                        <button class="btn btn-sm btn-primary float-end" data-bs-toggle="modal"
                            data-bs-target="#tambahModal"><i class='bx bxs-plus-circle'></i> Tambah Data</button>
                        <button class="btn btn-sm btn-warning float-end me-2" id="syncNewBtn">
                            <i class='bx bx-refresh'></i> Sinkron Mode Baru
                        </button>
                        <button class="btn btn-sm btn-info float-end me-2" id="syncSatminkalBtn">
                            <i class='bx bx-refresh'></i> Sinkron Satminkal
                        </button>
                        <button class="btn btn-sm btn-dark float-end me-2" id="syncJabatanBtn">
                            <i class='bx bx-refresh'></i> Sinkron Jabatan
                        </button>
                    </h5>
                    <div class="card-datatable table-responsive ">
                        <table class="table mb-10" id="table1">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Lembaga</th>
                                    <th>Jabatan</th>
                                    <th>Krit</th>
                                    <th>SIK</th>
                                    <th>Ijzah</th>
                                    <th>TMT</th>
                                    <th>Golongan</th>
                                    <th>Ket</th>
                                    <th>#</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1;
                                foreach ($data as $data): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong><?= $data->nama ?></strong></td>
                                        <td><?= $data->nmlembaga ?></td>
                                        <td><?= $data->nmjabatan ?></td>
                                        <td><?= $data->kriteria ?></td>
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
                                                    <a class="dropdown-item edit-btn" href="javascript:void(0);" data-bs-toggle="modal" data-id="<?= $data->guru_id ?>" data-nama="<?= $data->nama ?>" data-nipy="<?= $data->nipy ?>" data-nik="<?= $data->nik ?>" data-satminkal="<?= $data->satminkal ?>" data-jabatan="<?= $data->jabatan ?>" data-kriteria="<?= $data->kriteria ?>" data-sik="<?= $data->sik ?>" data-ijazah="<?= $data->ijazah ?>" data-tmt="<?= $data->tmt ?>" data-golongan="<?= $data->golongan ?>" data-santri="<?= $data->santri ?>" data-kategori="<?= $data->kategori ?>" data-email="<?= $data->email ?>" data-hp="<?= $data->hp ?>" data-rekening="<?= $data->rekening ?>" data-bs-target="#editModal"><i class="bx bx-edit-alt me-1"></i> Edit</a>
                                                    <a class="dropdown-item tombol-hapus" href="<?= base_url('guru/hapus/' . $data->guru_id) ?>"><i class="bx bx-trash me-1"></i> Delete</a>
                                                    <a class="dropdown-item" href="<?= base_url('settings/sinc_guruOne/' . $data->guru_id) ?>"><i class="bx bx-refresh me-1"></i> Sinc</a>
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
                            <label class="col-sm-2 col-form-label" for="a_kriteria">Kriteria</label>
                            <div class="col-sm-10">
                                <select class="form-control" id="a_kriteria" name="kriteria" required>
                                    <option value=""> -pilih- </option>
                                    <option value="Guru"> Guru </option>
                                    <option value="Karyawan"> Karyawan </option>
                                    <option value="Pengabdian"> Pengabdian </option>
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
                            <label class="col-sm-2 col-form-label" for="kriteria">Kriteria</label>
                            <div class="col-sm-10">
                                <select class="form-control" id="kriteria" name="kriteria" required>
                                    <option value=""> -pilih- </option>
                                    <option value="Guru"> Guru </option>
                                    <option value="Karyawan"> Karyawan </option>
                                    <option value="Pengabdian"> Pengabdian </option>
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

    <!-- Modal Sinkronisasi Baru -->
    <div class="modal fade" id="syncNewModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sinkronisasi Data Guru (Mode Baru)</h5>
                </div>
                <div class="modal-body">
                    <div class="progress mb-3" style="height: 20px;">
                        <div id="sync-new-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 0%">0%</div>
                    </div>
                    <div id="sync-new-status" class="mb-3 text-muted fw-bold">Menyiapkan sinkronisasi...</div>
                    <div class="card border p-2 bg-light" style="max-height: 200px; overflow-y: auto;" id="sync-new-hasil-container">
                        <ul id="sync-new-hasil" class="list-unstyled mb-0" style="font-size: 12px; font-family: monospace;"></ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="syncNewCloseBtn" data-bs-dismiss="modal" disabled>Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Sinkronisasi Satminkal -->
    <div class="modal fade" id="syncSatminkalModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sinkronisasi Satminkal Guru</h5>
                </div>
                <div class="modal-body">
                    <div class="progress mb-3" style="height: 20px;">
                        <div id="sync-satminkal-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated bg-info" role="progressbar" style="width: 0%">0%</div>
                    </div>
                    <div id="sync-satminkal-status" class="mb-3 text-muted fw-bold">Menyiapkan data guru...</div>
                    <div class="card border p-2 bg-light" style="max-height: 200px; overflow-y: auto;" id="sync-satminkal-hasil-container">
                        <ul id="sync-satminkal-hasil" class="list-unstyled mb-0" style="font-size: 12px; font-family: monospace;"></ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="syncSatminkalCloseBtn" data-bs-dismiss="modal" disabled>Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Sinkronisasi Jabatan -->
    <div class="modal fade" id="syncJabatanModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sinkronisasi Jabatan Guru</h5>
                </div>
                <div class="modal-body">
                    <div class="progress mb-3" style="height: 20px;">
                        <div id="sync-jabatan-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated bg-dark" role="progressbar" style="width: 0%">0%</div>
                    </div>
                    <div id="sync-jabatan-status" class="mb-3 text-muted fw-bold">Menyiapkan data guru...</div>
                    <div class="card border p-2 bg-light" style="max-height: 200px; overflow-y: auto;" id="sync-jabatan-hasil-container">
                        <ul id="sync-jabatan-hasil" class="list-unstyled mb-0" style="font-size: 12px; font-family: monospace;"></ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="syncJabatanCloseBtn" data-bs-dismiss="modal" disabled>Tutup</button>
                </div>
            </div>
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
                var kriteria = $(this).data('kriteria');
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
                $('#kriteria').val(kriteria).change();
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

            // --- SINKRONISASI MODE BARU ---
            var allSyncedIds = [];
            var totalPages = 1;
            
            $('#syncNewBtn').on('click', function() {
                Swal.fire({
                    title: 'Yakin?',
                    text: 'Sinkronisasi guru mode baru akan dijalankan secara menyeluruh.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Lanjutkan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.value) {
                        allSyncedIds = [];
                        $("#sync-new-hasil").html('');
                        $("#sync-new-progress-bar").css("width", "0%").text("0%").addClass("progress-bar-animated progress-bar-striped").removeClass("bg-danger").addClass("bg-success");
                        $("#sync-new-status").text("Menghubungi server API...");
                        $('#syncNewCloseBtn').prop('disabled', true);
                        
                        // Tampilkan modal
                        $('#syncNewModal').modal('show');
                        
                        // Mulai dari page 1
                        fetchSyncPage(1);
                    }
                });
            });
            
            function fetchSyncPage(page) {
                $("#sync-new-status").text("Mengambil data halaman " + page + "...");
                
                $.ajax({
                    url: "<?= base_url('guru/sinc_new_page') ?>",
                    method: "POST",
                    dataType: "json",
                    data: { page: page },
                    success: function(res) {
                        if (res.status === "success") {
                            totalPages = res.last_page;
                            let percent = Math.round((page / totalPages) * 80); // 80% progress for page loops
                            $("#sync-new-progress-bar").css("width", percent + "%").text(percent + "%");
                            
                            // Append logs
                            $("#sync-new-hasil").append("<li>[Halaman " + page + "/" + totalPages + "] Menyinkronkan " + res.saved + " data guru...</li>");
                            scrollLogs();
                            
                            // Accumulate synced ids
                            if (res.synced_ids && res.synced_ids.length > 0) {
                                allSyncedIds = allSyncedIds.concat(res.synced_ids);
                            }
                            
                            if (res.has_more) {
                                // Fetch next page after short delay
                                setTimeout(function() {
                                    fetchSyncPage(page + 1);
                                }, 150);
                            } else {
                                // Finish and run cleanup
                                finishSync();
                            }
                        } else {
                            handleSyncError("Gagal memproses data halaman " + page + ": " + (res.message || "Unknown error."));
                        }
                    },
                    error: function(xhr, status, error) {
                        handleSyncError("Gagal menghubungi server pada halaman " + page + ": " + error);
                    }
                });
            }
            
            function finishSync() {
                $("#sync-new-status").text("Memproses sinkronisasi data lokal...");
                $("#sync-new-progress-bar").css("width", "90%").text("90%");
                
                $.ajax({
                    url: "<?= base_url('guru/sinc_new_finish') ?>",
                    method: "POST",
                    dataType: "json",
                    data: { synced_ids: allSyncedIds },
                    success: function(res) {
                        if (res.status === "success") {
                            $("#sync-new-progress-bar").css("width", "100%").text("100%").removeClass("progress-bar-animated progress-bar-striped");
                            $("#sync-new-status").text("Sinkronisasi selesai! ✅");
                            $("#sync-new-hasil").append("<li class='text-success fw-bold'>[Selesai] " + allSyncedIds.length + " guru berhasil disinkronkan.</li>");
                            scrollLogs();
                            
                            $('#syncNewCloseBtn').prop('disabled', false);
                            
                            Swal.fire({
                                title: 'Berhasil!',
                                text: 'Sinkronisasi selesai. Silakan periksa kembali daftar guru yang tidak sinkron (jika ada).',
                                icon: 'success',
                                confirmButtonText: 'Selesai'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            handleSyncError(res.message || "Gagal memperbarui status data lokal.");
                        }
                    },
                    error: function(xhr, status, error) {
                        handleSyncError("Gagal menghubungi server saat pembersihan: " + error);
                    }
                });
            }
            
            function handleSyncError(message) {
                $("#sync-new-progress-bar").removeClass("bg-success").addClass("bg-danger").removeClass("progress-bar-animated");
                $("#sync-new-status").text("Sinkronisasi gagal ❌");
                $("#sync-new-hasil").append("<li class='text-danger fw-bold'>[Error] " + message + "</li>");
                scrollLogs();
                $('#syncNewCloseBtn').prop('disabled', false);
                
                Swal.fire({
                    title: 'Gagal!',
                    text: message,
                    icon: 'error',
                    confirmButtonText: 'Tutup'
                });
            }
            
            function scrollLogs() {
                var container = document.getElementById("sync-new-hasil-container");
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            }

            // --- SINKRONISASI SATMINKAL ---
            var totalSatminkalGurus = 0;
            var processedSatminkalGurus = 0;
            
            $('#syncSatminkalBtn').on('click', function() {
                Swal.fire({
                    title: 'Yakin?',
                    text: 'Sinkronisasi Satminkal akan berjalan per guru secara bertahap.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Lanjutkan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.value) {
                        processedSatminkalGurus = 0;
                        $("#sync-satminkal-hasil").html('');
                        $("#sync-satminkal-progress-bar").css("width", "0%").text("0%").addClass("progress-bar-animated progress-bar-striped").removeClass("bg-danger").addClass("bg-info");
                        $("#sync-satminkal-status").text("Menghitung data guru lokal...");
                        $('#syncSatminkalCloseBtn').prop('disabled', true);
                        
                        // Tampilkan modal
                        $('#syncSatminkalModal').modal('show');
                        
                        // Start sync
                        $.ajax({
                            url: "<?= base_url('guru/sync_detail_start') ?>",
                            method: "POST",
                            dataType: "json",
                            success: function(res) {
                                if (res.status === "success") {
                                    totalSatminkalGurus = res.total;
                                    if (totalSatminkalGurus === 0) {
                                        $("#sync-satminkal-status").text("Tidak ada guru untuk disinkronkan.");
                                        $('#syncSatminkalCloseBtn').prop('disabled', false);
                                        return;
                                    }
                                    fetchSatminkalBatch(0, 10);
                                } else {
                                    handleSatminkalError("Gagal memulai sinkronisasi: " + res.message);
                                }
                            },
                            error: function(xhr, status, error) {
                                handleSatminkalError("Gagal menghubungi server lokal: " + error);
                            }
                        });
                    }
                });
            });
            
            function fetchSatminkalBatch(offset, limit) {
                $("#sync-satminkal-status").text("Memproses data guru " + (offset + 1) + " sampai " + Math.min(offset + limit, totalSatminkalGurus) + " dari " + totalSatminkalGurus + "...");
                
                $.ajax({
                    url: "<?= base_url('guru/sync_satminkal_batch') ?>",
                    method: "POST",
                    dataType: "json",
                    data: { offset: offset, limit: limit },
                    success: function(res) {
                        if (res.status === "success") {
                            processedSatminkalGurus += res.processed;
                            let percent = Math.round((processedSatminkalGurus / totalSatminkalGurus) * 100);
                            $("#sync-satminkal-progress-bar").css("width", percent + "%").text(percent + "%");
                            
                            // Log logs
                            if (res.logs && res.logs.length > 0) {
                                res.logs.forEach(function(log) {
                                    $("#sync-satminkal-hasil").append("<li>" + log + "</li>");
                                });
                            }
                            scrollSatminkalLogs();
                            
                            if (res.has_more && processedSatminkalGurus < totalSatminkalGurus) {
                                setTimeout(function() {
                                    fetchSatminkalBatch(offset + limit, limit);
                                }, 150);
                            } else {
                                $("#sync-satminkal-progress-bar").removeClass("progress-bar-animated progress-bar-striped");
                                $("#sync-satminkal-status").text("Sinkronisasi Satminkal selesai! ✅");
                                $('#syncSatminkalCloseBtn').prop('disabled', false);
                                
                                Swal.fire({
                                    title: 'Selesai!',
                                    text: 'Sinkronisasi Satminkal selesai untuk ' + processedSatminkalGurus + ' guru.',
                                    icon: 'success',
                                    confirmButtonText: 'Selesai'
                                }).then(() => {
                                    location.reload();
                                });
                            }
                        } else {
                            handleSatminkalError("Gagal memproses batch pada offset " + offset + ": " + (res.message || "Unknown error."));
                        }
                    },
                    error: function(xhr, status, error) {
                        handleSatminkalError("Gagal menghubungi server pada offset " + offset + ": " + error);
                    }
                });
            }
            
            function handleSatminkalError(message) {
                $("#sync-satminkal-progress-bar").removeClass("bg-info").addClass("bg-danger").removeClass("progress-bar-animated");
                $("#sync-satminkal-status").text("Sinkronisasi gagal ❌");
                $("#sync-satminkal-hasil").append("<li class='text-danger fw-bold'>[Error] " + message + "</li>");
                scrollSatminkalLogs();
                $('#syncSatminkalCloseBtn').prop('disabled', false);
                
                Swal.fire({
                    title: 'Gagal!',
                    text: message,
                    icon: 'error',
                    confirmButtonText: 'Tutup'
                });
            }
            
            function scrollSatminkalLogs() {
                var container = document.getElementById("sync-satminkal-hasil-container");
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            }


            // --- SINKRONISASI JABATAN ---
            var totalJabatanGurus = 0;
            var processedJabatanGurus = 0;
            
            $('#syncJabatanBtn').on('click', function() {
                Swal.fire({
                    title: 'Yakin?',
                    text: 'Sinkronisasi Jabatan akan berjalan per guru secara bertahap.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Lanjutkan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.value) {
                        processedJabatanGurus = 0;
                        $("#sync-jabatan-hasil").html('');
                        $("#sync-jabatan-progress-bar").css("width", "0%").text("0%").addClass("progress-bar-animated progress-bar-striped").removeClass("bg-danger").addClass("bg-dark");
                        $("#sync-jabatan-status").text("Menghitung data guru lokal...");
                        $('#syncJabatanCloseBtn').prop('disabled', true);
                        
                        // Tampilkan modal
                        $('#syncJabatanModal').modal('show');
                        
                        // Start sync
                        $.ajax({
                            url: "<?= base_url('guru/sync_detail_start') ?>",
                            method: "POST",
                            dataType: "json",
                            success: function(res) {
                                if (res.status === "success") {
                                    totalJabatanGurus = res.total;
                                    if (totalJabatanGurus === 0) {
                                        $("#sync-jabatan-status").text("Tidak ada guru untuk disinkronkan.");
                                        $('#syncJabatanCloseBtn').prop('disabled', false);
                                        return;
                                    }
                                    fetchJabatanBatch(0, 10);
                                } else {
                                    handleJabatanError("Gagal memulai sinkronisasi: " + res.message);
                                }
                            },
                            error: function(xhr, status, error) {
                                handleJabatanError("Gagal menghubungi server lokal: " + error);
                            }
                        });
                    }
                });
            });
            
            function fetchJabatanBatch(offset, limit) {
                $("#sync-jabatan-status").text("Memproses data guru " + (offset + 1) + " sampai " + Math.min(offset + limit, totalJabatanGurus) + " dari " + totalJabatanGurus + "...");
                
                $.ajax({
                    url: "<?= base_url('guru/sync_jabatan_batch') ?>",
                    method: "POST",
                    dataType: "json",
                    data: { offset: offset, limit: limit },
                    success: function(res) {
                        if (res.status === "success") {
                            processedJabatanGurus += res.processed;
                            let percent = Math.round((processedJabatanGurus / totalJabatanGurus) * 100);
                            $("#sync-jabatan-progress-bar").css("width", percent + "%").text(percent + "%");
                            
                            // Log logs
                            if (res.logs && res.logs.length > 0) {
                                res.logs.forEach(function(log) {
                                    $("#sync-jabatan-hasil").append("<li>" + log + "</li>");
                                });
                            }
                            scrollJabatanLogs();
                            
                            if (res.has_more && processedJabatanGurus < totalJabatanGurus) {
                                setTimeout(function() {
                                    fetchJabatanBatch(offset + limit, limit);
                                }, 150);
                            } else {
                                $("#sync-jabatan-progress-bar").removeClass("progress-bar-animated progress-bar-striped");
                                $("#sync-jabatan-status").text("Sinkronisasi Jabatan selesai! ✅");
                                $('#syncJabatanCloseBtn').prop('disabled', false);
                                
                                Swal.fire({
                                    title: 'Selesai!',
                                    text: 'Sinkronisasi Jabatan selesai untuk ' + processedJabatanGurus + ' guru.',
                                    icon: 'success',
                                    confirmButtonText: 'Selesai'
                                }).then(() => {
                                    location.reload();
                                });
                            }
                        } else {
                            handleJabatanError("Gagal memproses batch pada offset " + offset + ": " + (res.message || "Unknown error."));
                        }
                    },
                    error: function(xhr, status, error) {
                        handleJabatanError("Gagal menghubungi server pada offset " + offset + ": " + error);
                    }
                });
            }
            
            function handleJabatanError(message) {
                $("#sync-jabatan-progress-bar").removeClass("bg-dark").addClass("bg-danger").removeClass("progress-bar-animated");
                $("#sync-jabatan-status").text("Sinkronisasi gagal ❌");
                $("#sync-jabatan-hasil").append("<li class='text-danger fw-bold'>[Error] " + message + "</li>");
                scrollJabatanLogs();
                $('#syncJabatanCloseBtn').prop('disabled', false);
                
                Swal.fire({
                    title: 'Gagal!',
                    text: message,
                    icon: 'error',
                    confirmButtonText: 'Tutup'
                });
            }
            
            function scrollJabatanLogs() {
                var container = document.getElementById("sync-jabatan-hasil-container");
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            }

        });
    </script>