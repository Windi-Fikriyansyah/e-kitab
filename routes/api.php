<?php

use App\Http\Controllers\KelolaData\KategoriController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KelolaData\ProdukController;

Route::middleware('api')->group(function () {
    Route::get('/produk', [ProdukController::class, 'api_index']);
    Route::get('/produk/{id}', [ProdukController::class, 'api_show']);
    Route::get('/kategoris', [KategoriController::class, 'get_api']);
    Route::get('/Penulis', [KategoriController::class, 'get_penulis']);
    Route::get('/Penerbit', [KategoriController::class, 'get_penerbit']);
    Route::get('/Harakat', [KategoriController::class, 'get_harokat']);
    Route::get('/cover', [KategoriController::class, 'get_cover']);
    Route::get('/kategoris/{id}', [KategoriController::class, 'get_api_show']);
});
