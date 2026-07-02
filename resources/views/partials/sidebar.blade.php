<div class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-logo">
            <img src="{{ asset('images/logocilacap.png') }}" alt="Logo">
        </div>
    </div>

    <ul class="nav-menu">
        <!-- Menu Home -->
        <li class="menu-section">HOME</li>
        <li class="nav-item single-menu {{ request()->is('admin/home*') ? 'active' : '' }}">
            <a href="/admin/home" class="menu-content">
                <img src="{{ asset('images/home.png') }}" alt="Home" class="nav-icon">
                Home
            </a>
        </li>

        <!-- Menu Kelola Program & Dusun -->
        <li class="menu-section">MAIN MENU</li>
        <li class="nav-item has-submenu {{ request()->is('admin/bidang*') 
   || request()->is('admin/subbidang*') 
   || request()->is('admin/kegiatan*') 
   || request()->is('admin/dusun*') 
   || request()->is('admin/rtrw*') 
   ? 'active' : '' }}">
            <div class="menu-content">
                <img src="{{ asset('images/program.png') }}" class="nav-icon">
                Kelola Program & Dusun
                <span class="submenu-icon">❯</span>
            </div>
            <ul class="nav-submenu">
                <li class="nav-sub-item {{ request()->is('admin/bidang*') || request()->is('admin/subbidang*') || request()->is('admin/kegiatan*') ? 'active' : '' }}">
                    <a href="/admin/bidang">Kelola Program</a>
                </li>
                <li class="nav-sub-item {{ request()->is('admin/dusun*') || request()->is('admin/rtrw*') ? 'active' : '' }}">
                    <a href="/admin/dusun">Kelola Dusun</a>
                </li>
            </ul>
        </li>

        <!-- Menu Periode -->
        <li class="nav-item single-menu {{ request()->is('admin/periode*') ? 'active' : '' }}">
            <a href="/admin/periode" class="menu-content">
                <img src="{{ asset('images/periode.png') }}" alt="Periode" class="nav-icon">
                Periode Usulan
            </a>
        </li>

        <li class="nav-item single-menu {{ request()->is('admin/kelolausulan*') ? 'active' : '' }}">
            <a href="/admin/kelolausulan" class="menu-content">
                <img src="{{ asset('images/kelola.png') }}" alt="Periode" class="nav-icon">
                Kelola Usulan
            </a>
        </li>

        <!-- Menu Perencanaan Desa -->
        <li class="nav-item has-submenu {{ request()->is('admin/penyusunanrpjm*') || request()->is('admin/penyusunanrkp*') ? 'active' : '' }}">
            <div class="menu-content">
                <img src="{{ asset('images/perencanaan.png') }}" alt="Perencanaandesa" class="nav-icon">
                Perencanaan Desa
                <span class="submenu-icon">❯</span>
            </div>
            <ul class="nav-submenu">
                <li class="nav-sub-item {{ request()->is('admin/penyusunanrpjm*') ? 'active' : '' }}">
                    <a href="/admin/penyusunanrpjm">Penyusunan RPJMDes</a>
                </li>
                <li class="nav-sub-item {{ request()->is('admin/penyusunanrkp*') ? 'active' : '' }}">
                    <a href="/admin/penyusunanrkp">Penyusunan RKPDes</a>
                </li>
            </ul>
        </li>

        <!-- Menu Aset Desa -->
        <li class="nav-item single-menu {{ request()->is('admin/asetdesa*') ? 'active' : '' }}">
            <a href="/admin/asetdesa" class="menu-content">
                <img src="{{ asset('images/asetdesa.png') }}" alt="Asetdesa" class="nav-icon">
                Kelola Aset Desa
            </a>
        </li>

        <!-- Menu Monitoring & Laporan -->
        <li class="nav-item has-submenu {{ request()->is('admin/monitoringrpjm*') || request()->is('admin/monitoringrkp*') || request()->is('admin/laporan*') ? 'active' : '' }}">
            <div class="menu-content">
                <img src="{{ asset('images/monitoring.png') }}" alt="Monitoring" class="nav-icon">
                Monitoring & Laporan
                <span class="submenu-icon">❯</span>
            </div>
            <ul class="nav-submenu">
                <li class="nav-sub-item {{ request()->is('admin/monitoringrpjm*') ? 'active' : '' }}">
                    <a href="/admin/monitoringrpjm">Monitoring RPJMDes</a>
                </li>
                <li class="nav-sub-item {{ request()->is('admin/monitoringrkp*') ? 'active' : '' }}">
                    <a href="/admin/monitoringrkp">Monitoring RKPDes</a>
                </li>
                <li class="nav-sub-item {{ request()->is('admin/laporan*') ? 'active' : '' }}">
                    <a href="/admin/laporan">Laporan Tahunan</a>
                </li>
            </ul>
        </li>

        <!-- Menu Pengguna -->
        <li class="nav-item single-menu {{ request()->is('admin/pengguna*') ? 'active' : '' }}">
            <a href="/admin/pengguna" class="menu-content">
                <img src="{{ asset('images/pengguna.png') }}" alt="Pengguna" class="nav-icon">
                Manajemen Pengguna
            </a>
        </li>
    </ul>
</div>