<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Services\BulletinService;
use App\Models\Bulletin;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Route pour générer les bulletins

Route::middleware(['auth'])->group(function () {
    Route::get('/bulletins/pdf/{bulletin}', function (Bulletin $bulletin, BulletinService $service) {
        return $service->genererPDF($bulletin);
    })->name('bulletin.pdf');
});

Route::get('/bulletins/pdf/{bulletin}', function (Bulletin $bulletin, BulletinService $service) {
    return $service->genererPDF($bulletin);
})->name('bulletin.pdf');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
