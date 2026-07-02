@extends('layouts.admin')

@section('title', 'Aset Desa')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/index.css') }}">
<link rel="stylesheet" href="{{ asset('css/header.css') }}">
@endsection

@section('content')

<div class="card">

    <div class="card-header">Aset Desa</div>

    <div class="table-container">
        <div class="table-controls">

            <div class="table-left">
                <button onclick="openModal('tambahModal')" class="btn">Tambah</button>
                <button onclick="openModal('importModal')" class="btn">Import Excel</button>

                <form method="GET" action="{{ route('admin.asetdesa') }}" style="display:flex;align-items:center;gap:5px;">

                    <input type="hidden" name="klas_aset" value="{{ request('klas_aset') }}">

                    <select name="entries" class="entries-select" onchange="this.form.submit()">
                        <option value="10" {{ request('entries') == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('entries') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('entries') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('entries') == 100 ? 'selected' : '' }}>100</option>
                    </select>

                    <span class="entries-label">Entries</span>

                </form>
            </div>

            <div class="table-filter">
                <form method="GET" action="{{ route('admin.asetdesa') }}">
                    <label class="filter-label">Filter</label>
                    <select name="klas_aset" class="filter" onchange="this.form.submit()">
                        <option value="">Semua Klas Aset</option>
                        @foreach($klasAsetList as $ka)
                        <option value="{{ $ka }}" {{ request('klas_aset') == $ka ? 'selected' : '' }}>
                            {{ $ka }}
                        </option>
                        @endforeach
                    </select>
                </form>

                <form action="{{ route('admin.asetdesa.export') }}" method="GET" class="export-form">
                    <input type="hidden" name="klas_aset" value="{{ request('klas_aset') }}">

                    <button type="submit" class="btn-export">
                        Export Excel
                    </button>
                </form>
            </div>

        </div>

        <div class="table-scroll">
            <table>
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>Klas Aset</th>
                        <th>Nama Aset</th>
                        <th>Jenis Kepemilikan</th>
                        <th>Nomor Kepemilikan</th>
                        <th>Tanggal Kepemilikan</th>
                        <th>Kode Aset</th>
                        <th>Tahun Perolehan</th>
                        <th>Nilai Perolehan</th>
                        <th>Kondisi Aset</th>
                        <th>Keterangan</th>
                        <th>Luas</th>
                        <th>Bukti Kepemilikan</th>
                        <th>Titik Pangkal</th>
                        <th>Titik Ujung</th>
                        <th width="100">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($asets as $i => $aset)
                    <tr data-id="{{ $aset->id }}" data-url="{{ route('admin.asetdesa.update', $aset->id) }}">
                        <td>{{ $asets->firstItem() + $i }}</td>
                        <td data-field="klas_aset">{{ $aset->klas_aset }}</td>
                        <td data-field="nama_aset">{{ $aset->nama_aset }}</td>
                        <td data-field="jenis_kepemilikan">{{ $aset->jenis_kepemilikan }}</td>
                        <td data-field="nomor_kepemilikan">{{ $aset->nomor_kepemilikan }}</td>
                        <td data-field="tanggal_kepemilikan">
                            {{ $aset->tanggal_kepemilikan ? \Carbon\Carbon::parse($aset->tanggal_kepemilikan)->format('d-m-Y') : '' }}
                        </td>
                        <td data-field="kode_aset">{{ $aset->kode_aset }}</td>
                        <td data-field="tahun_perolehan">{{ $aset->tahun_perolehan }}</td>
                        <td data-field="nilai_perolehan" data-type="numeric">{{ number_format($aset->nilai_perolehan,0,',','.') }}</td>
                        <td data-field="kondisi_aset">{{ $aset->kondisi_aset }}</td>
                        <td data-field="keterangan">{{ $aset->keterangan }}</td>
                        <td data-field="luas" data-type="numeric">
                            {{ $aset->luas ? number_format($aset->luas,3,',','.') : '' }}
                        </td>
                        <td data-field="bukti_kepemilikan">{{ $aset->bukti_kepemilikan }}</td>
                        <td data-field="titik_pangkal">{{ $aset->titik_pangkal }}</td>
                        <td data-field="titik_ujung">{{ $aset->titik_ujung }}</td>
                        <td class="action-btn">
                            <div class="dropdown-action">
                                <span class="action-trigger" onclick="toggleActionDropdown(this)">⋮</span>
                                <div class="action-menu">
                                    <a href="#" onclick="editRow(this); return false;">
                                        <img src="{{ asset('images/pencil-square.svg') }}" class="action-icon"> Edit
                                    </a>
                                    <a href="#" onclick="showDeletePopup(this); return false;" data-url="{{ route('admin.asetdesa.destroy', $aset->id) }}">
                                        <img src="{{ asset('images/trash3-fill.svg') }}" class="action-icon"> Hapus
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="14" style="text-align:center;">Belum ada data aset</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($asets instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="table-pagination">
            {{ $asets->links('pagination::bootstrap-5') }}
        </div>
        @endif

    </div>
</div>

<!-- Modal Tambah Aset Desa -->
<div id="tambahModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Tambah Aset Desa</h2>
            <button class="close-btn" onclick="closeModal('tambahModal')">&times;</button>
        </div>
        <form action="{{ route('admin.asetdesa.store') }}" method="POST">
            @csrf
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label class="form-label">Klas Aset</label>
                    <input type="text" class="form-input" name="klas_aset" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Nama Aset</label>
                    <input type="text" class="form-input" name="nama_aset" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Jenis Kepemilikan</label>
                    <input type="text" class="form-input" name="jenis_kepemilikan">
                </div>
                <div class="form-group">
                    <label class="form-label">Nomor Kepemilikan</label>
                    <input type="text" class="form-input" name="nomor_kepemilikan" placeholder="Contoh: 2.01.01.00.00001">
                </div>
                <div class="form-group">
                    <label class="form-label">Tanggal Kepemilikan</label>
                    <input type="date" class="form-input" name="tanggal_kepemilikan">
                </div>
                <div class="form-group">
                    <label class="form-label">Kode Aset</label>
                    <input type="text" class="form-input" name="kode_aset" placeholder="Contoh: 33.01.14.2001/2.1880" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Tahun Perolehan</label>
                    <input type="number" class="form-input" name="tahun_perolehan" min="1900" max="2100">
                </div>
                <div class="form-group">
                    <label class="form-label">Nilai Perolehan</label>
                    <input
                        type="text"
                        class="form-input rupiah-input"
                        name="nilai_perolehan"
                        placeholder="Contoh: 500.000.000"
                        required>
                </div>
                <div class="form-group">
                    <label class="form-label">Kondisi Aset</label>
                    <input type="text" class="form-input" name="kondisi_aset">
                </div>
                <div class="form-group">
                    <label class="form-label">Luas</label>
                    <input
                        type="text"
                        class="form-input decimal-input"
                        name="luas"
                        placeholder="Contoh: 2,455">
                </div>
                <div class="form-group">
                    <label class="form-label">Bukti Kepemilikan</label>
                    <input type="text" class="form-input" name="bukti_kepemilikan">
                </div>
            </div>
            <div class="form-group" style="margin-top: 15px;">
                <label class="form-label">Keterangan</label>
                <textarea class="form-input" name="keterangan" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Titik Pangkal</label>
                <input type="text" class="form-input" name="titik_pangkal">
            </div>

            <div class="form-group">
                <label class="form-label">Titik Ujung</label>
                <input type="text" class="form-input" name="titik_ujung">
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
            <h2 class="modal-title">Import Data Aset</h2>
            <button class="close-btn" onclick="closeModal('importModal')">&times;</button>
        </div>
        <form action="{{ route('admin.asetdesa.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="form-label">Upload File Excel</label>
                <input type="file" name="file" class="form-input" accept=".xlsx,.xls" required>

                <small style="display:block; margin-top:8px;">
                    Download template:
                    <a href="{{ asset('template/import_aset.xlsx') }}" target="_blank">
                        Template Excel Aset
                    </a>
                </small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel-modal" onclick="closeModal('importModal')">Batal</button>
                <button type="submit" class="btn-submit">Import</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('js')
<script src="{{ asset('js/admin/asetdesa.js') }}"></script>
@endsection