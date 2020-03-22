<?php

use Illuminate\Support\Facades\Route;
use SertxuDeveloper\Lyra\Http\Controllers\LoginController;
use SertxuDeveloper\Lyra\Http\Controllers\MainController;

Route::group(['middleware' => ['web']], function () {

  Route::prefix(config('lyra.routes.web.prefix'))->name(config('lyra.routes.web.name'))->group(function () {

    Route::get('logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('showLoginForm');
    Route::post('login', [LoginController::class, 'login'])->name('login');

    Route::group(['middleware' => 'lyra'], function () {
      Route::get('/', [MainController::class, 'index'])->name('dashboard');
      Route::get('/404', function (){ return redirect(route('lyra.dashboard')); });
      Route::get('/{any}', [MainController::class, 'index'])->where('any', '.*');
    });

  });

});
