function openProgresModal(id) {
    document.getElementById('progres_id').value = id;
    document.getElementById('tambahProgresModal').classList.add('show');
}

window.DetailVolumeRealisasi = function (id) {
    let el = document.getElementById('detail-' + id);

    if (el.style.display === 'none' || el.style.display === '') {
        el.style.display = 'block';
    } else {
        el.style.display = 'none';
    }
}