<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToCollection;

class ProdukImport implements ToCollection, WithHeadingRow
{
    private function convertImages($value)
    {
        if (!$value) {
            return json_encode([], JSON_UNESCAPED_UNICODE);
        }

        // Jika sudah JSON, langsung pakai
        $trim = trim($value);
        if (str_starts_with($trim, '[') && str_ends_with($trim, ']')) {
            return $trim;
        }

        // Jika format: img1.jpg,img2.jpg
        $array = array_map('trim', explode(',', $value));

        return json_encode($array, JSON_UNESCAPED_UNICODE);
    }


    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {

            // Lewati baris kosong
            if (!$row['judul']) continue;

            // Generate Kode Produk
            $tanggal = date('Ymd');
            $last = DB::table('produk')
                ->whereDate('created_at', now()->toDateString())
                ->orderBy('id', 'desc')
                ->first();

            $lastId = $last ? intval(substr($last->kd_produk, -5)) : 0;
            $kd = 'PR' . $tanggal . str_pad($lastId + 1, 5, '0', STR_PAD_LEFT);

            // INSERT PRODUK
            $produkId = DB::table('produk')->insertGetId([
                'kd_produk'         => $kd,
                'judul'             => $row['judul'],
                'cover'             => $row['cover'] ?? null,
                'kertas'            => $row['kertas'] ?? null,
                'kualitas'          => $row['kualitas'] ?? null,
                'harakat'           => $row['harakat'] ?? null,
                'halaman'           => $row['halaman'] ?? null,
                'berat'             => $row['berat'] ?? null,
                'ukuran'            => $row['ukuran'] ?? null,

                'kategori'          => json_encode(explode(',', $row['kategori'] ?? ''), JSON_UNESCAPED_UNICODE),
                'sub_kategori'      => json_encode(explode(',', $row['sub_kategori'] ?? ''), JSON_UNESCAPED_UNICODE),

                'penerbit'          => $row['penerbit'] ?? null,
                'supplier'          => $row['supplier'] ?? null,
                'penulis'           => $row['penulis'] ?? null,

                'harga_modal'       => $row['harga_modal'] ?? 0,
                'harga_jual'        => $row['harga_jual'] ?? 0,
                'laba'              => ($row['harga_jual'] ?? 0) - ($row['harga_modal'] ?? 0),

                'stok'              => $row['stok'] ?? 0,

                'images' => $this->convertImages($row['images'] ?? null),

                'link_youtube'      => $row['link_youtube'] ?? null,

                'editor'            => $row['editor'] ?? null,
                'Deskripsi'         => $row['deskripsi'] ?? null,

                'created_at'        => now(),
                'updated_at'        => now(),
            ]);

            // INSERT PRODUK INDO
            DB::table('produk_indo')->insert([
                'id_produk'             => $produkId,
                'judul_indo'            => $row['judul_indo'] ?? null,
                'cover_indo'            => $row['cover_indo'] ?? null,
                'kertas_indo'           => $row['kertas_indo'] ?? null,
                'kualitas_indo'         => $row['kualitas_indo'] ?? null,
                'harakat_indo'          => $row['harakat_indo'] ?? null,

                'kategori_indo'         => json_encode(explode(',', $row['kategori_indo'] ?? ''), JSON_UNESCAPED_UNICODE),
                'sub_kategori_indo'     => json_encode(explode(',', $row['sub_kategori_indo'] ?? ''), JSON_UNESCAPED_UNICODE),

                'penerbit_indo'         => $row['penerbit_indo'] ?? null,
                'cetakan_indo'          => $row['cetakan_indo'] ?? null,
                'penulis_indo'          => $row['penulis_indo'] ?? null,
                'editor_indo'           => $row['editor_indo'] ?? null,
                'Deskripsi_indo'        => $row['deskripsi_indo'] ?? null,
            ]);
        }
    }
}
