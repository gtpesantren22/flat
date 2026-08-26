    <?php
    include 'head.php';
    ?>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <?php
    $a = 1;
    $url = $datagaji->status == 'kunci' ? base_url('gaji/detail3') : base_url('gaji/detail2');
    ?>

    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-lg-12 mb-4 order-0">
                <div class="card">
                    <!-- <div class="card-header">
                        Data Gaji Guru/Karyawan
                    </div> -->

                    <div class="card-body">
                        <h5 class="card-title">
                            <?= $datagaji->status == 'kunci' ? "<span class='text-danger bx bxs-key'>locked</span> || " : '' ?> Data Gaji Guru/Karyawan
                            <!-- <a class="btn btn-outline-primary btn-sm float-end tbl-confirm" value="Fitur ini akan men-generate ulang semua data yang sudah ada" href="<?= base_url('gaji/regenerate/' . $idgaji) ?>"><i class="bx bx-refresh"></i> Generate Ulang</a> -->
                            <!-- <button class="btn btn-outline-danger btn-sm float-end" data-bs-toggle="modal" data-bs-target="#modal-kunci"><i class="bx bxs-key"></i> Kunci Data</button> -->
                            <div class="btn-group float-end">
                                <button type="button" class="btn btn-outline-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="bx bx-menu"></i> Menu</button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item " href="<?= base_url('gaji/regenerate/' . $idgaji) ?>">Regenerate</a></li>
                                    <li><a class="dropdown-item tbl-confirm" value="Fitur ini akan mengupdate nominal gaji berdasarkan data guru yang terbaru" href=" <?= base_url('sinc_guru/reloadNominal/' . $idgaji) ?>">Reload Nominal</a></li>
                                    <li><a class="dropdown-item cursor-pointer text-danger" data-bs-toggle="modal" data-bs-target="#modal-kunci"><i class="bx bxs-key"></i> Kunci Gaji</a></li>
                                </ul>
                            </div>
                            <?php if ($potong): ?>
                                <div class=" btn-group float-end">
                                    <button type="button" class="btn btn-outline-success btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="bx bx-spreadsheet"></i> Export Data</button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item tbl-confirm" value="Pastikan data gaji nya sudah dikunci terlebih dahulu" href="<?= base_url('gaji/exportGajiV2/' . $idgaji) ?>">Export Gaji</a></li>
                                        <li><a class="dropdown-item" href="<?= base_url('gaji/exportPotongan/' . $potong->potongan_id) ?>">Export Potongan</a></li>
                                        <?php if ($datagaji->status == 'kunci'): ?>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-primary cursor-pointer" data-bs-toggle="modal" data-bs-target="#exportSlipModal"><i class="bx bx-download me-1"></i> Export Slip Gaji</a></li>
                                        <?php endif ?>
                                    </ul>
                                </div>
                            <?php endif ?>
                        </h5>

                        <!-- TOOLBAR -->
                        <div class="row px-4 py-2 align-items-center">
                            <!-- PER PAGE (KIRI) -->
                            <div class="col-md-6 col-12 mb-2 mb-md-0">
                                <div class="d-flex align-items-center gap-2">
                                    <label class="mb-0 fw-semibold">Show</label>
                                    <select id="perPage" class="form-select form-select w-auto">
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>
                                    <span class="fw-semibold">entries</span>
                                </div>
                            </div>

                            <!-- SEARCH (KANAN) -->
                            <div class="col-md-6 col-12 text-md-end">
                                <div class="input-group input-group w-60 w-md-50 ms-md-auto">
                                    <span class="input-group-text">
                                        <i class="bx bx-search"></i>
                                    </span>
                                    <input
                                        type="search"
                                        id="search"
                                        class="form-control"
                                        placeholder="Cari data...">
                                </div>
                            </div>
                        </div>

                        <p class="card-text"></p>
                        <div class="table-responsive card-table">
                            <table class="table table-sm" id="">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nama Guru</th>
                                        <th>Satminkal</th>
                                        <th>Jabatan</th>
                                        <th>Gol</th>
                                        <th>SIK</th>
                                        <th>Ijazah</th>
                                        <th>TMT</th>
                                        <th>TOTAL</th>
                                        <th>#</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody">

                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between p-3 border-top">

                            <!-- INFO -->
                            <div class="text-muted small mb-2 mb-md-0">
                                Menampilkan
                                <span id="startRecord">1</span>
                                sampai
                                <span id="endRecord">10</span>
                                dari
                                <span id="totalRecords">100</span>
                                entri
                            </div>

                            <!-- PAGINATION -->
                            <div id="pagination"></div>

                        </div>
                        <!-- <button type="submit" class="btn btn-primary">Simpan</button> -->
                    </div>
                </div>
                <!-- Bootstrap Table with Caption -->

            </div>

        </div>

    </div>

    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <!-- <h5 class="modal-title" id="exampleModalLabel1">Rincian Nominal Gaji</h5> -->
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-3">
                            <table>
                                <tr>
                                    <td>Nama</td>
                                    <td>:</td>
                                    <td><b id="nama" class="text-primary"></b></td>
                                </tr>
                                <tr>
                                    <td>Lembaga</td>
                                    <td>:</td>
                                    <td><b id="lembaga" class="text-primary"></b></td>
                                </tr>
                                <tr>
                                    <td>Kriteria</td>
                                    <td>:</td>
                                    <td><b id="kriteria" class="text-primary"></b></td>
                                </tr>
                                <tr>
                                    <td>Gaji</td>
                                    <td>:</td>
                                    <td><b id="waktu" class="text-primary"></b></td>
                                </tr>
                            </table>
                            <hr>
                            <h5>Total akhir</h5>
                            <h3 id="total-akhir"></h3>
                            <hr>
                            <?php if ($datagaji->status != 'kunci'): ?>
                                <form action="<?= base_url("gaji/reloadGaji") ?>" method="post" class="form-update">
                                    <input type="hidden" name="guru_id" id="guru_id">
                                    <input type="hidden" name="gaji_id" value="<?= $idgaji ?>">
                                    <button type="submit" class="btn btn-outline-success">Refresh Data</button>
                                </form>
                            <?php else: ?>
                                <a href="#" id="btn-cetak-slip" target="_blank" class="btn btn-outline-primary"><i class="bx bx-printer"></i> Cetak Slip</a>
                            <?php endif ?>
                        </div>
                        <div class="col-md-5">
                            <table class="table table-sm">
                                <tr>
                                    <td class="text-bold text-primary" colspan="2">Rincian Gaji</td>
                                    <!-- <td class="text-bold text-primary"><b id="nama"></b></td> -->
                                </tr>
                                <tr>
                                    <td>
                                        <label for="" id="nama_gaji"></label>
                                    </td>
                                    <td><b id="gapok"></b></td>
                                </tr>
                                <tr>
                                    <td>
                                        Tunjangan Fungsional
                                    </td>
                                    <td><b id="fungsional"></b></td>
                                </tr>
                                <tr>
                                    <td>
                                        Tunjangan Kinerja
                                    </td>
                                    <td><b id="kinerja"></b></td>
                                </tr>
                                <tr>
                                    <td>
                                        Tunjangan BPJS
                                    </td>
                                    <td><b id="bpjs"></b></td>
                                </tr>
                                <tr>
                                    <td>
                                        Tunjangan Struktural
                                    </td>
                                    <td><b id="struktural"></b></td>
                                </tr>
                                <tr>
                                    <td>
                                        Tunjangan Wali Kelas
                                    </td>
                                    <td><b id="walas"></b></td>
                                </tr>
                                <tr>
                                    <td>
                                        Tunjangan Penyesuaian
                                    </td>
                                    <td><b id="penyesuaian"></b></td>
                                </tr>
                                <tr>
                                    <td>
                                        Tunjangan Tambahan
                                    </td>
                                    <td><b id="tambahan"></b></td>
                                </tr>
                                <tr>
                                    <th class="text-bold text-primary">TOTAL GAJI</th>
                                    <th class="text-bold text-primary"><b id="total"></b></th>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-4">
                            <div id="hasil-potong"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="modal fade" id="modal-kunci" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="backDropModalTitle">Kunci Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php if ($datagaji->status == 'kunci') { ?>
                        <div class="text-center">
                            <b class="text-danger">Data sudah terkunci</b><br>
                        </div>
                    <?php } else { ?>
                        <div class="text-center">
                            <b class="">Proses penguncian data</b><br>
                            <strong class="text-danger"><i class='bx bx-info-circle'></i> Pastikan data sudah valid sebelum terkuci!</strong><br><br>
                            <button type="button" class="btn btn-primary" id="proses-kunci" data-id="<?= $datagaji->gaji_id ?>"><span class="tf-icons bx bx-bolt-circle bx-18px me-2"></span>Lanjutkan Proses!</button>
                            <br>
                        </div>
                        <div id="view-hasil" class="mt-5"></div>
                    <?php } ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <!-- <button type="button" class="btn btn-primary">Save</button> -->
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Export Slip Kolektif -->
    <div class="modal fade" id="exportSlipModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class='bx bx-download me-1'></i> Export Slip Gaji Kolektif</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">Pilih lembaga (Satminkal) di bawah ini untuk mengunduh semua slip gaji guru di lembaga tersebut dalam format ZIP.</p>
                    <?php if (empty($satminkalList)): ?>
                        <div class="text-center text-muted py-3">Tidak ada data lembaga/Satminkal yang terdeteksi.</div>
                    <?php else: ?>
                        <div class="list-group">
                            <?php foreach ($satminkalList as $sat): ?>
                                <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center btn-export-bulk" data-satminkal="<?= htmlspecialchars($sat['nama']) ?>">
                                    <span><i class='bx bx-school me-2 text-primary'></i> <?= $sat['nama'] ?></span>
                                    <span class="badge bg-primary rounded-pill"><i class='bx bx-download'></i> ZIP</span>
                                </button>
                            <?php endforeach ?>
                        </div>
                    <?php endif ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- / Content -->
    <?php include 'foot.php' ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

    <script>
        $('document').ready(function() {

            $(document).on('submit', '.form-update', function(e) {
                e.preventDefault(); // Mencegah form dari reload halaman

                var form = $(this); // Form yang dikirim
                var formData = form.serialize(); // Serialize data form
                var button = $(this).find("button[type='submit']");
                button.prop("disabled", true).text("Memproses...");

                // Eksekusi AJAX
                $.ajax({
                    url: form.attr('action'), // Mengambil URL dari atribut form
                    type: 'POST',
                    data: formData,
                    dataType: 'json', // Pastikan format respon JSON
                    success: function(response) {
                        if (response) {
                            // console.log(response)
                            button.prop("disabled", false).text("Refresh Data");

                            var total = response.total;
                            $('#gapok').text(formatRupiah(response.gapok));
                            $('#fungsional').text(formatRupiah(response.fungsional));
                            $('#kinerja').text(formatRupiah(response.kinerja));
                            $('#bpjs').text(formatRupiah(response.bpjs));
                            $('#walas').text(formatRupiah(response.walas));
                            $('#struktural').text(formatRupiah(response.struktural));
                            $('#penyesuaian').text(formatRupiah(response.penyesuaian));
                            $('#total').text(formatRupiah(response.total));

                            $.ajax({
                                type: "POST",
                                url: "<?= base_url('gaji/getPotongan') ?>",
                                data: formData,
                                dataType: 'json',
                                success: function(data) {
                                    // console.log(data);
                                    // let cleanedStr = total.replace(/[^\d]/g, '');
                                    let num = parseInt(total, 10);
                                    let total_akhir = parseInt(num, 10) - parseInt(data.total_potong, 10);
                                    $('#hasil-potong').empty();
                                    $('#lembaga').empty();
                                    $('#total-akhir').empty();
                                    $('#waktu').empty();
                                    $('#hasil-potong').html(data.hasil);
                                    $('#lembaga').text(data.lembaga);
                                    $('#total-akhir').text(formatRupiah(total_akhir));
                                    $('#waktu').text(data.bulan + ' ' + data.tahun);
                                },
                                error: function(xhr, status, error) {
                                    console.log(xhr.responseText);
                                }
                            })
                        } else {
                            alert('Respon kosong. Pastikan server mengembalikan data dengan benar.');
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('Error: ' + xhr.responseText);
                    }
                });
            });
        })
        $(document).on('click', '.btn-detail', function() {

            var nama = $(this).data('nama');
            var gapok = $(this).data('gapok');
            var fungsional = $(this).data('fungsional');
            var kinerja = $(this).data('kinerja');
            var bpjs = $(this).data('bpjs');
            var walas = $(this).data('walas');
            var struktural = $(this).data('struktural');
            var penyesuaian = $(this).data('penyesuaian');
            var tambahan = $(this).data('tambahan');
            var total = $(this).data('total');
            var guru_id = $(this).data('guru_id');
            var id = $(this).data('id');
            var id_detail = $(this).data('id_detail');
            var sik = $(this).data('sik');
            var info = sik == 'PTY' ? 'Gaji Pokok' : 'Honor Insentif';
            var kriteria = $(this).data('kriteria');

            $('#nama').text(nama);
            $('#gapok').text(gapok);
            $('#fungsional').text(fungsional);
            $('#kinerja').text(kinerja);
            $('#bpjs').text(bpjs);
            $('#walas').text(walas);
            $('#struktural').text(struktural);
            $('#penyesuaian').text(penyesuaian);
            $('#tambahan').text(tambahan);
            $('#kriteria').text(kriteria);
            $('#total').text(total);
            $('#guru_id').val(guru_id);
            $('#nama_gaji').text(info);

            if ($('#btn-cetak-slip').length) {
                $('#btn-cetak-slip').attr('href', '<?= base_url("informasi/slip/") ?>' + id_detail);
            }

            $('#editModal').modal('show');

            $.ajax({
                type: "POST",
                url: "<?= base_url('gaji/getPotongan') ?>",
                data: {
                    gaji_id: id,
                    guru_id: guru_id,
                },
                dataType: 'json',
                success: function(data) {
                    // console.log(data);
                    let cleanedStr = total.replace(/[^\d]/g, '');
                    let num = parseInt(cleanedStr, 10);
                    let total_akhir = parseInt(num, 10) - parseInt(data.total_potong, 10);
                    $('#hasil-potong').empty();
                    $('#lembaga').empty();
                    $('#total-akhir').empty();
                    $('#waktu').empty();
                    $('#hasil-potong').html(data.hasil);
                    $('#lembaga').text(data.lembaga);
                    $('#total-akhir').text(formatRupiah(total_akhir));
                    $('#waktu').text(data.bulan + ' ' + data.tahun);
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                }
            })
        });

        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(number);
        }

        $(document).on('click', '#proses-kunci', function() {
            const id = $(this).data('id');

            var $button = $('#proses-kunci');

            $button.prop('disabled', true);
            $button.text('Processing...');

            $.ajax({
                url: '<?= base_url("gaji/kunci") ?>',
                type: 'POST',
                data: {
                    gaji_id: id,
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        window.location.reload()
                    } else {
                        console.error('Failed to update data');
                    }

                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                    console.log(status);
                    console.log(error);
                    // alert(xhr.responseText);
                }
            });
        })
        // $(document).on('click', '#proses-kunci', function() {
        //     const id = $(this).data('id');

        //     var $button = $('#proses-kunci');

        //     $.ajax({
        //         url: '<?= base_url("gaji/getGajiRinci") ?>',
        //         type: 'POST',
        //         data: {
        //             gaji_id: id,
        //         },
        //         dataType: 'json',
        //         success: function(response) {
        //             const hasil = $('#view-hasil');
        //             let berhasil = 0;
        //             let gagal = 0;
        //             let persen = 0;
        //             let errorMessages = [];

        //             $button.prop('disabled', true);
        //             $button.text('Processing...');

        //             function updateProgress() {
        //                 const total = Number(response.total); // Konversi ke number
        //                 if (isNaN(total) || total === 0) {
        //                     persen = 0; // Jika total tidak valid atau 0, set persen ke 0
        //                 } else {
        //                     // Hitung persentase
        //                     processed = berhasil + gagal; // Total yang sudah diproses
        //                     persen = (processed / total) * 100;
        //                 }

        //                 const errorList = errorMessages.length > 0 ?
        //                     `<ul class="text-start mt-2 text-danger" style="font-size: 0.9rem;">${errorMessages.map(msg => `<li>${msg}</li>`).join('')}</ul>` :
        //                     '';

        //                 hasil.html(`
        //                     <div class="text-center">
        //                         <strong class="mb-2">Proses kunci data ...</strong>
        //                         <div class="progress mb-3" style="height: 17px;">
        //                             <div class="progress-bar" role="progressbar" style="width: ${persen}%;" aria-valuenow="${persen}" aria-valuemin="0" aria-valuemax="100">${persen.toFixed(2)}%</div>
        //                         </div>
        //                     </div>
        //                     <strong class="mb-1">Total success : ${berhasil}</strong><br>
        //                     <strong class="mb-1 text-danger">Total error : ${gagal}</strong><br>
        //                      ${errorList}
        //                 `);
        //             }

        //             const ajaxRequests = response.data.map((item, index) => {
        //                 return new Promise((resolve) => {
        //                     setTimeout(() => {
        //                         $.ajax({
        //                             url: '<?= base_url("gaji/updateGaji") ?>',
        //                             type: 'POST',
        //                             data: {
        //                                 gaji_id: item.gaji_id,
        //                                 guru_id: item.guru_id,
        //                             },
        //                             dataType: 'json',
        //                             success: function(res) {
        //                                 if (res.status === 'success') {
        //                                     berhasil++;
        //                                 } else {
        //                                     gagal++;
        //                                     errorMessages.push(res.message || 'Gagal tanpa pesan');
        //                                 }
        //                                 updateProgress();
        //                             },
        //                             error: function() {
        //                                 gagal++;
        //                                 errorMessages.push(errorThrown || 'Error tidak diketahui');
        //                                 updateProgress();
        //                             },
        //                             complete: resolve // Menandai bahwa AJAX request selesai
        //                         });
        //                     }, index * 750); // Menambah jeda 500ms per request
        //                 });
        //             });

        //             Promise.all(ajaxRequests)
        //                 .then(function() {
        //                     // console.log('Semua permintaan AJAX selesai');
        //                     $.ajax({
        //                         url: '<?= base_url("gaji/updateKunci") ?>',
        //                         type: 'POST',
        //                         data: {
        //                             gaji_id: id,
        //                         },
        //                         dataType: 'json',
        //                         success: function(response) {
        //                             window.location.reload()
        //                         },
        //                         error: function() {
        //                             gagal++; // Tambahkan 1 ke variabel gagal
        //                             updateProgress(); // Perbarui tampilan progress bar dan hasil
        //                             console.error('Failed to update data');
        //                         }
        //                     });
        //                 })
        //                 .catch(function(error) {
        //                     console.error('Ada permintaan AJAX yang gagal', error);
        //                 });
        //         },
        //         error: function(xhr, status, error) {
        //             console.log(xhr.responseText);
        //             console.log(status);
        //             console.log(error);
        //             // alert(xhr.responseText);
        //         }
        //     });
        // })
    </script>

    <script>
        let state = {
            page: 1,
            perPage: 10,
            search: '',
            sortBy: 'nama',
            sortDir: 'ASC',
            total: 0,
            gaji_id: '<?= $idgaji ?>',
            bulan: <?= $datagaji->bulan ?>,
            tahun: <?= $datagaji->tahun ?>
        };

        function loadData() {
            const params = new URLSearchParams(state).toString();

            fetch(`<?= base_url('gaji/detail2') ?>?${params}`)
                .then(res => res.json())
                .then(res => {
                    renderTable(res.data, res);
                    renderPagination(res);
                    state.total = res.total;
                    info(state.perPage, state.page, state.total);
                });
        }

        function renderTable(data, meta) {
            const tbody = document.getElementById('tableBody');
            tbody.innerHTML = '';

            if (!Array.isArray(data)) return;
            let start = (meta.page - 1) * meta.perPage;

            data.forEach((row, index) => {
                let wrn = row.warna != '' && row.warna != null ? row.warna : 'black'
                tbody.innerHTML += `
                    <tr style="color: ${wrn}">
                        <td>${start + index + 1}</td>
                        <td>${row.nama}</td>
                        <td>${row.satminkal}</td>
                        <td>${row.jabatan}</td>
                        <td>${row.golongan}</td>
                        <td>${row.sik}</td>
                        <td>${row.ijazah}</td>
                        <td>${row.tmt} <span class="badge bg-secondary">${selisihTahun(row.tmt)} thn</span></td>
                        <td>${formatRupiah(row.total_gaji-row.potongan)}</td>
                        <td>
                            <button class="btn btn-xs btn-warning btn-detail" data-id="<?= $datagaji->gaji_id ?>" data-id_detail="${row['id_detail']}" data-nama="${row['nama']}" data-satminkal="${row['satminkal']}" data-sik="${row['sik']}" data-gapok="${formatRupiah(row['gapok'])}" data-fungsional="${formatRupiah(row['fungsional'])}" data-kinerja="${formatRupiah(row['kinerja'])}" data-bpjs="${formatRupiah(row['bpjs'])}" data-struktural="${formatRupiah(row['struktural'])}" data-walas="${formatRupiah(row['walas'])}" data-penyesuaian="${formatRupiah(row['penyesuaian'])}" data-tambahan="${formatRupiah(row['tambahan'])}" data-total="${formatRupiah(row['total_gaji'])}" data-guru_id="${row['guru_id']}" data-kriteria="${row['kriteria']}">Rincian</button>
                        </td>
                    </tr>
                `;
            });
        }

        function renderPagination(meta) {
            const pag = document.getElementById('pagination');
            pag.innerHTML = `
                <nav aria-label="Page navigation">
                    <ul class="pagination pagination-rounded"></ul>
                </nav>
            `;

            const ul = pag.querySelector('ul');

            const current = meta.page;
            const last = meta.lastPage;
            const delta = 1;

            function addButton(label, page = null, active = false, disabled = false) {

                let liClass = 'page-item';
                if (active) liClass += ' active';
                if (disabled) liClass += ' disabled';

                let content = label;
                if (label === '«') {
                    content = `<i class="icon-base bx bx-chevrons-left icon-sm"></i>`;
                    liClass += ' first';
                }
                if (label === '»') {
                    content = `<i class="icon-base bx bx-chevrons-right icon-sm"></i>`;
                    liClass += ' last';
                }

                ul.innerHTML += `
                    <li class="${liClass}">
                        <a class="page-link"
                        href="javascript:void(0);"
                        ${(!disabled && page) ? `onclick="goPage(${page})"` : ''}>
                        ${content}
                        </a>
                    </li>
                `;
            }

            // Prev
            addButton('«', current - 1, false, current === 1);

            // Page 1
            addButton(1, 1, current === 1);

            let start = Math.max(2, current - delta);
            let end = Math.min(last - 1, current + delta);

            if (start > 2) addButton('...', null, false, true);

            for (let i = start; i <= end; i++) {
                addButton(i, i, current === i);
            }

            if (end < last - 1) addButton('...', null, false, true);

            // Last page
            if (last > 1) addButton(last, last, current === last);

            // Next
            addButton('»', current + 1, false, current === last);
        }


        function goPage(page) {
            state.page = page;
            loadData();
        }

        function sort(field) {
            state.sortDir = state.sortDir === 'ASC' ? 'DESC' : 'ASC';
            state.sortBy = field;
            loadData();
        }

        function info(perpage, page, total) {
            document.getElementById('startRecord').textContent = (page - 1) * perpage + 1;
            document.getElementById('endRecord').textContent = Math.min(page * perpage, total);
            document.getElementById('totalRecords').textContent = total;
        }

        /* ===== EVENTS ===== */
        document.getElementById('search').addEventListener('input', e => {
            state.search = e.target.value;
            state.page = 1;
            loadData();
            info(state.perPage, state.page, state.total);
        });

        document.getElementById('perPage').addEventListener('change', e => {
            state.perPage = e.target.value;
            state.page = 1;
            loadData();
            info(state.perPage, state.page, 0);
        });

        /* INIT */
        loadData();

        function selisihTahun(a, b = new Date().toISOString().slice(0, 10)) {
            const awal = new Date(a);
            const akhir = new Date(b);
            if (awal > akhir) return 0;

            let y = akhir.getFullYear() - awal.getFullYear();
            if (
                akhir.getMonth() < awal.getMonth() ||
                (akhir.getMonth() === awal.getMonth() && akhir.getDate() < awal.getDate())
            ) y--;

            return y;
        }

        // Click handler for exporting slips collectively by Satminkal using silent iframe capture
        $(document).on('click', '.btn-export-bulk', function() {
            var satminkal = $(this).data('satminkal');
            var gaji_id = '<?= $idgaji ?>';

            $('#exportSlipModal').modal('hide');

            Swal.fire({
                title: 'Exporting Slips...',
                html: `Sedang memproses Satminkal <b>${satminkal}</b>.<br><br>
                       <div class="progress" style="height: 20px;">
                           <div id="bulk-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                       </div><br>
                       <span id="bulk-progress-text">Mengambil data guru...</span>`,
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    $.ajax({
                        url: '<?= base_url("informasi/get_slips_by_satminkal") ?>',
                        type: 'POST',
                        data: {
                            gaji_id: gaji_id,
                            satminkal: satminkal
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                var slips = response.slips;
                                if (slips.length === 0) {
                                    Swal.fire('Kosong', 'Tidak ada data guru untuk satminkal ini.', 'warning');
                                    return;
                                }

                                var zip = new JSZip();
                                var processed = 0;
                                var total = slips.length;

                                function processNext() {
                                    if (processed < total) {
                                        var slip = slips[processed];
                                        var clean_nama = slip.detail.nama.replace(/[^a-z0-9]/gi, '_').toLowerCase();
                                        var filename = 'Slip_Gaji_' + clean_nama + '_' + response.bulan_nama + '_' + response.tahun + '.png';

                                        $('#bulk-progress-text').text(`Mengekstrak slip: ${slip.detail.nama} (${processed + 1}/${total})`);

                                        // Create a temporary iframe to load the slip page with 0.01 opacity in the active viewport
                                        var iframe = document.createElement('iframe');
                                        iframe.style.position = 'fixed';
                                        iframe.style.left = '0';
                                        iframe.style.top = '0';
                                        iframe.style.width = '780px';
                                        iframe.style.height = '1200px';
                                        iframe.style.zIndex = '-9999';
                                        iframe.style.opacity = '0.01';
                                        iframe.style.pointerEvents = 'none';
                                        iframe.style.border = 'none';
                                        iframe.src = '<?= base_url("informasi/slip/") ?>' + slip.detail.id_detail + '?mode=print_silent';

                                        document.body.appendChild(iframe);

                                        iframe.onload = function() {
                                            // Wait for all web fonts to load inside the iframe before capturing
                                            iframe.contentWindow.document.fonts.ready.then(function() {
                                                setTimeout(() => {
                                                    var captureEl = iframe.contentWindow.document.querySelector("#capture");
                                                    if (!captureEl) {
                                                        console.error("Capture element not found in iframe");
                                                        document.body.removeChild(iframe);
                                                        processed++;
                                                        processNext();
                                                        return;
                                                    }

                                                    html2canvas(captureEl, {
                                                        useCORS: true,
                                                        allowTaint: true,
                                                        scale: 2
                                                    }).then(canvas => {
                                                        canvas.toBlob(function(blob) {
                                                            zip.file(filename, blob);

                                                            // Clean up iframe
                                                            document.body.removeChild(iframe);

                                                            processed++;
                                                            var percent = Math.round((processed / total) * 100);
                                                            $('#bulk-progress-bar').css('width', percent + '%').attr('aria-valuenow', percent).text(percent + '%');

                                                            processNext();
                                                        }, 'image/png');
                                                    }).catch(err => {
                                                        console.error("Canvas capture failed", err);
                                                        document.body.removeChild(iframe);
                                                        processed++;
                                                        processNext();
                                                    });
                                                }, 400); // 400ms safety delay after fonts are ready
                                            });
                                        };
                                    } else {
                                        $('#bulk-progress-text').text('Mengompres berkas ZIP...');
                                        zip.generateAsync({ type: 'blob' }).then(function(content) {
                                            var zipFilename = 'Slip_Gaji_' + satminkal.replace(/[^a-z0-9]/gi, '_') + '_' + response.bulan_nama + '_' + response.tahun + '.zip';
                                            var link = document.createElement('a');
                                            link.download = zipFilename;
                                            link.href = URL.createObjectURL(content);
                                            document.body.appendChild(link);
                                            link.click();
                                            document.body.removeChild(link);

                                            Swal.fire({
                                                title: 'Berhasil!',
                                                text: `Berhasil mengekspor ${total} slip gaji untuk Satminkal ${satminkal}.`,
                                                icon: 'success'
                                            });
                                        });
                                    }
                                }

                                processNext();
                            } else {
                                Swal.fire('Error', response.message || 'Gagal mengambil data.', 'error');
                            }
                        },
                        error: function(xhr) {
                            Swal.fire('Error', 'Gagal menghubungi server.', 'error');
                        }
                    });
                }
            });
        });
    </script>