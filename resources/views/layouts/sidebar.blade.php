@php

    use App\Models\Menu;

    $user = auth()->user();

    $allowedMenus = collect();

    if ($user && $user->role) {

        $allowedMenus = $user->role
            ->menus()
            ->where('menus.is_active', true)
            ->orderBy('sort_order')
            ->get();

    }

    $allMenus = Menu::query()
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->get();

    $mainMenus = $allMenus
        ->whereNull('parent_id')
        ->values();

    $allowedMenuIds = $allowedMenus
        ->pluck('id')
        ->toArray();

@endphp


<div class="sidebar" id="sidebar">

    {{-- =====================================================
        BRAND
    ====================================================== --}}

    <div class="sidebar-brand">

        <div class="brand-title">
            Stok Opname
        </div>

        <div class="brand-subtitle">
            BPJS Ketenagakerjaan
        </div>

    </div>


    {{-- =====================================================
        USER
    ====================================================== --}}

    @auth

        <div class="sidebar-user">

            <div class="sidebar-user-name">
                {{ auth()->user()->name }}
            </div>

            <div class="sidebar-user-role">
                {{ auth()->user()->role?->name ?? 'User' }}
            </div>

        </div>

    @endauth


    {{-- =====================================================
        NAVIGATION
    ====================================================== --}}

    <div class="sidebar-nav">

        @foreach ($mainMenus as $menu)

            @php

                $children = $allMenus
                    ->where('parent_id', $menu->id)
                    ->values();

                $allowedChildren = $children
                    ->filter(function ($child) use ($allowedMenuIds) {

                        return in_array(
                            $child->id,
                            $allowedMenuIds
                        );

                    })
                    ->values();

                $hasActiveChild = $allowedChildren->contains(
                    function ($child) {

                        if (!$child->route) {
                            return false;
                        }

                        $routePattern = $child->route;

                        if (
                            str_ends_with(
                                $routePattern,
                                '.index'
                            )
                        ) {

                            $routePattern = str_replace(
                                '.index',
                                '.*',
                                $routePattern
                            );

                        }

                        return request()->routeIs(
                            $routePattern
                        );

                    }
                );

            @endphp


            {{-- =================================================
                MENU LANGSUNG
            ================================================== --}}

            @if (
                $menu->route &&
                in_array($menu->id, $allowedMenuIds) &&
                $children->isEmpty()
            )

                <a
                    href="{{ Route::has($menu->route)
                        ? route($menu->route)
                        : '#' }}"
                    class="sidebar-link
                        {{ request()->routeIs($menu->route)
                            ? 'active'
                            : '' }}"
                >

                    <span>
                        {{ $menu->name }}
                    </span>

                </a>

            @endif


            {{-- =================================================
                MENU DROPDOWN
            ================================================== --}}

            @if ($allowedChildren->isNotEmpty())

                @php

                    $collapseId = 'menu-' . $menu->id;

                @endphp


                <div class="sidebar-dropdown">

                    <button
                        type="button"
                        class="sidebar-dropdown-toggle
                            {{ $hasActiveChild ? 'active' : '' }}"
                        data-bs-toggle="collapse"
                        data-bs-target="#{{ $collapseId }}"
                        aria-expanded="{{ $hasActiveChild ? 'true' : 'false' }}"
                        aria-controls="{{ $collapseId }}"
                    >

                        <span>
                            {{ $menu->name }}
                        </span>

                        <span class="sidebar-chevron">
                            ›
                        </span>

                    </button>


                    <div
                        id="{{ $collapseId }}"
                        class="collapse
                            {{ $hasActiveChild ? 'show' : '' }}"
                    >

                        <div class="sidebar-submenu">

                            @foreach ($allowedChildren as $child)

                                @if ($child->route)

                                    @php

                                        $routePattern = $child->route;

                                        if (
                                            str_ends_with(
                                                $routePattern,
                                                '.index'
                                            )
                                        ) {

                                            $routePattern = str_replace(
                                                '.index',
                                                '.*',
                                                $routePattern
                                            );

                                        }

                                    @endphp


                                    <a
                                        href="{{ Route::has($child->route)
                                            ? route($child->route)
                                            : '#' }}"
                                        class="sidebar-sublink
                                            {{ request()->routeIs($routePattern)
                                                ? 'active'
                                                : '' }}"
                                    >

                                        {{ $child->name }}

                                    </a>

                                @endif

                            @endforeach

                        </div>

                    </div>

                </div>

            @endif

        @endforeach

    </div>


    {{-- =====================================================
        LOGOUT
    ====================================================== --}}

    @auth

        <div class="sidebar-logout">

            <form
                method="POST"
                action="{{ route('logout') }}"
            >

                @csrf

                <button
                    type="submit"
                    class="sidebar-logout-button"
                >
                    Logout
                </button>

            </form>

        </div>

    @endauth

</div>