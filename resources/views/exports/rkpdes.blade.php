<table border="1" cellspacing="0" cellpadding="5" style="border-collapse: collapse; width:100%;">

    <tr>
        <td colspan="19" style="text-align:center; font-weight:bold;">
            RENCANA KERJA PEMERINTAH DESA (RKPDes)
        </td>
    </tr>

    <thead>
        <tr>
            <th rowspan="2">No</th>
            <th rowspan="2">Bidang / Sub Bidang / Kegiatan</th>

            <th rowspan="2">Mendukung SDGs ke</th>
            <th rowspan="2">Data Existing Tahun Berjalan</th>
            <th rowspan="2">Target Capaian</th>

            <th rowspan="2">Lokasi</th>

            <th colspan="3">Volume</th>
            <th colspan="2">Anggaran</th>
            <th colspan="3">Penerima Manfaat</th>

            <th rowspan="2">Waktu Pelaksanaan</th>
            <th rowspan="2">Pelaksana Kegiatan</th>
            <th rowspan="2">Rencana TPK</th>
            <th rowspan="2">Pola Pelaksanaan</th>
            <th rowspan="2">Status</th>
        </tr>

        <tr>
            <th>Jumlah</th>
            <th>Satuan</th>
            <th>Realisasi</th>

            <th>Biaya</th>
            <th>Sumber</th>

            <th>LK</th>
            <th>PR</th>
            <th>RTM</th>
        </tr>
    </thead>

    <tbody>

        @php $noBidang = 1; @endphp

        @foreach($data->groupBy('bidang_id') as $bidangGroup)

        <tr>
            <td>{{ $noBidang }}</td>
            <td colspan="18"><b>{{ $bidangGroup->first()->bidang->nama_bidang ?? '-' }}</b></td>
        </tr>

        @php $noSub = 1; @endphp

        @foreach($bidangGroup->groupBy('sub_bidang_id') as $subGroup)

        <tr>
            <td>{{ $noBidang }}.{{ $noSub }}</td>
            <td colspan="18" style="padding-left:15px;">
                {{ $subGroup->first()->subBidang->nama_sub_bidang ?? '-' }}
            </td>
        </tr>

        @php $noKeg = 1; @endphp

        @foreach($subGroup as $item)

        @php
        $volume = $item->volume ?? 0;

        if (is_string($volume) && str_contains($volume, 'x')) {
        $volumeTampil = $volume;
        } else {
        $volumeTampil = number_format((float)$volume, 0, ',', '.');
        }

        // ✅ REALISASI
        $realisasi = $item->monitoring->sum('volume_realisasi');
        $realisasiTampil = number_format($realisasi, 0, ',', '.');

        // ✅ STATUS
        $status = $item->status_progres ?? '-';

        $statusLabel = match($status) {
        'belum' => 'Belum Mulai',
        'proses' => 'Sedang Berjalan',
        'selesai' => 'Selesai',
        default => ucfirst($status)
        };
        @endphp

        <tr>
            <td>{{ $noBidang }}.{{ $noSub }}.{{ $noKeg }}</td>

            <td style="padding-left:30px;">
                {{ $item->kegiatan->nama_kegiatan ?? '-' }}
            </td>

            <td>{{ $item->sdgs ?? '-' }}</td>
            <td>{{ $item->data_existing ?? '-' }}</td>
            <td>{{ $item->target_capaian ?? '-' }}</td>

            <td>{{ $item->lokasi ?? '-' }}</td>

            <td>{{ $volumeTampil }}</td>
            <td>{{ $item->satuan ?? '-' }}</td>
            <td>{{ $realisasiTampil }}</td>

            <td>{{ number_format($item->anggaran ?? 0, 0, ',', '.') }}</td>
            <td>{{ $item->sumber_dana ?? '-' }}</td>

            <td>{{ $item->penerima_laki ?? 0 }}</td>
            <td>{{ $item->penerima_perempuan ?? 0 }}</td>
            <td>{{ $item->penerima_rtm ?? 0 }}</td>

            <td>{{ $item->waktu_pelaksanaan ?? '-' }}</td>
            <td>{{ $item->pelaksana_kegiatan ?? '-' }}</td>
            <td>{{ $item->rencana_tpk ?? '-' }}</td>

            <td>{{ $item->pola_pelaksanaan ?? '-' }}</td>
            <td>{{ $statusLabel }}</td>
        </tr>

        @php $noKeg++; @endphp
        @endforeach

        @php $noSub++; @endphp
        @endforeach

        @php $noBidang++; @endphp
        @endforeach

    </tbody>
</table>