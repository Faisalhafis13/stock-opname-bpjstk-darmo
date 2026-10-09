<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RoleMenuSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | ROLE
        |--------------------------------------------------------------------------
        */

        $adminRole = Role::updateOrCreate(
            ['name' => 'Admin'],
            [
                'description' => 'Administrator yang memiliki akses penuh terhadap sistem.',
            ]
        );

        $userRole = Role::updateOrCreate(
            ['name' => 'User'],
            [
                'description' => 'Pengguna yang dapat melihat barang dan membuat pengajuan.',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | MENU
        |--------------------------------------------------------------------------
        */

        // Dashboard
        $dashboard = Menu::updateOrCreate(
            ['slug' => 'dashboard'],
            [
                'parent_id' => null,
                'name' => 'Dashboard',
                'icon' => 'bi-speedometer2',
                'route' => 'dashboard',
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        // Master Data
        $masterData = Menu::updateOrCreate(
            ['slug' => 'master-data'],
            [
                'parent_id' => null,
                'name' => 'Master Data',
                'icon' => 'bi-database',
                'route' => null,
                'sort_order' => 2,
                'is_active' => true,
            ]
        );

        $kategoriBarang = Menu::updateOrCreate(
            ['slug' => 'kategori-barang'],
            [
                'parent_id' => $masterData->id,
                'name' => 'Kategori Barang',
                'icon' => 'bi-tags',
                'route' => 'kategori-barang.index',
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        $dataBarang = Menu::updateOrCreate(
            ['slug' => 'data-barang'],
            [
                'parent_id' => $masterData->id,
                'name' => 'Data Barang',
                'icon' => 'bi-box-seam',
                'route' => 'barang.index',
                'sort_order' => 2,
                'is_active' => true,
            ]
        );

        // Transaksi
        $transaksi = Menu::updateOrCreate(
            ['slug' => 'transaksi'],
            [
                'parent_id' => null,
                'name' => 'Transaksi',
                'icon' => 'bi-arrow-left-right',
                'route' => null,
                'sort_order' => 3,
                'is_active' => true,
            ]
        );

        $penambahan = Menu::updateOrCreate(
            ['slug' => 'penambahan-barang'],
            [
                'parent_id' => $transaksi->id,
                'name' => 'Penambahan Barang',
                'icon' => 'bi-box-arrow-in-down',
                'route' => 'penambahan-barang.index',
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        $pemakaian = Menu::updateOrCreate(
            ['slug' => 'pemakaian-barang'],
            [
                'parent_id' => $transaksi->id,
                'name' => 'Pemakaian Barang',
                'icon' => 'bi-box-arrow-up',
                'route' => 'pemakaian-barang.index',
                'sort_order' => 2,
                'is_active' => true,
            ]
        );

        // Stok Opname
        $stokOpname = Menu::updateOrCreate(
            ['slug' => 'stok-opname'],
            [
                'parent_id' => null,
                'name' => 'Stok Opname',
                'icon' => 'bi-clipboard-check',
                'route' => null,
                'sort_order' => 4,
                'is_active' => true,
            ]
        );

        $periodeOpname = Menu::updateOrCreate(
            ['slug' => 'periode-stok-opname'],
            [
                'parent_id' => $stokOpname->id,
                'name' => 'Periode Stok Opname',
                'icon' => 'bi-calendar3',
                'route' => 'periode-stok-opname.index',
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        $inputOpname = Menu::updateOrCreate(
            ['slug' => 'input-stok-opname'],
            [
                'parent_id' => $stokOpname->id,
                'name' => 'Input Stok Opname',
                'icon' => 'bi-clipboard-plus',
                'route' => 'stok-opname.index',
                'sort_order' => 2,
                'is_active' => true,
            ]
        );

        // Laporan
        $laporan = Menu::updateOrCreate(
            ['slug' => 'laporan'],
            [
                'parent_id' => null,
                'name' => 'Laporan',
                'icon' => 'bi-file-earmark-text',
                'route' => null,
                'sort_order' => 5,
                'is_active' => true,
            ]
        );

        $laporanStok = Menu::updateOrCreate(
            ['slug' => 'laporan-stok'],
            [
                'parent_id' => $laporan->id,
                'name' => 'Laporan Stok',
                'icon' => 'bi-boxes',
                'route' => 'laporan-stok.index',
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        $laporanOpname = Menu::updateOrCreate(
            ['slug' => 'laporan-stok-opname'],
            [
                'parent_id' => $laporan->id,
                'name' => 'Laporan Stok Opname',
                'icon' => 'bi-file-earmark-check',
                'route' => 'laporan-stok-opname.index',
                'sort_order' => 2,
                'is_active' => true,
            ]
        );

        $rekapitulasi = Menu::updateOrCreate(
            ['slug' => 'rekapitulasi'],
            [
                'parent_id' => $laporan->id,
                'name' => 'Rekapitulasi',
                'icon' => 'bi-bar-chart',
                'route' => 'rekapitulasi.index',
                'sort_order' => 3,
                'is_active' => true,
            ]
        );

        // Pengguna
        $pengguna = Menu::updateOrCreate(
            ['slug' => 'pengguna'],
            [
                'parent_id' => null,
                'name' => 'Pengguna',
                'icon' => 'bi-people',
                'route' => null,
                'sort_order' => 6,
                'is_active' => true,
            ]
        );

        $roleMenu = Menu::updateOrCreate(
            ['slug' => 'role-menu'],
            [
                'parent_id' => $pengguna->id,
                'name' => 'Role Menu',
                'icon' => 'bi-menu-button-wide',
                'route' => 'role-menu.index',
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        $roleUser = Menu::updateOrCreate(
            ['slug' => 'role-user'],
            [
                'parent_id' => $pengguna->id,
                'name' => 'Role User',
                'icon' => 'bi-person-gear',
                'route' => 'role-user.index',
                'sort_order' => 2,
                'is_active' => true,
            ]
        );

        // Pengajuan
        $pengajuan = Menu::updateOrCreate(
            ['slug' => 'pengajuan'],
            [
                'parent_id' => null,
                'name' => 'Pengajuan',
                'icon' => 'bi-send',
                'route' => 'pengajuan.index',
                'sort_order' => 7,
                'is_active' => true,
            ]
        );

        // Barang untuk User
        $barangUser = Menu::updateOrCreate(
            ['slug' => 'barang'],
            [
                'parent_id' => null,
                'name' => 'Barang',
                'icon' => 'bi-box',
                'route' => 'barang.user',
                'sort_order' => 8,
                'is_active' => true,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | ROLE MENU
        |--------------------------------------------------------------------------
        */

        // ADMIN → seluruh menu
        $allMenus = Menu::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $adminRole->menus()->sync($allMenus->pluck('id'));

        // USER → hanya Dashboard, Pengajuan, Barang
        $userRole->menus()->sync([
            $dashboard->id,
            $pengajuan->id,
            $barangUser->id,
        ]);

        /*
        |--------------------------------------------------------------------------
        | USER ACCOUNT
        |--------------------------------------------------------------------------
        */

        User::updateOrCreate(
            ['email' => 'admin@stokopname.test'],
            [
                'name' => 'Administrator',
                'role_id' => $adminRole->id,
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'user@stokopname.test'],
            [
                'name' => 'User',
                'role_id' => $userRole->id,
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
    }
}