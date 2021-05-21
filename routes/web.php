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
    return redirect()->to('/login');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::middleware('auth')->group(function () {
    Route::resource('product-variant', 'VariantController');
    Route::resource('blog', 'BlogController');
    Route::resource('blog-category', 'BlogCategoryController');
    Route::resource('product/create', 'BlogCategoryController');

    // PRODUCT MODULE
    // Route::resource('product', 'ProductController');

    Route::group(['prefix'=>'product'], function(){
    	Route::match(['GET', 'POST'], '/', 'ProductController@index')->name('product.index');
    	Route::get('/create', 'ProductController@create')->name('product.create');
    	Route::post('/store', 'ProductController@store');
    	Route::get('/edit/{id}', 'ProductController@edit')->name('product.edit');
    	Route::post('/update', 'ProductController@update');
    	Route::post('/variants', 'ProductController@variants')->name('product.variants');
    });

});
