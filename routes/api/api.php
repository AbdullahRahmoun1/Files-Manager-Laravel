<?php

use App\Http\Controllers\CheckInController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FileHistoryController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\GroupUserController;
use App\Http\Controllers\UserController;
use App\Services\FileComparisonService;
use Illuminate\Support\Facades\Route;

Route::get('test', function () {
    return app(FileComparisonService::class)->compare(
        'files/bYPgQKmDkuv6924jLhlpt1mq7HqbpZB93vbc6LG9.txt',
        'files/elRlxRmMEoAJFdqVU3rF3ezewaF2eUvtRUU2HXYR.txt'
    );
});
Route::middleware('auth:sanctum')->group(function () {
    Route::get('users/search', [UserController::class, 'dotAll']);
    Route::feature('groups', GroupController::class);
    Route::prefix('group-invitations')
        ->controller(GroupUserController::class)
        ->group(function () {
            Route::get('all', 'viewInvitations');
            Route::post('create', 'createInvitation');
            Route::post('accept/{id}', 'acceptInvitation');
            Route::delete('delete/{id}', 'deleteInvitation');
        });
    Route::feature('files', FileController::class);
    Route::get('files/{file}/children', [FileController::class, 'getChildren']);
    Route::get('groups/{group}/files-to-approve', [FileController::class, 'getPendingFiles']);
    Route::post('groups/files/decideStatus', [FileController::class, 'decideFileStatus']);
    Route::controller(CheckInController::class)->group(function () {
        Route::post('files/{file_id}/check-in', 'checkIn');
        Route::post('files/{files_ids}/bulk-check-in', 'bulkCheckIn');
        Route::post('files/{file_id}/check-out', 'checkOut');
        Route::get('checked-files/', 'checked-files');
    });
    Route::feature('file-history', FileHistoryController::class);
    Route::get('files/{file}/report', [FileController::class, 'getFileReport']);
    Route::get('groups/{group}/report', [GroupController::class, 'getGroupReport']);
});
