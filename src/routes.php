<?php

use Illuminate\Support\Facades\Route;
use TonyCodes\GitFlowReporter\Controllers\GitFlowReporterController;

Route::group(['prefix' => 'gitflow-reporter', 'middleware' => ['web']], function () {
    
    // Main error reporting endpoint
    Route::post('report', [GitFlowReporterController::class, 'store'])
        ->name('gitflow-reporter.report');
    
    // Get package configuration
    Route::get('config', [GitFlowReporterController::class, 'config'])
        ->name('gitflow-reporter.config');
        
});