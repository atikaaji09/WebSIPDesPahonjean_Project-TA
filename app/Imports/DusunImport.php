<?php

namespace App\Imports;

use App\Models\Dusun;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DusunImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $nama =
            $row['nama_dusun'] ??
            $row['nama dusun'] ??
            array_values($row)[0] ?? null;

        if (!$nama) return null;

        return new Dusun([
            'nama_dusun' => $nama,
        ]);
    }
}
