<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\BarangUserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BarangUserController extends Controller
{
    public function __construct(
        protected BarangUserRepositoryInterface $barangUserRepository
    ) {
    }

    public function index(Request $request): View
    {
        $perPage = (int) $request->input('per_page', 10);

        if (!in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 10;
        }

        $search = trim((string) $request->input('search', ''));

        $barangs = $this->barangUserRepository->getAll(
            $perPage,
            $search
        );

        return view('barang-user.index', compact(
            'barangs',
            'perPage',
            'search'
        ));
    }
}