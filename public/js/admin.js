document.addEventListener("DOMContentLoaded", function () {

    initFlashAlert();
    initUserDropdown();
    initActionDropdown();
    initSidebarSubmenu();
    initSubmenuActive();
    initExportFilter();
    initTableEdit();
    initModal();
    initSearch();

});


// ================= USER DROPDOWN =================
function initUserDropdown() {

    window.toggleDropdown = function () {
        const dropdown = document.getElementById("userDropdown");
        if (dropdown) dropdown.classList.toggle("show");
    };

    document.addEventListener("click", function (event) {
        if (!event.target.closest(".user-menu")) {
            const dropdown = document.getElementById("userDropdown");
            if (dropdown) dropdown.classList.remove("show");
        }
    });

}

let activeMenu = null;

// ================= ACTION DROPDOWN =================
function initActionDropdown() {

    window.toggleActionDropdown = function (el) {

        const container = el.closest(".dropdown-action");
        const menu = container.querySelector(".action-menu");

        if (!menu) return;

        document.querySelectorAll(".action-menu").forEach(m => {
            if (m !== menu) {
                m.classList.remove("show");
            }
        });

        const rect = el.getBoundingClientRect();

        menu.style.position = "fixed";
        menu.style.top = rect.bottom + "px";
        menu.style.left = rect.left + "px";

        menu.classList.toggle("show");
    };

    document.addEventListener("click", function (e) {

        if (!e.target.closest(".dropdown-action")) {
            document.querySelectorAll(".action-menu").forEach(menu => {
                menu.classList.remove("show");
            });
        }

    });

}

// ================= SIDEBAR SUBMENU =================
function initSidebarSubmenu() {

    document.querySelectorAll(".nav-item.has-submenu > .menu-content").forEach(menu => {

        menu.addEventListener("click", function (e) {

            // cegah pindah halaman
            e.preventDefault();

            const parent = this.parentElement;

            parent.classList.toggle("active");

            document.querySelectorAll(".nav-item.has-submenu").forEach(other => {
                if (other !== parent) {
                    other.classList.remove("active");
                }
            });

        });

    });

}


// ================= SUBMENU ACTIVE =================
function initSubmenuActive() {

    document.querySelectorAll(".nav-sub-item a").forEach(link => {

        link.addEventListener("click", function () {

            document.querySelectorAll(".nav-sub-item")
                .forEach(sub => sub.classList.remove("active"));

            this.parentElement.classList.add("active");

            const parentMenu = this.closest(".nav-item.has-submenu");
            if (parentMenu) parentMenu.classList.add("active");

        });

    });

}


// ================= EXPORT FILTER =================
function initExportFilter() {

    const exportForm = document.querySelector(".export-form");

    if (!exportForm) return;

    exportForm.addEventListener("submit", function () {

        const dusun = document.querySelector(".filter-dusun")?.value;
        const input = document.getElementById("exportDusun");

        if (input) input.value = dusun;

    });

}

