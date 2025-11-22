<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * Nama tabel di database.
     *
     * @var string
     */
    protected $table = 'products';

    /**
     * Field yang dapat diisi (mass assignable).
     *
     * @var array
     */
    protected $fillable = [
        'barcode',
        'name',
        'category',
        'purchase_price',
        'selling_price',
        'stock',
        'satuan',
        'photo'
    ];

    /**
     * Mendefinisikan format default untuk field yang bersifat numerik.
     *
     * @var array
     */
    protected $casts = [
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'stock' => 'integer',
    ];
}
