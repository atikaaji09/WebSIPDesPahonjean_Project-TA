@extends('layouts.admin')

@section('title', 'Kelola Dusun')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/index.css') }}">
<link rel="stylesheet" href="{{ asset('css/header.css') }}">
@endsection

@section('content')

<div class="card">

    <div class="card-header">Dusun</div>

    <div class="table-container">

        <div class="table-controls">

            <div class="table-left">
                <button onclick="openModal('tambahModal')" class="btn">Tambah</button>
                <button onclick="openModal('importModal')" class="btn">Import Excel</button>

                <form method="GET" id="entriesForm" class="entries-group">
                    <select name="entries" class="entries-select"
                        onchange="document.getElementById('entriesForm').submit()">

                        <option value="10" {{ request('entries') == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('entries') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('entries') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('entries') == 100 ? 'selected' : '' }}>100</option>

                    </select>
                    <span class="entries-label">Entries</span>
                </form>
            </div>

            <div>
                <input type="text" placeholder="Search..." style="padding:5px; border:1px solid #ccc; border-radius:4px;">
            </div>
        </div>

        <div class="table-scroll">

            <table>

                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>Nama Dusun</th>
                        <th>Jumlah RT/RW</th>
                        <th width="100">Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($dusun as $i => $d)
                    <tr
                        data-id="{{ $d->id }}"
                        data-url="/admin/dusun/edit/{{ $d->id }}">
                        <td>{{ $dusun->firstItem() + $i }}</td>

                        <td class="col-nama" data-field="nama_dusun">
                            {{ $d->nama_dusun }}
                        </td>

                        <td>{{ $d->rtrw_count }}</td>

                        <td class="action-btn">

                            <div class="action-wrapper">

                                <a href="{{ route('admin.rtrw', $d->id) }}"
                                    class="btn-icon-action btn-view"
                                    title="Lihat RT/RW">
                                    <img src="{{ asset('images/eye-fill.svg') }}" class="action-icon">
                                </a>

                                <button type="button"
                                    class="btn-icon-action btn-edit"
                                    onclick="editRow(this.closest('tr')); return false;"
                                    title="Edit">
                                    <img src="{{ asset('images/pencil-square.svg') }}" class="action-icon">
                                </button>

                                <button type="button"
                                    class="btn-icon-action btnaksi-delete"
                                    data-url="{{ route('admin.dusun.destroy', $d->id) }}"
                                    onclick="showDeletePopup(this); return false;"
                                    title="Hapus">
                                    <img src="{{ asset('images/trash3-fill.svg') }}" class="action-icon">
                                </button>

                            </div>

                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align:center;">Belum ada data dusun</td>
                    </tr>
                    @endforelse

                </tbody>

            </table>
            <div class="pagination-wrapper mt-2">
                {{ $dusun->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>

    <!-- Modal Tambah Dusun -->
    <div id="tambahModal" class="modal">
        <div class="modal-content">

            <div class="modal-header">
                <h2 class="modal-title">Tambah Dusun</h2>
                <button class="close-btn" onclick="closeModal('tambahModal')">&times;</button>
            </div>

            <form action="{{ route('admin.dusun.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label class="form-label">Nama Dusun</label>
                    <input type="text" class="form-input" name="nama_dusun" required>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-cancel-modal" onclick="closeModal('tambahModal')">Batal</button>
                    <button type="submit" class="btn-submit">Simpan</button>
                </div>
            </form>

        </div>
    </div>

</div>

<!-- Modal Import Excel -->
<div id="importModal" class="modal">
    <div class="modal-content">

        <div class="modal-header">
            <h2 class="modal-title">Import Dusun</h2>
            <button class="close-btn" onclick="closeModal('importModal')">&times;</button>
        </div>

        <form action="{{ route('admin.dusun.import') }}"
            method="POST"
            enctype="multipart/form-data">

            @csrf

            <div class="form-group">
                <label class="form-label">Upload File Excel</label>
                <input type="file" name="file" class="form-input" accept=".xlsx,.xls" required>
                <small style="display:block; margin-top:8px;">
                    Download template:
                    <a href="{{ asset('template/import_dusun.xlsx') }}" target="_blank">
                        Template Excel Dusun
                    </a>
                </small>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-cancel-modal"
                    onclick="closeModal('importModal')">Batal</button>

                <button type="submit" class="btn-submit">
                    Import
                </button>
            </div>

        </form>

    </div>
</div>

@endsection