// ================= TABLE EDIT MODE =================
function initTableEdit() {

    // ================= EDIT =================
    window.editRow = function (el) {

        // ambil row langsung dari tabel
        const row = el.closest("tr");

        if (!row) {
            console.error("Row tidak ditemukan");
            return;
        }

        if (row.classList.contains("editing")) return;

        row.classList.add("editing");

        const cells = row.children;
        const totalCells = cells.length;

        // simpan kondisi awal
        row.dataset.originalHtml = row.innerHTML;

        for (let i = 1; i < totalCells - 1; i++) {

            const cell = cells[i];
            const currentValue = cell.dataset.raw || cell.innerText.trim();
            const field = cell.dataset.field;

            // kalau tidak ada field → skip
            if (!field) continue;

            // ================= SELECT KHUSUS =================

            // ROLE
            if (cell.classList.contains("col-role")) {

                const value = currentValue.toLowerCase();

                cell.innerHTML = `
                <select name="${field}" class="edit-select">
                    <option value="admin" ${value === 'admin' ? 'selected' : ''}>Admin</option>
                    <option value="kadus" ${value === 'kadus' ? 'selected' : ''}>Kadus</option>
                </select>
            `;
                continue;
            }

            // DUSUN
            if (cell.dataset.field === "dusun_id") {

                const dusuns = JSON.parse(cell.dataset.dusun);

                let options = `<option value="">-- Pilih Dusun --</option>`;

                dusuns.forEach(d => {
                    options += `<option value="${d.id}" ${currentValue === d.nama_dusun ? 'selected' : ''}>
                    ${d.nama_dusun}
                </option>`;
                });

                cell.innerHTML = `
                <select name="${field}" class="edit-select" onchange="filterRtRw(this)">
                    ${options}
                </select>
            `;
                continue;
            }

            // ================= RT/RW KHUSUS =================
            if (cell.dataset.field === "rt_rw_id") {

                // ambil semua RT/RW dari dataset row
                const rtRws = JSON.parse(row.dataset.rtRws || '[]');
                const dusunId = row.dataset.dusunId;
                const selectedRtRwId = row.dataset.rtRwId;

                // filter RT/RW sesuai dusun
                const filteredRtRw = rtRws.filter(r => r.dusun_id == dusunId);

                let options = `<option value="">-- Pilih RT/RW --</option>`;
                filteredRtRw.forEach(r => {
                    const selected = r.id == selectedRtRwId ? 'selected' : '';
                    options += `<option value="${r.id}" ${selected}>RT ${r.rt} / RW ${r.rw}</option>`;
                });

                cell.innerHTML = `<select name="${field}" class="edit-select">${options}</select>`;
                continue;
            }

            // BIDANG DROPDOWN
            if (field === "bidang_id") {

                const selectedValue = cell.dataset.value;
                const bidangList = JSON.parse(row.dataset.bidang || '[]');

                let options = `<option value="">-- Pilih Bidang --</option>`;

                bidangList.forEach(b => {
                    const selected = b.id == selectedValue ? 'selected' : '';
                    options += `<option value="${b.id}" ${selected}>${b.nama_bidang}</option>`;
                });

                cell.innerHTML = `<select name="bidang_id" class="edit-select" onchange="filterSubBidang(this)">
        ${options}
    </select>`;

                continue;
            }
            //SUBBIDANG DROPDOWN
            if (field === "sub_bidang_id") {

                const selectedValue = cell.dataset.value;
                const subList = JSON.parse(row.dataset.subbidang || '[]');

                let options = `<option value="">-- Pilih Sub Bidang --</option>`;

                subList.forEach(s => {
                    const selected = s.id == selectedValue ? 'selected' : '';
                    options += `<option value="${s.id}" data-bidang="${s.bidang_id}" ${selected}>
            ${s.nama_sub_bidang}
        </option>`;
                });

                cell.innerHTML = `<select name="sub_bidang_id" class="edit-select" onchange="filterKegiatan(this)">
        ${options}
    </select>`;

                continue;
            }

            //VOLUME KHUSUS
            if (field === "satuan") {

                cell.innerHTML = `
        <select name="satuan" class="edit-select">
            <option value="m" ${currentValue === 'm' ? 'selected' : ''}>m</option>
            <option value="m2" ${currentValue === 'm2' ? 'selected' : ''}>m²</option>
            <option value="m3" ${currentValue === 'm3' ? 'selected' : ''}>m³</option>
            <option value="unit" ${currentValue === 'unit' ? 'selected' : ''}>Unit</option>
            <option value="buah" ${currentValue === 'buah' ? 'selected' : ''}>Buah</option>
            <option value="orang" ${currentValue === 'orang' ? 'selected' : ''}>Orang</option>
            <option value="persen" ${currentValue === 'persen' ? 'selected' : ''}>%</option>
        </select>
    `;
                continue;
            }

            //KEGIATAN DROPDOWN
            if (field === "kegiatan_id") {

                const selectedValue = cell.dataset.value; // 🔥 WAJIB
                const kegiatanList = JSON.parse(row.dataset.kegiatan || '[]');

                let options = `<option value="">-- Pilih Kegiatan --</option>`;

                kegiatanList.forEach(k => {
                    const selected = k.id == selectedValue ? 'selected' : '';
                    options += `<option value="${k.id}" data-sub="${k.sub_bidang_id}" ${selected}>
            ${k.nama_kegiatan}
        </option>`;
                });

                cell.innerHTML = `<select name="kegiatan_id" class="edit-select">
        ${options}
    </select>`;

                continue;
            }

            // ================= TANGGAL KHUSUS =================
            if (field === "tanggal_kepemilikan") {

                cell.innerHTML = `
        <input 
            type="date"
            name="${field}"
            value="${currentValue}"
            class="edit-input"
        >
    `;
                continue;
            }

            //Volume dan satuan digabung jadi 1 kolom di menu penyusunan rpjmdes
            if (field === "volume") {

                let raw = cell.dataset.raw || "";

                // 🔥 ambil angka saja kalau bukan format dimensi (x)
                if (!raw.includes('x')) {
                    let num = parseFloat(raw);

                    if (!isNaN(num)) {
                        // hilangkan .00
                        raw = (num % 1 === 0) ? parseInt(num) : num;
                    }
                }

                cell.innerHTML = `
        <input
            type="text"
            name="volume"
            value="${raw}"
            class="edit-input"
            placeholder="Contoh: 200x1.5 atau 120"
        >
    `;
                continue;
            }

            // ================= INPUT BIASA =================
            cell.innerHTML = `
            <input 
                type="text"
                name="${field}"
                value="${currentValue}"
                class="edit-input"
            >
        `;
        }

        // tombol aksi
        const actionCell = cells[totalCells - 1];

        actionCell.innerHTML = `
        <div class="edit-actions">
            <button type="button" class="btn-save" onclick="saveRow(this)">Simpan</button>
            <button type="button" class="btn-cancel" onclick="cancelEdit(this)">Batal</button>
        </div>
    `;
    };


    // ================= SAVE =================
    window.saveRow = function (el) {

        const row = el.closest("tr");
        const url = row.dataset.url;

        if (!url) {
            console.error("URL tidak ditemukan");
            return;
        }

        let data = {};

        // ambil semua kolom yang punya data-field
        const cells = row.querySelectorAll("td[data-field]");

        cells.forEach(td => {

            const field = td.dataset.field;
            let input = td.querySelector("input, select");
            if (!input) return;

            let value;

            if (field === 'waktu') {
                if (input.tagName === 'SELECT' && input.multiple) {
                    value = Array.from(input.selectedOptions).map(o => o.value);
                } else if (input.value) {
                    value = input.value.split(',').map(v => v.trim()).filter(v => v);
                } else {
                    value = [];
                }
            } else {
                value = input.value;

                // khusus numeric
                if (td.dataset.type === "numeric") {
                    value = value.replace(/\./g, '').replace(',', '.');
                }
            }

            /*if (field === "volume") {
                const original = td.dataset.raw;
                const newValue = input.value;

                if (original !== newValue) {
                    data[field] = newValue;
                }
                return;
            }*/

            if (field === "volume") {
                data[field] = input.value.trim();
                return;
            }

            // simpan dulu ke data
            data[field] = value;

        });

        fetch(url, {
            method: "PUT",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        })
            .then(async res => {

                const result = await res.json();

                if (!res.ok) {

                    console.error("Laravel validation:", result);

                    if (result.errors) {
                        let msg = Object.values(result.errors).flat().join("\n");
                        alert(msg);
                    } else {
                        alert(result.message || "Terjadi error");
                    }

                    throw result;
                }

                return result;

            })
            .then(res => {

                if (res.success) {
                    showAlert({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Data berhasil diperbarui',
                        timer: 1500
                    });
                    setTimeout(() => location.reload(), 1500);
                } else {
                    alert("Gagal menyimpan");
                }

            })
            .catch(err => console.error(err));
    };

    // ================= FILTER RT/RW =================
    window.filterRtRw = function (dusunSelect) {
        const row = dusunSelect.closest('tr');
        const rtRwCell = row.querySelector('td[data-field="rt_rw_id"]');
        const rtRws = JSON.parse(row.dataset.rtRws || '[]');
        const selectedDusunId = dusunSelect.value;

        // filter RT/RW sesuai dusun
        const filteredRtRw = rtRws.filter(r => r.dusun_id == selectedDusunId);

        let options = `<option value="">-- Pilih RT/RW --</option>`;
        filteredRtRw.forEach(r => {
            options += `<option value="${r.id}">RT ${r.rt} / RW ${r.rw}</option>`;
        });

        rtRwCell.innerHTML = `<select name="rt_rw_id" class="edit-select">${options}</select>`;
    };


    // ================= CANCEL =================
    window.cancelEdit = function (el) {

        const row = el.closest("tr");

        if (row.dataset.originalHtml) {
            row.innerHTML = row.dataset.originalHtml;
        }

        row.classList.remove("editing");

    };
}

