@extends('layouts.admin')

@section('title', 'Laporan Tahunan')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/index.css') }}">
<link rel="stylesheet" href="{{ asset('css/header.css') }}">
@endsection

@section('content')

<div class="card">

    <div class="card-header">Laporan Tahunan</div>

    <div class="table-container">

        <div class="table-controls">

            <form method="GET" action="" class="table-controls">

                <div class="table-filter">

                    <select name="tahun" class="filter" onchange="this.form.submit()">
                        @foreach($listTahun as $t)
                        <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>
                            {{ $t }}
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
                </div>

                <div class="table-filter">

                    <label class="filter-label">Filter</label>

                    <select class="filter" name="dusun_id" onchange="this.form.submit()">
                        <option value="">Semua Dusun</option>

                        @foreach($dusunList as $dusun)
                        <option value="{{ $dusun->id }}"
                            {{ $dusunId == $dusun->id ? 'selected' : '' }}>
                            {{ $dusun->nama_dusun }}
                        </option>
                        @endforeach
                    </select>

                    <button type="submit" formaction="{{ route('admin.laporan.export') }}"
                        formmethod="GET"
                        class="btn-export">
                        Export Excel
                    </button>

                </div>

            </form>

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
                        <th>Lokasi</th>
                        <th>Volume Target</th>
                        <th>Volume Realisasi</th>
                        <th>Capaian (%)</th>
                        <th>Waktu Pelaksanaan</th>
                        <th>Biaya</th>
                        <th>Sumber Dana</th>
                        <th width="100">Status</th>
                    </tr>
                </thead>

                <tbody>
                    @if($tahun && $data->count() > 0)
                    @foreach ($data as $item)
                    <tr>
                        <td>{{ $data->firstItem() + $loop->index }}</td>
                        <td>{{ $item->user->dusun->nama_dusun ?? '-' }}</td>
                        <td>{{ $item->bidang->nama_bidang ?? '-' }}</td>
                        <td>{{ $item->subBidang->nama_sub_bidang ?? '-' }}</td>
                        <td>{{ $item->kegiatan->nama_kegiatan ?? '-' }}</td>
                        <td>{{ $item->lokasi ?? '-' }}</td>
                        <td>{{ rtrim(rtrim(number_format($item->volume, 2, '.', ''), '0'), '.') }} {{ $item->satuan }}</td>
                        <td>
                            @php
                            $total = $item->monitoring->sum('volume_realisasi');
                            @endphp

                            @if($total > 0)
                            {{ rtrim(rtrim(number_format($total, 2, '.', ''), '0'), '.') }} {{ $item->satuan }}
                            @else
                            -
                            @endif
                        </td>
                        <td>
                            @php
                            $total = $item->monitoring->sum('volume_realisasi');
                            $target = $item->volume;
                            @endphp

                            @if($target > 0)
                            {{ round(($total / $target) * 100, 2) }}%
                            @else
                            -
                            @endif
                        </td>
                        <td>{{ $item->waktu_pelaksanaan ?? '-' }}</td>
                        <td>{{ number_format($item->anggaran, 0, ',', '.') }}</td>
                        <td>{{ $item->sumber_dana ?? '-' }}</td>
                        <td>{!! statusButton($item->status_progres) !!}</td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td colspan="13" class="text-center">
                            Data laporan tahunan belum tersedia
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
            <div class="pagination-wrapper mt-2">
                {{ $data->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

@endsection