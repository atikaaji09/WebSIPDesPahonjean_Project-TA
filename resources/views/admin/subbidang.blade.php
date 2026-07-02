@extends('layouts.admin')

@section('title', 'Kelola Sub Bidang')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/index.css') }}">
<link rel="stylesheet" href="{{ asset('css/header.css') }}">
@endsection

@section('content')

<div class="card">

    <div class="card-header">Sub Bidang</div>

    <div class="table-container">

        <div class="table-controls">

            <div class="table-left">
                <button onclick="openModal('tambahModal')" class="btn">Tambah</button>
                <button onclick="openModal('importModal')" class="btn">Import Excel</button>

                <form method="GET" id="entriesForm" class="entries-group">
                    <select name="entries" class="entries-select"
                        onchange="document.getElementById('entriesForm').submit()">

                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>

                    </select>
                    <span class="entries-label">Entries</span>
                </form>
            </div>

            <div>
                <input type="text" class="search-input" placeholder="Search...">
            </div>

        </div>

        <div class="table-scroll">

            <table>

                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>Nama Sub Bidang</th>
                        <th width="100">Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($subbidang as $i => $s)
                    <tr
                        data-id="{{ $s->id }}"
                        data-url="{{ route('admin.subbidang.update', $s->id) }}">
                        <td>{{ $subbidang->firstItem() + $i }}</td>

                        <td class="col-nama" data-field="nama_sub_bidang">
                            {{ $s->nama_sub_bidang }}
                        </td>

                        <td class="action-btn">
                            <div class="action-wrapper">

                                <a href="{{ route('admin.kegiatan', $s->id) }}"
                                    class="btn-action btn-view"
                                    title="Lihat Kegiatan">
                                    <img src="{{ asset('images/eye-fill.svg') }}" class="action-icon">
                                </a>

                                <button type="button"
                                    class="btn-action btn-edit"
                                    onclick="editRow(this.closest('tr')); return false;"
                                    title="Edit">
                                    <img src="{{ asset('images/pencil-square.svg') }}" class="action-icon">
                                </button>

                                <button type="button"
                                    class="btn-action btnaksi-delete"
                                    data-url="{{ route('admin.subbidang.destroy', $s->id) }}"
                                    onclick="showDeletePopup(this); return false;"
                                    title="Hapus">
                                    <img src="{{ asset('images/trash3-fill.svg') }}" class="action-icon">
                                </button>

                            </div>
                        </td>

                    </tr>

                    @empty
                    <tr>
                        <td colspan="3" style="text-align:center;">
                            Belum ada data sub bidang
                        </td>
                    </tr>
                    @endforelse

                </tbody>

            </table>
            <div class="pagination-wrapper mt-2">
                {{ $subbidang->links('pagination::bootstrap-5') }}
            </div>

        </div>

    </div>

    <!-- Modal Tambah Sub Bidang -->
    <div id="tambahModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Tambah Sub Bidang</h2>
                <button class="close-btn" onclick="closeModal('tambahModal')">&times;</button>
            </div>
            <form action="{{ route('admin.subbidang.store') }}" method="POST">
                @csrf
                <input type="hidden" name="bidang_id" value="{{ $bidang->id }}">
                <div class="form-group">
                    <label class="form-label">Nama Sub Bidang</label>
                    <input type="text" class="form-input" name="nama_sub_bidang" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel-modal" onclick="closeModal('tambahModal')">Batal</button>
                    <button type="submit" class="btn-submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Import Excel -->
    <div id="importModal" class="modal">
        <div class="modal-content">

            <div class="modal-header">
                <h2 class="modal-title">Import Sub Bidang</h2>
                <button class="close-btn" onclick="closeModal('importModal')">&times;</button>
            </div>

            <form action="{{ route('admin.subbidang.import', $bidang->id) }}"
                method="POST"
                enctype="multipart/form-data">

                @csrf

                <div class="form-group">
                    <label class="form-label">Upload File Excel</label>
                    <input type="file" name="file" class="form-input" accept=".xlsx,.xls" required>
                    <small style="display:block; margin-top:8px;">
                        Download template:
                        <a href="{{ asset('template/import_sub_bidang.xlsx') }}" target="_blank">
                            Template Excel Sub Bidang
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