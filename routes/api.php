<?php

use App\Http\Controllers\Admin\UsersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//!rutas bakcend
//ruta de login
Route::post('vikingouser/login', [UsersController::class, 'login']);

Route::middleware('auth:sanctum','verified')->group(function(){
    //ruta tipo recurso de users
    Route::resource('users', UsersController::class)->only(['index', 'store', 'show','update','destroy']);
    //Route::resource('users', UsersController::class)->except(['create','edit']);
    //ruta de busqueda por nombre o apellido de usuario
    Route::get('searchusers/{user}',[UsersController::class,'searchUser']);
    //ruta del total de usuarios
    Route::get('userstotal',[UsersController::class,'totalUsers']);
    //ruta total por genero de usuario
    Route::get('gender',[UsersController::class,'countGender']);
    //ruta de promedio de usuarios por genero
    Route::get('genderavg',[UsersController::class,'genderAVG']);
    //ruta de logout de users
    Route::post('vikingouser/logout', [UsersController::class, 'logout']);
});
