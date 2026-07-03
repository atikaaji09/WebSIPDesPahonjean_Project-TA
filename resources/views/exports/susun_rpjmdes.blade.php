@php
$tahunRange = collect($data)
->pluck('tahun_pelaksanaan')
->map(fn($t) => is_array($t) ? $t : json_decode($t, true))
->flatten()
->filter()
->unique()
->sort()
->take(6)
->values()
->toArray();

$totalKolom = 13 + count($tahunRange);
@endphp

<table border="1" cellspacing="0" cellpadding="5" style="border-collapse: collapse; width:100%;">
    <tr>
        <td colspan="{{ $totalKolom }}" style="text-align:center; font-weight:bold;">
            RENCANA RPJMDes DESA
        </td>
    </tr>
    <thead>
        <tr>
            <th rowspan="2">No</th>
            <th rowspan="2">Bidang / Sub Bidang / Kegiatan</th>
            <th rowspan="2">Lokasi</th>
            <th rowspan="2">Volume</th>
            <th rowspan="2">Satuan</th>

            <th colspan="3">Penerima Manfaat</th>

            <th rowspan="2">Sasaran / Manfaat</th>

            <th colspan="{{ count($tahunRange) }}">Waktu Pelaksanaan</th>

            <th rowspan="2">Anggaran</th>
            <th rowspan="2">Sumber Dana</th>
            <th rowspan="2">Pola Pelaksanaan</th>
        </tr>

        <tr>
            <th>LK</th>
            <th>PR</th>
            <th>RTM</th>

            @foreach($tahunRange as $th)
            <th>{{ $th }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>

        @if($data->isEmpty())
        <tr>
            <td colspan="{{ $totalKolom }}" style="text-align:center;">
                Tidak ada data
            </td>
        </tr>
        @else

        @php $noBidang = 1; @endphp

        @foreach($data->groupBy('bidang_id') as $bidangGroup)

        <tr>
            <td>{{ $noBidang }}</td>
            <td colspan="{{ $totalKolom - 1 }}">
                <b>{{ $bidangGroup->first()->bidang->nama_bidang ?? '-' }}</b>
            </td>
        </tr>

        @php $noSub = 1; @endphp

        @foreach($bidangGroup->groupBy('sub_bidang_id') as $subGroup)

        <tr>
            <td>{{ $noBidang }}.{{ $noSub }}</td>
            <td colspan="{{ $totalKolom - 1 }}">
                {{ $subGroup->first()->subBidang->nama_sub_bidang ?? '-' }}
            </td>
        </tr>

        @php $noKeg = 1; @endphp

        @foreach($subGroup as $item)

        @php
        $tahunList = is_array($item->tahun_pelaksanaan)
        ? $item->tahun_pelaksanaan
        : json_decode($item->tahun_pelaksanaan, true);
        @endphp

        <tr>
            <td>{{ $noBidang }}.{{ $noSub }}.{{ $noKeg }}</td>

            <td>{{ $item->kegiatan->nama_kegiatan ?? '-' }}</td>
            <td>{{ optional($item->usulan)->lokasi }}</td>
            <td>{{ $item->volume ?? optional($item->usulan)->volume }}</td>
            <td>{{ optional($item->usulan)->satuan }}</td>

            <td>{{ optional($item->usulan)->penerima_laki }}</td>
            <td>{{ optional($item->usulan)->penerima_perempuan }}</td>
            <td>{{ optional($item->usulan)->penerima_rtm }}</td>

            <td>{{ $item->sasaran_manfaat }}</td>

            @foreach($tahunRange as $th)
            <td style="text-align:center;">
                {{ in_array($th, $tahunList ?? []) ? '✔' : '' }}
            </td>
            @endforeach

            <td>{{ number_format($item->anggaran, 0, ',', '.') }}</td>
            <td>{{ $item->sumber }}</td>
            <td>{{ $item->pola_pelaksanaan }}</td>
        </tr>

        @php $noKeg++; @endphp
        @endforeach

        @php $noSub++; @endphp
        @endforeach

        @php $noBidang++; @endphp
        @endforeach

        @endif

    </tbody>
</table>
