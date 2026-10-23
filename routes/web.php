<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DpmController;
use App\Http\Controllers\ExecutiveDashboardController;
use App\Http\Controllers\ImcController;
use App\Http\Controllers\OmeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Naivas Marketing Command Center
|--------------------------------------------------------------------------
*/

// Judge Portal Landing Page
Route::get('/', [AuthController::class, 'portal'])->name('portal');
Route::get('/portal', [AuthController::class, 'portal']);

// Frictionless Login for Judges
Route::get('/login/as/{role}', [AuthController::class, 'loginAs'])->name('login.as');
Route::post('/login/as/{role}', [AuthController::class, 'loginAs'])->name('login.as.post');
Route::get('/switch-role/{role}', [AuthController::class, 'switchRole'])->name('switch.role');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/logout', [AuthController::class, 'logout']);

// Demo Reset Helper
Route::post('/demo/reset', [AuthController::class, 'resetDemo'])->name('demo.reset');
Route::get('/demo/reset', [AuthController::class, 'resetDemo']);

// Protected / Authenticated Routes
Route::middleware(['web'])->group(function () {
    // 1. Executive (CMO / Marketing Lead) Module
    Route::get('/executive', [ExecutiveDashboardController::class, 'index'])->name('executive.dashboard');
    Route::post('/executive/campaigns', [ExecutiveDashboardController::class, 'storeCampaign'])->name('executive.campaigns.store');

    // 2. IMC (Integrated Marketing Communications) Module
    Route::get('/imc', [ImcController::class, 'index'])->name('imc.dashboard');
    Route::post('/imc/briefs', [ImcController::class, 'storeBrief'])->name('imc.briefs.store');
    Route::post('/imc/assets', [ImcController::class, 'storeAsset'])->name('imc.assets.store');
    Route::post('/imc/assets/{asset}/approve', [ImcController::class, 'approveAsset'])->name('imc.assets.approve');
    Route::post('/imc/assets/{asset}/reject', [ImcController::class, 'rejectAsset'])->name('imc.assets.reject');

    // 3. DPM (Digital & Performance Marketing) Module
    Route::get('/dpm', [DpmController::class, 'index'])->name('dpm.dashboard');
    Route::post('/dpm/campaigns/{campaign}/deploy', [DpmController::class, 'deployAds'])->name('dpm.campaigns.deploy');
    Route::post('/dpm/budget/reallocate', [DpmController::class, 'reallocateBudget'])->name('dpm.budget.reallocate');

    // 4. OME (Operations Marketing Execution) Module
    Route::get('/ome', [OmeController::class, 'index'])->name('ome.dashboard');
    Route::post('/ome/tasks/{task}/proof', [OmeController::class, 'submitProof'])->name('ome.tasks.proof');
    Route::post('/ome/tasks/{task}/verify', [OmeController::class, 'verifyTask'])->name('ome.tasks.verify');
});
