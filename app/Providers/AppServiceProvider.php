<?php

namespace App\Providers;

use App\Repositories\BarangRepository;
use App\Repositories\Contracts\BarangRepositoryInterface;
use App\Repositories\Contracts\KategoriBarangRepositoryInterface;
use App\Repositories\KategoriBarangRepository;
use App\Repositories\PenambahanBarangRepository;
use App\Repositories\Contracts\PenambahanBarangRepositoryInterface;
use App\Repositories\PemakaianBarangRepository;
use App\Repositories\Contracts\PemakaianBarangRepositoryInterface;
use App\Repositories\PeriodeStokOpnameRepository;
use App\Repositories\Contracts\PeriodeStokOpnameRepositoryInterface;
use App\Repositories\Contracts\StokOpnameDetailRepositoryInterface;
use App\Repositories\StokOpnameDetailRepository;
use App\Repositories\Contracts\LaporanStokRepositoryInterface;
use App\Repositories\LaporanStokRepository;
use App\Repositories\Contracts\LaporanStokOpnameRepositoryInterface;
use App\Repositories\LaporanStokOpnameRepository;
use App\Repositories\Contracts\RekapitulasiRepositoryInterface;
use App\Repositories\RekapitulasiRepository;
use App\Repositories\RoleMenuRepository;
use App\Repositories\Contracts\RoleMenuRepositoryInterface;
use App\Repositories\RoleUserRepository;
use App\Repositories\Contracts\RoleUserRepositoryInterface;
use App\Repositories\BarangUserRepository;
use App\Repositories\Contracts\BarangUserRepositoryInterface;
use App\Repositories\PengajuanRepository;
use App\Repositories\Contracts\PengajuanRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            KategoriBarangRepositoryInterface::class,
            KategoriBarangRepository::class
        );

        $this->app->bind(
            BarangRepositoryInterface::class,
            BarangRepository::class
        );
        $this->app->bind(
    PenambahanBarangRepositoryInterface::class,
    PenambahanBarangRepository::class
);
        $this->app->bind(
            PemakaianBarangRepositoryInterface::class,
            PemakaianBarangRepository::class
        );
        $this->app->bind(
            PeriodeStokOpnameRepositoryInterface::class,
            PeriodeStokOpnameRepository::class
        );
        $this->app->bind(
            StokOpnameDetailRepositoryInterface::class,
            StokOpnameDetailRepository::class
        );
        $this->app->bind(
            LaporanStokRepositoryInterface::class,
            LaporanStokRepository::class
        );
        $this->app->bind(
            LaporanStokOpnameRepositoryInterface::class,
            LaporanStokOpnameRepository::class
        );
        $this->app->bind(
            RekapitulasiRepositoryInterface::class,
            RekapitulasiRepository::class
        );
        $this->app->bind(
            RoleMenuRepositoryInterface::class,
            RoleMenuRepository::class
        );
        $this->app->bind(
            RoleUserRepositoryInterface::class,
            RoleUserRepository::class
        );
        $this->app->bind(
            BarangUserRepositoryInterface::class,
            BarangUserRepository::class
        );
        $this->app->bind(
            PengajuanRepositoryInterface::class,
            PengajuanRepository::class
        );
    }

    public function boot(): void
    {
        //
    }
}