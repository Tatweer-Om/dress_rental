<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\DressController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('home', [HomeController::class, 'index'])->name('home');
Route::get('/switch-language/{locale}', [HomeController::class, 'switchLanguage'])->name('switch_language');
// category dress
Route::get('category', [CategoryController::class, 'index'])->name('category');
Route::post('add_category', [CategoryController::class, 'add_category'])->name('add_category');
Route::get('show_category', [CategoryController::class, 'show_category'])->name('show_category');
Route::post('edit_category', [CategoryController::class, 'edit_category'])->name('edit_category');
Route::post('update_category', [CategoryController::class, 'update_category'])->name('update_category');
Route::post('delete_category', [CategoryController::class, 'delete_category'])->name('delete_category');
// brand dress
Route::get('brand', [BrandController::class, 'index'])->name('brand');
Route::post('add_brand', [BrandController::class, 'add_brand'])->name('add_brand');
Route::get('show_brand', [BrandController::class, 'show_brand'])->name('show_brand');
Route::post('edit_brand', [BrandController::class, 'edit_brand'])->name('edit_brand');
Route::post('update_brand', [BrandController::class, 'update_brand'])->name('update_brand');
Route::post('delete_brand', [BrandController::class, 'delete_brand'])->name('delete_brand');

// size dress
Route::get('size', [SizeController::class, 'index'])->name('size');
Route::post('add_size', [SizeController::class, 'add_size'])->name('add_size');
Route::get('show_size', [SizeController::class, 'show_size'])->name('show_size');
Route::post('edit_size', [SizeController::class, 'edit_size'])->name('edit_size');
Route::post('update_size', [SizeController::class, 'update_size'])->name('update_size');
Route::post('delete_size', [SizeController::class, 'delete_size'])->name('delete_size');

// color dress
Route::get('color', [ColorController::class, 'index'])->name('color');
Route::post('add_color', [ColorController::class, 'add_color'])->name('add_color');
Route::get('show_color', [ColorController::class, 'show_color'])->name('show_color');
Route::post('edit_color', [ColorController::class, 'edit_color'])->name('edit_color');
Route::post('update_color', [ColorController::class, 'update_color'])->name('update_color');
Route::post('delete_color', [ColorController::class, 'delete_color'])->name('delete_color');

// dress dress
Route::get('dress', [DressController::class, 'index'])->name('dress');
Route::post('add_dress', [DressController::class, 'add_dress'])->name('add_dress');
Route::get('show_dress', [DressController::class, 'show_dress'])->name('show_dress');
Route::post('edit_dress', [DressController::class, 'edit_dress'])->name('edit_dress');
Route::post('update_dress', [DressController::class, 'update_dress'])->name('update_dress');
Route::post('delete_dress', [DressController::class, 'delete_dress'])->name('delete_dress');
Route::post('remove_attachments', [DressController::class, 'remove_attachments'])->name('remove_attachments');
Route::post('e_remove_attachments', [DressController::class, 'e_remove_attachments'])->name('e_remove_attachments');
Route::post('upload_attachments', [DressController::class, 'upload_attachments'])->name('upload_attachments');
