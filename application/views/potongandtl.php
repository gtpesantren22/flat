    <?php include 'head.php';
    $no = 1; ?>
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-lg-4 mb-4 order-0">
                <div class="card">
                    <h5 class="card-header">
                        Total Potongan
                    </h5>

                    <div class="card-body">
                        <ul class="p-0 m-0">
                            <table class="table table-striped table-sm">
                                <tbody>
                                    <?php foreach ($datapotongan as $potong): ?>
                                        <tr>
                                            <th><?= $potong['ket'] ?></th>
                                            <th><?= rupiah($potong['nominal']) ?></th>
                                        </tr>
                                    <?php endforeach ?>
                                </tbody>
                            </table>
                        </ul>
                    </div>
                </div>
                <!-- Bootstrap Table with Caption -->

            </div>
            <div class="col-lg-8 mb-4 order-0">
                <div class="card">
                    <div class="table-responsive card-datatable">
                        <table class="table mb-10 table-sm" id="table1">
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
                    <!-- <div id="detail"></div> -->
                    <div class="table-responsive">
                        <table width="100%" id="table-potongan">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Jenis Potongan</th>
                                    <th>Nominal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
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
    <script src="<?= base_url(); ?>assets/js/jquery.min.js"></script>
    <script src="<?= base_url(); ?>assets/js/jquery.mask.min.js"></script>
    <script>
        $(document).ready(function() {

            $('.btn-detail').on('click', function() {
                var id = $(this).data('id');
                $('#detailModal').modal('show');
                $.ajax({
                    type: 'POST',
                    url: '<?= base_url('potongan/get_data') ?>',
                    data: {
                        id: id
                    },
                    dataType: 'json',
                    success: function(response) {
                        const rows = generateTableRows(response.data);
                        $('#table-potongan tbody').html(rows);
                        // $('#id').val(id);
                        // $('.btn-close').attr('value', id);
                        $('.uang').mask('000.000.000.000', {
                            reverse: true
                        });
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                    }
                });

                $('#editModal').modal('show');
            });

            $('#table1').DataTable();
        });
        $('#table-potongan').on('change', '.form-input', function() {
            var newValue = $(this).val(); // nilai baru dari input
            var id = $(this).data('id'); // id dari baris data
            var inputName = $(this).attr('name');

            // alert(inputName)
            $.ajax({
                url: '<?= base_url("potongan/updatePotongan") ?>', // endpoint untuk update data
                type: 'POST',
                data: {
                    id: id,
                    value: newValue,
                    inputName: inputName
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status == 'success') {
                        $('.uang').mask('000.000.000.000', {
                            reverse: true
                        });
                    } else {
                        alert('Gagal mengupdate data');
                    }
                },
                error: function() {
                    alert('Terjadi kesalahan saat mengupdate data');
                }
            });
        });

        function generateTableRows(response) {
            let rows = '';
            let no = 1;
            $.each(response, function(index, item) {
                rows += `
            <tr>
                <td>${no++}</td>
                <td><input type="text" name="ket" class="form-control form-input" data-id="${item.id}" value="${item.ket}"></td>
                <td><input type="text" name="nominal" class="form-control form-input uang" data-id="${item.id}" value="${item.nominal}"></td>
                <td><button class="btn btn-sm btn-danger-outline del-btn" data-id="${item.id}">Del</button></td>
            </tr>
            `;
            });
            return rows;
        }
        $(document).on('click', '.del-btn', function() {
            var id = $(this).data('id');
            $.ajax({
                type: 'POST',
                url: '<?= base_url('potongan/del_row'); ?>',
                dataType: 'json',
                data: {
                    id: id
                },
                success: function(response) {
                    const rows = generateTableRows(response.data);
                    $('#table-potongan tbody').html(rows);
                    // $('#id').val(id);
                    $('.uang').mask('000.000.000.000', {
                        reverse: true
                    });
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                }
            })
        })
    </script>