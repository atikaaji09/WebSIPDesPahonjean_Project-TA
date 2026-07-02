@extends('layouts.admin')

@section('title', 'Penyusunan RKPDes')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/index.css') }}">
<link rel="stylesheet" href="{{ asset('css/header.css') }}">
@endsection

@section('content')

<div class="card">

    <div class="card-header">Penyusunan RKPDes</div>

    <div class="table-container">
        <div class="table-controls">
            <div class="table-filter">
                <label class="filter-label">Pilih Tahun</label>
                <form method="GET" action="{{ route('admin.susunrkp') }}">
                    <select class="filter" name="tahun" onchange="this.form.submit()">
                        <option value="" disabled {{ !$tahun ? 'selected' : '' }}>Tahun</option>

                        @foreach($tahunList as $th)
                        <option value="{{ $th }}" {{ $tahun == $th ? 'selected' : '' }}>
                            {{ $th }}
                        </option>
                        @endforeach

                    </select>
                </form>
            </div>
            <div class="table-right" style="display:flex; align-items:center; gap:10px;">

                <button type="button" onclick="openModal('tambahModal')" class="btn">
                    Tambah
                </button>

                <form method="GET" id="entriesForm" class="entries-group" style="display:flex; align-items:center; gap:5px;">

                    <input type="hidden" name="tahun" value="{{ $tahun }}">
                    <input type="hidden" name="dusun_id" value="{{ request('dusun_id') }}">

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


                <form method="GET" action="{{ route('admin.susunrkp') }}">
                    <input type="hidden" name="tahun" value="{{ $tahun }}">

                    <label class="filter-label">Filter</label>

                    <select class="filter" name="dusun_id" onchange="this.form.submit()">
                        <option value="">Semua Dusun</option>
                        @foreach($dusunList as $dusun)
                        <option value="{{ $dusun->id }}"
                            {{ request('dusun_id') == $dusun->id ? 'selected' : '' }}>
                            {{ $dusun->nama_dusun }}
                        </option>
                        @endforeach
                    </select>
                </form>

                <form action="{{ route('admin.susunrkpdes.export') }}" method="GET" class="export-form">
                    <input type="hidden" name="tahun" value="{{ $tahun }}">
                    <input type="hidden" name="dusun_id" value="{{ request('dusun_id') }}">

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
                        <th>Dusun</th>
                        <th>Bidang</th>
                        <th>Sub Bidang</th>
                        <th>Kegiatan</th>
                        <th>Mendukung SDGs ke</th>
                        <th>Data Existing Tahun Berjalan</th>
                        <th>Target Capaian</th>
                        <th>Lokasi</th>
                        <th>Volume</th>
                        <th>Satuan</th>
                        <th>Jumlah Biaya</th>
                        <th>Sumber Dana</th>
                        <th>LK</th>
                        <th>PR</th>
                        <th>RTM</th>
                        <th>Waktu Pelaksanaan</th>
                        <th>Pelaksana Kegiatan</th>
                        <th>Rencana TPK</th>
                        <th>Pola Pelaksanaan</th>
                        <th width="100">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($details as $i => $item)
                    <tr
                        data-id="{{ $item->id }}"
                        data-url="/admin/susunrkp/update/{{ $item->id }}">

                        <td>{{ $details->firstItem() + $i }}</td>

                        <td>{{ $item->rpjmdesDetail->usulan->rtrw->dusun->nama_dusun ?? '-' }}</td>
                        <td>{{ $item->bidang->nama_bidang ?? '-' }}</td>
                        <td>{{ $item->subBidang->nama_sub_bidang ?? '-' }}</td>
                        <td>{{ $item->kegiatan->nama_kegiatan ?? '-' }}</td>
                        <td data-field="sdgs">{{ $item->sdgs }}</td>
                        <td data-field="data_existing">{{ $item->data_existing }}</td>
                        <td data-field="target_capaian">{{ $item->target_capaian }}</td>
                        <td data-field="lokasi">{{ $item->lokasi }}</td>
                        <td data-field="volume" data-raw="{{ $item->volume }}">
                            {{ (int) $item->volume }}
                        </td>

                        <td data-field="satuan">
                            {!! $item->satuan_format !!}
                        </td>
                        <td data-field="anggaran" data-type="numeric">
                            <div onclick="toggleDetail(this)" style="cursor:pointer;">

                                {{ number_format($item->anggaran ?? 0, 0, ',', '.') }}

                                <div class="detail-volume" style="display:none; font-size:12px; color:#666;">
                                    Anggaran: {{ number_format($item->total_anggaran_rpjm ?? 0, 0, ',', '.') }} <br>
                                    Terpakai: {{ number_format($item->total_anggaran_terpakai ?? 0, 0, ',', '.') }} <br>
                                    Sisa: {{ number_format($item->sisa_anggaran ?? 0, 0, ',', '.') }}
                                </div>

                            </div>
                        </td>
                        <td data-field="sumber_dana">{{ $item->sumber_dana }}</td>
                        <td data-field="penerima_laki">{{ $item->penerima_laki }}</td>
                        <td data-field="penerima_perempuan">{{ $item->penerima_perempuan }}</td>
                        <td data-field="penerima_rtm">{{ $item->penerima_rtm }}</td>
                        <td data-field="waktu_pelaksanaan">{{ $item->waktu_pelaksanaan }}</td>
                        <td data-field="pelaksana_kegiatan">{{ $item->pelaksana_kegiatan }}</td>
                        <td data-field="rencana_tpk">{{ $item->rencana_tpk }}</td>
                        <td data-field="pola_pelaksanaan">{{ $item->pola_pelaksanaan }}</td>

                        <td class="action-btn">
                            <div class="action-wrapper">
                                <div class="action-group">
                                    <button type="button"
                                        class="btn-action btn-reject"
                                        data-url="/admin/susunrkp/hapus/{{ $item->id }}"
                                        onclick="showDeletePopup(this, 'reject'); return false;"
                                        title="Tolak">
                                        ✕
                                    </button>
                                </div>
                                <button type="button"
                                    class="btn-icon-action btn-edit"
                                    onclick="editRow(this.closest('tr')); return false;"
                                    title="Edit">
                                    <img src="{{ asset('images/pencil-square.svg') }}" class="action-icon">
                                </button>
                            </div>
                        </td>
                        @empty
                    <tr>
                        <td colspan="21" style="text-align:center;">
                            Belum ada data RKPDes
                        </td>
                    </tr>
                    @endforelse

                </tbody>

            </table>
        </div>

        @if($details instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="table-pagination">
            {{ $details->links('pagination::bootstrap-5') }}
        </div>
        @endif

        <div class="table-footer">
            <button class="btn-approve-all" onclick="tetapkanRkpdes()">
                Tetapkan RKPDes
            </button>
        </div>

    </div>

</div>

<div id="tambahModal" class="modal">
    <div class="modal-content" style="max-width: 800px;">
        <div class="modal-header">
            <h2 class="modal-title">Tambah Kegiatan RKPDes</h2>
            <button class="close-btn" onclick="closeModal('tambahModal')">&times;</button>
        </div>

        <form action="{{ route('admin.susunrkp.store') }}" method="POST">
            @csrf

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">

                <div class="form-group">
                    <label class="form-label">Tahun</label>
                    <select id="tahunModal" class="form-input" required>
                        <option value="">Pilih Tahun</option>
                        @foreach($tahunList as $th)
                        <option value="{{ $th }}">{{ $th }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Dusun</label>
                    <select id="dusunModal" class="form-input" required>
                        <option value="">Pilih Dusun</option>
                        @foreach($dusunList as $dusun)
                        <option value="{{ $dusun->id }}">{{ $dusun->nama_dusun }}</option>
                        @endforeach
                    </select>
                </div>

                <input type="hidden" name="tahun" id="tahunHidden">

                <div class="form-group">
                    <label class="form-label">Kegiatan (RPJMDes)</label>
                    <select class="form-input" name="rpjmdes_detail_id" id="kegiatanSelect" required>
                        <option value="">Pilih Tahun & Dusun dulu</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Info</label>
                    <small id="infoSisa" style="color:#666;">-</small>
                </div>

                <div class="form-group">
                    <label class="form-label">Lokasi</label>
                    <input type="text" class="form-input" name="lokasi" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Volume</label>
                    <input type="number" class="form-input" name="volume" id="volumeInput" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Satuan</label>
                    <input type="text" class="form-input" name="satuan" id="satuanInput" readonly>
                </div>

                <div style="display: flex; gap: 10px;">
                    <div class="form-group" style="flex: 1;">
                        <label class="form-label">LK</label>
                        <input type="number" class="form-input" name="penerima_laki">
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label class="form-label">PR</label>
                        <input type="number" class="form-input" name="penerima_perempuan">
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label class="form-label">RTM</label>
                        <input type="number" class="form-input" name="penerima_rtm">
                    </div>
                </div>
            </div>

            <div class="modal-footer" style="grid-column: span 2;">
                <button type="button" class="btn-cancel-modal" onclick="closeModal('tambahModal')">Batal</button>
                <button type="submit" class="btn-submit">Simpan</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('js')
<script src="{{ asset('js/admin/penyusunanrkpdes.js') }}"></script>
@endsection