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
    $urlBase = "/main/gc_logictest";
    return view('main', compact('urlBase'));
});


Route::get('main/{any}', 'MainController@index');

/**
 * Routes Matrix.
 */
Route::group(['prefix' => '/matrix'], function () {
    Route::post('/upload-data', 'MatrixUploader@uploadFile')->name('matrix.upload');
    Route::post('/analyze-data', 'MatrixUploader@analyze')->name('matrix.analyze');
});

