<table border="1">
    <thead>
        <tr>
            <th>No</th>
            <th>Klas Aset</th>
            <th>Nama Aset</th>
            <th>Jenis Kepemilikan</th>
            <th>Nomor Kepemilikan</th>
            <th>Tanggal Kepemilikan</th>
            <th>Kode Aset</th>
            <th>Tahun Perolehan</th>
            <th>Nilai Perolehan</th>
            <th>Kondisi Aset</th>
            <th>Keterangan</th>
            <th>Luas</th>
            <th>Bukti Kepemilikan</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $i => $aset)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $aset->klas_aset }}</td>
            <td>{{ $aset->nama_aset }}</td>
            <td>{{ $aset->jenis_kepemilikan }}</td>
            <td>{{ $aset->nomor_kepemilikan }}</td>
            <td>{{ $aset->tanggal_kepemilikan }}</td>
            <td>{{ $aset->kode_aset }}</td>
            <td>{{ $aset->tahun_perolehan }}</td>
            <td>{{ $aset->nilai_perolehan }}</td>
            <td>{{ $aset->kondisi_aset }}</td>
            <td>{{ $aset->keterangan }}</td>
            <td>{{ $aset->luas }}</td>
            <td>{{ $aset->bukti_kepemilikan }}</td>
        </tr>
        @endforeach
    </tbody>
</table>