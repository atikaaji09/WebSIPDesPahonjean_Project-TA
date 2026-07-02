@extends('layouts.admin')

@section('title', 'Monitoring RKPDes')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/index.css') }}">
<link rel="stylesheet" href="{{ asset('css/header.css') }}">
@endsection

@section('content')

<div class="card">

    <div class="card-header">Monitoring RKPDes</div>

    <div class="table-container">
        <div class="table-controls">
            <div class="table-filter">
                <label class="filter-label">Pilih Tahun</label>
                <form action="{{ route('admin.monitoringrkp') }}" method="GET">
                    <select name="tahun" class="filter" onchange="this.form.submit()">
                        <option value="" disabled {{ !$selectedTahun ? 'selected' : '' }}>Tahun RKPDes</option>
                        @foreach ($tahunList as $tahun)
                        <option value="{{ $tahun }}" {{ $selectedTahun == $tahun ? 'selected' : '' }}>
                            {{ $tahun }}
                        </option>
                        @endforeach
                    </select>

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

                <label class="filter-label">Filter</label>
                <form action="{{ route('admin.monitoringrkp') }}" method="GET">
                    <input type="hidden" name="tahun" value="{{ $selectedTahun }}">
                    <input type="hidden" name="entries" value="{{ request('entries', 10) }}">
                    <select class="filter" name="dusun_id" onchange="this.form.submit()">
                        <option value="">Semua Dusun</option>
                        @foreach($dusunList as $dusun)
                        <option value="{{ $dusun->id }}" {{ $dusunId == $dusun->id ? 'selected' : '' }}>
                            {{ $dusun->nama_dusun }}
                        </option>
                        @endforeach
                    </select>
                </form>

                @if($selectedTahun)
                <a href="{{ url('/admin/rkpdes/export') }}?tahun={{ $selectedTahun }}&dusun_id={{ $dusunId }}"
                    class="btn-export">
                    Export Excel
                </a>
                @else
                <button class="btn-export" disabled>
                    Export Excel
                </button>
                @endif

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
                        <th>
                            Target Capaian
                            @if($selectedTahun)
                            Tahun {{ $selectedTahun }}
                            @endif
                        </th>
                        <th>Lokasi</th>
                        <th>Volume</th>
                        <th>Volume Realisasi</th>
                        <th>Jumlah Dana</th>
                        <th>Sumber Dana</th>
                        <th>LK</th>
                        <th>PR</th>
                        <th>RTM</th>
                        <th>Waktu Pelaksanaan</th>
                        <th>Pelaksana Kegiatan</th>
                        <th>Rencana TPK</th>
                        <th>Pola Pelaksanaan</th>
                        <th>Status</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($rkpdesDetails as $i => $item)
                    <tr>
                        <td>{{ $rkpdesDetails->firstItem() + $i }}</td>
                        <td>{{ $item->rpjmdesDetail->usulan->rtrw->dusun->nama_dusun ?? '-' }}</td>
                        <td>{{ $item->rpjmdesDetail->bidang->nama_bidang ?? '-' }}</td>
                        <td>{{ $item->rpjmdesDetail->subBidang->nama_sub_bidang ?? '-' }}</td>
                        <td>{{ $item->rpjmdesDetail->kegiatan->nama_kegiatan ?? '-' }}</td>
                        <td>{{ $item->sdgs }}</td>
                        <td>{{ $item->data_existing }}</td>
                        <td>{{ $item->target_capaian }}</td>
                        <td>{{ $item->lokasi }}</td>
                        <td>{{ rtrim(rtrim(number_format($item->volume, 2, '.', ''), '0'), '.') }} {{ $item->satuan }}
                        </td>
                        <td onclick="DetailVolumeRealisasi('{{ $item->id }}')" style="cursor: pointer;">
                            <strong>
                                {{ number_format($item->monitoring->sum('volume_realisasi'), 0) }} {{ $item->satuan }}
                            </strong>
                            <div id="detail-{{ $item->id }}"
                                style="display: none; margin-top: 6px; font-size: 12px; text-align: left;">

                                @forelse($item->monitoring as $index => $m)
                                <div style="margin-bottom: 6px; padding-bottom: 4px; border-bottom: 1px dashed #ddd;">
                                    <div style="font-size: 11px; color: #666;">
                                        {{ $index + 1 }}. {{ \Carbon\Carbon::parse($m->tanggal)->format('d M Y') }}
                                    </div>
                                    <div style="font-weight: 600; font-size: 13px;">
                                        {{ rtrim(rtrim(number_format($m->volume_realisasi, 2, '.', ''), '0'), '.') }}
                                        {{ $item->satuan }}
                                    </div>

                                </div>
                                @empty
                                <div style="color: #999; font-size: 12px;">
                                    Belum ada data
                                </div>
                                @endforelse

                            </div>
                        </td>
                        <td>
                            {{ number_format($item->anggaran_efektif ?? $item->anggaran, 0, ',', '.') }}
                        </td>
                        <td>{{ $item->sumber_dana }}</td>
                        <td>{{ $item->penerima_laki }}</td>
                        <td>{{ $item->penerima_perempuan }}</td>
                        <td>{{ $item->penerima_rtm }}</td>
                        <td>{{ $item->waktu_pelaksanaan }}</td>
                        <td>{{ $item->pelaksana_kegiatan }}</td>
                        <td>{{ $item->rencana_tpk }}</td>
                        <td>{{ $item->pola_pelaksanaan }}</td>
                        <td>
                            {!! statusButton($item->status_progres) !!}
                        </td>
                        <td class="action-btn">
                            <div class="action-wrapper">
                                <div class="action-group">
                                    <button
                                        onclick="openProgresModal('{{ $item->id }}')"
                                        class="btn-aksi-tambah"
                                        {{ in_array($item->status_progres, ['selesai', 'lanjutan']) ? 'disabled' : '' }}>
                                        Tambah Progres
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="22" class="text-center">
                            Belum ada data RKPDes
                        </td>
                    </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
        <div class="pagination-wrapper mt-2">
            {{ $rkpdesDetails->links('pagination::bootstrap-5') }}
        </div>
    </div>

</div>

<div id="tambahProgresModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Tambah Progres Realisasi</h2>
            <button class="close-btn" onclick="closeModal('tambahProgresModal')">&times;</button>
        </div>

        <form action="/admin/monitoringrkp/progres" method="POST">
            @csrf

            <input type="hidden" name="id" id="progres_id">

            <div class="form-group">
                <label class="form-label">Volume Realisasi</label>
                <input type="number" step="0.01" class="form-input" name="volume_realisasi" placeholder="Contoh: 50"
                    required>
            </div>

            <div class="form-group">
                <label class="form-label">Satuan</label>
                <input type="text" class="form-input" name="satuan" placeholder="Contoh: %, M, Unit" required>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-cancel-modal" onclick="closeModal('tambahProgresModal')">Batal</button>
                <button type="submit" class="btn-submit">Simpan</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('js')
<script src="{{ asset('js/admin/monitoringrkpdes.js') }}"></script>
@endsection
