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
                                    $gajiIds = array_map(
                                        function ($gaji) {
                                            return $gaji->id_tambahan;
                                        },
                                        $data['listGaji']
                                    );
                                ?>
                                    <tr>
                                        <td><?= $a++ ?></td>
                                        <td><?= $data['nama'] ?></td>
                                        <td><?= $data['lembaga'] ?></td>
                                        <td><?= $data['sik'] ?></td>
                                        <td>
                                            <?php foreach ($tambahan_data as $item) {
                                                $checked = in_array($item->id_tambahan, $gajiIds) ? 'checked' : '';
                                            ?>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value="<?= $item->id_tambahan ?>" id="<?= $data['guru_id'] . $item->id_tambahan ?>" onchange="updateCheckbox2('<?= $data['guru_id'] ?>','<?= $data['gaji_id'] ?>', '<?= $item->id_tambahan ?>', this.checked)" <?= $checked ?> <?= $gaji->status == 'kunci' ? 'disabled' : '' ?> />
                                                    <label class="form-check-label" for="<?= $data['guru_id'] . $item->id_tambahan ?>">
                                                        <?= $item->nama . ' - ' . rupiah($item->nominal) ?>
                                                    </label>
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

            $('.edit-btn').on('click', function() {

                var id = $(this).data('id');
                var nama = $(this).data('nama');
                var nominal = $(this).data('nominal');

                $('#id').val(id);
                $('#nama').val(nama);
                $('#nominal').val(nominal);

                $('#editModal').modal('show');
            });

        });

        function updateCheckbox2(guru_id, gaji_id, itemId, isChecked) {
            $.ajax({
                url: '<?= base_url("tambahan/addAdds") ?>',
                type: 'POST',
                dataType: 'json',
                data: {
                    guru_id: guru_id,
                    gaji_id: gaji_id,
                    itemId: itemId,
                    value: isChecked ? 'Y' : 'N'
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
        }

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