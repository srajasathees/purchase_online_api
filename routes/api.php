<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


//API route for register new user
Route::post('/register', [App\Http\Controllers\API\AuthController::class, 'register']);
//API route for login user
Route::post('/login', [App\Http\Controllers\API\AuthController::class, 'login']);

//Protecting Routes
Route::group(['middleware' => ['auth:sanctum']], function () {
	
	Route::get('/home', [App\Http\Controllers\API\HomeController::class, 'index']);
	
    Route::get('/profile', function(Request $request) {
        return auth()->user();
    });
	
	Route::resource('roles', App\Http\Controllers\API\RoleController::class);
	Route::resource('users', App\Http\Controllers\API\UserController::class);
	Route::resource('settings', App\Http\Controllers\API\SettingController::class);
	Route::resource('departments', App\Http\Controllers\API\DepartmentController::class);
	

    // API route for logout user
    Route::post('/logout', [App\Http\Controllers\API\AuthController::class, 'logout']);
	
	Route::get('permissions', 'API\PermissionController@permission_list');
 Route::post('permissions', 'API\PermissionController@permission_store');
 Route::post('rolepermissions/{role}', 'API\PermissionController@role_has_permissions');
 Route::post('assignuserrole/{role}', 'API\PermissionController@assign_user_to_role');
 
});

Route::fallback(function(){
    return response()->json(['message' => 'Not Found.'], 404);
})->name('api.fallback.404');
