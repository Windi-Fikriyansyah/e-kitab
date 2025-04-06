<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenguranganStok_m extends Model
{
    use HasFactory;

    /**
     * Nama tabel di database.
     *
     * @var string
     */
    protected $table = 'penguranganstok';

    /**
     * Field yang dapat diisi (mass assignable).
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'id_product',
        'kurang_stok',
        'keterangan'
    ];

    /**
     * Mendefinisikan format default untuk field yang bersifat numerik.
     *
     * @var array
     */
    protected $casts = [
        'kurang_stok' => 'integer',
    ];

    public $timestamps = true;
}
