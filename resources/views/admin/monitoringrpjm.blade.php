@extends('layouts.admin')

@section('title', 'Monitoring RPJMDes')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/index.css') }}">
<link rel="stylesheet" href="{{ asset('css/header.css') }}">
@endsection

@section('content')

<div class="card">

    <div class="card-header">Monitoring RPJMDes</div>

    <div class="table-container">

        <form action="{{ route('admin.rpjmdes.monitoring') }}" method="GET">

            <div class="table-controls">

                <div class="table-filter">
                    <label class="filter-label">Periode</label>
                    <select class="filter" name="periode_id" onchange="this.form.submit()">
                        <option value="">Pilih Periode</option>

                        @foreach($periodeList as $periode)
                        <option value="{{ $periode->id }}"
                            {{ $selectedPeriodeId == $periode->id ? 'selected' : '' }}>
                            {{ $periode->nama_periode }}
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
                            {{ request('dusun_id') == $dusun->id ? 'selected' : '' }}>
                            {{ $dusun->nama_dusun }}
                        </option>
                        @endforeach
                    </select>

                    <a href="{{ url('/admin/rpjmdes/export') }}?dusun_id={{ request('dusun_id') }}&periode_id={{ request('periode_id') }}"
                        class="btn-export">
                        Export Excel
                    </a>

                </div>

            </div>

        </form>

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

                    @if(!$selectedPeriodeId)
                    <tr>
                        <td colspan="16" style="text-align:center; padding:20px;">
                            <b>Belum ada periode RPJMDes yang ditetapkan</b>
                        </td>
                    </tr>
                    @else

                    @forelse($data as $i => $item)
                    <tr>
                        <td>{{ $data->firstItem() + $i }}</td>
                        <td>{{ $item->usulan->rtrw->dusun->nama_dusun ?? '-' }}</td>
                        <td>{{ $item->bidang->nama_bidang ?? '-' }}</td>
                        <td>{{ $item->subBidang->nama_sub_bidang ?? '-' }}</td>
                        <td>{{ $item->kegiatan->nama_kegiatan ?? '-' }}</td>
                        <td>{{ $item->usulan->lokasi }}</td>
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
                        <td>{{ (int) $item->volume_terpakai }}</td>
                        <td>{{ $item->sisa }}</td>
                        <td>{!! $item->usulan->satuan_format !!}</td>
                        <td>{{ $item->sasaran_manfaat }}</td>
                        <td>
                            {{ $item->tahun_pelaksanaan 
        ? implode(', ', $item->tahun_pelaksanaan) 
        : '-' 
    }}
                        </td>
                        <td>{{ number_format($item->anggaran, 0, ',', '.') }}</td>
                        <td>{{ $item->sumber }}</td>
                        <td>{{ $item->pola_pelaksanaan }}</td>
                        <td>
                            {!! statusButton($item->status) !!}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="15" style="text-align:center;">
                            Belum ada data RPJMDes yang ditetapkan
                        </td>
                    </tr>
                    @endforelse

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
