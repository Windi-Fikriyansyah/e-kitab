<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class PenulisImport implements
    ToCollection,
    WithHeadingRow,
    WithCalculatedFormulas,
    SkipsEmptyRows,
    WithValidation,
    WithChunkReading
{
    public function collection(Collection $rows)
    {
        $data = [];

        foreach ($rows as $row) {
            $namaArab = trim((string) ($row['nama_arab'] ?? ''));
            $namaIndo = trim((string) ($row['nama_indonesia'] ?? ''));

            // kalau 2-2 nya kosong, jangan diinsert
            if ($namaArab === '' && $namaIndo === '') {
                continue;
            }

            $data[] = [
                // simpan null jika kosong (lebih rapi daripada string kosong)
                'nama_arab'      => $namaArab !== '' ? $namaArab : null,
                'nama_indonesia' => $namaIndo !== '' ? $namaIndo : null,
            ];
        }

        if (count($data) === 0) return;

        DB::table('penulis')->insert($data);
    }

    // ✅ tidak required, boleh kosong
    public function rules(): array
    {
        return [
            '*.nama_arab' => 'nullable|string|max:255',
            '*.nama_indonesia' => 'nullable|string|max:255',
        ];
    }

    public function chunkSize(): int
    {
        return 500;
    }
}
