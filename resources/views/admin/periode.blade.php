@extends('layouts.admin')

@section('title', 'Periode Usulan')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/index.css') }}">
<link rel="stylesheet" href="{{ asset('css/header.css') }}">
@endsection

@section('content')

<div class="card">

    <div class="card-header">Periode Usulan</div>

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

            <div>
                <input type="text" class="search-input" placeholder="Search...">
            </div>

        </div>
        <div class="table-scroll">

            <table>

                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>Nama Periode</th>
                        <th>Tanggal Mulai</th>
                        <th>Tanggal Selesai</th>
                        <th width="100">Status</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($periode as $i => $p)
                    <tr>
                        <td>{{ $periode->firstItem() + $i }}</td>
                        <td>{{ $p->nama_periode }}</td>
                        <td>{{ \Carbon\Carbon::parse($p->tanggal_mulai)->format('d-m-Y H:i') }}</td>
                        <td>{{ \Carbon\Carbon::parse($p->tanggal_selesai)->format('d-m-Y H:i') }}</td>
                        <td>
                            {{ $p->is_active ? 'Aktif' : 'Nonaktif' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align:center">
                            Belum ada periode
                        </td>
                    </tr>
                    @endforelse

                </tbody>

            </table>

            <div class="pagination-wrapper mt-2">
                {{ $periode->links('pagination::bootstrap-5') }}
            </div>
        </div>

    </div>

    <!-- Modal Tambah Periode -->
    <div id="tambahModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Tambah Periode Usulan</h2>
                <button class="close-btn" onclick="closeModal('tambahModal')">&times;</button>
            </div>
            <form action="{{ route('admin.periode.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">Nama Periode</label>
                    <input type="text" class="form-input" name="nama_periode" required>
                </div>
                <div class="form-group">
                    <label>Tanggal Mulai</label>
                    <input type="datetime-local" name="tanggal_mulai" class="form-input" required>
                </div>

                <div class="form-group">
                    <label>Tanggal Selesai</label>
                    <input type="datetime-local" name="tanggal_selesai" class="form-input" required>
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-input" required>
                        <option value="Aktif">Aktif</option>
                        <option value="Nonaktif">Nonaktif</option>
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