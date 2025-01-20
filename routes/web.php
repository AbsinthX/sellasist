<?php declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

Route::get('/pet', function () {
    return view('pet-form', ['id' => null]);
})->name('pet.create');

Route::get('/pet/{id}', function ($id) {
    return view('pet-form', ['id' => $id]);
})->where('id', '[0-9]+')->name('pet.edit');