//FILTER SUBBIDANG
window.filterSubBidang = function (el) {

    const row = el.closest("tr");
    const bidangId = el.value;

    const subSelect = row.querySelector('select[name="sub_bidang_id"]');
    const kegiatanSelect = row.querySelector('select[name="kegiatan_id"]');

    if (!subSelect) return;

    const allOptions = subSelect.querySelectorAll("option");

    allOptions.forEach(opt => {

        if (!opt.value) return; // skip placeholder

        const bId = opt.getAttribute("data-bidang");

        opt.style.display = (bId == bidangId) ? "block" : "none";
    });

    // reset pilihan
    subSelect.value = "";

    // 🔥 reset kegiatan juga
    if (kegiatanSelect) {
        kegiatanSelect.value = "";

        kegiatanSelect.querySelectorAll("option").forEach(opt => {
            opt.style.display = "none";
        });

        kegiatanSelect.querySelector('option[value=""]').style.display = "block";
    }
};

//FILTER KEGIATAN
window.filterKegiatan = function (el) {

    const row = el.closest("tr");
    const subId = el.value;

    const kegiatanSelect = row.querySelector('select[name="kegiatan_id"]');

    if (!kegiatanSelect) return;

    const allOptions = kegiatanSelect.querySelectorAll("option");

    allOptions.forEach(opt => {

        if (!opt.value) return;

        const sId = opt.getAttribute("data-sub");

        opt.style.display = (sId == subId) ? "block" : "none";
    });

    kegiatanSelect.value = "";
};

