document.addEventListener("DOMContentLoaded", function () {

    initSidebarSubmenu();
    initSubmenuActive();
    initActionDropdown();
    initInlineEditTable();
    initModal();
    initSearch();
    initUserDropdown();
    initFlashAlert();

});


/* ================= SIDEBAR SUBMENU ================= */

function initSidebarSubmenu() {

    document.querySelectorAll(".nav-item.has-submenu > .menu-content").forEach(menu => {

        menu.addEventListener("click", function () {

            const parent = this.parentElement;

            parent.classList.toggle("active");

            document.querySelectorAll(".nav-item.has-submenu").forEach(other => {
                if (other !== parent) other.classList.remove("active");
            });

        });

    });

}

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

/* ================= SUBMENU ACTIVE ================= */

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


/* ================= ACTION DROPDOWN ================= */

function initActionDropdown() {

    window.toggleActionDropdown = function (el) {

        const menu = el.nextElementSibling;

        document.querySelectorAll(".action-menu").forEach(m => {
            if (m !== menu) m.classList.remove("show");
        });

        if (!menu.classList.contains("show")) {

            const rect = el.getBoundingClientRect();

            menu.style.position = "fixed";
            menu.style.top = rect.bottom + "px";
            menu.style.left = (rect.left - 80) + "px";

            menu.classList.add("show");

        } else {
            menu.classList.remove("show");
        }

    };

    document.addEventListener("click", function (event) {

        if (!event.target.closest(".dropdown-action")) {

            document.querySelectorAll(".action-menu").forEach(menu => {
                menu.classList.remove("show");
            });

        }

    });

}

// ================= TABLE EDIT MODE =================
function initInlineEditTable() {

    window.editRow = function (el) {

        const row = el.closest("tr");

        if (row.classList.contains("editing")) return;

        row.classList.add("editing");

        const cells = row.children;
        const totalCells = cells.length;

        row.dataset.originalHtml = row.innerHTML;

        // EDIT HANYA KOLOM VOLUME (index 3)
        const volumeCell = cells[3];
        const currentValue = volumeCell.innerText.trim();

        volumeCell.innerHTML = `
    <input type="number" value="${currentValue}" class="edit-input">
`;
        const actionCell = cells[totalCells - 1];

        actionCell.innerHTML = `
            <div class="edit-actions">
                <button type="button" class="btn-save" onclick="saveRow(this)">Simpan</button>
                <button type="button" class="btn-cancel" onclick="cancelEdit(this)">Batal</button>
            </div>
        `;

    };


    window.saveRow = function (el) {

        const row = el.closest("tr");
        const id = row.dataset.id;

        const cells = row.children;

        const volume = cells[3].querySelector("input").value;

        fetch("/kadus/pengajuanrkp/update/" + id, {

            method: "PUT",

            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content")
            },

            body: JSON.stringify({
                panjang: volume,
                lebar: null,
                tinggi: null,
                lokasi: row.children[4].innerText
            })

        })
            .then(res => res.json())
            .then(res => {

                if (res.success) {

                    showAlert({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Data berhasil diperbarui',
                        timer: 1500
                    });

                    cells[3].innerText = volume;

                    row.classList.remove("editing");

                    restoreActionMenu(row);

                } else {

                    showAlert({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Gagal menyimpan perubahan'
                    });

                }

            })
            .catch(err => {

                console.error(err);
                showAlert({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi error saat update'
                });

            });

    };


    window.cancelEdit = function (el) {

        const row = el.closest("tr");

        if (row.dataset.originalHtml) {
            row.innerHTML = row.dataset.originalHtml;
        }

        row.classList.remove("editing");

    };


    function restoreActionMenu(row) {

        const totalCells = row.children.length;
        const actionCell = row.children[totalCells - 1];

        const originalRow = document.createElement("tr");
        originalRow.innerHTML = row.dataset.originalHtml;

        actionCell.innerHTML =
            originalRow.children[totalCells - 1].innerHTML;

    }

}

/* ================= MODAL ================= */

