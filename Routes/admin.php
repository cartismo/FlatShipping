<?php

use Illuminate\Support\Facades\Route;
use Modules\FlatShipping\Http\Controllers\Admin\FlatShippingController;

/*
|--------------------------------------------------------------------------
| FlatShipping Admin Routes
|--------------------------------------------------------------------------
|
| Admin routes for flat rate shipping settings.
|
*/

Route::prefix('modules/shipping/flat-shipping')->name('admin.shipping.flat.')->group(function () {
    Route::get('/settings', [FlatShippingController::class, 'index'])->name('settings');
    Route::put('/settings', [FlatShippingController::class, 'update'])->name('settings.update');
});