// ================= MODAL =================
function initModal() {

    window.openModal = function (modalId) {

        const modal = document.getElementById(modalId);

        if (modal) {
            modal.classList.add("show");

            if (modalId === 'periodeModal') {
                initValidasiTahun();
            }
        }

    };

    window.closeModal = function (modalId) {

        const modal = document.getElementById(modalId);
        if (modal) modal.classList.remove("show");

    };

    window.addEventListener("click", function (event) {

        if (event.target.classList.contains("modal")) {
            event.target.classList.remove("show");
        }

    });

}


// ================= DELETE MODAL =================
window.showDeletePopup = function (el, type = 'delete') {
    // Ambil URL dari tombol hapus
    const url = el.dataset.url;
    const modal = document.getElementById("deleteModal");

    if (!modal) return;

    const message = modal.querySelector(".popup-message");
    const btn = modal.querySelector(".btn-delete");

    if (type === 'reject') {
        if (message) message.innerText = "Apakah Anda yakin ingin menolak kegiatan ini?";
        if (btn) btn.innerText = "Tolak";
    } else {
        if (message) message.innerText = "Apakah Anda yakin ingin menghapus data ini?";
        if (btn) btn.innerText = "Hapus";
    }

    // Set action form di modal
    const form = modal.querySelector("form");
    if (form && url) form.action = url;

    // Tampilkan modal
    modal.classList.add("show");
};

