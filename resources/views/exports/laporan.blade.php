<table border="1">
    <thead>
        <tr>
            <th>No</th>
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
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $i => $item)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $item->user->dusun->nama_dusun ?? '-' }}</td>
            <td>{{ $item->bidang->nama_bidang ?? '-' }}</td>
            <td>{{ $item->subBidang->nama_sub_bidang ?? '-' }}</td>
            <td>{{ $item->kegiatan->nama_kegiatan ?? '-' }}</td>
            <td>{{ $item->lokasi ?? '-' }}</td>
            <td>{{ $item->volume }} {{ $item->satuan }}</td>
            @php
            $realisasi = $item->monitoring->sum('volume_realisasi') ?? 0;
            @endphp

            <td>
                {{ $realisasi > 0 ? $realisasi . ' ' . $item->satuan : '-' }}
            </td>
            @php
            $target = (float) $item->volume;
            $realisasi = $item->monitoring->sum('volume_realisasi') ?? 0;
            @endphp

            <td>
                @if($target > 0)
                {{ round(($realisasi / $target) * 100, 2) }}%
                @else
                -
                @endif
            </td>
            <td>{{ $item->waktu_pelaksanaan ?? '-' }}</td>
            <td>{{ $item->anggaran }}</td>
            <td>{{ $item->sumber_dana ?? '-' }}</td>
            <td>{{ $item->status_progres }}</td>
        </tr>
        @endforeach
    </tbody>
</table>