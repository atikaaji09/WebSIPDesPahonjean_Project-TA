<?php

namespace App\Exports;

use App\Models\Usulan;
use Maatwebsite\Excel\Concerns\FromArray;

class UsulanExport implements FromArray
{
    protected $dusunId;

    public function __construct($dusunId = null)
    {
        $this->dusunId = $dusunId;
    }

    public function array(): array
    {
        $data = Usulan::with('rtrw.dusun')
            ->where('status', 'diajukan')
            ->when($this->dusunId, function ($q) {
                $q->whereHas('rtrw.dusun', function ($q2) {
                    $q2->where('id', $this->dusunId);
                });
            })
            ->get();

        $rows = [];
        $rows[] = ['REKAPITULASI USULAN KEGIATAN'];
        $rows[] = [];
        $rows[] = [
            'No',
            'Nama',
            'Dusun',
            'RT/RW',
            'Gagasan Kegiatan',
            'Lokasi',
            'Volume',
            'Satuan',
            'LK',
            'PR',
            'RTM',
        ];

        $no = 1;

        foreach ($data as $item) {

            $rows[] = [
                $no++,
                $item->nama_lengkap,
                $item->rtrw->dusun->nama_dusun ?? '-',
                'RT ' . ($item->rtrw->rt ?? '-') . ' / RW ' . ($item->rtrw->rw ?? '-'),
                $item->gagasan_kegiatan,
                $item->lokasi,
                $item->volume,
                $this->formatSatuan($item->satuan),
                $item->penerima_laki,
                $item->penerima_perempuan,
                $item->penerima_rtm,
            ];
        }

        return $rows;
    }

    private function formatSatuan(string $satuan)
    {
        return match ($satuan) {
            'm2' => 'm²',
            'm3' => 'm³',
            default => $satuan,
        };
    }
}
