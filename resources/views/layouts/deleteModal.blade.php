<!-- Modal Delete (satu kali di layout) -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Konfirmasi Hapus</h2>
            <button class="close-btn" onclick="closeModal('deleteModal')">&times;</button>
        </div>
        <div class="modal-body">
            <p>Apakah Anda yakin ingin menghapus data ini?</p>
        </div>
        <div class="modal-footer">
            <form method="POST">
                @csrf
                @method('DELETE')
                <button type="button" class="btn-cancel-modal" onclick="closeModal('deleteModal')">Batal</button>
                <button type="submit" class="btn-submit">Hapus</button>
            </form>
        </div>
    </div>
</div>