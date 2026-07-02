<?php

namespace App\Imports;

use App\Models\Kegiatan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class KegiatanImport implements ToModel, WithHeadingRow
{
    protected ?int $sub_bidang_id;

    public function __construct(?int $sub_bidang_id)
    {
        $this->sub_bidang_id = $sub_bidang_id;
    }

    public function model(array $row)
    {
        $nama =
            $row['nama_kegiatan'] ??
            $row['nama kegiatan'] ??
            array_values($row)[0] ?? null;

        if (!$nama) return null;

        return new Kegiatan([
            'sub_bidang_id' => $this->sub_bidang_id,
            'nama_kegiatan' => $nama,
        ]);
    }
}
