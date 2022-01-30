<?php

use App\Http\Controllers\RandomUsersController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [RandomUsersController::class, 'index']);

Route::get('/xml', [RandomUsersController::class, 'xml'])->name('xml');
