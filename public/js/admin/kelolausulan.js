function toggleDetail(el) {
    let detail = el.querySelector('.detail-volume');
    if (!detail) return;

    detail.style.display = detail.style.display === 'none' ? 'block' : 'none';
}

