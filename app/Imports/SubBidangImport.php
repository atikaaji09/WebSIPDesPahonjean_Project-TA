<?php

namespace App\Imports;

use App\Models\SubBidang;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SubBidangImport implements ToModel, WithHeadingRow
{
    protected ?int $bidang_id;

    public function __construct(?int $bidang_id)
    {
        $this->bidang_id = $bidang_id;
    }

    public function model(array $row)
    {
        $key = array_keys($row)[0];

        $nama = $row[$key] ?? null;

        if (!$nama) return null;

        return new SubBidang([
            'bidang_id' => $this->bidang_id,
            'nama_sub_bidang' => $nama,
        ]);
    }
}
