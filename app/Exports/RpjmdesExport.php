<?php

namespace App\Exports;

use App\Models\RpjmdesDetail;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class RpjmdesExport implements FromView, WithStyles, WithEvents
{
    protected $periodeId;
    protected $dusunId;
    protected $totalKolom = 16;
    protected $tahunCount = 0;

    public function __construct($periodeId, $dusunId = null)
    {
        $this->periodeId = $periodeId;
        $this->dusunId   = $dusunId;
    }

    public function view(): View
    {
        $query = RpjmdesDetail::with([
            'usulan.rtrw.dusun',
            'bidang',
            'subBidang',
            'kegiatan',
            'rkpdesDetails.monitoring',
        ]);

        if (!empty($this->periodeId)) {
            $query->where('rpjmdes_id', $this->periodeId);
        }

        if (!empty($this->dusunId)) {
            $query->whereHas('usulan.rtrw.dusun', function ($q) {
                $q->where('id', $this->dusunId);
            });
        }

        $data = $query
            ->orderBy('bidang_id')
            ->orderBy('sub_bidang_id')
            ->orderBy('kegiatan_id')
            ->get()
            ->values();

        $data = $data->transform(function ($item) {

            $volume = $item->usulan->volume ?? '0';
            $satuan = $item->usulan->satuan ?? '';

            $isDimensi =
                in_array($satuan, ['m2', 'm3']) &&
                str_contains($volume, 'x');

            if ($isDimensi) {

                $dimensi = explode('x', strtolower($volume));

                $dimensi = array_map(function ($v) {
                    return (float) $v;
                }, $dimensi);

                if (count($dimensi) == 3) {

                    $hasil =
                        $dimensi[0] *
                        $dimensi[1] *
                        $dimensi[2];
                } elseif (count($dimensi) == 2) {

                    $hasil =
                        $dimensi[0] *
                        $dimensi[1];
                } else {

                    $hasil = (float) $volume;
                }
            } else {

                $hasil = (float) $volume;
            }

            // sesuaikan nama kolom volume di rkpdes_details
            $totalVolumeMasukRkp = $item->rkpdesDetails->sum('volume');

            $sisaVolumeRkp = max(0, $hasil - $totalVolumeMasukRkp);

            if ($item->rkpdesDetails->count() > 0) {

                if ($sisaVolumeRkp > 0) {
                    $item->status = 'lanjutan';
                } else {
                    $item->status = 'masuk_rkpdes';
                }
            }

            return $item;
        });

        $tahunRange = collect($data)
            ->pluck('tahun_pelaksanaan')
            ->map(function ($t) {
                if (is_array($t)) {
                    return $t;
                }

                $decoded = json_decode($t, true);

                return is_array($decoded) ? $decoded : [];
            })
            ->flatten()
            ->filter()
            ->unique()
            ->sort()
            ->take(6)
            ->values();

        $this->tahunCount = $tahunRange->count();
        $this->totalKolom = 16 + $this->tahunCount;

        return view('exports.rpjmdes', compact('data', 'tahunRange'));
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            2 => ['font' => ['bold' => true]],
            3 => ['font' => ['bold' => true]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                $lastColumn = Coordinate::stringFromColumnIndex($this->totalKolom);

                $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);

                for ($i = 1; $i <= $this->totalKolom; $i++) {
                    $col = Coordinate::stringFromColumnIndex($i);
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                $sheet->mergeCells("A1:{$lastColumn}1");

                $sheet->mergeCells('A2:A3');
                $sheet->mergeCells('B2:B3');
                $sheet->mergeCells('C2:C3');
                $sheet->mergeCells('D2:D3');
                $sheet->mergeCells('E2:E3');
                $sheet->mergeCells('F2:F3');
                $sheet->mergeCells('G2:G3');

                $sheet->mergeCells('H2:J2');
                $sheet->mergeCells('K2:K3');

                $startTahun = 12;

                if ($this->tahunCount > 0) {
                    $endTahun = $startTahun + $this->tahunCount - 1;

                    $sheet->mergeCells(
                        Coordinate::stringFromColumnIndex($startTahun) . '2:' .
                            Coordinate::stringFromColumnIndex($endTahun) . '2'
                    );

                    $anggaranIndex = $endTahun + 1;
                } else {
                    $anggaranIndex = $startTahun;
                }

                $anggaranCol = Coordinate::stringFromColumnIndex($anggaranIndex);
                $sumberCol   = Coordinate::stringFromColumnIndex($anggaranIndex + 1);
                $polaCol     = Coordinate::stringFromColumnIndex($anggaranIndex + 2);
                $statusCol   = Coordinate::stringFromColumnIndex($anggaranIndex + 3);

                $sheet->mergeCells("{$anggaranCol}2:{$anggaranCol}3");
                $sheet->mergeCells("{$sumberCol}2:{$sumberCol}3");
                $sheet->mergeCells("{$polaCol}2:{$polaCol}3");
                $sheet->mergeCells("{$statusCol}2:{$statusCol}3");

                $sheet->getStyle("A1:{$lastColumn}3")->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);

                $sheet->getStyle("A1:{$lastColumn}300")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                ]);

                $sheet->getStyle('B')->getAlignment()->setWrapText(true);

                $sheet->getRowDimension(2)->setRowHeight(30);
                $sheet->getRowDimension(3)->setRowHeight(25);
            },
        ];
    }
}
