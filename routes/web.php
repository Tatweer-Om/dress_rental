<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ColorController;

use App\Http\Controllers\DressController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ExpenseCategoryController;

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
Route::post('maint_dress', [DressController::class, 'maint_dress'])->name('maint_dress');
Route::get('maint_dress_all', [DressController::class, 'maint_dress_all'])->name('maint_dress_all');
Route::get('show_maint_dress', [DressController::class, 'show_maint_dress'])->name('show_maint_dress');
Route::post('maint_dress_comp', [DressController::class, 'maint_dress_comp'])->name('maint_dress_comp');

// booking
Route::get('booking', [BookingController::class, 'index'])->name('booking');
Route::post('get_dress_detail', [BookingController::class, 'get_dress_detail'])->name('get_dress_detail');
Route::post('add_booking', [BookingController::class, 'add_booking'])->name('add_booking');
Route::get('view_booking', [BookingController::class, 'view_booking'])->name('view_booking');
Route::get('show_booking', [BookingController::class, 'show_booking'])->name('show_booking');
Route::post('search_customer', [BookingController::class, 'search_customer']);
Route::post('add_booking_customer', [BookingController::class, 'add_booking_customer'])->name('add_booking_customer');
Route::post('get_payment', [BookingController::class, 'get_payment'])->name('get_payment');
Route::post('add_payment', [BookingController::class, 'add_payment'])->name('add_payment');
Route::post('add_dress_availability', [BookingController::class, 'add_dress_availability'])->name('add_dress_availability');
Route::post('get_booking_detail', [BookingController::class, 'get_booking_detail'])->name('get_booking_detail');



// customer dress
Route::get('customer', [CustomerController::class, 'index'])->name('customer');
Route::post('add_customer', [CustomerController::class, 'add_customer'])->name('add_customer');
Route::get('show_customer', [CustomerController::class, 'show_customer'])->name('show_customer');
Route::post('edit_customer', [CustomerController::class, 'edit_customer'])->name('edit_customer');
Route::post('update_customer', [CustomerController::class, 'update_customer'])->name('update_customer');
Route::post('delete_customer', [CustomerController::class, 'delete_customer'])->name('delete_customer');
Route::get('customer_profile/{id}', [CustomerController::class, 'customer_profile'])->name('customer_profile');
Route::post('customer_profile_data', [CustomerController::class, 'customer_profile_data'])->name('customer_profile_data');
//user
Route::get('login', [UserController::class, 'login'])->name('login');
Route::get('user', [UserController::class, 'index'])->name('user');
Route::post('add_user', [UserController::class, 'add_user'])->name('add_user');
Route::get('show_user', [UserController::class, 'show_user'])->name('show_user');
Route::post('edit_user', [UserController::class, 'edit_user'])->name('edit_user');
Route::post('update_user', [UserController::class, 'update_user'])->name('update_user');
Route::post('delete_user', [UserController::class, 'delete_user'])->name('delete_user');


Route::get('expense_category', [ExpenseCategoryController::class, 'index'])->name('expense_category');
Route::post('add_expense_category', [ExpenseCategoryController::class, 'add_expense_category'])->name('add_expense_category');
Route::get('show_expense_category', [ExpenseCategoryController::class, 'show_expense_category'])->name('show_expense_category');
Route::post('edit_expense_category', [ExpenseCategoryController::class, 'edit_expense_category'])->name('edit_expense_category');
Route::post('update_expense_category', [ExpenseCategoryController::class, 'update_expense_category'])->name('update_expense_category');
Route::post('delete_expense_category', [ExpenseCategoryController::class, 'delete_expense_category'])->name('delete_expense_category');

// expense_categoryController Routes

Route::get('expense', [ExpenseController::class, 'index'])->name('expense');
Route::post('add_expense', [ExpenseController::class, 'add_expense'])->name('add_expense');
Route::get('show_expense', [ExpenseController::class, 'show_expense'])->name('show_expense');
Route::post('edit_expense', [ExpenseController::class, 'edit_expense'])->name('edit_expense');
Route::post('update_expense', [ExpenseController::class, 'update_expense'])->name('update_expense');
Route::post('delete_expense', [ExpenseController::class, 'delete_expense'])->name('delete_expense_category');
Route::get('download_expense_image/{id}', [ExpenseController::class, 'download_expense_image'])->name('download_expense_image');


// AccountController Routes

Route::get('account', [AccountController::class, 'index'])->name('account');
Route::post('add_account', [AccountController::class, 'add_account'])->name('add_account');
Route::get('show_account', [AccountController::class, 'show_account'])->name('show_account');
Route::post('edit_account', [AccountController::class, 'edit_account'])->name('edit_account');
Route::post('update_account', [AccountController::class, 'update_account'])->name('update_account');
Route::post('delete_account', [AccountController::class, 'delete_account'])->name('delete_account');


//sms
Route::get('sms', [SmsController::class, 'index'])->name('sms');
Route::post('get_sms_status', [SmsController::class, 'get_sms_status'])->name('get_sms_status');
Route::match(['get', 'post'], 'add_status_sms', [SmsController::class, 'add_status_sms'])->name('add_status_sms');

