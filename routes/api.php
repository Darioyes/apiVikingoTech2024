<?php

use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\CategoriesProductsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CategoriesDirectCostsController;
use App\Http\Controllers\Admin\CategoriesIndirectCostsController;
use App\Http\Controllers\Admin\CitiesController;
use App\Http\Controllers\Admin\VikingoRolesController;
use App\Http\Controllers\Admin\ProductsController;
use App\Http\Controllers\Admin\MaintenancesController;

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
    //?rutas de usuarios
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

    //?rutas de categorias de productos
    //ruta tipo recurso de categoriesProducts
    Route::resource('categoriesproducts', CategoriesProductsController::class)->only(['index', 'store', 'show','update','destroy']);
    //?rutas de categorias de costos directos
    //ruta tipo recurso de categoriesDirectCosts
    Route::resource('categoriesdirectcosts', CategoriesDirectCostsController::class)->only(['index', 'store', 'show','update','destroy']);

    //?rutas de categorias de costos indirectos
    //ruta tipo recurso de categoriesIndirectCosts
    Route::resource('categoriesindirectcosts', CategoriesIndirectCostsController::class)->only(['index', 'store', 'show','update','destroy']);

    //?rutas de ciudades
    //ruta tipo recurso de cities
    Route::resource('cities', CitiesController::class)->only(['index', 'store', 'show','update','destroy']);

    //?rutas de roles
    //ruta tipo recurso de roles
    Route::resource('roles', VikingoRolesController::class)->only(['index', 'store', 'show','update','destroy']);

    //?rutas de productos
    //ruta tipo recurso de products
    Route::resource('products', ProductsController::class)->only(['index', 'store', 'show','update','destroy']);

    //?rutas de mantenimientos
    //ruta tipo recurso de maintenances
    Route::resource('maintenances', MaintenancesController::class)->only(['index', 'store', 'show','update','destroy']);

});
