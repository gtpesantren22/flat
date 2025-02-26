    <?php include 'head.php';
    $a = 1;
    ?>
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-lg-12 mb-4 order-0">
                <div class="card">
                    <h5 class="card-header">
                        Tunjangan Tambahan
                        <button class="btn btn-sm btn-warning float-end" onclick="window.location='<?= base_url('tambahan') ?>'"><i class='bx bxs-arrow-left'></i> Kembali</button>
                    </h5>
                    <div class="table-responsive card-datatable">
                        <table class="table mb-10 table-sm" id="table1">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Satminkal</th>
                                    <th>SIK</th>
                                    <th>List</th>
                                    <th>Total</th>
                                    <th>#</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $tambahan_data = $tambahan;

                                foreach ($data as $data):
                                    $gajiIds = array_map(function ($gaji) {
                                        return ['id' => $gaji->id_tambahan, 'nominal' => $gaji->jumlah];
                                    }, $data['listGaji']);

                                ?>
                                    <tr>
                                        <td><?= $a++ ?></td>
                                        <td><?= $data['nama'] ?></td>
                                        <td><?= $data['lembaga'] ?></td>
                                        <td><?= $data['sik'] ?></td>
                                        <td>
                                            <?php foreach ($tambahan_data as $item) {
                                                $index = array_search($item->id_tambahan, array_column($gajiIds, 'id'));
                                                $hasil = ($index !== false) ? $gajiIds[$index]['nominal'] : 0;
                                            ?>
                                                <div class="row">
                                                    <label for="html5-text-input" class="col-md-8 col-form-label"><?= $item->nama . ' - ' . number_format($item->nominal) ?></label>
                                                    <div class="col-md-4">
                                                        <input class="form-control form-control-sm kehadiran" data-guru_id="<?= $data['guru_id'] ?>" data-gaji_id="<?= $data['gaji_id'] ?>" data-id="<?= $item->id_tambahan ?>" value="<?= $hasil ?>" type="number" placeholder="Jml Kehadiran" />
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </td>
                                        <td id="tot_<?= $data['guru_id'] ?>"><?= rupiah($data['total']) ?></td>
                                        <td></td>
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

    <script src="<?= base_url(); ?>assets/js/jquery.mask.min.js"></script>
    <script>
        $(document).ready(function() {

            $(' .edit-btn').on('click', function() {

                var id = $(this).data('id');
                var nama = $(this).data('nama');
                var nominal = $(this).data('nominal');

                $('#id').val(id);
                $('#nama').val(nama);
                $('#nominal').val(nominal);

                $('#editModal').modal('show');
            });

        });

        $(document).on('change', '.kehadiran', function() {
            var id = $(this).data('id');
            var guru_id = $(this).data('guru_id');
            var gaji_id = $(this).data('gaji_id');
            var value = $(this).val();

            $.ajax({
                url: '<?= base_url("tambahan/addAdds") ?>',
                type: 'POST',
                dataType: 'json',
                data: {
                    guru_id: guru_id,
                    gaji_id: gaji_id,
                    itemId: id,
                    value: value
                },
                success: function(response) {
                    if (response.status == 'success') {
                        var total = Number(response.total);
                        $('#tot_' + guru_id).text(formatRupiah(total));
                    } else {
                        alert('error add/del gaji');
                    }
                },
                error: function() {
                    console.error('Failed to update data');
                }
            });
        })

        $('#table1').DataTable({
            pageLength: 5,
        });
        $('.uang').mask('000.000.000.000', {
            reverse: true
        });

        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(number);
        }
    </script>