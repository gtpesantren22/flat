    <?php include 'head.php' ?>
    <link rel="stylesheet" href="<?= base_url('assets/vendor/libs/select2/select2.css') ?>">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-lg-12 mb-2 order-0">
                <div class="card">
                    <h5 class="card-header">
                        Tunjangan Penyesuaian
                        <!-- <button class="btn btn-sm btn-primary float-end" data-bs-toggle="modal" data-bs-target="#tambahModal"><i class='bx bxs-plus-circle'></i> Tambah Data</button> -->
                        <!-- <a href="<?= base_url('penyesuaian/reset') ?>" class="btn btn-sm btn-danger float-end tbl-confirm" value="Data akan direset/dikosongkan"><i class='bx bx-reset'></i> Reset Data</a>
                        <a href="<?= base_url('penyesuaian/sesuaikan') ?>" class="btn btn-sm btn-primary float-end tbl-confirm" value="Data akan direset/dikosongkan"><i class='bx bxs-pin'></i> Sesuaikan data</a> -->
                    </h5>
                    <div class="table-responsive card-datatable">
                        <table class="table mb-10" id="table1">
                            <thead>
                                <tr>
                                    <th>Nama Guru</th>
                                    <th>SIK</th>
                                    <th>Sebelum Flat</th>
                                    <th>Sesudah Flat</th>
                                    <th>Selisih</th>
                                    <th>#</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $total = 0;
                                foreach ($data as $data):
                                    $selisih = $data['sesudah'] - $data['sebelum'];
                                    $total += $selisih;
                                ?>
                                    <tr>
                                        <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong><?= $data['nama'] ?></strong></td>
                                        <td><?= $data['sik'] ?></td>
                                        <td><?= rupiah($data['sebelum']) ?></td>
                                        <td> <?= rupiah($data['sesudah']) ?></td>
                                        <td><?php
                                            if ($selisih > 0) {
                                                echo "<b class='text-success'>" . rupiah($selisih) . "</b>";
                                            } elseif ($selisih < 0) {
                                                echo "<b class='text-danger'>" . rupiah($selisih) . "</b>";
                                            } else {
                                                echo rupiah($selisih);
                                            }
                                            ?></td>
                                        <td>
                                            <div class="dropdown">
                                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                    <i class="bx bx-dots-vertical-rounded"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <!-- <a class="dropdown-item edit-btn" href="javascript:void(0);" data-id="<?= $data['penyesuaian_id'] ?>"><i class="bx bx-edit-alt me-1"></i> Edit</a> -->

                                                    <!-- <a class="dropdown-item tombol-hapus" href="<?= base_url('penyesuaian/hapus/' . $data['penyesuaian_id']) ?>"><i class="bx bx-trash me-1"></i> Delete</a> -->
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4">TOTAL</th>
                                    <th><?= rupiah($total) ?></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                        <br>
                    </div>
                </div>
                <!-- Bootstrap Table with Caption -->

            </div>

        </div>

    </div>

    <div class="modal fade" id="tambahModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form action="<?= base_url('penyesuaian/tambah') ?>" method="post">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel1">Tambah Data Tunjangan</h5>
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <div class="row mb-2">
                            <label class="col-sm-2 col-form-label" for="">Nama Guru</label>
                            <div class="col-sm-10">
                                <select class="select2 form-select form-select-lg" data-allow-clear="true" id="a_guru" name="guru" required>
                                    <option value=""> -pilih- </option>
                                    <?php foreach ($guruOpt as $guru): ?>
                                        <option value="<?= $guru->guru_id ?>"><?= $guru->nama ?></option>
                                    <?php endforeach ?>
                                </select>
                                <div id="rincian-gaji"></div>
                            </div>
                        </div>
                        <input type="hidden" id="a_sesudah" name="sesudah" readonly />
                        <div class="row mb-2">
                            <label class="col-sm-2 col-form-label" for="a_sebelum">Sebelum</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control uang" id="a_sebelum" name="sebelum" placeholder="Nominal Sebelum Flats" required />
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
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form action="<?= base_url('penyesuaian/edit') ?>" method="post">
                <input type="hidden" name="id" id="id">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel1">Edit Data Tunjangan</h5>
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-2">
                            <label class="col-sm-2 col-form-label" for="nama">Nama Guru</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="nama" name="nama" readonly />
                                <div id="rincian-gaji2"></div>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <label class="col-sm-2 col-form-label" for="nominal">Sebelum Flat</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control uang" id="sebelum" name="sebelum" placeholder="Nominal Sebelum Flat" required />
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="sesudah" name="sesudah" readonly />
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

    <script src="<?= base_url(); ?>assets/js/jquery.mask.min.js"></script>
    <script src="<?= base_url(); ?>assets/vendor/libs/select2/select2.js"></script>
    <script src="<?= base_url(); ?>assets/vendor/libs/select2/forms-selects.js"></script>
    <script>
        $(document).ready(function() {

            $('#a_guru').change(function() {
                var guruId = $(this).val();

                if (guruId) {
                    $.ajax({
                        url: '<?= base_url("penyesuaian/getGajis") ?>',
                        type: 'POST',
                        data: {
                            id: guruId
                        },
                        dataType: 'json',
                        success: function(response) {
                            $('#rincian-gaji').html(response.hasil);
                            $('#a_sesudah').val(response.total);
                            $('#a_sebelum').val(response.sebelum);
                        },
                        error: function() {
                            $('#rincian-gaji').html('<p>Gagal mengambil data.</p>');
                        }
                    });
                } else {
                    $('#rincian-gaji').html('');
                }
            });

            $('.edit-btn').on('click', function() {

                var idPn = $(this).data('id');
                $('#id').val(idPn);

                if (idPn) {
                    $.ajax({
                        url: '<?= base_url("penyesuaian/showDetail") ?>',
                        type: 'POST',
                        data: {
                            idSend: idPn
                        },
                        dataType: 'json',
                        success: function(response) {
                            $('#rincian-gaji2').html(response.hasil);
                            $('#sesudah').val(response.total);
                            $('#nama').val(response.nama);
                            $('#sebelum').val(response.sebelum);
                            // console.log(response)
                        },
                        error: function() {
                            $('#rincian-gaji2').html('<p>Gagal mengambil data.</p>');
                        }
                    });
                } else {
                    alert('id tidak terkirim')
                }

                $('#editModal').modal('show');
            });

            $('#table1').DataTable();
            $('.uang').mask('000.000.000.000', {
                reverse: true
            });

        });
    </script>