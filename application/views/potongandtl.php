    <?php include 'head.php';
    $no = 1; ?>
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-lg-12 mb-4 order-0">
                <div class="card">
                    <h5 class="card-header">
                        Data Potongan Gaji
                    </h5>
                    <div class="table-responsive card-datatable">
                        <table class="table mb-10" id="table1">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Bulan</th>
                                    <th>Nama</th>
                                    <th>Jumlah Potongan</th>
                                    <th>#</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data as $data): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= bulan($data->bulan) . ' ' . $data->tahun ?></td>
                                        <td><?= $data->nama ?></td>
                                        <td><?= rupiah($data->total) ?></td>
                                        <td>
                                            <button class="btn btn-primary btn-xs btn-detail" data-id="<?= $data->id ?>">Detail</button>
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

    <div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Detail Data Potongan</h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="detail"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                </div>
            </div>

        </div>
    </div>


    <!-- / Content -->
    <?php include 'foot.php' ?>

    <script>
        $(document).ready(function() {

            $('.btn-detail').on('click', function() {
                var id = $(this).data('id');
                $.ajax({
                    type: 'POST',
                    url: '<?= base_url('potongan/get_data') ?>',
                    data: {
                        id: id
                    },
                    dataType: 'json',
                    success: function(data) {
                        $('#detail').empty();
                        $('#detail').html(data);
                        $('#detailModal').modal('show');
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                    }
                });

                $('#editModal').modal('show');
            });

            $('#table1').DataTable();
        });
    </script>