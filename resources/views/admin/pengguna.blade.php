@extends('layouts.admin')

@section('title', 'Manajemen Pengguna')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/index.css') }}">
<link rel="stylesheet" href="{{ asset('css/header.css') }}">
@endsection

@section('content')

<div class="card">

    <div class="card-header">Data Pengguna</div>

    <div class="table-container">

        <div class="table-controls">

            <div class="table-left">
                <button onclick="openModal('tambahModal')" class="btn">Tambah</button>

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

            <div class="table-filter">

                <label class="filter-label">Filter</label>

                <select class="filter" name="dusun_id" onchange="this.form.submit()">
                    <option value="">Semua Dusun</option>
                    @foreach($dusun as $d)
                    <option value="{{ $d->id }}">
                        {{ $d->nama_dusun }}
                    </option>
                    @endforeach
                </select>

                <input type="text" placeholder="Search..." class="search-input">

            </div>
        </div>
        <div class="table-scroll">

            <table>
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>Nama</th>
                        <th>Dusun</th>
                        <th>Username</th>
                        <th class="col-role">Role</th>
                        <th class="col-status">Status</th>
                        <th width="100">Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach ($users as $i => $user)
                    <tr
                        data-id="{{ $user->id }}"
                        data-url="{{ route('admin.pengguna.update', $user->id) }}">

                        <td>{{ $users->firstItem() + $i }}</td>

                        <td class="col-text" data-field="name">{{ $user->name }}</td>
                        <td class="col-text" data-field="dusun_id"
                            data-dusun='@json($dusun)'>

                            {{ $user->dusun->nama_dusun ?? '-' }}
                        </td>
                        <td class="col-text" data-field="username">{{ $user->username }}</td>
                        <td class="col-role" data-field="role">{{ $user->role }}</td>

                        <td class="col-status">
                            {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                        </td>

                        <td class="action-btn">
                            <div class="action-wrapper">

                                @if($user->is_active)
                                <button type="button"
                                    class="btn-icon-action btn-view"
                                    data-id="{{ $user->id }}"
                                    onclick="toggleStatus(this,0); return false;"
                                    title="Nonaktifkan">
                                    <img src="{{ asset('images/logout.png') }}" class="action-icon">
                                </button>
                                @else
                                <button type="button"
                                    class="btn-icon-action btn-view"
                                    data-id="{{ $user->id }}"
                                    onclick="toggleStatus(this,1); return false;"
                                    title="Aktifkan">
                                    <img src="{{ asset('images/logout.png') }}" class="action-icon">
                                </button>
                                @endif

                                <button type="button"
                                    class="btn-icon-action btn-edit"
                                    onclick="editRow(this.closest('tr')); return false;"
                                    title="Edit">
                                    <img src="{{ asset('images/pencil-square.svg') }}" class="action-icon">
                                </button>

                                <button type="button"
                                    class="btn-icon-action btnaksi-delete"
                                    data-url="{{ route('admin.pengguna.destroy', $user->id) }}"
                                    onclick="showDeletePopup(this); return false;"
                                    title="Hapus">
                                    <img src="{{ asset('images/trash3-fill.svg') }}" class="action-icon">
                                </button>

                            </div>
                        </td>

                    </tr>
                    @endforeach

                </tbody>
            </table>
            <div class="pagination-wrapper mt-2">
                {{ $users->links('pagination::bootstrap-5') }}
            </div>

        </div>

    </div>

    <div id="dropdown-root"></div>

    <!-- Modal Tambah Pengguna -->
    <div id="tambahModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Tambah Pengguna</h2>
                <button class="close-btn" onclick="closeModal('tambahModal')">&times;</button>
            </div>

            <form action="/admin/pengguna/tambah" method="POST">
                @csrf

                <div class="form-group">
                    <label class="form-label">Nama</label>
                    <input type="text" class="form-input" name="name" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Dusun</label>
                    <select class="form-input" name="dusun_id">
                        <option value="">-- Pilih Dusun --</option>
                        @foreach($dusun as $d)
                        <option value="{{ $d->id }}">{{ $d->nama_dusun }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" class="form-input" name="username" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-input" name="password" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Role</label>
                    <select class="form-input" name="role" required>
                        <option value="admin">Admin</option>
                        <option value="kadus">Kadus</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select class="form-input" name="is_active" required>
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-cancel-modal" onclick="closeModal('tambahModal')">Batal</button>
                    <button type="submit" class="btn-submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    @endsection