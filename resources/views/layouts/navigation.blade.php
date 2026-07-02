<div class="wrapper">
    <!-- ========== App Menu Start ========== -->
    <div class="main-nav">
        <div class="logo-box">
            <a href="{{ route('dashboard') }}" class="logo-dark">
                <img src="{{ asset('assets/images/logo_white.png') }}" class="logo-sm" alt="logo sm">
                <img src="{{ asset('assets/images/logo_white.png') }}" class="logo-lg" alt="logo dark">
            </a>

            <a href="{{ route('dashboard') }}" class="logo-light">
                <img src="{{ asset('assets/images/logo_white.png') }}" class="logo-sm" alt="logo sm">
                <img src="{{ asset('assets/images/logo_white.png') }}" class="logo-lg" alt="logo light">
            </a>
        </div>

        <button type="button" class="button-sm-hover">
            <iconify-icon icon="solar:double-alt-arrow-right-bold-duotone"></iconify-icon>
        </button>

        <div class="scrollbar" data-simplebar>

            <ul class="navbar-nav" id="navbar-nav">

                <li class="menu-title">
                    General
                </li>

                @foreach($menus as $menu)

                    @php

                        $hasChildren = $menu->children->count() > 0;

                        $visibleChildren = $menu->children->filter(function ($child) {

                            return empty($child->permission)
                                || auth()->user()->can($child->permission);

                        });

                    @endphp

                    {{-- MENU PARENT --}}
                    @if($hasChildren)

                        @if($visibleChildren->count() > 0)

                            <li class="nav-item">

                                <a class="nav-link menu-arrow" href="#menu{{ $menu->id }}" data-bs-toggle="collapse" role="button">

                                    <span class="nav-icon">
                                        <iconify-icon icon="{{ $menu->icon }}"></iconify-icon>
                                    </span>

                                    <span class="nav-text">
                                        {{ $menu->name }}
                                    </span>

                                </a>

                                <div class="collapse" id="menu{{ $menu->id }}">

                                    <ul class="nav sub-navbar-nav">

                                        @foreach($visibleChildren as $child)

                                            <li class="sub-nav-item">

                                                <a class="sub-nav-link" href="{{ Route::has($child->url) ? route($child->url) : '#' }}">

                                                    {{ $child->name }}

                                                </a>

                                            </li>

                                        @endforeach

                                    </ul>

                                </div>

                            </li>

                        @endif

                    @else

                        {{-- MENU TANPA CHILD --}}
                        @if(
                                (empty($menu->permission)
                                    || auth()->user()->can($menu->permission))
                                &&
                                !empty($menu->url)
                            )

                            <li class="nav-item">

                                <a class="nav-link" href="{{ Route::has($menu->url) ? route($menu->url) : '#' }}">

                                    <span class="nav-icon">
                                        <iconify-icon icon="{{ $menu->icon }}"></iconify-icon>
                                    </span>

                                    <span class="nav-text">
                                        {{ $menu->name }}
                                    </span>

                                </a>

                            </li>

                        @endif

                    @endif

                @endforeach

            </ul>

        </div>

    </div>
    <!-- ========== App Menu End ========== -->
</div>