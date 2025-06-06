<?php

use App\Http\Controllers\ExportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    //return view('welcome');
    return redirect('dashboard');
});

Route::get('export/{factura}/factura/pdf', [ExportController::class, 'factuta'])->name('pdf');
