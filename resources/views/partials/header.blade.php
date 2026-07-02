@php
$hideMenu = $hideMenu ?? false;
$role = $role ?? 'warga';
@endphp

<div class="navbar {{ $role }}">
    <div class="nav-wrapper">

        <!-- LEFT -->
        <div class="nav-left">

            {{-- Logo hanya untuk warga --}}
            @if($role == 'warga')
            <div class="logo-area">
                <img src="{{ asset('images/logocilacap.png') }}" class="logo-cilacap">

                <div class="logo-text">
                    <span class="title">SIPDes</span>
                    <span class="subtitle">Pahonjean</span>
                </div>
            </div>
            @endif

            {{-- Burger hanya untuk admin & kadus --}}
            @if($role == 'admin' || $role == 'kadus')
            <button class="burger-btn" onclick="toggleSidebar()">☰</button>
            @endif

        </div>


        <!-- CENTER (MENU WARGA) -->
        @if(!$hideMenu && $role == 'warga')
        <div class="menu">
            <a href="/warga" class="{{ request()->is('warga') ? 'active' : '' }}">
                Beranda
            </a>

            <a href="/form-usulan" class="{{ request()->is('form-usulan*') || request()->is('form-data-usulan*') ? 'active' : '' }}">
                Buat Usulan
            </a>

            <a href="/status-usulan" class="{{ request()->is('status-usulan*') ? 'active' : '' }}">
                Status Usulan
            </a>
        </div>
        @endif


        <!-- RIGHT (ADMIN / KADUS) -->
        @if(!$hideMenu && ($role == 'admin' || $role == 'kadus'))
        <div class="user-menu">

            <div class="user-trigger" onclick="toggleDropdown()">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-fill-check" viewBox="0 0 16 16">
                    <path d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7m1.679-4.493-1.335 2.226a.75.75 0 0 1-1.174.144l-.774-.773a.5.5 0 0 1 .708-.708l.547.548 1.17-1.951a.5.5 0 1 1 .858.514M11 5a3 3 0 1 1-6 0 3 3 0 0 1 6 0" />
                    <path d="M2 13c0 1 1 1 1 1h5.256A4.5 4.5 0 0 1 8 12.5a4.5 4.5 0 0 1 1.544-3.393Q8.844 9.002 8 9c-5 0-6 3-6 4" />
                </svg>

                {{-- Nama user --}}
                <span class="username">
                    {{ Auth::user()->name }}
                </span>

                <span class="arrow">
                    <svg width="10" height="10" viewBox="0 0 10 10">
                        <polyline points="1,3 5,7 9,3"
                            stroke="currentColor"
                            stroke-width="1.5"
                            fill="none"
                            stroke-linecap="round" />
                    </svg>
                </span>
            </div>

            <div class="dropdown-menu" id="userDropdown">

                <a href="{{ route('password.form') }}" class="dropdown-item">
                    <img src="{{ asset('images/pencil-square.svg') }}" class="dropdown-icon">
                    Ubah Password
                </a>

                <a href="#" class="dropdown-item logout-link"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">

                    <img src="{{ asset('images/box-arrow-right.svg') }}" class="dropdown-icon">
                    Logout

                </a>

                <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display:none;">
                    @csrf
                </form>

            </div>

        </div>
        @endif

    </div>
</div>