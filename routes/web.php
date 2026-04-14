<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ResumeController;
use Illuminate\Support\Facades\Route;






// Route::get('/', function () {
//     return view('welcome');
// });


Route::middleware('guest')->group(function () {
    Route::get('/',        [LoginController::class,    'showLoginForm'])->name('login');
    Route::post('/login',  [LoginController::class,    'login']);
    Route::get('/register',[RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register',[RegisterController::class,'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/resumes',          [ResumeController::class, 'index'])->name('resumes.index');
    Route::get('/resumes/{resume}', [ResumeController::class, 'show'])->name('resumes.show');
    Route::post('/resumes/upload',  [ResumeController::class, 'upload'])->name('resumes.upload');
    Route::delete('/resumes/{resume}', [ResumeController::class, 'destroy'])->name('resumes.destroy');
    Route::post('/resumes/{resume}/reanalyze', [ResumeController::class, 'reanalyze'])->name('resumes.reanalyze');
});