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
        <div class="row g-6">
            <!-- Card Border Shadow -->
            <div class="col-lg-4 col-sm-6">
                <div class="card card-border-shadow-primary h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-4">
                                <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-money bx-lg"></i></span>
                            </div>
                            <h4 class="mb-0" id="all">......</h4>
                        </div>
                        <p class="mb-2">Budged 1 tahun honor</p>
                        <!-- <p class="mb-0">
                            <span class="text-heading fw-medium me-2">+18.2%</span>
                            <span class="text-muted">than last week</span>
                        </p> -->
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-sm-6">
                <div class="card card-border-shadow-danger h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-4">
                                <span class="avatar-initial rounded bg-label-danger"><i class='bx bx-money bx-lg'></i></span>
                            </div>
                            <h4 class="mb-0" id="pakai">......</h4>
                        </div>
                        <p class="mb-2">Jumlah terpakai</p>
                        <!-- <p class="mb-0">
                            <span class="text-heading fw-medium me-2">-8.7%</span>
                            <span class="text-muted">than last week</span>
                        </p> -->
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-sm-6">
                <div class="card card-border-shadow-success h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-4">
                                <span class="avatar-initial rounded bg-label-success"><i class='bx bx-money bx-lg'></i></span>
                            </div>
                            <h4 class="mb-0" id="sisa">......</h4>
                        </div>
                        <p class="mb-2">Sisa pemakaian</p>
                        <!-- <p class="mb-0">
                            <span class="text-heading fw-medium me-2">-8.7%</span>
                            <span class="text-muted">than last week</span>
                        </p> -->
                    </div>
                </div>
            </div>

        </div>

    </div>
    <!-- / Content -->
    <?php include 'foot.php' ?>
    <script>
        $(document).ready(function() {
            $.ajax({
                url: "<?= base_url('welcome/loadNominal') ?>",
                type: "GET",
                dataType: "json",
                success: function(res) {
                    $('#all').text(res.all)
                    $('#pakai').text(res.pakai)
                    $('#sisa').text(res.sisa)
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                }
            })
        })
    </script>