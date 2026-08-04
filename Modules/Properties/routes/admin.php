<?php

use Illuminate\Support\Facades\Route;
use Modules\Properties\Http\Controllers\Admin\PropertyTypeController;
use Modules\Properties\Http\Controllers\Admin\PropertyFieldController;
use Modules\Properties\Http\Controllers\Admin\PropertyFieldSectionController;

Route::middleware(['auth', 'verified', 'active', 'role:superadmin|admin'])
    ->prefix('admin/property-types')
    ->name('admin.property-types.')
    ->group(function () {
        Route::get('/', [PropertyTypeController::class, 'index'])->name('index');
        Route::post('/', [PropertyTypeController::class, 'store'])->name('store');
        Route::put('/{propertyType}', [PropertyTypeController::class, 'update'])->name('update');
        Route::delete('/{propertyType}', [PropertyTypeController::class, 'destroy'])->name('destroy');

        Route::get('/{propertyType}/fields', [PropertyFieldController::class, 'index'])->name('fields.index');
        Route::post('/{propertyType}/fields', [PropertyFieldController::class, 'store'])->name('fields.store');
        Route::put('/{propertyType}/fields/{field}', [PropertyFieldController::class, 'update'])->name('fields.update');
        Route::delete('/{propertyType}/fields/{field}', [PropertyFieldController::class, 'destroy'])->name('fields.destroy');

        Route::post('/{propertyType}/sections', [PropertyFieldSectionController::class, 'store'])->name('sections.store');
        Route::put('/{propertyType}/sections/{section}', [PropertyFieldSectionController::class, 'update'])->name('sections.update');
        Route::delete('/{propertyType}/sections/{section}', [PropertyFieldSectionController::class, 'destroy'])->name('sections.destroy');
    });
