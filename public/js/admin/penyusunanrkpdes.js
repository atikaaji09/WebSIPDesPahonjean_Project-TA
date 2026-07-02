document.addEventListener("DOMContentLoaded", function () {
    const tetapkanBtn = document.querySelector('.btn-approve-all');

    if (!tetapkanBtn) return;

    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    tetapkanBtn.addEventListener('click', function () {

        const tahun = document.querySelector('select[name="tahun"]').value;

        if (!tahun) {
            Swal.fire('Oops', 'Pilih tahun dulu!', 'warning');
            return;
        }

        Swal.fire({
            title: 'Tetapkan RKPDes?',
            text: "Data RKPDes akan ditetapkan!",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Tetapkan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {

                Swal.fire({
                    title: 'Memproses...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                fetch('/admin/susunrkp/tetapkan', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ tahun: tahun })
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: data.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => location.reload());
                        } else {
                            Swal.fire('Gagal', data.message || 'Terjadi kesalahan', 'error');
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire('Error', 'Terjadi kesalahan pada server', 'error');
                    });

            }
        });

    });
});

function toggleDetail(el) {
    let detail = el.querySelector('.detail-volume');
    if (!detail) return;

    detail.style.display = detail.style.display === 'none' ? 'block' : 'none';
}

//FUNGSI TAMBAH KEGIATAN DI MENU PENYUSUNAN RKPDes
document.addEventListener("DOMContentLoaded", function () {

    const tahunEl = document.getElementById("tahunModal");
    const dusunEl = document.getElementById("dusunModal");
    const kegiatanEl = document.getElementById("kegiatanSelect");

    const volumeInput = document.getElementById("volumeInput");
    const satuanInput = document.getElementById("satuanInput");
    const infoSisa = document.getElementById("infoSisa");

    const tahunHidden = document.getElementById("tahunHidden");

    let currentSisa = 0;
    let isMultiYear = false;

    tahunEl.addEventListener("change", function () {
        tahunHidden.value = this.value;
        loadKegiatan();
    });

    dusunEl.addEventListener("change", loadKegiatan);

    function loadKegiatan() {

        const tahun = tahunEl.value;
        const dusun = dusunEl.value;

        if (!tahun || !dusun) {
            kegiatanEl.innerHTML = '<option value="">Pilih Tahun & Dusun dulu</option>';
            return;
        }

        const key = `${tahun}-${dusun}`;

        if (kegiatanEl.dataset.loaded === key) {
            return;
        }

        kegiatanEl.dataset.loaded = key;

        fetch(`/admin/kegiatan-by-tahun-dusun?tahun=${tahun}&dusun_id=${dusun}`)
            .then(res => res.json())
            .then(data => {

                if (!data || data.length === 0) {
                    kegiatanEl.innerHTML = '<option value="">Tidak ada kegiatan</option>';
                    return;
                }

                let options = '<option value="">Pilih Kegiatan</option>';

                data.forEach(item => {
                    options += `
                    <option 
                        value="${item.id}"
                        data-lokasi="${item.lokasi}"
                        data-satuan="${item.satuan}"
                        data-sisa="${item.sisa}"
                        data-multi="${item.is_multi_year}"
                        data-lk="${item.penerima_laki}"
                        data-pr="${item.penerima_perempuan}"
                        data-rtm="${item.penerima_rtm}"
                    >
                        ${item.nama_kegiatan}
                    </option>
                `;
                });

                kegiatanEl.innerHTML = options;

            })
            .catch(err => {
                console.error("ERROR FETCH:", err);
                kegiatanEl.innerHTML = '<option value="">Error load data</option>';
            });
    }

    kegiatanEl.addEventListener("change", function () {

        const opt = this.selectedOptions[0];
        if (!opt) return;

        currentSisa = parseFloat(opt.dataset.sisa || 0);
        isMultiYear = opt.dataset.multi === "true";

        document.querySelector('[name="lokasi"]').value = opt.dataset.lokasi || '';
        satuanInput.value = opt.dataset.satuan || '';

        document.querySelector('[name="penerima_laki"]').value = opt.dataset.lk || 0;
        document.querySelector('[name="penerima_perempuan"]').value = opt.dataset.pr || 0;
        document.querySelector('[name="penerima_rtm"]').value = opt.dataset.rtm || 0;

        infoSisa.innerText = `Sisa volume: ${currentSisa}`;

        if (!isMultiYear) {
            volumeInput.value = currentSisa;
            volumeInput.readOnly = true;
        } else {
            volumeInput.readOnly = false;
        }
    });

    volumeInput.addEventListener("input", function () {
        if (parseFloat(this.value) > currentSisa) {
            alert("Volume melebihi sisa!");
            this.value = currentSisa;
        }
    });

});