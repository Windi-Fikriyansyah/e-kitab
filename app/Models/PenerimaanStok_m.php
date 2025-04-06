<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenerimaanStok_m extends Model
{
    use HasFactory;

    /**
     * Nama tabel di database.
     *
     * @var string
     */
    protected $table = 'penerimaanstok';

    /**
     * Field yang dapat diisi (mass assignable).
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'id_product',
        'tambah_stok',
    ];

    /**
     * Mendefinisikan format default untuk field yang bersifat numerik.
     *
     * @var array
     */
    protected $casts = [
        'tambah_stok' => 'integer',
    ];

    public $timestamps = true;
}
