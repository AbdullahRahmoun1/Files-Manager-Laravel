<?php

use App\Enums\RoleTypeEnum;
use App\Http\Controllers\CheckInController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FileHistoryController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\GroupUserController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::feature('groups', GroupController::class);
    Route::prefix('group-invitations')
        ->controller(GroupUserController::class)
        ->group(function () {
            Route::get('all', 'viewInvitations');
            Route::post('create', 'createInvitation');
            Route::post('accept/{id}', 'acceptInvitation');
            Route::delete('delete/{id}', 'deleteInvitation');
        });
    Route::delete('groups/{group}/users/{user}/kick', [GroupController::class, 'kickUser']);
    Route::delete('groups/{group}/leave', [GroupController::class, 'leaveGroup']);
    Route::get('groups/{group}/files-to-approve', [FileController::class, 'getPendingFiles']);
    Route::post('groups/files/decideStatus', [FileController::class, 'decideFileStatus']);
    Route::delete('groups/{group}/files/{file}/remove', [FileController::class, 'removeFile']);
    Route::get('users/search', [UserController::class, 'dotAll']);
    Route::feature('files', FileController::class);
    Route::get('files/{file}/download', [FileController::class,'downloadFile']);
    Route::post('files/{file}/rename-folder', [FileController::class,'renameFolder']);
    Route::get('files/{file}/children', [FileController::class, 'getChildren']);
    Route::controller(CheckInController::class)->group(function () {
        Route::post('files/{file_id}/check-in', 'checkIn');
        Route::post('files/{files_ids}/bulk-check-in', 'bulkCheckIn');
        Route::post('files/{file_id}/check-out', 'checkOut');
        Route::get('checked-files/', 'checked-files');
    });
    Route::feature('file-history', FileHistoryController::class);
    Route::get('files/{file}/report', [FileController::class, 'getFileReport']);
    Route::get('groups/{group}/report', [GroupController::class, 'getGroupReport']);
    Route::middleware('ability:' . RoleTypeEnum::ADMIN->value)
        ->get('users/{user}/report', [UserController::class, 'report']);
});
