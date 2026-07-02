@extends('layouts.admin')

@section('title', 'Kelola Usulan')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/index.css') }}">
<link rel="stylesheet" href="{{ asset('css/header.css') }}">
@endsection

@section('content')

<div class="card">

    <div class="card-header">Kelola Usulan</div>

    <div class="table-container">

        <div class="table-controls">

            <div class="table-left">

                <button type="button" onclick="openModal('tambahModal')" class="btn">Tambah</button>

                <form method="GET" id="entriesForm" class="entries-group">

                    <select name="entries" class="entries-select"
                        onchange="document.getElementById('entriesForm').submit()">

                        <option value="10" {{ request('entries') == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('entries') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('entries') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('entries') == 100 ? 'selected' : '' }}>100</option>

                    </select>

                    <span class="entries-label">Entries</span>

                    <input type="hidden" name="dusun_id" value="{{ request('dusun_id') }}">

                </form>

            </div>

            <div class="table-filter">

                {{-- FILTER --}}
                <form method="GET" action="{{ route('admin.kelolausulan') }}">

                    <input type="hidden" name="entries" value="{{ request('entries') }}">

                    <label class="filter-label">Filter</label>

                    <select class="filter" name="dusun_id" onchange="this.form.submit()">
                        <option value="">Semua Dusun</option>
                        @foreach($dusun as $dusunItem)
                        <option value="{{ $dusunItem->id }}"
                            {{ request('dusun_id') == $dusunItem->id ? 'selected' : '' }}>
                            {{ $dusunItem->nama_dusun }}
                        </option>
                        @endforeach
                    </select>
                </form>

                <form action="{{ route('admin.kelolausulan.export') }}" method="GET" class="export-form">
                    <input type="hidden" name="dusun_id" value="{{ request('dusun_id') }}">
                    <input type="hidden" name="entries" value="{{ request('entries') }}">

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
                        <th>Nama</th>
                        <th>Dusun</th>
                        <th>RT/RW</th>
                        <th>Gagasan Kegiatan</th>
                        <th>Lokasi</th>
                        <th>Volume</th>
                        <th>Satuan</th>
                        <th>LK</th>
                        <th>PR</th>
                        <th>RTM</th>
                        <th width="100">Aksi</th>
                    </tr>
                </thead>

                @php
                // Ambil semua RT/RW beserta relasi dusun
                $rtRws = \App\Models\Rtrw::with('dusun')->get();
                @endphp

                <tbody>
                    @forelse($usulan as $i => $item)
                    <tr
                        data-id="{{ $item->id }}"
                        data-url="{{ route('admin.kelolausulan.update', $item->id) }}"
                        data-rt-rws='@json($rtRws)'
                        data-dusun-id="{{ $item->rtrw->dusun->id ?? '' }}"
                        data-rt-rw-id="{{ $item->rtrw->id ?? '' }}">
                        <td>{{ $i + 1 }}</td>
                        <td data-field="nama_lengkap">{{ $item->nama_lengkap }}</td>
                        <td data-field="dusun_id" data-dusun-id="{{ $item->rtrw->dusun->id ?? '' }}" data-dusun='@json($dusun)'>
                            {{ $item->rtrw->dusun->nama_dusun ?? '-' }}
                        </td>
                        <td data-field="rt_rw_id" data-rt-rw='@json($item->rtrw)'>
                            RT {{ $item->rtrw->rt ?? '-' }} / RW {{ $item->rtrw->rw ?? '-' }}
                        </td>
                        <td data-field="gagasan_kegiatan">{{ $item->gagasan_kegiatan }}</td>
                        <td data-field="lokasi">{{ $item->lokasi }}</td>
                        <td data-field="volume" data-raw="{{ $item->volume }}">
                            <div
                                @if(in_array($item->satuan, ['m2', 'm3']))
                                onclick="toggleDetail(this)" style="cursor:pointer;"
                                @endif
                                >
                                {{ $item->volume_hitung }}

                                @if(in_array($item->satuan, ['m2', 'm3']))
                                <div class="detail-volume" style="display:none; font-size:12px; color:#666;">
                                    {{ str_replace('x', ' x ', $item->volume) }}
                                </div>
                                @endif

                            </div>
                        </td>
                        <td data-field="satuan" data-raw="{{ $item->satuan }}">
                            {!! str_replace(['m2','m3'], ['m<sup>2</sup>','m<sup>3</sup>'], $item->satuan) !!}
                        </td>
                        <td data-field="penerima_laki">{{ $item->penerima_laki }}</td>
                        <td data-field="penerima_perempuan">{{ $item->penerima_perempuan }}</td>
                        <td data-field="penerima_rtm">{{ $item->penerima_rtm }}</td>
                        <td class="action-btn">
                            <div class="action-wrapper">
                                <div class="action-group">
                                    <button
                                        class="btn-action btn-approve"
                                        onclick="confirmApprove(this)"
                                        data-id="{{ $item->id }}">
                                        ✓
                                    </button>

                                    <button
                                        class="btn-action btn-reject"
                                        onclick="confirmReject(this)"
                                        data-id="{{ $item->id }}">
                                        ✕
                                    </button>
                                </div>
                                <button type="button"
                                    class="btn-icon-action btn-edit"
                                    onclick="editRow(this.closest('tr')); return false;"
                                    data-url="{{ route('admin.kelolausulan.update', $item->id) }}"
                                    title="Edit">
                                    <img src="{{ asset('images/pencil-square.svg') }}" class="action-icon">
                                </button>

                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="12" style="text-align:center;">Belum ada data usulan</td>
                    </tr>
                    @endforelse
                </tbody>

            </table>

            <div class="pagination-wrapper mt-2">
                {{ $usulan->links('pagination::bootstrap-5') }}
            </div>

            <div class="table-footer">
                <button class="btn-approve-all" onclick="confirmApproveAll()">
                    Setujui Semua
                </button>
            </div>

        </div>

    </div>

    <div id="tambahModal" class="modal">
        <div class="modal-content">

            <div class="modal-header">
                <h2 class="modal-title">Tambah Usulan</h2>
                <button class="close-btn" onclick="closeModal('tambahModal')">&times;</button>
            </div>
            <form action="{{ route('admin.kelolausulan.storeAdmin') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label>No Usulan</label>
                    <input type="text" class="form-input" value="{{ $noUsulan }}" readonly>
                </div>

                <div class="form-group">
                    <label>Nama</label>
                    <input type="text" name="nama" class="form-input" required>
                </div>

                <div class="form-group">
                    <label>Dusun</label>
                    <select name="dusun_id" class="form-input" required>
                        <option value="">Pilih Dusun</option>
                        @foreach($dusun as $d)
                        <option value="{{ $d->id }}">{{ $d->nama_dusun }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>RT/RW</label>
                    <select name="rt_rw_id" class="form-input" required id="rtRwSelectModal">
                        <option value="">Pilih RT/RW</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Gagasan Kegiatan</label>
                    <input type="text" class="form-input" name="gagasan_kegiatan" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Lokasi</label>
                    <input type="text" class="form-input" name="lokasi" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Volume</label>
                    <input
                        type="text"
                        class="form-input"
                        name="volume"
                        placeholder="Contoh: 100, 12x15, 12x1.2x2"
                        required>
                </div>

                <div class="form-group">
                    <label>Satuan</label>
                    <select name="satuan" class="form-input" required>
                        <option value="m">Meter (m)</option>
                        <option value="m2">Meter Persegi (m²)</option>
                        <option value="m3">Meter Kubik (m³)</option>
                        <option value="unit">Unit</option>
                        <option value="buah">Buah</option>
                        <option value="orang">Orang</option>
                        <option value="persen">%</option>
                        <option value="ekor">Ekor</option>
                        <option value="paket">Paket</option>
                        <option value="kg">Kg</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">LK</label>
                    <input type="number" class="form-input" name="lk">
                </div>

                <div class="form-group">
                    <label class="form-label">PR</label>
                    <input type="number" class="form-input" name="pr">
                </div>

                <div class="form-group">
                    <label class="form-label">RTM</label>
                    <input type="number" class="form-input" name="rtm">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel-modal" onclick="closeModal('tambahModal')">
                        Batal
                    </button>
                    <button type="submit" class="btn-submit">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    @endsection
    @section('js')
    <script src="{{ asset('js/admin/kelolausulan.js') }}"></script>
    @endsection