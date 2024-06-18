<?php

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\UploadManager;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

Route::get('/index', [HomeController::class, 'index'])->middleware('auth.redirect')->name('index');
Route::resource('/mains', MainController::class)->middleware('auth.redirect');

Route::get('/upload', [UploadManager::class, 'upload'])->name('upload')->middleware('auth.redirect');
Route::post('/upload', [UploadManager::class, 'uploadPost'])->name('upload.post')->middleware('auth.redirect');

Route::get('/mains/display', [UploadManager::class, 'display'])->name('display.geojson')->middleware('auth.redirect');
Route::get('/fetch-geojson', [UploadManager::class, 'fetch'])->name('fetch.geojson')->middleware('auth.redirect');

Route::post('/map/add-to-favorites', [MainController::class, 'addToFavorites']);
Route::post('/map/remove-from-favorites', [MainController::class, 'removeFromFavorites']);
Route::post('/map/add-to-recents', [MainController::class, 'addToRecents']);
Route::post('/map/remove-from-recents', [MainController::class, 'removeFromRecents']);
Route::post('/map/delete-selection', [MainController::class, 'deleteSelection']);