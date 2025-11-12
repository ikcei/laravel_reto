<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PalabraController;
use App\Http\Controllers\RankingController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

// 🔒 Al entrar al welcome, si hay sesión, se cierra automáticamente
Route::get('/', function (Request $request) {
    if (Auth::check()) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
    return view('lingo.welcome');
})->name('lingo.welcome');



Route::get('/palabras', [PalabraController::class, 'index'])->name('palabras.index');

Route::get('/palabrasStyled', [PalabraController::class, 'indexStyled'])->name('palabras.indexStyled');

Route::get('/palabrasBlade', [PalabraController::class, 'indexBlade'])->name('palabras.indexBlade');

Route::get('/ranking', [RankingController::class, 'index'])->name('ranking.index');


//Ruta que devuelve de la tabla 'palabras' la cantidad de palabras aleatorias solicitada por URL y sino, devuelve 5 palabras
Route::get('/palabrasRandom/{cantidad?}', [PalabraController::class, 'indexRandom'])->name('palabras.indexRandomw');

Route::get('/verificarPalabra/{palabra}', [PalabraController::class, 'verificarPalabra'])->name('palabras.verificarPalabra');


Route::get('/lingo', function () {
    return view('lingo.index');
})->middleware('auth')->name('lingo.index');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
