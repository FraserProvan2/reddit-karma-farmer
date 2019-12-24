<?php

/*------------------------------------------------------------------------
| Auth Routes
|-------------------------------------------------------------------------*/

Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login')->name('login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

/*------------------------------------------------------------------------
| Web App Navigation Routes
|-------------------------------------------------------------------------*/

Route::get('/home', function() {
  return view('home');
})->middleware('auth');

Route::get('/', function() {
  return redirect('/home');
});

/*------------------------------------------------------------------------
| Runtime Routes
|-------------------------------------------------------------------------*/

Route::get('/run', 'RepostController@run')->middleware('auth');
