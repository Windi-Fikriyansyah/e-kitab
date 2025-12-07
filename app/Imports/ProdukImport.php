<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class ProdukImport implements ToCollection, WithHeadingRow, WithCalculatedFormulas
{
    private function convertImages($value)
    {
        if (!$value) {
            return json_encode([], JSON_UNESCAPED_UNICODE);
        }

        $trim = trim((string) $value);

        // Jika sudah JSON array
        if (str_starts_with($trim, '[') && str_ends_with($trim, ']')) {
            return $trim;
        }

        $array = array_map('trim', explode(',', $trim));
        $array = array_values(array_filter($array, fn($v) => $v !== ''));

        return json_encode($array, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Terima input:
     * - "Fiqih,Aqidah"
     * - '["Fiqih","Aqidah"]'
     * - array dari excel
     * Output: array bersih (trim, no empty, unique)
     */
    private function parseList($value): array
    {
        if ($value === null) return [];

        // Jika excel sudah memberikan array
        if (is_array($value)) {
            $arr = $value;
        } else {
            $str = trim((string) $value);
            if ($str === '') return [];

            // Jika sudah JSON array
            if (str_starts_with($str, '[') && str_ends_with($str, ']')) {
                $decoded = json_decode($str, true);
                $arr = is_array($decoded) ? $decoded : [];
            } else {
                $arr = explode(',', $str);
            }
        }

        $arr = array_map(fn($v) => trim((string) $v), $arr);
        $arr = array_values(array_filter($arr, fn($v) => $v !== ''));
        $arr = array_values(array_unique($arr));

        return $arr;
    }

    private function toJsonList($value): string
    {
        return json_encode($this->parseList($value), JSON_UNESCAPED_UNICODE);
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {

            // Lewati baris kosong
            if (empty($row['judul'])) continue;

            // Generate Kode Produk
            $tanggal = date('Ymd');
            $last = DB::table('produk')
                ->whereDate('created_at', now()->toDateString())
                ->orderBy('id', 'desc')
                ->first();

            $lastId = $last ? intval(substr($last->kd_produk, -5)) : 0;
            $kd = 'PR' . $tanggal . str_pad($lastId + 1, 5, '0', STR_PAD_LEFT);

            // ====== PARSE KATEGORI (ARAB) ======
            $kategoriArab = $this->parseList($row['kategori'] ?? null);
            $subKategoriArab = $this->parseList($row['sub_kategori'] ?? null);

            // ====== PARSE KATEGORI (INDO) ======
            // dari file excel
            $kategoriIndo = $this->parseList($row['kategori_indo'] ?? null);
            $subKategoriIndo = $this->parseList($row['sub_kategori_indo'] ?? null);

            // OPSIONAL: kalau kolom indo kosong, auto isi dari tabel referensi berdasarkan nama_arab
            if (empty($kategoriIndo) && !empty($kategoriArab)) {
                $kategoriIndo = DB::table('kategori')
                    ->whereIn('nama_arab', $kategoriArab)
                    ->pluck('nama_indonesia')
                    ->filter()
                    ->values()
                    ->toArray();
            }

            if (empty($subKategoriIndo) && !empty($subKategoriArab)) {
                $subKategoriIndo = DB::table('sub_kategori')
                    ->whereIn('nama_arab', $subKategoriArab)
                    ->pluck('nama_indonesia')
                    ->filter()
                    ->values()
                    ->toArray();
            }

            // INSERT PRODUK
            $produkId = DB::table('produk')->insertGetId([
                'kd_produk'    => $kd,
                'judul'        => $row['judul'],
                'cover'        => $row['cover'] ?? null,
                'kertas'       => $row['kertas'] ?? null,
                'kualitas'     => $row['kualitas'] ?? null,
                'harakat'      => $row['harakat'] ?? null,
                'halaman'      => $row['halaman'] ?? null,
                'berat'        => $row['berat'] ?? null,
                'ukuran'       => $row['ukuran'] ?? null,

                // Simpan JSON rapi (tanpa [""])
                'kategori'     => json_encode($kategoriArab, JSON_UNESCAPED_UNICODE),
                'sub_kategori' => json_encode($subKategoriArab, JSON_UNESCAPED_UNICODE),

                'penerbit'     => $row['penerbit'] ?? null,
                'supplier'     => $row['supplier'] ?? null,
                'penulis'      => $row['penulis'] ?? null,

                'harga_modal'  => $row['harga_modal'] ?? 0,
                'harga_jual'   => $row['harga_jual'] ?? 0,
                'laba'         => ($row['harga_jual'] ?? 0) - ($row['harga_modal'] ?? 0),
                'stok'         => $row['stok'] ?? 0,

                'images'       => $this->convertImages($row['images'] ?? null),
                'link_youtube' => $row['link_youtube'] ?? null,

                'editor'       => $row['editor'] ?? null,
                'Deskripsi'    => $row['deskripsi'] ?? null,

                'created_at'   => now(),
                'updated_at'   => now(),
            ]);

            // INSERT PRODUK INDO
            DB::table('produk_indo')->insert([
                'id_produk'          => $produkId,
                'judul_indo'         => $row['judul_indo'] ?? null,
                'cover_indo'         => $row['cover_indo'] ?? null,
                'kertas_indo'        => $row['kertas_indo'] ?? null,
                'kualitas_indo'      => $row['kualitas_indo'] ?? null,
                'harakat_indo'       => $row['harakat_indo'] ?? null,

                // Simpan JSON rapi (tanpa [""])
                'kategori_indo'      => json_encode($kategoriIndo, JSON_UNESCAPED_UNICODE),
                'sub_kategori_indo'  => json_encode($subKategoriIndo, JSON_UNESCAPED_UNICODE),

                'penerbit_indo'      => $row['penerbit_indo'] ?? null,
                'cetakan_indo'       => $row['cetakan_indo'] ?? null,
                'penulis_indo'       => $row['penulis_indo'] ?? null,
                'editor_indo'        => $row['editor_indo'] ?? null,
                'Deskripsi_indo'     => $row['deskripsi_indo'] ?? null,
            ]);
        }
    }
}
