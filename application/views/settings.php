    <?php
    include 'head.php';
    $a = 1;
    $b = 1;
    ?>

    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-lg-12 mb-4 order-0">
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

            <!-- Setting DB -->
            <div class="col-lg-4 mb-4 order-0">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            Pindah Koneksi Database
                        </h5>
                        <p class="card-text">
                        <form action="<?= base_url('settings/setDb') ?>" method="post">
                            <div class="form-group row mb-2">
                                <div class="col-sm-12 col-md-12 col-lg-12">
                                    <label for="honor_rami" class="form-label">Pilih database</label>
                                    <select name="db_id" id="" class="form-select" required>
                                        <option value="">Pilih</option>
                                        <?php foreach ($db_list as $row): ?>
                                            <option value="<?= $row->id ?>"><?= $row->name ?></option>
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

        </div>

    </div>



    <!-- / Content -->
    <?php include 'foot.php' ?>
    <script src="<?= base_url(); ?>assets/js/jquery.mask.min.js"></script>
    <script>
        $('document').ready(function() {
            $('#table1').DataTable();
        })


        $('.uang').mask('000.000.000.000', {
            reverse: true
        });
    </script>