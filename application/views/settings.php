    <?php
    include 'head.php';
    $a = 1;
    $b = 1;
    ?>

    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <!-- SIK -->
            <div class="col-lg-4 mb-4 order-0">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Setting SIK</h5>
                        <p class="card-text">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Adds</th>
                                    <th>PTY</th>
                                    <th>PTTY</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sik as $sik): ?>
                                    <tr>
                                        <td><?= $a++; ?></td>
                                        <td><?= $sik->adds ?></td>
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="Y" <?= $sik->pty == 'Y' ? 'checked' : '' ?> onchange="updateCheckbox(<?= $sik->id ?>, 'pty', this.checked)" />
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="Y" <?= $sik->ptty == 'Y' ? 'checked' : '' ?> onchange="updateCheckbox(<?= $sik->id ?>, 'ptty', this.checked)" />
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                        </p>
                        <!-- <button type="submit" class="btn btn-primary">Simpan</button> -->
                    </div>
                </div>
                <!-- Bootstrap Table with Caption -->

            </div>
            <div class="col-lg-8 mb-4 order-0">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            Setting Nominal Honor Insentif
                        </h5>
                        <p class="card-text">
                        <form action="<?= base_url('settings/updateInsentif') ?>" method="post">
                            <div class="form-group row mb-2">
                                <div class="col-sm-3 col-md-3 col-lg-3">
                                    <label for="honor_non" class="form-label">Honor Non-Santri</label>
                                    <input type="text" class="form-control uang" id="honor_non" name="honor_non" required value="<?= $honor_non ?>">
                                </div>
                                <div class="col-sm-3 col-md-3 col-lg-3">
                                    <label for="honor_santri" class="form-label">Honor Santri</label>
                                    <input type="text" class="form-control uang" id="honor_santri" name="honor_santri" required value="<?= $honor_santri ?>">
                                </div>
                                <div class="col-sm-3 col-md-3 col-lg-3">
                                    <label for="honor_rami" class="form-label">Honor RA-MI</label>
                                    <input type="text" class="form-control uang" id="honor_rami" name="honor_rami" required value="<?= $honor_rami ?>">
                                </div>
                                <div class="col-sm-3 col-md-3 col-lg-3">
                                    <label for="honor_rami" class="form-label">Gunakan untuk honor bulan</label>
                                    <select name="honor_id" id="" class="form-select" required>
                                        <option value="">Pilih</option>
                                        <?php foreach ($honordata as $row): ?>
                                            <option value="<?= $row->honor_id ?>"><?= bulan($row->bulan) . ' ' . $row->tahun ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                            </div>
                            <button class="btn btn-success btn-sm" type="submit">Simpan</button>
                        </form>
                        </p>
                    </div>
                </div>
            </div>
            <!-- HAK -->
            <div class="col-lg-12 mb-4 order-0">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            Setting Hak Terima
                            <a href="<?= base_url('settings/generateAllHak') ?>" class="btn btn-outline-danger btn-sm float-end tbl-confirm" value="Fitur ini akan memperbarui semua data hak guru berdasarkan SIK">Generate All</a>
                            <button class="btn btn-outline-success btn-sm float-end" data-bs-toggle="modal" data-bs-target="#editHak">Edit per Hak</button>
                        </h5>
                        <p class="card-text">
                        <div class="table-responsive card-datatable">
                            <table class="table table-xs" id="table1">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nama</th>
                                        <th>SIK</th>
                                        <th>Satminkal</th>
                                        <th>Jabatan</th>
                                        <th>Gapok</th>
                                        <th>Insentif</th>
                                        <th>T.Fu</th>
                                        <th>T.Ki</th>
                                        <th>T.Str</th>
                                        <th>T.B</th>
                                        <th>T.WK</th>
                                        <th>T.Pn</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($hak as $hak): ?>
                                        <tr>
                                            <td><?= $b++; ?></td>
                                            <td><?= $hak->nama ?></td>
                                            <td><?= $hak->sik ?></td>
                                            <td><?= $hak->lembaga ?></td>
                                            <td><?= $hak->jabatan ?></td>
                                            <td>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value="Y" <?= $hak->gapok != null && $hak->sik == 'PTY' ? 'checked' : '' ?> onchange="updateCheckbox2('<?= $hak->guru_id ?>', 'gapok', this.checked)" <?= $hak->sik != 'PTY' ? 'disabled' : '' ?> />
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value="Y" <?= $hak->gapok != null && $hak->sik == 'PTTY' ? 'checked' : '' ?> onchange="updateCheckbox2('<?= $hak->guru_id ?>', 'gapok', this.checked)" <?= $hak->sik != 'PTTY' ? 'disabled' : '' ?> />
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value="Y" <?= $hak->fungsional != null ? 'checked' : '' ?> onchange="updateCheckbox2('<?= $hak->guru_id ?>', 'fungsional', this.checked)" />
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value="Y" <?= $hak->kinerja != null ? 'checked' : '' ?> onchange="updateCheckbox2('<?= $hak->guru_id ?>', 'kinerja', this.checked)" />
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value="Y" <?= $hak->struktural != null ? 'checked' : '' ?> onchange="updateCheckbox2('<?= $hak->guru_id ?>', 'struktural', this.checked)" />
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value="Y" <?= $hak->bpjs != null ? 'checked' : '' ?> onchange="updateCheckbox2('<?= $hak->guru_id ?>', 'bpjs', this.checked)" />
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value="Y" <?= $hak->walas != null ? 'checked' : '' ?> onchange="updateCheckbox2('<?= $hak->guru_id ?>', 'walas', this.checked)" />
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value="Y" <?= $hak->penyesuaian != null ? 'checked' : '' ?> onchange="updateCheckbox2('<?= $hak->guru_id ?>', 'penyesuaian', this.checked)" />
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach ?>
                                </tbody>
                            </table>
                        </div>
                        </p>
                        <!-- <button type="submit" class="btn btn-primary">Simpan</button> -->
                    </div>
                </div>
                <!-- Bootstrap Table with Caption -->

            </div>

        </div>

    </div>

    <div class="modal fade" id="editHak" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form action="<?= base_url('settings/editHak') ?>" method="post">
                <input type="hidden" name="id" id="id">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel1">Edit Hak Terima per Item</h5>
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">Jenis Hak</label>
                                <select name="payment" id="nameBasic" class="form-select" required>
                                    <option value=""> -pilih jenis- </option>
                                    <option value="gapok">Gaji Pokok/Insentif</option>
                                    <option value="fungsional">Tunjangan Fungsional</option>
                                    <option value="kinerja">Tunjangan Kinerja</option>
                                    <option value="struktural">Tunjangan Struktural</option>
                                    <option value="bpjs">Tunjangan BPJS</option>
                                    <option value="walas">Tunjangan Wali Kelas</option>
                                    <option value="penyesuaian">Tunjangan Penyesuaian</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="jumlah" class="form-label">Keterangan</label>
                                <div class="form-check">
                                    <input name="ket" class="form-check-input" type="radio" value="Y" id="defaultRadio1" />
                                    <label class="form-check-label" for="defaultRadio1">
                                        Tambahkan
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input name="ket" class="form-check-input" type="radio" value="N" id="defaultRadio2" />
                                    <label class="form-check-label" for="defaultRadio2">
                                        Kosongi
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

    <!-- / Content -->
    <?php include 'foot.php' ?>
    <script src="<?= base_url(); ?>assets/js/jquery.mask.min.js"></script>
    <script>
        $('document').ready(function() {
            $('#table1').DataTable();
        })

        function updateCheckbox(id, field, isChecked) {
            $.ajax({
                url: '<?= base_url("settings/update_sik") ?>',
                type: 'POST',
                data: {
                    id: id,
                    field: field,
                    value: isChecked ? 'Y' : 'N'
                },
                success: function(response) {
                    console.log('Data updated successfully');
                },
                error: function() {
                    console.error('Failed to update data');
                }
            });
        }

        function updateCheckbox2(id, field, isChecked) {
            $.ajax({
                url: '<?= base_url("settings/update_hak") ?>',
                type: 'POST',
                data: {
                    id: id,
                    field: field,
                    value: isChecked ? 'Y' : 'N'
                },
                success: function(response) {
                    console.log('Data updated successfully');
                },
                error: function() {
                    console.error('Failed to update data');
                }
            });
        }

        $('.uang').mask('000.000.000.000', {
            reverse: true
        });
    </script>