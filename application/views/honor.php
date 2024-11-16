    <?php
    include 'head.php';
    $a = 1;
    $b = 1;
    ?>

    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <!-- SIK -->
            <div class="col-lg-4 mb-4 order-0">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            Rekap Bulanan
                            <button class="btn btn-outline-success btn-sm float-end" data-bs-toggle="modal" data-bs-target="#tambahRekap">Buat Baru</button>
                        </h5>
                        <p class="card-text">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Bulan</th>
                                    <th>#</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($honorGroup as $row): ?>
                                    <tr>
                                        <td><?= bulan($row->bulan) . ' ' . $row->tahun ?></td>
                                        <td><button class="btn btn-xs btn-info btn-cek" data-id="<?= $row->id ?>">Cek</button></td>
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
            <!-- HAK -->
            <div class="col-lg-8 mb-4 order-0">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            Daftar Guru/Karyawan PTTY
                            <!-- <a href="<?= base_url('settings/generateAllHak') ?>" class="btn btn-outline-danger btn-sm float-end tbl-confirm" value="Fitur ini akan memperbarui semua data hak guru berdasarkan SIK">Generate All</a> -->

                        </h5>
                        <p class="card-text">
                        <div class="table-responsive card-datatable">
                            <table class="table table-sm" id="table1">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Bulan</th>
                                        <th>Nama</th>
                                        <th>Ket</th>
                                        <th>Jml Jam</th>
                                        <th>Nominal</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                        </p>
                        <!-- <button type="submit" class="btn btn-primary">Simpan</button> -->
                    </div>
                </div>
                <!-- Bootstrap Table with Caption -->

            </div>

        </div>

    </div>

    <div class="modal fade" id="tambahRekap" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form action="<?= base_url('honor/buatBaru') ?>" method="post">
                <input type="hidden" name="id" id="id">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel1">Edit Hak Terima per Item</h5>
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
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="tahun" class="form-label">Tahun</label>
                                <input type="text" name="tahun" id="tahun" class="form-control" value="<?= date('Y') ?>" required>
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

    <script>
        $('document').ready(function() {
            showTable(0);
        })
        $('.btn-cek').on('click', function() {
            var id = $(this).data('id');
            if ($.fn.DataTable.isDataTable('#table1')) {
                $('#table1').DataTable().destroy();
                $('#table1').empty(); // Kosongkan elemen tabel
            }
            showTable(id);
        })

        function showTable(params) {
            $('#table1').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": '<?= base_url('honor/rincian') ?>',
                    "type": "POST",
                    "data": {
                        'id': params
                    }
                },
                "columns": [{
                        "data": null,
                        "render": function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1; // Nomor urut
                        }
                    },
                    {
                        "data": 6
                    },
                    {
                        "data": 1
                    },
                    {
                        "data": 2
                    },
                    {
                        "data": 3,
                        "render": function(data, type, row) {
                            return `<input type="text" class="form-control form-input" data-id="${row[5]}" value="${data}">`;
                        }
                    },
                    {
                        "render": function(data, type, row, meta) {
                            return `<b id='hasil-honor-${row[5]}'>${formatRupiah(row[4])}</b>`;
                        }
                    }
                ],
                "pageLength": 10,
                "searchDelay": 500
            });
        }

        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(number);
        }

        $('#table1').on('change', '.form-input', function() {
            var newValue = $(this).val(); // nilai baru dari input
            var id = $(this).data('id'); // id dari baris data

            $.ajax({
                url: '<?= base_url("honor/editJam") ?>', // endpoint untuk update data
                type: 'POST',
                data: {
                    id: id,
                    value: newValue
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status == 'ok') {
                        $(this).val(newValue);
                        $(`#hasil-honor-${id}`).text(formatRupiah(newValue * response.besaran));
                    } else {
                        alert('Gagal mengupdate data');
                    }
                },
                error: function() {
                    alert('Terjadi kesalahan saat mengupdate data');
                }
            });
        });
    </script>