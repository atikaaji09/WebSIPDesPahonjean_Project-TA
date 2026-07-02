@extends('layouts.kadus')

@section('title', 'Pengajuan RKPDes')

@section('css')
<link rel="stylesheet" href="{{ asset('css/kadus/index.css') }}">
<link rel="stylesheet" href="{{ asset('css/header.css') }}">
@endsection

@section('content')

<div class="card">

    <div class="card-header">Pengajuan RKPDes</div>

    <div class="table-container">

        <div class="table-controls">

            <div class="table-left" style="display:flex; align-items:center; gap:10px;">
                <button onclick="openModal('tambahModal')" class="btn-tambah">Tambah</button>

                <form method="GET" id="entriesForm" class="entries-group"
                    style="display:flex; align-items:center; gap:5px;">

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
                <input type="text" placeholder="Search..." class="search-input">
            </div>
        </div>
        <div class="table-scroll">

            <table>

                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>Dusun</th>
                        <th>Kegiatan</th>
                        <th>Volume & Satuan</th>
                        <th>Lokasi</th>
                        <th>LK</th>
                        <th>PR</th>
                        <th>RTM</th>
                        <th width="100">Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($draft as $i => $item)
                    <tr data-id="{{ $item->id }}">
                        <td>{{ $draft->firstItem() + $i }}</td>
                        <td>{{ $item->rpjmdesDetail->usulan->rtrw->dusun->nama_dusun ?? '-' }}</td>
                        <td>{{ $item->kegiatan->nama_kegiatan ?? '-' }}</td>
                        <td>
                            {{ (int) $item->volume }}
                            <small style="color:#666;">{{ $item->satuan }}</small>
                            @if(in_array($item->satuan, ['m2', 'm3']))
                            <div class="detail-volume" style="display:none; font-size:12px; color:#666;">
                                {{ str_replace('x', ' x ', $item->volume) }}
                            </div>
                            @endif
                        </td>
                        <td>{{ $item->lokasi }}</td>
                        <td>{{ $item->penerima_laki }}</td>
                        <td>{{ $item->penerima_perempuan }}</td>
                        <td>{{ $item->penerima_rtm }}</td>
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
                                    data-url="/kadus/pengajuanrkp/hapus/{{ $item->id }}"
                                    onclick="showDeletePopup(this); return false;"
                                    title="Hapus">
                                    <img src="{{ asset('images/trash3-fill.svg') }}" class="action-icon">
                                </button>

                            </div>
                        </td>
                    </tr>

                    @empty
                    <tr>
                        <td colspan="9" class="text-center">
                            Belum ada data pengajuan RKPDes
                        </td>
                    </tr>
                    @endforelse

                </tbody>

            </table>

            <div class="pagination-wrapper mt-2">
                {{ $draft->links('pagination::bootstrap-5') }}
            </div>

            <div class="table-footer">
                <button onclick="kirimPengajuan()" type="button" class="btn-approve-all">
                    Kirim Pengajuan
                </button>
            </div>

        </div>

    </div>

    <!-- Modal Tambah Pengajuan RKPDes -->
    <div id="tambahModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Tambah Pengajuan RKPDes</h2>
                <button class="close-btn" onclick="closeModal('tambahModal')">&times;</button>
            </div>

            <form onsubmit="tambahKeTabel(event)">
                @csrf

                <div class="form-group">
                    <label class="form-label">Tahun</label>
                    <select class="form-input" name="tahun" id="filterTahun" required onchange="loadKegiatanByTahun()">
                        <option value="">Pilih Tahun</option>
                        @foreach($tahunList as $t)
                        <option value="{{ $t }}">{{ $t }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Dusun</label>
                    <input type="text" class="form-input" value="{{ $user->dusun->nama_dusun ?? '-' }}" readonly>

                    <input type="hidden" name="dusun_id" value="{{ $user->dusun_id }}">
                </div>

                <div class="form-group">
                    <label class="form-label">Kegiatan (RPJMDes)
                    </label>
                    <select class="form-input" name="rpjmdes_detail_id" id="kegiatanSelect" disabled>
                        <option value="">Pilih Tahun dulu</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Volume</label>

                    <div style="display:flex; gap:8px;">
                        <input type="number" class="form-input" name="panjang" id="panjang" placeholder="Panjang"
                            step="0.01">
                        <input type="number" class="form-input" name="lebar" id="lebar" placeholder="Lebar" step="0.01">
                        <input type="number" class="form-input" name="tinggi" id="tinggi" placeholder="Tinggi"
                            step="0.01">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Satuan</label>
                    <input type="text" id="satuan" class="form-input" readonly>
                </div>

                <div class="form-group">
                    <small style="color:#666;">
                        Sisa volume tersedia: <span id="sisaVolume">-</span>
                    </small>
                </div>

                <div class="form-group">
                    <label class="form-label">Lokasi</label>
                    <input type="text" class="form-input" name="lokasi" id="inputLokasi" required>
                </div>

                <div style="display: flex; gap: 10px;">
                    <div class="form-group" style="flex: 1;">
                        <label class="form-label">LK</label>
                        <input type="number" class="form-input" name="lk" id="inputLK">
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label class="form-label">PR</label>
                        <input type="number" class="form-input" name="pr" id="inputPR">
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label class="form-label">RTM</label>
                        <input type="number" class="form-input" name="rtm" id="inputRTM">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-cancel-modal" onclick="closeModal('tambahModal')">Batal</button>
                    <button type="submit" class="btn-submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    @endsection
    @section('js')
    <script src="{{ asset('js/admin/kelolausulan.js') }}"></script>
    @endsection