function initModal() {

    window.openModal = function (modalId) {

        const modal = document.getElementById(modalId);

        if (modal) modal.classList.add("show");

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
window.showDeletePopup = function (el) {
    // Ambil URL dari tombol hapus
    const url = el.dataset.url;
    const modal = document.getElementById("deleteModal");

    if (!modal) return;

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
                        text: 'Data berhasil dihapus'
                    });
                } else {
                    // SweetAlert error
                    showAlert({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Gagal menghapus data'
                    });
                }
            })
            .catch(err => {
                console.error(err);
                showAlert({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan saat menghapus data'
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

// ================= FLASH ALERT (SweetAlert dari session Laravel) =================
function initFlashAlert() {
    const flashEl = document.getElementById('flash-data');
    if (!flashEl) return;

    const success = JSON.parse(flashEl.dataset.success || 'null');
    const error = JSON.parse(flashEl.dataset.error || 'null');
    const errors = JSON.parse(flashEl.dataset.errors || '[]');

    if (success) {
        showAlert({
            icon: 'success',
            title: 'Berhasil',
            text: success,
            timer: 2500
        });
    }

    if (error) {
        showAlert({
            icon: 'error',
            title: 'Gagal',
            text: error,
            timer: 3000
        });
    }

    if (errors.length > 0) {
        showAlert({
            icon: 'error',
            title: 'Validasi Gagal',
            text: errors.join('\n'),
            timer: 4000
        });
    }
}


/* ================= SIDEBAR TOGGLE ================= */

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

//FILTER TAHUN RKPDES
function loadKegiatanByTahun() {
    let tahun = document.getElementById('filterTahun').value;
    let kegiatanSelect = document.getElementById('kegiatanSelect');

    kegiatanSelect.innerHTML = '<option>Loading...</option>';
    kegiatanSelect.disabled = true;

    // reset form
    document.getElementById('panjang').value = '';
    document.getElementById('lebar').value = '';
    document.getElementById('tinggi').value = '';
    document.getElementById('satuan').value = '';
    document.getElementById('sisaVolume').innerText = '-';

    document.getElementById('inputLokasi').value = '';
    document.getElementById('inputLK').value = '';
    document.getElementById('inputPR').value = '';
    document.getElementById('inputRTM').value = '';

    if (!tahun) {
        kegiatanSelect.innerHTML = '<option value="">Pilih Tahun dulu</option>';
        return;
    }

    fetch(`/kadus/rpjmdes/kegiatan-by-tahun?tahun=${tahun}`)
        .then(res => res.json())
        .then(data => {

            kegiatanSelect.innerHTML = '<option value="">Pilih Kegiatan</option>';

            if (!data || data.length === 0) {
                kegiatanSelect.innerHTML = '<option value="">Tidak ada kegiatan</option>';
                return;
            }

            kegiatanSelect.disabled = false;

            data.forEach(item => {
                const option = document.createElement('option');

                option.value = item.id;
                option.textContent = item.nama_kegiatan;

                option.dataset.lokasi = item.lokasi;
                option.dataset.satuan = item.satuan;
                option.dataset.sisa = item.sisa;
                option.dataset.multi = item.is_multi_year ? 1 : 0;

                option.dataset.lk = item.penerima_laki;
                option.dataset.pr = item.penerima_perempuan;
                option.dataset.rtm = item.penerima_rtm;

                kegiatanSelect.appendChild(option);
            });

        })
        .catch(() => {
            kegiatanSelect.innerHTML = '<option>Gagal load</option>';
        });
}

document.addEventListener("DOMContentLoaded", function () {

    const kegiatanSelect = document.getElementById('kegiatanSelect');

    kegiatanSelect.addEventListener('change', function () {

        const selected = this.options[this.selectedIndex];
        if (!selected.value) return;

        const panjang = document.getElementById('panjang');
        const lebar = document.getElementById('lebar');
        const tinggi = document.getElementById('tinggi');

        const satuan = document.getElementById('satuan');
        const sisaVolume = document.getElementById('sisaVolume');

        const lokasi = document.getElementById('inputLokasi');
        const lk = document.getElementById('inputLK');
        const pr = document.getElementById('inputPR');
        const rtm = document.getElementById('inputRTM');

        const isMulti = selected.dataset.multi == "1";

        // 🔥 tampilkan info
        satuan.value = selected.dataset.satuan;
        sisaVolume.innerText =
            selected.dataset.sisa + ' ' + selected.dataset.satuan;

        // 🔥 AUTO ISI DATA
        lokasi.value = selected.dataset.lokasi;
        lk.value = selected.dataset.lk;
        pr.value = selected.dataset.pr;
        rtm.value = selected.dataset.rtm;

        // 🔥 SINGLE YEAR
        if (!isMulti) {
            panjang.value = selected.dataset.sisa;
            panjang.readOnly = true;
        } else {
            panjang.value = '';
            panjang.readOnly = false;
        }

        lebar.value = '';
        tinggi.value = '';

    });

});


//SIMPAN KEGIATAN PENGAJUAN RKP DI TABEL 
let tempData = [];

function tambahKeTabel(e) {
    e.preventDefault();

    let tahun = document.getElementById('filterTahun').value;
    let dusunId = document.querySelector('[name="dusun_id"]').value;
    let rpjmId = document.getElementById('kegiatanSelect').value;

    let panjang = document.getElementById('panjang').value;
    let lebar = document.getElementById('lebar').value;
    let tinggi = document.getElementById('tinggi').value;

    let lokasi = document.querySelector('[name="lokasi"]').value;
    let lk = document.querySelector('[name="lk"]').value || 0;
    let pr = document.querySelector('[name="pr"]').value || 0;
    let rtm = document.querySelector('[name="rtm"]').value || 0;

    if (!rpjmId) {
        showAlert({
            icon: 'warning',
            title: 'Perhatian',
            text: 'Pilih kegiatan dulu!'
        });
        return;
    }

    if (!panjang) {
        showAlert({
            icon: 'warning',
            title: 'Perhatian',
            text: 'Isi volume terlebih dahulu!'
        });
        return;
    }

    fetch('/kadus/pengajuanrkp/store', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value
        },
        body: JSON.stringify({
            tahun,
            dusun_id: dusunId,
            rpjmdes_detail_id: rpjmId,
            panjang,
            lebar,
            tinggi,
            lokasi,
            lk,
            pr,
            rtm
        })
    })
        .then(res => res.json())
        .then(res => {

            if (!res.success) {
                showAlert({
                    icon: 'error',
                    title: 'Gagal',
                    text: res.message || 'Gagal menyimpan'
                });
                return;
            }

            closeModal('tambahModal');

            showAlert({
                icon: 'success',
                title: 'Berhasil',
                text: 'Data berhasil ditambahkan'
            });

            setTimeout(() => location.reload(), 1200);

        })
        .catch(err => {
            console.error(err);

            showAlert({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan'
            });
        });
}

function renderTable() {
    let tbody = document.querySelector("table tbody");
    tbody.innerHTML = "";

    tempData.forEach((item, index) => {
        tbody.innerHTML += `
            <tr>
                <td>${index + 1}</td>
                <td>${item.dusun}</td>
                <td>${item.kegiatan}</td>
                <td>${item.volume} ${item.satuan}</td>
                <td>${item.lokasi}</td>
                <td>${item.lk}</td>
                <td>${item.pr}</td>
                <td>${item.rtm}</td>
                <td>-</td>
            </tr>
        `;
    });
}

//KIRIM PENGAJUAN RKPDES
function kirimPengajuan() {

    Swal.fire({
        icon: 'warning',
        title: 'Kirim Pengajuan?',
        text: 'Apakah Anda yakin ingin mengirim pengajuan RKPDES ini?',
        showCancelButton: true,
        confirmButtonText: 'Ya, Kirim',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33'
    }).then((result) => {

        if (result.isConfirmed) {

            fetch('/kadus/pengajuanrkp/kirim', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value
                }
            })
                .then(res => res.json())
                .then(res => {

                    showAlert({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Pengajuan berhasil dikirim'
                    });

                    setTimeout(() => {
                        location.reload();
                    }, 1200);

                })
                .catch(err => {
                    console.error(err);

                    showAlert({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan saat mengirim pengajuan'
                    });
                });

        }

    });

}

