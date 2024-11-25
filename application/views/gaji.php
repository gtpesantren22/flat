    <?php
    include 'head.php';
    $a = 1;
    ?>

    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-lg-12 mb-4 order-0">
                <div class="card">
                    <!-- <div class="card-header">
                        Data Gaji Guru/Karyawan
                    </div> -->
                    <div class="card-body">
                        <h5 class="card-title">
                            Data Gaji Guru/Karyawan
                            <button class="btn btn-primary btn-sm float-end" data-bs-toggle="modal" data-bs-target="#tambahModal"><i class="bx bx-plus-circle"></i> Tambah Gaji</button>
                        </h5>
                        <!-- <p class="card-text"> -->
                        <table class="table table-sm" id="table1">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Keterangan</th>
                                    <th>Tahun Pelajaran</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($gaji as $gaji): ?>
                                    <tr>
                                        <td><?= $a++; ?></td>
                                        <td><?= $gaji->status == 'kunci' ? "<span class='text-warning bx bxs-key'></span>" : '' ?> <?= bulan($gaji->bulan) . ' ' . $gaji->tahun ?></td>
                                        <td><?= $gaji->tapel ?></td>
                                        <td>
                                            <button onclick="window.location='<?= base_url('gaji/detail/' . $gaji->gaji_id) ?>'" class="btn btn-info btn-sm"><i class="bx bx-detail"></i> Detail</button>
                                            <a href="<?= base_url('gaji/hapus/' . $gaji->gaji_id) ?>" class="btn btn-danger btn-sm tombol-hapus"><i class="bx bx-trash"></i> Hapus</a>
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

        </div>

    </div>

    <div class="modal fade" id="tambahModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form action="<?= base_url('gaji/tambah') ?>" method="post">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel1">Tambah Data Gaji</h5>
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