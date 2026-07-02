<?php

namespace App\Imports;

use App\Models\AsetDesa;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class AsetImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $v = array_values($row);

        if (empty(array_filter($v))) {
            return null;
        }

        $get = function ($key, $altKey, $index) use ($row, $v) {
            return $row[$key]
                ?? $row[$altKey]
                ?? $v[$index]
                ?? null;
        };

        $tanggal = $get('tanggal_kepemilikan', 'tanggal kepemilikan', 4);

        if ($tanggal) {
            try {
                if (is_numeric($tanggal)) {
                    $tanggal = Date::excelToDateTimeObject($tanggal)->format('Y-m-d');
                } else {
                    $tanggal = date('Y-m-d', strtotime($tanggal));
                }
            } catch (\Exception $e) {
                $tanggal = null;
            }
        }

        return new AsetDesa([
            'klas_aset' => $get('klas_aset', 'klas aset', 0),
            'nama_aset' => $get('nama_aset', 'nama aset', 1),
            'jenis_kepemilikan' => $get('jenis_kepemilikan', 'jenis kepemilikan', 2),
            'nomor_kepemilikan' => $get('nomor_kepemilikan', 'nomor kepemilikan', 3),

            'tanggal_kepemilikan' => $tanggal,

            'kode_aset' => $get('kode_aset', 'kode aset', 5),
            'tahun_perolehan' => $get('tahun_perolehan', 'tahun perolehan', 6),
            'nilai_perolehan' => $get('nilai_perolehan', 'nilai perolehan', 7),
            'kondisi_aset' => $get('kondisi_aset', 'kondisi aset', 8),
            'keterangan' => $get('keterangan', 'keterangan', 9),
            'luas' => $get('luas', 'luas', 10),
            'bukti_kepemilikan' => $get('bukti_kepemilikan', 'bukti kepemilikan', 11),

            'titik_pangkal' => $get('titik_pangkal', 'titik pangkal', 12),
            'titik_ujung' => $get('titik_ujung', 'titik ujung', 13),
        ]);
    }
}
