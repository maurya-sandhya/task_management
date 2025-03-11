<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TimesheetController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\DashboardController;




Route::get('/', function () {
    return view('layouts.layout');
});
Route::get('/tasks', [TaskController::class, 'index']);
Route::resource('tasks', TaskController::class);
Route::put('/tasks/{task}/update-status', [TaskController::class, 'updateStatus'])->name('tasks.update-status');
Route::resource('timesheets', TimesheetController::class)->except(['create', 'show']);
Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
Route::get('/reports/data', [ReportController::class, 'getData'])->name('reports.data');
Route::get('/reports/export-pdf', [ReportController::class, 'exportPDF'])->name('reports.exportPDF');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.data');


