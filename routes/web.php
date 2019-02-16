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

Route::get('/login/{provider}', 'Auth\AuthController@redirectToProvider')->name('login');

Route::get('/login/{provider}/callback', 'Auth\AuthController@handleProviderCallback');

Route::post('/logout', function() {
    Auth::logout();
    return redirect()->route('home');
})->name('logout');

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::resource('events', 'EventController')->only([
   'index', 'show', 'update'
]);

Route::resource('attendees', 'AttendeeController')->only([
   'index', 'show'
]);

Route::get('/me', 'AttendeeController@actions')->name('me');
Route::get('/passbook', 'AttendeeController@passbook')->name('passbook');
Route::get('/passbook/{id}', 'AttendeeController@others_passbook')->name('others_passbook');

Route::resource('reports', 'ReportController')->only([
   'index', 'show'
]);

Route::post('events/{id}/pay', 'EventController@payment')->name('events.pay');
Route::post('events/{id}/paystatus', 'EventController@payment_status')->name('events.payment_status');
