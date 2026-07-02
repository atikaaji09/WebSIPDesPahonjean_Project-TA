<?php

namespace App\Exports;

use App\Models\Admin\RkpdesDetail;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class RkpdesExport implements FromView, WithStyles, WithEvents
{
    protected string $tahun;
    protected ?int $dusunId;

    public function __construct(string $tahun, ?int $dusunId)
    {
        $this->tahun    = $tahun;
        $this->dusunId  = $dusunId;
    }

    public function view(): View
    {
        $query = RkpdesDetail::with([
            'rpjmdesDetail.usulan.rtrw.dusun',
            'bidang',
            'subBidang',
            'kegiatan',
            'rkpdes',
            'monitoring'
        ]);

        if ($this->tahun) {
            $query->whereHas('rkpdes', function ($q) {
                $q->where('tahun', $this->tahun);
            });
        }

        if ($this->dusunId) {
            $query->whereHas('rpjmdesDetail.usulan.rtrw.dusun', function ($q) {
                $q->where('id', $this->dusunId);
            });
        }

        $data = $query
            ->orderBy('bidang_id')
            ->orderBy('sub_bidang_id')
            ->orderBy('kegiatan_id')
            ->orderBy('id')
            ->get();

        return view('exports.rkpdes', compact('data'));
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

                $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);

                foreach (range('A', 'S') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                $sheet->mergeCells('A1:S1');

                $sheet->mergeCells('A2:A3');
                $sheet->mergeCells('B2:B3');
                $sheet->mergeCells('C2:C3');
                $sheet->mergeCells('D2:D3');
                $sheet->mergeCells('E2:E3');
                $sheet->mergeCells('F2:F3');

                $sheet->mergeCells('G2:I2');
                $sheet->mergeCells('J2:K2');
                $sheet->mergeCells('L2:N2');

                $sheet->mergeCells('O2:O3');
                $sheet->mergeCells('P2:P3');
                $sheet->mergeCells('Q2:Q3');
                $sheet->mergeCells('R2:R3');
                $sheet->mergeCells('S2:S3');

                $sheet->getStyle('A1:S3')->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);

                $sheet->getStyle('A1:S300')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => 'thin',
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
