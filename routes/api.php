<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\ActivityLogController; 
use App\Http\Controllers\Api\UserController; 
use App\Http\Controllers\Api\RolePermissionController;

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);

        // Endpoints requiring a Bearer Token
        Route::middleware('validate.jwt')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::post('/refresh', [AuthController::class, 'refresh']);
            Route::get('/me', [AuthController::class, 'me']);
        });
    });

    Route::prefix('activity-logs')->middleware('validate.jwt')->group(function () {
        Route::get('/', [ActivityLogController::class, 'getAllLogs']);
        Route::get('/log-name/{log_name}', [ActivityLogController::class, 'getLogsByLogName']);
        Route::get('/date-range', [ActivityLogController::class, 'getLogsByDateRange']);
    });

    Route::prefix('users')->middleware('validate.jwt')->group(function () {
        Route::post('/bulk', [UserController::class, 'createUsers']); 
        Route::post('/', [UserController::class, 'createUser']); 
        Route::get('/', [UserController::class, 'getAllUsers']);  
        Route::get('/{id}', [UserController::class, 'getUserById']); 
        Route::put('/{id}', [UserController::class, 'updateUser']); 
        Route::delete('/{id}', [UserController::class, 'deleteUser']); 
        Route::delete('/', [UserController::class, 'deleteUsers']);
        Route::patch('/{id}/status', [UserController::class, 'updateUserStatus']);
    });

    Route::prefix('permissions')->group(function () {
        Route::post('/', [RolePermissionController::class, 'createPermissions']);
        Route::get('/', [RolePermissionController::class, 'getAllPermissions']);
        Route::get('/{id}', [RolePermissionController::class, 'getPermissionById']);
        Route::delete('/{id}', [RolePermissionController::class, 'deletePermission']);
        Route::delete('/', [RolePermissionController::class, 'deletePermissions']);
        Route::put('/{id}', [RolePermissionController::class, 'updatePermission']);
    });

    Route::prefix('roles')->group(function () {
        Route::post('/', [RolePermissionController::class, 'createRoles']);
        Route::get('/', [RolePermissionController::class, 'getAllRoles']);
        Route::get('/with-permissions', [RolePermissionController::class, 'getRolesWithPermissions']);
        Route::get('/{id}', [RolePermissionController::class, 'getRoleById']);
        Route::get('/{id}/permissions', [RolePermissionController::class, 'getRoleWithPermissions']);
        Route::post('/{id}/permissions', [RolePermissionController::class, 'assignPermissionsToRole']);
        Route::delete('/{role_id}/permissions', [RolePermissionController::class, 'detachPermissionsFromRole']);
        Route::delete('/{id}', [RolePermissionController::class, 'deleteRole']);
        Route::put('/{id}', [RolePermissionController::class, 'updateRole']);
        Route::put('/{id}/permissions', [RolePermissionController::class, 'updateRoleWithPermissions']);
    });
});
