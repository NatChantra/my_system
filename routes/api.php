<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\UsageRecordController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\StockTransactionController;

// ===== Employee =====
Route::get   ('/employees',      [EmployeeController::class, 'index']);
Route::post  ('/employees',      [EmployeeController::class, 'store']);
Route::put   ('/employees/{id}', [EmployeeController::class, 'update']);
Route::delete('/employees/{id}', [EmployeeController::class, 'destroy']);

// ===== Attendance =====
Route::get ('/attendance',      [AttendanceController::class, 'index']);
Route::post('/attendance/scan', [AttendanceController::class, 'scan']);

// ===== Tasks =====
Route::get   ('/tasks',      [TaskController::class, 'index']);
Route::post  ('/tasks',      [TaskController::class, 'store']);
Route::put   ('/tasks/{id}', [TaskController::class, 'update']);
Route::delete('/tasks/{id}', [TaskController::class, 'destroy']);

// ===== Leave Requests =====
Route::get   ('/leaves',             [LeaveRequestController::class, 'index']);
Route::post  ('/leaves',             [LeaveRequestController::class, 'store']);
Route::patch ('/leaves/{id}/status', [LeaveRequestController::class, 'updateStatus']);
Route::delete('/leaves/{id}',        [LeaveRequestController::class, 'destroy']);

// ===== Resource (Stock) =====
Route::get   ('/resources',      [ResourceController::class, 'index']);
Route::post  ('/resources',      [ResourceController::class, 'store']);
Route::put   ('/resources/{id}', [ResourceController::class, 'update']);
Route::delete('/resources/{id}', [ResourceController::class, 'destroy']);

// ===== Usage Records =====
Route::get   ('/usage-records',      [UsageRecordController::class, 'index']);
Route::post  ('/usage-records',      [UsageRecordController::class, 'store']);
Route::delete('/usage-records/{id}', [UsageRecordController::class, 'destroy']);

// ===== Suppliers =====
Route::get   ('/suppliers',      [SupplierController::class, 'index']);
Route::post  ('/suppliers',      [SupplierController::class, 'store']);
Route::put   ('/suppliers/{id}', [SupplierController::class, 'update']);
Route::delete('/suppliers/{id}', [SupplierController::class, 'destroy']);

// ===== Categories =====
Route::get   ('/categories',      [CategoryController::class, 'index']);
Route::post  ('/categories',      [CategoryController::class, 'store']);
Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

// ===== Stock In / Out / History / Report =====
Route::post('/stock/in',      [StockTransactionController::class, 'stockIn']);
Route::post('/stock/out',     [StockTransactionController::class, 'stockOut']);
Route::get ('/stock/history', [StockTransactionController::class, 'index']);
Route::get ('/stock/report',  [StockTransactionController::class, 'report']);