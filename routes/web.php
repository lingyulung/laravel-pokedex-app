<?php

use App\Http\Controllers\pokemonController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/api/pokemons', [pokemonController::class, 'index']);

Route::get('/api/pokemons/search/{name}', [pokemonController::class, 'search']);