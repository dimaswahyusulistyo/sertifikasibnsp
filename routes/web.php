<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InventarisController;

Route::get('/', [InventarisController::class, 'index']);


Route::prefix('inventaris')->group(function () {
    Route::get('/', [InventarisController::class, 'index'])->name('data.inventaris');
    Route::get('/create', [InventarisController::class, 'create'])->name('inventaris.create');
    Route::post('/', [InventarisController::class, 'store'])->name('inventaris.store');
    Route::get('/{produk}/edit', [InventarisController::class, 'edit'])->name('inventaris.edit');
    Route::put('/{produk}', [InventarisController::class, 'update'])->name('inventaris.update');
    Route::delete('/{id}', [InventarisController::class, 'destroy'])->name('inventaris.destroy');
    Route::get('/inventaris/export', [InventarisController::class, 'export'])->name('inventaris.export');
});