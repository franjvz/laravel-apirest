<?php

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
    return view('welcome');
});


Route::group(
	[
	    'middleware' => 'cors',
	    'prefix' => 'api'
	],
	function ($router) {
	    Route::post('register', 'UserController@register');
		Route::post('login', 'UserController@login');
		Route::resource('cars', 'CarController');


		// Do not let GET petitions to call Method Controller
		Route::get('register', function(){return "Only POST requests allowed";});
		Route::get('login', function(){return "Only POST requests allowed";});
	});
/*Route::post('/api/register', 'UserController@register');
Route::post('/api/login', 'UserController@login');
Route::resource('/api/cars', 'CarController');*/