    <?php include 'head.php' ?>

    <link rel="stylesheet" href="<?= base_url('assets/vendor/libs/flatpickr/flatpickr.css') ?>">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-lg-12 mb-4 order-0">
                <div class="card">
                    <h5 class="card-header">
                        Data Guru/Karyawan
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
                                    type="text"
                                    id="search"
                                    class="form-control"
                                    placeholder="Cari data...">
                            </div>
                        </div>
                    </div>

                    <div class="card-datatable table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Lembaga</th>
                                    <th>Jabatan</th>
                                    <th>Krit</th>
                                    <th>SIK</th>
                                    <th>Ijazah</th>
                                    <th>TMT</th>
                                    <th>Golongan</th>
                                    <th>Ket</th>
                                    <!-- <th>#</th> -->
                                </tr>
                            </thead>
                            <tbody id="tableBody"></tbody>
                        </table>
                        <br>
                    </div>

                    <!-- Pagination -->
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
                        <nav aria-label="Pagination">
                            <ul class="pagination pagination-sm mb-0" id="pagination">
                                <!-- diisi via JS -->
                            </ul>
                        </nav>

                    </div>

                </div>
                <!-- Bootstrap Table with Caption -->

            </div>

        </div>

    </div>

    <!-- / Content -->
    <?php include 'foot.php' ?>

    <script src="<?= base_url('assets/vendor/libs/flatpickr/flatpickr.js') ?>"></script>

    <script>
        let state = {
            page: 1,
            perPage: 10,
            search: '',
            sortBy: 'nama',
            sortDir: 'ASC',
            total: 0
        };

        function loadData() {
            const params = new URLSearchParams(state).toString();

            fetch(`<?= base_url('guru/datatable') ?>?${params}`)
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
                        <td>${row.kriteria}</td>
                        <td>${row.sik}</td>
                        <td>${row.ijazah}</td>
                        <td>${row.tmt}</td>
                        <td>${row.golongan}</td>
                        <td>${row.ket}</td>
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
    </script>