<div class="sidebar">

    <div class="sidebar-brand">
        <div class="brand-logo">
            <img src="{{ asset('images/logocilacap.png') }}" alt="Logo">
        </div>
    </div>

    <ul class="nav-menu">

        <!-- HOME -->
        <li class="menu-section">HOME</li>

        <li class="nav-item single-menu {{ request()->is('kadus/home*') ? 'active' : '' }}">
            <a href="{{ url('/kadus/home') }}" class="menu-content">
                <img src="{{ asset('images/home.png') }}" class="nav-icon">
                Home
            </a>
        </li>


        <!-- MAIN MENU -->
        <li class="menu-section">MAIN MENU</li>

        <li class="nav-item single-menu {{ request()->is('kadus/rpjmdes*') ? 'active' : '' }}">
            <a href="{{ url('/kadus/rpjmdes') }}" class="menu-content">
                <img src="{{ asset('images/monitoring.png') }}" class="nav-icon">
                RPJMDes
            </a>
        </li>


        <!-- RKPDes -->
        <li class="nav-item has-submenu 
        {{ request()->is('kadus/pengajuanrkp*') || request()->is('kadus/rkpdes*') ? 'active' : '' }}">

            <div class="menu-content">
                <img src="{{ asset('images/pengajuanrkp.png') }}" class="nav-icon">
                RKPDes
                <span class="submenu-icon">❯</span>
            </div>

            <ul class="nav-submenu">

                <li class="nav-sub-item {{ request()->is('kadus/pengajuanrkp*') ? 'active' : '' }}">
                    <a href="{{ url('/kadus/pengajuanrkp') }}">
                        Pengajuan RKPDes
                    </a>
                </li>

                <li class="nav-sub-item {{ request()->is('kadus/rkpdes*') ? 'active' : '' }}">
                    <a href="{{ url('/kadus/rkpdes') }}">
                        RKPDes
                    </a>
                </li>

            </ul>

        </li>
    </ul>

</div>