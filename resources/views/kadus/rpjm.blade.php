@extends('layouts.kadus')

@section('title', 'RPJMDes')

@section('css')
<link rel="stylesheet" href="{{ asset('css/kadus/index.css') }}">
<link rel="stylesheet" href="{{ asset('css/header.css') }}">
@endsection

@section('content')
<div class="card">
    <div class="card-header">Data RPJMDes</div>

    <div class="table-container">
        <form method="GET">
            <div class="table-controls">
                <div class="table-filter">
                    <label class="filter-label">Pilih Periode</label>
                    <select class="filter" name="periode_id" onchange="this.form.submit()">
                        <option value="">Pilih Periode</option>
                        @foreach($periodes as $periode)
                        <option value="{{ $periode->id }}" {{ $periodeId == $periode->id ? 'selected' : '' }}>
                            {{ $periode->tahun_mulai }} - {{ $periode->tahun_selesai }}
                        </option>
                        @endforeach
                    </select>

                    <div class="table-filter">
                        <select name="entries" class="entries-select" onchange="this.form.submit()">
                            <option value="10" {{ request('entries') == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ request('entries') == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('entries') == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('entries') == 100 ? 'selected' : '' }}>100</option>
                        </select>
                        Entries
                    </div>
                </div>
                <div class="table-filter">
                    <label class="filter-label">Dusun</label>

                    <select name="dusun_id" class="filter" onchange="this.form.submit()">
                        <option value="">Semua Dusun</option>
                        @foreach($dusunList as $d)
                        <option value="{{ $d->id }}" {{ request('dusun_id') == $d->id ? 'selected' : '' }}>
                            {{ $d->nama_dusun }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>

        <div class="table-scroll">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Dusun</th>
                        <th>Bidang</th>
                        <th>Sub Bidang</th>
                        <th>Kegiatan</th>
                        <th>Lokasi</th>
                        <th>Prakiraan Volume</th>
                        <th>Terpakai</th>
                        <th>Sisa</th>
                        <th>Satuan</th>
                        <th>Sasaran/Manfaat</th>
                        <th>Waktu Pelaksanaan</th>
                        <th>Jumlah Biaya</th>
                        <th>Sumber</th>
                        <th>Pola Pelaksanaan</th>
                        <th width="120">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @if(!$data->count())
                    <tr>
                        <td colspan="15" style="text-align:center; padding:20px;">
                            Silakan pilih periode RPJMDes terlebih dahulu
                        </td>
                    </tr>
                    @else
                    @foreach($data as $i => $item)
                    <tr>
                        <td>{{ $data->firstItem() + $i }}</td>
                        <td>{{ $item->usulan->rtrw->dusun->nama_dusun ?? '-' }}</td>
                        <td>{{ $item->bidang->nama_bidang ?? '-' }}</td>
                        <td>{{ $item->subBidang->nama_sub_bidang ?? '-' }}</td>
                        <td>{{ $item->kegiatan->nama_kegiatan ?? '-' }}</td>
                        <td>{{ $item->usulan->lokasi ?? '-' }}</td>
                        <td>
                            @if($item->is_dimensi)
                            <div onclick="toggleDetail(this)" style="cursor:pointer;">
                                {{ $item->volume_hasil }}

                                <div class="detail-volume" style="display:none; font-size:12px; color:#666;">
                                    {{ str_replace('x', ' x ', $item->volume_asli) }}
                                </div>
                            </div>
                            @else
                            {{ $item->volume_hasil }}
                            @endif
                        </td>
                        <td>
                            {{ (int) $item->volume_terpakai }}
                        </td>
                        <td>
                            {{ $item->sisa }}
                        </td>
                        <td>{!! $item->usulan->satuan_format ?? $item->usulan->satuan !!}</td>
                        <td>{{ $item->sasaran_manfaat ?? '-' }}</td>
                        <td>{{ $item->tahun_pelaksanaan ? implode(', ', $item->tahun_pelaksanaan) : '-' }}</td>
                        <td>{{ $item->anggaran ? number_format($item->anggaran, 0, ',', '.') : '-' }}</td>
                        <td>{{ $item->sumber ?? '-' }}</td>
                        <td>{{ $item->pola_pelaksanaan ?? '-' }}</td>
                        <td>{!! statusButton($item->status) !!}</td>
                    </tr>
                    @endforeach
                    @endif
                </tbody>
            </table>
        </div>
        <div class="pagination-wrapper mt-2">
            {{ $data->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection


@section('js')
<script src="{{ asset('js/admin/kelolausulan.js') }}"></script>
@endsection