// ================= HANDLE SUBMIT DELETE =================
document.addEventListener('DOMContentLoaded', function () {
    const deleteModal = document.getElementById('deleteModal');
    if (!deleteModal) return;

    const form = deleteModal.querySelector('form');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault(); // cegah reload page

        const url = form.action;
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const btnDelete = deleteModal.querySelector('.btn-delete');
        const isReject = btnDelete && btnDelete.innerText === 'Tolak';
        const successText = isReject ? 'Data berhasil ditolak' : 'Data berhasil dihapus';
        const errorText = isReject ? 'Gagal menolak data' : 'Gagal menghapus data';
        const errorSystemText = isReject ? 'Terjadi kesalahan saat menolak data' : 'Terjadi kesalahan saat menghapus data';

        fetch(url, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            }
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // hapus row di tabel
                    const id = url.split('/').pop();
                    const row = document.querySelector(`tr[data-id='${id}']`);
                    if (row) row.remove();

                    // tutup modal
                    deleteModal.classList.remove('show');

                    // tampilkan SweetAlert sukses
                    showAlert({
                        icon: 'success',
                        title: 'Berhasil',
                        text: successText
                    });
                } else {
                    // SweetAlert error
                    showAlert({
                        icon: 'error',
                        title: 'Gagal',
                        text: errorText
                    });
                }
            })
            .catch(err => {
                console.error(err);
                showAlert({
                    icon: 'error',
                    title: 'Error',
                    text: errorSystemText
                });
            });
    });

    // klik di luar modal untuk tutup
    window.addEventListener("click", function (event) {
        if (event.target === deleteModal) {
            deleteModal.classList.remove("show");
        }
    });
});

// ================= SWEETALERT GLOBAL =================
function showAlert({ icon = 'success', title = '', text = '', timer = 2000, showConfirmButton = false }) {
    if (typeof Swal === 'undefined') {
        alert(text); // fallback
        return;
    }

    Swal.fire({
        icon,
        title,
        text,
        timer,
        showConfirmButton
    });
}


// ================= SIDEBAR TOGGLE =================
function toggleSidebar() {

    const sidebar = document.querySelector(".sidebar");
    const overlay = document.querySelector(".sidebar-overlay");

    sidebar.classList.toggle("active");

    if (sidebar.classList.contains("active")) {
        overlay.style.display = "block";
    } else {
        overlay.style.display = "none";
    }

}


// ================= VALIDASI PERIODE RPJMDES =================
let validasiSudahDipasang = false;

function initValidasiTahun() {

    // 🔥 biar ga dobel event
    if (validasiSudahDipasang) return;

    const mulai = document.querySelector('[name="tahun_mulai"]');
    const selesai = document.querySelector('[name="tahun_selesai"]');

    if (!mulai || !selesai) return;

    selesai.addEventListener("change", function () {
        const valMulai = parseInt(mulai.value);
        const valSelesai = parseInt(selesai.value);

        if (!isNaN(valMulai) && !isNaN(valSelesai) && valSelesai <= valMulai) {
            alert("Tahun selesai harus lebih besar dari tahun mulai");
            selesai.value = "";
        }
    });

    mulai.addEventListener("change", function () {
        const valMulai = parseInt(mulai.value);
        const valSelesai = parseInt(selesai.value);

        if (!isNaN(valMulai) && !isNaN(valSelesai) && valSelesai <= valMulai) {
            alert("Tahun mulai tidak boleh lebih besar dari tahun selesai");
            mulai.value = "";
        }
    });

    validasiSudahDipasang = true;
}

