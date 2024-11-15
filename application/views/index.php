    <?php include 'head.php' ?>
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-lg-12 mb-4 order-0">
                <div class="card">
                    <div class="d-flex align-items-end row">
                        <div class="col-sm-7">
                            <div class="card-body">
                                <h5 class="card-title text-primary">Selamat Datang <?= $user->nama ?>! 🎉</h5>
                                <p class="mb-4">
                                    Gaji bulan ini <span class="fw-bold"><?= bulan(date('m')) . ' ' . date('Y') ?></span> siap dibuat. silahkan kunjungi link dibawah ini.
                                </p>

                                <a href="<?= base_url('gaji') ?>" class="btn btn-sm btn-outline-primary"><i class="bx bx-calculator"></i>Cek gaji</a>
                            </div>
                        </div>
                        <div class="col-sm-5 text-center text-sm-left mb-2">
                            <div class="card-body pb-0 px-0 px-md-4">
                                <img
                                    src="<?= base_url() ?>assets/img/illustrations/man-with-laptop-light.png"
                                    height="140"
                                    alt="View Badge User"
                                    data-app-dark-img="illustrations/man-with-laptop-dark.png"
                                    data-app-light-img="illustrations/man-with-laptop-light.png" />
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>

    </div>
    <!-- / Content -->
    <?php include 'foot.php' ?>