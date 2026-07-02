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

class SusunRpjmdesExport implements FromView, WithStyles, WithEvents
{
    protected int $periodeId;
    protected ?int $dusunId;
    protected int $totalKolom;
    protected int $tahunCount;

    public function __construct(int $periodeId, ?int $dusunId)
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
            'kegiatan'
        ]);

        if ($this->periodeId) {
            $query->where('rpjmdes_id', $this->periodeId);
        }

        if ($this->dusunId) {
            $query->whereHas('usulan.rtrw.dusun', function ($q) {
                $q->where('id', $this->dusunId);
            });
        }

        $data = $query
            ->orderBy('bidang_id')
            ->orderBy('sub_bidang_id')
            ->orderBy('kegiatan_id')
            ->orderBy('id')
            ->get()
            ->sortBy([
                ['bidang_id', 'asc'],
                ['sub_bidang_id', 'asc'],
                ['kegiatan_id', 'asc']
            ])
            ->values();

        $tahunRange = collect($data)
            ->pluck('tahun_pelaksanaan')
            ->map(fn($t) => is_array($t) ? $t : json_decode($t, true))
            ->flatten()
            ->filter()
            ->unique()
            ->sort()
            ->take(6)
            ->values();

        $this->tahunCount = $tahunRange->count();

        $this->totalKolom = 13 + $this->tahunCount;

        return view('exports.susun_rpjmdes', compact('data', 'tahunRange'));
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

                foreach (range('A', $lastColumn) as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                $sheet->mergeCells("A1:{$lastColumn}1");

                $sheet->mergeCells('A2:A3');
                $sheet->mergeCells('B2:B3');
                $sheet->mergeCells('C2:C3');
                $sheet->mergeCells('D2:D3');
                $sheet->mergeCells('E2:E3');
                $sheet->mergeCells('F2:H2');
                $sheet->mergeCells('I2:I3');

                $startTahun = 10;
                $endTahun = $startTahun + $this->tahunCount - 1;

                $startCol = Coordinate::stringFromColumnIndex($startTahun);
                $endCol   = Coordinate::stringFromColumnIndex($endTahun);

                if ($this->tahunCount > 0) {
                    $sheet->mergeCells("{$startCol}2:{$endCol}2");
                }

                $anggaranCol = Coordinate::stringFromColumnIndex($endTahun + 1);
                $sumberCol   = Coordinate::stringFromColumnIndex($endTahun + 2);
                $polaCol     = Coordinate::stringFromColumnIndex($endTahun + 3);

                $sheet->mergeCells("{$anggaranCol}2:{$anggaranCol}3");
                $sheet->mergeCells("{$sumberCol}2:{$sumberCol}3");
                $sheet->mergeCells("{$polaCol}2:{$polaCol}3");

                $sheet->getStyle("A1:{$lastColumn}3")->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);

                $sheet->getStyle("A1:{$lastColumn}200")->applyFromArray([
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
