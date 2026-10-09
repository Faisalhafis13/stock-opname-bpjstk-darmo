<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMenuAccess
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {

        /*
        |--------------------------------------------------------------------------
        | Pastikan user sudah login
        |--------------------------------------------------------------------------
        */

        if (!auth()->check()) {
            abort(403, 'Anda harus login untuk mengakses halaman ini.');
        }


        /*
        |--------------------------------------------------------------------------
        | Pastikan user memiliki role
        |--------------------------------------------------------------------------
        */

        $user = auth()->user();

        if (!$user->role) {
            abort(403, 'User belum memiliki role.');
        }


        /*
        |--------------------------------------------------------------------------
        | Ambil nama route yang sedang diakses
        |--------------------------------------------------------------------------
        */

        $routeName = $request->route()?->getName();

        if (!$routeName) {
            return $next($request);
        }


        /*
        |--------------------------------------------------------------------------
        | CEK ROUTE ASLI
        |--------------------------------------------------------------------------
        |
        | Contoh:
        |
        | barang.user
        | pengajuan.index
        | dashboard
        |
        | Route tersebut dicek terlebih dahulu secara langsung.
        |
        */

        $hasAccess = $user->role
            ->menus()
            ->where('menus.route', $routeName)
            ->where('menus.is_active', true)
            ->exists();


        /*
        |--------------------------------------------------------------------------
        | Jika route asli belum memiliki akses,
        | cek route induknya.
        |--------------------------------------------------------------------------
        |
        | Digunakan untuk route seperti:
        |
        | barang.store
        | barang.update
        | barang.destroy
        |
        | yang menggunakan menu:
        |
        | barang.index
        |
        */

        if (!$hasAccess && $routeName !== 'dashboard') {

            $routeParts = explode('.', $routeName);

            $routePrefix = $routeParts[0];

            $menuRoute = $routePrefix . '.index';

            $hasAccess = $user->role
                ->menus()
                ->where('menus.route', $menuRoute)
                ->where('menus.is_active', true)
                ->exists();
        }


        /*
        |--------------------------------------------------------------------------
        | Tolak jika tidak memiliki akses
        |--------------------------------------------------------------------------
        */

        if (!$hasAccess) {
            abort(
                403,
                'Anda tidak memiliki izin untuk mengakses halaman ini.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Lanjutkan request
        |--------------------------------------------------------------------------
        */

        return $next($request);
    }
}