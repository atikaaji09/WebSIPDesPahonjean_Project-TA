@extends('layouts.admin')

@section('title', 'Kelola RT/RW')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/index.css') }}">
<link rel="stylesheet" href="{{ asset('css/header.css') }}">
@endsection

@section('content')

<div class="card">

    <div class="card-header">RT/RW</div>

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
                        <th>RT</th>
                        <th>RW</th>
                        <th width="100">Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($dusun->rtrw as $i => $r)
                    <tr
                        data-id="{{ $r->id }}" data-url="{{ route('admin.rtrw.update', $r->id) }}">
                        <td>{{ $rtrw->firstItem() + $i }}</td>
                        <td class="col-rt" data-field="rt">{{ $r->rt }}</td>
                        <td class="col-rw" data-field="rw">{{ $r->rw }}</td>

                        <td class="action-btn">

                            <div class="action-wrapper">

                                <button type="button"
                                    class="btn-icon-action btn-edit"
                                    onclick="editRow(this.closest('tr')); return false;"
                                    title="Edit">
                                    <img src="{{ asset('images/pencil-square.svg') }}" class="action-icon">
                                </button>

                                <button type="button"
                                    class="btn-icon-action btnaksi-delete"
                                    data-url="{{ route('admin.rtrw.destroy', $r->id) }}"
                                    onclick="showDeletePopup(this); return false;"
                                    title="Hapus">
                                    <img src="{{ asset('images/trash3-fill.svg') }}" class="action-icon">
                                </button>

                            </div>

                        </td>
                    </tr>

                    @empty
                    <tr>
                        <td colspan="4" style="text-align:center;">Belum ada data RT/RW</td>
                    </tr>
                    @endforelse

                </tbody>

            </table>
            <div class="pagination-wrapper mt-2">
                {{ $rtrw->links('pagination::bootstrap-5') }}
            </div>

        </div>

    </div>

</div>

<div id="dropdown-root"></div>

<!-- Modal Tambah RT/RW -->
<div id="tambahModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Tambah RT/RW</h2>
            <button class="close-btn" onclick="closeModal('tambahModal')">&times;</button>
        </div>

        <form action="{{ route('admin.rtrw.store') }}" method="POST">
            @csrf

            <input type="hidden" name="dusun_id" value="{{ $dusun->id }}">

            <div class="form-group">
                <label class="form-label">RT</label>
                <input type="text" class="form-input" name="rt" required>
            </div>

            <div class="form-group">
                <label class="form-label">RW</label>
                <input type="text" class="form-input" name="rw" required>
            </div>

            <button type="submit" class="btn-submit">Simpan</button>
        </form>
    </div>
</div>

<!-- Modal Import Excel -->
<div id="importModal" class="modal">
    <div class="modal-content">

        <div class="modal-header">
            <h2 class="modal-title">Import RT/RW</h2>
            <button class="close-btn" onclick="closeModal('importModal')">&times;</button>
        </div>

        <form action="{{ route('admin.rtrw.import', $dusun->id) }}"
            method="POST"
            enctype="multipart/form-data">

            @csrf

            <div class="form-group">
                <label class="form-label">Upload File Excel</label>
                <input type="file" name="file" class="form-input" accept=".xlsx,.xls" required>
                <small style="display:block; margin-top:8px;">
                    Download template:
                    <a href="{{ asset('template/import_rtrw.xlsx') }}" target="_blank">
                        Template Excel RT/RW
                    </a>
                </small>
            </div>

            <div class="modal-footer">
                <button type="button"
                    class="btn-cancel-modal"
                    onclick="closeModal('importModal')">
                    Batal
                </button>

                <button type="submit" class="btn-submit">
                    Import
                </button>
            </div>

        </form>

    </div>
</div>

@endsection