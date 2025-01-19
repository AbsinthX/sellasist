<?php

use App\Http\Controllers\PetstoreController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('pets')->group(function () {
    Route::get('/', [PetstoreController::class, 'getPetsByStatus']);
    Route::get('/{id}', [PetstoreController::class, 'getPetById'])->whereNumber('id');
    Route::post('/', [PetstoreController::class, 'createPet']);
    Route::put('/{id}', [PetstoreController::class, 'updatePetById'])->whereNumber('id');
    Route::delete('/{id}', [PetstoreController::class, 'deletePetById'])->whereNumber('id');
});
