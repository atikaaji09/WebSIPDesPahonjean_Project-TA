<?php

namespace App\Imports;

use App\Models\Rtrw;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class RtrwImport implements ToModel, WithHeadingRow
{
    protected ?int $dusun_id;

    public function __construct(?int $dusun_id)
    {
        $this->dusun_id = $dusun_id;
    }

    public function model(array $row)
    {
        $rt =
            $row['rt'] ??
            $row['r t'] ??
            array_values($row)[0] ?? null;

        $rw =
            $row['rw'] ??
            $row['r w'] ??
            array_values($row)[1] ?? null;

        $rt = $rt ? trim($rt) : null;
        $rw = $rw ? trim($rw) : null;

        if (!$rt || !$rw) return null;

        return Rtrw::firstOrCreate(
            [
                'dusun_id' => $this->dusun_id,
                'rt' => $rt,
                'rw' => $rw,
            ],
            []
        );
    }

    public function headingRow(): int
    {
        return 1;
    }
}
