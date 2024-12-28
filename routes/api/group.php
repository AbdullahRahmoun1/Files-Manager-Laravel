<?php

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
});
