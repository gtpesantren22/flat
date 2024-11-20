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
                                    <th>#</th>
                                    <th>Nama</th>
                                    <th>Lembaga</th>
                                    <th>Nominal Flat</th>
                                    <th>Nominal Sebelum</th>
                                    <th>Selisih</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($hasil as $data):
                                    $selisih = $data['total'] - $data['sebelum'];
                                ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= $data['nama'] ?></td>
                                        <td><?= $data['lembaga'] ?></td>
                                        <td><?= rupiah($data['total']) ?></td>
                                        <td><?= rupiah($data['sebelum']) ?></td>
                                        <td>
                                            <?php if ($data['total'] > $data['sebelum']) { ?>
                                                <b class="text-success"><i class="bx bx-plus"></i><?= number_format($selisih) ?> <i class='bx bxs-up-arrow-alt'></i></b>
                                            <?php } elseif ($data['total'] < $data['sebelum']) { ?>
                                                <b class="text-danger"><?= number_format($selisih) ?> <i class='bx bxs-down-arrow-alt'></i></b>
                                            <?php } else { ?>
                                                <b><?= rupiah($selisih) ?></b>
                                            <?php } ?>
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


    <!-- / Content -->
    <?php include 'foot.php' ?>

    <script>
        $(document).ready(function() {

            $('.edit-btn').on('click', function() {
                var id = $(this).data('id');
                var nama = $(this).data('nama');

                $('#nama').val(nama);
                $('#id').val(id);
                $('#editModal').modal('show');
            });

            $('#table1').DataTable();
        });
    </script>