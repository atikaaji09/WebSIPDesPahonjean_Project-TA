document.addEventListener("DOMContentLoaded", function () {

    const satuanSelect = document.getElementById("satuanSelect");
    const dimensi = document.getElementById("dimensiGroup");
    const jumlah = document.getElementById("jumlahGroup");

    if (!satuanSelect) return;

    satuanSelect.addEventListener("change", function () {

        let val = this.value;

        if (val === 'm' || val === 'm2' || val === 'm3') {
            dimensi.style.display = 'block';
            jumlah.style.display = 'none';
        } else if (val !== '') {
            dimensi.style.display = 'none';
            jumlah.style.display = 'block';
        } else {
            dimensi.style.display = 'block';
            jumlah.style.display = 'none';
        }

    });

});

document.addEventListener("DOMContentLoaded", function () {

    if (typeof Swal === 'undefined') return;

    const flashEl = document.getElementById('flash-data');

    if (!flashEl) return;

    const error = flashEl.dataset.error;
    const success = flashEl.dataset.success;

    if (error) {
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: error
        });
    }

    if (success) {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: success,
            timer: 2000,
            showConfirmButton: false
        });
    }

});