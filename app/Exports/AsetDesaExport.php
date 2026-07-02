<?php

namespace App\Exports;

use App\Models\AsetDesa;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class AsetDesaExport implements FromView
{
    protected ?string $klasAset;

    public function __construct(?string $klasAset)
    {
        $this->klasAset = $klasAset;
    }

    public function view(): View
    {
        $query = AsetDesa::query();

        if ($this->klasAset) {
            $query->where('klas_aset', $this->klasAset);
        }

        return view('exports.asetdesa', [
            'data' => $query->get()
        ]);
    }
}
