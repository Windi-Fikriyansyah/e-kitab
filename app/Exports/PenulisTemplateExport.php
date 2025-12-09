<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PenulisTemplateExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return [
            'nama_arab',
            'nama_indonesia',
        ];
    }

    public function array(): array
    {
        return [[
            'الدرر السنية',
            'penulis',
        ]];
    }
}
