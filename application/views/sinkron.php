    <?php include 'head.php' ?>
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-lg-12 mb-4 order-0">
                <div class="card">
                    <h5 class="card-header">
                        Sinkronisasi Data
                    </h5>
                    <div class="card-body">
                        <div class="demo-inline-spacing mt-1">
                            <ul class="list-group">
                                <?php foreach ($data as $data): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <?= $data->nama ?>
                                        <div>
                                            <span class="badge bg-primary">Last sinkron : <?= $data->last ?></span>
                                            <button class="btn btn-sm btn-warning mulai" data-id="<?= $data->id ?>" data-info="<?= $data->nama ?>" data-kode="<?= $data->kode ?>"><i class="bx bx-refresh"></i></button>
                                        </div>
                                    </li>
                                <?php endforeach ?>
                            </ul>
                        </div>

                        <!-- Test Api -->
                        <div class="progress my-3" style="height: 20px;">
                            <div id="progress-bar" class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 0%">0%</div>
                        </div>

                        <div id="status" class="mb-3 text-muted">Menunggu proses sinkron...</div>
                        <ul id="hasil"></ul>

                        <!-- end Test -->
                    </div>
                </div>
                <!-- Bootstrap Table with Caption -->

            </div>

        </div>

    </div>


    <!-- / Content -->
    <?php include 'foot.php' ?>
    <script>
        $('.mulai').on('click', function() {
            var id = $(this).data('id');
            var info = $(this).data('info');
            var kode = $(this).data('kode');
            $("#hasil").html('')
            Swal.fire({
                title: 'Yakin ?',
                text: info + ' akan disinkron',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Lanjutkan!',

            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: "<?= base_url('settings/sinc_') ?>" + kode,
                        method: "POST",
                        dataType: "json",
                        data: {
                            'id': id
                        },
                        beforeSend: function() {
                            $("#status").text(`Mengambil ${info} dari API...`);
                        },
                        success: function(res) {
                            if (res.status === "success") {
                                let total = res.total;
                                let saved = res.saved;
                                let index = 0;

                                let interval = setInterval(function() {
                                    let persen = Math.min(((index + 1) / total) * 100, 100);
                                    $("#progress-bar").css("width", persen + "%").text(Math.round(persen) + "%");

                                    if (index < total) {
                                        $("#hasil").append("<li>Data ke-" + (index + 1) + " berhasil disimpan</li>");
                                    }

                                    index++;
                                    if (index >= total) {
                                        clearInterval(interval);
                                        $("#status").text(`Sinkronisasi ${info} selesai ✅ Total disimpan: ` + saved);
                                    }
                                }, 300);
                            } else {
                                $("#status").text("Error: " + res.message);
                            }
                        },
                        error: function() {
                            $("#status").text("Gagal menghubungi API ❌");
                        }
                    });
                }
            });

        });
    </script>