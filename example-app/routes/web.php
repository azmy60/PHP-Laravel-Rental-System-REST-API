<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return 'Welcome!';
});



Route::get('user', "\App\Http\Controllers\UserController@getAll");
Route::get('user/{id}', "\App\Http\Controllers\UserController@get");
Route::post('user', "\App\Http\Controllers\UserController@create");
Route::put('user/{id}', "\App\Http\Controllers\UserController@update");
Route::delete('user/{id}', "\App\Http\Controllers\UserController@delete");

Route::get('inventory', "\App\Http\Controllers\InventoryController@getAll");
Route::get('inventory/{id}', "\App\Http\Controllers\InventoryController@get");
Route::post('inventory', "\App\Http\Controllers\InventoryController@create");
Route::put('inventory/{id}', "\App\Http\Controllers\InventoryController@update");
Route::delete('inventory/{id}', "\App\Http\Controllers\InventoryController@delete");

Route::get('inventory/{id}/rental','\App\Http\Controllers\RentalController@getAllRentalByInventoryId');
Route::get('rental', "\App\Http\Controllers\RentalController@getAll");
Route::get('rental/{id}', "\App\Http\Controllers\RentalController@get");
Route::post('rental', "\App\Http\Controllers\RentalController@create");
Route::put('rental/{id}', "\App\Http\Controllers\RentalController@update");
Route::delete('rental/{id}', "\App\Http\Controllers\RentalController@delete");
