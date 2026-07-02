@extends('layouts.kadus')

@section('title', 'RKPDes')

@section('css')
<link rel="stylesheet" href="{{ asset('css/kadus/index.css') }}">
<link rel="stylesheet" href="{{ asset('css/header.css') }}">
@endsection

@section('content')

<div class="card">

    <div class="card-header">Data RKPDes</div>

    <div class="table-container">
        <div class="table-controls">
            <div class="table-filter">
                <label class="filter-label">Pilih Tahun</label>
                <form action="{{ route('kadus.rkpdes') }}" method="GET">
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
                <label class="filter-label">Dusun</label>

                <select name="dusun_id" class="filter" onchange="this.form.submit()">
                    <option value="">Semua Dusun</option>
                    @foreach($dusunList as $d)
                    <option value="{{ $d->id }}" {{ $dusunId == $d->id ? 'selected' : '' }}>
                        {{ $d->nama_dusun }}
                    </option>
                    @endforeach
                </select>
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
                        <td>{{ rtrim(rtrim(number_format($item->volume, 2, '.', ''), '0'), '.') }} {{ $item->satuan }}</td>
                        <td>{{ number_format($item->anggaran, 0, ',', '.') }}</td>
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
                    </tr>
                    @empty
                    <tr>
                        <td colspan="22" class="text-center">Silakan pilih tahun untuk menampilkan data RKPDes</td>
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

@endsection