// ================= SEARCH FILTER =================
function initSearch() {
    const searchInput = document.querySelector('.search-input');
    if (!searchInput) return;

    searchInput.addEventListener('keyup', function () {
        const filter = this.value.toLowerCase();
        const tbody = document.querySelector('table tbody');
        if (!tbody) return;
        
        const rows = tbody.querySelectorAll('tr');

        rows.forEach(row => {
            // Abaikan baris kosong/pesan "belum ada data"
            if (row.querySelector('td[colspan]')) return;

            let text = row.innerText.toLowerCase();
            if (text.includes(filter)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
}

// ================= KONFIRMASI TETAPKAN RPJMDes =================
function confirmTetapkan() {
    const konfirmasi = confirm("Apakah Anda yakin ingin menetapkan RPJMDes ini?");

    if (konfirmasi) {
        alert("RPJMDes berhasil ditetapkan!");

    } else {
        return false;
    }
}

function toggleStatus(el, status) {

    const userId = el.dataset.id;

    const tokenElement = document.querySelector('meta[name="csrf-token"]');

    if (!tokenElement) {
        alert("CSRF token tidak ditemukan");
        return;
    }

    const token = tokenElement.getAttribute('content');

    if (!confirm('Yakin ingin mengubah status user ini?')) return;

    fetch(`/admin/pengguna/status/${userId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': token,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            is_active: status
        })
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showAlert({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Status pengguna berhasil diperbarui',
                    timer: 1500
                });
                setTimeout(() => location.reload(), 1500);
            }
        })
        .catch(err => {
            console.error(err);
        });

}

//FILTER RTRW DI FORM USULAN (WARGA)
document.addEventListener("DOMContentLoaded", function () {

    const dusun = document.getElementById("dusunSelect");
    const rtRw = document.getElementById("rtRwSelect");

    if (!dusun) return;

    dusun.addEventListener("change", function () {

        let dusunId = this.value;

        if (dusunId) {
            fetch(`/dusun/${dusunId}/rtrw`)
                .then(res => res.json())
                .then(data => {

                    let options = '<option value="">Pilih RT / RW</option>';

                    data.forEach(item => {
                        options += `<option value="${item.id}">
                        RT ${item.rt} / RW ${item.rw}
                    </option>`;
                    });

                    rtRw.innerHTML = options;
                });

        } else {
            rtRw.innerHTML = '<option value="">Pilih RT / RW</option>';
        }

    });

});

document.addEventListener("DOMContentLoaded", function () {

    const dusunModal = document.querySelector('#tambahModal select[name="dusun_id"]');
    const rtRwModal = document.getElementById("rtRwSelectModal");

    if (!dusunModal || !rtRwModal) return;

    dusunModal.addEventListener("change", function () {

        let dusunId = this.value;

        if (!dusunId) {
            rtRwModal.innerHTML = '<option value="">Pilih RT/RW</option>';
            return;
        }

        rtRwModal.innerHTML = '<option value="">Loading...</option>';

        fetch(`/dusun/${dusunId}/rtrw`)
            .then(res => res.json())
            .then(data => {

                let options = '<option value="">Pilih RT/RW</option>';

                data.forEach(r => {
                    options += `<option value="${r.id}">
                        RT ${r.rt} / RW ${r.rw}
                    </option>`;
                });

                rtRwModal.innerHTML = options;
            })
            .catch(() => {
                rtRwModal.innerHTML = '<option value="">Gagal load data</option>';
            });

    });

});

//MODAL KELOLA USULAN
const dusunSelect = document.querySelector('select[name="dusun_id"]');

if (dusunSelect) {
    dusunSelect.addEventListener('change', function () {
        let dusunId = this.value;
        let rtRwSelect = document.getElementById('rtRwSelectModal');

        if (!rtRwSelect) return;

        rtRwSelect.innerHTML = '<option value="">Loading...</option>';

        fetch(`/dusun/${dusunId}/rtrw`)
            .then(res => res.json())
            .then(data => {
                rtRwSelect.innerHTML = '<option value="">Pilih RT/RW</option>';
                data.forEach(r => {
                    rtRwSelect.innerHTML += `<option value="${r.id}">RT ${r.rt} / RW ${r.rw}</option>`;
                });
            });
    });
}

//MODAL APPROVE DAN REJECT
window.confirmApprove = function (el) {
    const id = el.dataset.id;

    Swal.fire({
        title: 'Setujui usulan?',
        text: "Data akan masuk ke RPJMDes",
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Setujui',
        cancelButtonText: 'Batal'
    }).then((result) => {

        if (result.isConfirmed) {

            Swal.fire({
                title: 'Memproses...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch(`/admin/usulan/approve/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Usulan disetujui',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => location.reload());
                    }
                })
                .catch(() => {
                    Swal.fire('Error', 'Terjadi kesalahan', 'error');
                });

        }

    });
};

