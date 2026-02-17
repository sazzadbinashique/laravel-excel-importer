<?php

use Illuminate\Support\Facades\Route;

Route::middleware(config('excel-importer.middleware', ['web', 'auth']))
    ->prefix(trim((string) config('excel-importer.route_prefix', 'excel-importer'), '/'))
    ->group(function () {
        Route::get('/', function () {
            return view('excel-importer::dashboard');
        })->name('excel-importer.dashboard');

        Route::get('/{type}', function (string $type) {
            return view('excel-importer::dashboard', ['type' => $type]);
        })->name('excel-importer.dashboard.type');
    });
