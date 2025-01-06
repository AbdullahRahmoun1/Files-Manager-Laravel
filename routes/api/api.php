<?php

use App\Http\Controllers\CheckInController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\GroupUserController;
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
    Route::feature('files', FileController::class);
    Route::get('files/{file}/children',[FileController::class,'getChildren']);
    Route::controller(CheckInController::class)->group(function(){
        Route::post('files/{file_id}/check-in','checkIn');
        Route::post('files/{file_id}/check-out','checkOut');
        Route::get('checked-files/','checked-files');
    });
});