window.confirmReject = function (el) {
    const id = el.dataset.id;

    Swal.fire({
        title: 'Tolak usulan?',
        text: "Data tidak akan masuk ke RPJMDes",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Tolak',
        cancelButtonText: 'Batal'
    }).then((result) => {

        if (result.isConfirmed) {

            Swal.fire({
                title: 'Memproses...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            fetch(`/admin/usulan/reject/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Usulan ditolak',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => location.reload());
                    }
                })
                .catch(() => {
                    Swal.fire('Error', 'Terjadi kesalahan', 'error');
                });

        }

    });
};

window.confirmApproveAll = function () {

    Swal.fire({
        title: 'Setujui semua usulan?',
        text: "Semua data akan masuk ke RPJMDes",
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Setujui Semua',
        cancelButtonText: 'Batal'
    }).then((result) => {

        if (result.isConfirmed) {

            Swal.fire({
                title: 'Memproses...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            fetch(`/admin/usulan/approve-all`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Semua usulan disetujui',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => location.reload());
                    }
                })
                .catch(() => {
                    Swal.fire('Error', 'Terjadi kesalahan', 'error');
                });

        }

    });
};

// ================= SUBMIT TETAPKAN RPJMDes =================
document.addEventListener("DOMContentLoaded", function () {
    window.submitTetapkan = function () {
        const btn = document.getElementById('btnTetapkan');
        if (!btn) return console.error("btnTetapkan tidak ditemukan");

        const url = btn.dataset.url;

        fetch(url, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                "Content-Type": "application/json",
                "Accept": "application/json"
            },
            body: JSON.stringify({})
        })
            .then(async res => {

                const text = await res.text(); // ambil raw response

                let data;
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    console.error("Response bukan JSON:", text);
                    throw new Error("Server error / response tidak valid");
                }

                // 🔥 FIX UTAMA (jangan throw object)
                if (!res.ok) {
                    throw new Error(data.message || 'Terjadi kesalahan');
                }

                return data;
            })
            .then(data => {
                if (data.success) {

                    closeModal('confirmModal');

                    showAlert({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data.message,
                    });

                    // 🔥 FLAG supaya tidak double alert setelah reload
                    localStorage.setItem('skip_alert', 'tetapkan');

                    setTimeout(() => location.reload(), 1500);
                }
            })
            .catch(err => {
                console.error(err);

                showAlert({
                    icon: 'error',
                    title: 'Gagal',
                    text: err.message || 'Terjadi kesalahan'
                });
            });
    }
});

function initFlashAlert() {
    const flashEl = document.getElementById('flash-data');
    if (!flashEl) return;

    const skipAlert = localStorage.getItem('skip_alert');

    // hanya skip untuk tetapkan
    if (skipAlert === 'tetapkan') {
        localStorage.removeItem('skip_alert');
        return;
    }

    const success = JSON.parse(flashEl.dataset.success || 'null');
    const error = JSON.parse(flashEl.dataset.error || 'null');
    const errors = JSON.parse(flashEl.dataset.errors || '[]');

    if (success) {
        showAlert({
            icon: 'success',
            title: 'Berhasil',
            text: success
        });
    }

    if (error) {
        showAlert({
            icon: 'error',
            title: 'Gagal',
            text: error
        });
    }

    if (errors.length > 0) {
        showAlert({
            icon: 'error',
            title: 'Validasi Gagal',
            text: errors.join('\n')
        });
    }
}