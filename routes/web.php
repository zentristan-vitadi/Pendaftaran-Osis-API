<?php

use App\Http\Controllers\PendaftaranController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/pendaftaran');

Route::resource('pendaftaran', PendaftaranController::class)->except(['show']);
