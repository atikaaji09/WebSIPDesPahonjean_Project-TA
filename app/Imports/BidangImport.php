<?php

namespace App\Imports;

use App\Models\Bidang;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BidangImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $kode =
            $row['kode_bidang'] ??
            $row['kode bidang'] ??
            array_values($row)[0] ?? null;

        $nama =
            $row['nama_bidang'] ??
            $row['nama bidang'] ??
            array_values($row)[1] ?? null;

        if (!$nama) return null;

        return new Bidang([
            'kode_bidang' => $kode,
            'nama_bidang' => $nama,
        ]);
    }
}
