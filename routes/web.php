<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\BarangKeluarController;
use App\Http\Controllers\DashboardController;


Route::get('/', function () {
    return view('login');
});


//ROUTE UNTUK MENUUJU HALAMAN DASHBOARD
Route::get('dashboard', [DashboardController::class,'index']);

//ROUTE UNTUK MENUUJU HALAMAN KATEGORI DENGAN MENAMBAH PROSES AUTENTIKASI
Route::resource('kategori', KategoriController::class)->middleware('auth');

//ROUTE UNTUK MENUUJU HALAMAN BARANG DENGAN MENAMBAH PROSES AUTENTIKASI
Route::resource('barang', BarangController::class)->middleware('auth');

//ROUTE UNTUK MENUJU HALAMAN BARANGMASUK DENGAN MENAMBAH PROSES AUTENTIKASI
Route::resource('barangmasuk', BarangMasukController::class)->middleware('auth');

//ROUTE UNTUK MENUJU HALAMAN BARANGKELUAR DENGAN MENAMBAH PROSES AUTENTIKASI
Route::resource('barangkeluar', BarangKeluarController::class)->middleware('auth');

//ROUTE UNTUK MENUJU HALAMAN LOGIN
Route::get('login', [LoginController::class,'index'])->name('login')->middleware('guest');
Route::post('login', [LoginController::class,'authenticate']);

//ROUTE UNTUK MENUJU HALAMAN LOGOUT
Route::get('logout', [LoginController::class,'logout']);
Route::post('logout', [LoginController::class,'logout']);

//ROUTE UNTUK MENUJU HALAMAN REGISTER
Route::post('register', [RegisterController::class,'store']);
Route::get('/register', [RegisterController::class,'create']);
