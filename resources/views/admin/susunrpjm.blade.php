@extends('layouts.admin')

@section('title', 'Penyusunan RPJMDes')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/index.css') }}">
<link rel="stylesheet" href="{{ asset('css/header.css') }}">
@endsection

@section('content')

<div class="card">

    <div class="card-header">Penyusunan RPJMDes</div>

    <div class="table-container">

        <div class="table-controls">
            <div class="table-left">
                <div class="left-actions" style="display: flex; align-items: center; gap: 10px;">

                    <button type="button" onclick="openModal('periodeModal')" class="btn">
                        Tambah Periode
                    </button>

                    @if(request('periode_id'))
                    <form action="{{ route('admin.rpjmdes.periode.delete', request('periode_id')) }}"
                        method="POST"
                        onsubmit="return confirm('Yakin ingin menghapus periode ini? Data usulan tidak akan terhapus.')">
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="btn-delete">
                            Hapus Periode
                        </button>
                    </form>
                    @endif

                    <form method="GET" id="entriesForm" class="entries-group" style="display:flex; align-items:center; gap:5px;">
                        <input type="hidden" name="periode_id" value="{{ request('periode_id') }}">
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
            </div>



            <div class="table-filter">

                <form action="{{ route('admin.rpjmdes.index') }}" method="GET" class="table-filter">

                    {{-- FILTER PERIODE --}}
                    <label class="filter-label">Periode</label>
                    <select class="filter" name="periode_id" onchange="this.form.submit()">
                        <option value="">Pilih Periode</option>

                        @foreach($periodeList as $p)
                        <option value="{{ $p->id }}"
                            {{ request('periode_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->nama_periode }}
                        </option>
                        @endforeach
                    </select>

                    {{-- FILTER DUSUN --}}
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

                <form action="{{ route('admin.susunrpjmdes.export') }}" method="GET" class="export-form">
                    <input type="hidden" name="periode_id" value="{{ request('periode_id') }}">
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
                        <th>Gagasan Kegiatan</th>
                        <th>Lokasi</th>
                        <th>Volume</th>
                        <th>Satuan</th>
                        <th>LK</th>
                        <th>PR</th>
                        <th>RTM</th>
                        <th>Bidang</th>
                        <th>Sub Bidang</th>
                        <th>Kegiatan</th>
                        <th>Sasaran/Manfaat</th>
                        <th>Waktu Pelaksanaan</th>
                        <th>Jumlah Biaya</th>
                        <th>Sumber</th>
                        <th>Pola Pelaksanaan</th>
                        <th width="100">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @if(!request('periode_id'))
                    <tr>
                        <td colspan="18" style="text-align:center; padding:20px;">

                            @if($periodeList->isEmpty())
                            <b>Buat periode RPJMDes terlebih dahulu</b>
                            @else
                            <b>Pilih periode terlebih dahulu</b>
                            @endif

                        </td>
                    </tr>
                    @else

                    @forelse($data as $i => $item)
                    <tr
                        data-id="{{ $item->id }}"
                        data-url="{{ route('admin.penyusunanrpjm.update', $item->id) }}"
                        data-bidang='@json($bidang)'
                        data-subbidang='@json($subbidang)'
                        data-kegiatan='@json($kegiatan)'
                        data-rt-rws='@json($rtRws ?? [])'
                        data-dusun-id="{{ $item->usulan->rtrw->dusun_id ?? '' }}"
                        data-rt-rw-id="{{ $item->usulan->rtrw_id ?? '' }}">
                        <td>{{ $data->firstItem() + $i }}</td>
                        <td class="readonly">
                            {{ $item->usulan->rtrw->dusun->nama_dusun ?? '-' }}
                        </td>
                        <td class="readonly">
                            {{ $item->usulan->gagasan_kegiatan }}
                        </td>
                        <td data-field="lokasi">{{ $item->usulan->lokasi }}</td>
                        <td data-field="volume" data-raw="{{ $item->usulan->volume }}">
                            <div
                                @if(in_array($item->usulan->satuan, ['m2', 'm3']))
                                onclick="toggleDetail(this)" style="cursor:pointer;"
                                @endif
                                >
                                {{ $item->usulan->volume_hitung ?? '-' }}

                                @if(in_array($item->usulan->satuan, ['m2', 'm3']))
                                <div class="detail-volume" style="display:none; font-size:12px; color:#666;">
                                    {{ str_replace('x', ' x ', $item->usulan->volume) }}
                                </div>
                                @endif
                            </div>
                        </td>

                        <td data-field="satuan">
                            @php
                            $satuan = $item->usulan->satuan;
                            @endphp

                            @if($satuan == 'm2')
                            m<sup>2</sup>
                            @elseif($satuan == 'm3')
                            m<sup>3</sup>
                            @else
                            {{ $satuan }}
                            @endif
                        </td>
                        <td data-field="lk">{{ $item->usulan->penerima_laki }}</td>
                        <td data-field="pr">{{ $item->usulan->penerima_perempuan }}</td>
                        <td data-field="rtm">{{ $item->usulan->penerima_rtm }}</td>
                        <td data-field="bidang_id" data-value="{{ $item->bidang_id ?? '' }}">{{ $item->bidang->nama_bidang ?? '-' }}</td>
                        <td data-field="sub_bidang_id" data-value="{{ $item->sub_bidang_id ?? '' }}">{{ $item->subBidang->nama_sub_bidang ?? '-' }}</td>
                        <td data-field="kegiatan_id" data-value="{{ $item->kegiatan_id ?? '' }}">{{ $item->kegiatan->nama_kegiatan ?? '-' }}</td>
                        <td data-field="sasaran">{{ $item->sasaran_manfaat }}</td>
                        <td data-field="waktu">{{ implode(', ', $item->tahun_pelaksanaan ?? []) }}</td>
                        <td data-field="biaya" data-type="numeric">
                            {{ number_format($item->anggaran, 0, ',', '.') }}
                        </td>
                        <td data-field="sumber">{{ $item->sumber }}</td>
                        <td data-field="pola">{{ $item->pola_pelaksanaan }}</td>
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
                                    data-url="{{ route('admin.penyusunanrpjm.delete', $item->id) }}"
                                    onclick="showDeletePopup(this); return false;"
                                    title="Hapus">
                                    <img src="{{ asset('images/trash3-fill.svg') }}" class="action-icon">
                                </button>

                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="18" class="text-center">Belum ada data usulan</td>
                    </tr>
                    @endforelse
                    @endif
                </tbody>
            </table>
        </div>

        @if($data && method_exists($data, 'links'))
        <div style="margin-top:10px;">
            {{ $data->links('pagination::bootstrap-5') }}
        </div>
        @endif

        <div class="table-footer">
            <button class="btn-approve-all" onclick="openModal('confirmModal')">
                Tetapkan RPJMDes
            </button>
        </div>

    </div>
</div>

<!-- Modal Tambah Periode RPJMDes -->
<div id="periodeModal" class="modal">
    <div class="modal-content" style="max-width: 500px;">

        <div class="modal-header">
            <h2 class="modal-title">Tambah Periode RPJMDes</h2>
            <button class="close-btn" onclick="closeModal('periodeModal')">&times;</button>
        </div>

        <form action="/admin/rpjmdes/periode/tambah" method="POST">
            @csrf

            <div class="form-group">
                <label class="form-label">Nama RPJMDes</label>
                <input type="text" class="form-input" name="nama" placeholder="Contoh: RPJMDes 2025-2030" required>
            </div>

            <div style="display: flex; gap: 10px;">
                <div class="form-group" style="flex: 1;">
                    <label class="form-label">Tahun Mulai</label>
                    <input type="number" class="form-input" name="tahun_mulai" required>
                </div>

                <div class="form-group" style="flex: 1;">
                    <label class="form-label">Tahun Selesai</label>
                    <input type="number" class="form-input" name="tahun_selesai" required>
                </div>
            </div>

            <div class="modal-footer" style="margin-top: 20px;">
                <button type="button" class="btn-cancel-modal" onclick="closeModal('periodeModal')">
                    Batal
                </button>
                <button type="submit" class="btn-submit">
                    Simpan
                </button>
            </div>

        </form>

    </div>
</div>

<!-- Modal Konfirmasi Tetapkan RPJMDes -->
<div id="confirmModal" class="modal">
    <div class="modal-content confirm-modal">

        <h3>Konfirmasi</h3>

        <p>
            Apakah Anda yakin ingin menetapkan RPJMDes ini?
            <span class="warning-text">
                Data tidak dapat diubah setelah ditetapkan
            </span>
        </p>

        <div class="confirm-actions">
            <button class="btn-cancel-modal" onclick="closeModal('confirmModal')">
                Batal
            </button>

            <button class="btn-submit" id="btnTetapkan"
                data-url="{{ route('admin.rpjmdes.tetapkan') }}"
                onclick="submitTetapkan()">
                Ya, Tetapkan
            </button>
        </div>

    </div>
</div>

@endsection
@section('js')
<script src="{{ asset('js/admin/kelolausulan.js') }}"></script>
@endsection