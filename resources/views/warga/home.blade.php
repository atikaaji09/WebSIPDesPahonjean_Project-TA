@extends('layouts.app')

@section('content')

<div class="hero">

    <div class="hero-content container">

        <div class="hero-text">

            <h1>
                Pengajuan Usulan RPJMDes
                <span class="desa-highlight">Desa Pahonjean</span>
            </h1>

            <p>
                Website ini digunakan oleh masyarakat Desa Pahonjean untuk
                menyampaikan usulan kegiatan pembangunan desa dalam proses
                penyusunan Rencana Pembangunan Jangka Menengah Desa (RPJMDes).
            </p>

            <div class="hero-buttons">
                <a href="/form-usulan" class="btn-primary">Buat Usulan</a>
                <a href="{{ route('status.usulan') }}" class="btn-secondary">
                    Cek Status Usulan
                </a>
            </div>

        </div>

        <div class="hero-img">
            <img src="{{ asset('images/diskusi.png') }}" alt="Musyawarah Desa">
        </div>

    </div>

</div>

<div class="info-section lazy-load">

    <img src="{{ asset('images/assistant.png') }}"
        class="info-character"
        alt="Karakter">

    <div class="container">

        <h2>Tentang RPJMDes</h2>

        <p>
            RPJMDes (Rencana Pembangunan Jangka Menengah Desa) merupakan dokumen
            perencanaan pembangunan desa yang disusun untuk jangka waktu 6 (enam)
            tahun sebagai pedoman dalam menentukan arah kebijakan, program, dan
            kegiatan pembangunan desa. Penyusunan RPJMDes dilakukan berdasarkan
            visi dan misi kepala desa serta mempertimbangkan kebutuhan, potensi,
            dan aspirasi masyarakat. Melalui sistem ini, masyarakat dapat
            menyampaikan usulan pembangunan secara langsung sesuai dengan kebutuhan
            di lingkungan masing-masing. Seluruh usulan yang masuk akan
            didokumentasikan dan menjadi bahan pertimbangan dalam pelaksanaan
            musyawarah desa untuk menentukan prioritas pembangunan yang akan
            dimasukkan ke dalam RPJMDes. Dengan adanya sistem ini, proses
            pengajuan usulan menjadi lebih terstruktur, transparan, dan
            memudahkan pemerintah desa dalam mengelola serta memantau usulan
            pembangunan dari masyarakat.
        </p>

    </div>

</div>

<div class="service-section lazy-load">

    <div class="container">

        <h3>Layanan Pengajuan Usulan</h3>

        <div class="service-cards">

            <div class="service-card lazy-load">

                <div class="service-icon">
                    <img src="{{ asset('images/IconBuatUsulan.png') }}" alt="Buat Usulan">
                </div>

                <h4>Buat Usulan</h4>

                <p>
                    Ajukan usulan pembangunan desa sesuai kebutuhan masyarakat
                    melalui sistem ini secara mudah dan transparan.
                </p>

                <a href="/form-usulan" class="service-btn">
                    Buat Usulan
                </a>

            </div>

            <div class="service-card lazy-load">

                <div class="service-icon">
                    <img src="{{ asset('images/Container.png') }}" alt="Status Usulan">
                </div>

                <h4>Cek Status Usulan</h4>

                <p>
                    Pantau perkembangan usulan yang telah diajukan dan lihat
                    status prosesnya secara real-time kapan saja.
                </p>

                <a href="{{ route('status.usulan') }}" class="service-btn">
                    Cek Status
                </a>

            </div>

        </div>

    </div>

</div>

<div class="stats lazy-load">

    <div class="container">

        <div class="stats-grid">

            <div class="stat-card lazy-load">
                <div class="stat-top">
                    <img src="{{ asset('images/report.png') }}">
                    <h2>{{ $usulanMasuk ?? 0 }}</h2>
                </div>
                <p>Usulan Masuk</p>
            </div>

            <div class="stat-card lazy-load">
                <div class="stat-top">
                    <img src="{{ asset('images/appointment.png') }}">
                    <h2>{{ $diproses ?? 0 }}</h2>
                </div>
                <p>Usulan Diproses</p>
            </div>

            <div class="stat-card lazy-load">
                <div class="stat-top">
                    <img src="{{ asset('images/collaboration.png') }}">
                    <h2>{{ $diterima ?? 0 }}</h2>
                </div>
                <p>Usulan Diterima</p>
            </div>

        </div>

    </div>

</div>

@endsection