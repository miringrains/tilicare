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
    return view('client.home');
});

Auth::routes();
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Creation Form Request
Route::get('/new', [App\Http\Controllers\PackagesController::class, 'index'])->name('panel.new')->middleware('auth');
Route::get('/new/one', [App\Http\Controllers\PackagesController::class, 'new_one'])->name('panel.new.one')->middleware('auth');
Route::get('/new/two', [App\Http\Controllers\PackagesController::class, 'new_two'])->name('panel.new.two')->middleware('auth');
Route::get('/new/three', [App\Http\Controllers\PackagesController::class, 'new_three'])->name('panel.new.three')->middleware('auth');
Route::get('/new/four', [App\Http\Controllers\PackagesController::class, 'new_four'])->name('panel.new.four')->middleware('auth');

// Creation Submissions
Route::get('/new/back_to/{step}', [App\Http\Controllers\PackagesController::class, 'back_to'])->name('panel.new.back_to')->middleware('auth');
Route::post('/new/one', [App\Http\Controllers\PackagesController::class, 'new_one_submit'])->name('panel.new.one.submit')->middleware('auth');
Route::post('/new/two', [App\Http\Controllers\PackagesController::class, 'new_two_submit'])->name('panel.new.two.submit')->middleware('auth');
Route::post('/new/three', [App\Http\Controllers\PackagesController::class, 'new_three_submit'])->name('panel.new.three.submit')->middleware('auth');
Route::post('/new/four/images', [App\Http\Controllers\PackagesController::class, 'new_four_images'])->name('panel.new.four.images')->middleware('auth');
Route::get('/new/four/images/del', [App\Http\Controllers\PackagesController::class, 'new_four_images_del'])->name('panel.new.four.images.del')->middleware('auth');
Route::post('/new/four', [App\Http\Controllers\PackagesController::class, 'new_four_submit'])->name('panel.new.four.submit')->middleware('auth');


// Admin
Route::get('admin_panel', [App\Http\Controllers\PanelController::class, 'dashboard'])->name('panel.dashboard');
