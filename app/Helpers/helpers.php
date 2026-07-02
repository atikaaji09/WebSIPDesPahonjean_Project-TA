<?php

if (!function_exists('statusButton')) {
    /**
     * Generate HTML badge untuk status RPJMDes / RKPDes
     */
    function statusButton($status)
    {
        $status = strtolower($status);

        $colors = [
            'baru' => 'background-color:#facc15;color:white;',       // kuning
            'diproses' => 'background-color:#007bff;color:white;',    // biru
            'selesai' => 'background-color:#28a745;color:white;',     // hijau
            'masuk_rkpdes' => 'background-color:#17a2b8;color:white;', // cyan
            'lanjutan' => 'background-color:#f39c12;color:white;',    // oranye
            'diajukan' => 'background-color:#6c757d;color:white;',    // abu
            'diterima' => 'background-color:#6610f2;color:white;',    // ungu
        ];

        $style = $colors[$status] ?? 'background-color:#6c757d;color:white;';

        return '<span style="
            display:inline-block;
            padding:4px 10px;
            border-radius:999px;
            font-size:0.875rem;
            font-weight:600;
            line-height:1.2;
            text-align:center;'
            . $style . '">'
            . ucfirst(str_replace('_', ' ', $status)) . '</span>';
    }
}
