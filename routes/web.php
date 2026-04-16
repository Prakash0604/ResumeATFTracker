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
    Route::post('/login',  [LoginController::class,    'login'])->name('login.store');
    Route::get('/register',[RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register',[RegisterController::class,'register'])->name('register.store');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/resumes',          [ResumeController::class, 'index'])->name('resumes.index');
    Route::get('/resumes/{resume}', [ResumeController::class, 'show'])->name('resumes.show');
    Route::post('/resumes/upload',  [ResumeController::class, 'upload'])->name('resumes.upload');
    Route::delete('/resumes/{resume}', [ResumeController::class, 'destroy'])->name('resumes.destroy');
    Route::post('/resumes/{resume}/reanalyze', [ResumeController::class, 'reanalyze'])->name('resumes.reanalyze');
});

Route::middleware(['auth', 'throttle:60,1'])->prefix('api')->group(function () {

    Route::get('/resumes/{resume}/status', [ResumeController::class, 'status'])
        ->name('api.resumes.status');

    Route::post('/resumes/{resume}/feedback/{feedbackId}/address',
        [ResumeController::class, 'addressFeedback'])
        ->name('api.feedback.address');
});


// Route::get('/ask',function(){
//     curl https://openrouter.ai/api/v1/chat/completions \
//   -H "Authorization: Bearer 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" \
//   -H "Content-Type: application/json" \
//   -d '{
//     "model": "google/gemma-4-31b-it:free",
//     "messages": [
//       { "role": "user", "content": "Hello AI" }
//     ]
//   }'
// })