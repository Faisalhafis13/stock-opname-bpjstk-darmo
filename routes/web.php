<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KategoriBarangController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\PenambahanBarangController;
use App\Http\Controllers\PemakaianBarangController;
use App\Http\Controllers\PeriodeStokOpnameController;
use App\Http\Controllers\StokOpnameController;
use App\Http\Controllers\LaporanStokController;
use App\Http\Controllers\LaporanStokOpnameController;
use App\Http\Controllers\RekapitulasiController;
use App\Http\Controllers\RoleMenuController;
use App\Http\Controllers\RoleUserController;
use App\Http\Controllers\BarangUserController;
use App\Http\Controllers\PengajuanController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Root
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});


/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    Route::get(
        '/login',
        [AuthController::class, 'showLogin']
    )->name('login');

    Route::post(
        '/login',
        [AuthController::class, 'login']
    )->name('login.store');

});


Route::post(
    '/logout',
    [AuthController::class, 'logout']
)
    ->middleware('auth')
    ->name('logout');


/*
|--------------------------------------------------------------------------
| Application
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'menu.access'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/dashboard',
        [DashboardController::class, 'index']
    )->name('dashboard');


    /*
    |--------------------------------------------------------------------------
    | Kategori Barang
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/kategori-barang',
        [KategoriBarangController::class, 'index']
    )->name('kategori-barang.index');

    Route::post(
        '/kategori-barang',
        [KategoriBarangController::class, 'store']
    )->name('kategori-barang.store');

    Route::put(
        '/kategori-barang/{id}',
        [KategoriBarangController::class, 'update']
    )->name('kategori-barang.update');

    Route::delete(
        '/kategori-barang/{id}',
        [KategoriBarangController::class, 'destroy']
    )->name('kategori-barang.destroy');


    /*
    |--------------------------------------------------------------------------
    | Data Barang Admin
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/barang',
        [BarangController::class, 'index']
    )->name('barang.index');

    Route::post(
        '/barang',
        [BarangController::class, 'store']
    )->name('barang.store');

    Route::put(
        '/barang/{id}',
        [BarangController::class, 'update']
    )->name('barang.update');

    Route::delete(
        '/barang/{id}',
        [BarangController::class, 'destroy']
    )->name('barang.destroy');


    /*
    |--------------------------------------------------------------------------
    | Data Barang User
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/barang-user',
        [BarangUserController::class, 'index']
    )->name('barang.user');


    /*
    |--------------------------------------------------------------------------
    | Penambahan Barang
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/penambahan-barang',
        [PenambahanBarangController::class, 'index']
    )->name('penambahan-barang.index');

    Route::post(
        '/penambahan-barang',
        [PenambahanBarangController::class, 'store']
    )->name('penambahan-barang.store');

    Route::put(
        '/penambahan-barang/{id}',
        [PenambahanBarangController::class, 'update']
    )->name('penambahan-barang.update');

    Route::delete(
        '/penambahan-barang/{id}',
        [PenambahanBarangController::class, 'destroy']
    )->name('penambahan-barang.destroy');


    /*
    |--------------------------------------------------------------------------
    | Pemakaian Barang
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/pemakaian-barang',
        [PemakaianBarangController::class, 'index']
    )->name('pemakaian-barang.index');

    Route::post(
        '/pemakaian-barang',
        [PemakaianBarangController::class, 'store']
    )->name('pemakaian-barang.store');

    Route::put(
        '/pemakaian-barang/{id}',
        [PemakaianBarangController::class, 'update']
    )->name('pemakaian-barang.update');

    Route::delete(
        '/pemakaian-barang/{id}',
        [PemakaianBarangController::class, 'destroy']
    )->name('pemakaian-barang.destroy');


    /*
    |--------------------------------------------------------------------------
    | Periode Stok Opname
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/periode-stok-opname',
        [PeriodeStokOpnameController::class, 'index']
    )->name('periode-stok-opname.index');

    Route::post(
        '/periode-stok-opname',
        [PeriodeStokOpnameController::class, 'store']
    )->name('periode-stok-opname.store');

    Route::put(
        '/periode-stok-opname/{id}',
        [PeriodeStokOpnameController::class, 'update']
    )->name('periode-stok-opname.update');

    Route::delete(
        '/periode-stok-opname/{id}',
        [PeriodeStokOpnameController::class, 'destroy']
    )->name('periode-stok-opname.destroy');


    /*
    |--------------------------------------------------------------------------
    | Stok Opname
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/stok-opname',
        [StokOpnameController::class, 'index']
    )->name('stok-opname.index');

    Route::post(
        '/stok-opname/generate',
        [StokOpnameController::class, 'generate']
    )->name('stok-opname.generate');

    Route::put(
        '/stok-opname/{id}',
        [StokOpnameController::class, 'update']
    )->name('stok-opname.update');

    Route::post(
        '/stok-opname/{periodeId}/selesai',
        [StokOpnameController::class, 'selesai']
    )->name('stok-opname.selesai');


    /*
    |--------------------------------------------------------------------------
    | Laporan Stok
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/laporan-stok',
        [LaporanStokController::class, 'index']
    )->name('laporan-stok.index');


    /*
    |--------------------------------------------------------------------------
    | Laporan Stok Opname
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/laporan-stok-opname',
        [LaporanStokOpnameController::class, 'index']
    )->name('laporan-stok-opname.index');


    /*
    |--------------------------------------------------------------------------
    | Rekapitulasi
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/rekapitulasi',
        [RekapitulasiController::class, 'index']
    )->name('rekapitulasi.index');


    /*
    |--------------------------------------------------------------------------
    | Role Menu
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/role-menu',
        [RoleMenuController::class, 'index']
    )->name('role-menu.index');

    Route::put(
        '/role-menu/{roleId}',
        [RoleMenuController::class, 'update']
    )->name('role-menu.update');


    /*
    |--------------------------------------------------------------------------
    | Role User
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/role-user',
        [RoleUserController::class, 'index']
    )->name('role-user.index');

    Route::post(
        '/role-user',
        [RoleUserController::class, 'store']
    )->name('role-user.store');

    Route::put(
        '/role-user/{id}',
        [RoleUserController::class, 'update']
    )->name('role-user.update');

    Route::delete(
        '/role-user/{id}',
        [RoleUserController::class, 'destroy']
    )->name('role-user.destroy');


    /*
    |--------------------------------------------------------------------------
    | Pengajuan
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/pengajuan',
        [PengajuanController::class, 'index']
    )->name('pengajuan.index');

    Route::post(
        '/pengajuan',
        [PengajuanController::class, 'store']
    )->name('pengajuan.store');

    Route::put(
        '/pengajuan/{id}/approve',
        [PengajuanController::class, 'approve']
    )->name('pengajuan.approve');

    Route::put(
        '/pengajuan/{id}/reject',
        [PengajuanController::class, 'reject']
    )->name('pengajuan.reject');





    // Export Excel Laporan Stok
Route::get(
    '/laporan-stok/export-excel',
    [LaporanStokController::class, 'exportExcel']
)->name('laporan-stok.export');

// Export Excel Laporan Stok Opname
Route::get(
    '/laporan-stok-opname/export-excel',
    [LaporanStokOpnameController::class, 'exportExcel']
)->name('laporan-stok-opname.export');

// Export Excel Rekapitulasi
Route::get(
    '/rekapitulasi/export-excel',
    [RekapitulasiController::class, 'exportExcel']
)->name('rekapitulasi.export');

});