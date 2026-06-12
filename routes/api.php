<?php
use App\Http\Controllers\ScannerController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

// Route::apiResource('students', StudentController::class);
Route::get('students', [StudentController::class, 'index']);
Route::post('students', [StudentController::class, 'store']);
Route::put('students/{student}', [StudentController::class, 'update']);
Route::delete('students/{student}', [StudentController::class, 'destroy']); 
Route::post('scan', [ScannerController::class, 'scan']);
Route::post('semester/refresh', [SemesterController::class, 'refresh']);
Route::get('students/{student}/history', [StudentController::class, 'history']);
Route::get('usage-history', [StudentController::class, 'allHistory']);  